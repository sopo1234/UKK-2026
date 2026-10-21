<?php
session_start();
include 'config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pelanggaran Siswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 263px;
            padding: 15px 25px 34px;
            background-color: #6bb9cc;
            border-radius: 7px;
        }

        .logo {
            text-align: center;
            font-size: 55px;
            line-height: 65px;
            margin-bottom: 10px;
        }

        label {
            display: block;
            font-size: 9px;
            margin: 0 0 8px 2px;
            color: #000;
        }

        input {
            width: 100%;
            height: 38px;
            border: none;
            border-radius: 8px;
            padding: 0 14px;
            margin-bottom: 15px;
            background-color: white;
            outline: none;
            font-size: 12px;
        }

        button {
            width: 100%;
            height: 38px;
            margin-top: 9px;
            border: none;
            border-radius: 8px;
            background-color: #164e6b;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #103d55;
        }

        .pesan-error {
            color: #8b0000;
            background-color: white;
            padding: 8px;
            border-radius: 5px;
            font-size: 12px;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <div class="logo">⚖</div>

        <?php
        if (isset($_SESSION['pesan_error'])) {
            echo '<p class="pesan-error">' .
                htmlspecialchars($_SESSION['pesan_error'], ENT_QUOTES, 'UTF-8') .
                '</p>';

            unset($_SESSION['pesan_error']);
        }
        ?>
<form action="proses_login.php" method="POST">

            <label for="email">username / email</label>
            <input
                type="text"
                name="email"
                id="email"
                placeholder="Masukkan email..."
                required
                autocomplete="username"
            >

            <label for="password">password</label>
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Masukkan password..."
                required
                autocomplete="current-password"
            >

            <!-- Cukup sisakan SATU tombol login di sini -->
            <button type="submit">LOGIN</button>


    </div>

</body>
</html>
