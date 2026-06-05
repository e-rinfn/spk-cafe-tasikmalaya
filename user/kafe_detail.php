<?php
require_once '../config/database.php';

$id_kafe = (int)$_GET['id'];

// Ambil data kafe
$sql_kafe = "SELECT * FROM kafe WHERE id_kafe = $id_kafe AND status = 'aktif'";
$result_kafe = query($sql_kafe);
$kafe = fetch_one($result_kafe);

if (!$kafe) {
    $_SESSION['error'] = "Kafe tidak ditemukan!";
    header("Location: index.php");
    exit();
}

// Ambil nilai kriteria terbaru untuk kafe ini
$sql_nilai = "SELECT n.*, k.kode_kriteria, k.nama_kriteria, k.jenis_kriteria, k.satuan 
              FROM nilai_kafe n 
              JOIN kriteria k ON n.id_kriteria = k.id_kriteria 
              WHERE n.id_kafe = $id_kafe 
              ORDER BY k.id_kriteria";
$nilai_list = query($sql_nilai);

// Ambil rata-rata skor dari semua sesi
$sql_avg = "SELECT AVG(h.skor_normal) as rata_rata, MAX(h.skor_normal) as terbaik, 
                   COUNT(h.id_hasil) as jumlah_penilaian
            FROM hasil_ocra h 
            WHERE h.id_kafe = $id_kafe";
$statistik = fetch_one(query($sql_avg));

// Ambil prestasi terbaik (peringkat 1 tertinggi)
$sql_best = "SELECT h.*, b.nama_sesi 
             FROM hasil_ocra h 
             JOIN bobot_sesi b ON h.id_sesi = b.id_sesi 
             WHERE h.id_kafe = $id_kafe AND h.peringkat = 1 
             ORDER BY h.skor_normal DESC 
             LIMIT 1";
$best_achievement = fetch_one(query($sql_best));

// Ambil 5 kafe terbaik berdasarkan rata-rata skor untuk perbandingan
$sql_top5 = "SELECT k.id_kafe, k.nama_kafe, k.foto, AVG(h.skor_normal) as avg_skor
             FROM hasil_ocra h 
             JOIN kafe k ON h.id_kafe = k.id_kafe 
             GROUP BY h.id_kafe 
             ORDER BY avg_skor DESC 
             LIMIT 5";
$top5_list = query($sql_top5);
$current_rank = 1;
$rank_position = null;
$top5_data = [];

