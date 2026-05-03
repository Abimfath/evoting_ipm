<?php
// ============================================================
// ADMIN DASHBOARD — admin/dashboard.php
// ============================================================
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Stats
$total_pemilih  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM pemilih"))['c'];
$sudah_memilih  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM pemilih WHERE status_memilih = 1"))['c'];
$belum_memilih  = $total_pemilih - $sudah_memilih;
$total_kandidat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM kandidat"))['c'];
$total_suara    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM suara"))['c'];
$partisipasi    = $total_pemilih > 0 ? round(($sudah_memilih / $total_pemilih) * 100) : 0;

// Kandidat dengan suara terbanyak
$top_kandidat_q = mysqli_query($koneksi,
    "SELECT k.nama, COUNT(s.id) AS jumlah_suara
     FROM kandidat k
     LEFT JOIN suara s ON k.id = s.id_kandidat
     GROUP BY k.id ORDER BY jumlah_suara DESC LIMIT 1"
);
$top_kandidat = mysqli_fetch_assoc($top_kandidat_q);

// Semua kandidat untuk tabel ringkasan
$kandidat_list = mysqli_query($koneksi,
    "SELECT k.id, k.nama, k.foto, COUNT(s.id) AS jumlah_suara
     FROM kandidat k
     LEFT JOIN suara s ON k.id = s.id_kandidat
     GROUP BY k.id ORDER BY jumlah_suara DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — E-Voting IPM</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-box">🗳️</div>
        <div class="logo-text">
          <div class="name">E-Voting IPM</div>
          <div class="sub">Admin Panel</div>
        </div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="sidebar-section-label">Utama</div>
      <a href="dashboard.php" class="sidebar-link active" id="nav-dashboard">
        <span class="link-icon">📊</span> Dashboard
      </a>
      <a href="hasil.php" class="sidebar-link" id="nav-hasil">
        <span class="link-icon">🏆</span> Hasil Voting
      </a>
      <div class="sidebar-section-label">Manajemen</div>
      <a href="tambah_kandidat.php" class="sidebar-link" id="nav-tambah">
        <span class="link-icon">➕</span> Tambah Kandidat
      </a>
      <a href="dashboard.php#kandidat-table" class="sidebar-link" id="nav-kandidat">
        <span class="link-icon">👤</span> Data Kandidat
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="logout_admin.php" class="sidebar-link" style="color:#f87171;">
        <span class="link-icon">🚪</span> Keluar
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="admin-main">
    <div class="admin-topbar">
      <h1>📊 Dashboard</h1>
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:14px;color:var(--gray);">Admin: <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
        <a href="logout_admin.php" class="btn btn-danger btn-sm">🚪 Logout</a>
      </div>
    </div>

    <div class="admin-content">

      <!-- STAT CARDS -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $total_pemilih ?>"><?= $total_pemilih ?></div>
            <div class="stat-label">Total Pemilih</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✅</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $sudah_memilih ?>"><?= $sudah_memilih ?></div>
            <div class="stat-label">Sudah Memilih</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red">⏳</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $belum_memilih ?>"><?= $belum_memilih ?></div>
            <div class="stat-label">Belum Memilih</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow">🏆</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $total_kandidat ?>"><?= $total_kandidat ?></div>
            <div class="stat-label">Total Kandidat</div>
          </div>
        </div>
      </div>

      <!-- PARTISIPASI & TOP KANDIDAT ROW -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">

        <div class="card">
          <div class="card-header"><h3>📊 Partisipasi</h3><span class="badge badge-info"><?= $partisipasi ?>%</span></div>
          <div class="card-body">
            <div style="margin-bottom:12px;display:flex;align-items:center;gap:16px;">
              <div class="progress-bar-wrap">
                <div class="progress-bar-fill" data-width="<?= $partisipasi ?>" style="width:0%"></div>
              </div>
              <span style="font-weight:700;color:var(--primary);white-space:nowrap;"><?= $sudah_memilih ?>/<?= $total_pemilih ?></span>
            </div>
            <p style="font-size:13px;color:var(--gray);">
              <?= $belum_memilih ?> pemilih masih belum menggunakan hak suara.
            </p>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3>🥇 Kandidat Unggul</h3></div>
          <div class="card-body">
            <?php if ($top_kandidat && $top_kandidat['jumlah_suara'] > 0): ?>
              <div style="font-size:22px;font-weight:800;color:var(--dark);margin-bottom:4px;">
                <?= htmlspecialchars($top_kandidat['nama']) ?>
              </div>
              <div class="badge badge-success" style="font-size:14px;padding:6px 14px;">
                🗳️ <?= $top_kandidat['jumlah_suara'] ?> Suara
              </div>
            <?php else: ?>
              <p style="color:var(--gray);font-size:14px;">Belum ada suara masuk.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- TABEL KANDIDAT -->
      <div class="card" id="kandidat-table">
        <div class="card-header">
          <h3>👤 Manajemen Kandidat</h3>
          <a href="tambah_kandidat.php" class="btn btn-primary btn-sm" id="btn-tambah-kandidat">➕ Tambah Kandidat</a>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Nama Kandidat</th>
                <th>Suara</th>
                <th>Persentase</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($kandidat_list) === 0): ?>
                <tr><td colspan="6" class="text-center" style="padding:40px;color:var(--gray);">Belum ada data kandidat.</td></tr>
              <?php else: ?>
                <?php $no = 1; while ($k = mysqli_fetch_assoc($kandidat_list)):
                  $pct = $total_suara > 0 ? round(($k['jumlah_suara'] / $total_suara) * 100) : 0;
                ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td>
                    <?php
                      $fp = '../assets/kandidat_foto/' . $k['foto'];
                      if (!empty($k['foto']) && $k['foto'] !== 'default.png' && file_exists($fp)):
                    ?>
                      <img src="<?= htmlspecialchars($fp) ?>" class="foto-kandidat" alt="Foto">
                    <?php else: ?>
                      <div class="foto-placeholder-sm">🧑</div>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
                  <td><span class="badge badge-info"><?= $k['jumlah_suara'] ?> suara</span></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;min-width:120px;">
                      <div class="progress-bar-wrap" style="flex:1;">
                        <div class="progress-bar-fill" data-width="<?= $pct ?>" style="width:0%;background:linear-gradient(90deg,var(--accent),#f97316);"></div>
                      </div>
                      <span style="font-size:12px;font-weight:700;color:var(--gray);white-space:nowrap;"><?= $pct ?>%</span>
                    </div>
                  </td>
                  <td>
                    <div style="display:flex;gap:8px;">
                      <a href="edit_kandidat.php?id=<?= $k['id'] ?>" class="btn btn-ghost btn-sm" id="btn-edit-<?= $k['id'] ?>">✏️ Edit</a>
                      <a href="hapus_kandidat.php?id=<?= $k['id'] ?>" class="btn btn-danger btn-sm btn-delete-confirm" id="btn-hapus-<?= $k['id'] ?>">🗑️ Hapus</a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- /admin-content -->
  </div><!-- /admin-main -->
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
