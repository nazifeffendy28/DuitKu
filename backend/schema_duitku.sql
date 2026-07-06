-- =========================================================
-- Database Schema: DuitKu - Pelacak Pengeluaran Mahasiswa
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_duitku;
USE db_duitku;

-- =========================================================
-- Tabel: users
-- =========================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================================
-- Tabel: transaksi
-- =========================================================
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nominal DECIMAL(12,2) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- =========================================================
-- Index tambahan untuk mempercepat query filter
-- =========================================================
CREATE INDEX idx_transaksi_user_id ON transaksi(user_id);
CREATE INDEX idx_transaksi_tanggal ON transaksi(tanggal);
CREATE INDEX idx_transaksi_kategori ON transaksi(kategori);

-- =========================================================
-- Data Dummy (opsional, untuk testing)
-- =========================================================

-- Password contoh di bawah adalah hash bcrypt dari "password123"
-- Ganti sesuai hasil hashing PHP: password_hash('password123', PASSWORD_BCRYPT)
INSERT INTO users (nama, email, password) VALUES
('Nazif Hamza Effendy', 'nazif@example.com', '$2y$10$examplehashedpasswordxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

INSERT INTO transaksi (user_id, nominal, kategori, tanggal, catatan) VALUES
(1, 25000.00, 'Makan', '2026-07-01', 'Makan siang di kantin kampus'),
(1, 15000.00, 'Transport', '2026-07-01', 'Ojek online ke kampus'),
(1, 50000.00, 'Kuliah', '2026-07-02', 'Fotokopi bahan ajar'),
(1, 35000.00, 'Hiburan', '2026-07-03', 'Nonton bioskop');
