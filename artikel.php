<?php
/**
 * Halaman Detail Artikel — Pusaka Himatif
 */

require_once __DIR__ . '/config/database.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php#artikel');
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT a.*, ad.nama AS author_nama
         FROM artikel a
         JOIN admin ad ON a.author_id = ad.id
         WHERE a.id = ? AND a.status = 'published'
         LIMIT 1"
    );
    $stmt->execute([$id]);
    $artikel = $stmt->fetch();
} catch (Exception $e) {
    $artikel = null;
}

if (!$artikel) {
    header('Location: index.php#artikel');
    exit;
}

// Artikel terkait (3 artikel lain terbaru)
try {
    $related = $db->prepare(
        "SELECT id, judul, gambar, created_at
         FROM artikel
         WHERE status = 'published' AND id != ?
         ORDER BY created_at DESC
         LIMIT 3"
    );
    $related->execute([$id]);
    $relatedArts = $related->fetchAll();
} catch (Exception $e) {
    $relatedArts = [];
}

function formatTanggal(string $date): string {
    $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $d = date('j', strtotime($date));
    $m = (int) date('n', strtotime($date));
    $y = date('Y', strtotime($date));
    return "$d {$bulan[$m]} $y";
}

$ringkasan = $artikel['ringkasan']
    ? htmlspecialchars(mb_strimwidth($artikel['ringkasan'], 0, 160, '…'))
    : 'Baca artikel lengkap di Pusaka Himatif.';
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $ringkasan ?>" />
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="Assets/logo.png" type="image/x-icon" />
    <title><?= htmlspecialchars($artikel['judul']) ?> — Pusaka Himatif</title>
  </head>

  <body>
    <header>
      <nav>
        <a href="index.php" class="logo">Pusaka Himatif<span>.</span></a>
        <div class="menuToggle" onclick="toggleMenu();"></div>
        <ul class="navigation">
          <li><a href="index.php#banner"       onclick="toggleMenu();">Beranda</a></li>
          <li><a href="index.php#order"        onclick="toggleMenu();">Pesan</a></li>
          <li><a href="index.php#calculator"   onclick="toggleMenu();">Kalkulator</a></li>
          <li><a href="index.php#activity"     onclick="toggleMenu();">Aktivitas</a></li>
          <li><a href="index.php#about"        onclick="toggleMenu();">Tentang</a></li>
          <li><a href="index.php#artikel"      onclick="toggleMenu();">Artikel</a></li>
          <li><a href="index.php#testimonials" onclick="toggleMenu();">Ulasan</a></li>
          <li><a href="index.php#contact"      onclick="toggleMenu();">Kontak</a></li>
        </ul>
      </nav>
    </header>

    <main>
      <!-- Hero tipis untuk artikel -->
      <div class="artikel-detail-hero">
        <div class="artikel-hero-overlay"></div>
        <?php if ($artikel['gambar']): ?>
          <img
            src="uploads/artikel/<?= htmlspecialchars($artikel['gambar']) ?>"
            alt="<?= htmlspecialchars($artikel['judul']) ?>"
            class="artikel-hero-bg"
          />
        <?php endif; ?>
        <div class="artikel-hero-content">
          <a href="index.php#artikel" class="artikel-back-link">← Kembali ke Artikel</a>
          <h1 class="artikel-hero-title"><?= htmlspecialchars($artikel['judul']) ?></h1>
          <div class="artikel-meta">
            <span class="artikel-meta-item">📅 <?= formatTanggal($artikel['created_at']) ?></span>
            <span class="artikel-meta-sep">·</span>
            <span class="artikel-meta-item">✍️ <?= htmlspecialchars($artikel['author_nama']) ?></span>
          </div>
        </div>
      </div>

      <!-- Konten Artikel -->
      <article class="artikel-detail-body">
        <div class="artikel-detail-container">
          <div class="artikel-content-text">
            <?= $artikel['konten'] /* Konten sudah disanitasi saat input */ ?>
          </div>

          <!-- Artikel Terkait -->
          <?php if (!empty($relatedArts)): ?>
          <div class="artikel-related">
            <h2 class="artikel-related-title">Artikel Lainnya</h2>
            <div class="artikel-related-grid">
              <?php foreach ($relatedArts as $rel): ?>
                <a href="artikel.php?id=<?= $rel['id'] ?>" class="artikel-related-card">
                  <div class="artikel-related-img-wrap">
                    <?php if ($rel['gambar']): ?>
                      <img
                        src="uploads/artikel/<?= htmlspecialchars($rel['gambar']) ?>"
                        alt="<?= htmlspecialchars($rel['judul']) ?>"
                        loading="lazy"
                      />
                    <?php else: ?>
                      <div class="artikel-img-placeholder small">📰</div>
                    <?php endif; ?>
                  </div>
                  <div class="artikel-related-body">
                    <span class="artikel-date"><?= formatTanggal($rel['created_at']) ?></span>
                    <h3><?= htmlspecialchars($rel['judul']) ?></h3>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div style="text-align:center;margin-top:40px;">
            <a href="index.php#artikel" class="btn">← Semua Artikel</a>
          </div>
        </div>
      </article>
    </main>

    <footer>
      <div class="copyrightText">
        <p>
          copyright 2023
          <a
            href="https://instagram.com/pusaka.himatif?igshid=NTA5ZTk1NTc="
            target="_blank"
            rel="nofollow"
            title="Menuju Instagram"
          >Divisi Kewirausahaan</a>. All Right Reserved
          &nbsp;|&nbsp;
          <a href="admin/login.php" style="color:#9fdaed;opacity:0.6;font-size:0.85em;">Admin</a>
        </p>
      </div>
    </footer>

    <script>
      /* Nav scroll sticky (minimal, tanpa kalkulator) */
      window.addEventListener('scroll', function () {
        const nav = document.querySelector('nav');
        nav.classList.toggle('sticky', window.scrollY > 0);
      });
      function toggleMenu() {
        document.querySelector('.menuToggle').classList.toggle('active');
        document.querySelector('.navigation').classList.toggle('active');
      }
    </script>
  </body>
</html>
