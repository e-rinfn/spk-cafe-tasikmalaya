<?php
require_once '../config/database.php';

$id_sesi = (int)$_GET['id'];

// Ambil informasi sesi
$sesi = fetch_one(query("SELECT * FROM bobot_sesi WHERE id_sesi = $id_sesi"));

if (!$sesi) {
    echo "<div class='alert alert-danger'>Sesi tidak ditemukan!</div>";
    require_once '../includes/footer.php';
    exit();
}

// Ambil hasil ranking
$hasil = query("SELECT h.*, k.nama_kafe, k.alamat 
                FROM hasil_ocra h 
                JOIN kafe k ON h.id_kafe = k.id_kafe 
                WHERE h.id_sesi = $id_sesi 
                ORDER BY h.peringkat ASC");

// Ambil bobot yang digunakan
$bobot = query("SELECT d.*, kr.nama_kriteria, kr.kode_kriteria 
                FROM detail_bobot_sesi d 
                JOIN kriteria kr ON d.id_kriteria = kr.id_kriteria 
                WHERE d.id_sesi = $id_sesi");

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

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            <?= htmlspecialchars($sesi['nama_sesi']) ?>
                        </h5>

                        <a href="dashboard.php" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Tanggal Perhitungan</label>
                                <div class="fw-semibold">
                                    <i class="fas fa-calendar-alt text-success me-2"></i>
                                    <?= date('d-m-Y H:i', strtotime($sesi['tanggal_perhitungan'])) ?>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Dibuat Oleh</label>
                                <div class="fw-semibold">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <?= htmlspecialchars($sesi['created_by']) ?>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Keterangan</label>
                                <div class="fw-semibold">
                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                    <?= htmlspecialchars($sesi['keterangan'] ?? '-') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-sliders-h me-2"></i>
                            Bobot Kriteria yang Digunakan
                        </h5>
                    </div>

                    <div class="card-body">
                        <?php if (mysqli_num_rows($bobot) > 0): ?>
                            <div class="row">
                                <?php while ($row = fetch_one($bobot)): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-bold text-primary">
                                                        <?= $row['kode_kriteria'] ?>
                                                    </h6>

                                                    <span class="badge bg-success">
                                                        <?= $row['bobot'] ?>%
                                                    </span>
                                                </div>

                                                <div class="progress" style="height:10px;">
                                                    <div class="progress-bar bg-info"
                                                        role="progressbar"
                                                        style="width: <?= $row['bobot'] ?>%;"
                                                        aria-valuenow="<?= $row['bobot'] ?>"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="mt-2 text-muted small">
                                                    Tingkat kepentingan:
                                                    <strong><?= $row['bobot'] ?>%</strong>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Belum ada bobot yang ditentukan untuk sesi ini.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                Hasil Perankingan Kafe Terbaik
                            </h5>

                            <span class="badge bg-light text-primary fs-6">
                                <?= mysqli_num_rows($hasil) ?> Kafe
                            </span>
                        </div>
                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($hasil) > 0): ?>

                            <?php
                            mysqli_data_seek($hasil, 0);
                            $top = fetch_one($hasil);
                            ?>

                            <!-- TOP 1 -->
                            <div class="card border-warning bg-warning bg-opacity-10 mb-4">
                                <div class="card-body text-center">

                                    <div class="mb-3">
                                        <i class="fas fa-crown text-warning fa-4x"></i>
                                    </div>

                                    <h4 class="fw-bold text-warning">
                                        🏆 Kafe Terbaik
                                    </h4>

                                    <h3 class="fw-bold">
                                        <?= htmlspecialchars($top['nama_kafe']) ?>
                                    </h3>

                                    <p class="text-muted mb-2">
                                        <?= htmlspecialchars($top['alamat']) ?>
                                    </p>

                                    <span class="badge bg-success fs-6">
                                        Skor: <?= number_format($top['skor_normal'], 4) ?>
                                    </span>

                                </div>
                            </div>

                            <!-- TABEL HASIL -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="100">Ranking</th>
                                            <th class="text-center">Nama Kafe</th>
                                            <th class="text-center">Alamat</th>
                                            <th class="text-center">Skor Normal</th>
                                            <th class="text-center">Skor Mentah</th>
                                            <th class="text-center" width="120">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php
                                        mysqli_data_seek($hasil, 0);

                                        while ($row = fetch_one($hasil)):
                                        ?>

                                            <tr>

                                                <td>
                                                    <?php if ($row['peringkat'] == 1): ?>
                                                        <span class="badge bg-warning text-dark fs-6">
                                                            🥇 #1
                                                        </span>

                                                    <?php elseif ($row['peringkat'] == 2): ?>
                                                        <span class="badge bg-secondary fs-6">
                                                            🥈 #2
                                                        </span>

                                                    <?php elseif ($row['peringkat'] == 3): ?>
                                                        <span class="badge bg-info fs-6">
                                                            🥉 #3
                                                        </span>

                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark">
                                                            #<?= $row['peringkat'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <strong>
                                                        <?= htmlspecialchars($row['nama_kafe']) ?>
                                                    </strong>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['alamat']) ?>
                                                </td>

                                                <td>
                                                    <span class="badge bg-success">
                                                        <?= number_format($row['skor_normal'], 4) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?= number_format($row['skor_mentah'], 4) ?>
                                                </td>

                                                <td>
                                                    <a href="kafe_detail.php?id=<?= $row['id_kafe'] ?>"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>

                            <div class="text-center py-5">

                                <i class="fas fa-calculator fa-4x text-secondary mb-3"></i>

                                <h4>Belum Ada Hasil Perhitungan</h4>

                                <p class="text-muted">
                                    Sesi ini belum memiliki hasil perhitungan OCRA.
                                </p>

                                <div class="alert alert-warning text-start mt-4">
                                    <strong>Langkah yang perlu dilakukan:</strong>
                                    <ol class="mb-0 mt-2">
                                        <li>Pastikan data kafe sudah tersedia.</li>
                                        <li>Pastikan nilai setiap kriteria telah diinput.</li>
                                        <li>Lakukan proses perhitungan OCRA.</li>
                                    </ol>
                                </div>

                                <a href="../proses/hitung_ulang.php?id_sesi=<?= $id_sesi ?>"
                                    class="btn btn-primary mt-3">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Hitung Ulang OCRA
                                </a>

                            </div>

                        <?php endif; ?>

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