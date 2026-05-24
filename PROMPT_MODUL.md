## "Sistem Informasi Wali Santri Berbasis Web dan Mobile (WaliSantri App)"

---

## 🎯 KONTEKS & INSTRUKSI UTAMA

Kamu adalah seorang penulis modul akademik profesional yang berpengalaman membuat dokumentasi teknis untuk tugas akhir / final project mahasiswa di Indonesia. Tugasmu adalah membuat **modul akademik lengkap** dengan judul:

> **"MODUL PENGEMBANGAN SISTEM INFORMASI WALI SANTRI BERBASIS WEB DAN MOBILE"**
> **"Studi Kasus: Pondok Pesantren — Menggunakan Framework Laravel dan Flutter"**

Modul ini merupakan dokumen akademik resmi yang disusun untuk memenuhi persyaratan **Final Project** program studi Teknik Informatika / Sistem Informasi. Modul harus ditulis dalam **Bahasa Indonesia yang baku, formal, dan akademis** sesuai standar penulisan karya ilmiah Indonesia (EYD/PUEBI).

---

## 📋 IDENTITAS DOKUMEN

| Atribut | Detail |
|---|---|
| **Judul Lengkap** | Modul Pengembangan Sistem Informasi Wali Santri Berbasis Web dan Mobile |
| **Sub-judul** | Studi Kasus Pondok Pesantren Menggunakan Framework Laravel dan Flutter |
| **Mata Kuliah** | Final Project / Proyek Akhir |
| **Program Studi** | Teknik Informatika / Sistem Informasi |
| **Total Halaman** | ±250 halaman |
| **Bagian Web (Laravel)** | ±120 halaman (Bab I s.d. Bab V) |
| **Bagian Mobile (Flutter)** | ±130 halaman (Bab VI s.d. Bab X) — dikerjakan oleh anggota tim lain |
| **Bahasa** | Bahasa Indonesia (formal, akademis, baku) |
| **Format Penulisan** | Mengikuti pedoman penulisan karya ilmiah Indonesia |

---

## 🏗️ SPESIFIKASI TEKNIS PROYEK (REFERENSI WAJIB)

Seluruh konten modul harus merujuk pada spesifikasi teknis proyek nyata berikut:

### Stack Teknologi Web (Laravel)
- **Framework Backend:** Laravel 11 (PHP)
- **Database:** SQLite (development) / MySQL (production)
- **ORM:** Eloquent ORM
- **Authentication:** Laravel Sanctum (API Token)
- **Payment Gateway:** Midtrans (Snap API + Webhook)
- **Notifikasi OTP:** WhatsApp API
- **Template Engine:** Blade Template Engine
- **Frontend Web:** Vanilla CSS + JavaScript (Alpine.js style)
- **Web Server:** Laragon (development) / Apache (production)
- **Deployment:** Server hosting dengan domain `dbs-santriapp.kypau.my.id`
- **Base URL API:** `https://dbs-santriapp.kypau.my.id/api`

### Struktur Database (11 Tabel Utama)
1. **users** — data akun (id, name, role, phone, phone_verified_at, password, remember_token, timestamps)
2. **students** — data santri (id, user_id, name, nis, nisn, class, room, gender, birth_date, father_name, mother_name, father_phone, mother_phone, address, photo, barcode_id, enrollment_year, status, timestamps)
3. **bills** — data tagihan (id, student_id, title, description, amount, paid_amount, status, type, month, year, due_date, timestamps)
4. **payments** — data pembayaran (id, student_id, bill_id, amount, payment_method, transaction_id, order_id, snap_token, status, paid_at, midtrans_response, timestamps)
5. **savings** — data tabungan (id, student_id, balance, timestamps)
6. **saving_transactions** — transaksi tabungan (id, student_id, saving_id, type, amount, description, transaction_id, order_id, balance_after, status, timestamps)
7. **exams** — data ujian (id, title, subject, description, teacher_name, exam_date, start_time, end_time, duration_minutes, status, timestamps)
8. **exam_questions** — soal ujian (id, exam_id, question_text, question_type, options, answer_key, points, order, timestamps)
9. **exam_answers** — jawaban ujian (id, exam_id, student_id, question_id, answer_text, is_correct, score, access_token, submitted_at, timestamps)
10. **reports** — raport (id, student_id, semester, academic_year, grades, average_score, rank, notes, published_at, timestamps)
11. **otp_verifications** — verifikasi OTP (id, user_id, phone, otp_code, expires_at, verified_at, timestamps)

### Struktur MVC Laravel
- **Controllers/Admin/:** AdminController.php, ExamAdminController.php
- **Controllers/Api/:** AuthController.php, BillController.php, DashboardController.php, ExamController.php, PaymentController.php, ProfileController.php, ReportController.php, SavingController.php
- **Controllers/Web/:** AuthController.php, DashboardController.php, ExamController.php, MidtransController.php
- **Models:** User, Student, Bill, Payment, Saving, SavingTransaction, Exam, ExamQuestion, ExamAnswer, Report, OtpVerification
- **Middleware:** EnsurePhoneVerified, EnsureIsAdmin