while ($top = fetch_one($top5_list)) {
    if ($top['id_kafe'] == $id_kafe) {
        $rank_position = $current_rank;
    }
    $top5_data[] = $top;
    $current_rank++;
}

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
                <div class="col-md-4">
                    <!-- Card Foto Kafe -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-camera"></i> Galeri</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($kafe['foto'] && file_exists("../uploads/" . $kafe['foto'])): ?>
                                <img src="../uploads/<?= $kafe['foto'] ?>" class="img-fluid rounded" alt="<?= $kafe['nama_kafe'] ?>" style="max-height: 250px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded p-5">
                                    <i class="bi bi-cup-hot" style="font-size: 80px; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada foto</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Informasi Kontak -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light text-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Lokasi & Kontak</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-building"></i> <strong>Kecamatan:</strong><br>
                                <?= $kafe['kecamatan'] ?: '<span class="text-muted">-</span>' ?>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-pin-map-fill"></i> <strong>Alamat:</strong><br>
                                <?= nl2br(htmlspecialchars($kafe['alamat'])) ?>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-telephone-fill"></i> <strong>Telepon:</strong><br>
                                <?= $kafe['no_telepon'] ?: '<span class="text-muted">Tidak tersedia</span>' ?>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-clock-fill"></i> <strong>Jam Operasional:</strong><br>
                                <?= date('H:i', strtotime($kafe['jam_buka'])) ?> -
                                <?= date('H:i', strtotime($kafe['jam_tutup'])) ?> WIB
                            </div>

                            <?php if ($kafe['latitude'] && $kafe['longitude']): ?>
                                <a href="https://www.google.com/maps?q=<?= $kafe['latitude'] ?>,<?= $kafe['longitude'] ?>"
                                    target="_blank" class="btn btn-success btn-sm w-100" target="_blank">
                                    <i class="bi bi-map"></i> Buka Google Maps
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Statistik OCRA -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Statistik OCRA</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="text-primary"><?= number_format(($statistik['rata_rata'] ?? 0) * 100, 1) ?>%</h3>
                                    <small class="text-muted">Rata-rata Skor</small>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-success"><?= number_format(($statistik['terbaik'] ?? 0) * 100, 1) ?>%</h3>
                                    <small class="text-muted">Skor Terbaik</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <h4><?= $statistik['jumlah_penilaian'] ?? 0 ?></h4>
                                    <small class="text-muted">Jumlah Penilaian</small>
                                </div>
                                <div class="col-6">
                                    <?php if ($rank_position): ?>
                                        <h4 class="text-warning">#<?= $rank_position ?></h4>
                                    <?php else: ?>
                                        <h4 class="text-muted">-</h4>
                                    <?php endif; ?>
                                    <small class="text-muted">Peringkat Rata-rata</small>
                                </div>
                            </div>

                            <?php if ($best_achievement): ?>
                                <hr>
                                <div class="alert alert-success small mt-2 mb-0">
                                    <i class="bi bi-trophy-fill"></i> Pernah menjadi #1
                                    di "<?= htmlspecialchars($best_achievement['nama_sesi']) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Card Informasi Umum -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between">
                            <h5 class="mb-0"><i class="bi bi-cup-hot-fill"></i> <?= htmlspecialchars($kafe['nama_kafe']) ?></h5>
                            <!-- <a href="index.php" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a> -->
                        </div>
                        <div class="card-body">
                            <h4><?= htmlspecialchars($kafe['nama_kafe']) ?></h4>
                            <div class="mb-3">
                                <?php
                                // Generate rating stars based on average score
                                $rating_percent = ($statistik['rata_rata'] ?? 0) * 5;
                                $full_stars = floor($rating_percent);
                                $half_star = ($rating_percent - $full_stars) >= 0.5;
                                ?>
                                <div class="text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $full_stars): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php elseif ($half_star && $i == $full_stars + 1): ?>
                                            <i class="bi bi-star-half"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-2">(Rating OCRA)</span>
                                </div>
                            </div>
                            <hr>
                            <h6>Deskripsi:</h6>
                            <p><?= nl2br(htmlspecialchars($kafe['deskripsi'] ?: 'Belum ada deskripsi untuk kafe ini.')) ?></p>
                        </div>
                    </div>

                    <!-- Card Nilai Kriteria -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Penilaian Kriteria</h5>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($nilai_list) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>Nilai</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($nilai = fetch_one($nilai_list)): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= $nilai['nama_kriteria'] ?></strong><br>
                                                        <small class="text-muted">(<?= $nilai['kode_kriteria'] ?>)</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $nilai['jenis_kriteria'] == 'benefit' ? 'success' : 'danger' ?> fs-6 p-2">
                                                            <?= number_format($nilai['nilai'], 2) ?> <?= $nilai['satuan'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($nilai['jenis_kriteria'] == 'benefit'): ?>
                                                            <small class="text-success">Semakin tinggi semakin baik</small>
                                                        <?php else: ?>
                                                            <small class="text-danger">Semakin rendah semakin baik</small>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Belum ada data penilaian untuk kafe ini.
                                    <br><small>Silakan cek kembali nanti.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Perbandingan dengan Kafe Lain -->
                    <?php if (count($top5_data) > 1): ?>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Perbandingan dengan Kafe Lain</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Peringkat</th>
                                                <th>Kafe</th>
                                                <th>Rata-rata Skor</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rank_num = 1;
                                            foreach ($top5_data as $top):
                                                $is_current = ($top['id_kafe'] == $id_kafe);
                                            ?>
                                                <tr class="<?= $is_current ? 'table-warning' : '' ?>">
                                                    <td>
                                                        <?php if ($rank_num == 1): ?>
                                                            🥇
                                                        <?php elseif ($rank_num == 2): ?>
                                                            🥈
                                                        <?php elseif ($rank_num == 3): ?>
                                                            🥉
                                                        <?php else: ?>
                                                            #<?= $rank_num ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($top['nama_kafe']) ?>
                                                        <?php if ($is_current): ?>
                                                            <span class="badge bg-primary ms-2">Saat ini</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?= $rank_num == 1 ? 'warning' : ($rank_num == 2 ? 'info' : 'success') ?>"
                                                                style="width: <?= ($top['avg_skor'] ?? 0) * 100 ?>%">
                                                                <?= number_format(($top['avg_skor'] ?? 0) * 100, 1) ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if (!$is_current): ?>
                                                            <a href="kafe_detail.php?id=<?= $top['id_kafe'] ?>" class="btn btn-sm btn-outline-primary">
                                                                Lihat
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php $rank_num++;
                                            endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                </div>
            </div>

            <style>
                .shadow-sm {
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }

                .card-header {
                    font-weight: 500;
                }

                .progress {
                    border-radius: 10px;
                }

                .progress-bar {
                    border-radius: 10px;
                    transition: width 0.5s ease;
                }
            </style>



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