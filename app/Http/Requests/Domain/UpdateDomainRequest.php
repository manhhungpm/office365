<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDomainRequest extends FormRequest
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
            'domain_id' => 'required|numeric',
            'id' => [
                'required',
                'max:255',
                Rule::unique('domains', 'id')->ignore($this->input('id'))
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
            'id' => 'Domain'
        ];
    }
}