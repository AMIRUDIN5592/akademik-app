# Pertemuan 13 Dashboard and Reporting Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Melengkapi aplikasi akademik Laravel 13 dengan dashboard, Chart.js, laporan nilai berfilter, grade, pagination, dan role access sesuai materi praktik Pertemuan 13.

**Architecture:** Query dashboard berada di `DashboardService`, sedangkan query laporan berada di `LaporanNilaiService` dengan satu builder filter privat yang dipakai ulang untuk tabel dan summary. Controller hanya memvalidasi request dan memetakan data siap-render ke Blade; route middleware menjadi batas keamanan utama.

**Tech Stack:** PHP 8.3, Laravel 13, Eloquent, Blade, Bootstrap 5.3, Chart.js, PHPUnit 12, SQLite in-memory testing.

---

## File Structure

- Create `tests/Feature/Pertemuan13DashboardReportingTest.php`: regression test dashboard, laporan, filter, role, grade, dan pagination.
- Modify `app/Models/Nilai.php`: accessor grade A–E.
- Modify `app/Services/DashboardService.php`: KPI dan dataset chart.
- Modify `app/Http/Controllers/DashboardController.php`: pemetaan data dashboard.
- Create `app/Services/LaporanNilaiService.php`: query laporan dan summary.
- Create `app/Http/Controllers/LaporanNilaiController.php`: validasi filter dan data view.
- Modify `routes/web.php`: route laporan admin/dosen.
- Modify `app/Providers/AppServiceProvider.php`: pagination Bootstrap 5.
- Modify `resources/views/layouts/app.blade.php`: muat Chart.js sekali.
- Modify `resources/views/dashboard/index.blade.php`: KPI, chart, nilai terbaru, tombol laporan.
- Create `resources/views/laporan/nilai.blade.php`: filter, summary, tabel, grade, pagination.
- Modify `resources/views/partials/navbar.blade.php`: menu laporan sesuai role.

### Task 1: Regression Tests for Grade and Dashboard Data

**Files:**
- Create: `tests/Feature/Pertemuan13DashboardReportingTest.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Write the failing dashboard and grade tests**

Create the test class with imports, `RefreshDatabase`, helper factories, and these tests:

```php
<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pertemuan13DashboardReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_summary_charts_and_latest_scores(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();
        $secondStudent = $this->createStudent(Jurusan::firstOrFail(), '2301002', 'Budi Santoso');

        Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 90]);
        Nilai::create(['mahasiswa_id' => $secondStudent->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 70]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totalMahasiswa', 2)
            ->assertViewHas('totalMataKuliah', 1)
            ->assertViewHas('totalNilai', 2)
            ->assertViewHas('rataRataNilai', 80.0)
            ->assertViewHas('nilaiTertinggi', 90)
            ->assertViewHas('nilaiTerendah', 70)
            ->assertViewHas('labelDistribusiNilai', ['A', 'B', 'C', 'D', 'E'])
            ->assertViewHas('dataDistribusiNilai', [1, 0, 1, 0, 0])
            ->assertSee('chartDistribusiNilai')
            ->assertSee('chartMahasiswaJurusan')
            ->assertSee('Buka Laporan Nilai')
            ->assertSee('Budi Santoso');
    }

    public function test_empty_dashboard_uses_zero_for_score_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('rataRataNilai', 0.0)
            ->assertViewHas('nilaiTertinggi', 0)
            ->assertViewHas('nilaiTerendah', 0)
            ->assertViewHas('dataDistribusiNilai', [0, 0, 0, 0, 0])
            ->assertSee('Belum ada data.');
    }

    public function test_grade_accessor_uses_the_boundaries_from_the_material(): void
    {
        foreach ([100 => 'A', 85 => 'A', 84 => 'B', 75 => 'B', 74 => 'C', 65 => 'C', 64 => 'D', 50 => 'D', 49 => 'E', 0 => 'E'] as $score => $grade) {
            $nilai = new Nilai(['nilai' => $score]);

            $this->assertSame($grade, $nilai->grade);
        }
    }

    private function createAcademicData(): array
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'TI', 'nama_jurusan' => 'Teknik Informatika']);
        $mahasiswa = $this->createStudent($jurusan, '2301001', 'Andi Saputra');
        $mataKuliah = MataKuliah::create(['kode_mk' => 'MK013', 'nama_mk' => 'Dashboard dan Reporting', 'sks' => 3]);

        return [$mahasiswa, $mataKuliah];
    }

    private function createStudent(Jurusan $jurusan, string $nim, string $nama): Mahasiswa
    {
        return Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => $nim,
            'nama' => $nama,
            'email' => strtolower(str_replace(' ', '.', $nama)).'@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Tangerang',
        ]);
    }
}
```

- [ ] **Step 2: Run the focused tests and verify RED**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php`

