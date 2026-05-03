<?php
// ============================================================
// LOGOUT — logout.php
// ============================================================
session_start();
session_unset();
session_destroy();

// Hapus cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header('Location: index.php');
exit;
?>
