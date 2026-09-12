# Panduan Instalasi Cepat (Laragon)
1. Buat DB via HeidiSQL/phpMyAdmin lalu import `database/schema.sql`.
2. Sesuaikan `config/config.php` (user/pass DB, BASE_URL).
3. Di Laragon: Menu > Apache > Sites -> buat virtual host `tatibapp.test` menunjuk ke `C:\laragon\www\tatibapp\public`.
4. Pastikan `Apache mod_rewrite` aktif. Folder `public/uploads/bukti` & `logo` writable.
5. Login awal: admin/admin123 (segera ganti password!). Kepsek/wakasek bersifat read-only di dashboard.

# Aturan Poin
- Pelanggaran: 25-50 SP1, 51-75 SP2, >=76 SP3, Force Majeure -> PENGEMBALIAN + status siswa= dikembalikan.
- Penghargaan: 100-125 BERPRESTASI, 126-150 HADIAH, >=151 WALUYA_UTAMA. Top skor tahunan tetap difilter untuk gelar walau <151.
