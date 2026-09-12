<div class="grid lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
  <div class="p-4 font-semibold text-sm sm:text-base">Master Jenis Penghargaan + Bobot Poin</div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Kode</th><th class="p-2">Nama</th><th class="p-2">Poin</th><th class="p-2">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 font-mono"><?= htmlspecialchars($r['kode']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['nama']) ?></td>
        <td class="p-2 font-bold"><?= (int)$r['bobot_poin'] ?></td>
        <td class="p-2 whitespace-nowrap">
          <button onclick='editRow(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)' title="Edit" data-tip="Edit" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-sky-600"><i class="fa-solid fa-pen-to-square"></i></button>
          <a href="/master-penghargaan-hapus?id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus?')" title="Hapus" data-tip="Hapus" class="tip px-2 py-1.5 rounded bg-red-100 text-red-600 dark:bg-red-950 ml-1"><i class="fa-solid fa-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 h-fit">
  <h2 class="font-semibold mb-2" id="formTitle">Tambah Penghargaan</h2>
  <form method="post" action="/master-penghargaan" class="space-y-2 text-sm">
    <input type="hidden" name="id" id="f_id">
    <label class="block">Kode<input name="kode" id="f_kode" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">Nama<input name="nama" id="f_nama" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">Bobot Poin<input type="number" name="bobot_poin" id="f_poin" min="1" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">Deskripsi<textarea name="deskripsi" id="f_desk" rows="2" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></textarea></label>
    <label class="flex gap-2 items-center"><input type="checkbox" name="is_active" checked value="1"> Aktif</label>
    <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Simpan</button>
  </form>
</div>
</div>
<script>
function editRow(r){document.getElementById('formTitle').innerText='Edit '+r.kode;
f_id.value=r.id;f_kode.value=r.kode;f_nama.value=r.nama;f_poin.value=r.bobot_poin;f_desk.value=r.deskripsi||'';}
</script>
