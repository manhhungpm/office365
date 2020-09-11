<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\User\ChangePasswordRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\User\DeleteAdminRequest;
use App\Http\Requests\Admin\User\UpdateAdminRequest;
use App\Http\Requests\Admin\User\StoreAdminRequest;
use App\Http\Requests\Admin\User\UpdatePasswordRequest;
use App\Repositories\Admin\UserRepository;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth');

        $this->userRepository = $userRepository;
    }

    public function store(StoreAdminRequest $request)
    {
        $result = $this->userRepository->store($request->only('name', 'display_name', 'email', 'password',
            'role', 'phone', 'num_user_max', 'status', 'domains', 'code', 'note'));

        return processCommonResponse($result);
    }

    public function edit(UpdateAdminRequest $request)
    {
        $result = $this->userRepository->edit($request->only('id', 'name', 'display_name', 'description',
            'phone', 'num_user_max', 'status', 'domains', 'code', 'note'));

        return processCommonResponse($result);
    }

    public function delete(DeleteAdminRequest $request)
    {
        $result = $this->userRepository->deleteUser($request->input('id'));

        return processCommonResponse($result);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $result = $this->userRepository->updatePassword($request->only('id', 'password'));
        return processCommonResponse($result);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->userRepository->changePassword($request->only('password','current_password'));
        return processCommonResponse($result);
    }

    public function updateProfile(Request $request){
        $result = $this->userRepository->updateProfile($request->only('id', 'display_name', 'email', 'phone'));
        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->userRepository->getList(
            $params['keyword'],
            true,
            $request->input('role')
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->userRepository->getList(
                $params['keyword'],
                false,
                $request->input('role'),
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

    public function listingAll(Request $request)
    {
        $length = 10;

        $page = $request->input('page');
        $search = $request->input('search');

        $data = $this->userRepository->listingAll(false, '', $search, $length, $page * $length);
        $total = $this->userRepository->listingAll(true, '', $search);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }

    public function listingReseller(Request $request)
    {
        $length = 10;

        $page = $request->input('page');
        $search = $request->input('search');

        $data = $this->userRepository->listingAll(false, 'Reseller', $search, $length, $page * $length);
        $total = $this->userRepository->listingAll(true, 'Reseller', $search);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }

    public function listingUserCreated(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->userRepository->getListUserCreated(
            $request->input('reseller_id'),
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->userRepository->getListUserCreated(
                $request->input('reseller_id'),
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

    public function increaseMaxUser(Request $request){
        $result = $this->userRepository->increaseMaxUser($request->input('id'));

        return processCommonResponse($result);
    }
}
