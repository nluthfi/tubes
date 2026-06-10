<?php
session_start();
include '../koneksi.php'; // sesuaikan path

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$stmt = $koneksi->prepare(
    "SELECT * FROM users WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {

    // kalau password disimpan biasa (bukan hash)
    if ($password === $user['password']) {

        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: ..\dashboard.php");
        exit;
    }
}

echo "
<script>
alert('Username atau Password salah!');
window.location='login.php';
</script>
";