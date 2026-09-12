<?php if (isset($_GET['ok'])): ?><div class="mb-4 text-sm p-2 rounded bg-emerald-100 text-emerald-700">Keputusan tersimpan. <?= isset($_GET['tindakan'])?'Tindakan: '.htmlspecialchars($_GET['tindakan']):'' ?> <?= isset($_GET['kategori'])?'Kategori: '.htmlspecialchars($_GET['kategori']):'' ?></div><?php endif; ?>
<div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden mb-4">
  <div class="p-4 font-semibold text-sm sm:text-base">Validasi Laporan Pelanggaran (pending) — OSIS butuh persetujuan Admin/Wali</div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">Siswa</th><th class="p-2">Jenis</th><th class="p-2">Pelapor</th><th class="p-2">Bukti</th><th class="p-2">Keputusan</th></tr></thead>
    <tbody>
    <?php foreach ($pelanggaran as $l): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2"><?= htmlspecialchars($l['tanggal']) ?></td>
        <td class="p-2"><?= htmlspecialchars($l['nis'].' - '.$l['siswa_nama']) ?><span class="block text-xs text-slate-500"><?= htmlspecialchars($l['nama_kelas'] ?? '') ?></span></td>
        <td class="p-2"><?= htmlspecialchars($l['jenis_nama']) ?> (<?= (int)$l['bobot_poin_saat_lapor'] ?>)<?= $l['is_force_majeure']?' <b class="text-red-600">FM</b>':'' ?></td>
        <td class="p-2"><?= htmlspecialchars($l['pelapor_nama'] ?? '-') ?></td>
        <td class="p-2"><?php if ($l['foto_bukti_path']): ?><a href="/<?= htmlspecialchars($l['foto_bukti_path']) ?>" target="_blank" class="text-sky-600 underline">Lihat foto</a><?php else: ?>-<?php endif; ?></td>
        <td class="p-2 whitespace-nowrap">
          <form method="post" action="/validasi-pelanggaran" class="inline"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="status" value="divalidasi"><button title="Validasi laporan" data-tip="Validasi" class="tip px-2.5 py-1.5 rounded bg-emerald-600 text-white text-xs"><i class="fa-solid fa-check"></i></button></form>
          <form method="post" action="/validasi-pelanggaran" class="inline"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="status" value="ditolak"><button title="Tolak laporan" data-tip="Tolak" class="tip px-2.5 py-1.5 rounded bg-red-500 text-white text-xs"><i class="fa-solid fa-xmark"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($pelanggaran)): ?><tr><td colspan="6" class="p-4 text-center text-slate-500">Tidak ada antrean.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
  <div class="p-4 font-semibold text-sm sm:text-base">Validasi Laporan Penghargaan (pending)</div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">Siswa</th><th class="p-2">Jenis</th><th class="p-2">Keputusan</th></tr></thead>
    <tbody>
    <?php foreach ($penghargaan as $l): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2"><?= htmlspecialchars($l['tanggal']) ?></td>
        <td class="p-2"><?= htmlspecialchars($l['nis'].' - '.$l['siswa_nama']) ?></td>
        <td class="p-2"><?= htmlspecialchars($l['jenis_nama']) ?> (<?= (int)$l['bobot_poin_saat_lapor'] ?>)</td>
        <td class="p-2 whitespace-nowrap">
          <form method="post" action="/validasi-penghargaan" class="inline"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="status" value="divalidasi"><button title="Validasi laporan" data-tip="Validasi" class="tip px-2.5 py-1.5 rounded bg-emerald-600 text-white text-xs"><i class="fa-solid fa-check"></i></button></form>
          <form method="post" action="/validasi-penghargaan" class="inline"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="status" value="ditolak"><button title="Tolak laporan" data-tip="Tolak" class="tip px-2.5 py-1.5 rounded bg-red-500 text-white text-xs"><i class="fa-solid fa-xmark"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($penghargaan)): ?><tr><td colspan="4" class="p-4 text-center text-slate-500">Tidak ada antrean.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
