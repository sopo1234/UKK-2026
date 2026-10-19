```php
<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = strtolower((string)($_SESSION['role'] ?? ''));

if (!defined('BASE_URL')) {
    define('BASE_URL', '/UKK-2026/UKK-2026');
}

$menuAdmin = [
    'dashboard.php' => 'Dashboard',
    'kelola_siswa.php' => 'Data Siswa',
    'kelola_guru.php' => 'Data Guru',
    'kelola_kelas.php' => 'Data Kelas',
    'kelola_tahun_ajaran.php' => 'Data Tahun Ajaran',
    'kelola_walikelas.php' => 'Data Wali Kelas',
    'penempatan_siswa.php' => 'Penempatan Siswa',
    'kelola_pelanggaran_kategori.php' => 'Kategori Pelanggaran',
    'kelola_pelanggaran.php' => 'Data Pelanggaran',
    'about_me.php' => 'About Me'
];

$menuGuru = [
    'dashboard.php' => 'Dashboard',
    'catatan_pelanggaran.php' => 'Catat Pelanggaran',
    'tindakan.php' => 'Tindakan',
    'riwayat.php' => 'Riwayat',
    'rekap_poin.php' => 'Rekap Poin',
    'about_me.php' => 'About Me'
];

$menu = [];

if ($role === 'admin') {
    $menu = $menuAdmin;
} elseif ($role === 'guru') {
    $menu = $menuGuru;
}
?>

<style>
    /* Pengaturan dasar halaman */
    html,
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        font-family: Arial, sans-serif;
        background: #f5f7f9;
    }

    /* Sidebar selalu di sebelah kiri */
    .app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 284px;
        height: 100vh;
        padding: 20px 14px;
        overflow-y: auto;
        overflow-x: hidden;
        background: #343a40;
        z-index: 1000;
    }

    /* Judul sidebar */
    .app-sidebar h4 {
        color: #ffffff;
        font-size: 21px;
        font-weight: bold;
        margin: 0 0 24px;
        padding: 0;
    }

    /* Daftar menu */
    .app-sidebar nav {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .app-sidebar nav a {
        display: block;
        width: 100%;
        padding: 11px 14px;
        margin-bottom: 6px;
        color: #ffffff;
        font-size: 14px;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.15s ease;
    }

    /* Efek saat menu disentuh */
    .app-sidebar nav a:hover {
        background: #495057;
        color: #ffffff;
    }

    /* Menu halaman yang sedang dibuka */
    .app-sidebar nav a.active {
        background: #0d6efd;
        color: #ffffff;
    }

    /* Garis pemisah */
    .app-sidebar nav hr {
        width: 100%;
        border: 0;
        border-top: 1px solid #777777;
        margin: 15px 0;
    }

    /* Tombol logout */
    .app-sidebar nav a.logout {
        color: #ff5964;
        margin-top: 4px;
    }

    .app-sidebar nav a.logout:hover {
        background: #495057;
        color: #ff5964;
    }

    /* Konten utama berada di kanan sidebar */
    .app-main {
        display: block;
        width: auto;
        min-width: 0;
        min-height: 100vh;
        margin-left: 284px;
        padding: 30px;
    }

    .app-main h1,
    .app-main h2 {
        margin-top: 0;
    }

    .app-main .table-responsive {
        width: 100%;
    }

    /* Tampilan pada layar HP */
    @media (max-width: 767.98px) {
        .app-sidebar {
            position: relative;
            top: auto;
            left: auto;
            bottom: auto;
            width: 100%;
            max-width: 100%;
            height: auto;
            min-height: 0;
        }

        .app-main {
            width: 100%;
            margin-left: 0;
            padding: 18px 12px;
        }
    }
</style>

<aside class="app-sidebar">
    <h4>Pelanggaran Siswa</h4>

    <nav>
        <?php foreach ($menu as $file => $label): ?>
            <a
                href="<?= BASE_URL . '/' . $file ?>"
                class="<?= $current_page === $file ? 'active' : '' ?>"
                <?= $current_page === $file ? 'aria-current="page"' : '' ?>
            ><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>

        <hr>

        <a href="<?= BASE_URL ?>/logout.php" class="logout">
            Logout
        </a>
    </nav>
</aside>
```