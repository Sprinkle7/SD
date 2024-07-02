<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\ShippingInfo;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddTranslationShippingInfoRequest extends FormRequest
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
            'title'=> 'required|string|max:50',
            'description'=> 'required|string',
            'language'=> ['required','string',new CheckLanguage],
        ];
    }
}
