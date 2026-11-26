<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Collection;

class PdfReportGenerator
{
    private const PAGE_WIDTH = 595.28;  // A4 Portrait width in pt
    private const PAGE_HEIGHT = 841.89; // A4 Portrait height in pt
    private const MARGIN_X = 36.0;

    /**
     * Generate a multi-page A4 PDF report for the given inventory items.
     *
     * @param  Collection<int, \App\Models\Barang>  $barangs
     * @param  array<string, string>  $filters
     */
    public function generate(Collection $barangs, AppSetting $settings, array $filters = []): string
    {
        $pages = [];
        $currentStream = '';
        $y = 0.0;

        $startNewPage = function (bool $isFirstPage) use (&$pages, &$currentStream, &$y, $settings, $filters, $barangs): void {
            if ($currentStream !== '') {
                $pages[] = $currentStream;
            }
            $currentStream = '';
            $y = $this->renderPageHeader($currentStream, $settings, $filters, $barangs, $isFirstPage);
            $y = $this->renderTableHeader($currentStream, $y);
        };

        $startNewPage(true);

        if ($barangs->isEmpty()) {
            $currentStream .= $this->drawRect(self::MARGIN_X, $y - 32, self::PAGE_WIDTH - (self::MARGIN_X * 2), 32, [0.97, 0.98, 0.99], [0.85, 0.88, 0.92]);
            $currentStream .= $this->drawText(self::MARGIN_X + 12, $y - 20, 'Tidak ada data barang yang sesuai dengan filter laporan.', 9.5, false, [0.4, 0.45, 0.52]);
            $y -= 38;
        } else {
            foreach ($barangs as $index => $barang) {
                if ($y < 78) {
                    $startNewPage(false);
                }

                $rowHeight = 22.0;
                $bg = $index % 2 === 1 ? [0.975, 0.982, 0.992] : [1.0, 1.0, 1.0];
                $currentStream .= $this->drawRect(
                    self::MARGIN_X,
                    $y - $rowHeight,
                    self::PAGE_WIDTH - (self::MARGIN_X * 2),
                    $rowHeight,
                    $bg,
                    [0.88, 0.90, 0.94]
                );

                $textY = $y - 14.5;
                $currentStream .= $this->drawText(self::MARGIN_X + 8, $textY, (string) ($index + 1), 8.5, false, [0.35, 0.40, 0.48]);
                $currentStream .= $this->drawText(self::MARGIN_X + 34, $textY, $this->truncate($barang->kode_barang, 14), 8.5, true, [0.12, 0.16, 0.24]);
                $currentStream .= $this->drawText(self::MARGIN_X + 114, $textY, $this->truncate($barang->nama_barang, 34), 8.5, false, [0.10, 0.14, 0.20]);
                $currentStream .= $this->drawText(self::MARGIN_X + 306, $textY, number_format($barang->jumlah_stok) . ' unit', 8.5, true, [0.22, 0.26, 0.65]);

                $kondisiColor = match ($barang->kondisi) {
                    'Baik' => [0.05, 0.48, 0.30],
                    'Rusak Ringan' => [0.70, 0.42, 0.02],
                    'Rusak Berat' => [0.75, 0.12, 0.22],
                    default => [0.30, 0.35, 0.42],
                };
                $currentStream .= $this->drawText(self::MARGIN_X + 366, $textY, $barang->kondisi, 8.5, true, $kondisiColor);

                $updatedStr = $barang->updated_at
                    ? $barang->updated_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i')
                    : '-';
                $currentStream .= $this->drawText(self::MARGIN_X + 448, $textY, $updatedStr, 8.0, false, [0.38, 0.44, 0.52]);

                $y -= $rowHeight;
            }

            // Summary footer row
            if ($y < 68) {
                $startNewPage(false);
            }
            $totalStok = (int) $barangs->sum('jumlah_stok');
            $currentStream .= $this->drawRect(self::MARGIN_X, $y - 24, self::PAGE_WIDTH - (self::MARGIN_X * 2), 24, [0.93, 0.95, 0.99], [0.78, 0.82, 0.92]);
            $currentStream .= $this->drawText(self::MARGIN_X + 8, $y - 15.5, 'TOTAL RINGKASAN (' . number_format($barangs->count()) . ' Jenis Barang)', 8.5, true, [0.15, 0.20, 0.45]);
            $currentStream .= $this->drawText(self::MARGIN_X + 306, $y - 15.5, number_format($totalStok) . ' unit', 8.5, true, [0.15, 0.20, 0.45]);
        }

        if ($currentStream !== '') {
            $pages[] = $currentStream;
        }

        return $this->buildPdfDocument($pages);
    }

