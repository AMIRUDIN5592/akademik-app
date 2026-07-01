import fs from "node:fs/promises";
import path from "node:path";
import { spawn } from "node:child_process";

import {
  createSlideContext,
  ensureArtifactToolWorkspace,
  importArtifactTool,
  padSlideNumber,
  saveBlobToFile,
} from "/Users/amirudin92/.codex/plugins/cache/openai-primary-runtime/presentations/26.521.10419/skills/presentations/scripts/artifact_tool_utils.mjs";

const root = process.cwd();
const workspace = path.join(root, "outputs", process.env.CODEX_THREAD_ID || "manual-split-pertemuan", "presentations", "split-pertemuan-9-10");
const previewRoot = path.join(workspace, "preview");
const slideSize = { width: 1280, height: 720 };

const theme = {
  navy: "#0F2742",
  blue: "#2563EB",
  teal: "#0F766E",
  green: "#16A34A",
  amber: "#D97706",
  red: "#DC2626",
  ink: "#111827",
  muted: "#64748B",
  line: "#CBD5E1",
  soft: "#F1F5F9",
  white: "#FFFFFF",
};

const commonFooter = "STMIK ANTAR BANGSA - Pemrograman Web Lanjut - Dosen: Amirudin";

const deck9 = {
  title: "Pertemuan 9",
  subtitle: "Form Mata Kuliah dan Generate Kartu Mahasiswa",
  filename: "Pertemuan_9.pptx",
  accent: theme.blue,
  slides: [
    {
      type: "title",
      title: "Pertemuan 9",
      subtitle: "Form Mata Kuliah dan Generate Kartu Mahasiswa",
      kicker: "Laravel 13 - Eloquent Relationship - CRUD Master Data",
      chips: ["Mahasiswa", "KartuMahasiswa", "MataKuliah", "Flash Message"],
    },
    {
      type: "agenda",
      title: "Agenda Praktik Pertemuan 9",
      items: [
        "Review relasi Mahasiswa, Jurusan, KartuMahasiswa, dan MataKuliah",
        "Membaca posisi fitur dari routes/web.php",
        "Membuat generate kartu dari detail mahasiswa",
        "Membuat CRUD master Mata Kuliah",
        "Menambahkan menu Mata Kuliah di navbar",
        "Menguji validasi, redirect, dan flash message",
      ],
    },
    {
      type: "bullets",
      title: "Tujuan Pembelajaran",
      lead: "Setelah praktik, mahasiswa mampu menghubungkan form, controller, model, dan view pada fitur data akademik.",
      items: [
        "Menjelaskan kapan menggunakan belongsTo, hasOne, hasMany, dan belongsToMany.",
        "Membuat nomor kartu mahasiswa otomatis dari NIM.",
        "Membuat form tambah dan edit master Mata Kuliah.",
        "Menampilkan jumlah mahasiswa peserta dengan withCount('mahasiswa').",
        "Memahami pola redirect()->route()->with() untuk pesan proses.",
      ],
    },
    {
      type: "flow",
      title: "Posisi Materi Setelah UTS",
      lead: "Pertemuan 9 memperkuat fitur master data dan relasi satu-ke-satu sebelum masuk ke nilai mahasiswa.",
      nodes: ["Mahasiswa", "KartuMahasiswa", "MataKuliah", "Siap Input Nilai"],
      notes: [
        "Mahasiswa tetap menjadi pusat data.",
        "Kartu dibuat otomatis dari detail mahasiswa.",
        "Mata kuliah disiapkan sebagai master data.",
        "Pertemuan 10 memakai data ini untuk tabel nilais.",
      ],
    },
    {
      type: "code",
      title: "Route Mahasiswa dan Generate Kartu",
      path: "routes/web.php",
      note: "Route generate kartu memakai POST karena membuat data baru pada tabel kartu_mahasiswas.",
      code: `Route::get('/mahasiswa/detail/{id}', [MahasiswaController::class, 'detail'])
    ->name('mahasiswa.detail');

Route::post('/mahasiswa/{id}/generate-kartu', [MahasiswaController::class, 'generateKartu'])
    ->name('mahasiswa.generate-kartu');`,
    },
    {
      type: "code",
      title: "Model Mahasiswa: Relasi Kartu",
      path: "app/Models/Mahasiswa.php",
      note: "Satu mahasiswa hanya memiliki satu kartu, sehingga relasinya hasOne.",
      code: `class Mahasiswa extends Model
{
    protected $fillable = [
        'jurusan_id', 'nim', 'nama',
        'email', 'jenis_kelamin', 'alamat',
    ];

    public function kartuMahasiswa()
    {
        return $this->hasOne(KartuMahasiswa::class);
    }
}`,
    },
    {
      type: "code",
      title: "Model KartuMahasiswa",
      path: "app/Models/KartuMahasiswa.php",
      note: "Nomor kartu, tanggal terbit, dan tanggal berlaku disimpan terpisah dari biodata mahasiswa.",
      code: `class KartuMahasiswa extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'nomor_kartu',
        'tanggal_terbit',
        'tanggal_berlaku',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}`,
    },
    {
      type: "code",
      title: "Migration Kartu Mahasiswa",
      path: "database/migrations/2026_05_12_143924_create_kartu_mahasiswas_table.php",
      note: "Foreign key diberi cascadeOnDelete agar kartu ikut terhapus saat mahasiswa dihapus.",
      code: `Schema::create('kartu_mahasiswas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mahasiswa_id')
        ->constrained('mahasiswas')
        ->cascadeOnDelete();
    $table->string('nomor_kartu')->unique();
    $table->date('tanggal_terbit');
    $table->date('tanggal_berlaku');
    $table->timestamps();
});`,
    },
    {
      type: "code",
      title: "Controller: Generate Kartu",
      path: "app/Http/Controllers/MahasiswaController.php",
      note: "Kode project mencegah kartu ganda dengan mengecek $mahasiswa->kartuMahasiswa terlebih dahulu.",
      code: `public function generateKartu($id)
{
    $mahasiswa = Mahasiswa::with('kartuMahasiswa')->findOrFail($id);

    if (!$mahasiswa->kartuMahasiswa) {
        KartuMahasiswa::create([
            'mahasiswa_id' => $mahasiswa->id,
            'nomor_kartu' => 'KTM-' . $mahasiswa->nim,
            'tanggal_terbit' => now(),
            'tanggal_berlaku' => now()->addYears(4),
        ]);
    }

    return redirect()
        ->route('mahasiswa.detail', $mahasiswa->id)
        ->with('success', 'Kartu mahasiswa berhasil digenerate.');
}`,
    },
    {
      type: "code",
      title: "View Detail: Tombol Generate Kartu",
      path: "resources/views/mahasiswa/detail.blade.php",
      note: "Tombol hanya tampil jika mahasiswa belum memiliki kartu.",
      code: `<p><strong>No Kartu:</strong> {{ $mahasiswa->kartuMahasiswa->nomor_kartu ?? '-' }}</p>

@if (!$mahasiswa->kartuMahasiswa)
    <form action="{{ route('mahasiswa.generate-kartu', $mahasiswa->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            Generate Kartu
        </button>
    </form>
@endif`,
    },
    {
      type: "flow",
      title: "Alur Generate Kartu",
      lead: "Alur ini menjadi tambahan penting dari project yang belum masuk pada slide awal.",
      nodes: ["Buka Detail Mahasiswa", "Klik Generate Kartu", "Cek Kartu Lama", "Create KTM-NIM", "Redirect Detail"],
      notes: [
        "Data diambil dengan findOrFail.",
        "Jika kartu sudah ada, tidak membuat ulang.",
        "Nomor kartu memakai pola KTM-{NIM}.",
        "Flash message memberi umpan balik.",
      ],
    },
    {
      type: "code",
      title: "Route Mata Kuliah",
      path: "routes/web.php",
      note: "Route ditulis eksplisit agar mahasiswa memahami mapping URL ke method controller.",
      code: `Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])
    ->name('mata-kuliah.index');
Route::get('/mata-kuliah/create', [MataKuliahController::class, 'create'])
    ->name('mata-kuliah.create');
Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])
    ->name('mata-kuliah.store');
Route::get('/mata-kuliah/{id}', [MataKuliahController::class, 'show'])
    ->name('mata-kuliah.show');
Route::get('/mata-kuliah/{id}/edit', [MataKuliahController::class, 'edit'])
    ->name('mata-kuliah.edit');
Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update'])
    ->name('mata-kuliah.update');`,
    },
    {
      type: "code",
      title: "Model MataKuliah",
      path: "app/Models/MataKuliah.php",
      note: "Relasi mahasiswa lewat tabel nilais akan dipakai lebih dalam pada Pertemuan 10.",
      code: `class MataKuliah extends Model
{
    protected $fillable = ['kode_mk', 'nama_mk', 'sks'];

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'nilais')
            ->withPivot('id', 'nilai')
            ->withTimestamps();
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}`,
    },
    {
      type: "code",
      title: "Controller Mata Kuliah: Index dan Create",
      path: "app/Http/Controllers/MataKuliahController.php",
      note: "withCount('mahasiswa') membuat kolom jumlah mahasiswa tanpa query manual di view.",
      code: `public function index()
{
    $mataKuliahs = MataKuliah::withCount('mahasiswa')
        ->orderBy('nama_mk')
        ->get();

    return view('mata-kuliah.index', compact('mataKuliahs'));
}

public function create()
{
    return view('mata-kuliah.create');
}`,
    },
    {
      type: "code",
      title: "Controller Mata Kuliah: Store",
      path: "app/Http/Controllers/MataKuliahController.php",
      note: "Validasi kode mata kuliah menjaga data master tetap unik.",
      code: `public function store(Request $request)
{
    $validated = $request->validate([
        'kode_mk' => ['required', 'max:20', 'unique:mata_kuliahs,kode_mk'],
        'nama_mk' => ['required', 'max:100'],
        'sks' => ['required', 'integer', 'min:1', 'max:6'],
    ]);

    MataKuliah::create($validated);

    return redirect()
        ->route('mata-kuliah.index')
        ->with('success', 'Data mata kuliah berhasil ditambahkan.');
}`,
    },
    {
      type: "code",
      title: "Controller Mata Kuliah: Edit dan Update",
      path: "app/Http/Controllers/MataKuliahController.php",
      note: "Rule unique mengabaikan id data saat ini supaya kode yang tidak berubah tetap valid.",
      code: `public function update(Request $request, $id)
{
    $mataKuliah = MataKuliah::findOrFail($id);

    $validated = $request->validate([
        'kode_mk' => ['required', 'max:20', 'unique:mata_kuliahs,kode_mk,' . $mataKuliah->id],
        'nama_mk' => ['required', 'max:100'],
        'sks' => ['required', 'integer', 'min:1', 'max:6'],
    ]);

    $mataKuliah->update($validated);

    return redirect()
        ->route('mata-kuliah.index')
        ->with('success', 'Data mata kuliah berhasil diperbarui.');
}`,
    },
    {
      type: "code",
      title: "View Index Mata Kuliah",
      path: "resources/views/mata-kuliah/index.blade.php",
      note: "Data jumlah mahasiswa berasal dari properti mahasiswa_count.",
      code: `@forelse ($mataKuliahs as $mk)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $mk->kode_mk }}</td>
        <td>{{ $mk->nama_mk }}</td>
        <td>{{ $mk->sks }}</td>
        <td>{{ $mk->mahasiswa_count }}</td>
        <td>
            <a href="{{ route('mata-kuliah.show', $mk->id) }}" class="btn btn-sm btn-outline-info">Detail</a>
            <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center text-muted">Data belum tersedia.</td></tr>
@endforelse`,
    },
    {
      type: "code",
      title: "View Form Mata Kuliah",
      path: "resources/views/mata-kuliah/create.blade.php",
      note: "create.blade.php dan edit.blade.php memakai field yang sama: kode_mk, nama_mk, dan sks.",
      code: `<form action="{{ route('mata-kuliah.store') }}" method="POST">
    @csrf
    <input type="text" name="kode_mk" value="{{ old('kode_mk') }}"
        class="form-control @error('kode_mk') is-invalid @enderror">
    <input type="text" name="nama_mk" value="{{ old('nama_mk') }}"
        class="form-control @error('nama_mk') is-invalid @enderror">
    <input type="number" name="sks" value="{{ old('sks') }}"
        class="form-control @error('sks') is-invalid @enderror">
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>`,
    },
    {
      type: "code",
      title: "Detail Mata Kuliah",
      path: "resources/views/mata-kuliah/show.blade.php",
      note: "Detail mata kuliah sudah siap membaca nilai dari pivot, namun input nilainya dibahas pada Pertemuan 10.",
      code: `@forelse ($mataKuliah->mahasiswa as $mahasiswa)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $mahasiswa->nim }}</td>
        <td>{{ $mahasiswa->nama }}</td>
        <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
        <td>{{ $mahasiswa->pivot->nilai }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted">
            Belum ada mahasiswa yang mengambil mata kuliah ini.
        </td>
    </tr>
@endforelse`,
    },
    {
      type: "code",
      title: "Navbar: Menu Mata Kuliah",
      path: "resources/views/partials/navbar.blade.php",
      note: "Menu aktif dibaca dari nama route, bukan dari URL manual.",
      code: `<a class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}"
   href="{{ route('mahasiswa.index') }}">
    <i class="fas fa-user-graduate"></i> Mahasiswa
</a>

<a class="nav-link {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}"
   href="{{ route('mata-kuliah.index') }}">
    <i class="fas fa-book"></i> Mata Kuliah
</a>`,
    },
    {
      type: "table",
      title: "Checklist Praktik Pertemuan 9",
      columns: ["No", "Praktik", "Output"],
      rows: [
        ["1", "Review relasi model", "Mahasiswa, KartuMahasiswa, MataKuliah"],
        ["2", "Generate kartu", "Nomor KTM-NIM muncul di detail mahasiswa"],
        ["3", "Route mata kuliah", "Index, create, store, show, edit, update"],
        ["4", "MataKuliahController", "Validasi, withCount, redirect, flash message"],
        ["5", "View mata kuliah", "Daftar, tambah, edit, detail"],
        ["6", "Navbar", "Menu Mata Kuliah aktif"],
      ],
    },
    {
      type: "table",
      title: "Latihan Praktik Mahasiswa",
      columns: ["No", "Instruksi", "Bukti"],
      rows: [
        ["1", "Buka detail salah satu mahasiswa", "No Kartu masih '-'"],
        ["2", "Klik Generate Kartu", "Nomor KTM sesuai NIM"],
        ["3", "Tambah MK004 Pemrograman Mobile", "Tampil di daftar mata kuliah"],
        ["4", "Tambah MK005 Data Mining", "Jumlah mahasiswa masih 0"],
        ["5", "Edit SKS salah satu mata kuliah", "Flash message sukses"],
        ["6", "Buka detail mata kuliah", "Tabel peserta siap menampilkan nilai"],
      ],
    },
    {
      type: "bullets",
      title: "Output Akhir Pertemuan 9",
      lead: "Project sudah memiliki master mata kuliah dan kartu mahasiswa otomatis.",
      items: [
        "Detail mahasiswa dapat membuat kartu jika belum tersedia.",
        "Nomor kartu memakai format KTM-{NIM}.",
        "Daftar mata kuliah menampilkan jumlah mahasiswa peserta.",
        "Form tambah dan edit mata kuliah sudah memakai validasi.",
        "Detail mata kuliah siap membaca data pivot dari tabel nilais.",
      ],
    },
    {
      type: "closing",
      title: "Jembatan ke Pertemuan 10",
      lead: "Setelah master Mata Kuliah siap, pertemuan berikutnya fokus pada input Nilai Mahasiswa melalui tabel nilais.",
      items: ["Form input nilai", "Validasi anti-duplikasi", "Edit dan hapus nilai", "Pivot table di detail mahasiswa"],
    },
  ],
};

