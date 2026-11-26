<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

class XlsxReportGenerator
{
    /**
     * Generate an authentic OpenXML (.xlsx) workbook binary string.
     *
     * @param  Collection<int, \App\Models\Barang>  $barangs
     * @param  array<string, string>  $filters
     */
    public function generate(Collection $barangs, AppSetting $settings, array $filters = []): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'inv_xlsx_');
        if ($tempFile === false) {
            throw new RuntimeException('Gagal membuat file sementara untuk export Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            @unlink($tempFile);
            throw new RuntimeException('Gagal menginisialisasi arsip XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($barangs, $settings, $filters));
        $zip->close();

        $binary = file_get_contents($tempFile);
        @unlink($tempFile);

        if ($binary === false) {
            throw new RuntimeException('Gagal membaca hasil export Excel.');
        }

        return $binary;
    }

    /**
     * @param  Collection<int, \App\Models\Barang>  $barangs
     * @param  array<string, string>  $filters
     */
    private function sheetXml(Collection $barangs, AppSetting $settings, array $filters): string
    {
        $rows = [];
        $printedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';
        $kondisiLabel = ($filters['kondisi'] ?? '') !== '' ? $filters['kondisi'] : 'Semua Kondisi';
        $start = $filters['tanggal_mulai'] ?? '';
        $end = $filters['tanggal_selesai'] ?? '';
        $periodeLabel = ($start !== '' || $end !== '') ? trim("{$start} s/d {$end}") : 'Semua Tanggal';

        // Title rows
        $rows[] = $this->buildRow(1, [['value' => $settings->nama_sekolah . ' - Laporan Inventaris Lab', 'style' => 1]]);
        $rows[] = $this->buildRow(2, [['value' => "Filter Kondisi: {$kondisiLabel} | Periode: {$periodeLabel} | Dicetak: {$printedAt}", 'style' => 0]]);
        $rows[] = $this->buildRow(3, []);

        // Header row
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Jumlah Stok', 'Kondisi', 'Deskripsi', 'Tanggal Dibuat (WIB)', 'Terakhir Diperbarui (WIB)'];
        $headerCells = array_map(fn (string $h) => ['value' => $h, 'style' => 2], $headers);
        $rows[] = $this->buildRow(4, $headerCells);

        $rowNum = 5;
        foreach ($barangs as $index => $barang) {
            $rows[] = $this->buildRow($rowNum, [
                ['value' => $index + 1, 'type' => 'n', 'style' => 3],
                ['value' => $barang->kode_barang, 'style' => 3],
                ['value' => $barang->nama_barang, 'style' => 3],
                ['value' => (int) $barang->jumlah_stok, 'type' => 'n', 'style' => 3],
                ['value' => $barang->kondisi, 'style' => 3],
                ['value' => $barang->deskripsi ?: '-', 'style' => 3],
                ['value' => $barang->created_at ? $barang->created_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-', 'style' => 3],
                ['value' => $barang->updated_at ? $barang->updated_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-', 'style' => 3],
            ]);
            $rowNum++;
        }

        // Summary row
        $rows[] = $this->buildRow($rowNum, [
            ['value' => '', 'style' => 1],
            ['value' => 'TOTAL', 'style' => 1],
            ['value' => $barangs->count() . ' Jenis Barang', 'style' => 1],
            ['value' => (int) $barangs->sum('jumlah_stok'), 'type' => 'n', 'style' => 1],
            ['value' => '', 'style' => 1],
            ['value' => '', 'style' => 1],
            ['value' => '', 'style' => 1],
            ['value' => '', 'style' => 1],
        ]);

        $rowsXml = implode("\n", $rows);

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <cols>
        <col min="1" max="1" width="6" customWidth="1"/>
        <col min="2" max="2" width="16" customWidth="1"/>
        <col min="3" max="3" width="32" customWidth="1"/>
        <col min="4" max="4" width="14" customWidth="1"/>
        <col min="5" max="5" width="16" customWidth="1"/>
        <col min="6" max="6" width="36" customWidth="1"/>
        <col min="7" max="7" width="22" customWidth="1"/>
        <col min="8" max="8" width="24" customWidth="1"/>
    </cols>
    <sheetData>
        {$rowsXml}
    </sheetData>
</worksheet>
XML;
    }

    /**
     * @param  list<array{value:string|int, type?:string, style?:int}>  $cells
     */
    private function buildRow(int $rowIndex, array $cells): string
    {
        if ($cells === []) {
            return "<row r=\"{$rowIndex}\"/>";
        }

        $cellXml = [];
        foreach ($cells as $colIndex => $cell) {
            $colLetter = $this->columnLetter($colIndex + 1);
            $ref = "{$colLetter}{$rowIndex}";
            $style = $cell['style'] ?? 0;
            $type = $cell['type'] ?? 's';

            if ($type === 'n') {
                $num = (int) $cell['value'];
                $cellXml[] = "<c r=\"{$ref}\" s=\"{$style}\"><v>{$num}</v></c>";
            } else {
                $escaped = htmlspecialchars((string) $cell['value'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $cellXml[] = "<c r=\"{$ref}\" t=\"inlineStr\" s=\"{$style}\"><is><t>{$escaped}</t></is></c>";
            }
        }

        return "<row r=\"{$rowIndex}\">" . implode('', $cellXml) . '</row>';
    }

    private function columnLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $mod = ($col - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $col = intdiv($col - 1, 26);
        }

        return $letter;
    }

    private function contentTypesXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>
XML;
    }

    private function rootRelsXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML;
    }

    private function workbookXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Laporan Inventaris" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
    }

    private function workbookRelsXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function stylesXml(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="3">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
    </fonts>
    <fills count="3">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FF1E3A8A"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border/>
        <border>
            <left style="thin"><color rgb="FFD1D5DB"/></left>
            <right style="thin"><color rgb="FFD1D5DB"/></right>
            <top style="thin"><color rgb="FFD1D5DB"/></top>
            <bottom style="thin"><color rgb="FFD1D5DB"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="4">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
    </cellXfs>
</styleSheet>
XML;
    }
}
