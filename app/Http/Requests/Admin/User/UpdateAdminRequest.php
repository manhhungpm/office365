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
                    //Lấy ra số max user của mã bảo mật
                    $reseller = User::find($this->input('id'));
                    $totalCodeMax = (int)$reseller->codes()->sum('max_user');

                    //Lấy ra số User đã tạo
                    //$numUserCreated = User::select('num_user_created')->where('id', $this->input('id'))->get()->toArray()[0]['num_user_created'];

                    //Số user tạo bằng tay chứ ko phải dùng mã
                    $totalCreateByHand = $reseller->msUser()->where('user_id',$this->input('id'))->where('code','=',null)->count();

                    $total = $totalCodeMax + $totalCreateByHand;

                    if ($value < $total) {
                        return $fail('Số credits phải lớn hơn ' . $total);
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
