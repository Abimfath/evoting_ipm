<?php
// ============================================================
// HASIL VOTING — admin/hasil.php
// ============================================================
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Stats umum
$total_pemilih = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM pemilih"))['c'];
$sudah_memilih = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM pemilih WHERE status_memilih = 1"))['c'];
$total_suara   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM suara"))['c'];
$partisipasi   = $total_pemilih > 0 ? round(($sudah_memilih / $total_pemilih) * 100) : 0;

// Hasil per kandidat
$hasil_query = mysqli_query($koneksi,
    "SELECT k.id, k.nama, k.foto, COUNT(s.id) AS jumlah_suara
     FROM kandidat k
     LEFT JOIN suara s ON k.id = s.id_kandidat
     GROUP BY k.id
     ORDER BY jumlah_suara DESC"
);

$hasil_data   = [];
$chart_labels = [];
$chart_values = [];
$chart_colors = ['#1a4fcf','#f5a623','#22c55e','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899'];

$rank = 1;
while ($r = mysqli_fetch_assoc($hasil_query)) {
    $r['rank'] = $rank++;
    $r['pct']  = $total_suara > 0 ? round(($r['jumlah_suara'] / $total_suara) * 100, 1) : 0;
    $hasil_data[]   = $r;
    $chart_labels[] = $r['nama'];
    $chart_values[] = (int)$r['jumlah_suara'];
}