const deck10 = {
  title: "Pertemuan 10",
  subtitle: "Form Nilai Mahasiswa dan Pivot Table",
  filename: "Pertemuan_10.pptx",
  accent: theme.teal,
  slides: [
    {
      type: "title",
      title: "Pertemuan 10",
      subtitle: "Form Nilai Mahasiswa dan Pivot Table",
      kicker: "Laravel 13 - belongsToMany - Validasi Relasi",
      chips: ["NilaiController", "nilais", "withPivot", "Detail Mahasiswa"],
    },
    {
      type: "agenda",
      title: "Agenda Praktik Pertemuan 10",
      items: [
        "Review tabel nilais sebagai pivot dan data nilai",
        "Membuat route create, store, edit, update, dan destroy nilai",
        "Membuat form input nilai dari detail mahasiswa",
        "Mencegah duplikasi nilai untuk mata kuliah yang sama",
        "Menampilkan nilai pada detail mahasiswa dan detail mata kuliah",
        "Menguji alur CRUD nilai end to end",
      ],
    },
    {
      type: "bullets",
      title: "Tujuan Pembelajaran",
      lead: "Mahasiswa mempraktikkan relasi many-to-many dengan atribut tambahan pada pivot.",
      items: [
        "Membaca $mk->pivot->id dan $mk->pivot->nilai dari belongsToMany.",
        "Membuat form nilai yang membawa mahasiswa_id tersembunyi.",
        "Membuat validasi exists untuk foreign key mahasiswa dan mata kuliah.",
        "Mencegah kombinasi mahasiswa_id dan mata_kuliah_id tersimpan dua kali.",
        "Menghapus data nilai dari halaman detail mahasiswa.",
      ],
    },
    {
      type: "flow",
      title: "Alur Besar Nilai Mahasiswa",
      lead: "Nilai dibuat dari sisi mahasiswa, tetapi bisa dilihat kembali dari sisi mahasiswa maupun mata kuliah.",
      nodes: ["Detail Mahasiswa", "Input Nilai", "Tabel nilais", "Detail Mahasiswa", "Detail Mata Kuliah"],
      notes: [
        "mahasiswa_id dibawa dari URL.",
        "mata_kuliah_id dipilih dari dropdown.",
        "nilai disimpan sebagai atribut pivot.",
        "Edit dan hapus memakai id dari tabel nilais.",
      ],
    },
    {
      type: "code",
      title: "Migration Tabel nilais",
      path: "database/migrations/2026_05_12_143922_create_nilais_table.php",
      note: "Unique gabungan menjadi pengaman di level database.",
      code: `Schema::create('nilais', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mahasiswa_id')
        ->constrained('mahasiswas')
        ->cascadeOnDelete();
    $table->foreignId('mata_kuliah_id')
        ->constrained('mata_kuliahs')
        ->cascadeOnDelete();
    $table->integer('nilai');
    $table->timestamps();

    $table->unique(['mahasiswa_id', 'mata_kuliah_id']);
});`,
    },
    {
      type: "code",
      title: "Model Nilai",
      path: "app/Models/Nilai.php",
      note: "Model Nilai dipakai saat create, update, dan delete data pada tabel pivot.",
      code: `class Nilai extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'nilai',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }
}`,
    },
    {
      type: "code",
      title: "Relasi Pivot di Mahasiswa",
      path: "app/Models/Mahasiswa.php",
      note: "withPivot('id', 'nilai') wajib agar tombol edit/hapus tahu id baris nilai.",
      code: `public function mataKuliah()
{
    return $this->belongsToMany(MataKuliah::class, 'nilais')
        ->withPivot('id', 'nilai')
        ->withTimestamps();
}

public function nilai()
{
    return $this->hasMany(Nilai::class);
}`,
    },
    {
      type: "code",
      title: "Relasi Pivot di MataKuliah",
      path: "app/Models/MataKuliah.php",
      note: "Relasi dua arah membuat detail mata kuliah dapat menampilkan daftar mahasiswa peserta.",
      code: `public function mahasiswa()
{
    return $this->belongsToMany(Mahasiswa::class, 'nilais')
        ->withPivot('id', 'nilai')
        ->withTimestamps();
}

public function nilai()
{
    return $this->hasMany(Nilai::class);
}`,
    },
    {
      type: "code",
      title: "Route Nilai Mahasiswa",
      path: "routes/web.php",
      note: "Route create memakai mahasiswa_id agar form langsung tahu mahasiswa yang sedang diberi nilai.",
      code: `Route::get('/nilai/create/{mahasiswa_id}', [NilaiController::class, 'create'])
    ->name('nilai.create');
Route::post('/nilai', [NilaiController::class, 'store'])
    ->name('nilai.store');
Route::get('/nilai/{id}/edit', [NilaiController::class, 'edit'])
    ->name('nilai.edit');
Route::put('/nilai/{id}', [NilaiController::class, 'update'])
    ->name('nilai.update');
Route::delete('/nilai/{id}', [NilaiController::class, 'destroy'])
    ->name('nilai.destroy');`,
    },
    {
      type: "code",
      title: "NilaiController: Create",
      path: "app/Http/Controllers/NilaiController.php",
      note: "Form membutuhkan biodata mahasiswa dan daftar mata kuliah untuk dropdown.",
      code: `public function create($mahasiswa_id)
{
    $mahasiswa = Mahasiswa::with('jurusan')->findOrFail($mahasiswa_id);
    $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();

    return view('nilai.create', compact('mahasiswa', 'mataKuliahs'));
}`,
    },
    {
      type: "code",
      title: "NilaiController: Store",
      path: "app/Http/Controllers/NilaiController.php",
      note: "Validasi menjaga foreign key valid dan nilai berada pada rentang 0 sampai 100.",
      code: `public function store(Request $request)
{
    $validated = $request->validate([
        'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
        'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
        'nilai' => ['required', 'integer', 'min:0', 'max:100'],
    ]);

    $sudahAda = Nilai::where('mahasiswa_id', $validated['mahasiswa_id'])
        ->where('mata_kuliah_id', $validated['mata_kuliah_id'])
        ->exists();

    if ($sudahAda) {
        return back()->withInput()
            ->with('error', 'Nilai untuk mata kuliah tersebut sudah ada.');
    }

    Nilai::create($validated);
    return redirect()->route('mahasiswa.detail', $validated['mahasiswa_id'])
        ->with('success', 'Nilai mahasiswa berhasil ditambahkan.');
}`,
    },
    {
      type: "code",
      title: "NilaiController: Edit",
      path: "app/Http/Controllers/NilaiController.php",
      note: "Edit memakai id tabel nilais, bukan id mata kuliah.",
      code: `public function edit($id)
{
    $nilai = Nilai::with(['mahasiswa', 'mataKuliah'])->findOrFail($id);
    $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();

    return view('nilai.edit', compact('nilai', 'mataKuliahs'));
}`,
    },
    {
      type: "code",
      title: "NilaiController: Update",
      path: "app/Http/Controllers/NilaiController.php",
      note: "Saat update, pengecekan duplikasi mengabaikan baris nilai yang sedang diedit.",
      code: `public function update(Request $request, $id)
{
    $nilai = Nilai::findOrFail($id);

    $validated = $request->validate([
        'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
        'nilai' => ['required', 'integer', 'min:0', 'max:100'],
    ]);

    $sudahAda = Nilai::where('mahasiswa_id', $nilai->mahasiswa_id)
        ->where('mata_kuliah_id', $validated['mata_kuliah_id'])
        ->where('id', '!=', $nilai->id)
        ->exists();

    if ($sudahAda) {
        return back()->withInput()
            ->with('error', 'Mahasiswa ini sudah memiliki nilai pada mata kuliah tersebut.');
    }

    $nilai->update($validated);
    return redirect()->route('mahasiswa.detail', $nilai->mahasiswa_id)
        ->with('success', 'Nilai mahasiswa berhasil diperbarui.');
}`,
    },
    {
      type: "code",
      title: "NilaiController: Destroy",
      path: "app/Http/Controllers/NilaiController.php",
      note: "mahasiswa_id disimpan dulu agar redirect tetap kembali ke detail mahasiswa yang benar.",
      code: `public function destroy($id)
{
    $nilai = Nilai::findOrFail($id);
    $mahasiswaId = $nilai->mahasiswa_id;

    $nilai->delete();

    return redirect()
        ->route('mahasiswa.detail', $mahasiswaId)
        ->with('success', 'Nilai mahasiswa berhasil dihapus.');
}`,
    },
    {
      type: "code",
      title: "Detail Mahasiswa: Tombol Input Nilai",
      path: "resources/views/mahasiswa/detail.blade.php",
      note: "Tombol mengirim id mahasiswa ke route nilai.create.",
      code: `<div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
    <span>Nilai Mata Kuliah</span>
    <a href="{{ route('nilai.create', $mahasiswa->id) }}" class="btn btn-light btn-sm">
        Input Nilai
    </a>
</div>`,
    },
    {
      type: "code",
      title: "Detail Mahasiswa: Tabel Nilai",
      path: "resources/views/mahasiswa/detail.blade.php",
      note: "Data mata kuliah dibaca melalui relasi belongsToMany, nilai dibaca dari pivot.",
      code: `@forelse ($mahasiswa->mataKuliah as $mk)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $mk->kode_mk }}</td>
        <td>{{ $mk->nama_mk }}</td>
        <td>{{ $mk->sks }}</td>
        <td>{{ $mk->pivot->nilai }}</td>
        <td>
            <a href="{{ route('nilai.edit', $mk->pivot->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('nilai.destroy', $mk->pivot->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center text-muted">Belum ada nilai.</td></tr>
@endforelse`,
    },
    {
      type: "code",
      title: "View Input Nilai: Data Mahasiswa",
      path: "resources/views/nilai/create.blade.php",
      note: "Bagian atas form memastikan user memberi nilai kepada mahasiswa yang benar.",
      code: `<div class="card-header bg-primary text-white fw-bold">Data Mahasiswa</div>
<div class="card-body">
    <p><strong>NIM:</strong> {{ $mahasiswa->nim }}</p>
    <p><strong>Nama:</strong> {{ $mahasiswa->nama }}</p>
    <p><strong>Jurusan:</strong> {{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</p>
</div>`,
    },
    {
      type: "code",
      title: "View Input Nilai: Form",
      path: "resources/views/nilai/create.blade.php",
      note: "mahasiswa_id disimpan sebagai hidden input karena sudah diketahui dari route.",
      code: `<form action="{{ route('nilai.store') }}" method="POST">
    @csrf
    <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
    <select name="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror">
        <option value="">-- Pilih Mata Kuliah --</option>
        @foreach ($mataKuliahs as $mk)
            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
            </option>
        @endforeach
    </select>
    <input type="number" name="nilai" value="{{ old('nilai') }}"
        class="form-control @error('nilai') is-invalid @enderror">
    <button type="submit" class="btn btn-success">Simpan Nilai</button>
</form>`,
    },
    {
      type: "code",
      title: "View Edit Nilai",
      path: "resources/views/nilai/edit.blade.php",
      note: "Form edit memakai method spoofing PUT karena browser hanya mendukung GET dan POST.",
      code: `<form action="{{ route('nilai.update', $nilai->id) }}" method="POST">
    @csrf
    @method('PUT')
    <select name="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror">
        <option value="">-- Pilih Mata Kuliah --</option>
        @foreach ($mataKuliahs as $mk)
            <option value="{{ $mk->id }}"
                {{ old('mata_kuliah_id', $nilai->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
                {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
            </option>
        @endforeach
    </select>
    <input type="number" name="nilai" value="{{ old('nilai', $nilai->nilai) }}"
        class="form-control @error('nilai') is-invalid @enderror">
    <button type="submit" class="btn btn-warning">Update Nilai</button>
</form>`,
    },
    {
      type: "code",
      title: "Detail Mata Kuliah: Peserta dan Nilai",
      path: "resources/views/mata-kuliah/show.blade.php",
      note: "Dari sisi mata kuliah, pivot tetap menyediakan nilai mahasiswa.",
      code: `@forelse ($mataKuliah->mahasiswa as $mahasiswa)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $mahasiswa->nim }}</td>
        <td>{{ $mahasiswa->nama }}</td>
        <td>{{ $mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
        <td>{{ $mahasiswa->pivot->nilai }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted">
            Belum ada mahasiswa yang mengambil mata kuliah ini.
        </td>
    </tr>
@endforelse`,
    },
    {
      type: "flow",
      title: "Alur Create Data Relasi",
      lead: "Mahasiswa melihat foreign key disimpan, lalu dibaca ulang sebagai relasi.",
      nodes: ["Klik Input Nilai", "Pilih Mata Kuliah", "Isi Nilai", "Simpan nilais", "Tampil di Pivot"],
      notes: [
        "mahasiswa_id dari hidden input.",
        "mata_kuliah_id dari dropdown.",
        "nilai disimpan di tabel nilais.",
        "View membaca $mk->pivot->nilai.",
      ],
    },
    {
      type: "flow",
      title: "Alur Edit dan Hapus Nilai",
      lead: "Edit dan hapus memakai id baris nilais yang tersedia lewat withPivot('id', 'nilai').",
      nodes: ["Detail Mahasiswa", "Klik Edit/Hapus", "Ambil Pivot ID", "Update/Delete Nilai", "Redirect Detail"],
      notes: [
        "Edit membuka nilai.edit.",
        "Update menjaga anti-duplikasi.",
        "Delete memakai method DELETE.",
        "Flash message memberi status proses.",
      ],
    },
    {
      type: "table",
      title: "Checklist Praktik Pertemuan 10",
      columns: ["No", "Praktik", "Output"],
      rows: [
        ["1", "Review tabel nilais", "FK mahasiswa_id dan mata_kuliah_id"],
        ["2", "Route nilai", "Create, store, edit, update, destroy"],
        ["3", "NilaiController", "Validasi, anti-duplikasi, redirect"],
        ["4", "View input nilai", "Dropdown mata kuliah dan hidden mahasiswa_id"],
        ["5", "Detail mahasiswa", "Nilai, edit, hapus dari pivot"],
        ["6", "Detail mata kuliah", "Peserta dan nilai tampil"],
      ],
    },
    {
      type: "table",
      title: "Latihan Praktik Mahasiswa",
      columns: ["No", "Instruksi", "Bukti"],
      rows: [
        ["1", "Input nilai Andi untuk MK004 = 88", "Tampil di detail mahasiswa"],
        ["2", "Input nilai Budi untuk MK005 = 92", "Tampil di tabel nilai"],
        ["3", "Edit nilai Andi dari 88 ke 95", "Nilai berubah setelah update"],
        ["4", "Uji input duplikat pada MK sama", "Muncul flash error"],
        ["5", "Hapus salah satu nilai", "Baris hilang dari detail mahasiswa"],
        ["6", "Buka detail mata kuliah", "Mahasiswa dan nilai tampil dari pivot"],
      ],
    },
    {
      type: "bullets",
      title: "Output Akhir Pertemuan 10",
      lead: "Fitur nilai mahasiswa berjalan sebagai CRUD relasional penuh.",
      items: [
        "Form input nilai tersedia dari detail mahasiswa.",
        "Nilai tidak bisa ganda untuk mahasiswa dan mata kuliah yang sama.",
        "Edit dan hapus nilai memakai id dari pivot.",
        "Detail mahasiswa dan detail mata kuliah membaca data yang sama dari tabel nilais.",
        "Validasi exists, min, max, dan flash message berjalan.",
      ],
    },
    {
      type: "closing",
      title: "Penutup dan Arah Lanjutan",
      lead: "Project akademik sudah memiliki data mahasiswa, kartu, mata kuliah, dan nilai yang saling terhubung.",
      items: ["Pagination", "Search", "Autentikasi sederhana", "Pembatasan akses menu"],
    },
  ],
};

