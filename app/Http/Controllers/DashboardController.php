<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function summary(Request $request)
    {
        $data = $this->dashboardService->summary($request);

        if (!$data) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($data);
    }
}
