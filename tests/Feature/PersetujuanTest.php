<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersetujuanTest extends TestCase
{
    use RefreshDatabase;

    protected User $katim;
    protected User $pegawai;
    protected Pokja $pokja;
    protected Pekerjaan $parentJob;
    protected Pekerjaan $subJob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pokja = Pokja::create([
            'nama_pokja' => 'Tim Pembelajaran & Peningkatan Kompetensi',
            'kode_pokja' => 'TIM-03',
            'deskripsi' => 'Pokja Pembelajaran',
        ]);

        $this->katim = User::create([
            'name' => 'Endang Kurniawan',
            'nip' => '197505122000031001',
            'email' => 'katim@bgtk.go.id',
            'password' => bcrypt('password'),
            'role' => 'katim',
            'jabatan' => 'Ketua Tim Kerja Pembelajaran',
            'pokja_id' => $this->pokja->id,
        ]);

        $this->pegawai = User::create([
            'name' => 'Dedi Suhendar',
            'nip' => '198804202012011002',
            'email' => 'dedi@bgtk.go.id',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'jabatan' => 'Pamong Belajar Pertama',
            'pokja_id' => $this->pokja->id,
        ]);

        $this->parentJob = Pekerjaan::create([
            'parent_id' => null,
            'kode_tampilan' => '1',
            'judul' => 'Program Fasilitasi Guru Penggerak',
            'hasil_diharapkan' => '4 Angkatan Terfasilitasi',
            'target' => 4,
            'satuan' => 'Angkatan',
            'tenggat' => now()->addDays(60),
            'penanggung_jawab_id' => $this->katim->id,
            'pembuat_id' => $this->katim->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'progres' => 0,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);

        $this->subJob = Pekerjaan::create([
            'parent_id' => $this->parentJob->id,
            'kode_tampilan' => '1.1',
            'judul' => 'Penyusunan Modul Ajar dan Rubrik Penilaian',
            'hasil_diharapkan' => '1 Dokumen Modul Ajar',
            'target' => 1,
            'satuan' => 'Dokumen',
            'tenggat' => now()->addDays(15),
            'penanggung_jawab_id' => $this->pegawai->id,
            'pembuat_id' => $this->pegawai->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'progres' => 0,
            'status_persetujuan' => 'diajukan',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);
    }

    public function test_persetujuan_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->katim)->get(route('persetujuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Persetujuan & Verifikasi');
        $response->assertSee('Tahap 1: Persetujuan Struktur Rincian');
        $response->assertSee('Penyusunan Modul Ajar dan Rubrik Penilaian');
    }

    public function test_katim_can_approve_rincian_pekerjaan(): void
    {
        $response = $this->actingAs($this->katim)->post(route('pekerjaan.review-rincian', $this->subJob), [
            'aksi' => 'setujui',
            'catatan' => 'Rincian tugas disetujui, silakan mulai pengerjaan.',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals('disetujui', $this->subJob->status_persetujuan);
        $this->assertEquals('dikerjakan', $this->subJob->status_pelaksanaan);

        $this->assertDatabaseHas('riwayat_persetujuan_pekerjaan', [
            'pekerjaan_id' => $this->subJob->id,
            'user_id' => $this->katim->id,
            'jenis_aksi' => 'review_rincian_setujui',
            'status_baru' => 'disetujui',
        ]);
    }

    public function test_katim_can_request_revision_on_rincian(): void
    {
        $response = $this->actingAs($this->katim)->post(route('pekerjaan.review-rincian', $this->subJob), [
            'aksi' => 'minta_perbaikan',
            'catatan' => 'Tolong sesuaikan bobot dan rincian target hasil.',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals('perlu_perbaikan', $this->subJob->status_persetujuan);
        $this->assertEquals('Tolong sesuaikan bobot dan rincian target hasil.', $this->subJob->catatan_persetujuan);
    }

    public function test_katim_can_verify_and_accept_final_result(): void
    {
        // Approve rincian first
        $this->subJob->update([
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'hasil_diajukan',
            'progres' => 100,
        ]);

        RealisasiPekerjaan::create([
            'pekerjaan_id' => $this->subJob->id,
            'user_id' => $this->pegawai->id,
            'tanggal' => now()->toDateString(),
            'progres_capaian' => 100,
            'deskripsi_progres' => 'Pekerjaan modul telah rampung dan siap diperiksa.',
        ]);

        $response = $this->actingAs($this->katim)->post(route('pekerjaan.review-hasil', $this->subJob), [
            'aksi' => 'terima',
            'catatan' => 'Hasil sangat baik dan sesuai target IKU.',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals('selesai_terverifikasi', $this->subJob->status_pelaksanaan);
        $this->assertNotNull($this->subJob->tanggal_selesai_terverifikasi);

        $this->assertDatabaseHas('riwayat_persetujuan_pekerjaan', [
            'pekerjaan_id' => $this->subJob->id,
            'user_id' => $this->katim->id,
            'jenis_aksi' => 'review_hasil_terima',
            'status_baru' => 'selesai_terverifikasi',
        ]);
    }

    public function test_pegawai_cannot_approve_job(): void
    {
        $response = $this->actingAs($this->pegawai)->post(route('pekerjaan.review-rincian', $this->subJob), [
            'aksi' => 'setujui',
        ]);

        $response->assertStatus(403);
    }
}
