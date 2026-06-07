<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="index.php" class="b-brand text-primary text-decoration-none">
                <span class="fs-4 fw-bold">SPK OCRA</span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item <?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <a href="index.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-cup"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Menu</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item <?= ($current_page == 'kafe.php') ? 'active' : ''; ?>">
                    <a href="kafe.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-building-store"></i></span>
                        <span class="pc-mtext">Daftar Kafe</span>
                    </a>
                </li>
                <li class="pc-item <?= ($current_page == 'sesi_baru.php') ? 'active' : ''; ?>">
                    <a href="sesi_baru.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calculator"></i></span>
                        <span class="pc-mtext">Pengujian</span>
                    </a>
                </li>
                <li class="pc-item <?= ($current_page == 'sesi_lama.php') ? 'active' : ''; ?>">
                    <a href="sesi_lama.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-list"></i></span>
                        <span class="pc-mtext">Riwayat Pengujian</span>
                    </a>
                </li>



            </ul>

        </div>
    </div>
</nav>