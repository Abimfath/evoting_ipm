<?php
$host = "tramway.proxy.rlwy.net";
$user = "root";
$pass = "DRuuxLLndrUguEfzDAMScNtXhbKRvBpf";
$db   = "railway";
$port = 36242;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>