# FlipPlay - Backend OOP Media Engine (Modul 3 Pemrograman Web)

Prototipe engine backend sederhana berbasis bahasa pemrograman PHP dengan paradigma **Object-Oriented Programming (OOP)** untuk studi kasus **FlipPlay: The Vibe Grid**.

---

## 📋 Pemenuhan Ketentuan Demo Praktikum

| No | Ketentuan Praktikum | Implementasi pada Proyek FlipPlay |
|:---|:---|:---|
| 1 | **Minimal tiga class** | Terdapat **6 class**: `Media`, `Anime`, `Movie`, `User`, `Library`, dan `CLIHelper`. |
| 2 | **Menerapkan constructor (`__construct`)** | Setiap class memiliki `__construct()`. Subclass (`Anime` dan `Movie`) memanggil `parent::__construct()` untuk inisialisasi class induk. |
| 3 | **Memiliki property dan method** | Beragam property typed (`$id`, `$title`, `$genres`, `$rating`, `$releaseYear`, dll.) dan method lengkap (getter, setter, formatters, search, filter, summary). |
| 4 | **Access modifier secara tepat** | Menggunakan `protected` pada base class `Media`, `private` pada class anak & service untuk enkapsulasi, serta `public` untuk antarmuka publik method. |
| 5 | **Inheritance & struktur class rapi** | `Anime extends Media` dan `Movie extends Media`. Menerapkan polimorfisme melalui abstract method `getType()` dan `getDetails()`. |
| 6 | **Type hinting & return type** | Menggunakan `declare(strict_types=1);` serta type hinting lengkap pada seluruh parameter dan return type method (`int`, `string`, `float`, `array`, `void`, `?Media`, dll.). |
| 7 | **Menggunakan namespace** | Menggunakan namespace `FlipPlay\Models` dan `FlipPlay\Services` dengan PSR-4 autoloader di `app/bootstrap.php`. |
| 8 | **File utama (`index.php`) sebagai entry point** | Menjalankan seluruh simulasi backend dari satu file utama `index.php` melalui terminal CLI. |

---

## 📂 Struktur Direktori

```text
TWeb_MD3/
├── app/
│   ├── Models/
│   │   ├── Media.php         <- Base abstract class (Parent)
│   │   ├── Anime.php         <- Child class Anime (Inheritance)
│   │   ├── Movie.php         <- Child class Movie (Inheritance)
│   │   └── User.php          <- Class User & koleksi media pribadi
│   ├── Services/
│   │   ├── Library.php       <- Service manajemen katalog, pencarian, & filter
│   │   └── CLIHelper.php     <- Utility formatting terminal (tabel, warna ANSI, badge)
│   └── bootstrap.php         <- PSR-4 Autoloader
├── index.php                 <- Entry point utama simulasi CLI
└── README.md
```

---

## 🚀 Cara Menjalankan Simulasi

Pastikan PHP versi 8.0 ke atas sudah terpasang di komputer Anda.

### 1. Jalankan Simulasi Otomatis (Demo Lengkap Tahap 1 - 5)
Buka terminal / PowerShell di root project, lalu jalankan:
```bash
php index.php
```

Output simulasi akan menampilkan:
1. **Tahap 1:** Menambahkan media (Anime & Movie) ke dalam library dan menampilkan ringkasan statistik katalog.
2. **Tahap 2:** Menampilkan daftar seluruh media dalam tabel terminal yang rapi + contoh filter Aura (Vibe).
3. **Tahap 3:** Mencari media berdasarkan judul (mencakup kasus data ditemukan dan data tidak ditemukan).
4. **Tahap 4:** Menampilkan informasi detail media dengan pembuktian polimorfisme method `getDetails()`.
5. **Tahap 5:** Menampilkan koleksi media milik pengguna (`User`) beserta status tonton dan rating pribadi.

---

### 2. Jalankan Mode Interaktif (Opsional untuk Eksplorasi)
Untuk mencoba menu interaktif mandiri:
```bash
php index.php --interactive
```
Pilihan menu interaktif:
- `1`: Tampilkan semua media di library
- `2`: Cari media berdasarkan judul
- `3`: Lihat detail lengkap media berdasarkan ID
- `4`: Tambah media ke koleksi pengguna
- `5`: Tampilkan koleksi pengguna
- `6`: Filter media berdasarkan Aura
- `0`: Keluar
