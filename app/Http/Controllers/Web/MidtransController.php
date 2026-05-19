<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
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

        // Find payment
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
     * Callback setelah user selesai di Snap (redirect dari finish URL)
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        $payment = $orderId ? Payment::where('transaction_id', $orderId)->first() : null;

        if ($payment && $payment->status === 'success') {
            return redirect('/bills')->with('success', 'Pembayaran berhasil! Terima kasih.');
        }

        return redirect('/bills')->with('info', 'Pembayaran sedang diproses. Status akan diperbarui otomatis.');
    }
}
