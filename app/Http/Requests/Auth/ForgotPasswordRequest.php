<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:' . User::class],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Пожалуйста, введите ваш адрес электронной почты.',
            'email.email' => 'Пожалуйста, укажите действительный адрес электронной почты.',
            'email.exists' => 'Пользователь с указанным адресом электронной почты не существует.',
        ];

    }

    public function authorize(): bool
    {
        return true;
    }
}
