<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!password_verify ($value, auth()->user()->password)) {
                        $fail("$attribute không đúng");
                    }
                },
            ],
            'password' => [
                'required',
                'max:128',
                'regex:/^(?=.*[a-z])+(?=.*[A-Z])+(?=.*\d)+(?=.*[$~!#^()@$!%*?&])[A-Za-z\d$~!#^()@$!%*?&]{8,}/',
            ],
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'current_password' => 'Mật khẩu cũ',
            'password' => trans('fields.user_password'),
        ];
    }
}
