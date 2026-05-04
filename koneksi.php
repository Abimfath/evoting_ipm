<?php

$host = "tramway.proxy.rlwy.net";
$port = 24631;
$user = "root";
$password = "dNqBcWTWaeiyBjTilPfgYQDWDMVffarj";
$database = "railway";

// koneksi
$conn = new mysqli($host, $user, $password, $database, $port);

// cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "Koneksi berhasil ke Railway MySQL!";

?>