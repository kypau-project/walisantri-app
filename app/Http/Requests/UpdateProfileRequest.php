<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'class' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'mother_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'address' => 'nullable|string|max:500',
        ];
    }
}
