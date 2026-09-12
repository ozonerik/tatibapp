<?php if (isset($_GET['ok'])): ?><div class="mb-4 text-sm p-2 rounded bg-emerald-100 text-emerald-700">Tampilan tersimpan<?= $_GET['ok'] === 'reset' ? ' (dikembalikan ke default)' : '' ?>.</div><?php endif; ?>
<?php if (isset($_GET['err'])): ?><div class="mb-4 text-sm p-2 rounded bg-red-100 text-red-700"><?= htmlspecialchars($_GET['err']) ?></div><?php endif; ?>
<div class="grid lg:grid-cols-3 gap-4">
  <form method="post" action="/tampilan" class="lg:col-span-2 rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3 text-sm">
    <h1 class="font-semibold text-base">🎨 Kustomisasi Tampilan</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <label class="block">Warna Primer
        <span class="flex gap-2 mt-1">
          <input type="color" name="warna_primer" id="t_warna" value="<?= htmlspecialchars($tampilan['warna_primer']) ?>" class="w-12 h-10 rounded border cursor-pointer">
          <input id="t_warna_hex" value="<?= htmlspecialchars($tampilan['warna_primer']) ?>" maxlength="7" class="flex-1 p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700 font-mono">
        </span>
      </label>
      <label class="block">Font Aplikasi
        <select name="font_family" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
          <?php foreach ($fonts as $val => $label): ?><option value="<?= $val ?>" <?= ($tampilan['font_family'] ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option><?php endforeach; ?>
        </select>
      </label>
    </div>
    <label class="block">Mode Gelap Default
      <select name="mode_default" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
        <option value="sistem" <?= ($tampilan['mode_default'] ?? '') === 'sistem' ? 'selected' : '' ?>>Ikuti perangkat (sistem)</option>
        <option value="terang" <?= ($tampilan['mode_default'] ?? '') === 'terang' ? 'selected' : '' ?>>Selalu terang</option>
        <option value="gelap" <?= ($tampilan['mode_default'] ?? '') === 'gelap' ? 'selected' : '' ?>>Selalu gelap</option>
      </select>
      <span class="text-xs text-slate-500">Pengguna tetap bisa mengganti manual via tombol 🌙 di navbar.</span>
    </label>
    <label class="block">CSS Tambahan <span class="text-xs text-slate-500">(opsional, untuk kustomisasi lanjut)</span>
      <textarea name="custom_css" rows="5" placeholder=".btn-khusus { ... }" class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700 font-mono text-xs"><?= htmlspecialchars($tampilan['custom_css'] ?? '') ?></textarea>
    </label>
    <div class="flex flex-wrap gap-2">
      <button class="px-4 py-2 rounded-lg bg-sky-600 text-white"><i class="fa-solid fa-floppy-disk mr-1"></i>Simpan Tampilan</button>
      <a href="/tampilan-reset" onclick="return confirm('Kembalikan ke tampilan default?')" class="px-4 py-2 rounded-lg border">Reset Default</a>
    </div>
  </form>
  <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 h-fit text-sm">
    <h2 class="font-semibold mb-2">Pratinjau</h2>
    <div class="space-y-2">
      <button class="px-4 py-2 rounded-lg bg-sky-600 text-white w-full">Tombol Primer</button>
      <span class="inline-block text-sky-600 underline">Tautan aksi</span>
      <span class="block text-xs px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 w-fit">Badge</span>
      <p class="text-xs text-slate-500">Warna primer, font, dan mode gelap langsung berlaku ke seluruh aplikasi setelah disimpan.</p>
    </div>
  </div>
</div>
<script>
(function(){
  var c = document.getElementById('t_warna'), h = document.getElementById('t_warna_hex');
  c.addEventListener('input', function(){ h.value = c.value; });
  h.addEventListener('input', function(){ if (/^#[0-9a-fA-F]{6}$/.test(h.value)) c.value = h.value; });
})();
</script>
