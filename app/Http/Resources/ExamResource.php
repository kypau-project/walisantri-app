<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'subject' => $this->subject,
            'exam_date' => $this->exam_date?->format('Y-m-d'),
            'duration_minutes' => $this->duration_minutes,
            'exam_url' => $this->exam_url,
            'status' => $this->status,
            'pivot' => $this->whenPivotLoaded('exam_student', function () {
                return [
                    'status' => $this->pivot->status,
                    'score' => $this->pivot->score,
                    'started_at' => $this->pivot->started_at,
                    'finished_at' => $this->pivot->finished_at,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
