<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string|min:3|max:255",
            "slug" => "required|string|min:1|max:255|unique:pages,slug",
            "query" => "required|string",
            "description" => "nullable|string",
            "introtext" => "nullable|string",
            "pagetitle" => "nullable|string",
            "is_published" => "boolean",
            "city_id" => "nullable|numeric|exists:cities,id"
        ];
    }
}
