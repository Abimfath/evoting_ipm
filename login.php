<?php
// ============================================================
// ADMIN LOGIN — admin/login.php
// ============================================================
session_start();
require_once '../config/koneksi.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($koneksi, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $result = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username' LIMIT 1");
        if ($result && mysqli_num_rows($result) === 1) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — E-Voting IPM</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-page">

  <div class="auth-hero" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f30a0 100%);">
    <div class="auth-hero-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:50px;height:50px;fill:none;stroke:#fff;stroke-width:1.5;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
      </svg>
    </div>
    <h1>Panel Admin</h1>
    <p>Akses terbatas untuk administrator sistem E-Voting IPM.</p>
    <div class="auth-hero-badge">
      <span>🔐 Terenkripsi</span>
      <span>👑 Admin Only</span>
    </div>
  </div>

  <div class="auth-form-side">
    <div class="auth-form-box">
      <h2>Login Admin</h2>
      <p class="subtitle">Masukkan kredensial administrator Anda</p>

      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" data-validate>
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-icon-wrap">
            <span class="icon">👤</span>
            <input type="text" id="username" name="username" class="form-control"
              placeholder="Masukkan username admin"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              required autocomplete="username">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-icon-wrap">
            <span class="icon">🔑</span>
            <input type="password" id="password" name="password" class="form-control"
              placeholder="Masukkan password admin"
              required autocomplete="current-password">
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-admin-login">
          🔐 Masuk ke Panel Admin
        </button>
      </form>

      <p class="text-center text-muted mt-3" style="font-size:13px;">
        <a href="../index.php" style="color:var(--primary);font-weight:600;">← Kembali ke Login Pemilih</a>
      </p>


    </div>
  </div>

</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
