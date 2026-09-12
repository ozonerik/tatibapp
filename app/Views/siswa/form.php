<div class="max-w-xl rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
<h1 class="font-semibold mb-3"><?= empty($data['id']) ? 'Tambah' : 'Edit' ?> Siswa</h1>
<form method="post" action="/siswa-form" class="space-y-2 text-sm">
  <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
    <label class="block">NIS*<input name="nis" required value="<?= htmlspecialchars($data['nis']) ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">NISN<input name="nisn" value="<?= htmlspecialchars($data['nisn'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  </div>
  <label class="block">Nama*<input name="nama" required value="<?= htmlspecialchars($data['nama']) ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
    <label class="block">JK<select name="jenis_kelamin" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="L" <?= $data['jenis_kelamin']==='L'?'selected':'' ?>>Laki-laki</option><option value="P" <?= $data['jenis_kelamin']==='P'?'selected':'' ?>>Perempuan</option></select></label>
    <label class="block">Kelas<select name="kelas_id" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option value="">-- Pilih --</option>
      <?php foreach ($kelas as $k): ?><option value="<?= (int)$k['id'] ?>" <?= (string)$data['kelas_id']===(string)$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option><?php endforeach; ?></select></label>
  </div>
  <label class="block">Alamat<textarea name="alamat" rows="2" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea></label>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
    <label class="block">Nama Ortu<input name="nama_ortu" value="<?= htmlspecialchars($data['nama_ortu'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block">No HP Ortu<input name="no_hp_ortu" value="<?= htmlspecialchars($data['no_hp_ortu'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  </div>
  <label class="block">Status<select name="status" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
    <?php foreach (['aktif','dikembalikan','lulus','pindah'] as $st): ?><option <?= ($data['status']??'')===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?></select></label>
  <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Simpan</button>
  <a href="/siswa" class="px-4 py-2 rounded-lg border">Batal</a>
</form></div>
