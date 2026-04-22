<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject',
        'exam_date',
        'duration_minutes',
        'exam_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)
            ->withPivot('status', 'score', 'started_at', 'finished_at')
            ->withTimestamps();
    }
}
