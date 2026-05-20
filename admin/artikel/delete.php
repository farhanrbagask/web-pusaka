<?php
/**
 * Admin — Delete Artikel
 */

session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$db   = getDB();
$stmt = $db->prepare('SELECT id, judul, gambar FROM artikel WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$artikel = $stmt->fetch();

if (!$artikel) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Artikel tidak ditemukan.'];
    header('Location: index.php');
    exit;
}

// Proses hapus jika konfirmasi dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['confirm'] ?? '') === 'yes') {
    // Hapus file gambar
    if ($artikel['gambar']) {
        $filePath = __DIR__ . '/../../uploads/artikel/' . $artikel['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus dari database
    $del = $db->prepare('DELETE FROM artikel WHERE id = ?');
    $del->execute([$id]);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Artikel "' . $artikel['judul'] . '" berhasil dihapus.'];
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hapus Artikel — Admin Pusaka Himatif</title>
  <link rel="icon" href="../../Assets/logo.png" type="image/x-icon" />
  <link rel="stylesheet" href="../assets/admin.css" />
</head>
<body class="admin-body">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <span class="logo-text">Pusaka<span class="accent">.</span></span>
      <span class="logo-sub">Admin</span>
    </div>
    <nav class="sidebar-nav">
      <a href="../index.php" class="nav-item">
        <span class="nav-icon">📊</span> Dashboard
      </a>
      <a href="index.php" class="nav-item active">
        <span class="nav-icon">📰</span> Kelola Artikel
      </a>
      <div class="nav-divider"></div>
      <a href="../../index.php" class="nav-item" target="_blank">
        <span class="nav-icon">🌐</span> Lihat Website
      </a>
      <a href="../logout.php" class="nav-item nav-logout">
        <span class="nav-icon">🚪</span> Logout
      </a>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <div class="topbar-title">Hapus Artikel</div>
      <div class="topbar-user">
        <span class="user-avatar">👤</span>
        <span><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
      </div>
    </header>

    <div class="admin-content">
      <div class="breadcrumb">
        <a href="../index.php">Dashboard</a>
        <span class="breadcrumb-sep">›</span>
        <a href="index.php">Artikel</a>
        <span class="breadcrumb-sep">›</span>
        <span>Hapus</span>
      </div>

      <div class="confirm-card" style="margin:0 auto;">
        <div class="confirm-icon">🗑️</div>
        <div class="confirm-title">Hapus Artikel?</div>
        <div class="confirm-text">
          Anda akan menghapus artikel:<br />
          <strong style="color:#111;">"<?= htmlspecialchars($artikel['judul']) ?>"</strong><br /><br />
          Tindakan ini <strong>tidak dapat dibatalkan</strong>. Gambar terkait juga akan ikut dihapus.
        </div>

        <form method="POST" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
          <input type="hidden" name="confirm" value="yes" />
          <button type="submit" class="btn-danger">Ya, Hapus Sekarang</button>
          <a href="index.php" class="btn-secondary">Batal</a>
        </form>
      </div>

    </div>
  </div>

  <script src="../assets/admin.js"></script>
</body>
</html>
