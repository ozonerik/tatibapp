<?php /** @var array $stat $tren $pie $top $sp $setting */ ?>
<!-- Kartu statistik -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
  <?php $cards = [['Siswa Aktif',$stat['siswa'],'bg-sky-500'],['Perlu Validasi',$stat['pelanggaran_pending'],'bg-amber-500'],['Kasus SP3',$stat['sp3'],'bg-red-500'],['Anugerah Waluya Utama',$stat['waluya'],'bg-emerald-500']]; ?>
  <?php foreach ($cards as [$label,$val,$color]): ?>
  <div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="text-xs text-slate-500"><?= $label ?></div>
    <div class="text-3xl font-bold mt-1"><?= (int)$val ?></div>
    <div class="h-1.5 mt-3 rounded <?= $color ?>"></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Daftar siswa perlu pembinaan SP -->
<div class="mt-6">
  <div class="flex flex-col sm:flex-row sm:items-center gap-1 mb-3">
    <h2 class="font-semibold text-sm sm:text-base">📋 Siswa Perlu Pembinaan (berdasarkan akumulasi poin)</h2>
    <span class="text-xs text-slate-500">SP1: 25–50 • SP2: 51–75 • SP3: ≥76</span>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <?php $spStyle = ['SP1' => ['amber', 'Surat Perjanjian Pertama'], 'SP2' => ['orange', 'Surat Perjanjian Kedua'], 'SP3' => ['red', 'Surat Perjanjian Ketiga']]; ?>
    <?php foreach (['SP1', 'SP2', 'SP3'] as $level): $list = $sp[$level] ?? []; ?>
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
      <div class="p-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800">
        <span class="px-2.5 py-1 rounded-lg text-sm font-bold bg-<?= $spStyle[$level][0] ?>-100 text-<?= $spStyle[$level][0] ?>-700 dark:bg-<?= $spStyle[$level][0] ?>-900 dark:text-<?= $spStyle[$level][0] ?>-200"><?= $level ?></span>
        <div class="min-w-0">
          <div class="text-sm font-semibold truncate"><?= $spStyle[$level][1] ?></div>
          <div class="text-xs text-slate-500"><?= count($list) ?> siswa</div>
        </div>
      </div>
      <ul class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
        <?php foreach ($list as $s): ?>
        <li class="p-3 flex items-center gap-2">
          <div class="min-w-0 flex-1">
            <div class="text-sm font-medium truncate"><?= htmlspecialchars($s['nama']) ?></div>
            <div class="text-xs text-slate-500"><?= htmlspecialchars($s['nis'] . ' • ' . ($s['nama_kelas'] ?? '-')) ?></div>
          </div>
          <span class="text-sm font-bold text-<?= $spStyle[$level][0] ?>-600 shrink-0"><?= (int)$s['total_poin_pelanggaran'] ?></span>
          <div class="flex gap-1 shrink-0">
            <a href="/siswa-detail?id=<?= (int)$s['id'] ?>" title="Lihat detail siswa" data-tip="Detail siswa" class="tip px-2 py-1.5 rounded border border-slate-300 dark:border-slate-700 text-slate-500"><i class="fa-solid fa-eye"></i></a>
            <?php if (in_array($user['role'] ?? '', ['admin', 'wali_kelas'])): ?>
            <a href="/cetak-sp?siswa_id=<?= (int)$s['id'] ?>" target="_blank" rel="noopener" title="Cetak <?= $level ?>" data-tip="Cetak <?= $level ?>" class="tip px-2 py-1.5 rounded bg-<?= $spStyle[$level][0] ?>-500 text-white"><i class="fa-solid fa-print"></i></a>
            <?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
        <?php if (!count($list)): ?><li class="p-4 text-center text-xs text-slate-400">Tidak ada siswa pada level ini. 🎉</li><?php endif; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Grafik Chart.js -->
