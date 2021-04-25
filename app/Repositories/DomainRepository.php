<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\Domain;

class DomainRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Domain::class;
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
            ->where('id', 'LIKE', "%$keyword%");

        if (!$counting) {
            $query->select('domain_id', 'authenticationType', 'availabilityStatus', 'id', 'isAdminManaged', 'isDefault', 'isInitial', 'isRoot', 'isVerified', 'supportedServices', 'state', 'account_id', 'sync_at');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            if ($orderBy != null && $orderType != null) {
                $query->orderBy($orderBy, $orderType);
            }

            $query->with('account:id,app_name');
            $query->withCount('resellers');
        } else {
            return $query->count();
        }

        return $query->get();
    }

    public function getListAll($keyword = null, $reseller = null, $counting = false, $limit = 10, $offset = 0)
    {
        $query = $this->model
            ->where('id', 'LIKE', "%$keyword%")->where('isVerified',1);

        if ($reseller != null) {
            $query->whereHas('resellers', function ($q) use ($reseller) {
                $q->where('users.id', $reseller);
            });
        }

        if (!$counting) {
            $query->select('domains.id', 'domain_id');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            $query->orderBy('domains.id', 'asc');

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
        $domain = $this->model->find($arr['domain_id']);

        if ($domain != null) {
            $domain->fill($arr);

            if ($domain->save()) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function deleteById($id): bool
    {
        $obj = $this->model->where('domain_id', $id)->with('account:id,access_token')->first();

        if ($obj != null) {
            try {
                sendRequest(API_DOMAIN . '/' . $obj->id, [], $obj->account->access_token, 'DELETE', true);

                if ($obj->delete()) {
                    fireEventActionLog(DELETE, $this->model->getTable(), $obj->domain_id, $obj[$this->objectName], json_encode($obj), null);
                }
                return true;
            } catch (QueryException $exception) {
                throw new \App\Exceptions\QueryException();
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
        $account = Account::find($arr['account_id']);
//        dd($account->access_token);
        if ($account != null) {
            $data = sendRequest(API_DOMAIN, [
                'id' => $arr['id']
            ], $account->access_token, 'POST', true);
            if ($data != '') {
                $result = json_decode($data);

                if (property_exists($result, 'id')) {
                    $domain = new $this->model;
                    $domain->id = $result->id;
                    $domain->authenticationType = $result->authenticationType;
                    $domain->availabilityStatus = $result->availabilityStatus;
                    $domain->isAdminManaged = $result->isAdminManaged;
                    $domain->isDefault = $result->isDefault;
                    $domain->isInitial = $result->isInitial;
                    $domain->isRoot = $result->isRoot;
                    $domain->account_id = $arr['account_id'];

                    if ($domain->save()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
