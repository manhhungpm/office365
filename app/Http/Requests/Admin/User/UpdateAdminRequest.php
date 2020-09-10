<?php

namespace App\Http\Requests\Admin\User;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
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
            'id' => 'required|numeric',
            'name' => [
                'required',
                'max:255',
                'min:6',
                'regex:/^[A-Za-z][A-Za-z0-9_]{0,}[A-Za-z0-9]$/',
                Rule::unique('users', 'name')->ignore($this->input('id'))
            ],
            'display_name' => [
                'required',
                'max:64',
            ],
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users', 'email')->ignore($this->input('id'))
            ],
            'note' => [
                'max:1024'
            ],
            'num_user_max' => [
                function ($attribute, $value, $fail) {
                    $numUserCreated = User::select('num_user_created')->where('id', $this->input('id'))->get()->toArray()[0]['num_user_created'];
                    if ($value < $numUserCreated) {
                        return $fail('Số người dùng cho phép phải lớn hơn số User đã tạo');
                    }
                },
            ],
            'code' => [
                'required',
                Rule::unique('users', 'code')->ignore($this->input('id'))
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
            'email' => trans('fields.user_description'),
            'code' => "Mã Reseller"
        ];
    }
}
