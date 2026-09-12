<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan - <?= htmlspecialchars($setting['nama_sekolah'] ?? '') ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>@media print{.no-print{display:none}}</style></head>
<body class="p-8 text-slate-900" onload="window.print()">
<div class="max-w-4xl mx-auto">
  <div class="text-center">
    <?php if (!empty($setting['logo_path'])): ?><img src="/<?= htmlspecialchars($setting['logo_path']) ?>" class="h-14 mx-auto mb-1"><?php endif; ?>
    <div class="font-bold"><?= htmlspecialchars($setting['nama_sekolah'] ?? 'SMK Negeri 1 Krangkeng') ?></div>
    <div class="text-xs"><?= htmlspecialchars($setting['alamat'] ?? '') ?></div>
    <h1 class="font-bold mt-3">LAPORAN DAFTAR SISWA: PELANGGARAN & PENGHARGAAN</h1>
    <div class="text-xs">TA <?= htmlspecialchars($f['tahun_ajaran'] ?: 'Semua') ?> • Periode <?= htmlspecialchars($f['dari'] ?: '-') ?> s/d <?= htmlspecialchars($f['sampai'] ?: '-') ?> • Status <?= htmlspecialchars($f['status'] ?: 'Semua') ?></div>
  </div>
  <div class="no-print my-3"><button onclick="window.print()" class="px-3 py-1.5 rounded bg-sky-600 text-white text-sm"><i class="fa-solid fa-print mr-1"></i>Cetak / Simpan PDF</button></div>

  <h2 class="font-bold text-sm mt-4 mb-1">A. Pelanggaran (<?= count($pelanggaran) ?>)</h2>
  <table class="w-full text-xs border-collapse border border-slate-400">
    <thead><tr class="bg-slate-100"><th class="border p-1">No</th><th class="border p-1">Tanggal</th><th class="border p-1">NIS/Nama/Kelas</th><th class="border p-1">Jenis</th><th class="border p-1">Poin</th><th class="border p-1">Status</th></tr></thead>
    <tbody>
    <?php $no = 1; foreach ($pelanggaran as $r): ?>
      <tr><td class="border p-1"><?= $no++ ?></td><td class="border p-1"><?= htmlspecialchars($r['tanggal']) ?></td>
      <td class="border p-1"><?= htmlspecialchars($r['nis'].' - '.$r['siswa_nama'].' ('.($r['nama_kelas'] ?? '-').')') ?></td>
      <td class="border p-1"><?= htmlspecialchars($r['jenis_nama']) ?></td><td class="border p-1"><?= (int)$r['bobot_poin_saat_lapor'] ?></td><td class="border p-1"><?= htmlspecialchars($r['status_validasi']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <h2 class="font-bold text-sm mt-4 mb-1">B. Penghargaan (<?= count($penghargaan) ?>)</h2>
  <table class="w-full text-xs border-collapse border border-slate-400">
    <thead><tr class="bg-slate-100"><th class="border p-1">No</th><th class="border p-1">Tanggal</th><th class="border p-1">NIS/Nama/Kelas</th><th class="border p-1">Jenis</th><th class="border p-1">Poin</th><th class="border p-1">Status</th></tr></thead>
    <tbody>
    <?php $no = 1; foreach ($penghargaan as $r): ?>
      <tr><td class="border p-1"><?= $no++ ?></td><td class="border p-1"><?= htmlspecialchars($r['tanggal']) ?></td>
      <td class="border p-1"><?= htmlspecialchars($r['nis'].' - '.$r['siswa_nama'].' ('.($r['nama_kelas'] ?? '-').')') ?></td>
      <td class="border p-1"><?= htmlspecialchars($r['jenis_nama']) ?></td><td class="border p-1"><?= (int)$r['bobot_poin_saat_lapor'] ?></td><td class="border p-1"><?= htmlspecialchars($r['status_validasi']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div class="flex justify-between text-xs mt-8">
    <div>Mengetahui,<br>Kepala Sekolah<br><br><br><b><?= htmlspecialchars($setting['kepala_sekolah_nama'] ?? '....................') ?></b></div>
    <div>Krangkeng, <?= date('d M Y') ?><br>Wali Kelas / Kesiswaan<br><br><br>(....................)</div>
  </div>
</div></body></html>
