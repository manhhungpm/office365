<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
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
            'id' => 'required|max:255|unique:domains,id'
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