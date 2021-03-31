<?php

namespace App\Http\Requests\MSUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMSUserRequest extends FormRequest
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

    public function rules()
    {
        return [
            'ms_user_id' => 'required|numeric',
            'displayName' => [
                'required',
                'max:255',
//                Rule::unique('ms_users', 'displayName')->ignore($this->input('ms_user_id'), 'ms_user_id')
            ],
            'userPrincipalName' => [
                'required',
                'max:255',
                Rule::unique('ms_users', 'userPrincipalName')->ignore($this->input('ms_user_id'), 'ms_user_id')
            ],
            'surname' => 'max:50',
            'givenName' => 'max:50'
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'displayName' => 'Tên hiển thị',
            'userPrincipalName' => 'Tên người dùng',
            'surname' => 'Họ',
            'givenName' => 'Tên'
        ];
    }
}
