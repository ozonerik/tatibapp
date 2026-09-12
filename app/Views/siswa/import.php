<div class="grid lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <h1 class="font-semibold text-base mb-1"><i class="fa-solid fa-file-excel text-emerald-600 mr-1"></i>Import Data Siswa (.xlsx)</h1>
    <ol class="text-sm text-slate-600 dark:text-slate-300 list-decimal ml-5 space-y-1 mb-3">
      <li>Unduh template, isi data (baris contoh boleh dihapus).</li>
      <li>Kolom <b>NIS, Nama, JK, Kelas</b> wajib diisi. JK cukup <b>L</b>/<b>P</b>.</li>
      <li>Nama kelas harus sama persis dengan Master Kelas<?= ($user['role'] ?? '') === 'wali_kelas' ? ' <b>yang Anda ampu</b>' : '' ?>.</li>
      <li>Format kolom NIS & No HP sebagai <b>Text</b> di Excel agar angka 0 di depan tidak hilang.</li>
      <li>Maksimal file 5 MB / 2000 baris. NIS ganda akan ditolak per baris.</li>
    </ol>
    <div class="flex flex-wrap gap-2 mb-4 text-sm">
      <a href="/assets/template/template-siswa.xlsx" class="px-4 py-2 rounded-lg bg-emerald-600 text-white"><i class="fa-solid fa-download mr-1"></i>Unduh Template</a>
      <a href="/siswa" class="px-4 py-2 rounded-lg border">Kembali</a>
    </div>
    <form method="post" action="/siswa-import" enctype="multipart/form-data" class="text-sm">
      <label class="block">File .xlsx<input type="file" name="file_xlsx" accept=".xlsx" required class="mt-1 w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-sky-600 file:text-white"></label>
      <button class="mt-3 px-4 py-2 rounded-lg bg-sky-600 text-white"><i class="fa-solid fa-upload mr-1"></i>Upload & Proses</button>
    </form>
  </div>
  <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 h-fit text-sm">
    <h2 class="font-semibold mb-2">Hasil Import Terakhir</h2>
    <?php if ($hasil === null): ?>
      <p class="text-slate-500">Belum ada proses import. Hasil akan tampil di sini.</p>
    <?php else: ?>
      <div class="grid grid-cols-2 gap-2 text-center mb-3">
        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950"><div class="text-2xl font-bold text-emerald-600"><?= (int)$hasil['masuk'] ?></div><div class="text-xs">Berhasil masuk</div></div>
        <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800"><div class="text-2xl font-bold"><?= (int)$hasil['lewat'] ?></div><div class="text-xs">Baris kosong dilewati</div></div>
      </div>
      <?php if (!empty($hasil['errors'])): ?>
        <div class="text-xs font-semibold mb-1">Ditolak (<?= count($hasil['errors']) ?>):</div>
        <ul class="text-xs space-y-1 max-h-64 overflow-y-auto">
          <?php foreach ($hasil['errors'] as $e): ?><li class="p-2 rounded bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300"><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-xs text-emerald-600">Semua baris valid, tanpa penolakan. 🎉</p>
      <?php endif; ?>
      <a href="/siswa" class="inline-block mt-3 px-4 py-2 rounded-lg bg-sky-600 text-white">Lihat Data Siswa</a>
    <?php endif; ?>
  </div>
</div>
