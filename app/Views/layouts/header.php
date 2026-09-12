<!DOCTYPE html>
<html lang="id" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($setting['nama_sekolah'] ?? 'SITATIB') ?> - Sistem Tata Tertib</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { darkMode: 'class', theme: { extend: { colors: { brand: {500:'#0ea5e9',600:'#0284c7'} } } } };
<?php $modeDefault = $tampilan['mode_default'] ?? 'sistem'; ?>
(function(){
  var saved = null;
  try { saved = localStorage.theme; } catch (e) {}
  var dark = <?php if ($modeDefault === 'gelap'): ?>saved !== 'light'
    <?php elseif ($modeDefault === 'terang'): ?>saved === 'dark'
    <?php else: ?>saved === 'dark' || (!saved && matchMedia('(prefers-color-scheme: dark)').matches)<?php endif; ?>;
  if (dark) document.documentElement.classList.add('dark');
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="/assets/css/custom.css?v=3" rel="stylesheet" />
<?php
// Tema dinamis dari menu Tampilan (pengaturan_tampilan)
$_tWarna = $tampilan['warna_primer'] ?? '#0284c7';
$_tFont = \App\Models\TampilanModel::fontCss($tampilan['font_family'] ?? 'sistem');
$_tFontUrl = \App\Models\TampilanModel::fontUrl($tampilan['font_family'] ?? 'sistem');
$_tCustom = str_ireplace(['</style', '<!--'], '', (string)($tampilan['custom_css'] ?? ''));
?>
<?php if ($_tFontUrl): ?><link href="<?= $_tFontUrl ?>" rel="stylesheet" /><?php endif; ?>
<style id="tema-sekolah">
  :root { --brand: <?= htmlspecialchars($_tWarna) ?>; }
  <?php if (strtolower($_tWarna) !== '#0284c7'): ?>
  /* Timpa aksen sky Tailwind dengan warna primer pilihan */
  .bg-sky-600 { background-color: var(--brand) !important; }
  .bg-sky-500 { background-color: var(--brand) !important; }
  .text-sky-600 { color: var(--brand) !important; }
  .text-sky-700 { color: var(--brand) !important; }
  .border-sky-600 { border-color: var(--brand) !important; }
  <?php endif; ?>
  <?php if ($_tFont): ?>body { font-family: <?= $_tFont ?>; }<?php endif; ?>
  <?= $_tCustom . "\n" ?>
</style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen text-[15px] sm:text-base">
<nav class="sticky top-0 z-20 backdrop-blur bg-white/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800">
  <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-2 sm:gap-3">
    <?php if (!empty($setting['logo_path'])): ?>
      <img src="/<?= htmlspecialchars($setting['logo_path']) ?>" class="w-8 h-8 sm:w-9 sm:h-9 rounded object-cover shrink-0" alt="logo">
    <?php else: ?>
      <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-sky-500 text-white grid place-items-center font-bold shrink-0">S</div>
    <?php endif; ?>
    <div class="font-semibold text-sm sm:text-base leading-tight min-w-0">
      <span class="block truncate"><?= htmlspecialchars($setting['nama_sekolah'] ?? 'SMK Negeri 1 Krangkeng') ?></span>
      <span class="block text-[11px] sm:text-xs font-normal text-slate-500">TA <?= htmlspecialchars($setting['tahun_ajaran_aktif'] ?? '') ?></span>
    </div>
    <div class="ml-auto flex items-center gap-1.5 sm:gap-2 text-sm shrink-0">
      <span class="hidden md:inline-block px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 max-w-[220px] truncate"><?= htmlspecialchars($user['nama'] ?? '') ?> (<?= htmlspecialchars($user['role'] ?? '') ?>)</span>
      <button onclick="document.documentElement.classList.toggle('dark');localStorage.theme=document.documentElement.classList.contains('dark')?'dark':'light'" aria-label="Toggle dark mode" class="px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700">🌙</button>
      <a href="/logout" class="hidden sm:inline-block px-3 py-1.5 rounded-lg bg-red-500 text-white">Logout</a>
      <button id="navToggle" aria-label="Buka menu" aria-expanded="false" class="md:hidden px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700">☰</button>
    </div>
  </div>
  <div id="navMenu" class="hidden md:block border-t border-slate-100 dark:border-slate-800/60">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2 flex flex-col md:flex-row md:flex-wrap gap-1 text-sm">
      <a href="/dashboard" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">📊 Dashboard</a>
      <a href="/laporan" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">📄 Laporan</a>
      <a href="/lapor-pelanggaran" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">📝 Lapor Pelanggaran</a>
      <a href="/siswa" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">👥 Data Siswa</a>
      <?php if (in_array($user['role'] ?? '', ['admin','wali_kelas'])): ?><a href="/validasi" class="px-3 py-2 rounded bg-amber-100 dark:bg-amber-900 hover:opacity-80">✅ Validasi Laporan</a><?php endif; ?>
      <?php if (in_array($user['role'] ?? '', ['admin'])): ?>
        <a href="/master-kelas" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">🏫 Master Kelas</a>
        <a href="/master-pelanggaran" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">⚠️ Master Pelanggaran</a>
        <a href="/master-penghargaan" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">🏆 Master Penghargaan</a>
      <a href="/users" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">👤 Pengguna</a>
      <a href="/tampilan" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">🎨 Tampilan</a>
      <a href="/setting" class="px-3 py-2 rounded hover:bg-slate-100 dark:hover:bg-slate-800">⚙️ Pengaturan</a>
      <?php endif; ?>
      <span class="md:hidden px-3 py-2 text-xs text-slate-500"><?= htmlspecialchars($user['nama'] ?? '') ?> (<?= htmlspecialchars($user['role'] ?? '') ?>)</span>
      <a href="/logout" class="sm:hidden px-3 py-2 rounded bg-red-500 text-white text-center">Logout</a>
    </div>
  </div>
</nav>
<script>
(function(){
  var btn = document.getElementById('navToggle');
  var menu = document.getElementById('navMenu');
  // Di mobile menu tertutup default; di desktop selalu tampil
  function sync(){ if (window.innerWidth >= 768) { menu.classList.remove('hidden'); } }
  window.addEventListener('resize', sync); sync();
  btn.addEventListener('click', function(){
    var open = menu.classList.toggle('hidden');
    btn.setAttribute('aria-expanded', open ? 'false' : 'true');
  });
})();
</script>
<main class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
