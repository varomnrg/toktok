-- Database: toko_elektronik
-- Create database
CREATE DATABASE IF NOT EXISTS toko_elektronik;
USE toko_elektronik;

-- Tabel untuk login admin
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) NOT NULL UNIQUE,
    nama_produk VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    thumbnail VARCHAR(500),   
    deskripsi TEXT,
    stok INT DEFAULT 0,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Insert data admin default
INSERT INTO users (username, password, nama_lengkap, email) VALUES 
('admin', '$2y$10$BrPbIthitnhA9VmvvQSDMen4eJnQzO2JqUDlMyN4gTboIOUjzahkS', 'Administrator', 'admin@tokoelektronik.com');

-- Insert data produk sesuai permintaan
INSERT INTO produk (kode_produk, nama_produk, kategori, harga, thumbnail, deskripsi, stok) VALUES 
('SP01', 'iPhone 13 Pro', 'iPhone', 12000000.00, 'https://images.unsplash.com/photo-1632661674596-df8be070a5c5?w=300', 'iPhone 13 Pro dengan kamera canggih dan performa tinggi', 10),
('SP02', 'Samsung Galaxy Z Flip', 'Samsung', 20000000.00, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=300', 'Samsung Galaxy Z Flip smartphone lipat inovatif', 5),
('SP03', 'Xiaomi Redmi Note 11 Pro', 'Xiaomi', 3200000.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300', 'Xiaomi Redmi Note 11 Pro dengan kamera 108MP', 15);