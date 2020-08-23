<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
            'id'=>'required|numeric',
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->input('id'))
            ],
            'display_name' => 'required|max:255',
            'description' => 'max:255'
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('fields.role_name'),
            'display_name' => trans('fields.role_display_name'),
            'description' => trans('fields.role_description')
        ];
    }
}
