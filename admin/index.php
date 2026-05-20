<?php
/**
 * Admin Dashboard — Pusaka Himatif
 */

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Statistik
$totalArtikel    = $db->query('SELECT COUNT(*) FROM artikel')->fetchColumn();
$totalPublished  = $db->query("SELECT COUNT(*) FROM artikel WHERE status = 'published'")->fetchColumn();
$totalDraft      = $db->query("SELECT COUNT(*) FROM artikel WHERE status = 'draft'")->fetchColumn();

// Artikel terbaru (5)
$artikelTerbaru = $db->query(
    "SELECT a.id, a.judul, a.status, a.created_at, ad.nama AS author
     FROM artikel a
     JOIN admin ad ON a.author_id = ad.id
     ORDER BY a.created_at DESC
     LIMIT 5"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Admin Pusaka Himatif</title>
  <link rel="icon" href="../Assets/logo.png" type="image/x-icon" />
  <link rel="stylesheet" href="assets/admin.css" />
</head>
<body class="admin-body">

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <span class="logo-text">Pusaka<span class="accent">.</span></span>
      <span class="logo-sub">Admin</span>
    </div>
    <nav class="sidebar-nav">
      <a href="index.php" class="nav-item active">
        <span class="nav-icon">📊</span> Dashboard
      </a>
      <a href="artikel/index.php" class="nav-item">
        <span class="nav-icon">📰</span> Kelola Artikel
      </a>
      <div class="nav-divider"></div>
      <a href="../index.php" class="nav-item" target="_blank">
        <span class="nav-icon">🌐</span> Lihat Website
      </a>
      <a href="logout.php" class="nav-item nav-logout">
        <span class="nav-icon">🚪</span> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <!-- Top Bar -->
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">☰</button>
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-user">
        <span class="user-avatar">👤</span>
        <span><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
      </div>
    </header>

    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?>!</h1>
        <p class="page-subtitle">Kelola konten website Pusaka Himatif dari sini.</p>
      </div>

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📰</div>
          <div class="stat-info">
            <div class="stat-number"><?= $totalArtikel ?></div>
            <div class="stat-label">Total Artikel</div>
          </div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <div class="stat-number"><?= $totalPublished ?></div>
            <div class="stat-label">Published</div>
          </div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">📝</div>
          <div class="stat-info">
            <div class="stat-number"><?= $totalDraft ?></div>
            <div class="stat-label">Draft</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="section-card">
        <div class="section-header">
          <h2 class="section-title">Aksi Cepat</h2>
        </div>
        <div class="quick-actions">
          <a href="artikel/create.php" class="quick-btn">
            <span class="quick-icon">✏️</span>
            <span>Tulis Artikel Baru</span>
          </a>
          <a href="artikel/index.php" class="quick-btn quick-btn-secondary">
            <span class="quick-icon">📋</span>
            <span>Semua Artikel</span>
          </a>
          <a href="../index.php" target="_blank" class="quick-btn quick-btn-secondary">
            <span class="quick-icon">🌐</span>
            <span>Lihat Website</span>
          </a>
        </div>
      </div>

      <!-- Artikel Terbaru -->
      <div class="section-card">
        <div class="section-header">
          <h2 class="section-title">Artikel Terbaru</h2>
          <a href="artikel/index.php" class="section-link">Lihat semua →</a>
        </div>
        <?php if (empty($artikelTerbaru)): ?>
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>Belum ada artikel. <a href="artikel/create.php">Buat artikel pertama!</a></p>
          </div>
        <?php else: ?>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Judul</th>
                  <th>Author</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($artikelTerbaru as $art): ?>
                <tr>
                  <td class="td-title"><?= htmlspecialchars($art['judul']) ?></td>
                  <td><?= htmlspecialchars($art['author']) ?></td>
                  <td>
                    <span class="badge badge-<?= $art['status'] === 'published' ? 'success' : 'warning' ?>">
                      <?= $art['status'] === 'published' ? 'Published' : 'Draft' ?>
                    </span>
                  </td>
                  <td><?= date('d M Y', strtotime($art['created_at'])) ?></td>
                  <td class="td-actions">
                    <a href="artikel/edit.php?id=<?= $art['id'] ?>" class="btn-action btn-edit">Edit</a>
                    <a href="artikel/delete.php?id=<?= $art['id'] ?>" class="btn-action btn-delete"
                       onclick="return confirm('Hapus artikel ini?')">Hapus</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div><!-- /.admin-content -->
  </div><!-- /.admin-main -->

  <script src="assets/admin.js"></script>
</body>
</html>
