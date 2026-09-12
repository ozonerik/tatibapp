<?php $qs = fn($over = []) => http_build_query(array_merge($f, $over)); ?>
<div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
  <div class="flex flex-wrap items-center gap-2">
    <h1 class="font-semibold">Laporan Pelanggaran & Penghargaan</h1>
    <?php if (($user['role'] ?? '') === 'wali_kelas'): ?><span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">Scope: kelas Anda</span><?php endif; ?>
    <div class="ml-auto flex gap-2 text-sm">
      <a href="/laporan-cetak?<?= $qs() ?>" target="_blank" title="Cetak laporan" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700"><i class="fa-solid fa-print mr-1"></i>Cetak</a>
      <a href="/laporan-export?<?= $qs() ?>" title="Export ke CSV" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white"><i class="fa-solid fa-file-csv mr-1"></i>Export CSV</a>
    </div>
  </div>
  <!-- Tab -->
  <div class="flex gap-2 mt-3 text-sm">
    <?php foreach (['pelanggaran' => 'Pelanggaran ('.count($pelanggaran).')', 'penghargaan' => 'Penghargaan ('.count($penghargaan).')', 'rekap' => 'Rekap per Siswa'] as $t => $label): ?>
      <a href="/laporan?<?= $qs(['tab' => $t]) ?>" class="px-3 py-1.5 rounded-lg <?= ($f['tab'] === $t) ? 'bg-sky-600 text-white' : 'bg-slate-100 dark:bg-slate-800' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </div>
  <!-- Filter -->
  <form method="get" action="/laporan" class="grid md:grid-cols-7 gap-2 mt-3 text-sm">
    <input type="hidden" name="tab" value="<?= htmlspecialchars($f['tab']) ?>">
    <select name="kelas_id" class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="">Semua kelas</option>
      <?php foreach ($kelas as $k): ?><option value="<?= (int)$k['id'] ?>" <?= (string)$f['kelas_id'] === (string)$k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option><?php endforeach; ?>
    </select>
    <select name="tahun_ajaran" class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="">Semua TA</option>
      <?php foreach ($tahunList as $t): ?><option <?= $f['tahun_ajaran'] === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option><?php endforeach; ?>
    </select>
    <input type="date" name="dari" value="<?= htmlspecialchars($f['dari']) ?>" class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
    <input type="date" name="sampai" value="<?= htmlspecialchars($f['sampai']) ?>" class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
    <select name="status" class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="">Semua status</option>
      <?php foreach (['pending', 'divalidasi', 'ditolak'] as $s): ?><option <?= $f['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
    </select>
    <input name="q" value="<?= htmlspecialchars($f['q']) ?>" placeholder="NIS/nama..." class="p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
    <div class="flex gap-2"><button class="px-3 py-2 rounded-lg bg-sky-600 text-white">Filter</button><a href="/laporan?tab=<?= htmlspecialchars($f['tab']) ?>" class="px-3 py-2 rounded-lg border">Reset</a></div>
  </form>
</div>

<?php if ($f['tab'] === 'penghargaan'): ?>
<div class="mt-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-x-auto table-scroll">
  <table class="w-full text-sm min-w-[800px]">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">NIS/Nama</th><th class="p-2">Kelas</th><th class="p-2">Jenis Penghargaan</th><th class="p-2">Poin</th><th class="p-2">Status</th><th class="p-2">Bukti</th></tr></thead>
    <tbody>
    <?php foreach ($penghargaan as $r): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 whitespace-nowrap"><?= htmlspecialchars($r['tanggal']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['nis']) ?><span class="block font-medium"><?= htmlspecialchars($r['siswa_nama']) ?></span></td>
        <td class="p-2"><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
        <td class="p-2"><?= htmlspecialchars($r['jenis_nama']) ?></td>
        <td class="p-2 font-bold text-emerald-600">+<?= (int)$r['bobot_poin_saat_lapor'] ?></td>
        <td class="p-2"><span class="text-xs px-2 py-0.5 rounded-full <?= $r['status_validasi'] === 'divalidasi' ? 'bg-emerald-100 text-emerald-700' : ($r['status_validasi'] === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') ?>"><?= htmlspecialchars($r['status_validasi']) ?></span></td>
        <td class="p-2"><?php if (!empty($r['foto_bukti_path'])): ?><a href="/<?= htmlspecialchars($r['foto_bukti_path']) ?>" target="_blank" class="text-sky-600 underline">Lihat</a><?php else: ?>-<?php endif; ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($penghargaan)): ?><tr><td colspan="7" class="p-4 text-center text-slate-500">Tidak ada data penghargaan.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php elseif ($f['tab'] === 'rekap'): ?>
