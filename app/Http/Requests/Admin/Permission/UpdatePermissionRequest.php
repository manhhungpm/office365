<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
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
            'id'=>'required|numeric',
            'name' => [
                'required',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($this->input('id'))
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
            'name' => trans('fields.permission_name'),
            'display_name' => trans('fields.permission_display_name'),
            'description' => trans('fields.permission_description')
        ];
    }
}
