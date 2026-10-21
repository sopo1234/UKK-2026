<?php
session_start();
include 'config/koneksi.php';

if (!isset($_POST['email'], $_POST['password'])) {
    header("Location: login.php");
    exit;
}

$input = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

// Cek apakah input cocok dengan kolom email ATAU name (username) di tabel t_user
$query = mysqli_query($koneksi, "SELECT * FROM t_user WHERE email = '$input' OR name = '$input'");

if (!$query) {
    die("Query login gagal: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($query) === 1) {
    $data = mysqli_fetch_assoc($query);

    if (password_verify($password, $data['password'])) {
        session_regenerate_id(true);
        $_SESSION['login']    = true;
        $_SESSION['id_user']  = (int)$data['id'];
        $_SESSION['name']     = $data['name'];
        $_SESSION['email']    = $data['email'];
        $_SESSION['role']     = $data['role'];

        session_write_close();
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['pesan_error'] = 'Password salah!';
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['pesan_error'] = 'Email atau Username tidak ditemukan!';
    header("Location: login.php");
    exit;
}
?>