### Fitur Utama Sistem
**Panel Wali Santri (Web):**
1. Registrasi & Login (matching nama santri + NIS)
2. Verifikasi OTP via WhatsApp
3. Dashboard (statistik tagihan, tabungan, ujian)
4. Manajemen Profil Santri (update kontak, upload foto)
5. Manajemen Tagihan (lihat, bayar via Midtrans)
6. Riwayat Pembayaran
7. Tabungan Santri (saldo + top up via Midtrans)
8. Ujian Online (via WebView/Web dengan token akses unik)
9. Raport / Nilai Akademik (unduh PDF)

**Panel Admin (Web):**
1. Dashboard Admin (statistik global)
2. Manajemen Data Santri (CRUD)
3. Manajemen Tagihan (buat tagihan per santri)
4. Monitoring Pembayaran
5. Manajemen Raport (buat, publikasi)
6. Manajemen Ujian (CRUD soal, monitoring hasil, penilaian essay)

**API (untuk Flutter):**
- 25+ endpoint RESTful API
- Auth: POST /login, POST /register, POST /verify-otp, POST /resend-otp, POST /logout, GET /user
- Dashboard: GET /dashboard
- Profile: GET /profile, PUT /profile, GET /profile/photo, POST /profile/photo, POST /change-password
- Bills: GET /bills, GET /bills/{id}, POST /bills/pay, POST /bills/check-status, GET /bills/history/paid
- Payments: GET /payments, GET /payments/{id}
- Savings: GET /savings, POST /savings/topup, GET /savings/history
- Exams: GET /exams, POST /exams/start, POST /exams/save-answer, POST /exams/submit, GET /exams/result
- Reports: GET /reports, GET /reports/{id}, GET /reports/download/{id}

---

## 📚 STRUKTUR LENGKAP MODUL (DAFTAR ISI DETAIL)

> ⚠️ **PENTING:** Setiap nomor halaman di bawah adalah **estimasi** dan panduan. Kamu harus mengisi konten secara detail sehingga setiap bagian mencapai jumlah halaman yang ditargetkan. Gunakan penjelasan mendalam, gambar/diagram (deskripsikan sebagai "[Gambar X.X: Deskripsi]"), tabel, dan contoh kode nyata dari proyek ini.

---

### HALAMAN AWAL (±10 halaman)

```
Halaman Sampul ............................................................................  i
Lembar Persetujuan Pembimbing ........................................................ ii
Lembar Pengesahan ....................................................................... iii
Kata Pengantar ...........................................................................  iv-v
Daftar Isi ..................................................................................  vi-viii
Daftar Gambar .............................................................................  ix-x
Daftar Tabel ...............................................................................  xi
Daftar Kode Program ....................................................................  xii
```

---

### BAB I — PENDAHULUAN (Target: 10 halaman | Hal. 1–10)

```
1.1  Latar Belakang ....................................................................  1-3
1.2  Rumusan Masalah ..................................................................  4
1.3  Tujuan Pembangunan Sistem .....................................................  4-5
1.4  Manfaat Sistem ....................................................................  5-6
1.5  Batasan Masalah ...................................................................  6-7
1.6  Metodologi Pengembangan Sistem ..............................................  7-8
1.7  Sistematika Penulisan Modul ....................................................  9-10
```

#### Panduan Konten Bab I:

**1.1 Latar Belakang (3 halaman penuh)**
- Deskripsikan peran pondok pesantren dalam sistem pendidikan Indonesia
- Jelaskan masalah komunikasi antara pihak pesantren dan orang tua/wali santri secara konvensional: pembayaran manual, raport fisik, tidak ada monitoring realtime
- Data/fakta pendukung tentang digitalisasi pesantren di Indonesia
- Pentingnya aplikasi berbasis web dan mobile untuk modernisasi administrasi pesantren
- Gambaran umum solusi yang dibangun: WaliSantri App (web + mobile)
- Paragraf penutup yang menegaskan urgensi proyek

**1.2 Rumusan Masalah**
Tuliskan minimal 5 rumusan masalah berformat pertanyaan ilmiah:
1. Bagaimana merancang sistem informasi berbasis web yang memudahkan admin pesantren dalam mengelola data santri, tagihan, dan ujian?
2. Bagaimana membangun REST API menggunakan Laravel Sanctum yang aman dan dapat dikonsumsi oleh aplikasi Flutter?
3. Bagaimana mengintegrasikan payment gateway Midtrans untuk pemrosesan pembayaran tagihan dan top-up tabungan secara online?
4. Bagaimana merancang sistem ujian online berbasis web yang terintegrasi dengan aplikasi mobile melalui mekanisme token akses unik?
5. Bagaimana membangun sistem notifikasi OTP berbasis WhatsApp untuk verifikasi nomor telepon wali santri?

