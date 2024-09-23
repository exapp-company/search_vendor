<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'url' => 'sometimes|required|string|max:5000',
            'picture' => 'nullable|string|max:5000',
            'shop_id' => 'nullable|exists:shops,id',
            'in_stock' => 'sometimes|required|boolean',
            'amount' => 'nullable|integer',
            'wholesale_price' => 'nullable|numeric',
            'sku' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'Название продукта может быть предоставлено для обновления.',
            'name.required' => 'Название продукта обязательно для заполнения при обновлении.',
            'name.string' => 'Название продукта должно быть строкой.',
            'price.sometimes' => 'Цена продукта может быть предоставлена для обновления.',
            'price.required' => 'Цена продукта обязательна для заполнения при обновлении.',
            'price.numeric' => 'Цена продукта должна быть числом.',
            'url.sometimes' => 'URL продукта может быть предоставлен для обновления.',
            'url.required' => 'URL продукта обязателен для заполнения при обновлении.',
            'url.string' => 'URL продукта должен быть строкой.',
            'url.max' => 'URL продукта не может превышать 5000 символов.',
            'picture.string' => 'Изображение должно быть строкой.',
            'picture.max' => 'Изображение не может превышать 5000 символов.',
            'shop_id.exists' => 'Выбранный магазин не существует.',
            'in_stock.sometimes' => 'Статус наличия может быть предоставлен для обновления.',
            'in_stock.required' => 'Необходим статус наличия на складе при обновлении.',
            'in_stock.boolean' => 'Статус наличия на складе должен быть булевым значением.',
            'amount.integer' => 'Количество должно быть целым числом.',
            'wholesale_price.numeric' => 'Оптовая цена должна быть числом.',
            'sku.string' => 'SKU должно быть строкой.',
            'sku.max' => 'SKU не может превышать 255 символов.',
        ];
    }
}
