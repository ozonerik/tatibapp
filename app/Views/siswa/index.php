<div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
  <div class="p-4 flex flex-wrap gap-2 items-center">
    <h1 class="font-semibold">Data Siswa <?= ($user['role']==='wali_kelas') ? '(Kelas Anda)' : '' ?></h1>
    <form class="ml-auto flex gap-2">
      <input name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Cari NIS/nama..." class="p-2 text-sm rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <button class="px-3 py-2 text-sm rounded-lg border">Cari</button>
    </form>
    <?php if (in_array($user['role'], ['admin','wali_kelas'])): ?>
    <a href="/siswa-import" class="px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700"><i class="fa-solid fa-file-excel mr-1 text-emerald-600"></i>Import XLSX</a>
    <a href="/siswa-form" class="px-3 py-2 text-sm rounded-lg bg-sky-600 text-white"><i class="fa-solid fa-plus mr-1"></i>Tambah Siswa</a>
    <?php endif; ?>
  </div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">NIS</th><th class="p-2">Nama</th><th class="p-2">Kelas</th><th class="p-2">Poin Langgar</th><th class="p-2">Poin Penghargaan</th><th class="p-2">Status</th><th class="p-2">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $s): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 font-mono"><?= htmlspecialchars($s['nis']) ?></td>
        <td class="p-2 font-medium"><?= htmlspecialchars($s['nama']) ?></td>
        <td class="p-2"><?= htmlspecialchars($s['nama_kelas'] ?? '-') ?></td>
        <td class="p-2"><span class="font-bold <?= $s['total_poin_pelanggaran']>=76?'text-red-600':($s['total_poin_pelanggaran']>=25?'text-amber-600':'') ?>"><?= (int)$s['total_poin_pelanggaran'] ?></span></td>
        <td class="p-2 font-bold text-emerald-600"><?= (int)$s['total_poin_penghargaan'] ?></td>
        <td class="p-2"><?= htmlspecialchars($s['status']) ?></td>
        <td class="p-2 whitespace-nowrap">
          <a href="/siswa-detail?id=<?= (int)$s['id'] ?>" title="Lihat detail" data-tip="Detail" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-slate-500"><i class="fa-solid fa-eye"></i></a>
          <?php if (in_array($user['role'], ['admin','wali_kelas'])): ?>
          <a href="/siswa-form?id=<?= (int)$s['id'] ?>" title="Edit siswa" data-tip="Edit" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-sky-600 ml-1"><i class="fa-solid fa-pen-to-square"></i></a>
          <a href="/cetak-sp?siswa_id=<?= (int)$s['id'] ?>" target="_blank" rel="noopener" title="Cetak SP" data-tip="Cetak SP" class="tip px-2 py-1.5 rounded bg-red-500 text-white ml-1"><i class="fa-solid fa-print"></i></a>
          <a href="/cetak-sertifikat?siswa_id=<?= (int)$s['id'] ?>" target="_blank" rel="noopener" title="Cetak sertifikat" data-tip="Sertifikat" class="tip px-2 py-1.5 rounded bg-emerald-600 text-white ml-1"><i class="fa-solid fa-award"></i></a>
          <?php endif; ?>
          <?php if (($user['role'] ?? '')==='admin'): ?>
          <a href="/siswa-hapus?id=<?= (int)$s['id'] ?>" onclick="return confirm('Hapus siswa?')" title="Hapus siswa" data-tip="Hapus" class="tip px-2 py-1.5 rounded bg-red-100 text-red-600 dark:bg-red-950 ml-1"><i class="fa-solid fa-trash"></i></a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($rows)): ?><tr><td colspan="7" class="p-4 text-center text-slate-500">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
