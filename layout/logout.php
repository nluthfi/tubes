<?php
session_start();

// hapus semua data session
$_SESSION = [];

// hancurkan session
session_destroy();

// kembali ke halaman login
header("Location: /tubes/index.php");
exit;
?>