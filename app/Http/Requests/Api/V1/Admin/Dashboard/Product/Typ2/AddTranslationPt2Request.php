<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddTranslationPt2Request extends FormRequest
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
            'title' => 'required|string|unique:product_translations,title',
            'benefit_desc' => 'required|string',
            'item_desc' => 'required|string',
            'feature_desc' => 'filled|string',
            'language' => ['required','string',new CheckLanguage],
        ];
    }
}
