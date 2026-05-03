<?php
// ============================================================
// HALAMAN DAFTAR KANDIDAT — kandidat.php
// ============================================================
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['pemilih_id'])) {
    header('Location: index.php');
    exit;
}

$status_memilih = (int)$_SESSION['status_memilih'];

// Ambil semua kandidat
$result = mysqli_query($koneksi, "SELECT * FROM kandidat ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Kandidat — E-Voting IPM</title>
  <meta name="description" content="Daftar kandidat formatur IPM. Lihat profil, visi, dan misi setiap kandidat.">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="page-wrapper">

  <nav class="navbar">
    <div class="navbar-brand">
      <div class="logo-box">🗳️</div>
      <div class="brand-text">
        <div class="name">E-Voting IPM</div>
        <div class="tagline">Pemilihan Formatur</div>
      </div>
    </div>
    <div class="navbar-user">
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($_SESSION['pemilih_nama']) ?></div>
        <div class="user-role">NIS: <?= htmlspecialchars($_SESSION['pemilih_nis']) ?></div>
      </div>
      <div class="navbar-avatar"><?= strtoupper(substr($_SESSION['pemilih_nama'], 0, 1)) ?></div>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">← Dashboard</a>
      <a href="logout.php" class="btn btn-ghost btn-sm">🚪 Keluar</a>
    </div>
  </nav>

  <div class="main-content">

    <div class="page-header-row">
      <div class="page-header" style="margin-bottom:0;">
        <h2>👤 Daftar Kandidat</h2>
        <p>Kenali profil setiap kandidat sebelum memberikan suara Anda</p>
      </div>
      <?php if ($status_memilih === 0): ?>
        <span class="badge badge-info" style="font-size:13px;padding:8px 16px;">Belum Memilih</span>
      <?php else: ?>
        <span class="badge badge-success" style="font-size:13px;padding:8px 16px;">✅ Sudah Memilih</span>
      <?php endif; ?>
    </div>

    <?php if ($status_memilih === 1): ?>
      <div class="alert alert-success mt-2">
        ✅ Anda sudah menggunakan hak suara. Anda masih bisa melihat profil kandidat, namun tidak bisa memilih lagi.
      </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) === 0): ?>
      <div class="card mt-3">
        <div class="card-body text-center" style="padding:60px;">
          <div style="font-size:60px;margin-bottom:16px;">📋</div>
          <h3 style="color:var(--dark);margin-bottom:8px;">Belum Ada Kandidat</h3>
          <p class="text-muted">Data kandidat belum tersedia. Silakan hubungi panitia.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="kandidat-grid mt-3">
        <?php $no = 1; while ($kandidat = mysqli_fetch_assoc($result)): ?>
          <div class="kandidat-card">

            <!-- Foto -->
            <?php
              $foto_path = 'assets/kandidat_foto/' . $kandidat['foto'];
              if (!empty($kandidat['foto']) && $kandidat['foto'] !== 'default.png' && file_exists($foto_path)):
            ?>
              <img src="<?= htmlspecialchars($foto_path) ?>" alt="Foto <?= htmlspecialchars($kandidat['nama']) ?>" style="width:100%;height:220px;object-fit:cover;">
            <?php else: ?>
              <div class="photo-placeholder">🧑</div>
            <?php endif; ?>

            <div class="card-content">
              <div class="card-number"><?= $no ?></div>
              <div class="card-name"><?= htmlspecialchars($kandidat['nama']) ?></div>

              <?php if (!empty($kandidat['visi'])): ?>
                <div class="visi-misi-title">🌟 VISI</div>
                <div class="visi-misi-text"><?= nl2br(htmlspecialchars($kandidat['visi'])) ?></div>
              <?php endif; ?>

              <?php if (!empty($kandidat['misi'])): ?>
                <div class="visi-misi-title" style="margin-top:12px;">🎯 MISI</div>
                <div class="visi-misi-text"><?= nl2br(htmlspecialchars($kandidat['misi'])) ?></div>
              <?php endif; ?>
            </div>

            <div class="card-actions">
              <?php if ($status_memilih === 0): ?>
                <a href="voting.php?id=<?= $kandidat['id'] ?>"
                   class="btn btn-primary btn-block btn-vote-confirm"
                   data-name="<?= htmlspecialchars($kandidat['nama']) ?>"
                   id="vote-btn-<?= $kandidat['id'] ?>">
                  🗳️ Pilih Kandidat Ini
                </a>
              <?php else: ?>
                <button class="btn btn-ghost btn-block" disabled style="cursor:not-allowed;opacity:0.6;">
                  🔒 Sudah Memilih
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php $no++; endwhile; ?>
      </div>
    <?php endif; ?>

  </div>

  <footer class="footer">
    &copy; <?= date('Y') ?> E-Voting IPM &mdash; Sistem Pemilihan Digital Formatur IPM
  </footer>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
