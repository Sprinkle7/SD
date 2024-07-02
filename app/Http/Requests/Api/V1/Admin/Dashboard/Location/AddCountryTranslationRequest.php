<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Location;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddCountryTranslationRequest extends FormRequest
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
//            'countryId' => 'required',
//            'hasUsdId' => ['required', 'regex:/(^0$)|(^1$)/'],
//            'shipping_price' => 'required|required|regex:/^\d{1,10}(\.\d{1,2})?$/',
//            'customs_price' => '',
            'name' => 'required|string|max:50',
            'language' => ['required', 'string', new CheckLanguage],
        ];
    }
}
