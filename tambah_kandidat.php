<?php
// ============================================================
// TAMBAH KANDIDAT — admin/tambah_kandidat.php
// ============================================================
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($koneksi, $_POST['nama'] ?? '');
    $visi = sanitize($koneksi, $_POST['visi'] ?? '');
    $misi = sanitize($koneksi, $_POST['misi'] ?? '');
    $foto = 'default.png';

    if (empty($nama)) {
        $error = 'Nama kandidat tidak boleh kosong.';
    } else {
        // Upload foto
        if (!empty($_FILES['foto']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type     = $_FILES['foto']['type'];
            $file_size     = $_FILES['foto']['size'];
            $max_size      = 3 * 1024 * 1024; // 3MB

            if (!in_array($file_type, $allowed_types)) {
                $error = 'Format foto tidak valid. Gunakan JPG, PNG, GIF, atau WEBP.';
            } elseif ($file_size > $max_size) {
                $error = 'Ukuran foto terlalu besar. Maksimal 3MB.';
            } else {
                $ext       = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto      = 'kandidat_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest      = '../assets/kandidat_foto/' . $foto;

                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                    $error = 'Gagal mengupload foto. Pastikan folder assets/kandidat_foto/ dapat ditulis.';
                    $foto  = 'default.png';
                }
            }
        }

        if (empty($error)) {
            $sql = "INSERT INTO kandidat (nama, foto, visi, misi) VALUES ('$nama', '$foto', '$visi', '$misi')";
            if (mysqli_query($koneksi, $sql)) {
                $success = 'Kandidat berhasil ditambahkan!';
            } else {
                $error = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Kandidat — Admin E-Voting IPM</title>
  <link rel="stylesheet" href="../assets/css/style.css">
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
      <a href="hasil.php" class="sidebar-link" id="nav-hasil">🏆 Hasil Voting</a>
      <div class="sidebar-section-label">Manajemen</div>
      <a href="tambah_kandidat.php" class="sidebar-link active" id="nav-tambah">➕ Tambah Kandidat</a>
    </nav>
    <div class="sidebar-footer">
      <a href="logout_admin.php" class="sidebar-link" style="color:#f87171;">🚪 Keluar</a>
    </div>
  </aside>

  <div class="admin-main">
    <div class="admin-topbar">
      <h1>➕ Tambah Kandidat</h1>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">← Kembali</a>
    </div>

    <div class="admin-content">
      <div class="card" style="max-width:680px;">
        <div class="card-header"><h3>Form Tambah Kandidat</h3></div>
        <div class="card-body">

          <?php if ($error):   ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
          <?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

          <form method="POST" enctype="multipart/form-data" data-validate>

            <div class="form-group">
              <label class="form-label" for="nama">Nama Lengkap Kandidat *</label>
              <input type="text" id="nama" name="nama" class="form-control"
                placeholder="Nama kandidat" required
                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="foto">Foto Kandidat</label>
              <input type="file" id="foto" name="foto" class="form-control"
                accept="image/*" data-preview="preview-foto">
              <p style="font-size:12px;color:var(--gray);margin-top:6px;">Format: JPG, PNG, WEBP. Maks 3MB.</p>
              <img id="preview-foto" src="#" alt="Preview" style="display:none;margin-top:12px;width:120px;height:120px;object-fit:cover;border-radius:12px;border:2px solid var(--border);">
            </div>

            <div class="form-group">
              <label class="form-label" for="visi">Visi</label>
              <textarea id="visi" name="visi" class="form-control" rows="3"
                placeholder="Visi kandidat..."><?= htmlspecialchars($_POST['visi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="misi">Misi</label>
              <textarea id="misi" name="misi" class="form-control" rows="5"
                placeholder="Misi kandidat (gunakan Enter untuk poin baru)..."><?= htmlspecialchars($_POST['misi'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
              <button type="submit" class="btn btn-primary" id="btn-simpan-kandidat">💾 Simpan Kandidat</button>
              <a href="dashboard.php" class="btn btn-ghost">Batal</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
