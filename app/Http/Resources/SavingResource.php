<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'balance' => (float) $this->balance,
            'formatted_balance' => 'Rp ' . number_format($this->balance, 0, ',', '.'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
