<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester',
        'academic_year',
        'file_path',
        'grades',
        'average_score',
        'rank',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'grades' => 'array',
            'average_score' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
