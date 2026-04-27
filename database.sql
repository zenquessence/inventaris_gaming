-- ============================================================
-- DATABASE: db_inventaris_gaming
-- Run this file once in phpMyAdmin or MySQL CLI
-- ============================================================
CREATE DATABASE IF NOT EXISTS db_inventaris_gaming
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_inventaris_gaming;

-- ------------------------------------------------------------
-- TABLE: users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    username     VARCHAR(50)   NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,          -- bcrypt via password_hash()
    nama_lengkap VARCHAR(100)  NOT NULL,
    role         ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOTE: Admin user is inserted by setup.php (needs PHP to generate the bcrypt hash).

-- ------------------------------------------------------------
-- TABLE: aksesoris
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS aksesoris (
    id             INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    nama_aksesoris VARCHAR(150)      NOT NULL,
    kategori       ENUM('Headset','Mouse','Keyboard','Controller',
                        'Mousepad','Webcam','Capture Card','Lainnya')
                                     NOT NULL DEFAULT 'Lainnya',
    merek          VARCHAR(100)      NOT NULL,
    stok           SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    harga          DECIMAL(12,2)     NOT NULL DEFAULT 0.00,
    kondisi        ENUM('Baru','Baik','Rusak Ringan','Rusak Berat')
                                     NOT NULL DEFAULT 'Baru',
    deskripsi      TEXT              NULL,
    foto           VARCHAR(255)      NULL,        -- full-size filename
    foto_thumb     VARCHAR(255)      NULL,        -- GD thumbnail filename
    created_at     TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (no images)
INSERT INTO aksesoris (nama_aksesoris, kategori, merek, stok, harga, kondisi, deskripsi) VALUES
('Headset Gaming RGB Pro',  'Headset',    'HyperX',  10, 850000.00,  'Baru', 'Surround 7.1, mic noise-cancelling'),
('Mouse Optical 16000 DPI', 'Mouse',      'Logitech',  5, 450000.00, 'Baru', 'RGB, 11 tombol programmable'),
('Keyboard Mechanical TKL', 'Keyboard',   'Razer',     8, 1200000.00,'Baru', 'Switch Red, anti-ghosting'),
('Controller Wireless',     'Controller', 'Sony',      3, 750000.00, 'Baik', 'PS5 DualSense compatible');
