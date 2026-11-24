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

        $this->user = User::factory()->create([
            'role' => 'katim',
            'jabatan' => 'Ketua Tim Kerja',
        ]);

        $this->pokja = Pokja::create([
            'nama_pokja' => 'Tim Kerja Transformasi Digital',
            'kode_pokja' => 'TIM-01',
            'katim_id' => $this->user->id,
            'keterangan' => 'Pokja pengujian hierarki',
        ]);
    }

    public function test_user_can_access_pekerjaan_index_and_see_tree_view(): void
    {
        $response = $this->actingAs($this->user)->get(route('pekerjaan.index'));

        $response->assertStatus(200);
        $response->assertSee('Total Penugasan Balai');
    }

    public function test_user_can_view_pekerjaan_detail_with_hierarchy_data(): void
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
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);

        $response = $this->actingAs($this->user)->get(route('pekerjaan.show', $pekerjaan));

        $response->assertStatus(200);
        $response->assertSee($pekerjaan->kode_tampilan);
        $response->assertSee($pekerjaan->judul);
    }

    public function test_status_cascade_from_children(): void
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
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);

        // Buat anak 1: selesai_terverifikasi
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
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'selesai_terverifikasi',
        ]);

        // Buat anak 2: dikerjakan
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
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);

        $parent->recalculateProgresFromChildren();
        $parent->refresh();

        $this->assertEquals('dikerjakan', $parent->status_pelaksanaan);

        // Jika anak 2 juga selesai terverifikasi, maka induk otomatis selesai terverifikasi
        $child2->update(['status_pelaksanaan' => 'selesai_terverifikasi']);
        $parent->recalculateProgresFromChildren();
        $parent->refresh();

        $this->assertEquals('selesai_terverifikasi', $parent->status_pelaksanaan);
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
