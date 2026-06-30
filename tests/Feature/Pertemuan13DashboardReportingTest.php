<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Pertemuan13DashboardReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_summary_charts_and_latest_scores(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();
        $secondStudent = $this->createStudent(Jurusan::firstOrFail(), '2301002', 'Budi Santoso');

        Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 90,
        ]);
        Nilai::create([
            'mahasiswa_id' => $secondStudent->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 70,
        ]);

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
            ->assertViewHas('labelJurusan', ['Teknik Informatika'])
            ->assertViewHas('dataJurusan', [2])
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

    public function test_report_access_follows_role_rules(): void
    {
        $this->assertTrue(Route::has('laporan.nilai'));
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
        $otherSubject = MataKuliah::create([
            'kode_mk' => 'MK014',
            'nama_mk' => 'Pengujian Web',
            'sks' => 2,
        ]);
        $otherStudent = $this->createStudent(Jurusan::firstOrFail(), '2301003', 'Citra Lestari');

        Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 90]);
        Nilai::create(['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $otherSubject->id, 'nilai' => 80]);
        Nilai::create(['mahasiswa_id' => $otherStudent->id, 'mata_kuliah_id' => $mataKuliah->id, 'nilai' => 60]);

        $query = http_build_query([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai_min' => 75,
            'nilai_max' => 100,
        ]);

        $this->actingAs($admin)
            ->get('/laporan/nilai?'.$query)
            ->assertOk()
            ->assertViewHas('summary', [
                'totalData' => 1,
                'rataRata' => 90.0,
                'nilaiTertinggi' => 90,
                'nilaiTerendah' => 90,
            ])
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
            ->get('/laporan/nilai?nilai_min=90&nilai_max=75')
            ->assertSessionHasErrors('nilai_max');

        $this->actingAs($admin)
            ->get('/laporan/nilai?nilai_min=-1&nilai_max=101')
            ->assertSessionHasErrors(['nilai_min', 'nilai_max']);
    }

    public function test_empty_report_uses_zero_summary_and_empty_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/laporan/nilai')
            ->assertOk()
            ->assertViewHas('summary', [
                'totalData' => 0,
                'rataRata' => 0.0,
                'nilaiTertinggi' => 0,
                'nilaiTerendah' => 0,
            ])
            ->assertSee('Data tidak ditemukan.');
    }

    public function test_report_pagination_keeps_active_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$mahasiswa] = $this->createAcademicData();

        for ($index = 1; $index <= 11; $index++) {
            $subject = MataKuliah::create([
                'kode_mk' => 'PG'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'nama_mk' => 'Mata Kuliah '.$index,
                'sks' => 2,
            ]);
            Nilai::create([
                'mahasiswa_id' => $mahasiswa->id,
                'mata_kuliah_id' => $subject->id,
                'nilai' => 80,
            ]);
        }

        $this->actingAs($admin)
            ->get('/laporan/nilai?mahasiswa_id='.$mahasiswa->id.'&nilai_min=75')
            ->assertOk()
            ->assertSee('mahasiswa_id='.$mahasiswa->id, false)
            ->assertSee('nilai_min=75', false)
            ->assertSee('page=2', false);
    }

    private function createAcademicData(): array
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'TI',
            'nama_jurusan' => 'Teknik Informatika',
        ]);
        $mahasiswa = $this->createStudent($jurusan, '2301001', 'Andi Saputra');
        $mataKuliah = MataKuliah::create([
            'kode_mk' => 'MK013',
            'nama_mk' => 'Dashboard dan Reporting',
            'sks' => 3,
        ]);

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
