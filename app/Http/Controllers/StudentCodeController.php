<?php

namespace App\Http\Controllers;


use App\Http\Requests\StudentCode\DeleteStudentCodeRequest;
use App\Http\Requests\StudentCode\StoreStudentCodeRequest;
use App\Http\Requests\StudentCode\UpdateStudentCodeRequest;
use App\Repositories\StudentCodeRepository;
use Illuminate\Http\Request;

class StudentCodeController extends Controller
{
    /**
     * @var StudentCodeRepository
     */
    protected $studentCodeRepository;

    function __construct(StudentCodeRepository $studentCodeRepository)
    {
        $this->middleware('auth')->except('check');

        $this->studentCodeRepository = $studentCodeRepository;
    }

    public function store(StoreStudentCodeRequest $request)
    {
        $result = $this->studentCodeRepository->store($request->only('domain_id', 'reseller_id', 'max_user', 'expired_date'));

        return processCommonResponse($result);
    }

    public function edit(UpdateStudentCodeRequest $request)
    {
        $result = $this->studentCodeRepository->edit($request->only('id','max_user', 'expired_date'));

        return processCommonResponse($result);
    }

    public function check(Request $request)
    {
        $result = $this->studentCodeRepository->check($request->only('code'));

        return processCommonResponse($result, $result);
    }

    public function delete(DeleteStudentCodeRequest $request)
    {
        $result = $this->studentCodeRepository->deleteStudentCode($request->input('id'));

        return processCommonResponse($result);
    }

    public function listing(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->studentCodeRepository->getList(
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->studentCodeRepository->getList(
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

        $data = $this->studentCodeRepository->getListAll($search, false, $length, $page * $length);
        $total = $this->studentCodeRepository->getListAll($search, true);

        return response()->json([
            'results' => $data,
            'total' => $total
        ]);
    }

    public function listingUserCreated(Request $request)
    {
        $params = getDataTableRequestParams($request);

        $total = $this->studentCodeRepository->getListUserCreated(
            $request->input('code'),
            $params['keyword'],
            true
        );

        $arr = array(
            'recordsTotal' => $total,
            'data' => $this->studentCodeRepository->getListUserCreated(
                $request->input('code'),
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
}
