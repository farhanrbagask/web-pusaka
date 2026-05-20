<?php
/**
 * Setup Script — Pusaka Himatif
 * =====================================================
 * Jalankan SEKALI untuk setup password admin.
 * Setelah selesai, HAPUS file ini dari server!
 * Akses: http://localhost/web-pusaka/setup.php
 * =====================================================
 */

// Keamanan: hanya bisa diakses dari localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    die('Akses ditolak. Setup hanya bisa dilakukan dari localhost.');
}

$done    = false;
$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Import database
    if ($action === 'import_db') {
        require_once __DIR__ . '/config/database.php';
        try {
            $db  = getDB();
            $sql = file_get_contents(__DIR__ . '/database/pusaka_himatif.sql');
            // Eksekusi per statement
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($s) => $s !== ''
            );
            foreach ($statements as $stmt) {
                $db->exec($stmt);
            }
            $message = '✅ Database berhasil diimport!';
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }

    // Set/ganti password admin
    if ($action === 'set_password') {
        $username = trim($_POST['username'] ?? 'admin');
        $password = $_POST['new_password'] ?? '';
        $nama     = trim($_POST['nama'] ?? 'Admin Pusaka Himatif');

        if (strlen($password) < 8) {
            $error = 'Password minimal 8 karakter.';
        } else {
            require_once __DIR__ . '/config/database.php';
            try {
                $db   = getDB();
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // Upsert: update jika ada, insert jika tidak
                $check = $db->prepare('SELECT id FROM admin WHERE username = ?');
                $check->execute([$username]);
                $existing = $check->fetch();

                if ($existing) {
                    $upd = $db->prepare('UPDATE admin SET password = ?, nama = ? WHERE username = ?');
                    $upd->execute([$hash, $nama, $username]);
                    $message = "✅ Password untuk <strong>$username</strong> berhasil diperbarui!";
                } else {
                    $ins = $db->prepare('INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)');
                    $ins->execute([$username, $hash, $nama]);
                    $message = "✅ Admin <strong>$username</strong> berhasil dibuat!";
                }
            } catch (Exception $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Setup — Pusaka Himatif</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .card {
      background: #fff;
      border-radius: 16px;
      padding: 40px;
      max-width: 560px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    h1 { font-size: 1.5em; color: #0f1f35; margin-bottom: 4px; }
    .subtitle { color: #999; font-size: 0.85em; margin-bottom: 28px; }
    .alert {
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 0.9em;
    }
    .alert-danger  { background: #fff0f0; color: #dc2626; border:1px solid #fcc; }
    .alert-success { background: #f0fff4; color: #16a34a; border:1px solid #bbf7d0; }
    .alert-warn    { background: #fffbeb; color: #b45309; border:1px solid #fde68a; }
    .section { margin-bottom: 32px; padding-bottom: 28px; border-bottom: 1px solid #eef1f5; }
    .section:last-child { border-bottom: none; margin-bottom: 0; }
    h2 { font-size: 1em; color: #111; margin-bottom: 16px; }
    label { display: block; font-size: 0.82em; color: #666; margin-bottom: 6px; font-weight: 500; }
    input[type=text], input[type=password] {
      width: 100%;
      padding: 11px 14px;
      border: 2px solid #e8eef2;
      border-radius: 10px;
      font-size: 0.9em;
      outline: none;
      margin-bottom: 14px;
      transition: border-color 0.2s;
    }
    input:focus { border-color: #9fdaed; }
    button {
      padding: 12px 24px;
      background: linear-gradient(135deg, #9fdaed, #6fc8e4);
      color: #0f1f35;
      border: none;
      border-radius: 10px;
      font-size: 0.9em;
      font-weight: 700;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    button:hover { opacity: 0.85; }
    .warn-box {
      background: #fff8e1;
      border: 1px solid #ffe082;
      border-radius: 10px;
      padding: 14px 16px;
      font-size: 0.82em;
      color: #7c5a00;
      margin-top: 24px;
      line-height: 1.6;
    }
    a { color: #6fc8e4; }
  </style>
</head>
<body>
  <div class="card">
    <h1>⚙️ Setup Pusaka Himatif</h1>
    <p class="subtitle">Jalankan sekali untuk konfigurasi awal. Hapus file ini setelah selesai!</p>

    <?php if ($error): ?>
      <div class="alert alert-danger">⚠️ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($message): ?>
      <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <!-- Step 1: Import DB -->
    <div class="section">
      <h2>📦 Langkah 1 — Import Database</h2>
      <p style="font-size:0.85em;color:#666;margin-bottom:16px;">
        Pastikan database <code>pusaka_himatif</code> sudah dibuat di MySQL (bisa lewat phpMyAdmin).
        Konfigurasi DB di <code>config/database.php</code>.
      </p>
      <form method="POST">
        <input type="hidden" name="action" value="import_db" />
        <button type="submit">🚀 Import Schema & Data</button>
      </form>
    </div>

    <!-- Step 2: Set Password -->
    <div class="section">
      <h2>🔐 Langkah 2 — Buat / Ganti Password Admin</h2>
      <form method="POST">
        <input type="hidden" name="action" value="set_password" />
        <label>Username Admin</label>
        <input type="text" name="username" value="admin" required />
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="Admin Pusaka Himatif" required />
        <label>Password Baru (min. 8 karakter)</label>
        <input type="password" name="new_password" placeholder="Masukkan password baru..." required />
        <button type="submit">💾 Simpan Password</button>
      </form>
    </div>

    <!-- Done -->
    <div class="section">
      <h2>✅ Langkah 3 — Login ke Admin Panel</h2>
      <p style="font-size:0.85em;color:#666;margin-bottom:14px;">
        Setelah setup selesai, login ke admin panel:
      </p>
      <a href="admin/login.php" style="display:inline-block;padding:10px 20px;background:#0f1f35;color:#9fdaed;border-radius:10px;font-weight:600;text-decoration:none;">
        → Buka Admin Panel
      </a>
    </div>

    <div class="warn-box">
      ⚠️ <strong>PENTING:</strong> Setelah setup selesai, <strong>hapus file <code>setup.php</code></strong>
      dari server untuk keamanan! File ini hanya boleh diakses dari localhost.
    </div>
  </div>
</body>
</html>
