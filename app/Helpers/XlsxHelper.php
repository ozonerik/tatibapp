<?php
namespace App\Helpers;

/**
 * Pembaca XLSX minimal (tanpa dependensi Composer).
 * Memanfaatkan ZipArchive + SimpleXML (bawaan PHP/Laragon).
 * Mengembalikan array baris: list cell string per kolom (0-indexed).
 */
class XlsxReader
{
    public static function read(string $path, int $maxRows = 2000): array
    {
        if (!is_file($path)) throw new \RuntimeException('File tidak ditemukan');
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) throw new \RuntimeException('File bukan XLSX valid');
        try {
            if ($zip->getFromName('[Content_Types].xml') === false) {
                throw new \RuntimeException('File bukan XLSX valid');
            }
            // --- Shared strings ---
            $shared = [];
            $ssXml = $zip->getFromName('xl/sharedStrings.xml');
            if ($ssXml !== false) {
                $ss = @simplexml_load_string(self::stripNs($ssXml));
                if ($ss === false) throw new \RuntimeException('Gagal membaca isi XLSX');
                foreach ($ss->si as $si) {
                    $t = $si->xpath('.//t');
                    $str = '';
                    foreach ($t as $node) $str .= (string)$node;
                    $shared[] = $str;
                }
            }
            // --- Sheet pertama ---
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($sheetXml === false) throw new \RuntimeException('Sheet1 tidak ditemukan di XLSX');
            $sheet = @simplexml_load_string(self::stripNs($sheetXml));
            if ($sheet === false) throw new \RuntimeException('Gagal membaca sheet XLSX');

            $rows = [];
            $count = 0;
            foreach ($sheet->sheetData->row as $row) {
                if ($count++ >= $maxRows) break;
                $cells = [];
                $maxCol = -1;
                foreach ($row->c as $c) {
                    $ref = (string)($c['r'] ?? '');
                    $col = self::colIndex($ref);
                    if ($col < 0) continue;
                    $maxCol = max($maxCol, $col);
                    $cells[$col] = self::cellValue($c, $shared);
                }
                $out = [];
                for ($i = 0; $i <= $maxCol; $i++) $out[$i] = trim((string)($cells[$i] ?? ''));
                $rows[] = $out;
            }
            return $rows;
        } finally {
            $zip->close();
        }
    }

    /** Hapus deklarasi namespace agar SimpleXML/XPath bisa dibaca langsung */
    private static function stripNs(string $xml): string
    {
        return (string)preg_replace('/xmlns(:\w+)?="[^"]*"/', '', $xml);
    }

    private static function colIndex(string $ref): int
    {        if (!preg_match('/^([A-Z]+)/', strtoupper($ref), $m)) return -1;
        $letters = $m[1];
        $n = 0;
        for ($i = 0; $i < strlen($letters); $i++) $n = $n * 26 + (ord($letters[$i]) - 64);
        return $n - 1;
    }

    private static function cellValue(\SimpleXMLElement $c, array $shared): string
    {
        $t = (string)($c['t'] ?? '');
        if ($t === 's') {
            $idx = (int)(string)($c->v ?? 0);
            return $shared[$idx] ?? '';
        }
        if ($t === 'inlineStr') {
            $parts = $c->is ? $c->is->xpath('.//t') : [];
            $str = '';
            foreach ($parts as $p) $str .= (string)$p;
            return $str;
        }
        if ($t === 'b') return ((string)($c->v ?? '0') === '1') ? '1' : '0';
        if (isset($c->v)) return (string)$c->v;
        return '';
    }
}
