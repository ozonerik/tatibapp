<?php
// Generator template import siswa (.xlsx murni via ZipArchive).
// Jalankan: php database/make-template-siswa.php
// Output : public/assets/template/template-siswa.xlsx
$outDir = __DIR__ . '/../public/assets/template';
if (!is_dir($outDir)) mkdir($outDir, 0755, true);
$outFile = $outDir . '/template-siswa.xlsx';

$esc = fn($s) => htmlspecialchars((string)$s, ENT_XML1 | ENT_COMPAT, 'UTF-8');

$headers = ['NIS*', 'NISN', 'Nama Lengkap*', 'JK (L/P)*', 'Kelas*', 'Alamat', 'Nama Orang Tua', 'No HP Ortu'];
$examples = [
    ['2001', '002001', 'Andi Pratama', 'L', 'X TKJ 1', 'Jl. Mawar No.1, Krangkeng', 'Bpk. Andi', '081200000101'],
    ['2002', '002002', 'Sari Wulandari', 'P', 'XI TKJ 1', 'Jl. Melati No.2, Krangkeng', 'Ibu Sari', '081200000102'],
    ['2003', '', 'Rudi Hartono', 'L', 'XII TKJ 1', 'Jl. Kenanga No.3, Krangkeng', '', ''],
];

$colLetter = function (int $i): string {
    $s = '';
    $i++;
    while ($i > 0) { $m = ($i - 1) % 26; $s = chr(65 + $m) . $s; $i = intdiv($i - 1, 26); }
    return $s;
};

$cellInline = function (string $ref, string $val, int $style = 0) use ($esc): string {
    return '<c r="' . $ref . '"' . ($style ? ' s="' . $style . '"' : '') . ' t="inlineStr"><is><t>'
        . $esc($val) . '</t></is></c>';
};

$rowsXml = '';
// Header (style 1 = bold + fill)
$r = 1;
$rowXml = '';
foreach ($headers as $ci => $h) $rowXml .= $cellInline($colLetter($ci) . $r, $h, 1);
$rowsXml .= '<row r="' . $r . '">' . $rowXml . '</row>';
// Contoh
foreach ($examples as $ri => $ex) {
    $r = $ri + 2;
    $rowXml = '';
    foreach ($ex as $ci => $v) $rowXml .= $cellInline($colLetter($ci) . $r, $v);
    $rowsXml .= '<row r="' . $r . '">' . $rowXml . '</row>';
}

$widths = [12, 12, 24, 10, 14, 32, 22, 16];
$colsXml = '';
foreach ($widths as $ci => $w) {
    $L = $colLetter($ci);
    $colsXml .= '<col min="' . ($ci + 1) . '" max="' . ($ci + 1) . '" width="' . $w . '" customWidth="1"/>';
}

$contentTypes = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
    . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
    . '</Types>';
$rels = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
    . '</Relationships>';
$workbook = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<sheets><sheet name="Data Siswa" sheetId="1" r:id="rId1"/></sheets></workbook>';
$workbookRels = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
    . '</Relationships>';
$styles = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<fonts><font><sz val="11"/><name val="Calibri"/></font>'
    . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts>'
    . '<fills><fill><patternFill patternType="none"/></fill>'
    . '<fill><patternFill patternType="gray125"/></fill>'
    . '<fill><patternFill patternType="solid"><fgColor rgb="FF0284C7"/><bgColor indexed="64"/></patternFill></fill></fills>'
    . '<borders><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
    . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
    . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
    . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
    . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
    . '</styleSheet>';
$sheet = '<?xml version="1.0" encoding="UTF-8"?>'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
    . '<cols>' . $colsXml . '</cols>'
    . '<sheetData>' . $rowsXml . '</sheetData>'
    . '<dataValidations count="1"><dataValidation type="list" allowBlank="1" showDropDown="1" showErrorMessage="1" sqref="D2:D1001">'
    . '<formula1>"L,P"</formula1></dataValidation></dataValidations>'
    . '</worksheet>';

$zip = new ZipArchive();
if ($zip->open($outFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Gagal membuat $outFile\n");
    exit(1);
}
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rels);
$zip->addFromString('xl/workbook.xml', $workbook);
$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
$zip->addFromString('xl/styles.xml', $styles);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
$zip->close();
echo "OK: $outFile (" . filesize($outFile) . " bytes)\n";
