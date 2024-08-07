<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Home\Section;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddTranslationSectionRequest extends FormRequest
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
            'language' => ['required', 'string', new CheckLanguage],
        ];
    }
}
