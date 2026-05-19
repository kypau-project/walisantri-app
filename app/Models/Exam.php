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
        'teacher_name',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'shuffle_questions',
        'show_result',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'shuffle_questions' => 'boolean',
            'show_result' => 'boolean',
        ];
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)
            ->withPivot('id', 'access_token', 'status', 'score', 'total_points', 'started_at', 'finished_at', 'graded_at')
            ->withTimestamps();
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * Cek apakah ujian sedang dalam periode aktif
     */
    public function isOpen(): bool
    {
        if ($this->status !== 'active') return false;

        $now = now();
        $examDate = $this->exam_date->format('Y-m-d');

        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse("{$examDate} {$this->start_time}");
            $end = \Carbon\Carbon::parse("{$examDate} {$this->end_time}");
            return $now->between($start, $end);
        }

        // Jika hanya tanggal, berlaku seharian
        return $now->isSameDay($this->exam_date);
    }

    /**
     * Cek apakah ujian sudah lewat
     */
    public function isPast(): bool
    {
        $now = now();
        if ($this->end_time) {
            $end = \Carbon\Carbon::parse($this->exam_date->format('Y-m-d') . ' ' . $this->end_time);
            return $now->isAfter($end);
        }
        return $now->isAfter($this->exam_date->endOfDay());
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }
}
