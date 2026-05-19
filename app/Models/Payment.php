<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bill_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at',
        'notes',
        'snap_token',
        'midtrans_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'midtrans_response' => 'array',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
