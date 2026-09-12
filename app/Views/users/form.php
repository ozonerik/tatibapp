<div class="max-w-xl rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
<h1 class="font-semibold mb-1"><?= empty($data['id']) ? 'Tambah' : 'Edit' ?> User</h1>
<?php if (!empty($error)): ?><div class="text-sm p-2 my-2 rounded bg-red-100 text-red-700"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<p class="text-xs text-slate-500 mb-3">Role menentukan permission (lihat matriks di halaman Pengguna). Password dikosongkan = tidak diubah (saat edit).</p>
<form method="post" action="/users-form" class="space-y-2 text-sm">
  <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">
  <label class="block">Nama Lengkap*<input name="nama" required value="<?= htmlspecialchars($data['nama']) ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="block">Username*<input name="username" required value="<?= htmlspecialchars($data['username']) ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="block">Password<?= empty($data['id']) ? '*' : ' (kosongkan jika tidak diubah)' ?><input type="password" name="password" <?= empty($data['id']) ? 'required' : '' ?> placeholder="<?= empty($data['id']) ? 'cth: wali123' : '••••••' ?>" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="block">Role*<select name="role" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
    <?php foreach (\App\Helpers\Permissions::ROLE_LABELS as $val => $label): ?>
      <option value="<?= $val ?>" <?= ($data['role'] ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label . " ($val)") ?></option>
    <?php endforeach; ?>
  </select></label>
  <label class="flex gap-2 items-center"><input type="checkbox" name="is_active" value="1" <?= !empty($data['is_active']) ? 'checked' : '' ?>> Akun aktif (bisa login)</label>
  <div class="flex gap-2">
    <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Simpan</button>
    <a href="/users" class="px-4 py-2 rounded-lg border">Batal</a>
  </div>
</form></div>
