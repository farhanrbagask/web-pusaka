<?php
/**
 * Admin — Process Create / Update Artikel
 */

session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/database.php';

$action = $_POST['action'] ?? '';

/* -------------------------------------------------------
   Helpers
   ------------------------------------------------------- */
function sanitizeSlug(string $slug): string {
    $slug = mb_strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

function handleUpload(): ?string {
    if (empty($_FILES['gambar']['name'])) return null;

    $file     = $_FILES['gambar'];
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize  = 2 * 1024 * 1024; // 2 MB

    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!in_array($file['type'], $allowed, true)) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Format gambar tidak didukung.'];
        return 'error';
    }
    if ($file['size'] > $maxSize) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Ukuran gambar melebihi 2MB.'];
        return 'error';
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('artikel_', true) . '.' . strtolower($ext);
    $dest     = __DIR__ . '/../../uploads/artikel/' . $filename;

    if (!is_dir(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Gagal menyimpan gambar.'];
        return 'error';
    }

    return $filename;
}

function validateFields(): array {
    $judul    = trim($_POST['judul'] ?? '');
    $slug     = sanitizeSlug($_POST['slug'] ?? $judul);
    $konten   = trim($_POST['konten'] ?? '');
    $ringkasan= trim($_POST['ringkasan'] ?? '');
    $status   = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';

    $errors = [];
    if ($judul  === '') $errors[] = 'Judul wajib diisi.';
    if ($slug   === '') $errors[] = 'Slug wajib diisi.';
    if ($konten === '') $errors[] = 'Konten wajib diisi.';

    return [$judul, $slug, $konten, $ringkasan, $status, $errors];
}

/* -------------------------------------------------------
   CREATE
   ------------------------------------------------------- */
if ($action === 'create') {
    [$judul, $slug, $konten, $ringkasan, $status, $errors] = validateFields();

    if ($errors) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => implode(' ', $errors)];
        header('Location: create.php');
        exit;
    }

    $gambar = handleUpload();
    if ($gambar === 'error') {
        header('Location: create.php');
        exit;
    }

    $db = getDB();

    // Pastikan slug unik
    $existing = $db->prepare('SELECT COUNT(*) FROM artikel WHERE slug = ?');
    $existing->execute([$slug]);
    if ($existing->fetchColumn() > 0) {
        $slug = $slug . '-' . time();
    }

    $stmt = $db->prepare(
        'INSERT INTO artikel (judul, slug, konten, ringkasan, gambar, author_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$judul, $slug, $konten, $ringkasan, $gambar, $_SESSION['admin_id'], $status]);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Artikel berhasil diterbitkan!'];
    header('Location: index.php');
    exit;
}

/* -------------------------------------------------------
   UPDATE
   ------------------------------------------------------- */
if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { header('Location: index.php'); exit; }

    [$judul, $slug, $konten, $ringkasan, $status, $errors] = validateFields();

    if ($errors) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => implode(' ', $errors)];
        header("Location: edit.php?id=$id");
        exit;
    }

    $db = getDB();

    // Cek slug unik (kecuali milik artikel ini sendiri)
    $existing = $db->prepare('SELECT COUNT(*) FROM artikel WHERE slug = ? AND id != ?');
    $existing->execute([$slug, $id]);
    if ($existing->fetchColumn() > 0) {
        $slug = $slug . '-' . time();
    }

    // Handle upload gambar baru
    $gambar = handleUpload();
    if ($gambar === 'error') {
        header("Location: edit.php?id=$id");
        exit;
    }

    if ($gambar !== null) {
        // Hapus gambar lama
        $old = $db->prepare('SELECT gambar FROM artikel WHERE id = ?');
        $old->execute([$id]);
        $oldGambar = $old->fetchColumn();
        if ($oldGambar) {
            $oldPath = __DIR__ . '/../../uploads/artikel/' . $oldGambar;
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $stmt = $db->prepare(
            'UPDATE artikel SET judul=?, slug=?, konten=?, ringkasan=?, gambar=?, status=?, updated_at=NOW()
             WHERE id=?'
        );
        $stmt->execute([$judul, $slug, $konten, $ringkasan, $gambar, $status, $id]);
    } else {
        $stmt = $db->prepare(
            'UPDATE artikel SET judul=?, slug=?, konten=?, ringkasan=?, status=?, updated_at=NOW()
             WHERE id=?'
        );
        $stmt->execute([$judul, $slug, $konten, $ringkasan, $status, $id]);
    }

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Artikel berhasil diperbarui!'];
    header('Location: index.php');
    exit;
}

// Fallback
header('Location: index.php');
exit;
