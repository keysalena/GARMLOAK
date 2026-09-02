<?php

namespace Tests\Feature;

use App\Models\Materi;
use App\Models\Module;
use App\Models\User;
use App\Models\UserMaterialProgress;
use App\Models\UserModuleProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModuleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_module_list_with_progress(): void
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(),
            'kelas_id' => null,
            'role' => 'mahasiswa',
            'nama' => 'Budi',
            'email' => 'budi@example.com',
            'password_hash' => bcrypt('password123'),
            'avatar_url' => null,
            'level' => 1,
            'total_xp' => 0,
        ]);

        $module1 = Module::create([
            'kode_modul' => 'Modul 1',
            'judul' => 'Computer System & Interconnection',
            'sub_cpmk' => 'Sub-CPMK 1-2',
            'deskripsi' => 'Deskripsi modul',
            'urutan' => 1,
        ]);

        $module2 = Module::create([
            'kode_modul' => 'Modul 2',
            'judul' => 'Memory & Storage',
            'sub_cpmk' => 'Sub-CPMK 3',
            'deskripsi' => 'Deskripsi modul 2',
            'urutan' => 2,
        ]);

        UserModuleProgress::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'module_id' => $module1->module_id,
            'persen_selesai' => 75,
            'status' => 'berjalan',
            'skor_rata_rata' => 80.50,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/modules');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.id', $module1->module_id)
            ->assertJsonPath('data.0.persen_selesai', 75)
            ->assertJsonPath('data.1.id', $module2->module_id)
            ->assertJsonPath('data.1.persen_selesai', 0);
    }

    public function test_user_can_fetch_module_detail_with_material_status(): void
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(),
            'kelas_id' => null,
            'role' => 'mahasiswa',
            'nama' => 'Sari',
            'email' => 'sari@example.com',
            'password_hash' => bcrypt('password123'),
            'avatar_url' => null,
            'level' => 1,
            'total_xp' => 0,
        ]);

        $module = Module::create([
            'kode_modul' => 'Modul 3',
            'judul' => 'I/O, Arithmetic & Instruction',
            'sub_cpmk' => 'Sub-CPMK 4-5',
            'deskripsi' => 'Detail modul 3',
            'urutan' => 3,
        ]);

        $materi1 = Materi::create([
            'module_id' => $module->module_id,
            'judul' => 'Pengenalan I/O',
            'tipe' => 'teks',
            'konten_url' => 'https://example.com/io',
            'urutan' => 1,
        ]);

        $materi2 = Materi::create([
            'module_id' => $module->module_id,
            'judul' => 'Arithmetic',
            'tipe' => 'video',
            'konten_url' => 'https://example.com/arithmetic',
            'urutan' => 2,
        ]);

        UserMaterialProgress::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'materi_id' => $materi1->materi_id,
            'status' => 'selesai',
            'selesai_pada' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/modules/' . $module->module_id);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $module->module_id)
            ->assertJsonPath('data.sub_cpmk', 'Sub-CPMK 4-5')
            ->assertJsonPath('data.materi.0.id', $materi1->materi_id)
            ->assertJsonPath('data.materi.0.status', 'selesai')
            ->assertJsonPath('data.materi.1.id', $materi2->materi_id)
            ->assertJsonPath('data.materi.1.status', 'belum');
    }

    public function test_user_can_fetch_material_detail_and_mark_it_complete(): void
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(),
            'kelas_id' => null,
            'role' => 'mahasiswa',
            'nama' => 'Rina',
            'email' => 'rina@example.com',
            'password_hash' => bcrypt('password123'),
            'avatar_url' => null,
            'level' => 1,
            'total_xp' => 0,
        ]);

        $module = Module::create([
            'kode_modul' => 'Modul 4',
            'judul' => 'CPU, Control & Parallel Processing',
            'sub_cpmk' => 'Sub-CPMK 6-7',
            'deskripsi' => 'Detail modul 4',
            'urutan' => 4,
        ]);

        $materi = Materi::create([
            'module_id' => $module->module_id,
            'judul' => 'CPU Pipeline',
            'tipe' => 'video',
            'konten_url' => 'https://example.com/cpu-pipeline',
            'urutan' => 1,
        ]);

        $detailResponse = $this->actingAs($user, 'sanctum')->getJson('/api/materi/' . $materi->materi_id);
        $detailResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $materi->materi_id)
            ->assertJsonPath('data.status', 'belum');

        $completeResponse = $this->actingAs($user, 'sanctum')->postJson('/api/materi/' . $materi->materi_id . '/complete');
        $completeResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.status', 'selesai');

        $this->assertDatabaseHas('user_material_progress', [
            'user_id' => $user->user_id,
            'materi_id' => $materi->materi_id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('user_module_progress', [
            'user_id' => $user->user_id,
            'module_id' => $module->module_id,
            'persen_selesai' => 100,
            'status' => 'selesai',
        ]);
    }
}
