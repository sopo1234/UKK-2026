<?php
session_start();
include 'config/koneksi.php';

// Proses ketika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $name     = mysqli_real_escape_string($koneksi, $_POST['name']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Query INSERT sesuai struktur kolom tabel t_user
    $query = "INSERT INTO t_user (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: kelola_guru.php"); // Atau arahkan ke halaman manajemen user yang Anda inginkan
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h2 {
            margin-bottom: 5px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin-bottom: 20px;
        }
        table td {
            padding: 8px 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            padding: 6px 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 7px 15px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

    <h2>Tambah User Baru</h2>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>
    <hr>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <table>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><input type="text" name="name" required></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td>Role</td>
                <td>:</td>
                <td>
                    <select name="role" required>
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><button type="submit" name="simpan">Simpan User</button></td>
            </tr>
        </table>
    </form>

</body>
</html>