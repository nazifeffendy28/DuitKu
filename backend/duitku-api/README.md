# Backend DuitKu — Panduan Setup

## 1. Lokasi Folder
Taruh folder `duitku-api` ini di dalam folder `htdocs` server lokal kamu:

- **XAMPP (Windows):** `C:\xampp\htdocs\duitku-api\`
- **XAMPP (Mac):** `/Applications/XAMPP/xamppfiles/htdocs/duitku-api/`
- **Laragon (Windows):** `C:\laragon\www\duitku-api\`

## 2. Setup Database
1. Buka phpMyAdmin (`http://localhost/phpmyadmin`)
2. Import file `schema_duitku.sql` (bikin database `db_duitku` otomatis)

## 3. Sesuaikan Kredensial Database
Buka `db.php`, sesuaikan baris berikut kalau perlu (default XAMPP: user `root`, password kosong):
```php
$username = "root";
$password = "";
```

## 4. Jalankan Server
Start **Apache** dan **MySQL** dari XAMPP Control Panel.

## 5. Testing Endpoint
Bisa dites pakai Postman sebelum dihubungkan ke Android:
```
POST http://localhost/duitku-api/register.php
POST http://localhost/duitku-api/login.php
GET  http://localhost/duitku-api/transaksi.php?user_id=1
POST http://localhost/duitku-api/transaksi.php
PUT  http://localhost/duitku-api/transaksi.php
DELETE http://localhost/duitku-api/transaksi.php?id=1
```

## 6. Akses dari Android
- **Emulator:** gunakan `http://10.0.2.2/duitku-api/` sebagai base URL (bukan `localhost`)
- **HP fisik:** gunakan IP address laptop di jaringan WiFi yang sama, misal `http://192.168.1.5/duitku-api/`
  - Cek IP laptop lewat `ipconfig` (Windows) atau `ifconfig` (Mac/Linux)
  - Pastikan HP dan laptop terhubung ke WiFi yang sama
  - Tambahkan `android:usesCleartextTraffic="true"` di `AndroidManifest.xml` (karena HTTP lokal, bukan HTTPS)