function addText(ctx, slide, text, x, y, w, h, opts = {}) {
  return ctx.addText(slide, {
    text,
    x,
    y,
    w,
    h,
    fontSize: opts.size || 28,
    color: opts.color || theme.ink,
    bold: opts.bold || false,
    typeface: opts.mono ? "Aptos Mono" : opts.face,
    align: opts.align || "left",
    valign: opts.valign || "top",
    fill: opts.fill || "#00000000",
    line: opts.line || { fill: "#00000000", width: 0 },
    insets: opts.insets || { left: 0, right: 0, top: 0, bottom: 0 },
  });
}

function addRect(ctx, slide, x, y, w, h, fill, line = undefined, radius = "roundRect") {
  return ctx.addShape(slide, {
    geometry: radius,
    x,
    y,
    w,
    h,
    fill: fill,
    line: line || { fill, width: 0 },
  });
}

function addHeader(ctx, slide, deck, slideNo, title) {
  addRect(ctx, slide, 0, 0, 1280, 720, theme.white, { fill: theme.white, width: 0 }, "rect");
  addRect(ctx, slide, 0, 0, 1280, 76, theme.navy, { fill: theme.navy, width: 0 }, "rect");
  addRect(ctx, slide, 0, 76, 1280, 6, deck.accent, { fill: deck.accent, width: 0 }, "rect");
  addText(ctx, slide, "Pemrograman Web Lanjut - Laravel 13", 46, 22, 520, 28, {
    size: 18,
    color: theme.white,
    bold: true,
  });
  addText(ctx, slide, deck.title, 1030, 20, 130, 30, {
    size: 18,
    color: theme.white,
    bold: true,
    align: "right",
  });
  addText(ctx, slide, String(slideNo).padStart(2, "0"), 1178, 16, 56, 38, {
    size: 24,
    color: theme.white,
    bold: true,
    align: "right",
  });
  if (title) {
    addText(ctx, slide, title, 54, 112, 800, 48, { size: 34, color: theme.ink, bold: true });
  }
}

