<?php
// about_me.php
// Cek login langsung di sini, tanpa file includes/cek_session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Me - Raditya Jeti Prasetya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Font judul. Jika tidak ada internet, otomatis memakai font bawaan sistem -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #1d2142;
            --coin: #ffc233;
            --paper: #f1f4f8;
            --line: #d9dfeb;
            --muted: #5b6280;
            --display: 'Bricolage Grotesque', 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: var(--paper);
            color: var(--ink);
        }

        /* Sidebar tetap di kiri dan memenuhi tinggi layar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        /* Konten bergeser agar tidak tertutup sidebar */
        main {
            margin-left: 16.666667%;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            main {
                margin-left: 0;
            }
        }

        .profil {
            max-width: 980px;
            margin: 0 auto;
        }

        /* ===== Bagian atas: nama dan monogram ===== */
        .profil-hero {
            display: flex;
            align-items: center;
            gap: clamp(1.5rem, 4vw, 3rem);
            flex-wrap: wrap;
            background: var(--ink);
            color: #fff;
            border-radius: 28px;
            padding: clamp(1.75rem, 4vw, 3.25rem);
        }

        .monogram {
            flex: none;
            width: clamp(110px, 16vw, 150px);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            background: var(--coin);
            color: var(--ink);
            border-radius: 32%;
            transform: rotate(-5deg);
            font-family: var(--display);
            font-weight: 800;
            font-size: clamp(2.2rem, 5vw, 3.2rem);
            letter-spacing: -0.04em;
            line-height: 1;
            user-select: none;
        }

        .hero-teks {
            flex: 1 1 320px;
            min-width: 0;
        }

        .hero-status {
            margin: 0 0 .35rem;
            font-weight: 600;
            color: var(--coin);
        }

        .nama {
            margin: 0 0 .85rem;
            font-family: var(--display);
            font-weight: 800;
            font-size: clamp(2.2rem, 5.2vw, 3.7rem);
            line-height: 1.02;
            letter-spacing: -0.025em;
            overflow-wrap: anywhere;
        }

        .tagline {
            max-width: 46ch;
            margin: 0 0 1.25rem;
            color: rgba(255, 255, 255, .8);
            line-height: 1.6;
        }

        .chip-terang {
            display: inline-block;
            margin: 0 .4rem .4rem 0;
            padding: .35rem .85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            font-size: .9rem;
        }

        /* ===== Bagian bawah: data diri dan proyek ===== */
        .judul-bagian {
            margin: 0 0 1rem;
            font-family: var(--display);
            font-weight: 700;
            font-size: 1.35rem;
            letter-spacing: -0.01em;
        }

        .data-diri {
            margin: 0;
            border-top: 3px solid var(--ink);
        }

        .data-diri .baris {
            display: grid;
            grid-template-columns: 6.5rem 1fr;
            gap: 1rem;
            align-items: center;
            padding: .95rem 0;
            border-bottom: 1px solid var(--line);
        }

        .data-diri dt {
            margin: 0;
            font-weight: 600;
            color: var(--muted);
        }

        .data-diri dd {
            margin: 0;
            font-weight: 600;
        }

        .data-diri .ikon {
            width: 20px;
            height: 20px;
            margin-right: .5rem;
            vertical-align: -4px;
        }

        .proyek {
            height: 100%;
            background: #fff;
            border-radius: 20px;
            border-left: 8px solid var(--coin);
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 2px rgba(29, 33, 66, .08);
        }

        .proyek h3 {
            margin: 0 0 .6rem;
            font-family: var(--display);
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.015em;
        }

        .proyek p {
            margin: 0 0 1.1rem;
            color: var(--muted);
            line-height: 1.65;
        }

        .chip-gelap {
            display: inline-block;
            margin: 0 .4rem .4rem 0;
            padding: .3rem .8rem;
            border-radius: 8px;
            border: 1.5px solid var(--ink);
            font-size: .88rem;
            font-weight: 600;
        }

        @media (max-width: 575.98px) {
            .data-diri .baris {
                grid-template-columns: 5rem 1fr;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <?php
        // Menu sidebar sesuai role, tanpa file layout/sidebar.php
        if (($_SESSION['role'] ?? '') === 'guru') {
            $menu = [
                'dashboard.php'           => 'Dashboard',
                'catatan_pelanggaran.php' => 'Catat Pelanggaran',
                'about_me.php'            => 'About Me',
            ];
        } else {
            $menu = [
                'dashboard.php'                  => 'Dashboard',
                'kelola_siswa.php'               => 'Data Siswa',
                'kelola_guru.php'                => 'Data Guru',
                'kelola_kelas.php'               => 'Kelola Kelas',
                'kelola_tahun_ajaran.php'        => 'Kelola Tahun Ajaran',
                'kelola_walikelas.php'           => 'Kelola Walikelas',
                'penempatan_siswa.php'           => 'Penempatan Siswa',
                'kelola_pelanggaran_kategori.php' => 'Pelanggaran Kategori',
                'about_me.php'                   => 'About Me',
            ];
        }

        $halaman_aktif = basename($_SERVER['PHP_SELF']);
        ?>

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="profil">

                <!-- Bagian atas -->
                <section class="profil-hero">
                    <div class="monogram" aria-hidden="true">RJP</div>

                    <div class="hero-teks">
                        <p class="hero-status">Pelajar SMK</p>
                        <h1 class="nama">Raditya Jeti Prasetya</h1>
                        <p class="tagline">
                            Seorang pelajar yang sedang belajar dan
                            mengembangkan kemampuan di bidang teknologi.
                        </p>
                        <span class="chip-terang">SMK Muhammadiyah Kota Tasikmalaya</span>
                        <span class="chip-terang">17 tahun</span>
                    </div>
                </section>

                <!-- Bagian bawah -->
                <div class="row g-4 mt-1">

                    <!-- Data diri -->
                    <section class="col-lg-5">
                        <h2 class="judul-bagian">Data diri</h2>

                        <dl class="data-diri">
                            <div class="baris">
                                <dt>Nama</dt>
                                <dd>Raditya Jeti Prasetya</dd>
                            </div>
                            <div class="baris">
                                <dt>Status</dt>
                                <dd>Pelajar SMK</dd>
                            </div>
                            <div class="baris">
                                <dt>Sekolah</dt>
                                <dd>SMK Muhammadiyah Kota Tasikmalaya</dd>
                            </div>
                            <div class="baris">
                                <dt>Umur</dt>
                                <dd>17 tahun</dd>
                            </div>
                            <div class="baris">
                                <dt>Hobi</dt>
                                <dd>
                                    <svg class="ikon" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round"
                                         aria-hidden="true">
                                        <line x1="6" y1="12" x2="10" y2="12"></line>
                                        <line x1="8" y1="10" x2="8" y2="14"></line>
                                        <line x1="15" y1="13" x2="15.01" y2="13"></line>
                                        <line x1="18" y1="11" x2="18.01" y2="11"></line>
                                        <rect x="2" y="6" width="20" height="12" rx="6"></rect>
                                    </svg>Main game, Main musik, gitar, drumer, dan keyboard 
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Proyek -->
                    <section class="col-lg-7">
                        <h2 class="judul-bagian">Proyek saat ini</h2>

                        <div class="proyek">
                            <h3>Sistem Pelanggaran Siswa</h3>
                            <p>
                                Aplikasi web untuk mencatat pelanggaran siswa.
                                Admin mengelola data siswa, guru, kelas, tahun
                                ajaran, dan wali kelas. Guru mencatat serta
                                memantau pelanggaran siswa. Dibuat sebagai
                                proyek UKK 2026.
                            </p>

                            <span class="chip-gelap">PHP</span>
                            <span class="chip-gelap">MySQL</span>
                            <span class="chip-gelap">Bootstrap 5</span>
                        </div>
                    </section>

                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>