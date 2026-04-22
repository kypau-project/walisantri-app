<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'semester' => $this->semester,
            'academic_year' => $this->academic_year,
            'grades' => $this->grades,
            'average_score' => $this->average_score ? (float) $this->average_score : null,
            'rank' => $this->rank,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'has_file' => !empty($this->file_path),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
