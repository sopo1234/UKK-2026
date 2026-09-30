<?php
session_start();
if (!isset($_SESSION['role'])) { header("Location: login.php"); exit; }
if ($_SESSION['role'] != "admin") {
    echo "<h1>Akses Ditolak!</h1><p>Menu 1 hanya dapat diakses oleh Admin.</p>";
    echo '<a href="dashboard.php">Kembali ke Dashboard</a>'; exit;
}
?>
<!DOCTYPE html><html><head><title>Menu 1</title></head><body>
<h1>Menu 1</h1><p>Halaman khusus Admin.</p>
<a href="dashboard.php">Kembali ke Dashboard</a>
</body></html>