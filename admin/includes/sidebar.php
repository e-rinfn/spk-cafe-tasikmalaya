<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="dashboard.php" class="b-brand text-primary text-decoration-none">
                <span class="fs-4 fw-bold">SPK OCRA</span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="dashboard.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Menu</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item <?= ($current_page == 'data_kafe.php') ? 'active' : ''; ?>">
                    <a href="kafe.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-building-store"></i></span>
                        <span class="pc-mtext">Data Kafe</span>
                    </a>
                </li>
                <li class="pc-item <?= ($current_page == 'kriteria.php') ? 'active' : ''; ?>">
                    <a href="kriteria.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-list"></i></span>
                        <span class="pc-mtext">Kriteria</span>
                    </a>
                </li>
                <li class="pc-item <?= ($current_page == 'nilai.php') ? 'active' : ''; ?>">
                    <a href="nilai.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-pencil"></i></span>
                        <span class="pc-mtext">Input Nilai</span>
                    </a>
                </li>
                <li class="pc-item <?= ($current_page == 'sesi_lama.php') ? 'active' : ''; ?>">
                    <a href="sesi_lama.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-history"></i></span>
                        <span class="pc-mtext">Riwayat Pengujian</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Pages</label>
                    <i class="ti ti-news"></i>
                </li>
                <li class="pc-item">
                    <a href="../logout.php" class="pc-link text-danger">
                        <span class="pc-micon"><i class="ti ti-logout"></i></span>
                        <span class="pc-mtext ">Logout</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>