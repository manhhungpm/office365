<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\Domain;
use App\Models\MSUser;
use App\Models\UserDomain;
use Illuminate\Support\Facades\Artisan;

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
            $query->select('id', 'app_name', 'description', 'client_id', 'client_secret', 'tenant_id', 'status','access_token');
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
            //Gọi sync user và domain luôn
            Artisan::call("office:get-token");
            Artisan::call("office:sync-user");
            Artisan::call("office:sync-domain");

            return true;
        } else {
            return false;
        }
    }

    public function getLicense($id)
    {
        $accounts = Account::where('status', ACCOUNT_STATUS_ACTIVE)->where('id',$id)->get();
        $access_token = $accounts[0]['access_token'];

        $data = sendRequest(GRAPH_ROOT . "/subscribedSkus", [], $access_token, 'GET');
        if ($data != '') {
            $result = json_decode($data);

            return ($result);
        }
    }

    public function syncOffline(){
        //Gọi sync user và domain luôn
        Artisan::call("office:get-token");
        Artisan::call("office:sync-user");
        Artisan::call("office:sync-domain");

        return true;
    }

    public function deleteAccount($id){
        $arrDomainId = Domain::select('domain_id')->where('account_id',$id)->get()->toArray();

        if (sizeof($arrDomainId) != 0){
            foreach ($arrDomainId as $item){
                if(UserDomain::where('domain_id',$item['domain_id'])->count() != 0){
                    return CODE_ERROR_DELETE_ACCOUNT_WHEN_DOMAIN_ASSIGNED_FOR_USER; //khong xoa vi co reseller dang dc gan domain day
                }
            }
            //Xoa account
            Account::find($id)->delete();
            Domain::where('account_id',$id)->delete();
            MSUser::where('account_id',$id)->delete();
        } else {
            //Xoa luon Account
            Account::find($id)->delete();
            Domain::where('account_id',$id)->delete();
            MSUser::where('account_id',$id)->delete();
        }
    }
}
