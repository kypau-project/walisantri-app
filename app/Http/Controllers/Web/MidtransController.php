<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    /**
     * Create Snap Token untuk pembayaran tagihan
     */
    public function createSnapToken(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $student = $request->user()->student;
        $bill = $student->bills()->findOrFail($request->bill_id);

        if ($bill->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Tagihan sudah lunas.'], 400);
        }

        $remaining = $bill->amount - $bill->paid_amount;
        $payAmount = (int) min($request->amount, $remaining);

        // Generate unique order ID
        $orderId = 'WS-' . $bill->id . '-' . time() . '-' . strtoupper(Str::random(4));

        // Create pending payment record
        $payment = Payment::create([
            'student_id' => $student->id,
            'bill_id' => $bill->id,
            'amount' => $payAmount,
            'payment_method' => 'midtrans',
            'transaction_id' => $orderId,
            'status' => 'pending',
        ]);

        // Build Midtrans params
        $midtrans = new MidtransService();
        $params = $midtrans->buildTransactionParams(
            $orderId,
            $payAmount,
            [
                'name' => $student->name,
                'phone' => $student->father_phone ?? $student->mother_phone ?? '',
            ],
            [
                [
                    'id' => 'BILL-' . $bill->id,
                    'price' => $payAmount,
                    'quantity' => 1,
                    'name' => Str::limit($bill->title, 50),
                ],
            ]
        );

        $snapToken = $midtrans->createSnapToken($params);

        if (!$snapToken) {
            $payment->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Gagal membuat token pembayaran.'], 500);
        }

        $payment->update(['snap_token' => $snapToken]);

        return response()->json([
            'success' => true,
            'data' => [
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'payment_id' => $payment->id,
            ],
        ]);
    }

    /**
     * Create Snap Token untuk top-up tabungan
     */
    public function createSavingSnapToken(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $student = $request->user()->student;
        $amount = (int) $request->amount;
        $description = $request->description ?? 'Top up saldo tabungan';

        // Ensure saving account exists
        $saving = $student->savingAccount ?? $student->savingAccount()->create(['balance' => 0]);

        // Generate unique order ID with SAV prefix
        $orderId = 'SAV-' . $student->id . '-' . time() . '-' . strtoupper(Str::random(4));

        // Create pending saving transaction
        $transaction = $saving->transactions()->create([
            'type' => 'topup',
            'amount' => $amount,
            'description' => $description,
            'transaction_id' => $orderId,
            'balance_after' => $saving->balance, // will be updated after payment
            'status' => 'pending',
        ]);

        // Build Midtrans params
        $midtrans = new MidtransService();
        $params = $midtrans->buildTransactionParams(
            $orderId,
            $amount,
            [
                'name' => $student->name,
                'phone' => $student->father_phone ?? $student->mother_phone ?? '',
            ],
            [
                [
                    'id' => 'TOPUP-' . $saving->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => Str::limit($description, 50),
                ],
            ]
        );

        $snapToken = $midtrans->createSnapToken($params);

        if (!$snapToken) {
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Gagal membuat token pembayaran.'], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ],
        ]);
    }

    /**
     * Webhook / Notification handler dari Midtrans
     * URL: POST /midtrans/notification
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans Notification', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? 'accept';
        $paymentType = $payload['payment_type'] ?? 'unknown';

        if (!$orderId) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        // Verify signature
        $midtrans = new MidtransService();
        if (!$midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Invalid Signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Route to savings handler if order starts with SAV-
        if (str_starts_with($orderId, 'SAV-')) {
            return $this->handleSavingNotification($payload, $orderId, $transactionStatus, $fraudStatus);
        }

        // Find payment (for bill payments)
        $payment = Payment::where('transaction_id', $orderId)->first();
        if (!$payment) {
            Log::warning('Midtrans Payment Not Found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Already completed? Skip
        if ($payment->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        // Map payment type to readable method
        $methodMap = [
            'bank_transfer' => 'Virtual Account',
            'echannel' => 'Mandiri Bill',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'cstore' => 'Mitra/Agen',
        ];
        $readableMethod = $methodMap[$paymentType] ?? $paymentType;

        // Update payment based on transaction status
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept') {
                $payment->update([
                    'status' => 'success',
                    'payment_method' => $readableMethod,
                    'paid_at' => now(),
                    'midtrans_response' => $payload,
                ]);

                // Update bill
                $bill = $payment->bill;
                if ($bill) {
                    $bill->paid_amount += $payment->amount;
                    $bill->status = $bill->paid_amount >= $bill->amount ? 'paid' : 'partial';
                    $bill->save();
                }

                Log::info("Payment SUCCESS: {$orderId}");
            }
        } elseif ($transactionStatus === 'pending') {
            $payment->update([
                'status' => 'pending',
                'payment_method' => $readableMethod,
                'midtrans_response' => $payload,
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
            $payment->update([
                'status' => 'failed',
                'payment_method' => $readableMethod,
                'midtrans_response' => $payload,
            ]);
        } elseif ($transactionStatus === 'expire') {
            $payment->update([
                'status' => 'expire',
                'midtrans_response' => $payload,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Handle Midtrans notification for savings top-up (SAV- prefix)
     */
    private function handleSavingNotification(array $payload, string $orderId, string $transactionStatus, string $fraudStatus)
    {
        $transaction = SavingTransaction::where('transaction_id', $orderId)->first();

        if (!$transaction) {
            Log::warning('Midtrans Saving Transaction Not Found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        if (($transactionStatus === 'capture' || $transactionStatus === 'settlement') && $fraudStatus === 'accept') {
            // Credit the savings balance
            $saving = $transaction->saving;
            $saving->balance += $transaction->amount;
            $saving->save();

            $transaction->update([
                'status' => 'success',
                'balance_after' => $saving->balance,
            ]);

            Log::info("Saving Top Up SUCCESS: {$orderId}, Amount: {$transaction->amount}");
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure', 'expire'])) {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Callback setelah user selesai di Snap (redirect dari finish URL)
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');

        // Determine redirect based on order type
        if ($orderId && str_starts_with($orderId, 'SAV-')) {
            $tx = SavingTransaction::where('transaction_id', $orderId)->first();
            if ($tx && $tx->status === 'success') {
                return redirect('/savings')->with('success', 'Top up berhasil! Saldo tabungan telah ditambahkan.');
            }
            return redirect('/savings')->with('info', 'Top up sedang diproses. Saldo akan diperbarui otomatis.');
        }

        $payment = $orderId ? Payment::where('transaction_id', $orderId)->first() : null;

        if ($payment && $payment->status === 'success') {
            return redirect('/bills')->with('success', 'Pembayaran berhasil! Terima kasih.');
        }

        return redirect('/bills')->with('info', 'Pembayaran sedang diproses. Status akan diperbarui otomatis.');
    }
}
