import fs from "node:fs/promises";
import { FileBlob, PresentationFile } from "@oai/artifact-tool";

const source = process.argv[2];
const output = process.argv[3];
const presentation = await PresentationFile.importPptx(await FileBlob.load(source));

const codeBySlide = new Map([
  [4, `mahasiswas
- id
- jurusan_id
- nim
- nama
- email

mata_kuliahs
- id
- kode_mk
- nama_mk
- sks

nilais
- id
- mahasiswa_id
- mata_kuliah_id
- nilai`],
  [8, `use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa')->after('password');
            $table->foreignId('mahasiswa_id')->nullable()
                ->after('role')->constrained('mahasiswas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mahasiswa_id');
            $table->dropColumn('role');
        });
    }
};`],
  [9, `namespace App\\Models;

use Illuminate\\Foundation\\Auth\\User as Authenticatable;
use Illuminate\\Notifications\\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'mahasiswa_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}`],
  [10, `namespace Database\\Seeders;

use App\\Models\\Mahasiswa;
use App\\Models\\User;
use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kampus.test'],
            ['name' => 'Admin Akademik', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'dosen@kampus.test'],
            ['name' => 'Dosen Pengampu', 'password' => Hash::make('password'), 'role' => 'dosen']
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@kampus.test'],
            ['name' => 'Mahasiswa Demo', 'password' => Hash::make('password'),
             'role' => 'mahasiswa', 'mahasiswa_id' => Mahasiswa::first()?->id]
        );
    }
}`],
  [12, `use App\\Http\\Controllers\\AuthController;
use App\\Http\\Controllers\\DashboardController;
use Illuminate\\Support\\Facades\\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});`],
  [13, `namespace App\\Http\\Controllers;

use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    // Method login dan logout dijelaskan pada slide berikutnya.
}`],
  [17, `Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])
        ->name('mahasiswa.index');
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])
        ->name('mata-kuliah.index');
});

Route::middleware(['auth', 'role:admin,dosen'])->group(function () {
    Route::post('/nilai', [NilaiController::class, 'store'])
        ->name('nilai.store');
});`],
  [18, `use App\\Http\\Middleware\\CheckRole;
use Illuminate\\Foundation\\Application;
use Illuminate\\Foundation\\Configuration\\Exceptions;
use Illuminate\\Foundation\\Configuration\\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();`],
  [19, `namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Symfony\\Component\\HttpFoundation\\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}`],
  [20, `Route::middleware(['auth', 'role:admin'])->group(function () {
    // Route mahasiswa dan mata kuliah hanya untuk admin.
});

Route::middleware(['auth', 'role:admin,dosen'])->group(function () {
    // Route create, store, edit, update, dan destroy nilai.
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/nilai-saya', [NilaiSayaController::class, 'index'])
        ->name('nilai-saya.index');
});`],
  [21, `namespace App\\Http\\Controllers;

use App\\Models\\Nilai;
use Illuminate\\Http\\Request;

class NilaiSayaController extends Controller
{
    public function index(Request $request)
    {
        $nilai = Nilai::with('mataKuliah')
            ->where('mahasiswa_id', $request->user()->mahasiswa_id)
            ->latest()
            ->get();

        return view('nilai-saya.index', compact('nilai'));
    }
}`],
  [22, `@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Nilai Saya</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($nilai as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->mataKuliah->kode_mk ?? '-' }}</td>
                    <td>{{ $row->mataKuliah->nama_mk ?? '-' }}</td>
                    <td>{{ $row->mataKuliah->sks ?? '-' }}</td>
                    <td>{{ $row->nilai }}</td>
                </tr>`],
  [23, `            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada data nilai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection`],
  [24, `@auth
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Akademik</a>
        <div class="navbar-nav me-auto">
            @if (auth()->user()->role === 'admin')
                <a class="nav-link" href="{{ route('mahasiswa.index') }}">Mahasiswa</a>
                <a class="nav-link" href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
            @endif

            @if (in_array(auth()->user()->role, ['admin', 'dosen'], true))
                <a class="nav-link" href="{{ route('nilai.index') }}">Nilai</a>
            @endif

            @if (auth()->user()->role === 'mahasiswa')
                <a class="nav-link" href="{{ route('nilai-saya.index') }}">Nilai Saya</a>
            @endif`],
  [26, `namespace App\\Http\\Controllers;

use App\\Models\\Mahasiswa;
use App\\Models\\MataKuliah;
use App\\Models\\Nilai;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'totalMahasiswa' => Mahasiswa::count(),
            'totalMataKuliah' => MataKuliah::count(),
            'totalNilai' => Nilai::count(),
        ]);
    }
}`],
  [27, `@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h3>Dashboard Akademik</h3>
    <p class="text-muted">Login sebagai: {{ auth()->user()->role }}</p>

    <div class="row g-3">
        <div class="col-md-4"><div class="card"><div class="card-body">
            <h6>Total Mahasiswa</h6><h2>{{ $totalMahasiswa }}</h2>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <h6>Total Mata Kuliah</h6><h2>{{ $totalMataKuliah }}</h2>
        </div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">
            <h6>Total Nilai</h6><h2>{{ $totalNilai }}</h2>
        </div></div></div>
    </div>
</div>
@endsection`],
]);

function removeLineNumbers(text) {
  const lines = text.split("\n");
  const numbered = lines.filter((line) => /^\s*\d+\s/.test(line)).length;
  if (numbered < 2) return text;
  return lines.map((line) => line.replace(/^\s*\d+\s?/, "")).join("\n");
}

function alignProjectTerms(text) {
  return text
    .replaceAll("Matakuliah", "MataKuliah")
    .replaceAll("matakuliah", "mata-kuliah")
    .replaceAll("nilai_angka", "nilai")
    .replaceAll("\n- nilai_huruf", "")
    .replaceAll("\n- semester", "")
    .replaceAll("Auth::user()->mahasiswa_id", "request()->user()->mahasiswa_id");
}

for (let slideIndex = 0; slideIndex < presentation.slides.items.length; slideIndex += 1) {
  const slideNumber = slideIndex + 1;
  const slide = presentation.slides.items[slideIndex];
  let replacedMainCode = false;

  for (const shape of slide.shapes.items) {
    const original = shape.text?.toString?.() ?? "";
    if (!original) continue;

    if (codeBySlide.has(slideNumber) && !replacedMainCode && original.includes("\n") && /^\s*\d+\s/m.test(original)) {
      shape.text.set(codeBySlide.get(slideNumber));
      replacedMainCode = true;
      continue;
    }

    const cleaned = alignProjectTerms(removeLineNumbers(original));
    if (cleaned !== original) shape.text.set(cleaned);
  }
}

const pptx = await PresentationFile.exportPptx(presentation);
await fs.mkdir(new URL(".", `file://${output}`).pathname, { recursive: true });
await pptx.save(output);
