<?php

namespace App\Http\Requests\StudentCode;

use App\Models\StudentCode;
use Illuminate\Foundation\Http\FormRequest;

class DeleteStudentCodeRequest extends FormRequest
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
            'id' => [
                'required',
                function ($attribute, $value, $fail) {
                    //Check status trc khi xóa
                    $isActive = StudentCode::where('id',$value)->where('status',STUDENT_STATUS_ACTIVE)->exists();
//                    dd($isActive);
                    if ($isActive) {
                        return $fail('Không thể xóa vì tài khoản đã có user sử dụng');
                    }
                }
            ]
        ];
    }
}
