<?php

namespace App\Http\Controllers\Common;


use App\Http\Controllers\Controller;
use App\Repositories\Common\LogsReponsitory;
use Illuminate\Http\Request;

class LogsController extends Controller
{


    protected $logsRepository;

    function __construct(LogsReponsitory $logsRepository)
    {
        $this->middleware('auth');

        $this->logsRepository = $logsRepository;
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);
        $searchParams = $request->only('action_name','timeFrom','timeTo','username','class_name');

        $total = $this->logsRepository->getList(
            $params['keyword'],
            $searchParams,
            true
        );

        $arr = array(
            'recordsTotal' => $total,

            'data' => $this->logsRepository->getList(
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
}