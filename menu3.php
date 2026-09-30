<?php
session_start();
if (!isset($_SESSION['role'])) { header("Location: login.php"); exit; }
if ($_SESSION['role'] != "admin" && $_SESSION['role'] != "guru") {
    echo "<h1>Akses Ditolak!</h1>"; exit;
}
?>
<!DOCTYPE html><html><head><title>Menu 3</title></head><body>
<h1>Menu 3</h1><p>Menu 3 dapat diakses oleh Admin dan Guru.</p>
<a href="dashboard.php">Kembali ke Dashboard</a>
</body></html>