=====================================================================
SITATIB — Sistem Manajemen Tata Tertib & Penghargaan Siswa
SMK Negeri 1 Krangkeng
Fullstack PHP (MVC murni, server-side render) + MySQL + Tailwind CSS
Chart.js (grafik) • Select2 (dropdown cari) • DataTables (tabel)
FontAwesome (ikon) • Dark Mode • Responsive mobile
=====================================================================

1. KEBUTUHAN
- Laragon (Apache + PHP 8.1+ dengan ekstensi zip, xml, mbstring + MySQL 8)
- Browser modern + koneksi internet (CDN: Tailwind, Chart.js, jQuery,
  Select2, DataTables, FontAwesome, Google Fonts opsional)

2. INSTALASI CEPAT (Laragon)
1. Import database: via HeidiSQL/phpMyAdmin, jalankan
   `database/schema.sql` (otomatis buat DB `tatibapp` + data dummy).
   Atau via terminal:  mysql -u root < database/schema.sql
2. Sesuaikan `config/config.php` (host/user/pass DB, BASE_URL).
3. VirtualHost `tatibapp.test` WAJIB mengarah ke folder `public`:
   C:\laragon\www\tatibapp\public   (otomatis bila pakai Laragon magic host)
4. Pastikan Apache mod_rewrite aktif.
5. Pastikan folder `public/uploads/bukti`, `public/uploads/logo`,
   dan `public/assets/template` bisa ditulis (writable).
6. Buka http://tatibapp.test/login (https juga bisa, abaikan peringatan
   sertifikat self-signed Laragon).

3. AKUN DEFAULT (data dummy — SEGERA GANTI PASSWORD!)
+--------------+-------------+-------------+---------------------------+
| Role         | Username    | Password    | Hak akses                 |
+--------------+-------------+-------------+---------------------------+
| Admin        | admin       | admin123    | Penuh: master, user,      |
|              |             |             | validasi, cetak, setting  |
| Kepala       | kepsek      | kepsek123   | Read-only: dashboard +    |
| Sekolah      |             |             | grafik + laporan          |
| Wakasek      | wakasek     | wakasek123  | Sama seperti Kepsek       |
| Wali Kelas   | wali        | wali123     | Siswa/validasi/cetak,     |
|              |             |             | hanya untuk kelasnya      |
| OSIS         | osis        | osis123     | Melapor pelanggaran &     |
|              |             |             | penghargaan (pending)     |
+--------------+-------------+-------------+---------------------------+
Matriks lengkap ada di halaman Pengguna (login sebagai admin).

4. FITUR UTAMA
- Dashboard: kartu statistik, daftar pembinaan SP1/SP2/SP3, kandidat
  Anugerah Waluya Utama, grafik tren (filter tahun) + pie jenis
  pelanggaran (filter bulan & tahun).
- Lapor Pelanggaran (dengan foto bukti opsional) + validasi berjenjang.
- Data Siswa: CRUD, import .xlsx (template di halaman Import XLSX),
  riwayat poin, cetak SP/sertifikat (tab baru).
- Laporan: 3 tab (pelanggaran/penghargaan/rekap) + filter + cetak + CSV.
- Master: Kelas, Jenis Pelanggaran (bobot poin), Jenis Penghargaan.
- Pengaturan Sekolah (nama, alamat, TA, logo) + Tampilan (warna primer,
  font, mode gelap default, CSS tambahan) + Pengguna (role/permission).

5. ATURAN POIN
- Pelanggaran : 25-50 => SP1, 51-75 => SP2, >=76 => SP3.
  Force Majeure (hukum/asusila) => PENGEMBALIAN + status dikembalikan.
- Penghargaan : 100-125 => Sertifikat Berprestasi,
  126-150 => Sertifikat + Hadiah, >=151 => Anugerah Waluya Utama.
  Peringkat teratas tahunan tetap ditampilkan walau < 151.

6. STRUKTUR PROYEK
app/Core        : Database, Router, Auth, View
app/Controllers : Auth, Dashboard, Laporan, Report, Siswa, Validasi,
                  Master, Setting, Tampilan, User, Cetak
app/Models      : Setting, Dashboard, Laporan, Master, Kelas, Siswa,
                  Validasi, Report, User, Tampilan
app/Helpers     : PoinRule, UploadHelper, Permissions, XlsxReader
app/Views       : layouts, dashboard, laporan, siswa, validasi, master,
                  setting, users, cetak, auth
config          : config.php (DB, upload, BASE_URL)
database        : schema.sql (struktur + dummy), seed-dummy.sql
                  (refresh data contoh), make-template-siswa.php
public          : index.php (front controller), .htaccess,
                  assets/css/custom.css (kustomisasi),
                  assets/template/template-siswa.xlsx, uploads/
