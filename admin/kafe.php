<?php
require_once '../config/database.php';

// Handle hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    query("DELETE FROM kafe WHERE id_kafe = $id");
    echo "<script>alert('Kafe berhasil dihapus!'); window.location.href='kafe.php';</script>";
}

$kafe = query("SELECT * FROM kafe ORDER BY id_kafe DESC");

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


            <!-- Main Content Start -->

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Daftar Kafe</h5>
                        <a href="kafe_tambah.php" class="btn btn-primary btn-sm">+ Tambah Kafe</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nama Kafe</th>
                                    <th class="text-center">Alamat</th>
                                    <th class="text-center">Kecamatan</th>
                                    <th class="text-center">Jam Operasional</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = fetch_one($kafe)): ?>
                                    <tr>
                                        <td><?= $row['id_kafe'] ?></td>
                                        <td><?= $row['nama_kafe'] ?></td>
                                        <td><?= $row['alamat'] ?></td>
                                        <td><?= $row['kecamatan'] ?></td>
                                        <td><?= date('H:i', strtotime($row['jam_buka'])) . ' - ' . date('H:i', strtotime($row['jam_tutup'])) ?></td>
                                        <td class="text-center"><span class="badge bg-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?>"><?= $row['status'] ?></span></td>
                                        <td class="text-center ">
                                            <a href="kafe_edit.php?id=<?= $row['id_kafe'] ?>" class="btn btn-warning btn-sm m-1">Edit</a>
                                            <a href="kafe_detail.php?id=<?= $row['id_kafe'] ?>" class="btn btn-info btn-sm m-1">Detail</a>
                                            <a href="?hapus=<?= $row['id_kafe'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm m-1">Hapus</a>
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