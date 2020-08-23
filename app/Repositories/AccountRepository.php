<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Account::class;
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
    public function getList($keyword = null, $counting = false, $limit = 10, $offset = 0, $orderBy = 'app_name', $orderType = 'asc')
    {
        $query = $this->model
            ->where('app_name', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('id', 'app_name', 'description', 'client_id', 'client_secret', 'tenant_id', 'status');
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
            ->where('app_name', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('id', 'app_name');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            $query->orderBy('app_name', 'asc');

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
        $account = $this->model->find($arr['id']);

        if ($account != null) {
            $account->fill($arr);

            if ($account->save()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function changeStatus($id, $status)
    {
        $account = $this->model->find($id);

        if ($account != null) {
            $account->status = $status;

            if ($account->save()) {
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
        $account = new $this->model;
        $account->fill($arr);
        $account->status = ACCOUNT_STATUS_ACTIVE;
        $account->user_id = auth()->id();

        if ($account->save()) {
            return true;
        } else {
            return false;
        }
    }
}