Expected: FAIL because the grade accessor and dashboard chart variables do not exist.

- [ ] **Step 3: Commit the red tests**

Run:

```bash
git add tests/Feature/Pertemuan13DashboardReportingTest.php
git commit -m "test: define pertemuan 13 dashboard behavior"
```

### Task 2: Grade Accessor and Dashboard Service

**Files:**
- Modify: `app/Models/Nilai.php`
- Modify: `app/Services/DashboardService.php`
- Modify: `app/Http/Controllers/DashboardController.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Add the grade accessor**

Add to `Nilai`:

```php
public function getGradeAttribute(): string
{
    return match (true) {
        $this->nilai >= 85 => 'A',
        $this->nilai >= 75 => 'B',
        $this->nilai >= 65 => 'C',
        $this->nilai >= 50 => 'D',
        default => 'E',
    };
}
```

- [ ] **Step 2: Expand `DashboardService`**

Use these public methods and keys:

```php
public function getSummary(): array
{
    return [
        'totalMahasiswa' => Mahasiswa::count(),
        'totalMataKuliah' => MataKuliah::count(),
        'totalNilai' => Nilai::count(),
        'rataRataNilai' => round((float) (Nilai::avg('nilai') ?? 0), 2),
        'nilaiTertinggi' => Nilai::max('nilai') ?? 0,
        'nilaiTerendah' => Nilai::min('nilai') ?? 0,
    ];
}

public function getDistribusiNilai(): array
{
    return [
        'A' => Nilai::whereBetween('nilai', [85, 100])->count(),
        'B' => Nilai::whereBetween('nilai', [75, 84])->count(),
        'C' => Nilai::whereBetween('nilai', [65, 74])->count(),
        'D' => Nilai::whereBetween('nilai', [50, 64])->count(),
        'E' => Nilai::whereBetween('nilai', [0, 49])->count(),
    ];
}

public function getMahasiswaPerJurusan(): Collection
{
    return Mahasiswa::query()
        ->select('jurusans.nama_jurusan')
        ->selectRaw('COUNT(mahasiswas.id) as total')
        ->join('jurusans', 'jurusans.id', '=', 'mahasiswas.jurusan_id')
        ->groupBy('jurusans.nama_jurusan')
        ->orderBy('jurusans.nama_jurusan')
        ->get();
}
```

Retain `getLatestNilai()` with eager loading and remove the obsolete `getStatistikNilai()` method.

- [ ] **Step 3: Map chart datasets in `DashboardController`**

```php
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
        'dataJurusan' => $mahasiswaPerJurusan->pluck('total')->map(fn ($total) => (int) $total)->values()->all(),
    ]);
}
```

- [ ] **Step 4: Run focused tests**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php`

Expected: grade assertions PASS; dashboard tests still FAIL because the Blade view has not added charts and the report route does not exist.

- [ ] **Step 5: Commit dashboard domain changes**

```bash
git add app/Models/Nilai.php app/Services/DashboardService.php app/Http/Controllers/DashboardController.php
git commit -m "feat: provide dashboard reporting datasets"
```

### Task 3: Dashboard Blade and Chart.js

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/dashboard/index.blade.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Load Chart.js once in the layout**

Add before `@stack('scripts')`:

