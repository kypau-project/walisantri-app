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

    /**
     * Top up savings
     */
    public function topup(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $student = $request->user()->student;
        $saving = $student->savingAccount;

        if (!$saving) {
            $saving = $student->savingAccount()->create(['balance' => 0]);
        }

        $saving->balance += $request->amount;
        $saving->save();

        $transaction = SavingTransaction::create([
            'saving_id' => $saving->id,
            'type' => 'topup',
            'amount' => $request->amount,
            'description' => $request->description ?? 'Top up saldo',
            'transaction_id' => 'SAV-' . strtoupper(Str::random(10)),
            'balance_after' => $saving->balance,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Top up berhasil.',
            'data' => [
                'saving' => new SavingResource($saving),
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