// Pemenang (kandidat dengan suara terbanyak)
$pemenang = !empty($hasil_data) ? $hasil_data[0] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Voting — Admin E-Voting IPM</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .rank-badge {
      width: 36px; height: 36px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 15px; flex-shrink: 0;
    }
    .rank-1 { background: linear-gradient(135deg,#f5a623,#d4881a); color:#fff; box-shadow:0 4px 12px rgba(245,166,35,0.4); }
    .rank-2 { background: linear-gradient(135deg,#94a3b8,#64748b); color:#fff; }
    .rank-3 { background: linear-gradient(135deg,#cd7c3f,#a0522d); color:#fff; }
    .rank-other { background: var(--gray-light); color: var(--gray); }
    .hasil-row {
      display: flex; align-items: center; gap: 16px;
      padding: 16px 0;
      border-bottom: 1px solid var(--border);
    }
    .hasil-row:last-child { border-bottom: none; }
    .hasil-info { flex: 1; }
    .hasil-nama { font-weight: 700; font-size: 15px; color: var(--dark); margin-bottom: 6px; }
    .hasil-bar-wrap { display: flex; align-items: center; gap: 12px; }
    .print-btn { display: none; }
    @media print {
      .admin-sidebar, .admin-topbar, .print-hide { display: none !important; }
      .admin-main { margin: 0; }
      .admin-content { padding: 0; }
      .print-btn { display: inline-flex !important; }
    }
  </style>
</head>
<body>
<div class="admin-layout">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-box">🗳️</div>
        <div class="logo-text"><div class="name">E-Voting IPM</div><div class="sub">Admin Panel</div></div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="sidebar-section-label">Utama</div>
      <a href="dashboard.php" class="sidebar-link" id="nav-dashboard">📊 Dashboard</a>
      <a href="hasil.php" class="sidebar-link active" id="nav-hasil">🏆 Hasil Voting</a>
      <div class="sidebar-section-label">Manajemen</div>
      <a href="tambah_kandidat.php" class="sidebar-link" id="nav-tambah">➕ Tambah Kandidat</a>
    </nav>
    <div class="sidebar-footer">
      <a href="logout_admin.php" class="sidebar-link" style="color:#f87171;">🚪 Keluar</a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="admin-main">
    <div class="admin-topbar print-hide">
      <h1>🏆 Hasil Voting</h1>
      <div style="display:flex;gap:10px;">
        <button onclick="window.print()" class="btn btn-ghost btn-sm" id="btn-cetak">🖨️ Cetak</button>
        <a href="dashboard.php" class="btn btn-ghost btn-sm">← Dashboard</a>
      </div>
    </div>

    <div class="admin-content">

      <!-- SUMMARY STATS -->
      <div class="stat-grid" style="margin-bottom:24px;">
        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $total_pemilih ?>"><?= $total_pemilih ?></div>
            <div class="stat-label">Total Pemilih</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">🗳️</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $total_suara ?>"><?= $total_suara ?></div>
            <div class="stat-label">Total Suara Masuk</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow">📊</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $partisipasi ?>"><?= $partisipasi ?></div>
            <div class="stat-label">% Partisipasi</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon <?= $total_pemilih - $sudah_memilih > 0 ? 'red' : 'green' ?>">⏳</div>
          <div class="stat-info">
            <div class="stat-value" data-target="<?= $total_pemilih - $sudah_memilih ?>"><?= $total_pemilih - $sudah_memilih ?></div>
            <div class="stat-label">Belum Memilih</div>
          </div>
        </div>
      </div>

      <!-- PEMENANG SEMENTARA -->
      <?php if ($pemenang && $pemenang['jumlah_suara'] > 0): ?>
      <div class="card" style="margin-bottom:24px;background:linear-gradient(135deg,#0f30a0,#1a4fcf,#2563eb);color:#fff;border:none;">
        <div class="card-body" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
          <div style="font-size:56px;">🥇</div>
          <div>
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;opacity:0.7;margin-bottom:6px;">Kandidat Unggul Sementara</div>
            <div style="font-size:1.8rem;font-weight:800;margin-bottom:8px;"><?= htmlspecialchars($pemenang['nama']) ?></div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
              <span style="background:rgba(255,255,255,0.2);padding:5px 14px;border-radius:999px;font-size:14px;font-weight:600;">🗳️ <?= $pemenang['jumlah_suara'] ?> Suara</span>
              <span style="background:rgba(255,255,255,0.2);padding:5px 14px;border-radius:999px;font-size:14px;font-weight:600;">📊 <?= $pemenang['pct'] ?>% Perolehan</span>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

        <!-- TABEL HASIL -->
        <div class="card">
          <div class="card-header"><h3>📋 Perolehan Suara</h3></div>
          <div class="card-body" style="padding:20px 24px;">
            <?php if (empty($hasil_data)): ?>
              <p style="color:var(--gray);text-align:center;padding:30px 0;">Belum ada suara masuk.</p>
            <?php else: ?>
              <?php foreach ($hasil_data as $i => $h):
                $colors = ['#1a4fcf','#f5a623','#22c55e','#ef4444','#8b5cf6'];
                $c = $colors[$i] ?? '#64748b';
              ?>
              <div class="hasil-row">
                <div class="rank-badge <?= $h['rank'] <= 3 ? 'rank-' . $h['rank'] : 'rank-other' ?>">
                  <?= $h['rank'] === 1 ? '🥇' : ($h['rank'] === 2 ? '🥈' : ($h['rank'] === 3 ? '🥉' : $h['rank'])) ?>
                </div>
                <div class="hasil-info">
                  <div class="hasil-nama"><?= htmlspecialchars($h['nama']) ?></div>
                  <div class="hasil-bar-wrap">
                    <div class="progress-bar-wrap">
                      <div class="progress-bar-fill" data-width="<?= $h['pct'] ?>"
                        style="width:0%;background:<?= $c ?>;"></div>
                    </div>
                    <span style="font-size:12px;color:var(--gray);white-space:nowrap;font-weight:600;"><?= $h['pct'] ?>%</span>
                  </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                  <div style="font-size:1.4rem;font-weight:800;color:var(--dark);"><?= $h['jumlah_suara'] ?></div>
                  <div style="font-size:11px;color:var(--gray);">suara</div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- CHART -->
        <div class="card">
          <div class="card-header"><h3>📊 Grafik Perolehan Suara</h3></div>
          <div class="card-body">
            <?php if (empty($hasil_data) || $total_suara === 0): ?>
              <div style="text-align:center;padding:60px 0;color:var(--gray);">
                <div style="font-size:48px;margin-bottom:12px;">📊</div>
                <p>Belum ada data suara untuk ditampilkan.</p>
              </div>
            <?php else: ?>
              <div class="chart-container" style="position:relative;height:300px;">
                <canvas id="chartHasil"></canvas>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- TABEL LENGKAP -->
      <div class="card">
        <div class="card-header"><h3>📄 Tabel Lengkap Hasil Voting</h3></div>
        <div class="table-wrapper">
          <table id="tabel-hasil">
            <thead>
              <tr>
                <th>Peringkat</th>
                <th>Foto</th>
                <th>Nama Kandidat</th>
                <th>Jumlah Suara</th>
                <th>Persentase</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($hasil_data)): ?>
                <tr><td colspan="6" class="text-center" style="padding:40px;color:var(--gray);">Belum ada data.</td></tr>
              <?php else: ?>
                <?php foreach ($hasil_data as $h):
                  $fp = '../assets/kandidat_foto/' . $h['foto'];
                ?>
                <tr>
                  <td>
                    <span class="rank-badge <?= $h['rank'] <= 3 ? 'rank-'.$h['rank'] : 'rank-other' ?>" style="margin:0 auto;">
                      <?= $h['rank'] === 1 ? '🥇' : ($h['rank'] === 2 ? '🥈' : ($h['rank'] === 3 ? '🥉' : '#'.$h['rank'])) ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($h['foto']) && $h['foto'] !== 'default.png' && file_exists($fp)): ?>
                      <img src="<?= htmlspecialchars($fp) ?>" class="foto-kandidat" alt="Foto">
                    <?php else: ?>
                      <div class="foto-placeholder-sm">🧑</div>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($h['nama']) ?></strong></td>
                  <td>
                    <span style="font-size:1.3rem;font-weight:800;color:var(--dark);"><?= $h['jumlah_suara'] ?></span>
                    <span style="font-size:12px;color:var(--gray);"> suara</span>
                  </td>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div class="progress-bar-wrap" style="min-width:80px;">
                        <div class="progress-bar-fill" data-width="<?= $h['pct'] ?>" style="width:0%;"></div>
                      </div>
                      <span style="font-weight:700;color:var(--primary);font-size:13px;"><?= $h['pct'] ?>%</span>
                    </div>
                  </td>
                  <td>
                    <?php if ($h['rank'] === 1 && $h['jumlah_suara'] > 0): ?>
                      <span class="badge badge-success">🏆 Unggul</span>
                    <?php elseif ($h['jumlah_suara'] === 0): ?>
                      <span class="badge badge-danger">0 Suara</span>
                    <?php else: ?>
                      <span class="badge badge-info">Bersaing</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer" style="font-size:12px;color:var(--gray);">
          Data diperbarui secara real-time. Terakhir dimuat: <?= date('d M Y H:i:s') ?> WIB
        </div>
      </div>

    </div><!-- /admin-content -->
  </div><!-- /admin-main -->
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
<?php if (!empty($hasil_data) && $total_suara > 0): ?>
(function () {
  var labels = <?= json_encode($chart_labels) ?>;
  var values = <?= json_encode($chart_values) ?>;
  var palette = ['#1a4fcf','#f5a623','#22c55e','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899'];
  var colors  = labels.map(function(_, i){ return palette[i % palette.length]; });

  var ctx = document.getElementById('chartHasil');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: values,
        backgroundColor: colors,
        borderColor: '#ffffff',
        borderWidth: 3,
        hoverOffset: 10
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 16,
            font: { size: 13, family: 'Plus Jakarta Sans', weight: '600' },
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              var total = ctx.dataset.data.reduce(function(a,b){ return a+b; }, 0);
              var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
              return ' ' + ctx.parsed + ' suara (' + pct + '%)';
            }
          }
        }
      }
    }
  });

  // Bar chart below doughnut — swap to bar on small screens
  if (window.innerWidth < 700) {
    // re-render as bar
    Chart.getChart('chartHasil').destroy();
    new Chart(document.getElementById('chartHasil'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Suara',
          data: values,
          backgroundColor: colors,
          borderRadius: 8,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  }
})();
<?php endif; ?>
</script>
</body>
</html>