```blade
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

- [ ] **Step 2: Render dashboard sections**

Replace the dashboard content with:

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dashboard Akademik</h3>
            <p class="text-muted mb-0">Ringkasan data mahasiswa, mata kuliah, dan nilai.</p>
        </div>
        @if (in_array(auth()->user()->role, ['admin', 'dosen'], true))
            <a href="{{ route('laporan.nilai') }}" class="btn btn-primary">Buka Laporan Nilai</a>
        @endif
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            ['label' => 'Total Mahasiswa', 'value' => $totalMahasiswa],
            ['label' => 'Total Mata Kuliah', 'value' => $totalMataKuliah],
            ['label' => 'Total Nilai', 'value' => $totalNilai],
        ] as $item)
            <div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body">
                <small class="text-muted">{{ $item['label'] }}</small>
                <h2 class="fw-bold mb-0">{{ $item['value'] }}</h2>
            </div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Rata-rata Nilai', 'value' => $rataRataNilai],
            ['label' => 'Nilai Tertinggi', 'value' => $nilaiTertinggi],
            ['label' => 'Nilai Terendah', 'value' => $nilaiTerendah],
        ] as $item)
            <div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body">
                <small class="text-muted">{{ $item['label'] }}</small>
                <h3 class="fw-bold mb-0">{{ $item['value'] ?? 0 }}</h3>
            </div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-body">
            <h5 class="fw-bold mb-3">Distribusi Nilai</h5>
            <canvas id="chartDistribusiNilai" height="180"></canvas>
        </div></div></div>
        <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-body">
            <h5 class="fw-bold mb-3">Mahasiswa per Jurusan</h5>
            <canvas id="chartMahasiswaJurusan" height="180"></canvas>
        </div></div></div>
    </div>

    <div class="card shadow-sm"><div class="card-body">
        <h5 class="fw-bold mb-3">Nilai Terbaru</h5>
        <div class="table-responsive"><table class="table table-bordered table-striped align-middle mb-0">
            <thead class="table-light"><tr><th>No</th><th>Mahasiswa</th><th>Mata Kuliah</th><th>Nilai</th></tr></thead>
            <tbody>
            @forelse ($nilaiTerbaru as $row)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $row->mahasiswa->nama ?? '-' }}</td><td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td><td>{{ $row->nilai }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartDistribusiNilai'), {
    type: 'bar',
    data: { labels: @json($labelDistribusiNilai), datasets: [{ label: 'Jumlah Mahasiswa', data: @json($dataDistribusiNilai), backgroundColor: '#0d6efd' }] },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('chartMahasiswaJurusan'), {
    type: 'doughnut',
    data: { labels: @json($labelJurusan), datasets: [{ label: 'Jumlah Mahasiswa', data: @json($dataJurusan) }] },
    options: { responsive: true }
});
</script>
@endpush
```

- [ ] **Step 3: Run focused tests**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php --filter=dashboard`

Expected: 2 dashboard tests PASS.

- [ ] **Step 4: Commit dashboard UI**

```bash
git add resources/views/layouts/app.blade.php resources/views/dashboard/index.blade.php
git commit -m "feat: render dashboard charts and summaries"
```

### Task 4: Reporting Tests

**Files:**
- Modify: `tests/Feature/Pertemuan13DashboardReportingTest.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Add failing role, filtering, validation, and pagination tests**

Add these methods before the helpers:

