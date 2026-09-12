<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Sertifikat - <?= htmlspecialchars($siswa['nama']) ?></title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="p-10 bg-amber-50" onload="window.print()">
<div class="max-w-2xl mx-auto border-4 border-amber-400 bg-white p-10 text-center">
  <div class="text-xs tracking-widest">PENGHARGAAN SISWA</div>
  <h1 class="text-2xl font-bold mt-1"><?= $kategori==='WALUYA_UTAMA' ? 'ANUGERAH WALUYA UTAMA' : ($kategori==='HADIAH' ? 'SERTIFIKAT + HADIAH PRESTASI' : 'SERTIFIKAT MURID BERPRESTASI') ?></h1>
  <div class="text-sm"><?= htmlspecialchars($setting['nama_sekolah']) ?></div>
  <p class="mt-6">Diberikan kepada</p><div class="text-xl font-bold"><?= htmlspecialchars($siswa['nama']) ?></div>
  <p class="text-sm mt-2">atas perolehan <b><?= (int)$siswa['total_poin_penghargaan'] ?> poin penghargaan</b> (100–125 sertifikat • 126–150 +hadiah • ≥151 Waluya Utama)</p>
  <div class="flex justify-between text-sm mt-10 text-left"><div>Wali Kelas<br><br>(....................)</div>
  <div>Kepala Sekolah<br><br><b><?= htmlspecialchars($setting['kepala_sekolah_nama'] ?? '') ?></b></div></div>
</div></body></html>
