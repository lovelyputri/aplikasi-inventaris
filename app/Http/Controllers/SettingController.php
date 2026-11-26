<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('pengaturan.index', [
            'user' => $this->resolveOperator(),
            'settings' => AppSetting::current(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $this->resolveOperator();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Nama pengelola wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        ActivityLog::record(
            aktivitas: 'Pengaturan',
            namaBarang: $user->name,
            kodeBarang: 'PROFIL',
            barangId: null,
            keterangan: 'Pembaruan profil pengelola laboratorium',
            actor: $user
        );

        return redirect()
            ->route('pengaturan.index')
            ->with('success', 'Profil pengelola berhasil diperbarui.');
    }

    public function updateApp(Request $request): RedirectResponse
    {
        $settings = AppSetting::current();

        $validated = $request->validate([
            'nama_aplikasi' => ['required', 'string', 'max:100'],
            'tagline' => ['required', 'string', 'max:150'],
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
            'batas_stok_minimum' => ['required', 'integer', 'min:0', 'max:1000'],
            'versi_aplikasi' => ['required', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ], [
            'nama_aplikasi.required' => 'Nama aplikasi wajib diisi.',
            'tagline.required' => 'Tagline aplikasi wajib diisi.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'batas_stok_minimum.required' => 'Batas stok minimum wajib diisi.',
            'batas_stok_minimum.integer' => 'Batas stok minimum harus berupa angka bulat.',
            'batas_stok_minimum.min' => 'Batas stok minimum minimal 0.',
            'versi_aplikasi.required' => 'Versi aplikasi wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.mimes' => 'Format logo harus PNG, JPG, JPEG, atau WEBP.',
            'logo.max' => 'Ukuran file logo maksimal 2 MB.',
        ]);

        $logoPath = $settings->logo_path ?: 'images/logo-sekolah.png';

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $destinationDir = public_path('images');
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = 'logo-sekolah-' . time() . '.' . $ext;
            $file->move($destinationDir, $filename);
            $logoPath = 'images/' . $filename;
        }

        $settings->update([
            'nama_aplikasi' => $validated['nama_aplikasi'],
            'tagline' => $validated['tagline'],
            'nama_sekolah' => $validated['nama_sekolah'],
            'alamat_sekolah' => $validated['alamat_sekolah'] ?? null,
            'batas_stok_minimum' => (int) $validated['batas_stok_minimum'],
            'versi_aplikasi' => $validated['versi_aplikasi'],
            'logo_path' => $logoPath,
        ]);

        ActivityLog::record(
            aktivitas: 'Pengaturan',
            namaBarang: $settings->nama_sekolah,
            kodeBarang: 'APP-CFG',
            barangId: null,
            keterangan: "Pembaruan identitas & batas stok min: {$settings->batas_stok_minimum}"
        );

        return redirect()
            ->route('pengaturan.index')
            ->with('success', 'Pengaturan sekolah dan aplikasi berhasil disimpan.');
    }

    private function resolveOperator(): User
    {
        return User::query()->firstOrCreate(
            ['email' => 'admin@smkmuh6.sch.id'],
            [
                'name' => 'Administrator Lab',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
