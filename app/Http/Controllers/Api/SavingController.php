<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavingResource;
use App\Http\Resources\SavingTransactionResource;
use App\Models\SavingTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SavingController extends Controller
{
    /**
     * Get savings balance
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;
        $saving = $student->savingAccount;

        if (!$saving) {
            return response()->json([
                'success' => true,
                'data' => ['balance' => 0, 'formatted_balance' => 'Rp 0'],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new SavingResource($saving),
        ]);
    }

    public function topup(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $student = $request->user()->student;
        $saving = $student->savingAccount ?? $student->savingAccount()->create(['balance' => 0]);

        $amount = (int) $request->amount;
        $description = $request->description ?? 'Top up saldo';
        $orderId = 'SAV-' . $student->id . '-' . time() . '-' . strtoupper(Str::random(4));

        $transaction = SavingTransaction::create([
            'saving_id' => $saving->id,
            'type' => 'topup',
            'amount' => $amount,
            'description' => $description,
            'transaction_id' => $orderId,
            'balance_after' => $saving->balance,
            'status' => 'pending',
        ]);

        $midtrans = new \App\Services\MidtransService();
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

        $result = $midtrans->createTransaction($params);

        if (!$result || empty($result['token'])) {
            $transaction->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Gagal membuat token pembayaran.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token pembayaran berhasil dibuat.',
            'data' => [
                'snap_token' => $result['token'],
                'redirect_url' => $result['redirect_url'],
                'order_id' => $orderId,
                'transaction' => new SavingTransactionResource($transaction),
            ],
        ]);
    }

    /**
     * Savings transaction history
     */
    public function history(Request $request): JsonResponse
    {
        $student = $request->user()->student;
        $saving = $student->savingAccount;

        if (!$saving) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $transactions = $saving->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => SavingTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }
}
