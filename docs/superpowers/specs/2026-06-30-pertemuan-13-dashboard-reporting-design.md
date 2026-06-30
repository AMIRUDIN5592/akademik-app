# Pertemuan 13 Dashboard dan Reporting Design

## Tujuan

Melengkapi aplikasi akademik Laravel 13 sesuai materi praktik utama pada `Pertemuan_13_Laravel_13_Dashboard_Reporting_Amirudin.pptx`: dashboard akademik, chart berbasis data database, laporan nilai berfilter, grade, pagination yang mempertahankan filter, serta pembatasan akses berdasarkan role.

Bagian "Tugas Mandiri" pada slide 42, termasuk laporan mahasiswa per jurusan tambahan, tidak termasuk ruang lingkup berdasarkan keputusan pengguna.

## Pendekatan

Implementasi mengadaptasi contoh materi ke struktur project yang sudah ada. Nama tabel, kolom, relasi, middleware, dan layout existing dipertahankan. Contoh slide tidak disalin secara literal jika dapat menimbulkan duplikasi query atau benturan dengan kode sebelumnya.

## Arsitektur

### Dashboard

- `DashboardService` menghitung total mahasiswa, total mata kuliah, total nilai, rata-rata nilai, nilai tertinggi, dan nilai terendah.
- Service juga menyediakan distribusi grade A–E, agregasi jumlah mahasiswa per jurusan, dan lima nilai terbaru dengan eager loading.
- `DashboardController` hanya mengambil hasil service dan memetakan label/data chart ke view.
- `dashboard/index.blade.php` tidak menjalankan query database.

### Laporan Nilai

- Route `GET /laporan/nilai` menggunakan middleware `auth` dan `role:admin,dosen` dengan nama `laporan.nilai`.
- `LaporanNilaiService` membangun query terfilter untuk mahasiswa, mata kuliah, nilai minimum, dan nilai maksimum.
- Satu builder filter privat digunakan oleh hasil terpaginasikan dan summary agar perilakunya konsisten.
- Query tabel memakai eager loading `mahasiswa.jurusan` dan `mataKuliah`, urutan data terbaru, `paginate(10)`, serta `withQueryString()`.
- `LaporanNilaiController` memvalidasi request dan menyediakan data dropdown mahasiswa dan mata kuliah.
- `laporan/nilai.blade.php` hanya merender filter, summary, tabel, badge grade, dan pagination.

### Grade

- Model `Nilai` menyediakan accessor `grade`.
- Batas grade mengikuti materi: A 85–100, B 75–84, C 65–74, D 50–64, dan E 0–49.

## Alur Data

1. User terautentikasi membuka dashboard.
2. Controller meminta summary, distribusi nilai, mahasiswa per jurusan, dan nilai terbaru dari `DashboardService`.
3. View menerima array/collection siap render dan mengirim data chart ke JavaScript menggunakan `@json`.
4. Admin atau dosen membuka laporan nilai melalui route terproteksi.
5. Controller memvalidasi query string, lalu service menerapkan filter yang sama pada tabel dan summary.
6. Pagination mempertahankan seluruh query string filter.

## Desain Tampilan

### Dashboard

- Header "Dashboard Akademik", keterangan ringkas, dan tombol "Buka Laporan Nilai" khusus admin/dosen.
- Baris KPI pertama: Total Mahasiswa, Total Mata Kuliah, dan Total Nilai.
- Baris KPI kedua: Rata-rata Nilai, Nilai Tertinggi, dan Nilai Terendah.
- Chart batang untuk distribusi nilai A–E.
- Chart doughnut untuk mahasiswa per jurusan.
- Tabel responsif lima nilai terbaru.
- Chart.js dimuat satu kali dari layout utama dan script halaman ditempatkan melalui `@stack('scripts')`.

### Laporan Nilai

- Header, deskripsi, dan tombol kembali ke dashboard.
- Form GET berisi dropdown mahasiswa, dropdown mata kuliah, nilai minimum, nilai maksimum, tombol Filter, dan tombol Reset.
- Empat kartu summary: total data, rata-rata, tertinggi, dan terendah.
- Tabel responsif berisi nomor, NIM, mahasiswa, jurusan, mata kuliah, SKS, nilai, dan grade.
- Grade menggunakan badge warna sesuai kategori.
- Empty state ditampilkan ketika filter tidak menghasilkan data.

### Navigasi dan Role

- Dashboard tersedia untuk semua user yang sudah login.
- Menu dan tombol laporan hanya terlihat untuk admin dan dosen.
- Admin dan dosen dapat membuka laporan nilai.
- Mahasiswa menerima HTTP 403 ketika mencoba membuka URL laporan secara langsung.
- Guest diarahkan ke halaman login.

## Validasi dan Kondisi Tepi

- `mahasiswa_id` harus mengacu ke tabel `mahasiswas` jika diisi.
- `mata_kuliah_id` harus mengacu ke tabel `mata_kuliahs` jika diisi.
- `nilai_min` dan `nilai_max` bersifat opsional, numerik, serta berada pada rentang 0–100.
- `nilai_max` harus lebih besar atau sama dengan `nilai_min` ketika keduanya diisi.
- Agregasi tanpa data menghasilkan `0`, bukan `null` atau error.
- Data chart kosong tetap dikirim sebagai label/data JSON yang valid.
- Relasi opsional pada view menggunakan fallback `-` agar rendering tetap aman.

## Strategi Pengujian

Pengembangan menggunakan TDD. Feature test baru harus membuktikan:

- guest tidak dapat mengakses dashboard dan laporan;
- admin dan dosen dapat mengakses laporan;
- mahasiswa menerima 403 pada laporan;
- dashboard menampilkan enam KPI yang benar;
- distribusi grade dan jumlah mahasiswa per jurusan benar;
- accessor grade mengikuti seluruh batas A–E;
- filter mahasiswa, mata kuliah, nilai minimum, dan nilai maksimum bekerja;
- kombinasi filter memengaruhi tabel dan summary secara konsisten;
- rentang nilai terbalik ditolak oleh validasi;
- pagination mempertahankan query string;
- data kosong menghasilkan summary nol dan empty state.

Verifikasi akhir menjalankan seluruh test suite, Laravel Pint dalam mode pemeriksaan, daftar route, kompilasi view, serta smoke test halaman terautentikasi.

## Kriteria Selesai

- Seluruh output praktik utama slide 4 tersedia.
- Struktur file pada slide 8 terwujud tanpa merusak fitur pertemuan sebelumnya.
- Checklist clean code slide 39 terpenuhi.
- Seluruh skenario testing slide 36 memiliki bukti test otomatis.
- Seluruh test existing dan test Pertemuan 13 lulus.
- Aplikasi dapat dijalankan dengan konfigurasi project yang tersedia tanpa exception pada alur dashboard dan laporan nilai.
