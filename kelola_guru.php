
<?php
session_start();
require_once "config.php";

// Hanya admin yang boleh mengakses
if (!isset($_SESSION["role"]) ||
    strtolower($_SESSION["role"]) !== "admin") {
    exit("Akses ditolak. Silakan login sebagai Admin.");
}

$pesan = "";

// Tambah guru
if (isset($_POST["tambah"])) {
    $nip = trim($_POST["nip"]);
    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $status = (int) $_POST["status_aktif"];
    $user_id = (int) $_POST["user_id"];

    try {
        $sql = "INSERT INTO t_guru
                (nip, nama, email, status_aktif, user_id,
                 created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nip, $nama, $email, $status, $user_id
        ]);

        header("Location: kelola_guru.php?pesan=tambah");
        exit;
    } catch (PDOException $e) {
        $pesan = "Gagal menambah guru. Periksa NIP, email, dan user ID.";
    }
}

// Hapus guru
if (isset($_GET["hapus"])) {
    $id = (int) $_GET["hapus"];

    try {
        $stmt = $pdo->prepare(
            "DELETE FROM t_guru WHERE id = ?"
        );
        $stmt->execute([$id]);

        header("Location: kelola_guru.php?pesan=hapus");
        exit;
    } catch (PDOException $e) {
        $pesan = "Data tidak dapat dihapus karena masih digunakan.";
    }
}

// Pesan berhasil
if (isset($_GET["pesan"])) {
    if ($_GET["pesan"] === "tambah") {
        $pesan = "Guru berhasil ditambahkan.";
    } elseif ($_GET["pesan"] === "hapus") {
        $pesan = "Guru berhasil dihapus.";
    } elseif ($_GET["pesan"] === "edit") {
        $pesan = "Data guru berhasil diperbarui.";
    }
}

// Simpan edit guru
if (isset($_POST["edit"])) {
    $id = (int) $_POST["id"];
    $nip = trim($_POST["nip"]);
    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $status = (int) $_POST["status_aktif"];
    $user_id = (int) $_POST["user_id"];

    try {
        $stmt = $pdo->prepare(
            "UPDATE t_guru
             SET nip=?, nama=?, email=?, status_aktif=?,
                 user_id=?, updated_at=NOW()
             WHERE id=?"
        );

        $stmt->execute([
            $nip, $nama, $email, $status, $user_id, $id
        ]);

        header("Location: kelola_guru.php?pesan=edit");
        exit;
    } catch (PDOException $e) {
        $pesan = "Gagal mengedit guru. Periksa data yang dimasukkan.";
    }
}

// Ambil data untuk form edit
$data_edit = null;

if (isset($_GET["edit"])) {
    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare(
        "SELECT * FROM t_guru WHERE id = ?"
    );
    $stmt->execute([$id]);
    $data_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil seluruh guru
$stmt = $pdo->query(
    "SELECT * FROM t_guru ORDER BY id DESC"
);
$guru = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Guru</title>
</head>
<body>

<h2>KELOLA DATA GURU</h2>

<p><?php echo h($pesan); ?></p>

<p>
    <a href="dashboard (1).php">Kembali ke Dashboard</a>
</p>

<hr>

<h3><?php echo $data_edit ? "Edit Guru" : "Tambah Guru"; ?></h3>

<form method="POST" action="kelola_guru.php">

    <?php if ($data_edit): ?>
        <input type="hidden" name="id"
               value="<?php echo h($data_edit["id"]); ?>">
    <?php endif; ?>

    <p>NIP:</p>
    <input type="text" name="nip" required
           value="<?php echo h($data_edit["nip"] ?? ""); ?>">

    <p>Nama Guru:</p>
    <input type="text" name="nama" required
           value="<?php echo h($data_edit["nama"] ?? ""); ?>">

    <p>Email:</p>
    <input type="email" name="email" required
           value="<?php echo h($data_edit["email"] ?? ""); ?>">

    <p>Status Aktif:</p>
    <select name="status_aktif">
        <option value="1"
            <?php echo isset($data_edit["status_aktif"]) &&
                $data_edit["status_aktif"] == 1
                ? "selected" : ""; ?>>
            Aktif
        </option>
        <option value="0"
            <?php echo isset($data_edit["status_aktif"]) &&
                $data_edit["status_aktif"] == 0
                ? "selected" : ""; ?>>
            Tidak Aktif
        </option>
    </select>

    <p>User ID:</p>
    <input type="number" name="user_id" min="1" required
           value="<?php echo h($data_edit["user_id"] ?? ""); ?>">

    <br><br>

    <?php if ($data_edit): ?>
        <button type="submit" name="edit">Simpan Perubahan</button>
        <a href="kelola_guru.php">Batal</a>
    <?php else: ?>
        <button type="submit" name="tambah">Tambah Guru</button>
    <?php endif; ?>

</form>

<hr>

<h3>Daftar Guru</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
        <th>User ID</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($guru as $g): ?>
    <tr>
        <td><?php echo h($g["id"]); ?></td>
        <td><?php echo h($g["nip"]); ?></td>
        <td><?php echo h($g["nama"]); ?></td>
        <td><?php echo h($g["email"]); ?></td>
        <td>
            <?php echo $g["status_aktif"] ? "Aktif" : "Tidak Aktif"; ?>
        </td>
        <td><?php echo h($g["user_id"]); ?></td>
        <td>
            <a href="kelola_guru.php?edit=<?php echo h($g["id"]); ?>">
                Edit
            </a>
            |
            <a href="kelola_guru.php?hapus=<?php echo h($g["id"]); ?>"
               onclick="return confirm('Yakin ingin menghapus guru ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>