<div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 mt-6">
  <form method="get" action="/dashboard" class="flex flex-wrap items-end gap-2 text-sm">
    <label class="block">Tahun
      <select name="tahun" class="mt-1 p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
        <?php foreach ($tahunList as $t): ?><option value="<?= (int)$t ?>" <?= ((int)$tahun === (int)$t) ? 'selected' : '' ?>><?= (int)$t ?></option><?php endforeach; ?>
      </select>
    </label>
    <label class="block">Bulan <span class="text-xs text-slate-500">(untuk diagram pie)</span>
      <select name="bulan" class="mt-1 p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
        <option value="">Semua bulan</option>
        <?php for ($m = 1; $m <= 12; $m++): $nm = date('F', mktime(0, 0, 0, $m, 1)); ?>
          <option value="<?= $m ?>" <?= ((int)($bulan ?? 0) === $m) ? 'selected' : '' ?>><?= $nm ?></option>
        <?php endfor; ?>
      </select>
    </label>
    <button class="px-4 py-2 rounded-lg bg-sky-600 text-white">Tampilkan</button>
    <?php if (!empty($bulan)): ?><span class="text-xs text-slate-500 pb-2">Pie: <?= date('F', mktime(0, 0, 0, (int)$bulan, 1)) ?> <?= (int)$tahun ?> • Tren: 12 bulan <?= (int)$tahun ?></span>
    <?php else: ?><span class="text-xs text-slate-500 pb-2">Menampilkan data tahun <?= (int)$tahun ?></span><?php endif; ?>
  </form>
</div>
<div class="grid lg:grid-cols-3 gap-4 mt-4">
  <div class="lg:col-span-2 rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <h2 class="font-semibold mb-2">Tren Bulanan Pelanggaran vs Penghargaan</h2>
    <canvas id="chartTren" height="120"></canvas>
  </div>
  <div class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <h2 class="font-semibold mb-2">Persentase Jenis Pelanggaran</h2>
    <canvas id="chartPie" height="180"></canvas>
  </div>
</div>

<!-- Daftar berprestasi / Anugerah Waluya Utama -->
<div class="mt-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden">
  <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-1 sm:justify-between">
    <h2 class="font-semibold text-sm sm:text-base">🏆 Kandidat Anugerah Waluya Utama (skor tertinggi <?= htmlspecialchars($setting['tahun_ajaran_aktif']) ?>)</h2>
    <span class="text-xs text-slate-500">100–125 Sertifikat • 126–150 +Hadiah • ≥151 Waluya Utama</span>
  </div>
  <div class="table-scroll">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/60 text-left">
      <tr><th class="p-3">NIS</th><th class="p-3">Nama</th><th class="p-3">Kelas</th><th class="p-3">Poin</th><th class="p-3">Aksi</th></tr>
    </thead>
    <tbody>
    <?php foreach ($top as $s): $kat = \App\Helpers\PoinRule::penghargaanUntuk((int)$s['poin']); ?>
      <tr class="border-t border-slate-100 dark:border-slate-800">
        <td class="p-3"><?= htmlspecialchars($s['nis']) ?></td>
        <td class="p-3 font-medium"><?= htmlspecialchars($s['nama']) ?>
          <?php if ($kat==='WALUYA_UTAMA'): ?><span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Waluya Utama</span><?php endif; ?>
        </td>
        <td class="p-3"><?= htmlspecialchars($s['nama_kelas'] ?? '-') ?></td>
        <td class="p-3 font-bold"><?= (int)$s['poin'] ?></td>
        <td class="p-3">
          <?php if (in_array($user['role'] ?? '', ['admin','wali_kelas'])): ?>
          <a class="text-sky-600 underline" target="_blank" rel="noopener" title="Cetak sertifikat" href="/cetak-sertifikat?siswa_id=<?= (int)$s['id'] ?>"><i class="fa-solid fa-award"></i> Sertifikat</a>
          <?php else: ?><span class="text-slate-400">read-only</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

<?php if (in_array($user['role'] ?? '', ['kepsek','wakasek'])): ?>
<div class="mt-4 text-xs p-3 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300">
  Anda login sebagai <b><?= htmlspecialchars($user['role']) ?></b> — mode read-only/dashboard view sesuai hak akses.
</div>
<?php endif; ?>

<script>
// Data dari server (Fullstack PHP render langsung, tanpa SPA)
const trenLabels = <?= json_encode($tren['labels']) ?>;
const trenLanggar = <?= json_encode($tren['langgar']) ?>;
const trenHarga = <?= json_encode($tren['harga']) ?>;
const pieLabels = <?= json_encode($pie['labels']) ?>;
const pieData = <?= json_encode($pie['data']) ?>;

new Chart(document.getElementById('chartTren'), {
  type: 'line',
  data: { labels: trenLabels,
    datasets: [
      { label: 'Pelanggaran', data: trenLanggar, tension: .35, fill: true },
      { label: 'Penghargaan', data: trenHarga, tension: .35, fill: true }
    ]},
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('chartPie'), {
  type: 'pie',
  data: { labels: pieLabels.length ? pieLabels : ['Belum ada data'],
    datasets: [{ data: pieData.length ? pieData : [1] }] },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
