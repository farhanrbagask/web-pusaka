<?php
/**
 * Admin — List Artikel
 */

session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Filter status
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where  = [];
$params = [];

if ($status !== 'all') {
    $where[]  = 'a.status = ?';
    $params[] = $status;
}
if ($search !== '') {
    $where[]  = 'a.judul LIKE ?';
    $params[] = '%' . $search . '%';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$artikels = $db->prepare(
    "SELECT a.id, a.judul, a.status, a.created_at, a.updated_at, ad.nama AS author
     FROM artikel a
     JOIN admin ad ON a.author_id = ad.id
     $whereSQL
     ORDER BY a.created_at DESC"
);
$artikels->execute($params);
$artikels = $artikels->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Artikel — Admin Pusaka Himatif</title>
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
      <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">☰</button>
      <div class="topbar-title">Kelola Artikel</div>
      <div class="topbar-user">
        <span class="user-avatar">👤</span>
        <span><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
      </div>
    </header>

    <div class="admin-content">
      <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;">
        <div>
          <h1 class="page-title">Kelola Artikel</h1>
          <p class="page-subtitle">Buat, edit, dan hapus artikel yang tampil di website.</p>
        </div>
        <a href="create.php" class="btn-primary" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
          ✏️ Tulis Artikel Baru
        </a>
      </div>

      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> flash-message" style="margin-bottom:20px;">
          <span class="alert-icon"><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span>
          <?= htmlspecialchars($flash['msg']) ?>
        </div>
      <?php endif; ?>

      <!-- Filter Bar -->
      <div class="section-card" style="margin-bottom:20px;">
        <div style="padding:16px 20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
          <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;flex:1;align-items:center;">
            <input
              type="text"
              name="q"
              placeholder="🔍 Cari judul artikel..."
              value="<?= htmlspecialchars($search) ?>"
              class="form-control"
              style="max-width:280px;"
            />
            <select name="status" class="form-control" style="max-width:160px;">
              <option value="all"      <?= $status === 'all'       ? 'selected' : '' ?>>Semua Status</option>
              <option value="published"<?= $status === 'published' ? 'selected' : '' ?>>Published</option>
              <option value="draft"    <?= $status === 'draft'     ? 'selected' : '' ?>>Draft</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            <?php if ($search || $status !== 'all'): ?>
              <a href="index.php" class="btn-secondary">Reset</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="section-card">
        <div class="section-header">
          <h2 class="section-title">
            Daftar Artikel
            <span style="font-weight:400;color:#999;font-size:0.85em;">(<?= count($artikels) ?> artikel)</span>
          </h2>
        </div>

        <?php if (empty($artikels)): ?>
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>Tidak ada artikel ditemukan. <a href="create.php">Tulis artikel baru!</a></p>
          </div>
        <?php else: ?>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Judul</th>
                  <th>Author</th>
                  <th>Status</th>
                  <th>Dibuat</th>
                  <th>Diubah</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($artikels as $i => $art): ?>
                <tr>
                  <td style="color:#999;"><?= $i + 1 ?></td>
                  <td class="td-title"><?= htmlspecialchars($art['judul']) ?></td>
                  <td><?= htmlspecialchars($art['author']) ?></td>
                  <td>
                    <span class="badge badge-<?= $art['status'] === 'published' ? 'success' : 'warning' ?>">
                      <?= $art['status'] === 'published' ? 'Published' : 'Draft' ?>
                    </span>
                  </td>
                  <td style="white-space:nowrap;"><?= date('d M Y', strtotime($art['created_at'])) ?></td>
                  <td style="white-space:nowrap;"><?= date('d M Y', strtotime($art['updated_at'])) ?></td>
                  <td class="td-actions">
                    <a href="../../artikel.php?slug=<?= urlencode($art['id']) ?>" target="_blank" class="btn-action btn-view">Lihat</a>
                    <a href="edit.php?id=<?= $art['id'] ?>" class="btn-action btn-edit">Edit</a>
                    <a href="delete.php?id=<?= $art['id'] ?>" class="btn-action btn-delete">Hapus</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <script src="../assets/admin.js"></script>
</body>
</html>
