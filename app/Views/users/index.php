<?php if (isset($_GET['ok'])): ?><div class="mb-4 text-sm p-2 rounded bg-emerald-100 text-emerald-700">Tersimpan.</div><?php endif; ?>
<?php if (isset($_GET['err'])): ?><div class="mb-4 text-sm p-2 rounded bg-red-100 text-red-700"><?= htmlspecialchars($_GET['err']) ?></div><?php endif; ?>
<div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
  <div class="p-4 flex flex-wrap gap-2 items-center">
    <h1 class="font-semibold">Pengaturan User & Role</h1>
    <form class="ml-auto flex gap-2">
      <input name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Cari nama/username/role..." class="p-2 text-sm rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <button class="px-3 py-2 text-sm rounded-lg border">Cari</button>
    </form>
    <a href="/users-form" class="px-3 py-2 text-sm rounded-lg bg-sky-600 text-white"><i class="fa-solid fa-user-plus mr-1"></i>Tambah User</a>
  </div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Nama</th><th class="p-2">Username</th><th class="p-2">Role</th><th class="p-2">Kelas Diampu</th><th class="p-2">Status</th><th class="p-2">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $u): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 font-medium"><?= htmlspecialchars($u['nama']) ?><?= ((int)$user['id'] === (int)$u['id']) ? ' <span class="text-xs text-slate-400">(Anda)</span>' : '' ?></td>
        <td class="p-2 font-mono"><?= htmlspecialchars($u['username']) ?></td>
        <td class="p-2"><span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-200"><?= htmlspecialchars(\App\Helpers\Permissions::label($u['role'])) ?></span></td>
        <td class="p-2"><?= $u['role'] === 'wali_kelas' ? (int)$u['jml_kelas'] . ' kelas' : '-' ?></td>
        <td class="p-2"><?= $u['is_active'] ? '<span class="text-emerald-600">Aktif</span>' : '<span class="text-red-500">Nonaktif</span>' ?></td>
        <td class="p-2 whitespace-nowrap">
          <a href="/users-form?id=<?= (int)$u['id'] ?>" title="Edit user" data-tip="Edit" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-sky-600"><i class="fa-solid fa-pen-to-square"></i></a>
          <a href="/users-toggle?id=<?= (int)$u['id'] ?>" title="<?= $u['is_active'] ? 'Nonaktifkan user' : 'Aktifkan user' ?>" data-tip="<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-slate-500 ml-1"><i class="fa-solid <?= $u['is_active'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i></a>
          <a href="/users-hapus?id=<?= (int)$u['id'] ?>" onclick="return confirm('Hapus user <?= htmlspecialchars($u['username']) ?>?')" title="Hapus user" data-tip="Hapus" class="tip px-2 py-1.5 rounded bg-red-100 text-red-600 dark:bg-red-950 ml-1"><i class="fa-solid fa-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($rows)): ?><tr><td colspan="6" class="p-4 text-center text-slate-500">Tidak ada user.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<div class="mt-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-x-auto table-scroll">
  <div class="p-4 font-semibold text-sm">Matriks Role & Permission <span class="font-normal text-slate-500">(ditegakkan server-side via Auth::requireRole di tiap route)</span></div>
  <table class="w-full text-sm min-w-[700px] no-dt">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Fitur</th><?php foreach ($roles as $r): ?><th class="p-2 text-center"><?= htmlspecialchars(\App\Helpers\Permissions::label($r)) ?></th><?php endforeach; ?></tr></thead>
    <tbody>
    <?php foreach ($matrix as $fitur => $boleh): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2"><?= htmlspecialchars($fitur) ?></td>
        <?php foreach ($roles as $r): ?>
          <td class="p-2 text-center"><?= in_array($r, $boleh, true) ? '<span class="text-emerald-600 font-bold">✓</span>' : '<span class="text-slate-300">–</span>' ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <p class="p-4 text-xs text-slate-500">Wali Kelas otomatis di-scope ke kelasnya (data siswa, validasi, laporan). Kepsek/Wakasek read-only. OSIS hanya melapor. Mengubah role user langsung mengubah hak aksesnya saat login berikutnya.</p>
</div>