**1.3 Tujuan Pembangunan Sistem**
- Tujuan umum (1 paragraf)
- Tujuan khusus (minimal 6 poin)

**1.4 Manfaat Sistem**
- Manfaat bagi Wali Santri
- Manfaat bagi Admin/Pihak Pesantren
- Manfaat bagi Santri
- Manfaat Akademis

**1.5 Batasan Masalah**
Minimal 6 batasan yang jelas dan terukur, contoh:
- Sistem hanya mengelola data santri dalam satu pondok pesantren
- Pembayaran hanya melalui payment gateway Midtrans
- Fitur ujian online hanya mendukung tipe soal pilihan ganda dan essay
- dst.

**1.6 Metodologi**
Jelaskan metodologi pengembangan yang digunakan (misalnya Waterfall atau Agile/Scrum). Sertakan diagram alur metodologi [Gambar 1.1: Diagram Alur Metodologi Pengembangan Sistem].

**1.7 Sistematika Penulisan**
Jelaskan isi setiap bab secara ringkas.

---

### BAB II — LANDASAN TEORI (Target: 20 halaman | Hal. 11–30)

```
2.1  Sistem Informasi Manajemen .....................................................  11-12
2.2  Pondok Pesantren dan Digitalisasi Administrasi ...............................  12-13
2.3  Pengembangan Aplikasi Berbasis Web ...........................................  13-14
2.4  Framework Laravel .................................................................  14-16
     2.4.1  Arsitektur MVC (Model-View-Controller) ...............................  14-15
     2.4.2  Eloquent ORM .............................................................  15
     2.4.3  Blade Template Engine ....................................................  15-16
     2.4.4  Middleware dan Request Lifecycle ........................................  16
2.5  RESTful API dan Arsitektur Client-Server ......................................  16-18
     2.5.1  Konsep REST (Representational State Transfer) .........................  16-17
     2.5.2  HTTP Methods dan Status Code .............................................  17
     2.5.3  JSON sebagai Format Data API ............................................  17-18
2.6  Laravel Sanctum untuk Autentikasi API .........................................  18-19
2.7  Payment Gateway Midtrans .......................................................  19-20
     2.7.1  Konsep Payment Gateway ..................................................  19
     2.7.2  Midtrans Snap API ........................................................  19-20
     2.7.3  Webhook dan Notifikasi Pembayaran .......................................  20
2.8  One-Time Password (OTP) dan Verifikasi Nomor Telepon ......................  20-21
2.9  Framework Flutter (Tinjauan Singkat untuk Integrasi) .........................  21-22
2.10 Basis Data Relasional dan SQLite/MySQL ........................................  22-23
2.11 Konsep Keamanan Aplikasi Web ...................................................  23-24
     2.11.1  CSRF Protection .........................................................  23
     2.11.2  SQL Injection Prevention ................................................  23-24
     2.11.3  Authentication dan Authorization ........................................  24
2.12 Penelitian Terdahulu / Kajian Literatur ...........................................  24-30
```

#### Panduan Konten Bab II:

- Setiap sub-bab harus memiliki **definisi dari minimal 2 sumber pustaka** (format: Nama Penulis, Tahun)
- Sub-bab 2.4 (Laravel): jelaskan secara detail arsitektur Laravel, sertakan diagram MVC [Gambar 2.1: Arsitektur MVC Laravel], contoh struktur folder proyek nyata
- Sub-bab 2.5 (REST API): buat tabel perbandingan HTTP Methods, sertakan contoh request/response format JSON
- Sub-bab 2.7 (Midtrans): jelaskan alur pembayaran dari frontend hingga webhook, sertakan diagram [Gambar 2.2: Alur Pembayaran Midtrans]
- Sub-bab 2.12: tuliskan minimal 5 penelitian terdahulu yang relevan (sistem informasi pesantren, payment gateway, dll) dalam format tabel perbandingan

---

### BAB III — ANALISIS DAN PERANCANGAN SISTEM (Target: 30 halaman | Hal. 31–60)

