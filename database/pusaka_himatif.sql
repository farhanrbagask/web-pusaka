-- ============================================================
-- Database: pusaka_himatif
-- Dibuat untuk sistem admin + CRUD artikel Pusaka Himatif
-- ============================================================

CREATE DATABASE IF NOT EXISTS `pusaka_himatif`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `pusaka_himatif`;

-- ============================================================
-- Tabel: admin
-- Menyimpan akun admin yang bisa login ke panel
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)      NOT NULL UNIQUE,
  `password`   VARCHAR(255)     NOT NULL COMMENT 'bcrypt hash',
  `nama`       VARCHAR(100)     NOT NULL,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: artikel
-- Menyimpan artikel yang dibuat admin
-- ============================================================
CREATE TABLE IF NOT EXISTS `artikel` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `judul`      VARCHAR(255)     NOT NULL,
  `slug`       VARCHAR(255)     NOT NULL UNIQUE,
  `konten`     LONGTEXT         NOT NULL,
  `ringkasan`  TEXT             NULL     COMMENT 'Deskripsi singkat untuk preview card',
  `gambar`     VARCHAR(255)     NULL     COMMENT 'Nama file gambar di uploads/artikel/',
  `author_id`  INT UNSIGNED     NOT NULL,
  `status`     ENUM('published','draft') NOT NULL DEFAULT 'published',
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_author` (`author_id`),
  CONSTRAINT `fk_artikel_author` FOREIGN KEY (`author_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed: Default Admin
-- Username: admin
-- Password: pusaka2024 (di-hash menggunakan bcrypt)
-- Ganti password setelah login pertama!
-- ============================================================
INSERT INTO `admin` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Pusaka Himatif');
-- Note: Password hash di atas = "password"
-- Setelah import, login dulu lalu ganti via admin panel

-- ============================================================
-- Seed: Contoh Artikel
-- ============================================================
INSERT INTO `artikel` (`judul`, `slug`, `konten`, `ringkasan`, `author_id`, `status`) VALUES
(
  'Selamat Datang di Pusaka Himatif!',
  'selamat-datang-di-pusaka-himatif',
  '<p>Pusaka Himatif hadir sebagai solusi percetakan terpercaya bagi mahasiswa. Kami menyediakan layanan print, fotocopy, scan, dan berbagai kebutuhan alat tulis kantor di area kampus.</p>\r\n<p>Dengan jam operasional <strong>08.00 – 19.00 WIB</strong>, Senin hingga Jumat, kami siap melayani kebutuhan akademis Anda dengan cepat, ramah, dan harga terjangkau.</p>\r\n<h3>Layanan Kami</h3>\r\n<ul>\r\n  <li>🖨️ Print Hitam Putih &amp; Berwarna</li>\r\n  <li>📄 Fotocopy (A4, F4, A3)</li>\r\n  <li>🔍 Scan Dokumen</li>\r\n  <li>📚 Jilid Spiral &amp; Softcover</li>\r\n  <li>✨ Laminating</li>\r\n</ul>\r\n<p>Kunjungi kami atau pesan via WhatsApp untuk info lebih lanjut!</p>',
  'Pusaka Himatif hadir sebagai solusi percetakan terpercaya bagi mahasiswa dengan layanan print, fotocopy, scan, dan berbagai kebutuhan alat tulis kantor.',
  1,
  'published'
),
(
  'Tips Hemat Biaya Print untuk Mahasiswa',
  'tips-hemat-biaya-print-mahasiswa',
  '<p>Sebagai mahasiswa, biaya percetakan bisa cukup membebani. Berikut beberapa tips untuk menghemat biaya print:</p>\r\n<h3>1. Cetak 2-in-1</h3>\r\n<p>Manfaatkan fitur cetak 2 halaman dalam 1 lembar untuk materi bacaan yang tidak perlu detail tinggi. Bisa menghemat hingga 50%!</p>\r\n<h3>2. Pilih Hitam Putih</h3>\r\n<p>Untuk tugas dan laporan biasa, pilih mode hitam putih. Harganya jauh lebih terjangkau dibanding berwarna.</p>\r\n<h3>3. Edit Sebelum Print</h3>\r\n<p>Periksa kembali dokumen Anda sebelum dicetak untuk menghindari cetak ulang akibat kesalahan.</p>\r\n<h3>4. Manfaatkan Paket Bundel</h3>\r\n<p>Pusaka Himatif menawarkan harga spesial untuk pemesanan dalam jumlah banyak. Ajak teman-teman untuk print bersama!</p>',
  'Sebagai mahasiswa, biaya percetakan bisa cukup membebani. Berikut beberapa tips praktis untuk menghemat biaya print kuliah.',
  1,
  'published'
);