function addFooter(ctx, slide) {
  addText(ctx, slide, commonFooter, 54, 675, 820, 20, { size: 12, color: theme.muted });
}

function addNote(ctx, slide, text, x, y, w, h, color = theme.blue) {
  addRect(ctx, slide, x, y, w, h, "#EFF6FF", { fill: "#BFDBFE", width: 1 }, "roundRect");
  addRect(ctx, slide, x, y, 6, h, color, { fill: color, width: 0 }, "rect");
  addText(ctx, slide, text, x + 20, y + 14, w - 34, h - 20, { size: 17, color: theme.ink });
}

function wrappedCode(code) {
  const max = 92;
  const lines = [];
  for (const raw of code.trim().split("\n")) {
    if (raw.length <= max) {
      lines.push(raw);
      continue;
    }
    let rest = raw;
    while (rest.length > max) {
      const cutAt = Math.max(rest.lastIndexOf(" ", max), rest.lastIndexOf(",", max), rest.lastIndexOf(")", max));
      const cut = cutAt > 30 ? cutAt + 1 : max;
      lines.push(rest.slice(0, cut));
      rest = "    " + rest.slice(cut).trimStart();
    }
    if (rest) lines.push(rest);
  }
  return lines.join("\n");
}

function renderTitle(ctx, slide, deck, data, i) {
  addRect(ctx, slide, 0, 0, 1280, 720, theme.navy, { fill: theme.navy, width: 0 }, "rect");
  addRect(ctx, slide, 0, 510, 1280, 210, deck.accent, { fill: deck.accent, width: 0 }, "rect");
  addText(ctx, slide, data.kicker, 70, 70, 760, 28, { size: 18, color: "#BFDBFE", bold: true });
  addText(ctx, slide, data.title, 70, 130, 520, 82, { size: 58, color: theme.white, bold: true });
  addText(ctx, slide, data.subtitle, 70, 220, 840, 64, { size: 30, color: theme.white, bold: true });
  addText(ctx, slide, "Materi praktik setelah UTS disesuaikan dengan kode project akademik-app_pert9.", 70, 312, 760, 54, {
    size: 21,
    color: "#DDEAFE",
  });
  data.chips.forEach((chip, idx) => {
    const x = 72 + idx * 230;
    addRect(ctx, slide, x, 405, 205, 46, "#FFFFFF", { fill: "#FFFFFF", width: 0 }, "roundRect");
    addText(ctx, slide, chip, x + 18, 418, 168, 20, { size: 15, color: theme.navy, bold: true, align: "center" });
  });
  addText(ctx, slide, "Dosen: Amirudin", 70, 585, 340, 30, { size: 22, color: theme.white, bold: true });
  addText(ctx, slide, "STMIK ANTAR BANGSA", 70, 624, 340, 24, { size: 18, color: theme.white });
  addText(ctx, slide, String(i).padStart(2, "0"), 1134, 580, 90, 58, { size: 44, color: theme.white, bold: true, align: "right" });
}