```
3.1  Analisis Sistem yang Berjalan ...................................................  31-33
     3.1.1  Gambaran Umum Proses Bisnis Saat Ini ..................................  31-32
     3.1.2  Identifikasi Masalah Sistem Lama ........................................  32-33
     3.1.3  Usulan Perbaikan .........................................................  33
3.2  Analisis Kebutuhan Sistem .......................................................  33-37
     3.2.1  Kebutuhan Fungsional ....................................................  33-35
     3.2.2  Kebutuhan Non-Fungsional ................................................  35-36
     3.2.3  Kebutuhan Perangkat Keras ...............................................  36
     3.2.4  Kebutuhan Perangkat Lunak ...............................................  36-37
3.3  Perancangan Use Case Diagram ...................................................  37-40
     3.3.1  Use Case Diagram Wali Santri (Web) .....................................  37-38
     3.3.2  Use Case Diagram Admin (Web) ...........................................  38-39
     3.3.3  Skenario Use Case (Narasi) ...............................................  39-40
3.4  Perancangan Activity Diagram ....................................................  40-43
     3.4.1  Activity Diagram Login dan Verifikasi OTP ...............................  40-41
     3.4.2  Activity Diagram Pembayaran Tagihan .....................................  41-42
     3.4.3  Activity Diagram Top-Up Tabungan .......................................  42
     3.4.4  Activity Diagram Ujian Online ............................................  42-43
3.5  Perancangan Sequence Diagram ..................................................  43-46
     3.5.1  Sequence Diagram Login API .............................................  43-44
     3.5.2  Sequence Diagram Pembayaran via Midtrans ..............................  44-45
     3.5.3  Sequence Diagram Ujian Online ...........................................  45-46
3.6  Perancangan Database (Entity-Relationship Diagram) ..........................  46-52
     3.6.1  Entity-Relationship Diagram (ERD) .......................................  46-47
     3.6.2  Struktur Tabel users ......................................................  47
     3.6.3  Struktur Tabel students ...................................................  47-48
     3.6.4  Struktur Tabel bills ......................................................  48
     3.6.5  Struktur Tabel payments .................................................  48-49
     3.6.6  Struktur Tabel savings dan saving_transactions ..........................  49
     3.6.7  Struktur Tabel exams, exam_questions, exam_answers ...................  49-50
     3.6.8  Struktur Tabel reports ...................................................  50-51
     3.6.9  Struktur Tabel otp_verifications ..........................................  51
     3.6.10 Relasi Antar Tabel .......................................................  51-52
3.7  Perancangan Arsitektur Sistem ..................................................  52-55
     3.7.1  Arsitektur Keseluruhan (Web + Mobile + API) ............................  52-53
     3.7.2  Arsitektur Laravel MVC ..................................................  53-54
     3.7.3  Arsitektur Integrasi Midtrans .............................................  54-55
3.8  Perancangan Antarmuka (UI/UX) Web ...........................................  55-60
     3.8.1  Wireframe Halaman Login ................................................  55-56
     3.8.2  Wireframe Dashboard Wali Santri .........................................  56-57
     3.8.3  Wireframe Halaman Tagihan ..............................................  57
     3.8.4  Wireframe Dashboard Admin .............................................  57-58
     3.8.5  Perancangan Navigasi Sistem ............................................  58-59
     3.8.6  Pemilihan Skema Warna dan Tipografi ....................................  59-60
```

#### Panduan Konten Bab III:

- **3.3 Use Case Diagram:** Wali Santri memiliki use case: Login, Registrasi, Verifikasi OTP, Lihat Dashboard, Kelola Profil, Lihat Tagihan, Bayar Tagihan, Lihat Riwayat Bayar, Lihat Tabungan, Top-Up Tabungan, Ikut Ujian, Lihat Raport, Download Raport, Ubah Password. Admin memiliki use case: Login, Dashboard Admin, Kelola Santri (CRUD), Buat Tagihan, Monitoring Pembayaran, Buat Raport, Kelola Ujian (CRUD Soal, Nilai Essay).
- **3.4 Activity Diagram:** Buat narasi detail untuk setiap activity diagram. Sertakan notasi keputusan (decision node) yang jelas.
- **3.6 Struktur Tabel:** Buat dalam format tabel dengan kolom: No, Nama Kolom, Tipe Data, Panjang, Keterangan (PK/FK/NULL). Gunakan spesifikasi database nyata dari proyek.
- **3.7 Arsitektur Sistem:** Sertakan diagram arsitektur [Gambar 3.X] yang menunjukkan hubungan antara: Browser/Flutter App → HTTP/API Request → Laravel Router → Middleware → Controller → Model → Database → Response.

---

### BAB IV — IMPLEMENTASI SISTEM WEB (Target: 45 halaman | Hal. 61–105)

