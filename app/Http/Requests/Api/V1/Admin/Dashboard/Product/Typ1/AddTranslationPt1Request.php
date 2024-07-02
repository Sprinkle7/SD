<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ1;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddTranslationPt1Request extends FormRequest
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
            'title' => 'required|string',
            'benefit_desc' => 'filled|string',
            'item_desc' => 'filled|string',
            'feature_desc' => 'filled|string',
            'language' => ['required','string',new CheckLanguage],
        ];
    }
}