    /**
     * @param  Collection<int, \App\Models\Barang>  $barangs
     * @param  array<string, string>  $filters
     */
    private function renderPageHeader(string &$stream, AppSetting $settings, array $filters, Collection $barangs, bool $isFirstPage): float
    {
        $topY = self::PAGE_HEIGHT - 36;
        $contentWidth = self::PAGE_WIDTH - (self::MARGIN_X * 2);

        // Top accent bar
        $stream .= $this->drawRect(self::MARGIN_X, $topY - 52, $contentWidth, 52, [0.15, 0.22, 0.56], null);
        $stream .= $this->drawText(self::MARGIN_X + 14, $topY - 21, strtoupper($settings->nama_sekolah), 12.5, true, [1.0, 1.0, 1.0]);
        $stream .= $this->drawText(self::MARGIN_X + 14, $topY - 37, 'Laporan Inventaris Barang Laboratorium - ' . $settings->nama_aplikasi, 9.5, false, [0.86, 0.90, 0.99]);

        $printedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';
        $stream .= $this->drawText(self::PAGE_WIDTH - self::MARGIN_X - 160, $topY - 21, 'Dicetak: ' . $printedAt, 8.0, false, [0.88, 0.92, 1.0]);

        $y = $topY - 64;

        if ($isFirstPage) {
            $kondisiLabel = ($filters['kondisi'] ?? '') !== '' ? $filters['kondisi'] : 'Semua Kondisi';
            $periodeLabel = $this->formatPeriode($filters['tanggal_mulai'] ?? '', $filters['tanggal_selesai'] ?? '');

            $stream .= $this->drawRect(self::MARGIN_X, $y - 38, $contentWidth, 38, [0.97, 0.98, 0.995], [0.86, 0.89, 0.94]);
            $stream .= $this->drawText(self::MARGIN_X + 10, $y - 14, 'Filter Kondisi: ' . $kondisiLabel . '   |   Periode: ' . $periodeLabel, 8.5, true, [0.22, 0.27, 0.35]);

            $baik = $barangs->where('kondisi', 'Baik')->count();
            $ringan = $barangs->where('kondisi', 'Rusak Ringan')->count();
            $berat = $barangs->where('kondisi', 'Rusak Berat')->count();
            $summaryText = sprintf(
                'Total Jenis: %d   |   Total Unit: %d   |   Baik: %d   |   Rusak Ringan: %d   |   Rusak Berat: %d',
                $barangs->count(),
                (int) $barangs->sum('jumlah_stok'),
                $baik,
                $ringan,
                $berat
            );
            $stream .= $this->drawText(self::MARGIN_X + 10, $y - 29, $summaryText, 8.5, false, [0.35, 0.41, 0.50]);

            $y -= 48;
        }

        return $y;
    }

