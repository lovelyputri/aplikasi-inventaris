<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AppSetting::firstOrCreate([], [
            'nama_aplikasi' => 'Inventaris Lab',
            'tagline' => 'Sistem Manajemen Aset Komputer',
            'nama_sekolah' => 'SMK Muhammadiyah 6 Rogojampi',
            'alamat_sekolah' => 'Jl. Raya Rogojampi, Banyuwangi, Jawa Timur',
            'logo_path' => 'images/logo-sekolah.png',
            'batas_stok_minimum' => 5,
            'versi_aplikasi' => '2.0.0',
        ]);

        if (User::where('email', 'admin@smkmuh6.sch.id')->doesntExist()) {
            User::create([
                'name' => 'Administrator Lab',
                'email' => 'admin@smkmuh6.sch.id',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);
        }

        if (User::where('email', 'petugas@smkmuh6.sch.id')->doesntExist()) {
            User::create([
                'name' => 'Petugas Laboratorium',
                'email' => 'petugas@smkmuh6.sch.id',
                'role' => 'petugas',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
