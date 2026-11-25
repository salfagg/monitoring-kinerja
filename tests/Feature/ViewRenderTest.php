<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::first();
    }

    public function test_dashboard_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_command_center_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('monitoring.balai'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_index_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.index'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_saya_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.saya'));
        $response->assertStatus(200);
    }

    public function test_pekerjaan_show_renders(): void
    {
        $pekerjaan = Pekerjaan::first();
        if ($pekerjaan) {
            $response = $this->actingAs($this->user)->get(route('pekerjaan.show', $pekerjaan));
            $response->assertStatus(200);
        }
    }

    public function test_pokjas_index_renders(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('pokjas.index'));
        $response->assertStatus(200);
    }

    public function test_pokjas_create_renders(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('pokjas.create'));
        $response->assertStatus(200);
    }

    public function test_pokjas_edit_renders(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $pokja = Pokja::first();
        if ($pokja) {
            $response = $this->actingAs($admin)->get(route('pokjas.edit', $pokja));
            $response->assertStatus(200);
        }
    }

    public function test_pokjas_update_with_members_synchronizes_correctly(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $pokja = Pokja::first();
        $members = User::where('id', '!=', $admin->id)->take(3)->get();

        if ($pokja && $members->count() >= 2) {
            $katim = $members->first();
            $anggota = $members->slice(1)->pluck('id')->toArray();
            $allSelected = array_merge([$katim->id], $anggota);

            $response = $this->actingAs($admin)->put(route('pokjas.update', $pokja), [
                'nama_pokja' => 'Tim Kerja Pengujian Integrasi',
                'kode_pokja' => $pokja->kode_pokja,
                'tipe' => $pokja->tipe,
                'deskripsi' => 'Pengujian kelola personil di form edit',
                'katim_id' => $katim->id,
                'manage_members_submitted' => 1,
                'anggota_ids' => $allSelected,
            ]);

            $response->assertRedirect(route('pokjas.index'));
            $this->assertDatabaseHas('pokjas', [
                'id' => $pokja->id,
                'nama_pokja' => 'Tim Kerja Pengujian Integrasi',
                'katim_id' => $katim->id,
            ]);
            $this->assertDatabaseHas('pokja_user', [
                'pokja_id' => $pokja->id,
                'user_id' => $katim->id,
                'peran' => 'ketua',
            ]);
            foreach ($anggota as $aid) {
                $this->assertDatabaseHas('pokja_user', [
                    'pokja_id' => $pokja->id,
                    'user_id' => $aid,
                    'peran' => 'anggota',
                ]);
            }
        }
    }

    public function test_users_index_renders(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertStatus(200);
    }

    public function test_rekap_penugasan_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('rekap-penugasan.index'));
        $response->assertStatus(200);
    }

    public function test_log_harian_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('log-harian.index'));
        $response->assertStatus(200);
    }
}
