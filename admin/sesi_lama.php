<?php
require_once '../config/database.php';

$sesi_list = query("SELECT * FROM bobot_sesi ORDER BY id_sesi DESC");

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
            <div class="card">
                <div class="card-header">
                    <h5>Riwayat Sesi Perhitungan OCRA</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($sesi_list) == 0): ?>
                        <div class="alert alert-info">Belum ada sesi perhitungan. Buat sesi baru terlebih dahulu.</div>
                    <?php else: ?>
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Sesi</th>
                                    <th class="text-center">Tanggal Perhitungan</th>
                                    <!-- <th class="text-center">Dibuat Oleh</th> -->
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($sesi = fetch_one($sesi_list)):
                                    // Hitung jumlah kafe yang dinilai dalam sesi ini
                                    $count = fetch_one(query("SELECT COUNT(*) as total FROM hasil_ocra WHERE id_sesi = {$sesi['id_sesi']}"))['total'];
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><strong><?= $sesi['nama_sesi'] ?></strong></td>
                                        <td><?= date('d-M-Y H:i', strtotime($sesi['tanggal_perhitungan'])) ?></td>
                                        <!-- <td><?= $sesi['created_by'] ?></td> -->
                                        <td>
                                            <?= $sesi['keterangan'] ?>
                                            <br><small class="text-muted"><?= $count ?> kafe dinilai</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="hasil_detail.php?id=<?= $sesi['id_sesi'] ?>" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                            <button onclick="if(confirm('Hapus sesi ini?')) window.location.href='../proses/hapus_sesi.php?id=<?= $sesi['id_sesi'] ?>'"
                                                class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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