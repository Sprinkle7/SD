<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Location;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateCountryRequest extends FormRequest
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
            'has_ust_id' => 'required|numeric|min:0|max:1',
            'tax_required' => 'required|numeric|min:0|max:1',
//            'customs_price' => '',
            'name' => 'required|string|max:50|unique:country_translations',
            'language' => ['required', 'string', new CheckLanguage],
        ];
    }
}
