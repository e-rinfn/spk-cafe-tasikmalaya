<?php
require_once '../config/database.php';

$id_kafe = (int)$_GET['id'];

// Ambil data kafe
$sql_kafe = "SELECT * FROM kafe WHERE id_kafe = $id_kafe";
$result_kafe = query($sql_kafe);
$kafe = fetch_one($result_kafe);

if (!$kafe) {
    $_SESSION['error'] = "Kafe tidak ditemukan!";
    header("Location: kafe.php");
    exit();
}

// Ambil nilai kriteria terbaru untuk kafe ini
$sql_nilai = "SELECT n.*, k.kode_kriteria, k.nama_kriteria, k.jenis_kriteria, k.satuan 
              FROM nilai_kafe n 
              JOIN kriteria k ON n.id_kriteria = k.id_kriteria 
              WHERE n.id_kafe = $id_kafe 
              ORDER BY k.id_kriteria";
$nilai_list = query($sql_nilai);

// Ambil riwayat penilaian per tahun
$sql_tahun = "SELECT DISTINCT tahun_penilaian FROM nilai_kafe WHERE id_kafe = $id_kafe ORDER BY tahun_penilaian DESC";
$tahun_list = query($sql_tahun);

// Ambil riwayat hasil OCRA untuk kafe ini
$sql_riwayat = "SELECT h.*, b.nama_sesi, b.tanggal_perhitungan 
                FROM hasil_ocra h 
                JOIN bobot_sesi b ON h.id_sesi = b.id_sesi 
                WHERE h.id_kafe = $id_kafe 
                ORDER BY b.tanggal_perhitungan DESC 
                LIMIT 5";
$riwayat_list = query($sql_riwayat);

// Hitung rata-rata rating dari semua sesi
$sql_avg = "SELECT AVG(h.skor_normal) as rata_rata 
            FROM hasil_ocra h 
            WHERE h.id_kafe = $id_kafe";