```
4.1  Lingkungan Pengembangan .......................................................  61-63
     4.1.1  Instalasi dan Konfigurasi Laravel ........................................  61-62
     4.1.2  Konfigurasi Database dan Environment ...................................  62
     4.1.3  Konfigurasi Midtrans .....................................................  62-63
     4.1.4  Konfigurasi Laravel Sanctum ............................................  63
4.2  Implementasi Database ..........................................................  63-68
     4.2.1  Migration users ..........................................................  63-64
     4.2.2  Migration students .......................................................  64-65
     4.2.3  Migration bills ..........................................................  65
     4.2.4  Migration payments (termasuk kolom Midtrans) .........................  65-66
     4.2.5  Migration savings dan saving_transactions ...............................  66
     4.2.6  Migration exams, exam_questions, exam_answers .........................  66-67
     4.2.7  Migration reports ........................................................  67
     4.2.8  Migration otp_verifications ...............................................  67-68
4.3  Implementasi Model Eloquent ....................................................  68-73
     4.3.1  Model User .................................................................  68-69
     4.3.2  Model Student ..............................................................  69-70
     4.3.3  Model Bill dan Payment ...................................................  70-71
     4.3.4  Model Saving dan SavingTransaction .....................................  71-72
     4.3.5  Model Exam, ExamQuestion, ExamAnswer .................................  72-73
     4.3.6  Model Report dan OtpVerification .........................................  73
4.4  Implementasi Routing ............................................................  73-76
     4.4.1  Web Routes (routes/web.php) .............................................  73-74
     4.4.2  API Routes (routes/api.php) ..............................................  74-75
     4.4.3  Penjelasan Grup Route dan Middleware ....................................  75-76
4.5  Implementasi Middleware ........................................................  76-78
     4.5.1  Middleware EnsurePhoneVerified ..........................................  76-77
     4.5.2  Middleware EnsureIsAdmin ................................................  77-78
4.6  Implementasi Panel Wali Santri (Web) ...........................................  78-92
     4.6.1  Implementasi Halaman Login .............................................  78-80
     4.6.2  Implementasi Halaman Registrasi .........................................  80-81
     4.6.3  Implementasi Verifikasi OTP .............................................  81-83
     4.6.4  Implementasi Dashboard .................................................  83-85
     4.6.5  Implementasi Halaman Profil .............................................  85-86
     4.6.6  Implementasi Halaman Tagihan ...........................................  86-88
     4.6.7  Implementasi Pembayaran via Midtrans ...................................  88-89
     4.6.8  Implementasi Halaman Riwayat Pembayaran ..............................  89-90
     4.6.9  Implementasi Halaman Tabungan .........................................  90-91
     4.6.10 Implementasi Top-Up Tabungan via Midtrans .............................  91-92
     4.6.11 Implementasi Ujian Online (Web) .........................................  92-94
     4.6.12 Implementasi Halaman Raport ............................................  94-95
4.7  Implementasi Panel Admin (Web) ................................................  95-105
     4.7.1  Implementasi Dashboard Admin ...........................................  95-97
     4.7.2  Implementasi Manajemen Data Santri .....................................  97-99
     4.7.3  Implementasi Form Tambah & Edit Santri .................................  99-100
     4.7.4  Implementasi Manajemen Tagihan .........................................  100-101
     4.7.5  Implementasi Monitoring Pembayaran .....................................  101-102
     4.7.6  Implementasi Manajemen Ujian ...........................................  102-103
     4.7.7  Implementasi Bank Soal & Penilaian Essay ................................  103-104
     4.7.8  Implementasi Manajemen Raport ..........................................  104-105
```

#### Panduan Konten Bab IV:

- **SETIAP sub-bab implementasi WAJIB menyertakan:**
  a) Penjelasan tujuan / fungsi fitur tersebut (1 paragraf)
  b) **Potongan kode program nyata** (PHP/Blade) dengan komentar penjelasan — ambil dari kode proyek yang ada
  c) **Screenshot antarmuka** yang direpresentasikan sebagai: `[Gambar 4.X: Tampilan Halaman NAMA]`
  d) Penjelasan alur kerja kode (narrative walkthrough)

- **4.2 Migration:** Tampilkan kode migrasi lengkap untuk setiap tabel. Berikan penjelasan setiap kolom.

- **4.3 Model:** Tampilkan kode model Eloquent beserta relasi ($fillable, $casts, hasMany, belongsTo, dll). Jelaskan mengapa setiap relasi didefinisikan.

- **4.4 Routing:** Tampilkan seluruh isi routes/web.php dan routes/api.php. Jelaskan perbedaan middleware grup.

- **4.6 Panel Wali Santri:** Untuk setiap halaman:
  - Controller method yang menangani halaman tersebut (kode lengkap)
  - Blade view yang relevan (bagian utama)
  - Penjelasan logika bisnis
  - Screenshot halaman

- **4.7 Panel Admin:** Fokus pada AdminController.php dan ExamAdminController.php. Jelaskan fitur CRUD, validasi form, dan logika bisnis (contoh: otomatis membuat tabungan saat santri baru ditambahkan).

---

### BAB V — IMPLEMENTASI REST API (Target: 15 halaman | Hal. 106–120)

