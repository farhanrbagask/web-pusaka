<?php
/**
 * Admin — Create Artikel
 */

session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: ../login.php'); exit; }

$error = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tulis Artikel Baru — Admin Pusaka Himatif</title>
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
      <div class="topbar-title">Tulis Artikel Baru</div>
      <div class="topbar-user">
        <span class="user-avatar">👤</span>
        <span><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
      </div>
    </header>

    <div class="admin-content">
      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <a href="../index.php">Dashboard</a>
        <span class="breadcrumb-sep">›</span>
        <a href="index.php">Artikel</a>
        <span class="breadcrumb-sep">›</span>
        <span>Tulis Baru</span>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;">
          <span class="alert-icon">⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="process.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create" />

          <div class="form-group">
            <label for="judul">Judul Artikel <span style="color:#ef4444;">*</span></label>
            <input
              type="text"
              id="judul"
              name="judul"
              class="form-control"
              placeholder="Masukkan judul artikel..."
              required
              value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>"
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
              value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>"
            />
            <p class="form-hint">Di-generate otomatis dari judul. Contoh: <code>tips-hemat-biaya-print</code></p>
          </div>

          <div class="form-group">
            <label for="ringkasan">Ringkasan</label>
            <textarea
              id="ringkasan"
              name="ringkasan"
              class="form-control"
              rows="3"
              placeholder="Deskripsi singkat artikel (ditampilkan di card preview website)..."
              style="min-height:90px;"
            ><?= htmlspecialchars($_POST['ringkasan'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="konten">Konten Artikel <span style="color:#ef4444;">*</span></label>
            <textarea
              id="konten"
              name="konten"
              class="form-control"
              placeholder="Tulis konten artikel di sini... (HTML diperbolehkan)"
              required
            ><?= htmlspecialchars($_POST['konten'] ?? '') ?></textarea>
            <p class="form-hint">Anda bisa menggunakan HTML dasar seperti &lt;p&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;strong&gt;, dll.</p>
          </div>

          <div class="form-group">
            <label for="gambar">Gambar Artikel</label>
            <input
              type="file"
              id="gambar"
              name="gambar"
              class="form-control"
              accept="image/jpeg,image/png,image/webp,image/gif"
              onchange="previewImage(this, 'imgPreview')"
            />
            <p class="form-hint">Format: JPG, PNG, WEBP, GIF. Maksimal 2MB.</p>
            <div class="img-preview-wrapper" id="previewWrapper" style="display:none;margin-top:12px;">
              <img id="imgPreview" src="" alt="Preview" class="img-preview" />
            </div>
          </div>

          <div class="form-group">
            <label for="status">Status Publikasi</label>
            <select id="status" name="status" class="form-control" style="max-width:260px;">
              <option value="published">✅ Published (Langsung tampil di website)</option>
              <option value="draft">📝 Draft (Tersimpan, tidak tampil)</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary">💾 Simpan Artikel</button>
            <a href="index.php" class="btn-secondary">Batal</a>
          </div>
        </form>
      </div>

    </div>
  </div>

  <script src="../assets/admin.js"></script>
  <script>
    // Show image preview wrapper when file selected
    document.getElementById('gambar').addEventListener('change', function() {
      if (this.files[0]) {
        document.getElementById('previewWrapper').style.display = 'block';
      }
    });
  </script>
</body>
</html>
