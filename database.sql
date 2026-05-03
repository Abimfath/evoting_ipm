-- ============================================================
-- E-VOTING IPM - DATABASE SCRIPT
-- Import file ini melalui phpMyAdmin atau MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS evoting_ipm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE evoting_ipm;

-- -------------------------------------------------------
-- Tabel: admin
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: pemilih
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS pemilih (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status_memilih TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: kandidat
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS kandidat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    foto VARCHAR(255) DEFAULT 'default.png',
    visi TEXT,
    misi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: suara
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS suara (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pemilih INT NOT NULL,
    id_kandidat INT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pemilih) REFERENCES pemilih(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kandidat) REFERENCES kandidat(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (id_pemilih)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Data Awal: Contoh Kandidat
-- (Admin & Pemilih diinsert via setup.php agar password_hash() benar)
-- -------------------------------------------------------
INSERT INTO kandidat (nama, foto, visi, misi) VALUES
('Muhammad Rizki Pratama', 'default.png',
 'Mewujudkan IPM yang aktif, inovatif, dan berakhlak mulia demi kemajuan pelajar Muhammadiyah.',
 '1. Meningkatkan kegiatan dakwah dan pengkaderan.\n2. Mengembangkan potensi akademik dan non-akademik anggota.\n3. Membangun komunikasi yang solid antar anggota.\n4. Menjalin kerjasama dengan organisasi pelajar lainnya.'),
('Nurul Hidayah', 'default.png',
 'Membangun generasi pelajar IPM yang berkarakter, berprestasi, dan bertanggung jawab.',
 '1. Memperkuat nilai-nilai keislaman dalam setiap kegiatan.\n2. Mendorong kreativitas dan inovasi anggota.\n3. Meningkatkan kesejahteraan dan kebersamaan anggota.\n4. Aktif dalam kegiatan sosial kemasyarakatan.');
