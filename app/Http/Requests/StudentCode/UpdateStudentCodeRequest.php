<?php

namespace App\Http\Requests\StudentCode;

use App\Models\MSUser;
use App\Models\StudentCode;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentCodeRequest extends FormRequest
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
            'id' => 'required',
            'max_user' => [
                'required',
                'min:1',
                'numeric',
                function ($attribute, $value, $fail) {
                    $code = StudentCode::find($this->id);
                    if ($code) {
                        if ($value < $code->used_number) {
                            $fail("Số tài khoản tối đa không được nhỏ hơn số tài khoản đang sử dụng ({$code->used_number}).");
                        }
                    } else {
                        $fail('Mã bảo mật này hiện đang có lỗi');
                    }

                    $reseller_id = $this->reseller_id ? $this->reseller_id : auth()->id();
                    $reseller = User::find($reseller_id);
                    if ($reseller) {
//                        $codesMax = $reseller->codes()->where('id', '<>', $this->id)->get()->sum('max_user');
//                        $usedUsersTotal = $codesMax + $reseller->num_user_created;
//                        if ($value + $usedUsersTotal > $reseller->num_user_max) {
//                            $limit = $reseller->num_user_max - $usedUsersTotal;
//                            $fail("Reseller đã sử dụng {$usedUsersTotal}/{$reseller->num_user_max} credits." .
//                                " Số credits còn lại là {$limit}");
//                        }

                        //----new
                        $codeName = $this->input('code');

                        //Tổng số user đã tạo bằng mã đó
                        $totalUserUseThisCode = MSUser::where('code',$code->code)->get()->count();

                        //Tổng số credits còn lại : $totalCreditRest
                        $totalMaxUser = $reseller->num_user_max;
                        $totalCodeMax = (int)$reseller->codes()->sum('max_user'); //So user toi da cho phep dc tao boi ma bao mat nay
                        $totalCreateByHand = $reseller->msUser()->where('user_id',$reseller_id)->where('code','=',null)->count();
                        $totalCreditRest = $totalMaxUser- ($totalCodeMax + $totalCreateByHand);

                        //Max user trc
                        $oldMaxUser = $code->max_user;

                        if ($value < $totalUserUseThisCode || $value>($oldMaxUser+$totalCreditRest)){
                            return $fail("Số User của mã bảo mật phải >= " . $totalUserUseThisCode ." và <= " . ($oldMaxUser+$totalCreditRest));
                        }
                        //-----
                    } else {
                        $fail('Reseller hiện đang có lỗi, vui lòng thử lại sau');
                    }
                }
            ],
            'expired_date' => [
                function ($attribute, $value, $fail) {
                    if (isset($value)) {
                        if (Carbon::createFromFormat('d/m/Y', $value)->startOfDay()->lessThan(Carbon::tomorrow()->startOfDay())) {
                            $fail('Ngày hết hạn phải sau hôm nay');
                        }
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

    public function messages()
    {
        return [
            'expired_date.after' => 'Ngày hết hạn phải sau ngày hôm nay'
        ];
    }
}
