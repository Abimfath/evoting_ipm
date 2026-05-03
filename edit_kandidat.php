<?php
// ============================================================
// EDIT KANDIDAT — admin/edit_kandidat.php
// ============================================================
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Ambil data kandidat
$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kandidat WHERE id = $id LIMIT 1"));
if (!$row) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($koneksi, $_POST['nama'] ?? '');
    $visi = sanitize($koneksi, $_POST['visi'] ?? '');
    $misi = sanitize($koneksi, $_POST['misi'] ?? '');
    $foto = $row['foto']; // Gunakan foto lama by default

    if (empty($nama)) {
        $error = 'Nama kandidat tidak boleh kosong.';
    } else {
        // Upload foto baru jika ada
        if (!empty($_FILES['foto']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type     = $_FILES['foto']['type'];
            $file_size     = $_FILES['foto']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $error = 'Format foto tidak valid. Gunakan JPG, PNG, GIF, atau WEBP.';
            } elseif ($file_size > 3 * 1024 * 1024) {
                $error = 'Ukuran foto terlalu besar. Maksimal 3MB.';
            } else {
                $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $nama_foto = 'kandidat_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest     = '../assets/kandidat_foto/' . $nama_foto;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                    // Hapus foto lama jika bukan default
                    $old_path = '../assets/kandidat_foto/' . $row['foto'];
                    if ($row['foto'] !== 'default.png' && file_exists($old_path)) {
                        unlink($old_path);
                    }
                    $foto = $nama_foto;
                } else {
                    $error = 'Gagal mengupload foto.';
                }
            }
        }

        if (empty($error)) {
            $sql = "UPDATE kandidat SET nama='$nama', foto='$foto', visi='$visi', misi='$misi' WHERE id=$id";
            if (mysqli_query($koneksi, $sql)) {
                $success = 'Data kandidat berhasil diperbarui!';
                // Refresh data
                $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kandidat WHERE id = $id LIMIT 1"));
            } else {
                $error = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
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
  <title>Edit Kandidat — Admin E-Voting IPM</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">

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
      <a href="tambah_kandidat.php" class="sidebar-link" id="nav-tambah">➕ Tambah Kandidat</a>
    </nav>
    <div class="sidebar-footer">
      <a href="logout_admin.php" class="sidebar-link" style="color:#f87171;">🚪 Keluar</a>
    </div>
  </aside>

  <div class="admin-main">
    <div class="admin-topbar">
      <h1>✏️ Edit Kandidat</h1>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">← Kembali ke Dashboard</a>
    </div>

    <div class="admin-content">
      <div class="card" style="max-width:680px;">
        <div class="card-header">
          <h3>Edit: <?= htmlspecialchars($row['nama']) ?></h3>
        </div>
        <div class="card-body">

          <?php if ($error):   ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
          <?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

          <form method="POST" enctype="multipart/form-data" data-validate>

            <div class="form-group">
              <label class="form-label" for="nama">Nama Lengkap Kandidat *</label>
              <input type="text" id="nama" name="nama" class="form-control"
                placeholder="Nama kandidat" required
                value="<?= htmlspecialchars($row['nama']) ?>">
            </div>

            <div class="form-group">
              <label class="form-label">Foto Saat Ini</label>
              <?php
                $foto_path = '../assets/kandidat_foto/' . $row['foto'];
                if (!empty($row['foto']) && $row['foto'] !== 'default.png' && file_exists($foto_path)):
              ?>
                <div style="margin-bottom:12px;">
                  <img src="<?= htmlspecialchars($foto_path) ?>" alt="Foto kandidat"
                    style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--border);">
                </div>
              <?php else: ?>
                <div style="width:100px;height:100px;background:var(--primary-light);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:40px;margin-bottom:12px;">🧑</div>
              <?php endif; ?>
              <label class="form-label" for="foto">Ganti Foto (kosongkan jika tidak ingin mengganti)</label>
              <input type="file" id="foto" name="foto" class="form-control"
                accept="image/*" data-preview="preview-foto">
              <img id="preview-foto" src="#" alt="Preview baru"
                style="display:none;margin-top:10px;width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--primary);">
            </div>

            <div class="form-group">
              <label class="form-label" for="visi">Visi</label>
              <textarea id="visi" name="visi" class="form-control" rows="3"
                placeholder="Visi kandidat..."><?= htmlspecialchars($row['visi']) ?></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="misi">Misi</label>
              <textarea id="misi" name="misi" class="form-control" rows="5"
                placeholder="Misi kandidat..."><?= htmlspecialchars($row['misi']) ?></textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
              <button type="submit" class="btn btn-primary" id="btn-update-kandidat">💾 Simpan Perubahan</button>
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
