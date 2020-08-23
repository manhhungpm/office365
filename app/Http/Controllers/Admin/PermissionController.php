<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\DeletePermissionRequest;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Repositories\Admin\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    function __construct(PermissionRepository $permissionRepository)
    {
        $this->middleware('auth');

        $this->permissionRepository = $permissionRepository;
    }

    public function store(StorePermissionRequest $request)
    {
        $result = $this->permissionRepository->store($request->only('name', 'display_name', 'description'));

        return processCommonResponse($result);
    }

    public function edit(UpdatePermissionRequest $request)
    {
        $result = $this->permissionRepository->edit($request->only('id', 'name', 'display_name', 'description'));

        return processCommonResponse($result);
    }

    public function delete(DeletePermissionRequest $request)
    {
        $result = $this->permissionRepository->deleteById($request->input('id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->permissionRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->permissionRepository->getList(
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

    /**
     * Get permission list
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getPermissionList()
    {
        $permissionList = $this->permissionRepository->getPermissionList();
        return response()->json([
            'code' => CODE_SUCCESS,
            'data' => $permissionList
        ]);
    }
}