```
5.1  Konsep dan Desain REST API WaliSantri .........................................  106-107
     5.1.1  Base URL dan Konvensi Endpoint ..........................................  106
     5.1.2  Format Request dan Response ............................................  106-107
     5.1.3  Penanganan Error (Error Handling) ........................................  107
5.2  Implementasi Autentikasi API ...................................................  107-110
     5.2.1  Endpoint POST /api/login ................................................  107-108
     5.2.2  Endpoint POST /api/register .............................................  108-109
     5.2.3  Endpoint POST /api/verify-otp ...........................................  109
     5.2.4  Endpoint POST /api/resend-otp ...........................................  109
     5.2.5  Endpoint POST /api/logout ...............................................  109-110
     5.2.6  Endpoint GET /api/user ..................................................  110
5.3  Implementasi Endpoint Dashboard dan Profil ....................................  110-111
     5.3.1  Endpoint GET /api/dashboard .............................................  110-111
     5.3.2  Endpoint GET & PUT /api/profile ..........................................  111
     5.3.3  Endpoint POST /api/profile/photo ........................................  111
5.4  Implementasi Endpoint Tagihan (Bills) ...........................................  111-113
     5.4.1  Endpoint GET /api/bills ..................................................  111-112
     5.4.2  Endpoint GET /api/bills/{id} .............................................  112
     5.4.3  Endpoint POST /api/bills/pay (Midtrans Integration) .....................  112-113
     5.4.4  Endpoint POST /api/bills/check-status ...................................  113
5.5  Implementasi Endpoint Tabungan (Savings) ......................................  113-114
     5.5.1  Endpoint GET /api/savings ................................................  113
     5.5.2  Endpoint POST /api/savings/topup ........................................  113-114
     5.5.3  Endpoint GET /api/savings/history .......................................  114
5.6  Implementasi Endpoint Ujian (Exams) ............................................  114-116
     5.6.1  Endpoint GET /api/exams .................................................  114-115
     5.6.2  Endpoint POST /api/exams/start ..........................................  115
     5.6.3  Endpoint POST /api/exams/save-answer ...................................  115-116
     5.6.4  Endpoint POST /api/exams/submit ........................................  116
     5.6.5  Endpoint GET /api/exams/result ..........................................  116
5.7  Implementasi Endpoint Raport (Reports) .........................................  116-117
5.8  Integrasi Midtrans Webhook ....................................................  117-118
     5.8.1  Endpoint POST /midtrans/notification ....................................  117-118
     5.8.2  Logika Pemrosesan Notifikasi Pembayaran ................................  118
5.9  Pengujian API dengan Tabel Ringkasan Endpoint ................................  118-120
     5.9.1  Tabel Ringkasan Seluruh Endpoint API ....................................  118-119
     5.9.2  Contoh Pengujian dengan cURL ............................................  119-120
```

#### Panduan Konten Bab V:

- **5.2 s.d. 5.7:** Untuk setiap endpoint, tampilkan:
  - Method HTTP + URL endpoint
  - Middleware/guard yang digunakan
  - Kode controller method (lengkap dengan logika bisnis)
  - Format request JSON (tabel atau code block)
  - Format response JSON sukses dan gagal
  - HTTP Status Code yang dikembalikan

- **5.8 Midtrans Webhook:** Jelaskan secara detail alur: pembayaran user → Midtrans memproses → Midtrans POST ke /midtrans/notification → server memverifikasi signature → server update status payment + bill. Tampilkan kode implementasi webhook handler.

- **5.9 Tabel Ringkasan:** Buat tabel dengan kolom: No | Method | Endpoint | Deskripsi | Auth Required | Response Format

---

### BAGIAN MOBILE (Flutter) — Dikerjakan Tim Lain

> Bab VI s.d. Bab X (±130 halaman) tentang implementasi Flutter mobile app akan dikerjakan oleh anggota tim yang lain. Berikan placeholder atau outline singkat saja untuk bagian ini.

---

### DAFTAR PUSTAKA (Target: 2–3 halaman)

Tuliskan minimal **20 referensi** dalam format APA 7th Edition, mencakup:
- Buku teks Laravel, Flutter, PHP
- Dokumentasi resmi (Laravel.com, Flutter.dev, Midtrans.com)
- Jurnal ilmiah tentang sistem informasi manajemen pesantren
- Jurnal tentang payment gateway di Indonesia
- Artikel tentang REST API design
- Referensi OTP/WhatsApp API

---

### LAMPIRAN (Target: 5 halaman)

```
Lampiran A — Daftar Demo Akun Sistem .............................................  L-1
Lampiran B — Konfigurasi Environment (.env) ........................................  L-2
Lampiran C — Daftar Seluruh Endpoint API ...........................................  L-3
Lampiran D — Konfigurasi Midtrans Webhook .........................................  L-4
Lampiran E — Panduan Instalasi dan Deployment ......................................  L-5
```

---

## ✍️ INSTRUKSI PENULISAN DETAIL (WAJIB DIIKUTI)

