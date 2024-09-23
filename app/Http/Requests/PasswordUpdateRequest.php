<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => ['required'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }


    public function messages(): array
    {
        return [
            'old_password.required' => 'Пожалуйста, укажите страрый пароль.',
            'password.required' => 'Пожалуйста, укажите новый пароль.',
            'password.confirmed' => 'Подтверждение нового пароля не совпадает.',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
