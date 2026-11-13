<?php

namespace Database\Seeders;

use App\Models\Pokja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seeder Akun Pengguna BGTK Provinsi DKI Jakarta Tahun 2026
     * Berdasarkan SK Kepala Balai Nomor 30 Tahun 2026 tentang Susunan Tim Kerja
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $subbagUmum = Pokja::where('kode_pokja', 'SUBBAG-UMUM')->first();
        $tim1 = Pokja::where('kode_pokja', 'TIM-01')->first();
        $tim2 = Pokja::where('kode_pokja', 'TIM-02')->first();
        $tim3 = Pokja::where('kode_pokja', 'TIM-03')->first();
        $tim4 = Pokja::where('kode_pokja', 'TIM-04')->first();
        $tim5 = Pokja::where('kode_pokja', 'TIM-05')->first();

        $users = [
            // ==============================================================
            // ADMINISTRATOR SISTEM
            // ==============================================================
            [
                'name' => 'Administrator Sistem',
                'nip' => '198501012010011001',
                'email' => 'admin@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'admin',
                'jabatan' => 'Pranata Komputer Ahli Pertama',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567890',
            ],

            // ==============================================================
            // PIMPINAN BALAI (Role 1: Kepala Balai)
            // ==============================================================
            [
                'name' => 'Erfan Agus Munif, S.Pd., M.Pd.',
                'nip' => '197605152003121002',
                'email' => 'kabal@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'kabal',
                'jabatan' => 'Kepala Balai Guru dan Tenaga Kependidikan Provinsi DKI Jakarta',
                'pokja_id' => null,
                'nomor_hp' => '081234567891',
            ],

            // ==============================================================
            // KOORDINATOR JABATAN FUNGSIONAL (Role 3: Korjafung)
            // ==============================================================
            [
                'name' => 'Dr. Endah Ariani M, M.Pd.',
                'nip' => '196910232002122007',
                'email' => 'korjafung@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'korjafung',
                'jabatan' => 'Widyaiswara (Pembina Utama Muda, IV/c)',
                'pokja_id' => $tim2?->id,
                'nomor_hp' => '081234567893',
            ],

            // ==============================================================
            // I. SUBBAGIAN UMUM
            // ==============================================================
            // 1. Kepala Subbagian Umum (Role 2)
            [
                'name' => 'Dudi Ruhyadi Muharam, S.Si., M.Si.',
                'nip' => '197712112005011004',
                'email' => 'kasubag@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'kasubag',
                'jabatan' => 'Kepala Subbagian Umum (Pembina, IV/a)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567892',
            ],
            // 2. Bendahara Pengeluaran (Role 5: Korlayanan)
            [
                'name' => 'Immaculata Heni Herawati, S.Pd.',
                'nip' => '198712182020122008',
                'email' => 'heni.herawati@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'korlayanan',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Muda Tk 1, III/b)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567101',
            ],
            // 3. Keuangan
            [
                'name' => 'Okki Ristya Mutasi Ningsih, S.Pd.',
                'nip' => '199005172022032003',
                'email' => 'okki.ristya@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567102',
            ],
            [
                'name' => 'Novita Dewitri, A.Md.',
                'nip' => '198605192025212037',
                'email' => 'novita.dewitri@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Pengelola Layanan Operasional (Golongan VII)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567103',
            ],
            [
                'name' => 'Aviesina Dwi Kurnia, S.IP.',
                'nip' => null,
                'email' => 'aviesina.kurnia@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567104',
            ],
            [
                'name' => 'Adji Prasetya',
                'nip' => null,
                'email' => 'adji.prasetya@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567105',
            ],
            [
                'name' => 'Agus Sopiyan',
                'nip' => null,
                'email' => 'agus.sopiyan@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567106',
            ],
            // Organisasi dan SDM
            [
                'name' => 'Noni Lara Sestia, S.Psi.',
                'nip' => '198905232022032006',
                'email' => 'noni.sestia@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567107',
            ],
            [
                'name' => 'Lia Yulianti, S.Pd.',
                'nip' => null,
                'email' => 'lia.yulianti@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567108',
            ],
            [
                'name' => 'Annisa Alfiyaturrahmah, S.Psi.',
                'nip' => null,
                'email' => 'annisa.alfiyaturrahmah@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567109',
            ],
            [
                'name' => 'Fanny Nur Safitri',
                'nip' => null,
                'email' => 'fanny.safitri@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567110',
            ],
            // Administrasi dan Persuratan
            [
                'name' => 'Elvira Ratna Sari, S.Pd.',
                'nip' => '197201052005012001',
                'email' => 'elvira.sari@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Tk. I, III/d)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567111',
            ],
            [
                'name' => 'Rizka Maulidina Asti A, S.M.',
                'nip' => null,
                'email' => 'rizka.maulidina@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567112',
            ],
            [
                'name' => 'Fenny Amalia Zahra, S.Pd.',
                'nip' => null,
                'email' => 'fenny.zahra@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567113',
            ],
            // Sistem Informasi dan Jaringan
            [
                'name' => 'Muhammad Anwar, A.md.Kom',
                'nip' => '197908242025211035',
                'email' => 'muhammad.anwar@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Pengelola Layanan Operasional (Golongan VII)',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567114',
            ],
            [
                'name' => 'Eko Wahyu',
                'nip' => '198210312025211050',
                'email' => 'eko.wahyu@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Operator Layanan Operasional',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567115',
            ],
            [
                'name' => 'Moch Fadrila, S.M.',
                'nip' => null,
                'email' => 'moch.fadrila@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567116',
            ],
            [
                'name' => 'Haliza Moy',
                'nip' => null,
                'email' => 'haliza.moy@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567117',
            ],
            [
                'name' => 'Gilang Ramadhan, S.Kom.',
                'nip' => null,
                'email' => 'gilang.ramadhan@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567118',
            ],
            [
                'name' => 'Ilun Raafi Septian, S.Kom.',
                'nip' => null,
                'email' => 'ilun.septian@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567119',
            ],
            [
                'name' => 'Anggi Amalia, S.T.',
                'nip' => null,
                'email' => 'anggi.amalia@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567120',
            ],
            [
                'name' => 'Widaningsih',
                'nip' => null,
                'email' => 'widaningsih@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567121',
            ],
            [
                'name' => 'Zakaria',
                'nip' => null,
                'email' => 'zakaria@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567122',
            ],
            [
                'name' => 'Achmad Faisal',
                'nip' => null,
                'email' => 'achmad.faisal@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567123',
            ],
            [
                'name' => 'Amir Hamzah',
                'nip' => null,
                'email' => 'amir.hamzah@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $subbagUmum?->id,
                'nomor_hp' => '081234567124',
            ],

            // ==============================================================
            // II. TIM KERJA 1: PEMETAAN DAN EVALUASI
            // ==============================================================
            [
                'name' => 'Dr. Widiatmoko',
                'nip' => '197009212002121005',
                'email' => 'widiatmoko@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'katim',
                'jabatan' => 'Widyaiswara (Pembina Utama Muda, IV/c)',
                'pokja_id' => $tim1?->id,
                'nomor_hp' => '081234567894',
            ],
            [
                'name' => 'Dwi Yoga Peny Hadyanti, S.Pd., M.Pd.',
                'nip' => '197001082002122001',
                'email' => 'dwiyoga.hadyanti@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Utama Muda, IV/c)',
                'pokja_id' => $tim1?->id,
                'nomor_hp' => '081234567201',
            ],
            [
                'name' => 'Drs. Taufik Nugroho, M.Hum.',
                'nip' => '196710292002121001',
                'email' => 'taufik.nugroho@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Utama Muda, IV/c)',
                'pokja_id' => $tim1?->id,
                'nomor_hp' => '081234567202',
            ],
            [
                'name' => 'Dr. Virgo Rita Furyani, M.Ed.',
                'nip' => '197409122000122002',
                'email' => 'virgo.furyani@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Penata Tk. I, III/d)',
                'pokja_id' => $tim1?->id,
                'nomor_hp' => '081234567203',
            ],
            [
                'name' => 'Gunawan Widiyanto, M.Hum.',
                'nip' => '197112132002121001',
                'email' => 'gunawan.widiyanto@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Tk. I, IV/b)',
                'pokja_id' => $tim1?->id,
                'nomor_hp' => '081234567204',
            ],

            // ==============================================================
            // III. TIM KERJA 2: INOVASI
            // ==============================================================
            [
                'name' => 'Dr. Ahmad Ghozi',
                'nip' => '197305022002121004',
                'email' => 'ahmad.ghozy@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'katim',
                'jabatan' => 'Widyaiswara (Pembina Tk. I, IV/b)',
                'pokja_id' => $tim2?->id,
                'nomor_hp' => '081234567895',
            ],
            [
                'name' => 'Dwi Hadi Mulyaningsih, S.S., M.Pd.',
                'nip' => '198401302006042001',
                'email' => 'dwihadi.mulyaningsih@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Penata, III/c)',
                'pokja_id' => $tim2?->id,
                'nomor_hp' => '081234567301',
            ],
            [
                'name' => 'Anita Priyatmi, S.Pd., M.Ed.',
                'nip' => '197604182005012002',
                'email' => 'anita.priyatmi@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina, IV/a)',
                'pokja_id' => $tim2?->id,
                'nomor_hp' => '081234567302',
            ],
            [
                'name' => 'Triana Utary, S.Psi, M.Si.',
                'nip' => '198403172010032001',
                'email' => 'triana.utary@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata, III/c)',
                'pokja_id' => $tim2?->id,
                'nomor_hp' => '081234567303',
            ],

            // ==============================================================
            // IV. TIM KERJA 3: PEMBELAJARAN
            // ==============================================================
            [
                'name' => 'Endang Kurniawan, M.Pd.',
                'nip' => '197412072002121005',
                'email' => 'endang@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'katim',
                'jabatan' => 'Widyaiswara (Pembina Utama Muda, IV/c)',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567896',
            ],
            [
                'name' => 'Lia Herawaty, S.Pd., M.Pd.',
                'nip' => '197703242002122001',
                'email' => 'lia.herawaty@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Tk. I, IV/b)',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567401',
            ],
            [
                'name' => 'Siti Nurhayati, S.Pd., M.Pd.',
                'nip' => '197407252002122001',
                'email' => 'siti.nurhayati@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Tk. I, IV/b)',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567402',
            ],
            [
                'name' => 'Wahyuningrum, S.Pd., M.Pd.',
                'nip' => '197106252005012002',
                'email' => 'wahyuningrum@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Pembina Tk. I, IV/b)',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567403',
            ],
            [
                'name' => 'Pininto Sarwendah, S.S., M.A.',
                'nip' => '197207302005012001',
                'email' => 'pininto.sarwendah@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Penata Tk. I, III/d)',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567404',
            ],

            // ==============================================================
            // V. TIM KERJA 4: DATA INFORMASI DAN PUBLIKASI
            // ==============================================================
            [
                'name' => 'Widyatmo, M.Pd.',
                'nip' => '197209042003121001',
                'email' => 'widyatmo@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'katim',
                'jabatan' => 'Pengembang Teknologi Pembelajaran Ahli Muda (Pembina, IV/a)',
                'pokja_id' => $tim4?->id,
                'nomor_hp' => '081234567897',
            ],
            [
                'name' => 'Ati Rosidah, S.Ag., M.Pd.',
                'nip' => '197410052002122001',
                'email' => 'ati.rosidah@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Pengembang Teknologi Pembelajaran Ahli Muda (Penata Tk. I, III/d)',
                'pokja_id' => $tim4?->id,
                'nomor_hp' => '081234567501',
            ],
            [
                'name' => 'Desramanita Ayuning B., S.Psi',
                'nip' => '199612292022032023',
                'email' => 'desramanita.ayuning@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
                'pokja_id' => $tim4?->id,
                'nomor_hp' => '081234567502',
            ],
            [
                'name' => 'Bagus Aji Saputro, S.T.',
                'nip' => '199705132022031009',
                'email' => 'bagusaji.saputro@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Penata Muda, III/a)',
                'pokja_id' => $tim4?->id,
                'nomor_hp' => '081234567503',
            ],
            [
                'name' => 'Kurnia Yulianto',
                'nip' => null,
                'email' => 'kurnia.yulianto@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $tim4?->id,
                'nomor_hp' => '081234567504',
            ],

            // ==============================================================
            // VI. TIM KERJA 5: KEMITRAAN DAN FASILITASI
            // ==============================================================
            [
                'name' => 'Ikrar Triardhana, S.E., M.Si',
                'nip' => '197701022002121002',
                'email' => 'ikrar@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'katim',
                'jabatan' => 'Arsiparis Muda (Pembina, IV/a)',
                'pokja_id' => $tim5?->id,
                'nomor_hp' => '081234567898',
            ],
            [
                'name' => 'Dwi Puspitasari, S.Pd., M.Pd.',
                'nip' => '197905142005012002',
                'email' => 'dwi.puspitasari@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Penata Tk. I, III/d)',
                'pokja_id' => $tim5?->id,
                'nomor_hp' => '081234567601',
            ],
            [
                'name' => 'Mulyawarni, S.S., M.Pd.',
                'nip' => '198303032008012012',
                'email' => 'mulyawarni@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Widyaiswara (Penata Tk. I, III/d)',
                'pokja_id' => $tim5?->id,
                'nomor_hp' => '081234567602',
            ],
            [
                'name' => 'Muhammad Faisal, S.E., M.Si.',
                'nip' => '198304082008121002',
                'email' => 'muhammad.faisal@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Penelaah Teknis Kebijakan (Pembina, IV/a)',
                'pokja_id' => $tim5?->id,
                'nomor_hp' => '081234567603',
            ],
            [
                'name' => 'Iin Sodikin',
                'nip' => null,
                'email' => 'iin.sodikin@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Tenaga Alih Daya',
                'pokja_id' => $tim5?->id,
                'nomor_hp' => '081234567604',
            ],

            // ==============================================================
            // ALIAS / AKUN DEMO SINGKAT (Untuk kemudahan login demo)
            // ==============================================================
            [
                'name' => 'Pegawai Pelaksana BGTK (Akun Demo)',
                'nip' => '199511102020121005',
                'email' => 'pegawai@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Pelaksana Tugas',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567999',
            ],
            [
                'name' => 'Verifikator Tim Kerja (Akun Demo)',
                'nip' => '199004252015032004',
                'email' => 'pokja@bgtk.go.id',
                'password' => $defaultPassword,
                'role' => 'pegawai',
                'jabatan' => 'Verifikator Pelaporan',
                'pokja_id' => $tim3?->id,
                'nomor_hp' => '081234567998',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Tautkan Katim pada masing-masing Tim Kerja resmi
        $widiatmoko = User::where('email', 'widiatmoko@bgtk.go.id')->first();
        if ($tim1 && $widiatmoko) $tim1->update(['katim_id' => $widiatmoko->id]);

        $ghozy = User::where('email', 'ahmad.ghozy@bgtk.go.id')->first();
        if ($tim2 && $ghozy) $tim2->update(['katim_id' => $ghozy->id]);

        $endang = User::where('email', 'endang@bgtk.go.id')->first();
        if ($tim3 && $endang) $tim3->update(['katim_id' => $endang->id]);

        $widyatmo = User::where('email', 'widyatmo@bgtk.go.id')->first();
        if ($tim4 && $widyatmo) $tim4->update(['katim_id' => $widyatmo->id]);

        $ikrar = User::where('email', 'ikrar@bgtk.go.id')->first();
        if ($tim5 && $ikrar) $tim5->update(['katim_id' => $ikrar->id]);
    }
}
