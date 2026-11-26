<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Barang;
use App\Services\PdfReportGenerator;
use App\Services\XlsxReportGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);
        $query = $this->filteredQuery($filters);

        $summaryQuery = clone $query;
        $summary = $summaryQuery
            ->selectRaw('COUNT(*) as total_jenis')
            ->selectRaw('COALESCE(SUM(jumlah_stok), 0) as total_stok')
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END), 0) as baik")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END), 0) as rusak_ringan")
            ->selectRaw("COALESCE(SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END), 0) as rusak_berat")
            ->first();

        $barangs = $query->latest()->paginate(10)->withQueryString();

        return view('laporan.index', [
            'barangs' => $barangs,
            'filters' => $filters,
            'kondisiOptions' => Barang::KONDISI_OPTIONS,
            'summary' => [
                'total_jenis' => (int) ($summary->total_jenis ?? 0),
                'total_stok' => (int) ($summary->total_stok ?? 0),
                'baik' => (int) ($summary->baik ?? 0),
                'rusak_ringan' => (int) ($summary->rusak_ringan ?? 0),
                'rusak_berat' => (int) ($summary->rusak_berat ?? 0),
            ],
        ]);
    }

    public function exportPdf(Request $request, PdfReportGenerator $pdf): Response
    {
        $filters = $this->extractFilters($request);
        $barangs = $this->filteredQuery($filters)->latest()->get();
        $settings = AppSetting::current();

        $binary = $pdf->generate($barangs, $settings, $filters);
        $filename = 'laporan-inventaris-' . now()->timezone('Asia/Jakarta')->format('Ymd-His') . '.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportExcel(Request $request, XlsxReportGenerator $xlsx): Response
    {
        $filters = $this->extractFilters($request);
        $barangs = $this->filteredQuery($filters)->latest()->get();
        $settings = AppSetting::current();

        $binary = $xlsx->generate($barangs, $settings, $filters);
        $filename = 'laporan-inventaris-' . now()->timezone('Asia/Jakarta')->format('Ymd-His') . '.xlsx';

        return response($binary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @return array{kondisi:string, tanggal_mulai:string, tanggal_selesai:string}
     */
    private function extractFilters(Request $request): array
    {
        $validated = $request->validate([
            'kondisi' => ['nullable', 'string', 'in:' . implode(',', Barang::KONDISI_OPTIONS)],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
        ]);

        return [
            'kondisi' => trim((string) ($validated['kondisi'] ?? '')),
            'tanggal_mulai' => trim((string) ($validated['tanggal_mulai'] ?? '')),
            'tanggal_selesai' => trim((string) ($validated['tanggal_selesai'] ?? '')),
        ];
    }

    /**
     * @param  array{kondisi:string, tanggal_mulai:string, tanggal_selesai:string}  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return Barang::query()
            ->when($filters['kondisi'] !== '', fn (Builder $q) => $q->where('kondisi', $filters['kondisi']))
            ->when($filters['tanggal_mulai'] !== '', fn (Builder $q) => $q->whereDate('created_at', '>=', $filters['tanggal_mulai']))
            ->when($filters['tanggal_selesai'] !== '', fn (Builder $q) => $q->whereDate('created_at', '<=', $filters['tanggal_selesai']));
    }
}
