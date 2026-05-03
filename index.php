<?php
// ============================================================
// HALAMAN LOGIN PEMILIH — index.php
// ============================================================
session_start();
require_once 'config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['pemilih_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis      = sanitize($koneksi, $_POST['nis'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nis) || empty($password)) {
        $error = 'NIS dan password tidak boleh kosong.';
    } else {
        $sql    = "SELECT * FROM pemilih WHERE nis = '$nis' LIMIT 1";
        $result = mysqli_query($koneksi, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $pemilih = mysqli_fetch_assoc($result);
            if (password_verify($password, $pemilih['password'])) {
                session_regenerate_id(true);
                $_SESSION['pemilih_id']   = $pemilih['id'];
                $_SESSION['pemilih_nama'] = $pemilih['nama'];
                $_SESSION['pemilih_nis']  = $pemilih['nis'];
                $_SESSION['status_memilih'] = $pemilih['status_memilih'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Password salah. Silakan coba lagi.';
            }
        } else {
            $error = 'NIS tidak ditemukan dalam sistem.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Pemilih — E-Voting IPM</title>
  <meta name="description" content="Login sistem E-Voting pemilihan formatur IPM. Masukkan NIS dan password Anda.">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">

  <!-- Hero Side -->
  <div class="auth-hero">
    <div class="auth-hero-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
      </svg>
    </div>
    <h1>E-Voting IPM</h1>
    <p>Sistem Pemilihan Formatur Ikatan Pelajar Muhammadiyah secara digital, aman, dan transparan.</p>
    <div class="auth-hero-badge">
      <span>🔒 Aman</span>
      <span>⚡ Real-time</span>
      <span>✅ Terverifikasi</span>
    </div>
  </div>

  <!-- Form Side -->
  <div class="auth-form-side">
    <div class="auth-form-box">
      <h2>Selamat Datang</h2>
      <p class="subtitle">Masukkan NIS dan password Anda untuk melanjutkan</p>

      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="index.php" data-validate>
        <div class="form-group">
          <label class="form-label" for="nis">Nomor Induk Siswa (NIS)</label>
          <div class="input-icon-wrap">
            <span class="icon">🎓</span>
            <input
              type="text"
              id="nis"
              name="nis"
              class="form-control"
              placeholder="Contoh: 2024001"
              value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>"
              required
              autocomplete="username"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-icon-wrap">
            <span class="icon">🔑</span>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password"
              required
              autocomplete="current-password"
            >
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-login">
          🗳️ Masuk ke Sistem Voting
        </button>
      </form>

      <p class="text-center text-muted mt-3" style="font-size:13px;">
        Panel Admin? <a href="admin/login.php" style="color:var(--primary);font-weight:600;">Masuk di sini →</a>
      </p>


    </div>
  </div>

</div>
<script src="assets/js/main.js"></script>
</body>
</html>
