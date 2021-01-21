<?php

namespace App\Http\Controllers;
use App\Repositories\DomainRepository;
use App\Repositories\LicenseConfigRepository;
use Illuminate\Http\Request;

class LicenseConfigController extends Controller
{
    /**
     * @var DomainRepository
     */
    protected $licenseConfigRepository;

    function __construct(LicenseConfigRepository $licenseConfigRepository)
    {
        $this->middleware('auth');

        $this->licenseConfigRepository = $licenseConfigRepository;
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $searchParams = $request->input('id');

        $total = $this->licenseConfigRepository->getList(
            $params['keyword'],
            $searchParams,
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->licenseConfigRepository->getList(
                $params['keyword'],
                $searchParams,
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

    public function add(Request $request)
    {
        $result = $this->licenseConfigRepository->add($request->only('assigned_licenses','account_id'));

        return processCommonResponse($result);
    }
}
