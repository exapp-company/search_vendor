<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:3'],

            //'city_id' => ['nullable', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
    public function messages()
    {
        return [
            'query.required' => "Укажите запрос для поиска",
            "query.min" => "Введите хотябы 3 символа"
        ];
    }
}
