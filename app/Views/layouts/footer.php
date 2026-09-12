</main>
<footer class="max-w-7xl mx-auto px-4 py-6 text-xs text-slate-500 dark:text-slate-400">
  Sistem Pembinaan Kedisiplinan & Karakter — <?= htmlspecialchars($setting['nama_sekolah'] ?? '') ?> • SP1 (25–50) • SP2 (51–75) • SP3 (≥76) • Force Majeure → Pengembalian
</footer>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
  // Semua dropdown <select> otomatis jadi searchable (Select2)
  $('select').each(function(){
    var $s = $(this);
    var hasEmpty = $s.find('option[value=""]').length > 0;
    $s.select2({
      width: '100%',
      placeholder: hasEmpty ? ($s.find('option[value=""]').first().text() || 'Pilih...') : null,
      allowClear: hasEmpty && !$s.prop('required')
    });
  });
  // Semua tabel konten otomatis pakai DataTables (kecuali .no-dt)
  if ($.fn.DataTable) {
    $('main table:not(.no-dt)').each(function(){
      if ($(this).hasClass('dataTable')) return;
      $(this).DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        pagingType: 'simple_numbers',
        order: [],
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampil _MENU_ data',
          info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(disaring dari _MAX_ total data)',
          zeroRecords: 'Data tidak ditemukan',
          emptyTable: 'Belum ada data',
          paginate: { first: '«', last: '»', next: '›', previous: '‹' }
        }
      });
    });
  }
});
</script>
</body>
</html>
