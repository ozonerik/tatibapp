<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>SP - <?= htmlspecialchars($siswa['nama']) ?></title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="p-10 text-slate-900" onload="window.print()">
<div class="max-w-2xl mx-auto border p-8">
  <div class="text-center font-bold"><?= htmlspecialchars($setting['nama_sekolah']) ?><div class="text-xs font-normal"><?= htmlspecialchars($setting['alamat'] ?? '') ?></div></div>
  <hr class="my-4 border-2">
  <h1 class="text-center font-bold text-lg">SURAT PERJANJIAN <?= htmlspecialchars($jenis ?? 'PEM BINAAN') ?></h1>
  <p class="text-sm mt-4">Yang bertanda tangan di bawah ini, siswa:</p>
  <table class="text-sm mt-2"><tr><td>Nama</td><td>: <b><?= htmlspecialchars($siswa['nama']) ?></b></td></tr>
  <tr><td>Kelas</td><td>: <?= htmlspecialchars($siswa['nama_kelas'] ?? '-') ?></td></tr>
  <tr><td>Total Poin</td><td>: <b><?= (int)$siswa['total_poin_pelanggaran'] ?></b> (SP1: 25–50, SP2: 51–75, SP3: ≥76)</td></tr></table>
  <p class="text-sm mt-4 text-justify">Menyatakan bersedia dibina dan tidak mengulangi pelanggaran tata tertib. Apabila mengulangi, bersedia menerima tindakan lanjutan hingga pengembalian kepada orang tua. Pembinaan ini bertujuan membentuk kedisiplinan dan karakter.</p>
  <div class="flex justify-between text-sm mt-10"><div>Orang Tua/Wali<br><br><br>(....................)</div>
  <div>Krangkeng, <?= date('d M Y') ?><br>Wali Kelas<br><br><br>(....................)</div></div>
</div></body></html>
