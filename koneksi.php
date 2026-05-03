<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'evoting_ipm');

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$koneksi) {
    die('<div style="font-family:Arial;padding:30px;background:#fff1f0;border:2px solid #ff4d4f;border-radius:8px;max-width:500px;margin:50px auto;"><h3 style="color:#cf1322;">Koneksi Database Gagal</h3><p>Error: ' . mysqli_connect_error() . '</p></div>');
}

mysqli_set_charset($koneksi, 'utf8mb4');

function sanitize($koneksi, $data) {
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data)));
}
?>
