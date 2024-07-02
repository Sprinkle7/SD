<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Menu;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddMenuTranslationRequest extends FormRequest
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
            'description' => 'nullable|string',
            'language'=> ['required','string'],
            'cover_images' => 'nullable|array',
            'cover_images.*.id' => 'required|numeric|min:1',
            'cover_images.*.link' => 'nullable|url',
//            'level'=>'required'
        ];
    }
}
