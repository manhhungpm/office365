<?php

namespace App\Http\Controllers;


use App\Http\Requests\Domain\DeleteDomainRequest;
use App\Http\Requests\Domain\StoreDomainRequest;
use App\Http\Requests\Domain\UpdateDomainRequest;
use App\Repositories\DomainRepository;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * @var DomainRepository
     */
    protected $domainRepository;

    function __construct(DomainRepository $domainRepository)
    {
        $this->middleware('auth');

        $this->domainRepository = $domainRepository;
    }

    public function store(StoreDomainRequest $request)
    {
        $result = $this->domainRepository->store($request->only('id', 'account_id'));

        return processCommonResponse($result);
    }

    public function edit(UpdateDomainRequest $request)
    {
        $result = $this->domainRepository->edit($request->only('id', 'account_id', 'domain_id'));

        return processCommonResponse($result);
    }

    public function delete(DeleteDomainRequest $request)
    {
        $result = $this->domainRepository->deleteById($request->input('domain_id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->domainRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->domainRepository->getList(
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

        $data = $this->domainRepository->getListAll($search, $request->input('reseller'), false, $length, $page * $length);
        $total = $this->domainRepository->getListAll($search, $request->input('reseller'), true);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }
}