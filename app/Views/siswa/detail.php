<div class="grid lg:grid-cols-3 gap-4">
<div class="rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
  <h1 class="font-bold text-lg"><?= htmlspecialchars($s['nama']) ?></h1>
  <div class="text-sm text-slate-500"><?= htmlspecialchars($s['nis']) ?> • <?= htmlspecialchars($s['nama_kelas'] ?? '-') ?> • <?= htmlspecialchars($s['status']) ?></div>
  <div class="grid grid-cols-2 gap-2 mt-4 text-center">
    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-950"><div class="text-2xl font-bold text-red-600"><?= (int)$s['total_poin_pelanggaran'] ?></div><div class="text-xs">Poin Pelanggaran</div><div class="text-xs font-semibold"><?= htmlspecialchars($tindakan ?? 'Aman') ?></div></div>
    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950"><div class="text-2xl font-bold text-emerald-600"><?= (int)$s['total_poin_penghargaan'] ?></div><div class="text-xs">Poin Penghargaan</div><div class="text-xs font-semibold"><?= htmlspecialchars($kat ?? '-') ?></div></div>
  </div>
  <?php if (in_array($user['role'], ['admin','wali_kelas'])): ?>
  <div class="flex flex-col sm:flex-row gap-2 mt-4 text-sm">
    <a href="/cetak-sp?siswa_id=<?= (int)$s['id'] ?>" target="_blank" rel="noopener" title="Cetak SP" class="px-3 py-2 rounded-lg bg-red-500 text-white text-center"><i class="fa-solid fa-print mr-1"></i>Cetak SP</a>
    <a href="/cetak-sertifikat?siswa_id=<?= (int)$s['id'] ?>" target="_blank" rel="noopener" title="Cetak sertifikat" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-center"><i class="fa-solid fa-award mr-1"></i>Cetak Sertifikat</a>
  </div>
  <?php endif; ?>
</div>
<div class="lg:col-span-2 space-y-4 min-w-0">
  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="p-3 font-semibold text-sm">Riwayat Pelanggaran (termasuk foto bukti)</div>
    <div class="table-scroll">
    <table class="w-full text-sm"><thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">Jenis</th><th class="p-2">Poin</th><th class="p-2">Status</th><th class="p-2">Bukti</th></tr></thead>
    <tbody><?php foreach ($riwayat['langgar'] as $l): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800"><td class="p-2 whitespace-nowrap"><?= htmlspecialchars($l['tanggal']) ?></td><td class="p-2"><?= htmlspecialchars($l['jenis_nama']) ?><?= $l['is_force_majeure']?' <span class="text-xs text-red-600">FM</span>':'' ?></td><td class="p-2"><?= (int)$l['bobot_poin_saat_lapor'] ?></td><td class="p-2"><?= htmlspecialchars($l['status_validasi']) ?></td>
      <td class="p-2"><?php if ($l['foto_bukti_path']): ?><a href="/<?= htmlspecialchars($l['foto_bukti_path']) ?>" target="_blank" class="text-sky-600 underline">Lihat</a><?php else: ?>-<?php endif; ?></td></tr>
    <?php endforeach; ?></tbody></table>
    </div>
  </div>
  <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="p-3 font-semibold text-sm">Riwayat Penghargaan & Tindakan</div>
    <div class="table-scroll">
    <table class="w-full text-sm"><thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">Jenis</th><th class="p-2">Poin</th></tr></thead>
    <tbody><?php foreach ($riwayat['harga'] as $l): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800"><td class="p-2 whitespace-nowrap"><?= htmlspecialchars($l['tanggal']) ?></td><td class="p-2"><?= htmlspecialchars($l['jenis_nama']) ?></td><td class="p-2"><?= (int)$l['bobot_poin_saat_lapor'] ?></td></tr>
    <?php endforeach; ?></tbody></table>
    </div>
    <div class="p-3 text-sm">Tindakan: <?php foreach ($riwayat['tindak'] as $t): ?><span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 mr-1"><?= htmlspecialchars($t['jenis'].' @ '.$t['tanggal_cetak']) ?></span><?php endforeach; ?></div>
  </div>
</div>
</div>
