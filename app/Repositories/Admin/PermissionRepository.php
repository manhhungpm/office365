<?php

namespace App\Repositories\Admin;

use App\Models\Permission;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Permission::class;
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
        $permission = $this->model->find($arr['id']);
        $oldPermission = json_encode($permission);

        if ($permission != null) {
            $permission->fill($arr);

            if ($permission->save()) {
                fireEventActionLog(UPDATE, $this->model->getTable(), $permission->id, $permission->name, $oldPermission, json_encode($permission));
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
        $permission = new $this->model;
        $permission->fill($arr);

        if ($permission->save()) {
            fireEventActionLog(ADD, $this->model->getTable(), $permission->id, $permission->name, null, json_encode($permission));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id): bool
    {
        $permission = $this->model->find($id);

        if ($permission != null) {
            $permission->roles()->detach();
            if ($permission->delete()) {
                fireEventActionLog(DELETE, $this->model->getTable(), $permission->id, $permission->name, json_encode($permission), null);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @return array|null
     */
    public function getPermissionList()
    {
        $permissionList = $this->model()::select('id', 'display_name', 'name')->orderBy('display_name')->get();
        return $permissionList;
    }
}
