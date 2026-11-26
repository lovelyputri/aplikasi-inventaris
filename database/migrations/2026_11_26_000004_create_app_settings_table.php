<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aplikasi')->default('Inventaris Lab');
            $table->string('tagline')->default('Sistem Manajemen Aset Komputer');
            $table->string('nama_sekolah')->default('SMK Muhammadiyah 6 Rogojampi');
            $table->string('alamat_sekolah')->nullable()->default('Jl. Raya Rogojampi, Banyuwangi, Jawa Timur');
            $table->string('logo_path')->default('images/logo-sekolah.png');
            $table->integer('batas_stok_minimum')->default(5);
            $table->string('versi_aplikasi')->default('2.0.0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
