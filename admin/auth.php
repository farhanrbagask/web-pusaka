<?php
/**
 * Admin Auth Helper — Pusaka Himatif
 * Include di setiap halaman admin yang butuh proteksi
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . str_repeat('../', substr_count(str_replace(realpath(__DIR__ . '/..'), '', __FILE__), DIRECTORY_SEPARATOR) - 1) . 'admin/login.php');
    exit;
}

function adminNama(): string {
    return htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin');
}

function adminUsername(): string {
    return htmlspecialchars($_SESSION['admin_user'] ?? '');
}
