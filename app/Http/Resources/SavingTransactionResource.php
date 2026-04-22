<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'formatted_amount' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'description' => $this->description,
            'transaction_id' => $this->transaction_id,
            'balance_after' => (float) $this->balance_after,
            'formatted_balance_after' => 'Rp ' . number_format($this->balance_after, 0, ',', '.'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
