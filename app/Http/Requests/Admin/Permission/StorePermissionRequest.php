<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required|max:255|unique:permissions,name',
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
