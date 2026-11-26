<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $aktivitasFilter = trim((string) $request->query('aktivitas', ''));

        $logs = ActivityLog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->when(in_array($aktivitasFilter, ActivityLog::AKTIVITAS_TYPES, true), function ($query) use ($aktivitasFilter) {
                $query->where('aktivitas', $aktivitasFilter);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('riwayat.index', [
            'logs' => $logs,
            'search' => $search,
            'aktivitasFilter' => $aktivitasFilter,
            'aktivitasTypes' => ActivityLog::AKTIVITAS_TYPES,
        ]);
    }
}
