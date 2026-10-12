<?php
session_start();
require_once __DIR__ . "/config.php";

error_reporting(E_ALL);
ini_set("display_errors", "1");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Email dan password wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, name, email, password, role
                 FROM t_user
                 WHERE email = ?
                 LIMIT 1"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["id_user"] = $user["id"];
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = strtolower($user["role"]);

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Email atau password salah.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Terjadi kesalahan saat memeriksa akun.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login UKK-2026</title>
</head>
<body>
<h2>LOGIN UKK-2026</h2>
<p>Silakan login menggunakan akun yang terdaftar.</p>

<?php if ($error !== ""): ?>
    <p><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
    <p>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required autocomplete="username"
               value="<?php echo htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8"); ?>">
    </p>
    <p>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required autocomplete="current-password">
    </p>
    <button type="submit">Login</button>
</form>
</body>
</html>