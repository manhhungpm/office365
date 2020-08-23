<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'id' => 'required',
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
            'password' => trans('fields.user_password'),
        ];
    }
}
