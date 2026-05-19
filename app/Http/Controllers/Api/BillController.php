<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Http\Resources\PaymentResource;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillController extends Controller
{
    /**
     * List all bills for student
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Data santri tidak ditemukan.'], 404);
        }

        $bills = $student->bills()
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'partial' THEN 2 WHEN 'overdue' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BillResource::collection($bills),
        ]);
    }

    /**
     * Create Midtrans Snap Token for bill payment (untuk Flutter)
     */
    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $student = $request->user()->student;
        $bill = Bill::where('id', $request->bill_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
        }

        if ($bill->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Tagihan sudah lunas.'], 400);
        }

        $remaining = $bill->amount - $bill->paid_amount;
        $payAmount = (int) min($request->amount, $remaining);

        $orderId = 'WS-' . $bill->id . '-' . time() . '-' . strtoupper(Str::random(4));

        // Create pending payment
        $payment = Payment::create([
            'student_id' => $student->id,
            'bill_id' => $bill->id,
            'amount' => $payAmount,
            'payment_method' => 'midtrans',
            'transaction_id' => $orderId,
            'status' => 'pending',
        ]);

        // Create Snap token
        $midtrans = new \App\Services\MidtransService();
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

        $result = $midtrans->createTransaction($params);

        if (!$result || !$result['token']) {
            $payment->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Gagal membuat token pembayaran.'], 500);
        }

        $payment->update(['snap_token' => $result['token']]);

        return response()->json([
            'success' => true,
            'message' => 'Token pembayaran berhasil dibuat.',
            'data' => [
                'snap_token' => $result['token'],
                'redirect_url' => $result['redirect_url'],
                'order_id' => $orderId,
                'payment_id' => $payment->id,
                'amount' => $payAmount,
            ],
        ]);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate(['order_id' => 'required|string']);

        $student = $request->user()->student;
        $payment = Payment::where('transaction_id', $request->order_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $payment->transaction_id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Show single bill detail
     */
    public function show(Request $request, $id): JsonResponse
    {
        $student = $request->user()->student;
        $bill = $student->bills()->with('payments')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $bill->id,
                'title' => $bill->title,
                'description' => $bill->description,
                'amount' => $bill->amount,
                'paid_amount' => $bill->paid_amount,
                'remaining' => $bill->amount - $bill->paid_amount,
                'due_date' => $bill->due_date?->format('Y-m-d'),
                'status' => $bill->status,
                'type' => $bill->type,
                'payments' => $bill->payments->map(fn($p) => [
                    'id' => $p->id,
                    'amount' => $p->amount,
                    'payment_method' => $p->payment_method,
                    'transaction_id' => $p->transaction_id,
                    'status' => $p->status,
                    'paid_at' => $p->paid_at?->format('Y-m-d H:i'),
                ]),
                'created_at' => $bill->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Bill payment history
     */
    public function history(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $bills = $student->bills()
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BillResource::collection($bills),
        ]);
    }
}