### Format Akademik
1. **Bahasa:** Bahasa Indonesia baku, formal. Hindari kata "saya", "kami", "kita" — gunakan bentuk pasif atau sudut pandang orang ketiga.
2. **Paragraf:** Setiap paragraf minimal 4–5 kalimat. Tidak ada paragraf single-sentence.
3. **Referensi:** Setiap definisi atau konsep teoritis harus disertai sitasi (Nama, Tahun).
4. **Judul Bab:** Format: "BAB I", "BAB II", dst. Judul bab ditulis dengan huruf kapital semua dan tebal.
5. **Judul Sub-Bab:** Format: "1.1", "1.1.1", dst. Ditulis tebal.
6. **Nomor Halaman:** Halaman awal (sampul s.d. daftar isi) menggunakan romawi kecil (i, ii, iii...). Isi modul mulai dari angka 1.
7. **Spasi:** 1,5 spasi untuk teks utama.
8. **Font:** Times New Roman 12pt (standar akademik Indonesia).
9. **Margin:** Atas 4cm, Bawah 3cm, Kiri 4cm, Kanan 3cm (standar skripsi Indonesia).

### Format Kode Program
1. Setiap blok kode harus memiliki **Kode Program X.X: Judul Kode** sebagai caption.
2. Gunakan syntax highlighting (PHP, JSON, Dart, SQL).
3. Setiap baris penting dalam kode harus diberi **komentar penjelasan dalam Bahasa Indonesia**.
4. Kode program diambil dari implementasi nyata proyek (bukan kode fiktif/contoh generik).
5. Jika kode terlalu panjang, tampilkan bagian yang paling relevan dan beri keterangan "... (kode dilanjutkan)".

### Format Gambar dan Diagram
1. Setiap gambar/diagram harus memiliki caption: **Gambar X.X: Judul Gambar**
2. Gambar di-referensikan dalam teks: "seperti yang terlihat pada Gambar X.X..."
3. Diagram (Use Case, ERD, Activity, Sequence) harus dijelaskan secara naratif sebelum dan sesudah gambar.
4. Untuk screenshot antarmuka: deskripsikan elemen-elemen UI yang terlihat (header, sidebar, tabel data, tombol, dsb.).

### Format Tabel
1. Setiap tabel memiliki caption di atas: **Tabel X.X: Judul Tabel**
2. Tabel di-referensikan dalam teks: "seperti yang ditunjukkan pada Tabel X.X..."
3. Tabel struktur database wajib memiliki kolom: No | Nama Field | Tipe Data | Panjang | Keterangan
4. Tabel endpoint API wajib memiliki kolom: No | Method | Endpoint | Deskripsi | Auth | Response

---

## 📏 TARGET HALAMAN & DISTRIBUSI KONTEN

| Bab | Judul | Target Halaman |
|-----|-------|----------------|
| Halaman Awal | Sampul, Lembar Pengesahan, Kata Pengantar, Daftar Isi, dll. | ±10 hal |
| BAB I | Pendahuluan | 10 hal |
| BAB II | Landasan Teori | 20 hal |
| BAB III | Analisis dan Perancangan Sistem | 30 hal |
| BAB IV | Implementasi Sistem Web (Panel Wali Santri + Admin) | 45 hal |
| BAB V | Implementasi REST API | 15 hal |
| Daftar Pustaka | Referensi (minimal 20 sumber) | 3 hal |
| Lampiran | Konfigurasi, Demo, dsb. | 5 hal |
| **TOTAL WEB** | | **±120 halaman** |

---

## 🔑 KATA KUNCI TEKNIS (WAJIB MUNCUL DALAM MODUL)

Pastikan istilah-istilah teknis berikut dijelaskan dan digunakan dengan benar:
- Laravel Framework, MVC Architecture, Eloquent ORM, Blade Template
- Laravel Sanctum, Bearer Token, API Authentication
- RESTful API, HTTP Methods (GET, POST, PUT, DELETE), JSON Response
- Midtrans, Snap API, Payment Gateway, Webhook, Virtual Account
- OTP (One-Time Password), WhatsApp API, Phone Verification
- Migration, Seeder, Faker, Artisan CLI
- Middleware, Guard, Gate
- Flutter (hanya disebutkan sebagai klien/consumer API, tidak dijelaskan mendalam)
- CRUD (Create, Read, Update, Delete)
- Pagination, Eager Loading, Lazy Loading
- Entity Relationship Diagram (ERD), Normalisasi Database

---

## 🎓 PANDUAN TAMBAHAN UNTUK AI

1. **Jangan membuat konten fiktif/generik.** Semua contoh kode, nama tabel, nama kolom, endpoint API, dan alur sistem harus konsisten dengan spesifikasi teknis proyek yang telah diberikan di atas.

