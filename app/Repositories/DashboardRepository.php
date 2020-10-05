<?php

namespace App\Repositories;

use App\Models\MSUser;
use App\Models\StudentCode;
use App\User;

class DashboardRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function getResellerStats()
    {
        $reseller = $this->model->find(auth()->id());
        if ($reseller) {
            return [
                'reseller' => $reseller,
                'codes' => $reseller->codes()->get(),
                'totalCodeUsed' => $reseller->codes()->sum('used_number'), //So user su dung ma bao mat nay
                'totalCodeMax' => $reseller->codes()->sum('max_user'), //So user toi da cho phep dc tao boi ma bao mat nay
                'totalCreateByHand' => $reseller->msUser()->where('user_id',auth()->id())->where('code','=',null)->count()
            ];
        } else {
            return false;
        }
    }

    public function getAdminStats()
    {
        try {
            $resellerCount = $this->model->whereHas('roles', function ($q) {
                $q->where('name', ROLE_RESELLER);
            })->count();

            return [
                'reseller' => $resellerCount,
                'codes' => StudentCode::count(),
                'msUser' => MSUser::count(),
            ];
        } catch
        (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
