<?php

namespace App\Http\Requests\Api\V1\Admin\Dashboard\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'code'=>'filled|string|unique:coupons',
            'percent'=>'required|numeric',
            'expires_at'=>'required|date',
        ];
    }
}
