<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pertemuan11AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_pages_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_login_and_logout_safely(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@kampus.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_can_access_academic_management_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('mahasiswa.index'))->assertOk();
        $this->actingAs($admin)->get(route('mata-kuliah.index'))->assertOk();
    }

    public function test_dosen_can_manage_scores_but_cannot_access_student_management(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($dosen)->get(route('mahasiswa.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('nilai.index'))->assertOk();

        [$mahasiswa] = $this->createAcademicData();

        $this->actingAs($dosen)
            ->get(route('nilai.create', $mahasiswa))
            ->assertOk();
    }

    public function test_mahasiswa_can_only_access_nilai_saya(): void
    {
        [$mahasiswa] = $this->createAcademicData();
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'mahasiswa_id' => $mahasiswa->id,
        ]);

        $this->actingAs($user)->get(route('nilai-saya.index'))->assertOk();
        $this->actingAs($user)->get(route('mahasiswa.index'))->assertForbidden();
        $this->actingAs($user)->get(route('nilai.create', $mahasiswa))->assertForbidden();
    }

    public function test_nilai_saya_only_shows_scores_belonging_to_logged_in_student(): void
    {
        [$mahasiswa, $mataKuliah] = $this->createAcademicData();
        $jurusan = Jurusan::firstOrFail();
        $other = Mahasiswa::create([
            'jurusan_id' => $jurusan->id,
            'nim' => '2301002',
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'jenis_kelamin' => 'L',
            'alamat' => 'Bekasi',
        ]);

        Nilai::create([
            'mahasiswa_id' => $mahasiswa->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 95,
        ]);
        Nilai::create([
            'mahasiswa_id' => $other->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'nilai' => 55,
        ]);

        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'mahasiswa_id' => $mahasiswa->id,
        ]);

        $this->actingAs($user)
            ->get(route('nilai-saya.index'))
            ->assertOk()
            ->assertSee('95')
            ->assertDontSee('55');
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
            'kode_mk' => 'MK001',
            'nama_mk' => 'Pemrograman Web Lanjut',
            'sks' => 3,
        ]);

        return [$mahasiswa, $mataKuliah];
    }
}
