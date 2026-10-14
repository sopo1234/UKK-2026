```php
<?php
$host = "localhost";
$port = "3306";
$dbname = "db_ukk_2026";
$username = "root";
$password = "";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $dbname,
    (int) $port
);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```