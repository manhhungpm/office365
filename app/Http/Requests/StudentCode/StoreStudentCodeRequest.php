<?php

namespace App\Http\Requests\StudentCode;

use App\Models\StudentCode;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentCodeRequest extends FormRequest
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
            'max_user' => [
                'required',
                'min:1',
                'numeric',
                function ($attribute, $value, $fail) {


                    $reseller_id = $this->reseller_id ? $this->reseller_id : auth()->id();
                    $reseller = User::find($reseller_id);
                    if ($reseller) {
                        $codesMax = $reseller->codes()->get()->sum('max_user');
                        $usedUsersTotal = $codesMax + $reseller->num_user_created;
                        if ($value + $usedUsersTotal > $reseller->num_user_max) {
                            $limit = $reseller->num_user_max - $usedUsersTotal;
                            $fail("Reseller đã sử dụng {$usedUsersTotal}/{$reseller->num_user_max} tài khoản." .
                                " Số tài khoản tối đa cho mã này là {$limit}");
                        }
                    } else {
                        $fail('Reseller hiện đang có lỗi, vui lòng thử lại sau');
                    }
                }
            ],
            'expired_date' => [
                'required',
                function($attribute, $value, $fail) {
                    if( Carbon::createFromFormat('d/m/Y', $value)->startOfDay()->lessThan(Carbon::tomorrow()->startOfDay()) ) {
                        $fail('Ngày hết hạn phải sau hôm nay');
                    }
                }
            ],
            'domain_id' => 'required',
            'reseller_id' => 'required',
        ];
    }

    /**
     * assign attributes
     * @return array
     */
    public function attributes()
    {
        return [
            'max_user' => 'Số MSuser tối đa',
            'domain_id' => 'Domain',
            'reseller_id' => 'Reseller',
            'expired_date' => 'Ngày hết hạn',
            'today' => 'hôm nay',
        ];
    }

}