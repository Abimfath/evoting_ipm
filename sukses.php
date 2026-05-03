<?php
// ============================================================
// HALAMAN SUKSES — sukses.php
// ============================================================
session_start();

if (!isset($_SESSION['pemilih_id'])) {
    header('Location: index.php');
    exit;
}

// Hanya bisa diakses jika sudah memilih
if ((int)$_SESSION['status_memilih'] !== 1) {
    header('Location: kandidat.php');
    exit;
}

$nama_pemilih = $_SESSION['pemilih_nama'] ?? 'Pemilih';
$voted_for    = $_SESSION['voted_for'] ?? '';
// Hapus voted_for dari session setelah ditampilkan
unset($_SESSION['voted_for']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voting Berhasil — E-Voting IPM</title>
  <meta name="description" content="Suara Anda telah berhasil dicatat dalam sistem E-Voting IPM.">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .confetti-wrap {
      position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
    }
    .confetti-piece {
      position: absolute;
      width: 10px; height: 14px;
      border-radius: 2px;
      top: -20px;
      animation: fall linear infinite;
    }
    @keyframes fall {
      to { transform: translateY(105vh) rotate(720deg); opacity: 0; }
    }
  </style>
</head>
<body>
<div class="confetti-wrap" id="confetti"></div>

<div class="sukses-page">
  <div class="sukses-card" style="position:relative;z-index:1;">

    <div class="sukses-icon">✅</div>

    <h1>Suara Berhasil Dicatat!</h1>

    <p>
      Terima kasih, <strong><?= htmlspecialchars($nama_pemilih) ?></strong>!
      Suara Anda telah berhasil dicatat dalam sistem E-Voting IPM.
      <?php if ($voted_for): ?>
        <br><br>Anda memilih: <strong style="color:var(--primary);"><?= htmlspecialchars($voted_for) ?></strong>
      <?php endif; ?>
    </p>

    <div style="background:var(--gray-light);border-radius:12px;padding:18px 20px;margin-bottom:32px;text-align:left;">
      <div style="font-size:13px;color:var(--gray);line-height:1.8;">
        <div>🕐 Waktu: <strong><?= date('d F Y, H:i') ?> WIB</strong></div>
        <div>🔒 Status: <span class="badge badge-success">Terverifikasi</span></div>
        <div style="margin-top:8px;font-size:12px;color:#aaa;">Simpan informasi ini sebagai bukti partisipasi Anda.</div>
      </div>
    </div>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="dashboard.php" class="btn btn-primary btn-lg">📊 Kembali ke Dashboard</a>
      <a href="logout.php" class="btn btn-ghost btn-lg">🚪 Keluar</a>
    </div>

    <p style="margin-top:28px;font-size:12px;color:var(--gray);">
      "Setiap suara adalah amanah. Gunakan dengan penuh tanggung jawab."
    </p>
  </div>
</div>

<script>
// Confetti animation
(function() {
  var colors = ['#1a4fcf','#f5a623','#22c55e','#ef4444','#8b5cf6','#06b6d4'];
  var container = document.getElementById('confetti');
  for (var i = 0; i < 60; i++) {
    var el = document.createElement('div');
    el.className = 'confetti-piece';
    el.style.left = Math.random() * 100 + 'vw';
    el.style.background = colors[Math.floor(Math.random() * colors.length)];
    el.style.animationDuration = (Math.random() * 3 + 2) + 's';
    el.style.animationDelay = (Math.random() * 3) + 's';
    el.style.opacity = Math.random() * 0.7 + 0.3;
    el.style.width = (Math.random() * 8 + 6) + 'px';
    el.style.height = (Math.random() * 10 + 8) + 'px';
    container.appendChild(el);
  }
})();
</script>
</body>
</html>
