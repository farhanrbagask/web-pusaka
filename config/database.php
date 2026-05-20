<?php
/**
 * Konfigurasi Database — Pusaka Himatif
 * Menggunakan PDO untuk keamanan & kemudahan pengembangan
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'pusaka_himatif');
define('DB_USER', 'root');       // Ganti sesuai user MySQL Anda
define('DB_PASS', '');           // Ganti sesuai password MySQL Anda
define('DB_CHARSET', 'utf8mb4');

/**
 * Membuat koneksi PDO ke database.
 * Melempar exception jika koneksi gagal.
 *
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Di production, jangan tampilkan pesan error detail
            error_log('DB Connection Error: ' . $e->getMessage());
            die('<p style="font-family:sans-serif;color:red;padding:20px;">
                    Koneksi database gagal. Periksa konfigurasi di <code>config/database.php</code>.
                 </p>');
        }
    }

    return $pdo;
}