```php
public function test_report_access_follows_role_rules(): void
{
    $this->get('/laporan/nilai')->assertRedirect(route('login'));

    foreach (['admin', 'dosen'] as $role) {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get('/laporan/nilai')
            ->assertOk();
    }

    $this->actingAs(User::factory()->create(['role' => 'mahasiswa']))
        ->get('/laporan/nilai')
        ->assertForbidden();
}

public function test_report_filters_rows_and_summary_consistently(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    [$mahasiswa, $mataKuliah] = $this->createAcademicData();
    $otherSubject = MataKuliah::create(['kode_mk' => 'MK014', 'nama_mk' => 'Pengujian Web', 'sks' => 2]);
    $otherStudent = $this->createStudent(Jurusan::firstOrFail(), '2301003', 'Citra Lestari');

    Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 90]);
    Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $otherSubject->id, 'nilai' => 80]);
    Nilai::create(['mahasiswa_id' => $otherStudent->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 60]);

    $query = ['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai_min' => 75, 'nilai_max' => 100];

    $this->actingAs($admin)
        ->get(route('laporan.nilai', $query))
        ->assertOk()
        ->assertViewHas('summary', ['totalData' => 1, 'rataRata' => 90.0, 'nilaiTertinggi' => 90, 'nilaiTerendah' => 90])
        ->assertViewHas('nilai', fn ($nilai) => $nilai->total() === 1
            && $nilai->first()->mahasiswa_id === $mahasiswa->id
            && $nilai->first()->mata_kuliah_id === $mataKuliah->id
            && $nilai->first()->nilai === 90)
        ->assertSee('Andi Saputra')
        ->assertSee('Dashboard dan Reporting')
        ->assertSee('>A<', false);
}

public function test_report_rejects_invalid_and_reversed_score_ranges(): void
{
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('laporan.nilai', ['nilai_min' => 90, 'nilai_max' => 75]))
        ->assertSessionHasErrors('nilai_max');

    $this->actingAs($admin)
        ->get(route('laporan.nilai', ['nilai_min' => -1, 'nilai_max' => 101]))
        ->assertSessionHasErrors(['nilai_min', 'nilai_max']);
}

public function test_empty_report_uses_zero_summary_and_empty_state(): void
{
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('laporan.nilai'))
        ->assertOk()
        ->assertViewHas('summary', ['totalData' => 0, 'rataRata' => 0.0, 'nilaiTertinggi' => 0, 'nilaiTerendah' => 0])
        ->assertSee('Data tidak ditemukan.');
}

public function test_report_pagination_keeps_active_filters(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    [$mahasiswa] = $this->createAcademicData();

    for ($index = 1; $index <= 11; $index++) {
        $subject = MataKuliah::create(['kode_mk' => 'PG'.str_pad((string) $index, 3, '0', STR_PAD_LEFT), 'nama_mk' => 'Mata Kuliah '.$index, 'sks' => 2]);
        Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $subject->id, 'nilai' => 80]);
    }

    $this->actingAs($admin)
        ->get(route('laporan.nilai', ['mahasiswa_id' => $mahasiswa->id, 'nilai_min' => 75]))
        ->assertOk()
        ->assertSee('mahasiswa_id='.$mahasiswa->id, false)
        ->assertSee('nilai_min=75', false)
        ->assertSee('page=2', false);
}
```

- [ ] **Step 2: Run reporting tests and verify RED**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php --filter=report`

Expected: FAIL because `laporan.nilai` and its service/view do not exist.

- [ ] **Step 3: Commit the reporting tests**

```bash
git add tests/Feature/Pertemuan13DashboardReportingTest.php
git commit -m "test: define filtered score report behavior"
```

### Task 5: Reporting Service, Controller, and Route

**Files:**
- Create: `app/Services/LaporanNilaiService.php`
- Create: `app/Http/Controllers/LaporanNilaiController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Implement `LaporanNilaiService`**

```php
<?php

namespace App\Services;

use App\Models\Nilai;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LaporanNilaiService
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->with(['mahasiswa.jurusan', 'mataKuliah'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function getSummary(array $filters): array
    {
        $query = $this->filteredQuery($filters);

        return [
            'totalData' => (clone $query)->count(),
            'rataRata' => round((float) ((clone $query)->avg('nilai') ?? 0), 2),
            'nilaiTertinggi' => (clone $query)->max('nilai') ?? 0,
            'nilaiTerendah' => (clone $query)->min('nilai') ?? 0,
        ];
    }

    private function filteredQuery(array $filters): Builder
    {
        return Nilai::query()
            ->when(filled($filters['mahasiswa_id'] ?? null), fn (Builder $query) => $query->where('mahasiswa_id', $filters['mahasiswa_id']))
            ->when(filled($filters['mata_kuliah_id'] ?? null), fn (Builder $query) => $query->where('mata_kuliah_id', $filters['mata_kuliah_id']))
            ->when(filled($filters['nilai_min'] ?? null), fn (Builder $query) => $query->where('nilai', '>=', $filters['nilai_min']))
            ->when(filled($filters['nilai_max'] ?? null), fn (Builder $query) => $query->where('nilai', '<=', $filters['nilai_max']));
    }
}
```

