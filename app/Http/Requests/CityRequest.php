<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    public function rules(): array
    {
        $city = $this->route('city');
        $unique_rule = Rule::unique('cities', 'name')->whereNull('deleted_at');
        if ($city) {
            $unique_rule->ignore($city);
        }
        return [
            'name' => ['required', 'string', $unique_rule],
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, укажите название города.',
            'name.unique' => "Такой город уже сущесвует",
            'country_id.required' => 'Пожалуйста, выберите страну.',
            'country_id.exists' => 'Выбранная страна не существует.',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