2. **Kedalaman konten:** Modul ini ditujukan untuk mahasiswa tingkat 3–4 Teknik Informatika. Penjelasan harus cukup mendalam untuk dimengerti oleh orang yang baru belajar Laravel, tetapi tetap teknis dan tidak trivial.

3. **Konsistensi terminologi:** Gunakan terminologi yang konsisten sepanjang modul. Misalnya, selalu sebut "wali santri" (bukan "orang tua"), "santri" (bukan "siswa"), "tagihan" (bukan "invoice").

4. **Narasi alur sistem:** Sebelum menampilkan kode, selalu berikan penjelasan naratif tentang APA yang dilakukan kode tersebut dan MENGAPA diperlukan. Setelah kode, berikan penjelasan BAGAIMANA kode bekerja baris per baris atau blok per blok.

5. **Keterkaitan antar bab:** Pastikan ada kalimat transisi yang menghubungkan antar bab. Misalnya: "Perancangan basis data yang telah dijabarkan pada Subbab 3.6 selanjutnya diimplementasikan menggunakan Migration Laravel sebagaimana diuraikan pada Subbab 4.2."

6. **Screenshot placeholder:** Karena ini adalah template prompt, gunakan format `[Gambar X.X: Tampilan halaman NAMA — menampilkan ELEMEN A, ELEMEN B, ELEMEN C]` setiap kali perlu screenshot. Deskripsikan elemen UI secara detail.

7. **Pembuatan modul secara bertahap:** Jika token habis, lanjutkan dari sub-bab terakhir yang sudah selesai. Gunakan penanda "## LANJUTAN SUB-BAB X.X" di awal respons berikutnya.

---

## 📝 CONTOH GAYA PENULISAN YANG DIHARAPKAN

### Contoh Penulisan Paragraf Akademis:
> "Sistem informasi berbasis web merupakan suatu sistem yang menggunakan teknologi internet dan browser sebagai media antarmuka pengguna. Menurut O'Brien dan Marakas (2010), sistem informasi adalah kombinasi teratur dari orang-orang, perangkat keras, perangkat lunak, jaringan komunikasi, dan sumber data yang mengumpulkan, mengubah, dan menyebarkan informasi dalam sebuah organisasi. Dalam konteks pengelolaan administrasi pondok pesantren, kehadiran sistem informasi berbasis web memberikan kemudahan akses bagi seluruh pemangku kepentingan, baik pengurus pesantren maupun wali santri yang berada di lokasi yang berbeda-beda."

### Contoh Penulisan Penjelasan Kode:
> "Kode Program 4.3 menunjukkan implementasi method `storeStudent()` pada `AdminController.php`. Method ini bertugas untuk memvalidasi data yang dikirimkan dari form tambah santri, melakukan upload foto jika ada, dan menyimpan data ke dalam tabel `students`. Pada baris 56–71, dilakukan validasi menggunakan fitur Request Validation milik Laravel. Validasi ini memastikan bahwa kolom `name` wajib diisi dan berupa string maksimal 255 karakter, sedangkan kolom `nis` bersifat unik di seluruh tabel students. Jika validasi gagal, Laravel secara otomatis akan mengembalikan respons dengan pesan error yang sesuai. Selanjutnya, pada baris 73–76, dilakukan pengecekan apakah request menyertakan file foto. Jika ada, foto disimpan ke direktori `photos/santri` menggunakan Storage Facade Laravel dengan disk `public`. Penting dicatat bahwa pada baris 98, setelah data santri berhasil disimpan, sistem secara otomatis membuat rekord tabungan awal dengan saldo 0 (nol) menggunakan `Saving::create()`. Hal ini memastikan setiap santri baru selalu memiliki akun tabungan sejak pertama kali didaftarkan."

---

## 🚀 MULAI GENERATE

Setelah membaca semua instruksi di atas, mulailah menulis modul dari:

**HALAMAN SAMPUL → KATA PENGANTAR → DAFTAR ISI → BAB I → BAB II → BAB III → BAB IV → BAB V → DAFTAR PUSTAKA → LAMPIRAN**

Tulis secara berurutan, lengkap, detail, dan akademis. Pastikan setiap bab mencapai target halaman yang ditentukan dengan konten yang substantif, bukan diisi dengan padding/kalimat berulang yang tidak bermakna.

**Penting:** Jika kamu menulis modul ini secara bertahap (karena batasan panjang respons), selalu mulai dengan menyebutkan: "Melanjutkan dari [nama sub-bab terakhir yang selesai ditulis]" agar kontinuitas penulisan terjaga.

---

*Prompt ini dibuat khusus untuk proyek Final Project WaliSantri App.*
*Web Backend: Laravel 11 | Mobile: Flutter | Payment: Midtrans | Auth: Sanctum*
*Base URL: https://dbs-santriapp.kypau.my.id*