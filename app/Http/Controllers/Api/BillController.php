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
     * Pay a bill
     */
    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'nullable|string|in:transfer,cash,ewallet',
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
        $payAmount = min($request->amount, $remaining);

        // Create payment record
        $payment = Payment::create([
            'student_id' => $student->id,
            'bill_id' => $bill->id,
            'amount' => $payAmount,
            'payment_method' => $request->payment_method ?? 'transfer',
            'transaction_id' => 'TRX-' . strtoupper(Str::random(12)),
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // Update bill
        $bill->paid_amount += $payAmount;
        if ($bill->paid_amount >= $bill->amount) {
            $bill->status = 'paid';
        } else {
            $bill->status = 'partial';
        }
        $bill->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil.',
            'data' => [
                'payment' => new PaymentResource($payment),
                'bill' => new BillResource($bill->fresh()),
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
