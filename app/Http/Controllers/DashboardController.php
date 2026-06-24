<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService): View
    {
        return view('dashboard.index', [
            ...$dashboardService->getSummary(),
            ...$dashboardService->getStatistikNilai(),
            'nilaiTerbaru' => $dashboardService->getLatestNilai(),
        ]);
    }
}
