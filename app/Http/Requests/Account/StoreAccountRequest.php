<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
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
            'app_name' => 'required|max:100|unique:accounts,app_name',
            'description' => 'max:255',
            'client_id' => 'required|max:255',
            'client_secret' => 'required|max:255',
            'tenant_id' => 'required|max:255'
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Tên ứng dụng',
            'description' => 'Mô tả',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'tenant_id' => 'Tenant ID'
        ];
    }
}