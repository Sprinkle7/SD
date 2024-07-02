<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Home;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateHomeRequest extends FormRequest
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
            'slider_id' => 'required|numeric|min:1',
            'sections_id' => 'required_if:type,manual|array|min:1',
            'sections_id.*' => 'required|numeric|min:1',
            'language' => ['required', 'string', new CheckLanguage],
            'description'=>'nullable|string'

        ];
    }
}
