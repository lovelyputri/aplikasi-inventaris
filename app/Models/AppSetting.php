<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AppSetting extends Model
{
    protected $fillable = [
        'nama_aplikasi',
        'tagline',
        'nama_sekolah',
        'alamat_sekolah',
        'logo_path',
        'batas_stok_minimum',
        'versi_aplikasi',
    ];

    protected function casts(): array
    {
        return [
            'batas_stok_minimum' => 'integer',
        ];
    }

    /**
     * Retrieve the active application settings, falling back to defaults before migration.
     */
    public static function current(): self
    {
        $defaults = [
            'nama_aplikasi' => 'Inventaris Lab',
            'tagline' => 'Sistem Manajemen Aset Komputer',
            'nama_sekolah' => 'SMK Muhammadiyah 6 Rogojampi',
            'alamat_sekolah' => 'Jl. Raya Rogojampi, Banyuwangi, Jawa Timur',
            'logo_path' => 'images/logo-sekolah.png',
            'batas_stok_minimum' => 5,
            'versi_aplikasi' => '2.0.0',
        ];

        if (!Schema::hasTable('app_settings')) {
            return new self($defaults);
        }

        return self::query()->firstOrCreate([], $defaults);
    }

    public function resolvedLogoPath(): string
    {
        $path = $this->logo_path ?: 'images/logo-sekolah.png';

        if (file_exists(public_path($path))) {
            return $path;
        }

        return 'images/logo-sekolah.png';
    }
}
