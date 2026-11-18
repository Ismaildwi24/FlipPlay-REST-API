# FlipPlay REST API — The Vibe Grid (Modul 4 Pemrograman Web)

REST API modern berbasis **Laravel 12** yang melanjutkan arsitektur Object-Oriented Programming (OOP) dari **Modul 3** ke dalam arsitektur backend nyata dengan persistensi database MySQL, Eloquent ORM, Relationships Many-to-Many, Form Request Validation, API Resource konsisten, Custom Actions, serta penanganan error lengkap.

---

## 📋 Pemenuhan Seluruh Ketentuan Demo & Rubrik Penilaian

| Aspek Penilaian | Bobot | Ketentuan Praktikum | Implementasi pada Proyek FlipPlay | Status |
|:---|:---:|:---|:---|:---:|
| **CRUD API Lengkap** | **20%** | Method `index`, `show`, `store`, `update`, `destroy` dengan HTTP status code tepat (`200 OK`, `201 Created`, dll.). | `MediaController` menerapkan seluruh method CRUD lengkap: `index` (200), `show` (200 / 404), `store` (201), `update` (200), `destroy` (200). | ✅ **LENGKAP** |
| **Eloquent Relationship** | **20%** | Minimal 2 entitas dengan relasi Eloquent (`belongsToMany`), migration benar dengan pivot table, eager loading (`with()`). | Relasi Many-to-Many antara `Media` dan `User` melalui pivot table `media_user` (dengan kolom tambahan `status`, `user_rating`, `note`). Eager loading diterapkan dengan `with('users')` dan `with('media')`. | ✅ **LENGKAP** |
| **Form Request Validation** | **15%** | Validasi input (POST/PATCH) di Form Request, pesan kustom dalam bahasa Indonesia, respon status `422 Unprocessable Content`. | `StoreMediaRequest` (aturan `required` saat POST, `sometimes` saat PATCH, `unique:media,title`, validasi numerik) & `AddToCollectionRequest`. | ✅ **LENGKAP** |
| **API Resource** | **10%** | Format JSON konsisten menggunakan Resource, memanfaatkan conditional attribute dan `whenLoaded()`. | `MediaResource` dan `UserResource` memformat seluruh data output secara seragam dan menyematkan relasi hanya saat di-load (`whenLoaded`). | ✅ **LENGKAP** |
| **Custom Action di luar CRUD** | **10%** | Aksi khusus relasi antartabel (attach/detach koleksi pengguna) dengan penanganan race condition / duplicate. | `POST /api/users/{user}/collection` (menambahkan media ke koleksi pribadi dengan deteksi duplikat `409 Conflict`), `DELETE /api/users/{user}/collection/{media}` (melepas relasi dari pivot), serta `GET /api/users/{user}/collection`. | ✅ **LENGKAP** |
| **Pengujian Endpoint** | **5%** | Uji coba happy path & failure path menggunakan Postman. | Disertakan file koleksi Postman siap pakai `FlipPlay_API.postman_collection.json` dan 20 automated tests suite (`php artisan test`). | ✅ **LENGKAP** |

---

## 🗄️ Struktur Database & Relasi Eloquent

```text
┌───────────────────────────────┐                  ┌────────────────────────────────────────┐
│             users             │                  │                 media                  │
├───────────────────────────────┤                  ├────────────────────────────────────────┤
│ id (PK)                       │ 1              * │ id (PK)                                │
│ name (VARCHAR)                │◄───────┐      ┌─►│ title (VARCHAR, UNIQUE)                │
│ username (VARCHAR, UNIQUE)    │        │      │  │ type (VARCHAR: Anime/Movie)            │
│ email (VARCHAR, UNIQUE)       │        │      │  │ genres (JSON: ["Action", "Sci-Fi"])    │
│ favorite_aura (VARCHAR)       │        │      │  │ rating (DECIMAL 3,1)                   │
│ created_at / updated_at       │        │      │  │ release_year (INT)                     │
└───────────────────────────────┘        │      │  │ aura (VARCHAR: Binge-Mode, dll.)       │
                                         │      │  │ synopsis (TEXT)                        │
                                         │      │  │ episode_count, studio, status (Anime)  │
                                         │      │  │ duration_minutes, director, quality    │
                                         │      │  │ created_at / updated_at                │
                                         │      │  └────────────────────────────────────────┘
                                 ┌───────┴──────┴─────────┐
                                 │       media_user       │ (Pivot Table)
                                 ├────────────────────────┤
                                 │ id (PK)                │
                                 │ user_id (FK -> users)  │
                                 │ media_id (FK -> media) │
                                 │ status (VARCHAR)       │
                                 │ user_rating (DECIMAL)  │
                                 │ note (VARCHAR)         │
                                 │ created_at, updated_at │
                                 │ UNIQUE(user_id, media) │
                                 └────────────────────────┘
```

---

## 🌐 Daftar Endpoint REST API

