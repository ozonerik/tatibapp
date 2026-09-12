<div class="max-w-2xl rounded-2xl p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
<h1 class="text-lg font-bold mb-1">Lapor Pelanggaran</h1>
<p class="text-xs text-slate-500 mb-4">OSIS / Wali / Admin. Laporan masuk status <b>pending</b> dan wajib divalidasi. Foto bukti opsional.</p>
<form method="post" action="/lapor-pelanggaran" enctype="multipart/form-data" class="space-y-3">
  <label class="block text-sm">Siswa
    <select name="siswa_id" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <?php foreach ($siswa as $s): ?><option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['nis'].' - '.$s['nama']) ?></option><?php endforeach; ?>
    </select></label>
  <label class="block text-sm">Jenis Pelanggaran
    <select name="jenis_id" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <?php foreach ($jenis as $j): ?><option value="<?= (int)$j['id'] ?>"><?= htmlspecialchars($j['nama'].' ('.$j['bobot_poin'].' poin)') ?></option><?php endforeach; ?>
    </select></label>
  <label class="block text-sm">Tanggal<input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="block text-sm">Kronologi<textarea name="kronologi" rows="3" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></textarea></label>
  <label class="block text-sm">Foto Bukti (opsional, JPG/PNG max 2MB)<input type="file" name="foto_bukti" accept="image/*" class="mt-1 w-full text-sm"></label>
  <label class="flex items-center gap-2 text-sm text-red-600"><input type="checkbox" name="is_force_majeure" value="1"> Force Majeure — pelanggaran berat (hukum/asusila) → langsung Pengembalian ke orang tua</label>
  <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Kirim Laporan</button>
</form></div>
