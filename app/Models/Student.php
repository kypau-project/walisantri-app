<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nis',
        'class',
        'room',
        'father_phone',
        'mother_phone',
        'barcode_id',
        'photo',
        'birth_date',
        'gender',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function savingAccount()
    {
        return $this->hasOne(Saving::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot('status', 'score', 'started_at', 'finished_at')
            ->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