function renderAgenda(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  data.items.forEach((item, idx) => {
    const y = 188 + idx * 70;
    addRect(ctx, slide, 74, y, 58, 44, idx % 2 === 0 ? deck.accent : theme.teal, { fill: idx % 2 === 0 ? deck.accent : theme.teal, width: 0 }, "roundRect");
    addText(ctx, slide, String(idx + 1).padStart(2, "0"), 88, y + 10, 30, 20, { size: 17, color: theme.white, bold: true, align: "center" });
    addText(ctx, slide, item, 154, y + 4, 900, 42, { size: 23, color: theme.ink, bold: idx === 0 });
  });
  addFooter(ctx, slide);
}

function renderBullets(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  addText(ctx, slide, data.lead, 72, 168, 960, 46, { size: 22, color: theme.muted });
  data.items.forEach((item, idx) => {
    const y = 246 + idx * 70;
    addRect(ctx, slide, 86, y + 4, 18, 18, deck.accent, { fill: deck.accent, width: 0 }, "ellipse");
    addText(ctx, slide, item, 126, y - 2, 920, 46, { size: 23, color: theme.ink });
  });
  addFooter(ctx, slide);
}

function renderFlow(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  addText(ctx, slide, data.lead, 72, 162, 1020, 44, { size: 21, color: theme.muted });
  const count = data.nodes.length;
  const gap = count === 5 ? 22 : 30;
  const boxW = count === 5 ? 205 : 240;
  const start = 72;
  data.nodes.forEach((node, idx) => {
    const x = start + idx * (boxW + gap);
    addRect(ctx, slide, x, 250, boxW, 86, idx % 2 === 0 ? "#E0F2FE" : "#ECFDF5", { fill: idx % 2 === 0 ? "#7DD3FC" : "#86EFAC", width: 1 }, "roundRect");
    addText(ctx, slide, node, x + 16, 276, boxW - 32, 30, { size: 19, color: theme.ink, bold: true, align: "center" });
    if (idx < count - 1) {
      addText(ctx, slide, ">", x + boxW + 4, 278, gap + 14, 30, { size: 24, color: deck.accent, bold: true, align: "center" });
    }
  });
  data.notes.forEach((note, idx) => {
    const x = idx % 2 === 0 ? 96 : 655;
    const y = 405 + Math.floor(idx / 2) * 82;
    addRect(ctx, slide, x, y, 475, 58, theme.soft, { fill: theme.line, width: 1 }, "roundRect");
    addText(ctx, slide, note, x + 18, y + 16, 430, 24, { size: 18, color: theme.ink });
  });
  addFooter(ctx, slide);
}

