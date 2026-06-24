<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pertemuan10Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_form_nilai_uses_mahasiswa_context_and_saves_pivot_score(): void
    {
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();

        $this->get(route('nilai.create', $mahasiswa))
            ->assertOk()
            ->assertSee('Data Mahasiswa')
            ->assertSee($mahasiswa->nim)
            ->assertSee('name="mahasiswa_id"', false)
            ->assertSee((string) $mahasiswa->id)
            ->assertSee($mataKuliah->kode_mk);

        $this->post(route('nilai.store'), [
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ])->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertDatabaseHas('nilais', [
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ]);

        $this->get(route('mahasiswa.detail', $mahasiswa))
            ->assertOk()
            ->assertSee($mataKuliah->nama_mk)
            ->assertSee('88')
            ->assertSee(route('nilai.edit', Nilai::first()), false);
    }

    public function test_update_rejects_duplicate_subject_and_destroy_returns_to_mahasiswa_detail(): void
    {
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();
        $mataKuliahLain = MataKuliah::create([
            'kode_mk' => 'MK005',
            'nama_mk' => 'Data Mining',
            'sks' => 3,
        ]);

        $nilai = Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ]);
        Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliahLain->id,
            'nilai' => 92,
        ]);

        $this->get(route('nilai.edit', $nilai))
            ->assertOk()
            ->assertSee('Edit Nilai Mahasiswa')
            ->assertSee('name="_method" value="PUT"', false);

        $this->put(route('nilai.update', $nilai), [
            'mata_kuliah_id' => $mataKuliahLain->id,
            'nilai' => 95,
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('nilais', [
            'id' => $nilai->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ]);

        $this->put(route('nilai.update', $nilai), [
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 95,
        ])->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->delete(route('nilai.destroy', $nilai))
            ->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertDatabaseMissing('nilais', ['id' => $nilai->id]);
    }

    public function test_mata_kuliah_detail_reads_mahasiswa_scores_from_pivot(): void
    {
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();

        Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 88,
        ]);

        $this->get(route('mata-kuliah.show', $mataKuliah))
            ->assertOk()
            ->assertSee($mahasiswa->nim)
            ->assertSee($mahasiswa->nama)
            ->assertSee($mahasiswa->jurusan->nama_jurusan)
            ->assertSee('88');
    }

    private function createAcademicData(): array
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

        $mataKuliah = MataKuliah::create([
            'kode_mk' => 'MK004',
            'nama_mk' => 'Pemrograman Mobile',
            'sks' => 3,
        ]);

        return [$mahasiswa, $mataKuliah];
    }
}
