<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Duration;

use App\Rules\CheckLanguage;
use Illuminate\Foundation\Http\FormRequest;

class CreateDurationRequest extends FormRequest
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
            'duration' => 'required|numeric|min:1'
        ];
    }
}
