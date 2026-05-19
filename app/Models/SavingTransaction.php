<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'saving_id',
        'type',
        'amount',
        'description',
        'transaction_id',
        'balance_after',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function saving()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function savingAccount()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }
}
