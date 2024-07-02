<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreatePt2Request extends FormRequest
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
//            'reorder' => 'required|boolean',
            'cover_image' => 'nullable|string',
//            'video' => 'nullable|string',
            'data_sheet_pdf' => 'nullable|string',
            'assembly_pdf' => 'nullable|string',
            ///translate
            'title' => 'required|string|unique:product_translations,title',
            'benefit_desc' => 'required|string',
            'feature_desc' => 'filled|string',
            'item_desc' => 'required|string',
            'language' => ['required', 'string', new CheckLanguage],
        ];
    }
}
