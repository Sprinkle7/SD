<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Menu;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateMenuRequest extends FormRequest
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
            //menu
            'level' => 'required|numeric|min:1|max:2',
            'is_active' => 'required|numeric|min:0|max:1',
            'parent_id' => 'required_unless:level,1|numeric|min:1',
            //translation
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'language' => ['required', 'string', new CheckLanguage],
            //image
            'thumbnail_image' => 'required_unless:level,1|nullable|string',
            'cover_images' => 'nullable|array',
            'cover_images.*.id' => 'required|numeric|min:1',
            'cover_images.*.link' => 'nullable|url',
        ];
    }
}
