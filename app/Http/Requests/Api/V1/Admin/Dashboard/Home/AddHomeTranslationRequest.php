<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Home;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class AddHomeTranslationRequest extends FormRequest
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
            'language' => ['required', 'string', new CheckLanguage],
            'description'=>'nullable|string'
        ];
    }
}
