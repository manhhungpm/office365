<?php

namespace App\Repositories\Admin;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Role::class;
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
        $query = $this->model->select('id', 'name', 'display_name', 'description')
            ->with('perms:permissions.id')
            ->where('name', 'LIKE', "%$keyword%")
            ->orWhere('display_name', 'LIKE', "%$keyword%");

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

    /**
     * @param $arr
     * @return mixed
     */
    public function edit($arr)
    {
        $roleIdList = $arr['permissionIdList'];
        $role = $this->model->find($arr['id']);
        $oldRole = $role;
        if ($role != null) {
            $role->fill($arr);
            if ($role->save()) {
                $role->perms()->detach();
                if ($roleIdList != null) {
                    $role->attachPermissions($roleIdList);
                }
                fireEventActionLog(UPDATE, $this->model->getTable(), $role->id, $role->name, json_encode($oldRole), json_encode($role));
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
        $roleIdList = $arr['permissionIdList'];
        $role = new $this->model;
        $role->fill($arr);
        if ($role->save()) {
            if ($roleIdList != null) {
                $role->attachPermissions($roleIdList);
            }
            fireEventActionLog(ADD, $this->model->getTable(), $role->id, $role->name, null, json_encode($role));
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id): bool
    {

        $role = $this->model->find($id);

        if ($role != null) {
            $role->perms()->detach();
            if ($this->model->where('id', $id)->delete()) {
                fireEventActionLog(DELETE, $this->model->getTable(), $role->id, $role->name, json_encode($role), null);
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    public function getRoleList()
    {
        $roleList = $this->model()::select('id', 'display_name')->orderBy('display_name')->get();
        return $roleList;
    }
}
