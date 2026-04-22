<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining' => (float) $this->remaining,
            'month' => $this->month,
            'year' => $this->year,
            'type' => $this->type,
            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'formatted_amount' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'formatted_remaining' => 'Rp ' . number_format($this->remaining, 0, ',', '.'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
