<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($this->supplier)],
            'description' => ['required', 'string'],
            'website' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'address' => ['nullable', 'string', 'max:255'],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'user_id' => ['nullable', Rule::exists('users', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, укажите название поставщика.',
            'name.unique' => 'Поставщик с указанным названием уже существует. Пожалуйста, используйте другое название.',
            'description.required' => 'Пожалуйста, укажите описание поставщика.',
            'website.required' => 'Пожалуйста, укажите веб-сайт поставщика.',
            'phone.required' => 'Пожалуйста, укажите номер телефона поставщика.',
            'email.required' => 'Пожалуйста, укажите адрес электронной почты поставщика.',
            'email.email' => 'Пожалуйста, укажите действительный адрес электронной почты.',
            'city_id.required' => 'Пожалуйста, выберите город.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
