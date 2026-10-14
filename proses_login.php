```php
<?php
session_start();

require_once __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    exit("Email dan password wajib diisi.");
}

$stmt = $conn->prepare(
    "SELECT id, name, email, password, role
     FROM t_user
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    exit("Gagal menyiapkan query login.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {
    exit("Email tidak ditemukan.");
}

/*
 * Mendukung password yang disimpan menggunakan
 * password_hash() maupun password teks biasa.
 */
$passwordBenar = password_verify($password, $user["password"])
    || hash_equals((string) $user["password"], $password);

if (!$passwordBenar) {
    exit("Password salah.");
}

session_regenerate_id(true);

$_SESSION["id_user"] = $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = strtolower(trim($user["role"]));

header("Location: dashboard.php");
exit;
?>
```