function renderCode(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  addText(ctx, slide, data.path, 72, 166, 760, 24, { size: 15, color: deck.accent, bold: true });
  addNote(ctx, slide, data.note, 860, 155, 335, 112, deck.accent);
  addRect(ctx, slide, 72, 205, 760, 420, "#0B1220", { fill: "#1E293B", width: 1 }, "roundRect");
  const code = wrappedCode(data.code);
  const lineCount = code.split("\n").length;
  const size = lineCount > 20 ? 12 : lineCount > 16 ? 13 : 14;
  addText(ctx, slide, code, 94, 228, 716, 376, {
    size,
    color: "#E5E7EB",
    mono: true,
    insets: { left: 0, right: 0, top: 0, bottom: 0 },
  });
  addRect(ctx, slide, 860, 302, 335, 216, "#F8FAFC", { fill: theme.line, width: 1 }, "roundRect");
  addText(ctx, slide, "Fokus baca kode", 885, 326, 280, 24, { size: 18, color: theme.ink, bold: true });
  addText(ctx, slide, "1. Method yang dipanggil route\n2. Model yang dipakai query\n3. Data yang dikirim ke view\n4. Redirect dan pesan setelah proses", 885, 368, 270, 104, {
    size: 17,
    color: theme.muted,
  });
  addFooter(ctx, slide);
}

