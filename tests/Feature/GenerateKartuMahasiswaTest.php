<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\KartuMahasiswa;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateKartuMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_mahasiswa_card_can_be_generated_from_detail_page(): void
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'TI',
            'nama_jurusan' => 'Teknik Informatika',
        ]);

        $mahasiswa = Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2303001',
            'nama' => 'Citra Lestari',
            'email' => 'citra@example.test',
            'jenis_kelamin' => 'P',
            'alamat' => 'Tangerang',
        ]);

        $this->post(route('mahasiswa.generate-kartu', $mahasiswa))
            ->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertDatabaseHas('kartu_mahasiswas', [
            'mahasiswa_id' => $mahasiswa->id,
            'nomor_kartu' => 'KTM-2303001',
        ]);

        $this->get(route('mahasiswa.detail', $mahasiswa))
            ->assertOk()
            ->assertSee('KTM-2303001')
            ->assertDontSee('Generate Kartu');
    }

    public function test_generate_card_does_not_duplicate_existing_card(): void
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 'SI',
            'nama_jurusan' => 'Sistem Informasi',
        ]);

        $mahasiswa = Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2303002',
            'nama' => 'Dian Pratama',
            'email' => 'dian@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Depok',
        ]);

        KartuMahasiswa::create([
            'mahasiswa_id' => $mahasiswa->id,
            'nomor_kartu' => 'KTM-LAMA',
            'tanggal_terbit' => now(),
            'tanggal_berlaku' => now()->addYears(4),
        ]);

        $this->post(route('mahasiswa.generate-kartu', $mahasiswa))
            ->assertRedirect(route('mahasiswa.detail', $mahasiswa));

        $this->assertSame(1, KartuMahasiswa::where('mahasiswa_id', $mahasiswa->id)->count());
        $this->assertDatabaseHas('kartu_mahasiswas', [
            'mahasiswa_id' => $mahasiswa->id,
            'nomor_kartu' => 'KTM-LAMA',
        ]);
    }
}
