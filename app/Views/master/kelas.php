<?php if (isset($_GET['ok'])): ?><div class="mb-4 text-sm p-2 rounded bg-emerald-100 text-emerald-700">Tersimpan.</div><?php endif; ?>
<div class="grid lg:grid-cols-3 gap-4">
<div class="lg:col-span-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden h-fit">
  <div class="p-4 font-semibold text-sm sm:text-base">Master Data Kelas</div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left"><tr><th class="p-2">Kelas</th><th class="p-2">Tingkat</th><th class="p-2">Jurusan</th><th class="p-2">TA</th><th class="p-2">Wali</th><th class="p-2">Siswa</th><th class="p-2">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-2 font-medium"><?= htmlspecialchars($r['nama_kelas']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['tingkat']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['jurusan'] ?? '-') ?></td>
        <td class="p-2"><?= htmlspecialchars($r['tahun_ajaran']) ?></td>
        <td class="p-2"><?= htmlspecialchars($r['wali_nama'] ?? '-') ?></td>
        <td class="p-2"><?= (int)$r['jml_siswa'] ?></td>
        <td class="p-2 whitespace-nowrap">
          <button onclick='editRow(<?= json_encode($r, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)' title="Edit kelas" data-tip="Edit" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-sky-600"><i class="fa-solid fa-pen-to-square"></i></button>
          <a href="/master-kelas-hapus?id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus kelas? Siswa di dalamnya jadi tanpa kelas.')" title="Hapus kelas" data-tip="Hapus" class="tip px-2 py-1.5 rounded bg-red-100 text-red-600 dark:bg-red-950 ml-1"><i class="fa-solid fa-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($rows)): ?><tr><td colspan="7" class="p-4 text-center text-slate-500">Belum ada kelas. Tambahkan lewat form.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 h-fit">
  <h2 class="font-semibold mb-2" id="formTitle">Tambah Kelas</h2>
  <form method="post" action="/master-kelas" class="space-y-2 text-sm">
    <input type="hidden" name="id" id="f_id">
    <label class="block">Nama Kelas* <span class="text-xs text-slate-500">cth: X TKJ 1</span><input name="nama_kelas" id="f_nama" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
      <label class="block">Tingkat*<select name="tingkat" id="f_tingkat" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
        <option value="X">X</option><option value="XI">XI</option><option value="XII">XII</option></select></label>
      <label class="block">Tahun Ajaran*<input name="tahun_ajaran" id="f_ta" value="<?= htmlspecialchars($tahunAktif) ?>" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    </div>
    <label class="block">Jurusan<input name="jurusan" id="f_jurusan" placeholder="TKJ / TBSM / ..." class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">Wali Kelas<select name="wali_kelas_id" id="f_wali" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="">-- Tanpa wali --</option>
      <?php foreach ($wali as $w): ?><option value="<?= (int)$w['id'] ?>"><?= htmlspecialchars($w['nama'].' ('.$w['username'].')') ?></option><?php endforeach; ?></select></label>
    <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Simpan</button>
  </form>
  <?php if (!count($wali)): ?><p class="mt-2 text-xs text-amber-600">Belum ada user role wali_kelas. Buat dulu di tabel users (username wali / wali123 sudah ada satu).</p><?php endif; ?>
</div>
</div>
<script>
function editRow(r){document.getElementById('formTitle').innerText='Edit '+r.nama_kelas;
document.getElementById('f_id').value=r.id;document.getElementById('f_nama').value=r.nama_kelas;
document.getElementById('f_tingkat').value=r.tingkat;document.getElementById('f_ta').value=r.tahun_ajaran;
document.getElementById('f_jurusan').value=r.jurusan||'';document.getElementById('f_wali').value=r.wali_kelas_id||'';
if (window.jQuery) { jQuery('#f_tingkat, #f_wali').trigger('change'); } }
</script>
