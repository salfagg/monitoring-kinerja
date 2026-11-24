<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\Pokja;
use App\Models\RealisasiPekerjaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PekerjaanSayaTest extends TestCase
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
            'nama_pokja' => 'Tim Pembelajaran & Peningkatan Mutu',
            'kode_pokja' => 'TIM-03',
            'deskripsi' => 'Pokja Pembelajaran',
        ]);

        $this->katim = User::create([
            'name' => 'Endang Kurniawan',
            'nip' => '197505122000031001',
            'email' => 'katim@bgtk.go.id',
            'password' => bcrypt('password'),
            'role' => 'katim',
            'jabatan' => 'Ketua Tim Kerja',
            'pokja_id' => $this->pokja->id,
        ]);

        $this->pokja->update(['katim_id' => $this->katim->id]);

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
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'belum_dimulai',
        ]);

        $this->subJob = Pekerjaan::create([
            'parent_id' => $this->parentJob->id,
            'kode_tampilan' => '1.1',
            'judul' => 'Penyusunan Modul Ajar dan Instrumen Penilaian',
            'hasil_diharapkan' => '1 Dokumen Modul Ajar Lengkap',
            'target' => 1,
            'satuan' => 'Dokumen',
            'tenggat' => now()->addDays(15),
            'penanggung_jawab_id' => $this->pegawai->id,
            'pembuat_id' => $this->pegawai->id,
            'pokja_id' => $this->pokja->id,
            'status_persetujuan' => 'disetujui',
            'status_pelaksanaan' => 'dikerjakan',
        ]);
    }

    public function test_pekerjaan_saya_page_is_accessible(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('pekerjaan.saya'));

        $response->assertStatus(200);
        $response->assertSee('Tugas & Pekerjaan Saya');
        $response->assertSee('Penyusunan Modul Ajar dan Instrumen Penilaian');
    }

    public function test_pegawai_can_report_incremental_progress(): void
    {
        $response = $this->actingAs($this->pegawai)->post(route('pekerjaan.update-progres', $this->subJob), [
            'tanggal' => now()->toDateString(),
            'jenis_laporan' => 'kemajuan',
            'deskripsi_progres' => 'Telah menyelesaikan draf bab 1 dan bab 2 modul pelatihan.',
        ]);

        $response->assertSessionHas('success');

        // Laporan menunggu persetujuan Katim
        $realisasi = RealisasiPekerjaan::where('pekerjaan_id', $this->subJob->id)->latest()->first();
        $this->assertNotNull($realisasi);
        $this->assertEquals('menunggu_persetujuan', $realisasi->status_approval);
        $this->assertEquals('kemajuan', $realisasi->jenis_laporan);

        // Katim menyetujui (ACC) laporan kemajuan
        $approveResp = $this->actingAs($this->katim)->post(route('realisasi.approve', $realisasi));
        $approveResp->assertSessionHas('success');

        // Setelah di-ACC, status tetap dikerjakan
        $this->subJob->refresh();
        $this->assertEquals('dikerjakan', $this->subJob->status_pelaksanaan);
    }

    public function test_pegawai_can_complete_task_at_100_percent_and_it_submits_for_verification(): void
    {
        $response = $this->actingAs($this->pegawai)->post(route('pekerjaan.update-progres', $this->subJob), [
            'tanggal' => now()->toDateString(),
            'jenis_laporan' => 'penyelesaian',
            'deskripsi_progres' => 'Modul ajar telah selesai 100% dan instrumen penilaian lengkap.',
            'link_bukti' => 'https://drive.google.com/sample-modul-lengkap',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals('hasil_diajukan', $this->subJob->status_pelaksanaan);

        $realisasi = RealisasiPekerjaan::where('pekerjaan_id', $this->subJob->id)->latest()->first();
        $this->assertNotNull($realisasi);
        $this->assertEquals('menunggu_persetujuan', $realisasi->status_approval);
        $this->assertEquals('penyelesaian', $realisasi->jenis_laporan);

        // Katim memverifikasi & menyetujui laporan penyelesaian
        $approveResp = $this->actingAs($this->katim)->post(route('realisasi.approve', $realisasi));
        $approveResp->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals('selesai_terverifikasi', $this->subJob->status_pelaksanaan);

        $this->parentJob->refresh();
        $this->assertEquals('selesai_terverifikasi', $this->parentJob->status_pelaksanaan);

        $this->assertDatabaseHas('realisasi_pekerjaan', [
            'pekerjaan_id' => $this->subJob->id,
            'user_id' => $this->pegawai->id,
            'jenis_laporan' => 'penyelesaian',
            'status_approval' => 'disetujui',
        ]);
    }

    public function test_pegawai_can_start_task_with_mulai_kerja(): void
    {
        $this->subJob->update(['status_pelaksanaan' => 'belum_dimulai']);
        $this->assertEquals('belum_dimulai', $this->subJob->status_pelaksanaan);

        $response = $this->actingAs($this->pegawai)->post(route('pekerjaan.update-progres', $this->subJob), [
            'aksi_status' => 'mulai_kerja',
        ]);

        $response->assertSessionHas('success');
        $this->subJob->refresh();
        $this->assertEquals('dikerjakan', $this->subJob->status_pelaksanaan);
    }
}