    private function renderTableHeader(string &$stream, float $y): float
    {
        $contentWidth = self::PAGE_WIDTH - (self::MARGIN_X * 2);
        $headerH = 24.0;

        $stream .= $this->drawRect(self::MARGIN_X, $y - $headerH, $contentWidth, $headerH, [0.92, 0.94, 0.97], [0.80, 0.84, 0.90]);
        $textY = $y - 15.5;
        $stream .= $this->drawText(self::MARGIN_X + 8, $textY, 'NO', 8.0, true, [0.25, 0.30, 0.38]);
        $stream .= $this->drawText(self::MARGIN_X + 34, $textY, 'KODE', 8.0, true, [0.25, 0.30, 0.38]);
        $stream .= $this->drawText(self::MARGIN_X + 114, $textY, 'NAMA BARANG', 8.0, true, [0.25, 0.30, 0.38]);
        $stream .= $this->drawText(self::MARGIN_X + 306, $textY, 'STOK', 8.0, true, [0.25, 0.30, 0.38]);
        $stream .= $this->drawText(self::MARGIN_X + 366, $textY, 'KONDISI', 8.0, true, [0.25, 0.30, 0.38]);
        $stream .= $this->drawText(self::MARGIN_X + 448, $textY, 'DIPERBARUI', 8.0, true, [0.25, 0.30, 0.38]);

        return $y - $headerH;
    }

    private function formatPeriode(string $start, string $end): string
    {
        if ($start !== '' && $end !== '') {
            return "{$start} s/d {$end}";
        }
        if ($start !== '') {
            return "Mulai {$start}";
        }
        if ($end !== '') {
            return "Sampai {$end}";
        }

        return 'Semua Tanggal';
    }

    /**
     * @param  list<string>  $pageStreams
     */
    private function buildPdfDocument(array $pageStreams): string
    {
        $totalPages = count($pageStreams);
        $objects = [];

        // Object 1: Catalog, Object 2: Pages, Object 3: Font Regular, Object 4: Font Bold
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $pageObjectIds = [];
        $nextId = 5;

        foreach ($pageStreams as $idx => $stream) {
            $pageNum = $idx + 1;
            $stream .= $this->drawText(
                self::MARGIN_X,
                24,
                "Halaman {$pageNum} dari {$totalPages} - Dokumen resmi inventaris laboratorium sekolah",
                8.0,
                false,
                [0.45, 0.50, 0.58]
            );

            $contentId = $nextId++;
            $pageId = $nextId++;

            $objects[$contentId] = "<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream";
            $objects[$pageId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::PAGE_WIDTH,
                self::PAGE_HEIGHT,
                $contentId
            );

            $pageObjectIds[] = "{$pageId} 0 R";
        }

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageObjectIds) . '] /Count ' . count($pageObjectIds) . ' >>';

        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0 => 0];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxId = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxId; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size " . ($maxId + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    /**
     * @param  array{0:float,1:float,2:float}|null  $fillRgb
     * @param  array{0:float,1:float,2:float}|null  $strokeRgb
     */
    private function drawRect(float $x, float $y, float $w, float $h, ?array $fillRgb, ?array $strokeRgb): string
    {
        $cmd = "q\n";
        if ($fillRgb !== null) {
            $cmd .= sprintf("%.3f %.3f %.3f rg\n", $fillRgb[0], $fillRgb[1], $fillRgb[2]);
        }
        if ($strokeRgb !== null) {
            $cmd .= sprintf("%.3f %.3f %.3f RG\n0.5 w\n", $strokeRgb[0], $strokeRgb[1], $strokeRgb[2]);
        }
        $op = match (true) {
            $fillRgb !== null && $strokeRgb !== null => 'B',
            $fillRgb !== null => 'f',
            default => 'S',
        };
        $cmd .= sprintf("%.2f %.2f %.2f %.2f re %s\nQ\n", $x, $y, $w, $h, $op);

        return $cmd;
    }

    /**
     * @param  array{0:float,1:float,2:float}  $rgb
     */
    private function drawText(float $x, float $y, string $text, float $size, bool $bold, array $rgb): string
    {
        $font = $bold ? 'F2' : 'F1';
        $escaped = $this->escapePdfText($text);

        return sprintf(
            "BT\n/%s %.2f Tf\n%.3f %.3f %.3f rg\n1 0 0 1 %.2f %.2f Tm\n(%s) Tj\nET\n",
            $font,
            $size,
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $x,
            $y,
            $escaped
        );
    }

    private function escapePdfText(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '?', $text) ?? '';
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $converted);
    }

    private function truncate(string $text, int $max): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max - 3) . '...';
    }
}