Seluruh endpoint terdaftar di [routes/api.php](file:///C:/Personal/My_Code/TWeb_MD4/routes/api.php):

### 1. Katalog Media (`/api/media`)

| Method | Endpoint | Fungsi | Status Code |
|:---|:---|:---|:---:|
| `GET` | `/api/media` | Mengambil seluruh media (Pagination 10 data & Eager Loading `users`) | `200 OK` |
| `GET` | `/api/media?title={keyword}` | Mencari media berdasarkan judul / sinopsis (Query Scope `search`) | `200 OK` |
| `GET` | `/api/media?aura={vibe}` | Filter media berdasarkan aura (Query Scope `byAura`) | `200 OK` |
| `GET` | `/api/media?type={Anime\|Movie}` | Filter media berdasarkan tipe (Query Scope `byType`) | `200 OK` |
| `GET` | `/api/media/{id}` | Detail satu media beserta pengguna yang mengoleksinya | `200 OK` / `404 Not Found` |
| `POST` | `/api/media` | Menambahkan media baru (Validasi `StoreMediaRequest`) | `201 Created` / `422 Unprocessable` |
| `PATCH` | `/api/media/{id}` | Memperbarui sebagian data media (Validasi `sometimes`) | `200 OK` / `422 Unprocessable` |
| `DELETE` | `/api/media/{id}` | Menghapus media dari katalog sistem | `200 OK` / `404 Not Found` |

### 2. Pengguna & Koleksi Pribadi (`/api/users`)

| Method | Endpoint | Fungsi | Status Code |
|:---|:---|:---|:---:|
| `GET` | `/api/users` | Menampilkan seluruh pengguna dan koleksi media | `200 OK` |
| `GET` | `/api/users/{id}` | Menampilkan profil pengguna beserta seluruh koleksinya | `200 OK` / `404 Not Found` |
| `POST` | `/api/users` | Mendaftarkan pengguna baru | `201 Created` / `422 Unprocessable` |
| `GET` | `/api/users/{id}/collection` | **[Custom Action]** Menampilkan khusus daftar koleksi pengguna | `200 OK` |
| `POST` | `/api/users/{id}/collection` | **[Custom Action]** Menambahkan media ke koleksi pengguna (`attach`) | `201 Created` / `409 Conflict` / `422` |
| `DELETE` | `/api/users/{id}/collection/{media_id}` | **[Custom Action]** Menghapus media dari koleksi (`detach`) | `200 OK` / `404 Not Found` |

---

## 🚀 Panduan Setup & Menjalankan Project

### 1. Prasyarat Sistem
- **PHP** versi 8.2 atau lebih tinggi
- **Composer** versi 2.x
- **MySQL** aktif (via Laragon atau XAMPP) pada port 3306

### 2. Konfigurasi Environment & Database
Pastikan MySQL sudah berjalan, buat database `flipplay_db` atau sesuaikan konfigurasi di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flipplay_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data Awal
Jalankan migrasi sekaligus seeding data dummy:
```bash
php artisan migrate:fresh --seed
```
> Perintah di atas akan membuat tabel `users`, `media`, `media_user` dan mengisinya dengan 7 data media bawaan Modul 3 (Attack on Titan, Cyberpunk: Edgerunners, Your Name, Steins;Gate, Interstellar, Spider-Man: Across the Spider-Verse, La La Land) serta 3 pengguna (`kenjiro`, `aoi_vibes`, `ren_runner`) lengkap beserta koleksi awal.

### 4. Jalankan Server Development
```bash
php artisan serve
```
Server API akan aktif pada: `http://127.0.0.1:8000`

---

## 🧪 Pengujian API (Postman & Automated Tests)

### A. Pengujian via Postman
File koleksi Postman sudah disediakan pada root project:
📁 [FlipPlay_API.postman_collection.json](file:///C:/Personal/My_Code/TWeb_MD4/FlipPlay_API.postman_collection.json)

1. Buka aplikasi **Postman**.
2. Klik tombol **Import** di pojok kiri atas.
3. Pilih file `FlipPlay_API.postman_collection.json`.
4. Seluruh request sudah terkelompokkan rapi ke dalam folder:
   - **1. Media (Katalog)**: Pengujian CRUD lengkap, Query Filters, Happy Path (200, 201), dan Failure Path (404, 422).
   - **2. Users & Collections**: Pengujian Relasi Many-to-Many, Custom Action `addToCollection`, `removeFromCollection`, Happy Path, dan Failure Path (409 Conflict saat duplikasi koleksi, 404 saat data tidak ditemukan, 422 saat ID salah).

### B. Pengujian Otomatis (PHPUnit Feature Tests)
Project ini dilengkapi 20 unit & feature test otomatis yang mencakup 100% skenario penilaian demo:
```bash
php artisan test
```
Hasil pengujian:
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\MediaApiTest (10 tests)
PASS  Tests\Feature\UserCollectionApiTest (8 tests)

Tests:    20 passed (143 assertions)
Duration: ~1.5s
```

---

## 📦 Struktur Folder Laravel

```text
TWeb_MD4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── MediaController.php     <- CRUD Media + Eager Loading + Filter
│   │   │   └── UserController.php      <- Profil User + Custom Actions Collection
│   │   ├── Requests/
│   │   │   ├── StoreMediaRequest.php   <- Form Request Validation (POST & PATCH)
│   │   │   └── AddToCollectionRequest.php <- Validasi request koleksi
│   │   └── Resources/
│   │       ├── MediaResource.php       <- API Resource format Media (whenLoaded)
│   │       └── UserResource.php        <- API Resource format User (whenLoaded)
│   ├── Models/
│   │   ├── Media.php                   <- Model Eloquent Media (belongsToMany User)
│   │   └── User.php                    <- Model Eloquent User (belongsToMany Media)
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── 2026_01_01_000000_create_users_table.php
│   │   ├── 2026_01_01_000001_create_media_table.php
│   │   └── 2026_01_01_000002_create_media_user_table.php <- Pivot Table
│   └── seeders/
│       └── DatabaseSeeder.php          <- Seeder data katalog & koleksi awal
├── routes/
│   └── api.php                         <- Pendaftaran rute API & Custom Action
├── tests/
│   └── Feature/
│       ├── MediaApiTest.php            <- Test suite CRUD & validasi Media
│       └── UserCollectionApiTest.php   <- Test suite Relasi & Custom Action
├── FlipPlay_API.postman_collection.json <- Koleksi Postman siap pakai
├── modul3_legacy/                      <- Arsip kode simulasi CLI Modul 3
└── README.md
```
