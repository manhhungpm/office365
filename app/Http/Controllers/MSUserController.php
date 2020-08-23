<?php

namespace App\Http\Controllers;


use App\Http\Requests\MSUser\DeleteMSUserRequest;
use App\Http\Requests\MSUser\StoreMSUserRequest;
use App\Http\Requests\MSUser\UpdateMSUserRequest;
use App\Repositories\MSUserRepository;
use Illuminate\Http\Request;

class MSUserController extends Controller
{
    /**
     * @var MSUserRepository
     */
    protected $msUserRepository;

    function __construct(MSUserRepository $msUserRepository)
    {
        $this->middleware('auth')->except('guestStore');

        $this->msUserRepository = $msUserRepository;
    }

    public function store(StoreMSUserRequest $request)
    {
        $result = $this->msUserRepository->store($request->only('displayName', 'userPrincipalName',
            'password', 'domain_id', 'accountEnabled', 'username', 'reseller_id'));

        return processCommonResponse($result);
    }

    public function guestStore(StoreMSUserRequest $request)
    {
        $result = $this->msUserRepository->guestStore($request->only('code', 'displayName', 'userPrincipalName',
            'surname', 'givenName', 'password', 'domain_id', 'accountEnabled', 'username'));

        return processCommonResponse($result);
    }

    public function edit(UpdateMSUserRequest $request)
    {
        $result = $this->msUserRepository->edit($request->only('ms_user_id', 'displayName', 'userPrincipalName',
            'surname', 'givenName', 'domain_id', 'accountEnabled', 'username'));

        return processCommonResponse($result);
    }

    public function changeStatus(Request $request)
    {
        $result = $this->msUserRepository->changeStatus($request->input('id'), $request->input('status'));

        return processCommonResponse($result);
    }

    public function delete(DeleteMSUserRequest $request)
    {
        $result = $this->msUserRepository->deleteById($request->input('ms_user_id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->msUserRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->msUserRepository->getList(
                $params['keyword'],
                false,
                $params['length'],
                $params['start'],
                $params['orderBy'],
                $params['orderType']
            ),
            'draw' => $params['draw'],
            'recordsFiltered' => $total
        );

        return response()->json($arr);
    }

    public function listAll(Request $request)
    {
        $length = 10;

        $page = $request->input('page');
        $search = $request->input('search');

        $data = $this->msUserRepository->getListAll($search, false, $length, $page * $length);
        $total = $this->msUserRepository->getListAll($search, true);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }
}
