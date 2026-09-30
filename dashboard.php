<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
<h1>Dashboard</h1>
<p>Selamat datang, <b><?php echo $_SESSION['username']; ?></b></p>
<p>Role: <b><?php echo $_SESSION['role']; ?></b></p>
<hr>
<?php if ($_SESSION['role'] == "admin") { ?>
<h3>Menu Admin</h3>
<ul>
<li><a href="menu1.php">Menu 1</a></li>
<li><a href="menu2.php">Menu 2</a></li>
<li><a href="menu3.php">Menu 3</a></li>
<li><a href="menu4.php">Menu 4</a></li>
</ul>
<?php } else { ?>
<h3>Menu Guru</h3>
<ul>
<li><a href="menu3.php">Menu 3</a></li>
<li><a href="menu4.php">Menu 4</a></li>
</ul>
<?php } ?>
<hr>
<a href="logout.php">Logout</a>
</body>
</html>