<?php
// ============================================================
// HAPUS KANDIDAT — admin/hapus_kandidat.php
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

// Ambil data kandidat dulu
$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kandidat WHERE id = $id LIMIT 1"));
if (!$row) {
    header('Location: dashboard.php');
    exit;
}

// Hapus foto dari server jika bukan default
$foto_path = '../assets/kandidat_foto/' . $row['foto'];
if ($row['foto'] !== 'default.png' && file_exists($foto_path)) {
    unlink($foto_path);
}

// Hapus dari database (suara terkait otomatis terhapus karena ON DELETE CASCADE)
mysqli_query($koneksi, "DELETE FROM kandidat WHERE id = $id");

header('Location: dashboard.php?hapus=1');
exit;
?>