$avg_result = fetch_one(query($sql_avg));
$rata_rata = $avg_result['rata_rata'] ?? 0;

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
                <div class="row">
                    <div class="col-md-4">
                        <!-- Card Foto Kafe -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-camera"></i> Foto Kafe</h5>
                            </div>
                            <div class="card-body text-center">
                                <?php if ($kafe['foto'] && file_exists("../uploads/" . $kafe['foto'])): ?>
                                    <img src="../uploads/<?= $kafe['foto'] ?>" class="img-fluid rounded" alt="<?= $kafe['nama_kafe'] ?>" style="max-height: 300px; width: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded p-5">
                                        <i class="bi bi-image" style="font-size: 80px; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Belum ada foto</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Informasi Kontak -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-telephone"></i> Kontak & Lokasi</h5>
                            </div>
                            <div class="card-body">
                                <p><strong><i class="bi bi-telephone-fill"></i> Telepon:</strong><br>
                                    <?= $kafe['no_telepon'] ?: '<span class="text-muted">Tidak tersedia</span>' ?></p>
                                <p><strong><i class="bi bi-geo-alt-fill"></i> Alamat:</strong><br>
                                    <?= nl2br(htmlspecialchars($kafe['alamat'])) ?></p>
                                <p><strong><i class="bi bi-building"></i> Kecamatan:</strong><br>
                                    <?= $kafe['kecamatan'] ?: '-' ?></p>

                                <?php if ($kafe['latitude'] && $kafe['longitude']): ?>
                                    <a href="https://www.google.com/maps?q=<?= $kafe['latitude'] ?>,<?= $kafe['longitude'] ?>"
                                        target="_blank" class="btn btn-success btn-sm w-100 mt-2">
                                        <i class="bi bi-map"></i> Buka di Google Maps
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Statistik -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Statistik</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h3 class="text-primary"><?= number_format($rata_rata * 100, 2) ?>%</h3>
                                    <p class="text-muted">Rata-rata Skor OCRA</p>
                                </div>
                                <hr>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-<?= $kafe['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                        <?= strtoupper($kafe['status']) ?>
                                    </span>
                                </p>
                                <p><strong>Terdaftar:</strong> <?= date('d-M-Y', strtotime($kafe['created_at'])) ?></p>
                                <p><strong>Terakhir Update:</strong> <?= date('d-M-Y', strtotime($kafe['updated_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <!-- Card Informasi Umum -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white d-flex justify-content-between">
                                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Umum</h5>
                                <div>
                                    <a href="kafe_edit.php?id=<?= $id_kafe ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="kafe.php" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3><?= htmlspecialchars($kafe['nama_kafe']) ?></h3>
                                <p class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    <?= date('H:i', strtotime($kafe['jam_buka'])) ?> -
                                    <?= date('H:i', strtotime($kafe['jam_tutup'])) ?> WIB
                                </p>
                                <hr>
                                <h6>Deskripsi:</h6>
                                <p><?= nl2br(htmlspecialchars($kafe['deskripsi'] ?: 'Belum ada deskripsi')) ?></p>
                            </div>
                        </div>

                        <!-- Card Nilai Kriteria -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white d-flex justify-content-between">
                                <h5 class="mb-0"><i class="bi bi-table"></i> Nilai Kriteria</h5>
                                <a href="nilai.php?id_kafe=<?= $id_kafe ?>" class="btn btn-light btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit Nilai
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($nilai_list) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Kriteria</th>
                                                    <th>Jenis</th>
                                                    <th>Nilai</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($nilai = fetch_one($nilai_list)): ?>
                                                    <tr>
                                                        <td><?= $nilai['kode_kriteria'] ?></td>
                                                        <td><?= $nilai['nama_kriteria'] ?></td>
                                                        <td>
                                                            <span class="badge bg-<?= $nilai['jenis_kriteria'] == 'benefit' ? 'success' : 'danger' ?>">
                                                                <?= $nilai['jenis_kriteria'] ?>
                                                            </span>
                                                        </td>
                                                        <td><strong><?= number_format($nilai['nilai'], 2) ?></strong></td>
                                                        <td><?= $nilai['satuan'] ?: '-' ?></td>
                                                        <td><?= $nilai['tahun_penilaian'] ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        Belum ada data nilai untuk kafe ini.
                                        <a href="nilai.php?id_kafe=<?= $id_kafe ?>" class="alert-link">Input nilai sekarang</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Riwayat Penilaian per Tahun -->
                        <?php if (mysqli_num_rows($tahun_list) > 0): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="bi bi-calendar"></i> Riwayat Penilaian per Tahun</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php while ($tahun = fetch_one($tahun_list)):
                                            $sql_nilai_tahun = "SELECT n.*, k.kode_kriteria, k.nama_kriteria, k.satuan 
                                           FROM nilai_kafe n 
                                           JOIN kriteria k ON n.id_kriteria = k.id_kriteria 
                                           WHERE n.id_kafe = $id_kafe AND n.tahun_penilaian = {$tahun['tahun_penilaian']}";
                                            $nilai_tahun = query($sql_nilai_tahun);
                                        ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <strong>Tahun <?= $tahun['tahun_penilaian'] ?></strong>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <table class="table table-sm table-bordered">
                                                            <?php while ($row = fetch_one($nilai_tahun)): ?>
                                                                <tr>
                                                                    <td><?= $row['kode_kriteria'] ?></td>
                                                                    <td><?= number_format($row['nilai'], 2) ?> <?= $row['satuan'] ?></td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Card Riwayat Hasil OCRA -->
                        <?php if (mysqli_num_rows($riwayat_list) > 0): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Riwayat Hasil OCRA</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sesi</th>
                                                    <th>Tanggal</th>
                                                    <th>Skor Normal</th>
                                                    <th>Peringkat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($riwayat = fetch_one($riwayat_list)): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($riwayat['nama_sesi']) ?></td>
                                                        <td><?= date('d-m-Y H:i', strtotime($riwayat['tanggal_perhitungan'])) ?></td>
                                                        <td><?= number_format($riwayat['skor_normal'] * 100, 2) ?>%</td>
                                                        <td>
                                                            <?php
                                                            $badge_class = 'secondary';
                                                            if ($riwayat['peringkat'] == 1) $badge_class = 'warning';
                                                            elseif ($riwayat['peringkat'] == 2) $badge_class = 'info';
                                                            elseif ($riwayat['peringkat'] == 3) $badge_class = 'success';
                                                            ?>
                                                            <span class="badge bg-<?= $badge_class ?>">#<?= $riwayat['peringkat'] ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="hasil_detail.php?id=<?= $riwayat['id_sesi'] ?>" class="btn btn-sm btn-primary">
                                                                Lihat Sesi
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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