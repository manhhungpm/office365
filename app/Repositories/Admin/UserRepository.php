<?php

namespace App\Repositories\Admin;

use App\Models\MSUser;
use App\Models\Role;
use App\Models\StudentCode;
use App\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param null $keyword
     * @param bool $counting
     * @param string $role
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $orderType
     * @return mixed
     */
    public function getList($keyword = null, $counting = false, $role = '', $limit = 10, $offset = 0, $orderBy = 'name', $orderType = 'asc')
    {
        $query = $this->model
            ->select('id', 'name', 'code', 'display_name', 'phone', 'num_user_max', 'num_user_created', 'email', 'status', 'created_at', 'updated_at', 'note')
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%");
            });


        if ($role !== '') {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if (!$counting) {
            $query->with('domains:domains.domain_id,domains.id');

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

    /**
     * @param $arr
     * @return mixed
     */
    public function edit($arr)
    {
//        dd($arr);
        $user = $this->model->find($arr['id']);
        $oldUser = json_encode($user);
        if ($user != null) {
            $user->fill($arr);
            if ($user->save()) {
                if ($arr['domains'] != null && is_array($arr['domains'])) {
                    $user->domains()->detach();
                    $user->domains()->attach($arr['domains']);
                }

                fireEventActionLog(UPDATE, $this->model->getTable(), $user->id, $user->name, $oldUser, json_encode($user));
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function store($arr)
    {
        $role = Role::where('name', $arr['role'])->first();

        if ($role != null) {
            $user = new $this->model;
            $user->fill($arr);
            $user->password = Hash::make($arr['password']);
            if ($user->save()) {
                if ($arr['domains'] != null && count($arr['domains']) > 0) {
                    $user->domains()->attach($arr['domains']);
                }

                if ($role != null) {
                    $user->attachRole($role->id);
                    fireEventActionLog(ADD, $this->model->getTable(), $user->id, $user->name, null, json_encode($user));
                }
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteUser($id)
    {
        //Check xem co dang tao User nao ko
        $isHaveUserCreated = $this->model->select('num_user_created')->where('id',$id)->get()->toArray()[0]['num_user_created'];
        //Check xem co dang tao Student code nao ko
        $isHaveStudentCode = StudentCode::where('user_id',$id)->exists();

        if ($isHaveUserCreated == 0 && $isHaveStudentCode == false){
            $user = $this->model->find($id);

            if ($user != null) {
                $user->roles()->detach();
                if ($this->model->where('id', $id)->delete()) {
                    fireEventActionLog(DELETE, $this->model->getTable(), $user->id, $user->name, json_encode($user), null);
                    return true;
                } else {
                    return false;
                }
            }
        }else {
            return CODE_ERROR_DELETE_USER_WHEN_HAVE_USER_CREATED_AND_STUDENT_CODE;
        }
    }

    public function changePassword($arr)
    {
        $user = $this->model->find(auth()->user()->id);
        if ($user != null) {
            $user->password = Hash::make($arr['password']);
            return $user->save();
        }
        return false;
    }


    public function updatePassword($arr)
    {
        $user = $this->model->find($arr['id']);
        if ($user != null) {
            $user->password = Hash::make($arr['password']);
            return $user->save();
        }
        return false;
    }

    public function updateProfile($arr)
    {
        $user = $this->model->find($arr['id']);
        if ($user != null) {
            $user->fill($arr);
            return $user->save();
        }
        return false;
    }

    public function listingAll($isCounting = false, $role = '', $keyword = null, $limit = 10, $offset = 0)
    {
        $query = $this->model->select('id', 'name', 'display_name')
            ->where('name', 'LIKE', "%$keyword%");

        if ($role !== '') {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if (!$isCounting) {
            $query->skip($offset)
                ->take($limit)
                ->orderBy('name', 'asc');
        } else {
            return $query->count();
        }

        return $query->get();
    }

    public function getListUserCreated($resellerId = null, $keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'id', $orderType = 'asc')
    {
        $query = MSUser::select('id', 'displayName', 'givenName', 'mail', 'mobilePhone', 'userPrincipalName',
            'account_id', 'domain_id', 'sync_at', 'state', 'userType',
            'createdDateTime', 'surname', 'accountEnabled', 'user_id')
            ->where('user_id',$resellerId);

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
}
