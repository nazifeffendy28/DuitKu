# Dokumentasi REST API — DuitKu

**Base URL:** `http://<domain-atau-ip>/duitku-api/`
**Format:** JSON
**Backend:** PHP + MySQL/MariaDB

---

## 1. Register

**Endpoint:** `POST /register.php`

Mendaftarkan akun pengguna baru.

### Request
```json
{
  "nama": "Nazif Hamza Effendy",
  "email": "nazif@example.com",
  "password": "password123"
}
```

### Response — Berhasil (201)
```json
{
  "status": "success",
  "message": "Registrasi berhasil",
  "data": {
    "id": 1,
    "nama": "Nazif Hamza Effendy",
    "email": "nazif@example.com"
  }
}
```

### Response — Gagal, email sudah terdaftar (400)
```json
{
  "status": "error",
  "message": "Email sudah terdaftar"
}
```

---

## 2. Login

**Endpoint:** `POST /login.php`

Autentikasi pengguna terdaftar.

### Request
```json
{
  "email": "nazif@example.com",
  "password": "password123"
}
```

### Response — Berhasil (200)
```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "id": 1,
    "nama": "Nazif Hamza Effendy",
    "email": "nazif@example.com"
  }
}
```

### Response — Gagal, kredensial salah (401)
```json
{
  "status": "error",
  "message": "Email atau password salah"
}
```

---

## 3. Ambil Semua Transaksi

**Endpoint:** `GET /transaksi.php?user_id={user_id}`

Mengambil seluruh transaksi milik user tertentu.

### Contoh Request
```
GET /transaksi.php?user_id=1
```

### Response — Berhasil (200)
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "nominal": 25000.00,
      "kategori": "Makan",
      "tanggal": "2026-07-01",
      "catatan": "Makan siang di kantin kampus",
      "created_at": "2026-07-01 12:30:00"
    },
    {
      "id": 2,
      "user_id": 1,
      "nominal": 15000.00,
      "kategori": "Transport",
      "tanggal": "2026-07-01",
      "catatan": "Ojek online ke kampus",
      "created_at": "2026-07-01 13:00:00"
    }
  ]
}
```

---

## 4. Ambil Detail Transaksi

**Endpoint:** `GET /transaksi.php?id={id}`

### Contoh Request
```
GET /transaksi.php?id=1
```

### Response — Berhasil (200)
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "user_id": 1,
    "nominal": 25000.00,
    "kategori": "Makan",
    "tanggal": "2026-07-01",
    "catatan": "Makan siang di kantin kampus",
    "created_at": "2026-07-01 12:30:00"
  }
}
```

### Response — Tidak ditemukan (404)
```json
{
  "status": "error",
  "message": "Transaksi tidak ditemukan"
}
```

---

## 5. Tambah Transaksi

**Endpoint:** `POST /transaksi.php`

### Request
```json
{
  "user_id": 1,
  "nominal": 20000.00,
  "kategori": "Makan",
  "tanggal": "2026-07-04",
  "catatan": "Sarapan"
}
```

### Response — Berhasil (201)
```json
{
  "status": "success",
  "message": "Transaksi berhasil ditambahkan",
  "data": {
    "id": 5,
    "user_id": 1,
    "nominal": 20000.00,
    "kategori": "Makan",
    "tanggal": "2026-07-04",
    "catatan": "Sarapan"
  }
}
```

### Response — Validasi gagal (400)
```json
{
  "status": "error",
  "message": "Nominal harus lebih besar dari 0"
}
```

---

## 6. Update Transaksi

**Endpoint:** `PUT /transaksi.php`

### Request
```json
{
  "id": 5,
  "user_id": 1,
  "nominal": 22000.00,
  "kategori": "Makan",
  "tanggal": "2026-07-04",
  "catatan": "Sarapan + kopi"
}
```

### Response — Berhasil (200)
```json
{
  "status": "success",
  "message": "Transaksi berhasil diperbarui",
  "data": {
    "id": 5,
    "user_id": 1,
    "nominal": 22000.00,
    "kategori": "Makan",
    "tanggal": "2026-07-04",
    "catatan": "Sarapan + kopi"
  }
}
```

---

## 7. Hapus Transaksi

**Endpoint:** `DELETE /transaksi.php?id={id}`

### Contoh Request
```
DELETE /transaksi.php?id=5
```

### Response — Berhasil (200)
```json
{
  "status": "success",
  "message": "Transaksi berhasil dihapus"
}
```

### Response — Tidak ditemukan (404)
```json
{
  "status": "error",
  "message": "Transaksi tidak ditemukan"
}
```

---

## Ringkasan Endpoint

| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/register.php` | Registrasi user baru |
| POST | `/login.php` | Login user |
| GET | `/transaksi.php?user_id=` | Ambil semua transaksi milik user |
| GET | `/transaksi.php?id=` | Ambil detail satu transaksi |
| POST | `/transaksi.php` | Tambah transaksi baru |
| PUT | `/transaksi.php` | Update transaksi |
| DELETE | `/transaksi.php?id=` | Hapus transaksi |

---

## Catatan Validasi

- `nominal`: wajib diisi, harus numerik, dan > 0
- `kategori`: wajib dipilih dari daftar (Makan, Transport, Kuliah, Hiburan, Lainnya)
- `tanggal`: wajib diisi, format `YYYY-MM-DD`
- `email` (register/login): wajib format email valid
- `password`: minimal 6 karakter, disimpan dalam bentuk hash (bcrypt) di database
