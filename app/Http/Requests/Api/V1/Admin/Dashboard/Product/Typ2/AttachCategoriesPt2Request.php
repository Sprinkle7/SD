<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product\Typ2;

use Illuminate\Foundation\Http\FormRequest;

class AttachCategoriesPt2Request extends FormRequest
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
            'categories' => 'required|array|min:1',
            'categories.*.category_id' => 'required|numeric|min:1',
            'categories.*.arrange' => 'required|numeric|min:1',
        ];
    }
}