function renderTable(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  const x = 66;
  const y = 168;
  const widths = [70, 430, 610];
  const rowH = 58;
  addRect(ctx, slide, x, y, 1110, 48, deck.accent, { fill: deck.accent, width: 0 }, "roundRect");
  let cur = x;
  data.columns.forEach((col, idx) => {
    addText(ctx, slide, col, cur + 12, y + 13, widths[idx] - 24, 20, { size: 16, color: theme.white, bold: true });
    cur += widths[idx];
  });
  data.rows.forEach((row, r) => {
    const yy = y + 58 + r * rowH;
    addRect(ctx, slide, x, yy, 1110, rowH - 6, r % 2 === 0 ? "#F8FAFC" : "#EEF2FF", { fill: theme.line, width: 1 }, "roundRect");
    cur = x;
    row.forEach((cell, idx) => {
      addText(ctx, slide, cell, cur + 12, yy + 14, widths[idx] - 24, 26, {
        size: idx === 0 ? 15 : 17,
        color: idx === 0 ? deck.accent : theme.ink,
        bold: idx === 0,
      });
      cur += widths[idx];
    });
  });
  addFooter(ctx, slide);
}

function renderClosing(ctx, slide, deck, data, i) {
  addHeader(ctx, slide, deck, i, data.title);
  addRect(ctx, slide, 80, 170, 1040, 158, "#F8FAFC", { fill: theme.line, width: 1 }, "roundRect");
  addText(ctx, slide, data.lead, 112, 214, 960, 58, { size: 27, color: theme.ink, bold: true, align: "center" });
  data.items.forEach((item, idx) => {
    const x = 94 + idx * 270;
    addRect(ctx, slide, x, 395, 230, 86, idx % 2 === 0 ? "#DBEAFE" : "#CCFBF1", { fill: idx % 2 === 0 ? "#93C5FD" : "#5EEAD4", width: 1 }, "roundRect");
    addText(ctx, slide, item, x + 18, 424, 194, 30, { size: 20, color: theme.ink, bold: true, align: "center" });
  });
  addFooter(ctx, slide);
}

