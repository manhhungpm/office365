<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            'name' => [
                'required',
                'max:255',
                'unique:users',
                'min:6',
                'regex:/^[A-Za-z][A-Za-z0-9_]{0,}[A-Za-z0-9]$/'
            ],
            'display_name' => [
                'required',
                'max:64',
            ],
            'password' => [
                'required',
                'max:128',
                'regex:/^(?=.*[a-z])+(?=.*[A-Z])+(?=.*\d)+(?=.*[$~!#^()@$!%*?&])[A-Za-z\d$~!#^()@$!%*?&]{8,}/',
            ],
            'email' => [
                'required',
                'max:255',
                'email',
                'unique:users'
            ],
            'note' => [
                'max:1024'
            ],
            'code' => [
                'required',
                'unique:users',
            ]
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('fields.user_name'),
            'display_name' => trans('fields.user_display_name'),
            'password' => trans('fields.user_password'),
            'email' => trans('fields.user_email'),
            'code' => 'Code'
        ];
    }
}
