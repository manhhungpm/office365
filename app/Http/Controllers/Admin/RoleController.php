<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\Role\DeleteRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Repositories\Admin\RoleRepository;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * @var roleRepository
     */
    protected $roleRepository;

    function __construct(RoleRepository $roleRepository)
    {
        $this->middleware('auth');

        $this->roleRepository = $roleRepository;
    }

    public function store(StoreRoleRequest $request)
    {
        $result = $this->roleRepository->store($request->only('name', 'display_name', 'description', 'permissionIdList'));

        return processCommonResponse($result);
    }

    public function edit(UpdateRoleRequest $request)
    {
        $result = $this->roleRepository->edit($request->only('id', 'name', 'display_name', 'description', 'permissionIdList'));

        return processCommonResponse($result);
    }

    public function delete(DeleteRoleRequest $request)
    {
        $result = $this->roleRepository->deleteById($request->input('id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->roleRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->roleRepository->getList(
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

    public function getRoleList()
    {
        $roleList = $this->roleRepository->getRoleList();
        return response()->json([
            'code' => CODE_SUCCESS,
            'data' => $roleList
        ]);
    }
}
