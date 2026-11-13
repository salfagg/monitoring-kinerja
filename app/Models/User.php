<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'role',
        'jabatan',
        'pokja_id',
        'nomor_hp',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function pokja(): BelongsTo
    {
        return $this->belongsTo(Pokja::class, 'pokja_id');
    }

    public function pokjas(): BelongsToMany
    {
        return $this->belongsToMany(Pokja::class, 'pokja_user')->withPivot('peran')->withTimestamps();
    }

    public function skpTahunan(): HasMany
    {
        return $this->hasMany(SkpTahunan::class, 'user_id');
    }

    public function skpTargetBulanan(): HasMany
    {
        return $this->hasMany(SkpTargetBulanan::class, 'user_id');
    }

    public function rencanaMingguan(): HasMany
    {
        return $this->hasMany(RencanaMingguan::class, 'user_id');
    }

    public function laporanKinerja(): HasMany
    {
        return $this->hasMany(LaporanKinerja::class, 'user_id');
    }

    public function logHarian(): HasMany
    {
        return $this->hasMany(LogHarian::class, 'user_id');
    }

    public function pekerjaanDitugaskan(): BelongsToMany
    {
        return $this->belongsToMany(Pekerjaan::class, 'pekerjaan_pelaksana', 'user_id', 'pekerjaan_id')
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function pekerjaanSebagaiPj(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'penanggung_jawab_id');
    }

    public function pekerjaanDibuat(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'pembuat_id');
    }

    // 7 Official Roles BGTK 2026
    public const ROLE_KABAL = 'kabal';
    public const ROLE_KASUBAG = 'kasubag';
    public const ROLE_KORJAFUNG = 'korjafung';
    public const ROLE_KATIM = 'katim';
    public const ROLE_KORLAYANAN = 'korlayanan';
    public const ROLE_PEGAWAI = 'pegawai';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_KABAL => 'Kepala Balai',
        self::ROLE_KASUBAG => 'Kepala Subbagian Umum',
        self::ROLE_KORJAFUNG => 'Koordinator Jabatan Fungsional',
        self::ROLE_KATIM => 'Ketua Tim Kerja / Ketua Pokja',
        self::ROLE_KORLAYANAN => 'Koordinator Bidang / Layanan',
        self::ROLE_PEGAWAI => 'Pegawai',
        self::ROLE_ADMIN => 'Administrator',
    ];

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKabal(): bool
    {
        return $this->role === self::ROLE_KABAL;
    }

    public function isKasubag(): bool
    {
        return $this->role === self::ROLE_KASUBAG;
    }

    public function isKorjafung(): bool
    {
        return $this->role === self::ROLE_KORJAFUNG;
    }

    public function isKatim(): bool
    {
        return in_array($this->role, [self::ROLE_KATIM, 'katim_korjafung'], true);
    }

    public function isKorlayanan(): bool
    {
        return in_array($this->role, [self::ROLE_KORLAYANAN, 'koordinator_layanan'], true);
    }

    public function isPegawai(): bool
    {
        return in_array($this->role, [self::ROLE_PEGAWAI, 'tim_pokja'], true);
    }

    public function isTimPokja(): bool
    {
        return $this->role === 'tim_pokja';
    }

    // Role Capabilities
    /**
     * Hak membuat pekerjaan utama, membagi tugas, dan menetapkan target & tenggat.
     * Dimiliki oleh: Katim, Koordinator Layanan, Koordinator JF, Kasubag, Kabal, Admin.
     */
    public function canManagePekerjaan(): bool
    {
        return $this->isKatim() || $this->isKorlayanan() || $this->isKorjafung() || $this->isKasubag() || $this->isKabal() || $this->isAdmin();
    }

    /**
     * Hak menyetujui struktur rincian subpekerjaan & memverifikasi penerimaan hasil akhir.
     * Dimiliki oleh: Katim, Koordinator Layanan, Koordinator JF, Kasubag, Kabal, Admin.
     */
    public function canReviewPekerjaan(): bool
    {
        return $this->canManagePekerjaan();
    }

    /**
     * Hak memantau, mengoordinasikan lingkup penugasan, dan memberikan evaluasi.
     * Dimiliki oleh: Koordinator Jabatan Fungsional, serta pimpinan/katim.
     */
    public function canEvaluatePekerjaan(): bool
    {
        return $this->isKorjafung() || $this->canManagePekerjaan();
    }

    /**
     * Label resmi Role untuk antarmuka.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? str_replace('_', ' ', ucwords($this->role));
    }
}
