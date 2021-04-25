<?php

namespace App\Repositories;

use App\Models\MSUser;
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
    public function getList($keyword = null, $searchParams = [], $counting = false, $limit = 10, $offset = 0, $orderBy = 'name', $orderType = 'asc')
    {
        $this->setStatusUnused();

        $query = $this->model
            ->where('code', 'LIKE', "%$keyword%");

        collect($searchParams)->each(function ($item, $key) use ($query) {
            switch ($key) {
                case 'domain':
                    if (isset($item)) {
                        $query->whereHas('domain', function ($q) use ($item) {
                            $q->where('domain_id', $item);
                        });
                    }
                    break;
                case 'reseller':
                    if (isset($item)) {
                        $query->whereHas('reseller', function ($q) use ($item) {
                            $q->where('id', $item);
                        });
                    }
                    break;
                default:
                    break;
            }
        });

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
        //Thoi gian het han mac dinh la 01-01-2099
        if (empty($arr['expired_date'])) {
            $arr['expired_date'] = "01/01/2099";
        }

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

    public function deleteStudentCode($id)
    {
        $isActive = StudentCode::where('id', $id)->where('status', STUDENT_STATUS_ACTIVE)->exists();

        if ($isActive) {
            return CODE_ERROR_DELETE_STUDENT_CODE_WHEN_HAVE_STATUS_ACTIVE;
        } else {
            if ($this->model->find($id)->delete()) {
                return CODE_SUCCESS;
            }
            return CODE_ERROR;
        }
    }

    public function getListUserCreated($code = null, $keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'id', $orderType = 'asc')
    {
        $query = MSUser::select('id', 'displayName', 'givenName', 'mail', 'mobilePhone', 'userPrincipalName',
            'account_id', 'domain_id', 'sync_at', 'state', 'userType',
            'createdDateTime', 'surname', 'accountEnabled', 'user_id')
            ->where('code', $code);

        if (!$counting) {
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

    public function setStatusUnused()
    {
        //Set status = 0 khi chua co user su dung
        $this->model->where('used_number', 0)->update([
            'status' => 0
        ]);
    }

    public function studentCodeCheckApi($arr)
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
}
