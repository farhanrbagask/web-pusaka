<?php
/**
 * Admin Login — Pusaka Himatif
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, username, password, nama FROM admin WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_user'] = $admin['username'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Admin — Pusaka Himatif</title>
  <link rel="icon" href="../Assets/logo.png" type="image/x-icon" />
  <link rel="stylesheet" href="assets/admin.css" />
</head>
<body class="login-page">

  <div class="login-container">
    <div class="login-card">
      <div class="login-logo">
        <span class="logo-text">Pusaka<span class="accent">.</span></span>
        <p class="login-subtitle">Admin Panel</p>
      </div>

      <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
          <span class="alert-icon">⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="login-form" autocomplete="off">
        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <span class="input-icon">👤</span>
            <input
              type="text"
              id="username"
              name="username"
              placeholder="Masukkan username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              required
              autofocus
            />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan password"
              required
            />
          </div>
        </div>

        <button type="submit" class="btn-login">Masuk ke Dashboard</button>
      </form>

      <p class="login-back">
        <a href="../index.php">← Kembali ke Website</a>
      </p>
    </div>
  </div>

</body>
</html>
