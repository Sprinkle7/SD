<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Categroy;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
            'title' => 'required|string|unique:category_translations',
            'language' => ['required', 'string', new CheckLanguage]
        ];
    }
}
