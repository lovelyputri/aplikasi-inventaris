<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'admin'): User
    {
        return User::factory()->create([
            'role' => $role,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/barangs')->assertRedirect(route('login'));
        $this->get('/laporan')->assertRedirect(route('login'));
        $this->get('/riwayat')->assertRedirect(route('login'));
        $this->get('/pengaturan')->assertRedirect(route('login'));
    }

    public function test_authenticated_dashboard_displays_stats_and_alerts(): void
    {
        $user = $this->createUser('admin');
        AppSetting::current();

        Barang::create([
            'kode_barang' => 'LAB-001',
            'nama_barang' => 'Monitor LG 24 Inch',
            'jumlah_stok' => 12,
            'kondisi' => 'Baik',
        ]);
        Barang::create([
            'kode_barang' => 'LAB-002',
            'nama_barang' => 'Keyboard Logitech',
            'jumlah_stok' => 2,
            'kondisi' => 'Rusak Ringan',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Inventaris Lab');
        $response->assertSee('SMK Muhammadiyah 6 Rogojampi');
        $response->assertSee('Keyboard Logitech');
        $response->assertSee('Sisa 2 unit');
    }

    public function test_pagination_renders_only_over_ten_items_and_preserves_filters(): void
    {
        $user = $this->createUser('admin');

        for ($i = 1; $i <= 15; $i++) {
            Barang::create([
                'kode_barang' => sprintf('LAB-%03d', $i),
                'nama_barang' => "Komputer Lab {$i}",
                'jumlah_stok' => 6,
                'kondisi' => $i <= 12 ? 'Baik' : 'Rusak Ringan',
            ]);
        }

        $pageOne = $this->actingAs($user)->get('/barangs?kondisi=Baik&search=Komputer');
        $pageOne->assertStatus(200);
        $pageOne->assertSee('Navigasi Halaman');
        $pageOne->assertSee('Menampilkan');
        $pageOne->assertSee('search=Komputer');
        $pageOne->assertSee('kondisi=Baik');

        $filteredSingle = $this->actingAs($user)->get('/barangs?kondisi=Rusak+Ringan');
        $filteredSingle->assertStatus(200);
        $filteredSingle->assertDontSee('Navigasi Halaman');
    }

    public function test_crud_records_activity_logs_and_detail_qr_works(): void
    {
        $user = $this->createUser('admin');

        $this->actingAs($user)->post(route('barangs.store'), [
            'kode_barang' => 'LAB-050',
            'nama_barang' => 'Switch Cisco 24 Port',
            'jumlah_stok' => 4,
            'kondisi' => 'Baik',
            'deskripsi' => 'Switch inti rak server Lab 1',
        ])->assertRedirect(route('barangs.index'));

        $barang = Barang::where('kode_barang', 'LAB-050')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'aktivitas' => 'Tambah Barang',
            'kode_barang' => 'LAB-050',
            'user_id' => $user->id,
        ]);

        $show = $this->actingAs($user)->get(route('barangs.show', $barang));
        $show->assertStatus(200);
        $show->assertSee('Switch inti rak server Lab 1');
        $show->assertSee('WIB');

        $this->actingAs($user)->get(route('barangs.qr', $barang))->assertStatus(200);

        $this->actingAs($user)->put(route('barangs.update', $barang), [
            'kode_barang' => 'LAB-050',
            'nama_barang' => 'Switch Cisco 24 Port',
            'jumlah_stok' => 2,
            'kondisi' => 'Rusak Ringan',
            'deskripsi' => 'Port 12 bermasalah',
        ])->assertRedirect(route('barangs.index'));

        $this->actingAs($user)->delete(route('barangs.destroy', $barang))
            ->assertRedirect(route('barangs.index'));

        $this->assertSame(3, ActivityLog::where('kode_barang', 'LAB-050')->count());
    }

    public function test_report_exports_and_admin_only_app_settings(): void
    {
        $admin = $this->createUser('admin');
        $petugas = $this->createUser('petugas');

        Barang::create([
            'kode_barang' => 'LAB-099',
            'nama_barang' => 'Proyektor Epson',
            'jumlah_stok' => 3,
            'kondisi' => 'Baik',
        ]);

        $pdf = $this->actingAs($petugas)->get(route('laporan.export.pdf'));
        $pdf->assertStatus(200);
        $this->assertStringStartsWith('%PDF-1.4', $pdf->getContent());

        $xlsx = $this->actingAs($petugas)->get(route('laporan.export.excel'));
        $xlsx->assertStatus(200);
        $this->assertStringStartsWith('PK', $xlsx->getContent());

        $this->actingAs($petugas)->put(route('pengaturan.app.update'), [
            'nama_aplikasi' => 'Hack',
            'tagline' => 'Hack',
            'nama_sekolah' => 'Hack',
            'batas_stok_minimum' => 1,
            'versi_aplikasi' => '9.9',
        ])->assertStatus(403);

        $this->actingAs($admin)->put(route('pengaturan.app.update'), [
            'nama_aplikasi' => 'Inventaris Lab',
            'tagline' => 'Sistem Manajemen Aset Komputer',
            'nama_sekolah' => 'SMK Muhammadiyah 6 Rogojampi',
            'alamat_sekolah' => 'Rogojampi, Banyuwangi',
            'batas_stok_minimum' => 5,
            'versi_aplikasi' => '2.0.0',
        ])->assertRedirect(route('pengaturan.index'));
    }
}