function renderSlide(ctx, slide, deck, data, i) {
  if (data.type === "title") renderTitle(ctx, slide, deck, data, i);
  if (data.type === "agenda") renderAgenda(ctx, slide, deck, data, i);
  if (data.type === "bullets") renderBullets(ctx, slide, deck, data, i);
  if (data.type === "flow") renderFlow(ctx, slide, deck, data, i);
  if (data.type === "code") renderCode(ctx, slide, deck, data, i);
  if (data.type === "table") renderTable(ctx, slide, deck, data, i);
  if (data.type === "closing") renderClosing(ctx, slide, deck, data, i);
}

async function buildDeck(artifact, deck) {
  const { Presentation, PresentationFile } = artifact;
  const presentation = Presentation.create({ slideSize });
  const previewDir = path.join(previewRoot, deck.filename.replace(".pptx", ""));
  await fs.mkdir(previewDir, { recursive: true });

  for (const [index, data] of deck.slides.entries()) {
    const slide = presentation.slides.add();
    const ctx = createSlideContext(artifact, {
      slideSize,
      slideNumber: index + 1,
      outputDir: root,
      assetDir: path.join(workspace, "assets"),
      workspaceDir: workspace,
    });
    renderSlide(ctx, slide, deck, data, index + 1);
  }

  const out = path.join(root, deck.filename);
  const pptx = await PresentationFile.exportPptx(presentation);
  await pptx.save(out);

  for (let idx = 0; idx < presentation.slides.count; idx += 1) {
    const slide = presentation.slides.getItem(idx);
    const preview = await presentation.export({ slide, format: "png", scale: 0.75 });
    await saveBlobToFile(preview, path.join(previewDir, `slide-${padSlideNumber(idx + 1)}.png`));
  }

  return { out, slideCount: presentation.slides.count, previewDir };
}

async function zipTest(file) {
  return new Promise((resolve, reject) => {
    const child = spawn("unzip", ["-t", file], { stdio: ["ignore", "pipe", "pipe"] });
    let stderr = "";
    child.stderr.on("data", (chunk) => {
      stderr += chunk.toString();
    });
    child.on("close", (code) => {
      if (code === 0) resolve();
      else reject(new Error(stderr || `unzip -t failed for ${file}`));
    });
  });
}

async function main() {
  await ensureArtifactToolWorkspace(workspace);
  const artifact = await importArtifactTool(workspace);
  const results = [];
  for (const deck of [deck9, deck10]) {
    const result = await buildDeck(artifact, deck);
    await zipTest(result.out);
    results.push(result);
  }
  await fs.writeFile(path.join(workspace, "manifest.json"), JSON.stringify(results, null, 2) + "\n", "utf8");
  console.log(JSON.stringify(results, null, 2));
}

main().catch((error) => {
  console.error(error.stack || error.message || String(error));
  process.exit(1);
});
