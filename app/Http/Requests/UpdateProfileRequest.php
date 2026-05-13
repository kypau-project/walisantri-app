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
        // Wali santri hanya boleh update kontak dan alamat
        return [
            'father_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'mother_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'father_phone.min' => 'No. WA Ayah minimal 8 digit.',
            'father_phone.max' => 'No. WA Ayah maksimal 15 digit.',
            'father_phone.regex' => 'No. WA Ayah harus berupa angka.',
            'mother_phone.min' => 'No. WA Ibu minimal 8 digit.',
            'mother_phone.max' => 'No. WA Ibu maksimal 15 digit.',
            'mother_phone.regex' => 'No. WA Ibu harus berupa angka.',
            'address.max' => 'Alamat maksimal 500 karakter.',
        ];
    }
}
