<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Laporan.php';

Auth::requireLaporan();

$tipe = $_GET['tipe'] ?? 'peminjaman';
if (!in_array($tipe, ['peminjaman', 'inventaris'], true)) {
    http_response_code(400);
    exit('Tipe laporan tidak valid.');
}

function excelColumnName(int $number): string {
    $name = '';
    while ($number > 0) {
        $number--;
        $name = chr(65 + ($number % 26)) . $name;
        $number = intdiv($number, 26);
    }
    return $name;
}

function excelCellXml($value, string $reference): string {
    if (is_int($value) || is_float($value)) {
        return '<c r="' . $reference . '" t="n"><v>' . $value . '</v></c>';
    }

    $value = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    return '<c r="' . $reference . '" t="inlineStr"><is><t xml:space="preserve">' . $value . '</t></is></c>';
}

function excelWorksheetXml(array $rows): string {
    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

    foreach ($rows as $rowIndex => $row) {
        $rowNumber = $rowIndex + 1;
        $xml .= '<row r="' . $rowNumber . '">';
        foreach (array_values($row) as $columnIndex => $value) {
            $reference = excelColumnName($columnIndex + 1) . $rowNumber;
            $xml .= excelCellXml($value, $reference);
        }
        $xml .= '</row>';
    }

    return $xml . '</sheetData></worksheet>';
}

function downloadExcelWorkbook(string $filename, string $sheetName, array $rows): void {
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('Ekstensi ZIP PHP diperlukan untuk membuat file Excel.');
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'laporan_');
    if ($tempFile === false) {
        throw new RuntimeException('Tidak dapat menyiapkan file Excel.');
    }

    try {
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file Excel.');
        }

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="' . htmlspecialchars($sheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '" sheetId="1" r:id="rId1"/></sheets>'
                . '</workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '</Relationships>',
            'xl/worksheets/sheet1.xml' => excelWorksheetXml($rows),
        ];

        foreach ($files as $path => $contents) {
            if (!$zip->addFromString($path, $contents)) {
                $zip->close();
                throw new RuntimeException('Tidak dapat menulis isi file Excel.');
            }
        }
        if (!$zip->close()) {
            throw new RuntimeException('Tidak dapat menyelesaikan file Excel.');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        readfile($tempFile);
    } finally {
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
}

$laporanObj = new Laporan();

if ($tipe === 'peminjaman') {
    $tglAwal = $_GET['tgl_awal'] ?? date('Y-m-01');
    $tglAkhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
    $status = $_GET['status'] ?? 'ALL';
    $rows = $laporanObj->getLaporanPeminjaman($tglAwal, $tglAkhir, $status);
    $excelRows = [[
        'Nama Peminjam',
        'NIP / Identitas',
        'Kode Peminjaman',
        'Tanggal Pinjam',
        'Kategori',
        'Nama Sarana',
        'Kode Inventaris',
        'Ruangan',
        'Jumlah',
        'Tanggal Kembali',
        'Status',
    ]];

    foreach ($rows as $row) {
        $excelRows[] = [
            $row['nama_pegawai'],
            $row['nip'],
            $row['kode_peminjaman'],
            $row['tanggal_pinjam'],
            $row['nama_jenis'] ?? '-',
            $row['nama_barang'],
            $row['kode_inventaris'],
            $row['nama_ruang'] ?? '-',
            (int) $row['jumlah'],
            $row['tanggal_kembali'] ?? '',
            $row['status_peminjaman'],
        ];
    }

    $safeDate = preg_replace('/[^0-9-]/', '', $tglAwal);
    downloadExcelWorkbook('laporan_peminjaman_' . $safeDate . '.xlsx', 'Peminjaman', $excelRows);
} else {
    $idRuang = !empty($_GET['ruang']) ? (int) $_GET['ruang'] : null;
    $idJenis = !empty($_GET['jenis']) ? (int) $_GET['jenis'] : null;
    $kondisi = $_GET['kondisi'] ?? 'ALL';
    $rows = $laporanObj->getLaporanInventaris($idRuang, $idJenis, $kondisi);
    $excelRows = [[
        'Kode Inventaris',
        'Nama Sarana',
        'Jenis',
        'Ruangan',
        'Kondisi',
        'Stok Unit',
        'Tanggal Register',
        'Keterangan',
    ]];

    foreach ($rows as $row) {
        $excelRows[] = [
            $row['kode_inventaris'],
            $row['nama'],
            $row['nama_jenis'],
            $row['nama_ruang'],
            $row['kondisi'],
            (int) $row['jumlah'],
            $row['tanggal_register'],
            $row['keterangan'],
        ];
    }

    downloadExcelWorkbook('laporan_inventaris_' . date('Y-m-d') . '.xlsx', 'Inventaris', $excelRows);
}
