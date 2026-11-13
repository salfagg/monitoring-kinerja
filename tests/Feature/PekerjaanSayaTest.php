<?php

namespace Tests\Feature;

use App\Models\Pekerjaan;
use App\Models\Pokja;
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
            'judul' => 'Penyusunan Modul Ajar dan Instrumen Penilaian',
            'hasil_diharapkan' => '1 Dokumen Modul Ajar Lengkap',
            'target' => 1,
            'satuan' => 'Dokumen',
            'tenggat' => now()->addDays(15),
            'penanggung_jawab_id' => $this->pegawai->id,
            'pembuat_id' => $this->pegawai->id,
            'pokja_id' => $this->pokja->id,
            'bobot' => 100,
            'progres' => 0,
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
            'progres_capaian' => 50,
            'deskripsi_progres' => 'Telah menyelesaikan draf bab 1 dan bab 2 modul pelatihan.',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals(50.0, (float) $this->subJob->progres);
        $this->assertEquals('dikerjakan', $this->subJob->status_pelaksanaan);

        // Pastikan induk ikut naik progresnya
        $this->parentJob->refresh();
        $this->assertEquals(50.0, (float) $this->parentJob->progres);
    }

    public function test_pegawai_can_complete_task_at_100_percent_and_it_submits_for_verification(): void
    {
        $response = $this->actingAs($this->pegawai)->post(route('pekerjaan.update-progres', $this->subJob), [
            'tanggal' => now()->toDateString(),
            'progres_capaian' => 100,
            'deskripsi_progres' => 'Modul ajar telah selesai 100% dan instrumen penilaian lengkap.',
            'link_bukti' => 'https://drive.google.com/sample-modul-lengkap',
        ]);

        $response->assertSessionHas('success');

        $this->subJob->refresh();
        $this->assertEquals(100.0, (float) $this->subJob->progres);
        // Otomatis pindah ke status hasil_diajukan untuk Tahap 2 verifikasi Katim
        $this->assertEquals('hasil_diajukan', $this->subJob->status_pelaksanaan);

        $this->assertDatabaseHas('realisasi_pekerjaan', [
            'pekerjaan_id' => $this->subJob->id,
            'user_id' => $this->pegawai->id,
            'progres_capaian' => 100,
        ]);
    }
}
