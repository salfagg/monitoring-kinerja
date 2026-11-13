<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PekerjaanHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Pokja $pokja;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pokja = Pokja::create([
            'nama_pokja' => 'Tim Pembelajaran',
            'kode_pokja' => 'TIM-03',
            'deskripsi' => 'Deskripsi Pokja',
        ]);

        $this->user = User::create([
            'name' => 'Dr. Widiatmoko',
            'nip' => '197001011995011001',
            'email' => 'widiatmoko@bgtk.go.id',
            'password' => bcrypt('password'),
            'role' => 'katim',
            'jabatan' => 'Ketua Tim Kerja',
            'pokja_id' => $this->pokja->id,
        ]);
    }

    public function test_hierarchy_tree_page_is_accessible_by_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Hierarki Pekerjaan');
    }

    public function test_detail_pekerjaan_loads_with_correct_breadcrumbs(): void
    {
        $pekerjaan = Pekerjaan::create([
            'kode_tampilan' => '1',
            'judul' => 'Pekerjaan Utama Uji Coba',
            'hasil_diharapkan' => 'Laporan Hasil',
            'target' => 1,
            'satuan' => 'Kegiatan',
            'tenggat' => now()->addDays(30),
            'penanggung_jawab_id' => $this->user->id,
            'pembuat_id' => $this->user->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
            'progres' => 0,
        ]);

        $response = $this->actingAs($this->user)->get(route('pekerjaan.show', $pekerjaan));

        $response->assertStatus(200);
        $response->assertSee($pekerjaan->kode_tampilan);
        $response->assertSee($pekerjaan->judul);
    }

    public function test_weighted_progress_recalculation_from_approved_children(): void
    {
        // Buat parent
        $parent = Pekerjaan::create([
            'kode_tampilan' => '99',
            'judul' => 'Pekerjaan Induk Uji Coba',
            'hasil_diharapkan' => 'Output Induk',
            'target' => 1,
            'satuan' => 'Kegiatan',
            'tenggat' => now()->addDays(30),
            'penanggung_jawab_id' => $this->user->id,
            'pembuat_id' => $this->user->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
            'progres' => 0,
        ]);

        // Buat anak 1: bobot 60, progres 100, disetujui -> kontribusi = 60 * 100 = 6000
        $child1 = Pekerjaan::create([
            'parent_id' => $parent->id,
            'kode_tampilan' => '99.1',
            'judul' => 'Sub 1',
            'hasil_diharapkan' => 'Output 1',
            'target' => 1,
            'satuan' => 'Dokumen',
            'tenggat' => now()->addDays(20),
            'penanggung_jawab_id' => $this->user->id,
            'pembuat_id' => $this->user->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 60,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'selesai_terverifikasi',
            'progres' => 100,
        ]);

        // Buat anak 2: bobot 40, progres 50, disetujui -> kontribusi = 40 * 50 = 2000
        // Total kontribusi = 8000 / 100 = 80%
        $child2 = Pekerjaan::create([
            'parent_id' => $parent->id,
            'kode_tampilan' => '99.2',
            'judul' => 'Sub 2',
            'hasil_diharapkan' => 'Output 2',
            'target' => 1,
            'satuan' => 'Laporan',
            'tenggat' => now()->addDays(25),
            'penanggung_jawab_id' => $this->user->id,
            'pembuat_id' => $this->user->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 40,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
            'progres' => 50,
        ]);

        $parent->recalculateProgresFromChildren();
        $parent->refresh();

        $this->assertEquals(80.0, (float) $parent->progres);
        $this->assertEquals('dikerjakan', $parent->status_pelaksanaan);
    }

    public function test_can_store_root_job_without_parent_id(): void
    {
        $response = $this->actingAs($this->user)->post(route('pekerjaan.store'), [
            'kode_tampilan' => '10',
            'judul' => 'Pekerjaan Utama Baru',
            'deskripsi' => 'Deskripsi pekerjaan utama',
            'hasil_diharapkan' => 'Laporan Utama',
            'target' => 1,
            'satuan' => 'Kegiatan',
            'tenggat' => now()->addDays(30)->format('Y-m-d'),
            'penanggung_jawab_id' => $this->user->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'status_persetujuan' => 'diajukan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pekerjaan', [
            'kode_tampilan' => '10',
            'judul' => 'Pekerjaan Utama Baru',
            'parent_id' => null,
            'status_persetujuan' => 'disetujui', // Otomatis disetujui karena dibuat Katim
        ]);
    }
}
