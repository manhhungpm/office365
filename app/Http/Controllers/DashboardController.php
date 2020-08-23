<?php

namespace App\Http\Controllers;


use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @var DashboardRepository
     */
    protected $repo;

    function __construct(DashboardRepository $dashboardRepository)
    {
        $this->middleware('auth');

        $this->repo = $dashboardRepository;
    }

    public function resellerStats(Request $request) {
        $result = $this->repo->getResellerStats();
        return processCommonResponse($result, $result);
    }

    public function adminStats(Request $request) {
        $result = $this->repo->getAdminStats();
        return processCommonResponse($result, $result);
    }
}