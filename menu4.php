<?php
session_start();
if (!isset($_SESSION['role'])) { header("Location: login.php"); exit; }
if ($_SESSION['role'] != "admin" && $_SESSION['role'] != "guru") {
    echo "<h1>Akses Ditolak!</h1>"; exit;
}
?>
<!DOCTYPE html><html><head><title>Menu 4</title></head><body>
<h1>Menu 4</h1><p>Menu 4 dapat diakses oleh Admin dan Guru.</p>
<a href="dashboard.php">Kembali ke Dashboard</a>
</body></html>