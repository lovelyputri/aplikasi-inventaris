<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Barang;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $settings = AppSetting::current();
        $threshold = max(0, (int) $settings->batas_stok_minimum);

        $summary = Barang::query()
            ->selectRaw('COUNT(*) as total_jenis')
            ->selectRaw('COALESCE(SUM(jumlah_stok), 0) as total_stok')
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END), 0) as baik")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END), 0) as rusak_ringan")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END), 0) as rusak_berat")
            ->selectRaw('COALESCE(SUM(CASE WHEN jumlah_stok = 0 THEN 1 ELSE 0 END), 0) as stok_habis')
            ->selectRaw('COALESCE(SUM(CASE WHEN jumlah_stok > 0 AND jumlah_stok <= ? THEN 1 ELSE 0 END), 0) as stok_menipis', [$threshold])
            ->first();

        $totalJenis = (int) ($summary->total_jenis ?? 0);
        $baik = (int) ($summary->baik ?? 0);
        $rusakRingan = (int) ($summary->rusak_ringan ?? 0);
        $rusakBerat = (int) ($summary->rusak_berat ?? 0);

        $stats = [
            'total_jenis' => $totalJenis,
            'total_stok' => (int) ($summary->total_stok ?? 0),
            'baik' => $baik,
            'rusak_ringan' => $rusakRingan,
            'rusak_berat' => $rusakBerat,
            'stok_habis' => (int) ($summary->stok_habis ?? 0),
            'stok_menipis' => (int) ($summary->stok_menipis ?? 0),
            'baik_pct' => $totalJenis > 0 ? round(($baik / $totalJenis) * 100, 1) : 0,
            'ringan_pct' => $totalJenis > 0 ? round(($rusakRingan / $totalJenis) * 100, 1) : 0,
            'berat_pct' => $totalJenis > 0 ? round(($rusakBerat / $totalJenis) * 100, 1) : 0,
        ];

        $lowStockBarangs = Barang::query()
            ->where('jumlah_stok', '<=', $threshold)
            ->orderBy('jumlah_stok')
            ->orderBy('nama_barang')
            ->limit(6)
            ->get();

        $recentActivities = ActivityLog::query()
            ->latest()
            ->limit(6)
            ->get();

        $recentBarangs = Barang::query()
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'threshold' => $threshold,
            'lowStockBarangs' => $lowStockBarangs,
            'recentActivities' => $recentActivities,
            'recentBarangs' => $recentBarangs,
        ]);
    }
}
