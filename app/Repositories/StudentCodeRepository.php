<?php

namespace App\Repositories;

use App\Models\StudentCode;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StudentCodeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return StudentCode::class;
    }

    /**
     * @param null $keyword
     * @param bool $counting
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $orderType
     * @return mixed
     */
    public function getList($keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'name', $orderType = 'asc')
    {
        $query = $this->model
            ->where('code', 'LIKE', "%$keyword%");
        if (auth()->user()->hasRole(ROLE_RESELLER)) {
            $query->where('user_id', auth()->id());
        }
        if (!$counting) {
            $query->select('id', 'code', 'status', 'user_id', 'domain_id', 'created_at', 'expired_date', 'max_user', 'used_number');
            $query->with('reseller:id,name,display_name');
            $query->with('domain:id,domain_id');

            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            if ($orderBy != null && $orderType != null) {
                $query->orderBy($orderBy, $orderType);
            }
        } else {
            return $query->count();
        }

        return $query->get();
    }

    public function getListAll($keyword = null, $counting = false, $limit = 10, $offset = 0)
    {
        $query = $this->model
            ->where('code', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('id', 'code');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            $query->orderBy('code', 'asc');

        } else {
            return $query->count();
        }

        return $query->get();
    }

    public function check($arr)
    {
        $studentCode = $this->model->with('domain:id,domain_id')
            ->where('code', $arr['code'])
            ->whereRaw('used_number < max_user')
            ->where(function ($query) {
                $query->whereNull('expired_date')
                    ->orWhere('expired_date', '>', Carbon::now());
            })
            ->first();
        if ($studentCode != null) {
            return $studentCode->domain;
        }

        return false;
    }

    /**
     * @param $arr
     * @return mixed
     */
    public function edit($arr)
    {
        $studentCode = $this->model->find($arr['id']);
        $arr['expired_date'] = isset($arr['expired_date']) ? Carbon::createFromFormat('d/m/Y', $arr['expired_date']) : Carbon::now()->addDay(1);
        if ($studentCode != null) {
            $studentCode->fill($arr);

            if ($studentCode->save()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function store($arr)
    {
        $domainId = $arr['domain_id'];

        $resellerId = $arr['reseller_id'];
        $expiredDate = isset($arr['expired_date']) ? Carbon::createFromFormat('d/m/Y', $arr['expired_date']) : Carbon::now()->addDay(1);
        $prefix = '';
        $userId = '';

        if (auth()->user()->hasRole('Reseller')) {
            $prefix = auth()->user()->code;
            $userId = auth()->id();
        } else {
            if ($resellerId != null) {
                $reseller = User::find($resellerId);

                if ($reseller != null) {
                    $prefix = $reseller->code;
                    $userId = $reseller->id;
                } else {
                    $prefix = 'AD';
                    $userId = auth()->id();
                }
            } else {
                $prefix = 'AD';
                $userId = auth()->id();
            }
        }

        $new = [];


        $new[] = [
            'code' => $prefix . '_' . Str::random(8),
            'domain_id' => $domainId,
            'user_id' => $userId,
            'status' => STUDENT_STATUS_UNUSED,
            'created_at' => Carbon::now(),
            'expired_date' => $expiredDate,
            'max_user' => $arr['max_user']
        ];


        if (StudentCode::insert($new)) {
            return true;
        }

        return false;
    }
}