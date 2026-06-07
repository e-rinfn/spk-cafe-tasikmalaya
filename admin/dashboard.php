<?php
require_once '../config/database.php';

// Hitung statistik
$total_kafe = fetch_one(query("SELECT COUNT(*) as total FROM kafe WHERE status='aktif'"))['total'];
$total_kriteria = fetch_one(query("SELECT COUNT(*) as total FROM kriteria WHERE is_active=1"))['total'];
$total_sesi = fetch_one(query("SELECT COUNT(*) as total FROM bobot_sesi"))['total'];
$total_user = fetch_one(query("SELECT COUNT(*) as total FROM user WHERE role='user'"))['total'];

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
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item header-user-profile">
                        <div>
                            <span class="badge bg-primary"><?= ucfirst($_SESSION['role']) ?></span>
                            <span class="ms-2"><?= $_SESSION['nama_lengkap'] ?></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Kafe</h6>
                            <h4 class="mb-3"><?= $total_kafe ?><span class="badge bg-light-primary border border-primary"></h4>
                            <p class="mb-0 text-muted text-sm">Kafe Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Kriteria</h6>
                            <h4 class="mb-3"><?= $total_kriteria ?><span class="badge bg-light-primary border border-primary"></h4>
                            <p class="mb-0 text-muted text-sm">Kriteria Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Sesi Perhitungan</h6>
                            <h4 class="mb-3"><?= $total_sesi ?><span class="badge bg-light-primary border border-primary"></h4>
                            <p class="mb-0 text-muted text-sm">Total Sesi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Pengguna</h6>
                            <h4 class="mb-3"><?= $total_user ?><span class="badge bg-light-primary border border-primary"></h4>
                            <p class="mb-0 text-muted text-sm">Terdaftar</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content Start -->

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Sesi Perhitungan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Nama Sesi</th>
                                        <th>Tanggal</th>
                                        <!-- <th>Dibuat Oleh</th> -->
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM bobot_sesi ORDER BY id_sesi DESC LIMIT 10";
                                    $result = query($sql);
                                    $no = 1;
                                    while ($row = fetch_one($result)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_sesi'] ?></td>
                                            <td><?= date('d-M-Y', strtotime($row['tanggal_perhitungan'])) ?></td>
                                            <!-- <td><?= $row['created_by'] ?></td> -->
                                            <td class="text-center">
                                                <a href="../admin/hasil_detail.php?id=<?= $row['id_sesi'] ?>" class="btn btn-sm btn-info">Lihat</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Main Content End -->

            </div>
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