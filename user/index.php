<?php
require_once '../config/database.php';

$total_kafe = fetch_one(query("SELECT COUNT(*) as total FROM kafe WHERE status='aktif'"))['total'];
$total_sesi = fetch_one(query("SELECT COUNT(*) as total FROM bobot_sesi"))['total'];

?>

<!-- [Head] start -->
<?php require_once 'includes/header.php'; ?>
<!-- [Head] end -->

<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ Sidebar Menu ] start -->
    <?php require_once 'includes/sidebar.php'; ?>
    <!-- [ Sidebar Menu ] end -->


    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>

                </ul>
            </div>
            <!-- [Mobile Media Block end] -->

        </div>
    </header>
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-plus-circle" style="font-size: 48px;"></i>
                            <h3>Pengujian Baru</h3>
                            <p>Buat perhitungan dengan bobot kustom</p>
                            <a href="sesi_baru.php" class="btn btn-primary">Mulai</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-clock-history" style="font-size: 48px;"></i>
                            <h3>Riwayat Pengujian</h3>
                            <p>Lihat hasil pengujian sebelumnya</p>
                            <a href="sesi_lama.php" class="btn btn-info">Lihat</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Statistik</h5>
                </div>
                <div class="card-body">
                    <p>Total Kafe Terdaftar: <strong><?= $total_kafe ?></strong></p>
                    <p>Total Sesi Perhitungan: <strong><?= $total_sesi ?></strong></p>
                    <p>Metode: <strong>OCRA (Operational Competitiveness Rating Analysis)</strong></p>
                </div>
            </div>

            <!-- [ Main Content ] end -->
        </div>
    </div>

    <!-- [Page Specific JS] start -->
    <script src="../assets/js/plugins/apexcharts.min.js"></script>
    <script src="../assets/js/pages/dashboard-default.js"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/pcoded.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>





    <script>
        layout_change('light');
    </script>




    <script>
        change_box_container('false');
    </script>



    <script>
        layout_rtl_change('false');
    </script>


    <script>
        preset_change("preset-1");
    </script>


    <script>
        font_change("Public-Sans");
    </script>



</body>
<!-- [Body] end -->

</html>