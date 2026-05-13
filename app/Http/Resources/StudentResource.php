<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'class' => $this->class,
            'room' => $this->room,
            'enrollment_year' => $this->enrollment_year,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'father_phone' => $this->father_phone,
            'mother_phone' => $this->mother_phone,
            'barcode_id' => $this->barcode_id,
            'photo' => $this->photo,
            'photo_url' => $this->photo ? asset('storage/' . $this->photo) : null,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'gender' => $this->gender,
            'address' => $this->address,
            'status' => $this->status,
            'is_claimed' => $this->isClaimed(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
