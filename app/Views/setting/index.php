<div class="max-w-2xl rounded-2xl p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
<h1 class="text-lg font-bold">Kustomisasi Sekolah</h1>
<?php if (isset($_GET['ok'])): ?><div class="text-sm p-2 my-3 rounded bg-emerald-100 text-emerald-700">Tersimpan.</div><?php endif; ?>
<form method="post" action="/setting" enctype="multipart/form-data" class="space-y-3 mt-3">
  <input type="hidden" name="id" value="<?= (int)($setting['id'] ?? 1) ?>">
  <label class="block text-sm">Nama Sekolah<input name="nama_sekolah" value="<?= htmlspecialchars($setting['nama_sekolah'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="block text-sm">Alamat<textarea name="alamat" rows="2" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"><?= htmlspecialchars($setting['alamat'] ?? '') ?></textarea></label>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <label class="block text-sm">Tahun Ajaran Aktif<input name="tahun_ajaran_aktif" value="<?= htmlspecialchars($setting['tahun_ajaran_aktif'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block text-sm">Semester<select name="semester_aktif" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
      <option <?= ($setting['semester_aktif']??'')==='Ganjil'?'selected':'' ?>>Ganjil</option>
      <option <?= ($setting['semester_aktif']??'')==='Genap'?'selected':'' ?>>Genap</option></select></label>
  </div>
  <label class="block text-sm">Logo Sekolah (unggah pengganti)<input type="file" name="logo" accept="image/*" class="mt-1 w-full text-sm"></label>
  <?php if (!empty($setting['logo_path'])): ?><img src="/<?= htmlspecialchars($setting['logo_path']) ?>" class="h-16 rounded border"><?php endif; ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <label class="block text-sm">Nama Kepsek<input name="kepala_sekolah_nama" value="<?= htmlspecialchars($setting['kepala_sekolah_nama'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
    <label class="block text-sm">NIP Kepsek<input name="kepala_sekolah_nip" value="<?= htmlspecialchars($setting['kepala_sekolah_nip'] ?? '') ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  </div>
  <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Simpan</button>
</form></div>