<div class="mt-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-x-auto table-scroll">
  <table class="w-full text-sm min-w-[800px]">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">NIS</th><th class="p-2">Nama</th><th class="p-2">Kelas</th><th class="p-2">Kasus Langgar</th><th class="p-2">Poin Langgar</th><th class="p-2">Jml Penghargaan</th><th class="p-2">Poin Penghargaan</th><th class="p-2">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rekap as $r): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 font-mono"><?= htmlspecialchars($r['nis']) ?></td>
        <td class="p-2 font-medium"><?= htmlspecialchars($r['nama']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
        <td class="p-2"><?= (int)$r['jml_langgar'] ?></td>
        <td class="p-2 font-bold <?= $r['poin_langgar'] >= 76 ? 'text-red-600' : ($r['poin_langgar'] >= 25 ? 'text-amber-600' : '') ?>"><?= (int)$r['poin_langgar'] ?></td>
        <td class="p-2"><?= (int)$r['jml_harga'] ?></td>
        <td class="p-2 font-bold text-emerald-600"><?= (int)$r['poin_harga'] ?></td>
        <td class="p-2"><a href="/siswa-detail?id=<?= (int)$r['id'] ?>" title="Lihat detail siswa" data-tip="Detail" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-slate-500"><i class="fa-solid fa-eye"></i></a></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($rekap)): ?><tr><td colspan="8" class="p-4 text-center text-slate-500">Tidak ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="mt-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-x-auto table-scroll">
  <table class="w-full text-sm min-w-[900px]">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">NIS/Nama</th><th class="p-2">Kelas</th><th class="p-2">Jenis Pelanggaran</th><th class="p-2">Poin</th><th class="p-2">Status</th><th class="p-2">Bukti</th></tr></thead>
    <tbody>
    <?php foreach ($pelanggaran as $r): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 whitespace-nowrap"><?= htmlspecialchars($r['tanggal']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['nis']) ?><span class="block font-medium"><?= htmlspecialchars($r['siswa_nama']) ?></span><span class="block text-xs text-slate-500">Pelapor: <?= htmlspecialchars($r['pelapor_nama'] ?? '-') ?></span></td>
        <td class="p-2"><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
        <td class="p-2"><?= htmlspecialchars($r['jenis_nama']) ?><?= $r['is_force_majeure'] ? ' <b class="text-red-600 text-xs">FM</b>' : '' ?></td>
        <td class="p-2 font-bold"><?= (int)$r['bobot_poin_saat_lapor'] ?></td>
        <td class="p-2"><span class="text-xs px-2 py-0.5 rounded-full <?= $r['status_validasi'] === 'divalidasi' ? 'bg-emerald-100 text-emerald-700' : ($r['status_validasi'] === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') ?>"><?= htmlspecialchars($r['status_validasi']) ?></span></td>
        <td class="p-2"><?php if (!empty($r['foto_bukti_path'])): ?><a href="/<?= htmlspecialchars($r['foto_bukti_path']) ?>" target="_blank" class="text-sky-600 underline">Lihat foto</a><?php else: ?>-<?php endif; ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($pelanggaran)): ?><tr><td colspan="7" class="p-4 text-center text-slate-500">Tidak ada data pelanggaran. Silakan tambah via Lapor Pelanggaran atau longgarkan filter.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
