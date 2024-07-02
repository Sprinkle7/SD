<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'reorder' => 'required|boolean',
            'cover_image' => 'required|string',
            'video' => 'required|string',
            ///translate
            'title' => 'required|string',
            'benefit_desc' => 'required|string',
            'item_desc' => 'required|string',
            'language' => 'required|string',

            ///category
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|numeric|min:1',
        ];
    }
}
