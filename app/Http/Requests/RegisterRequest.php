<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_name' => 'required|string|max:255',
            'nis' => 'required|string|max:50',
            'phone' => 'required|string|min:8|max:15|regex:/^[0-9]+$/',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'student_name.required' => 'Nama lengkap santri wajib diisi.',
            'nis.required' => 'Nomor Induk Santri wajib diisi.',
            'phone.required' => 'No. HP wali santri wajib diisi.',
            'phone.min' => 'No. HP minimal 8 digit.',
            'phone.max' => 'No. HP maksimal 15 digit.',
            'phone.regex' => 'No. HP harus berupa angka.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