- [ ] **Step 2: Implement `LaporanNilaiController`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Services\LaporanNilaiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanNilaiController extends Controller
{
    public function index(Request $request, LaporanNilaiService $service): View
    {
        $filters = $request->validate([
            'mahasiswa_id' => ['nullable', 'exists:mahasiswas,id'],
            'mata_kuliah_id' => ['nullable', 'exists:mata_kuliahs,id'],
            'nilai_min' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_max' => ['nullable', 'numeric', 'min:0', 'max:100', 'gte:nilai_min'],
        ]);

        return view('laporan.nilai', [
            'nilai' => $service->getFiltered($filters),
            'summary' => $service->getSummary($filters),
            'mahasiswas' => Mahasiswa::orderBy('nama')->get(),
            'mataKuliahs' => MataKuliah::orderBy('nama_mk')->get(),
            'filters' => $filters,
        ]);
    }
}
```

- [ ] **Step 3: Register the protected report route**

Import `LaporanNilaiController`, then add inside the `auth`, `role:admin,dosen` group:

```php
Route::get('/laporan/nilai', [LaporanNilaiController::class, 'index'])->name('laporan.nilai');
```

- [ ] **Step 4: Run role tests**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php --filter=report_access`

Expected: route role checks reach the controller; admin/dosen may still fail until the report view exists, mahasiswa and guest assertions PASS.

- [ ] **Step 5: Commit reporting backend**

```bash
git add app/Services/LaporanNilaiService.php app/Http/Controllers/LaporanNilaiController.php routes/web.php
git commit -m "feat: add filtered score report backend"
```

### Task 6: Reporting Blade, Navbar, and Bootstrap Pagination

**Files:**
- Create: `resources/views/laporan/nilai.blade.php`
- Modify: `resources/views/partials/navbar.blade.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Pertemuan13DashboardReportingTest.php`

- [ ] **Step 1: Enable Bootstrap pagination**

Import `Illuminate\Pagination\Paginator` and configure it:

```php
public function boot(): void
{
    Paginator::useBootstrapFive();
}
```

- [ ] **Step 2: Add the role-aware navbar link**

Inside the admin/dosen condition, after the Nilai link, add:

```blade
<a class="nav-link {{ request()->routeIs('laporan.nilai') ? 'active' : '' }}" href="{{ route('laporan.nilai') }}">
    <i class="fas fa-chart-column"></i> Laporan Nilai
</a>
```

- [ ] **Step 3: Create the report view**

Create a Blade page with the approved structure:

