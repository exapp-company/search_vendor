<?php

namespace App\Http\Requests;

use App\Models\Office;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRequest extends FormRequest
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
            'name' => 'sometimes|string|min:3',
            'phone' => ['sometimes', 'string', 'regex:/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i'],
            'address' => 'sometimes|nullable|string|min:3',
            'email' => 'sometimes|nullable|string|email',
            'lat' => 'nullable|required_with:lon|numeric',
            'lon' => 'nullable|required_with:lat|numeric',
            'use_parent_feed' => 'sometimes|boolean',
            'use_parent_mapping' => 'sometimes|boolean',
            'feed_url' => 'sometimes|nullable|url',
            'feed_type' => 'required_with:feed_url,nullable|string|in:xml,json',
            'feed_mapping' => 'sometimes|nullable|array:name,price,picture,url,in_stock,amount,sku,wholesale_price',
            'feed_mapping.*' => 'sometimes|nullable|array:type,name|size:2',
            'feed_mapping.*.type' => 'required|in:attribute,field',
            'feed_mapping.*.name' => 'required|string',
            'locations' => 'sometimes|nullable|array',
            'locations.*' => 'numeric|exists:locations,id'
        ];
    }
}
