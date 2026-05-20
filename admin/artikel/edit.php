<?php
/**
 * Admin — Edit Artikel
 */

session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: ../login.php'); exit; }

require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$db   = getDB();
$stmt = $db->prepare('SELECT * FROM artikel WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$artikel = $stmt->fetch();

if (!$artikel) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Artikel tidak ditemukan.'];
    header('Location: index.php');
    exit;
}

// Flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Artikel — Admin Pusaka Himatif</title>
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
      <div class="topbar-title">Edit Artikel</div>
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
        <span>Edit</span>
      </div>

      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> flash-message" style="margin-bottom:20px;">
          <span class="alert-icon"><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span>
          <?= htmlspecialchars($flash['msg']) ?>
        </div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="process.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="id" value="<?= $artikel['id'] ?>" />

          <div class="form-group">
            <label for="judul">Judul Artikel <span style="color:#ef4444;">*</span></label>
            <input
              type="text"
              id="judul"
              name="judul"
              class="form-control"
              placeholder="Masukkan judul artikel..."
              required
              value="<?= htmlspecialchars($artikel['judul']) ?>"
            />
          </div>

          <div class="form-group">
            <label for="slug">Slug (URL) <span style="color:#ef4444;">*</span></label>
            <input
              type="text"
              id="slug"
              name="slug"
              class="form-control"
              placeholder="judul-artikel-dalam-format-url"
              required
              value="<?= htmlspecialchars($artikel['slug']) ?>"
              data-manual="true"
            />
            <p class="form-hint">Contoh: <code>tips-hemat-biaya-print</code></p>
          </div>

          <div class="form-group">
            <label for="ringkasan">Ringkasan</label>
            <textarea
              id="ringkasan"
              name="ringkasan"
              class="form-control"
              rows="3"
              placeholder="Deskripsi singkat artikel untuk preview card..."
              style="min-height:90px;"
            ><?= htmlspecialchars($artikel['ringkasan'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="konten">Konten Artikel <span style="color:#ef4444;">*</span></label>
            <textarea
              id="konten"
              name="konten"
              class="form-control"
              placeholder="Tulis konten artikel..."
              required
            ><?= htmlspecialchars($artikel['konten']) ?></textarea>
            <p class="form-hint">Anda bisa menggunakan HTML dasar: &lt;p&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;strong&gt;, dll.</p>
          </div>

          <div class="form-group">
            <label for="gambar">Ganti Gambar Artikel</label>
            <?php if ($artikel['gambar']): ?>
              <div style="margin-bottom:12px;display:flex;align-items:center;gap:14px;">
                <img
                  src="<?= '../../uploads/artikel/' . htmlspecialchars($artikel['gambar']) ?>"
                  alt="Gambar saat ini"
                  class="img-preview"
                />
                <div>
                  <p style="font-size:0.8em;color:#999;">Gambar saat ini</p>
                  <p style="font-size:0.78em;color:#bbb;"><?= htmlspecialchars($artikel['gambar']) ?></p>
                </div>
              </div>
            <?php endif; ?>
            <input
              type="file"
              id="gambar"
              name="gambar"
              class="form-control"
              accept="image/jpeg,image/png,image/webp,image/gif"
              onchange="previewImage(this, 'imgPreview')"
            />
            <p class="form-hint">Kosongkan jika tidak ingin mengganti gambar. Maks 2MB.</p>
            <div id="previewWrapper" style="display:none;margin-top:12px;">
              <img id="imgPreview" src="" alt="Preview baru" class="img-preview" />
            </div>
          </div>

          <div class="form-group">
            <label for="status">Status Publikasi</label>
            <select id="status" name="status" class="form-control" style="max-width:260px;">
              <option value="published" <?= $artikel['status'] === 'published' ? 'selected' : '' ?>>
                ✅ Published (Tampil di website)
              </option>
              <option value="draft" <?= $artikel['status'] === 'draft' ? 'selected' : '' ?>>
                📝 Draft (Tidak tampil)
              </option>
            </select>
          </div>

          <div style="font-size:0.78em;color:#aaa;margin-bottom:8px;">
            Dibuat: <?= date('d M Y H:i', strtotime($artikel['created_at'])) ?> &nbsp;|&nbsp;
            Terakhir diubah: <?= date('d M Y H:i', strtotime($artikel['updated_at'])) ?>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
            <a href="index.php" class="btn-secondary">Batal</a>
            <a href="delete.php?id=<?= $artikel['id'] ?>"
               class="btn-secondary"
               style="color:#ef4444;border-color:#fca5a5;margin-left:auto;"
               onclick="return confirm('Yakin hapus artikel ini?')">
              🗑️ Hapus Artikel
            </a>
          </div>
        </form>
      </div>

    </div>
  </div>

  <script src="../assets/admin.js"></script>
  <script>
    document.getElementById('gambar').addEventListener('change', function() {
      if (this.files[0]) {
        document.getElementById('previewWrapper').style.display = 'block';
      }
    });
  </script>
</body>
</html>
