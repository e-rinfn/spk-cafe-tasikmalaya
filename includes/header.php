<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Kafe Tasikmalaya - OCRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            padding-left: 25px;
        }

        .sidebar .active {
            background: rgba(255, 255, 255, 0.3);
            border-left: 4px solid white;
        }

        .content {
            padding: 20px;
        }

        .card-stats {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="text-center py-4">
                    <h4 class="text-white">SPK Kafe OCRA</h4>
                    <small class="text-white-50">Kota Tasikmalaya</small>
                </div>
                <nav class="nav flex-column">
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="../admin/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="../admin/kafe.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'kafe') !== false ? 'active' : '' ?>">
                            <i class="bi bi-cup-hot"></i> Data Kafe
                        </a>
                        <a href="../admin/kriteria.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kriteria.php' ? 'active' : '' ?>">
                            <i class="bi bi-list-check"></i> Kriteria
                        </a>
                        <a href="../admin/nilai.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'nilai.php' ? 'active' : '' ?>">
                            <i class="bi bi-pencil-square"></i> Input Nilai
                        </a>
                    <?php else: ?>
                        <a href="../user/index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                            <i class="bi bi-house"></i> Beranda
                        </a>
                        <a href="../user/sesi_baru.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sesi_baru.php' ? 'active' : '' ?>">
                            <i class="bi bi-plus-circle"></i> Sesi Baru
                        </a>
                        <a href="../user/sesi_lama.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sesi_lama.php' ? 'active' : '' ?>">
                            <i class="bi bi-clock-history"></i> Sesi Lama
                        </a>
                    <?php endif; ?>
                    <a href="../logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?= $page_title ?? 'SPK Pemilihan Kafe Terbaik' ?></h2>
                    <div>
                        <span class="badge bg-primary"><?= ucfirst($_SESSION['role']) ?></span>
                        <span class="ms-2"><?= $_SESSION['nama_lengkap'] ?></span>
                    </div>
                </div>