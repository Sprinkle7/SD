<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Option;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddTranslationOptionValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'language' => ['required', 'string', new CheckLanguage],
            'option_values' => 'required|array|min:1',
            'option_values.*.title' => 'required|string',
            'option_values.*.option_value_id' => 'required|numeric|min:1',
        ];
    }
}