<?php
session_start();

if (isset($_SESSION['role'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "admin" && $password == "12345") {
        $_SESSION["username"] = "admin";
        $_SESSION["role"] = "admin";
        header("Location: dashboard.php");
        exit;
    } elseif ($username == "guru" && $password == "12345") {
        $_SESSION["username"] = "guru";
        $_SESSION["role"] = "guru";
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<center>
<h1>LOGIN</h1>
<?php if ($error != "") echo "<p>$error</p>"; ?>
<form method="POST">
<table>
<tr><td>Username</td><td>:</td><td><input type="text" name="username" required></td></tr>
<tr><td>Password</td><td>:</td><td><input type="password" name="password" required></td></tr>
<tr><td colspan="3" align="center"><br><input type="submit" value="Login"></td></tr>
</table>
</form>
<p>Admin: admin / 12345</p>
<p>Guru: guru / 12345</p>
</center>
</body>
</html>