<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pertemuan9Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_mata_kuliah_can_be_created_updated_and_shown_with_mahasiswa_scores(): void
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'TI',
            'nama_jurusan' => 'Teknik Informatika',
        ]);

        $mahasiswa = Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2301001',
            'nama' => 'Andi Saputra',
            'email' => 'andi@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jakarta',
        ]);

        $this->post(route('mata-kuliah.store'), [
            'kode_mk' => 'MK004',
            'nama_mk' => 'Pemrograman Mobile',
            'sks' => 3,
        ])->assertRedirect(route('mata-kuliah.index'));

        $mataKuliah = MataKuliah::where('kode_mk', 'MK004')->firstOrFail();

        $this->put(route('mata-kuliah.update', $mataKuliah), [
            'kode_mk' => 'MK004',
            'nama_mk' => 'Pemrograman Mobile Lanjut',
            'sks' => 4,
        ])->assertRedirect(route('mata-kuliah.index'));

        Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 95,
        ]);

        $this->get(route('mata-kuliah.show', $mataKuliah))
            ->assertOk()
            ->assertSee('Pemrograman Mobile Lanjut')
            ->assertSee('Andi Saputra')
            ->assertSee('95');
    }

    public function test_statistik_jurusan_route_returns_mahasiswa_counts(): void
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'TI',
            'nama_jurusan' => 'Teknik Informatika',
        ]);

        Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2301001',
            'nama' => 'Andi Saputra',
            'email' => 'andi@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jakarta',
        ]);

        $this->get(route('mahasiswa.statistik'))
            ->assertOk()
            ->assertSee('Statistik Mahasiswa per Jurusan')
            ->assertSee('Teknik Informatika')
            ->assertSee('1');
    }

    public function test_nilai_can_be_created_updated_deleted_and_rejects_duplicate_subjects(): void
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'SI',
            'nama_jurusan' => 'Sistem Informasi',
        ]);

        $mahasiswa = Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2302001',
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Bekasi',
        ]);

        $mataKuliah = MataKuliah::create([
            'kode_mk' => 'MK005',
            'nama_mk' => 'Data Mining',
            'sks' => 3,
        ]);

        $this->post(route('nilai.store'), [
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 92,
        ])->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $nilai = Nilai::firstOrFail();

        $this->post(route('nilai.store'), [
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 80,
        ])->assertSessionHas('error');

        $this->assertSame(1, Nilai::count());

        $this->put(route('nilai.update', $nilai), [
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ])->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertDatabaseHas('nilais', [
            'id' => $nilai->id,
            'nilai' => 88,
        ]);

        $this->get(route('mahasiswa.detail', $mahasiswa))
            ->assertOk()
            ->assertSee('Data Mining')
            ->assertSee('88')
            ->assertSee(route('nilai.edit', $nilai), false);

        $this->delete(route('nilai.destroy', $nilai))
            ->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertDatabaseMissing('nilais', [
            'id' => $nilai->id,
        ]);
    }
}
