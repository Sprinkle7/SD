<?php

namespace App\Http\Requests\Api\V1\User\Site\NewLetter;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubribeRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|string|max:50|unique:news_letters',
        ];
    }
}
