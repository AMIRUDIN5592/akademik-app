<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService): View
    {
        $distribusiNilai = $dashboardService->getDistribusiNilai();
        $mahasiswaPerJurusan = $dashboardService->getMahasiswaPerJurusan();

        return view('dashboard.index', [
            ...$dashboardService->getSummary(),
            'nilaiTerbaru' => $dashboardService->getLatestNilai(),
            'labelDistribusiNilai' => array_keys($distribusiNilai),
            'dataDistribusiNilai' => array_values($distribusiNilai),
            'labelJurusan' => $mahasiswaPerJurusan->pluck('nama_jurusan')->values()->all(),
            'dataJurusan' => $mahasiswaPerJurusan
                ->pluck('total')
                ->map(fn ($total) => (int) $total)
                ->values()
                ->all(),
        ]);
    }
}
