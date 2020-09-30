<?php

namespace App\Http\Requests\MSUser;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreMSUserRequest extends FormRequest
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
            'displayName' => 'required|max:255',
            'userPrincipalName' => 'required|max:255|unique:ms_users,userPrincipalName',
            'surname' => 'max:50',
            'givenName' => 'max:50',
            'password' => 'min:8|max:100',
            'reseller_id' => [
                function ($attribute, $value, $fail) {
                    $reseller = User::where('id', $value)->first();
                    if ($reseller) {
                        $codesMax = $reseller->codes()->get()->sum('max_user');
                        $usedUsersTotal = $codesMax + $reseller->num_user_created;
                        if ($usedUsersTotal >= $reseller->num_user_max) {
                            $fail("Reseller đã sử dụng {$usedUsersTotal}/{$reseller->num_user_max} credits.");
                        }
                    } else {
                        $fail('Reseller hiện đang có lỗi, vui lòng thử lại sau');
                    }
                }
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
            'displayName' => 'Tên hiển thị',
            'userPrincipalName' => 'Tên người dùng',
            'surname' => 'Họ',
            'givenName' => 'Tên',
            'password' => 'Mật khẩu'
        ];
    }
}
