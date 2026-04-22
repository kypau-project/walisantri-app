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
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'student_name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students,nis',
            'class' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'mother_phone' => 'nullable|string|min:8|max:15|regex:/^[0-9]+$/',
            'gender' => 'nullable|in:L,P',
        ];
    }

    public function messages(): array
    {
        return [
            'father_phone.min' => 'Nomor HP Ayah minimal 8 digit.',
            'father_phone.max' => 'Nomor HP Ayah maksimal 15 digit.',
            'father_phone.regex' => 'Nomor HP Ayah harus berupa angka.',
            'mother_phone.min' => 'Nomor HP Ibu minimal 8 digit.',
            'mother_phone.max' => 'Nomor HP Ibu maksimal 15 digit.',
            'mother_phone.regex' => 'Nomor HP Ibu harus berupa angka.',
        ];
    }
}