```blade
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div><h3 class="fw-bold mb-1">Laporan Nilai</h3><p class="text-muted mb-0">Filter dan rekap data nilai mahasiswa.</p></div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="card shadow-sm mb-4"><div class="card-body">
        <form method="GET" action="{{ route('laporan.nilai') }}">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="mahasiswa_id">Mahasiswa</label><select id="mahasiswa_id" name="mahasiswa_id" class="form-select"><option value="">Semua Mahasiswa</option>@foreach ($mahasiswas as $mahasiswa)<option value="{{ $mahasiswa->id }}" @selected(($filters['mahasiswa_id'] ?? '') == $mahasiswa->id)>{{ $mahasiswa->nama }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label" for="mata_kuliah_id">Mata Kuliah</label><select id="mata_kuliah_id" name="mata_kuliah_id" class="form-select"><option value="">Semua Mata Kuliah</option>@foreach ($mataKuliahs as $mataKuliah)<option value="{{ $mataKuliah->id }}" @selected(($filters['mata_kuliah_id'] ?? '') == $mataKuliah->id)>{{ $mataKuliah->nama_mk }}</option>@endforeach</select></div>
                <div class="col-md-2"><label class="form-label" for="nilai_min">Nilai Min</label><input id="nilai_min" type="number" name="nilai_min" class="form-control @error('nilai_min') is-invalid @enderror" value="{{ $filters['nilai_min'] ?? '' }}" min="0" max="100">@error('nilai_min')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-2"><label class="form-label" for="nilai_max">Nilai Max</label><input id="nilai_max" type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror" value="{{ $filters['nilai_max'] ?? '' }}" min="0" max="100">@error('nilai_max')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Filter</button></div>
            </div>
            <a href="{{ route('laporan.nilai') }}" class="btn btn-outline-danger btn-sm mt-3">Reset Filter</a>
        </form>
    </div></div>

    <div class="row g-3 mb-4">
        @foreach ([['Total Data', $summary['totalData']], ['Rata-rata', $summary['rataRata']], ['Nilai Tertinggi', $summary['nilaiTertinggi']], ['Nilai Terendah', $summary['nilaiTerendah']]] as [$label, $value])
            <div class="col-sm-6 col-lg-3"><div class="card shadow-sm h-100"><div class="card-body"><small class="text-muted">{{ $label }}</small><h4 class="fw-bold mb-0">{{ $value ?? 0 }}</h4></div></div></div>
        @endforeach
    </div>

    <div class="card shadow-sm"><div class="card-body"><div class="table-responsive">
        <table class="table table-bordered table-striped align-middle"><thead class="table-light"><tr><th>No</th><th>NIM</th><th>Mahasiswa</th><th>Jurusan</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Grade</th></tr></thead><tbody>
        @forelse ($nilai as $row)
            @php
                $badgeClass = match ($row->grade) { 'A' => 'bg-success', 'B' => 'bg-primary', 'C' => 'bg-warning text-dark', 'D' => 'bg-secondary', default => 'bg-danger' };
            @endphp
            <tr><td>{{ $nilai->firstItem() + $loop->index }}</td><td>{{ $row->mahasiswa->nim ?? '-' }}</td><td>{{ $row->mahasiswa->nama ?? '-' }}</td><td>{{ $row->mahasiswa->jurusan->nama_jurusan ?? '-' }}</td><td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td><td>{{ $row->mataKuliah->sks ?? '-' }}</td><td>{{ $row->nilai }}</td><td><span class="badge {{ $badgeClass }}">{{ $row->grade }}</span></td></tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>
        @endforelse
        </tbody></table>
    </div>{{ $nilai->links() }}</div></div>
</div>
@endsection
```

- [ ] **Step 4: Run all Pertemuan 13 tests**

Run: `php artisan test tests/Feature/Pertemuan13DashboardReportingTest.php`

Expected: PASS with no failures.

- [ ] **Step 5: Commit report UI**

```bash
git add app/Providers/AppServiceProvider.php resources/views/laporan/nilai.blade.php resources/views/partials/navbar.blade.php
git commit -m "feat: render filtered score report"
```

### Task 7: Full Verification and Cleanup

**Files:**
- Modify only files requiring formatter fixes.
- Test: all tests under `tests/`.

- [ ] **Step 1: Format changed PHP files**

Run: `./vendor/bin/pint app/Models/Nilai.php app/Services/DashboardService.php app/Services/LaporanNilaiService.php app/Http/Controllers/DashboardController.php app/Http/Controllers/LaporanNilaiController.php app/Providers/AppServiceProvider.php routes/web.php tests/Feature/Pertemuan13DashboardReportingTest.php`

Expected: formatter exits 0.

- [ ] **Step 2: Run the full test suite**

Run: `php artisan test`

Expected: all existing and Pertemuan 13 tests PASS.

- [ ] **Step 3: Verify routing and Blade compilation**

Run:

```bash
php artisan route:list --path=laporan
php artisan view:clear
php artisan view:cache
```

Expected: `laporan.nilai` lists middleware `web, auth, role:admin,dosen`; view cache completes without errors.

- [ ] **Step 4: Verify production asset build**

Run: `npm run build`

Expected: Vite exits 0 and emits production assets.

- [ ] **Step 5: Check patch hygiene**

Run:

```bash
git diff --check
git status --short
```

Expected: no whitespace errors; only intentional implementation changes or known pre-existing untracked files remain.

- [ ] **Step 6: Commit formatter or verification adjustments if any**

```bash
git add app routes resources tests
git commit -m "test: verify pertemuan 13 reporting flow"
```

Skip this commit only when Step 1–5 produce no additional tracked changes.
