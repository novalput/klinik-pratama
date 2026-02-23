# 🦷 Sistem Informasi Klinik Pratama

Sistem manajemen klinik gigi berbasis web yang dibangun menggunakan **Laravel 12** dengan fitur rekam medis elektronik, manajemen jadwal dokter, dan kontrol akses berbasis peran (Role-Based Access Control).

---

## 📋 Deskripsi Proyek

Aplikasi klinik pratama yang mengelola alur pelayanan pasien secara digital, mulai dari pendaftaran kunjungan oleh staf administrasi, pemeriksaan tanda vital oleh tenaga kesehatan (nakes/perawat), hingga diagnosis dan resep oleh dokter. Semua data tersimpan dalam rekam medis elektronik yang dapat diekspor ke PDF.

---

## ✨ Fitur Utama

### 👥 Manajemen Multi-Role
| Role | Akses |
|------|-------|
| **Admin** | Kelola dokter, nakes, staf, pasien, jadwal |
| **Staf Administrasi** | Daftar pasien, buat kunjungan |
| **Nakes (Perawat)** | Isi profil pasien & tanda vital |
| **Dokter** | Diagnosis, tindakan, resep obat |

### 🏥 Rekam Medis Elektronik
- Pendaftaran kunjungan dengan filter dokter yang sedang praktik
- Form edit bertingkat sesuai hak akses masing-masing role
- Deteksi perubahan form yang belum disimpan (unsaved changes)
- Export rekam medis ke **PDF** (pilih kunjungan yang diinginkan)

### 📅 Jadwal Dokter
- Jadwal rutin mingguan (template)
- Pengecualian/cuti pada tanggal tertentu
- Validasi ketersediaan dokter secara real-time saat mendaftar kunjungan

### 📊 Dashboard
- Statistik ringkasan: total pasien, kunjungan, kunjungan mingguan
- Tabel aktivitas kunjungan yang disesuaikan per role
- Badge status kunjungan: `Menunggu Nakes`, `Menunggu Dokter`, `Selesai`

---

## 🛠️ Tech Stack

### Backend
- **PHP** `^8.2`
- **Laravel** `^12.0`
- **MySQL** (via XAMPP)

### Frontend
- **Blade** (Laravel Templating)
- **Tailwind CSS** (via Vite)
- **Alpine.js** (reaktivitas UI)
- **Font Awesome 6** (ikon)
- **Google Fonts** — Quicksand & Sansita One

### Library Tambahan
- `barryvdh/laravel-dompdf` — Generate PDF rekam medis
- `intervention/image` `^2.7` — Manajemen gambar/avatar
- `nesbot/carbon` — Manipulasi tanggal & waktu

### Dev Dependencies
- `laravel/breeze` — Auth scaffolding
- `pestphp/pest` — Testing
- `fakerphp/faker` — Data dummy

---

## 🗄️ Struktur Database (Model Utama)

```
users
├── id, name, email, phone, password, role
├── role: admin | dokter | nakes | staf_administrasi | patient
└── avatar_url, whatsapp_verified, last_login_at

patients
├── id, no_rm, nik, name, gender, dob, address, phone

patients_profile          ← diisi oleh Nakes
├── patient_id, blood_type, allergies, bpjs_number
└── occupation, education, marital_status

dokter
├── user_id, nip, education, specialization, bio, status

nakes
├── user_id, nip, education, specialization, position, bio, status

staff_administrasi
├── user_id, nip, address, status

workers                   ← karyawan umum (non-sistem)
├── name, nip, position, phone, address, status

health_visits             ← dibuat oleh Staf Admin
├── patient_id, dokter_id, visit_date
└── complaint, visit_type, payment_type, consent

medical_records           ← diisi Nakes & Dokter
├── health_visit_id
├── blood_pressure, temperature, weight, height  ← Nakes
└── diagnosis_icd10, treatment_notes, medications (JSON), refferal, follow_up_plan  ← Dokter

doctor_schedule_templates ← jadwal rutin mingguan
├── dokter_id, day, start_time, end_time, week_cycle

doctor_schedule_exceptions ← libur/jadwal tambahan
├── dokter_id, date, type (libur|tambahan), start_time, end_time

otp_requests              ← login via WhatsApp (OTP)
login_attempts            ← log percobaan login
```

---

## 🚀 Setup & Instalasi

### Prasyarat
- **XAMPP** (PHP 8.2+, MySQL)
- **Composer**
- **Node.js** & **npm**

### Langkah Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/novalput/klinik-pratama.git
cd klinik-pratama
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi Node.js**
```bash
npm install
```

**4. Konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

**5. Edit file `.env`** — sesuaikan koneksi database:
```env
APP_NAME="Klinik Pratama"
APP_URL=http://localhost/

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klinik_pratama
DB_USERNAME=root
DB_PASSWORD=
```

**6. Buat database di phpMyAdmin**
```sql
CREATE DATABASE klinik_pratama;
```

**7. Jalankan migrasi & seeder**
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

**8. Build asset frontend**
```bash
# Development (dengan hot reload)
npm run dev

# Production
npm run build
```

**9. Buat symlink storage**
```bash
php artisan storage:link
```

---

## 🔑 Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@gmail.com` | `password` |
| Dokter | `dokter@gmail.com` | `password` |
| Nakes | `nakes@gmail.com` | `password` |
| Staf Admin | `stafadmin@gmail.com` | `password` |

---

## 🗂️ Struktur Direktori Penting

```
klinik-pratama/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← AdminController, DokterController, dll
│   │   │   ├── Auth/           ← Login, Register, OTP WhatsApp
│   │   │   ├── DashboardController.php
│   │   │   ├── MedicalVisitController.php
│   │   │   ├── MonitoringController.php
│   │   │   └── ExportController.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── User.php, Patient.php, Dokter.php, Nakes.php
│       ├── HealthVisit.php, MedicalRecord.php
│       └── DoctorScheduleTemplate.php, DoctorScheduleException.php
├── resources/views/
│   ├── admin/          ← Dashboard, kelola dokter/nakes/staf/pasien
│   ├── dokter/         ← Dashboard, jadwal
│   ├── nakes/          ← Dashboard, data pasien
│   ├── staff_administrasi/ ← Dashboard
│   ├── monitoring/     ← Detail rekam medis pasien (show.blade.php)
│   ├── visits/         ← Form buat & edit kunjungan
│   ├── exports/        ← Template PDF rekam medis
│   └── layouts/        ← app.blade.php, sidebar, navigasi
├── routes/
│   └── web.php         ← Semua route dengan middleware role
└── database/
    ├── migrations/
    └── seeders/
        └── UserSeeder.php
```

---

## 🔐 Middleware & Otorisasi

```php
// Contoh penggunaan middleware role di routes/web.php
Route::middleware(['auth', 'role:staf_administrasi'])->group(...);
Route::middleware(['auth', 'role:nakes'])->group(...);
Route::middleware(['auth', 'role:dokter'])->group(...);
Route::middleware(['auth', 'admin'])->group(...);
```

**Helper permission di `User.php`:**
```php
canEditAdministrasiData()  // staf_administrasi
canEditProfileAndVitals()  // nakes & dokter
canEditMedicalDiagnosis()  // dokter
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik/internal klinik.

---

> Dikembangkan dengan ❤️ menggunakan Laravel 12 + Alpine.js + Tailwind CSS