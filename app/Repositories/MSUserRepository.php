<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\Domain;
use App\Models\MSUser;
use App\Models\StudentCode;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class MSUserRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MSUser::class;
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
        $query = $this->model->where(function ($query) use ($keyword) {
            $query->where('displayName', 'LIKE', "%$keyword%")
                ->orWhere('givenName', 'LIKE', "%$keyword%")
                ->orWhere('userPrincipalName', 'LIKE', "%$keyword%")
                ->orWhere('mail', 'LIKE', "%$keyword%");
        });
        if (auth()->user()->hasRole(ROLE_RESELLER)) {
            $query->where('user_id', auth()->id());
        }
        if (!$counting) {
            $query->select('ms_user_id', 'displayName', 'givenName', 'surname', 'userPrincipalName',
                'code', 'mail', 'domain_id', 'account_id', 'createdDateTime', 'accountEnabled', 'user_id','id');
            if ($limit > 0) {
                $query->skip($offset)
                    ->take($limit);
            }

            if ($orderBy != null && $orderType != null) {
                $query->orderBy($orderBy, $orderType);
            }

            $query->with('account:id,app_name');
            $query->with('reseller:id,name');
            $query->with('domain:id,domain_id');
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
        $ms_user = $this->model->with('account:id,access_token')->find($arr['ms_user_id']);


        if ($ms_user != null) {
            try {
                $api = sendRequest(API_USER . '/' . $ms_user->id, [
                    'accountEnabled' => !!$arr['accountEnabled'],
                    'surname' => $arr['surname'],
                    'givenName' => $arr['givenName'],
                    'displayName' => $arr['displayName'],
                    'mailNickname' => $arr['username'],
                    'userPrincipalName' => $arr['userPrincipalName'],
                    "passwordPolicies" => "DisablePasswordExpiration",
                    "usageLocation" => 'VN',
                ], $ms_user->account->access_token, 'PATCH', true);

                if ($api != RESPONSE_ERROR) {
                    $ms_user->fill($arr);

                    if ($ms_user->save()) {
                        return true;
                    }
                }
            } catch (QueryException $exception) {
                throw new \App\Exceptions\QueryException();
            }
        }
        return false;
    }

    public function deleteById($id): bool
    {
        $obj = $this->model->where('ms_user_id', $id)->with('account:id,access_token')->first();
        if ($obj != null) {
            try {
                $api = sendRequest(API_USER . '/' . $obj->id, [], $obj->account->access_token, 'DELETE', true);


                if ($api != RESPONSE_ERROR) {
                    if ($obj->delete()) {
                        $reseller = User::find($obj->user_id);
                        if ($reseller) {
                            $reseller->num_user_created--;
                            $reseller->save();
                        }
                        $code = StudentCode::where('code', $obj->code)->first();
                        if ($code) {
                            $code->used_number--;
                            $code->save();
                        }
                        fireEventActionLog(DELETE, $this->model->getTable(), $obj->ms_user_id, $obj[$this->objectName], json_encode($obj), null);
                    }
                    return true;
                }
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
        $domainId = $arr['domain_id'];
        $domain = Domain::find($domainId);

        if ($domain != null) {
            $account = Account::find($domain->account_id);

            if ($account != null) {
                $data = sendRequest(API_USER, [
                    'accountEnabled' => !!$arr['accountEnabled'],
                    'displayName' => $arr['displayName'],
                    'mailNickname' => $arr['username'],
                    'userPrincipalName' => $arr['userPrincipalName'],
                    "passwordPolicies" => "DisablePasswordExpiration",
                    "usageLocation" => 'VN',
                    'passwordProfile' => [
                        'forceChangePasswordNextSignIn' => false,
                        'password' => $arr['password']
                    ]
                ], $account->access_token, 'POST', true);

                if ($data != RESPONSE_ERROR) {
                    if (isset($arr['reseller_id'])) {
                        $reseller = User::find($arr['reseller_id']);
                        if ($reseller) {
                            $reseller->num_user_created++;
                            $reseller->save();
                        }
                    }

                    $result = json_decode($data);
                    if (property_exists($result, 'id')) {
                        $ms_user = new $this->model;
                        $ms_user->id = $result->id;
                        $ms_user->displayName = $result->displayName;
                        $ms_user->givenName = $result->givenName;
                        $ms_user->mail = $result->mail;
                        $ms_user->surname = $result->surname;
                        $ms_user->userPrincipalName = $result->userPrincipalName;
                        $ms_user->accountEnabled = $arr['accountEnabled'];
                        $ms_user->createdDateTime = Carbon::now();
                        $ms_user->account_id = $account->id;
                        $ms_user->domain_id = $arr['domain_id'];
                        $ms_user->user_id = isset($arr['reseller_id']) ? $arr['reseller_id'] : auth()->id();

                        if ($ms_user->save()) {

                            Artisan::call("office:assign-user-license", [
                                'accountId' => $account->id,
                                'id' => $result->id
                            ]);

                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function guestStore($arr)
    {
        //Detect Reseller
        $codeRsl = $arr['code'];
        $idReseller = StudentCode::select('user_id')->where('code',$codeRsl)->get()->toArray()[0]['user_id'];

        $domainId = $arr['domain_id'];
        $domain = Domain::find($domainId);

        if ($domain != null) {
            $account = Account::find($domain->account_id);

            if ($account != null) {
                $data = sendRequest(API_USER, [
                    'accountEnabled' => !!$arr['accountEnabled'],
                    'displayName' => $arr['displayName'],
                    'mailNickname' => $arr['username'],
                    'userPrincipalName' => $arr['userPrincipalName'],
                    "passwordPolicies" => "DisablePasswordExpiration",
                    "usageLocation" => 'VN',
                    'passwordProfile' => [
                        'forceChangePasswordNextSignIn' => false,
                        'password' => $arr['password']
                    ]
                ], $account->access_token, 'POST', true);

                if ($data != RESPONSE_ERROR) {
                    $code = StudentCode::where('code', $arr['code'])->first();

                    $result = json_decode($data);
                    if (property_exists($result, 'id')) {
                        $ms_user = new $this->model;
                        $ms_user->id = $result->id;
                        $ms_user->displayName = $result->displayName;
                        $ms_user->givenName = $result->givenName;
                        $ms_user->mail = $result->mail;
                        $ms_user->surname = $result->surname;
                        $ms_user->userPrincipalName = $result->userPrincipalName;
                        $ms_user->accountEnabled = $arr['accountEnabled'];
                        $ms_user->createdDateTime = Carbon::now();
                        $ms_user->account_id = $account->id;
                        $ms_user->domain_id = $arr['domain_id'];
                        $ms_user->code = $code ? $code->code : null;
                        $ms_user->user_id = $idReseller;

                        if ($ms_user->save()) {

                            Artisan::call("office:assign-user-license", [
                                'accountId' => $account->id,
                                'id' => $result->id
                            ]);
                            if ($code) {
                                $code->used_number++;
                                $code->status = STUDENT_STATUS_ACTIVE;
                                $code->save();
                            }
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

}
