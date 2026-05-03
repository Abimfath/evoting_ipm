<?php
// ============================================================
// PROSES VOTING — voting.php
// ============================================================
session_start();
require_once 'config/koneksi.php';

// Wajib login
if (!isset($_SESSION['pemilih_id'])) {
    header('Location: index.php');
    exit;
}

$id_pemilih  = (int)$_SESSION['pemilih_id'];
$id_kandidat = (int)($_GET['id'] ?? 0);

// Validasi kandidat ID
if ($id_kandidat <= 0) {
    header('Location: kandidat.php');
    exit;
}

// Cek apakah pemilih sudah memilih (double check dari DB)
$cek_pemilih = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT status_memilih FROM pemilih WHERE id = $id_pemilih LIMIT 1")
);

if (!$cek_pemilih || (int)$cek_pemilih['status_memilih'] === 1) {
    $_SESSION['status_memilih'] = 1;
    header('Location: dashboard.php');
    exit;
}

// Cek kandidat valid
$cek_kandidat = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT id, nama FROM kandidat WHERE id = $id_kandidat LIMIT 1")
);

if (!$cek_kandidat) {
    header('Location: kandidat.php');
    exit;
}

// Cek apakah sudah ada suara dari pemilih ini (extra safety)
$cek_suara = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT id FROM suara WHERE id_pemilih = $id_pemilih LIMIT 1")
);

if ($cek_suara) {
    // Sudah ada suara, update status dan redirect
    mysqli_query($koneksi, "UPDATE pemilih SET status_memilih = 1 WHERE id = $id_pemilih");
    $_SESSION['status_memilih'] = 1;
    header('Location: dashboard.php');
    exit;
}

// ── Simpan suara ke database ──
$waktu = date('Y-m-d H:i:s');
$insert_suara = mysqli_query($koneksi,
    "INSERT INTO suara (id_pemilih, id_kandidat, waktu) VALUES ($id_pemilih, $id_kandidat, '$waktu')"
);

if ($insert_suara) {
    // Update status pemilih
    mysqli_query($koneksi, "UPDATE pemilih SET status_memilih = 1 WHERE id = $id_pemilih");
    $_SESSION['status_memilih'] = 1;
    $_SESSION['voted_for'] = htmlspecialchars($cek_kandidat['nama']);
    header('Location: sukses.php');
    exit;
} else {
    // Gagal simpan — redirect dengan pesan error
    header('Location: kandidat.php?error=1');
    exit;
}
?>
