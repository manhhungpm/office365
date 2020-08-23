<?php

namespace App\Http\Controllers;


use App\Http\Requests\Account\DeleteAccountRequest;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    function __construct(AccountRepository $accountRepository)
    {
        $this->middleware('auth');

        $this->accountRepository = $accountRepository;
    }

    public function store(StoreAccountRequest $request)
    {
        $result = $this->accountRepository->store($request->only('app_name', 'description', 'client_id', 'client_secret', 'tenant_id'));

        return processCommonResponse($result);
    }

    public function edit(UpdateAccountRequest $request)
    {
        $result = $this->accountRepository->edit($request->only('id', 'app_name', 'description', 'client_id', 'client_secret', 'tenant_id', 'status'));

        return processCommonResponse($result);
    }

    public function changeStatus(Request $request)
    {
        $result = $this->accountRepository->changeStatus($request->input('id'), $request->input('status'));

        return processCommonResponse($result);
    }

    public function delete(DeleteAccountRequest $request)
    {
        $result = $this->accountRepository->deleteById($request->input('id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->accountRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->accountRepository->getList(
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

        $data = $this->accountRepository->getListAll($search, false, $length, $page * $length);
        $total = $this->accountRepository->getListAll($search, true);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }
}