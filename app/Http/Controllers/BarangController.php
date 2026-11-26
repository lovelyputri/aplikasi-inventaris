<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $settings = AppSetting::current();
        $threshold = max(0, (int) $settings->batas_stok_minimum);

        $search = trim((string) $request->query('search', ''));
        $kondisiFilter = trim((string) $request->query('kondisi', ''));
        $stokFilter = trim((string) $request->query('stok', ''));

        $barangs = Barang::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            })
            ->when(in_array($kondisiFilter, Barang::KONDISI_OPTIONS, true), function ($query) use ($kondisiFilter) {
                $query->where('kondisi', $kondisiFilter);
            })
            ->when($stokFilter === 'habis', fn ($query) => $query->where('jumlah_stok', '<=', 0))
            ->when($stokFilter === 'menipis', fn ($query) => $query->where('jumlah_stok', '>', 0)->where('jumlah_stok', '<=', $threshold))
            ->when($stokFilter === 'perhatian', fn ($query) => $query->where('jumlah_stok', '<=', $threshold))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = Barang::query()
            ->selectRaw('COUNT(*) as total_jenis')
            ->selectRaw('COALESCE(SUM(jumlah_stok), 0) as total_stok')
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END), 0) as baik")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END), 0) as rusak_ringan")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END), 0) as rusak_berat")
            ->selectRaw('COALESCE(SUM(CASE WHEN jumlah_stok <= ? THEN 1 ELSE 0 END), 0) as perlu_perhatian', [$threshold])
            ->first();

        $stats = [
            'total_jenis' => (int) ($summary->total_jenis ?? 0),
            'total_stok' => (int) ($summary->total_stok ?? 0),
            'baik' => (int) ($summary->baik ?? 0),
            'rusak_ringan' => (int) ($summary->rusak_ringan ?? 0),
            'rusak_berat' => (int) ($summary->rusak_berat ?? 0),
            'perlu_perhatian' => (int) ($summary->perlu_perhatian ?? 0),
        ];

        return view('barangs.index', [
            'barangs' => $barangs,
            'search' => $search,
            'kondisiFilter' => $kondisiFilter,
            'stokFilter' => $stokFilter,
            'kondisiOptions' => Barang::KONDISI_OPTIONS,
            'threshold' => $threshold,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('barangs.create', [
            'kondisiOptions' => Barang::KONDISI_OPTIONS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBarang($request);

        $barang = Barang::create($validated);

        ActivityLog::record(
            aktivitas: 'Tambah Barang',
            namaBarang: $barang->nama_barang,
            kodeBarang: $barang->kode_barang,
            barangId: $barang->id,
            keterangan: "Stok awal: {$barang->jumlah_stok} unit | Kondisi: {$barang->kondisi}",
            actor: $request->user()
        );

        return redirect()
            ->route('barangs.index')
            ->with('success', "Barang \"{$barang->nama_barang}\" berhasil ditambahkan ke inventaris.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang): View
    {
        $threshold = max(0, (int) AppSetting::current()->batas_stok_minimum);
        $logs = ActivityLog::query()
            ->where('barang_id', $barang->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('barangs.show', [
            'barang' => $barang,
            'threshold' => $threshold,
            'logs' => $logs,
            'qrUrl' => route('barangs.show', $barang),
        ]);
    }

    /**
     * Display printable QR code label for the specified item.
     */
    public function qr(Barang $barang): View
    {
        return view('barangs.qr', [
            'barang' => $barang,
            'settings' => AppSetting::current(),
            'qrUrl' => route('barangs.show', $barang),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang): View
    {
        return view('barangs.edit', [
            'barang' => $barang,
            'kondisiOptions' => Barang::KONDISI_OPTIONS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $validated = $this->validateBarang($request, $barang);

        $changes = [];
        if ($barang->jumlah_stok !== (int) $validated['jumlah_stok']) {
            $changes[] = "Stok: {$barang->jumlah_stok} → {$validated['jumlah_stok']}";
        }
        if ($barang->kondisi !== $validated['kondisi']) {
            $changes[] = "Kondisi: {$barang->kondisi} → {$validated['kondisi']}";
        }
        if ($barang->nama_barang !== $validated['nama_barang']) {
            $changes[] = "Nama diubah";
        }
        if ($barang->kode_barang !== $validated['kode_barang']) {
            $changes[] = "Kode: {$barang->kode_barang} → {$validated['kode_barang']}";
        }

        $barang->update($validated);

        ActivityLog::record(
            aktivitas: 'Perbarui Barang',
            namaBarang: $barang->nama_barang,
            kodeBarang: $barang->kode_barang,
            barangId: $barang->id,
            keterangan: $changes !== [] ? implode(' | ', $changes) : 'Pembaruan detail barang',
            actor: $request->user()
        );

        return redirect()
            ->route('barangs.index')
            ->with('success', "Data barang \"{$barang->nama_barang}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Barang $barang): RedirectResponse
    {
        $barangId = $barang->id;
        $kodeBarang = $barang->kode_barang;
        $namaBarang = $barang->nama_barang;
        $stok = $barang->jumlah_stok;
        $kondisi = $barang->kondisi;

        $barang->delete();

        ActivityLog::record(
            aktivitas: 'Hapus Barang',
            namaBarang: $namaBarang,
            kodeBarang: $kodeBarang,
            barangId: $barangId,
            keterangan: "Stok terakhir: {$stok} unit | Kondisi: {$kondisi}",
            actor: $request->user()
        );

        return redirect()
            ->route('barangs.index')
            ->with('success', "Barang \"{$namaBarang}\" berhasil dihapus dari inventaris.");
    }

    /**
     * Validate request input for creating or updating an inventory item.
     *
     * @return array<string, mixed>
     */
    private function validateBarang(Request $request, ?Barang $barang = null): array
    {
        return $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')->ignore($barang?->id),
            ],
            'nama_barang' => ['required', 'string', 'max:255'],
            'jumlah_stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', Rule::in(Barang::KONDISI_OPTIONS)],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.max' => 'Kode barang maksimal 50 karakter.',
            'kode_barang.unique' => 'Kode barang sudah terdaftar, silakan gunakan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max' => 'Nama barang maksimal 255 karakter.',
            'jumlah_stok.required' => 'Jumlah stok wajib diisi.',
            'jumlah_stok.integer' => 'Jumlah stok harus berupa angka bulat.',
            'jumlah_stok.min' => 'Jumlah stok minimal 0.',
            'kondisi.required' => 'Kondisi barang wajib dipilih.',
            'kondisi.in' => 'Pilihan kondisi tidak valid.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
        ]);
    }
}
