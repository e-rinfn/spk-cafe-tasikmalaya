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

// Ambil hasil ranking (termasuk latitude & longitude)
$hasil = query("SELECT h.*, k.nama_kafe, k.alamat, k.latitude, k.longitude, k.kecamatan, k.foto
                FROM hasil_ocra h 
                JOIN kafe k ON h.id_kafe = k.id_kafe 
                WHERE h.id_sesi = $id_sesi 
                ORDER BY h.peringkat ASC");

// Ambil bobot yang digunakan
$bobot = query("SELECT d.*, kr.nama_kriteria, kr.kode_kriteria 
                FROM detail_bobot_sesi d 
                JOIN kriteria kr ON d.id_kriteria = kr.id_kriteria 
                WHERE d.id_sesi = $id_sesi");

// Fungsi helper untuk generate Google Maps link
function getGoogleMapsLink($latitude, $longitude, $nama_kafe)
{
    if ($latitude && $longitude && $latitude != 'NULL' && $longitude != 'NULL') {
        $lat = (float)$latitude;
        $lng = (float)$longitude;
        return "https://www.google.com/maps?q={$lat},{$lng}&z=17";
    }
    return "#";
}

function getGoogleMapsSearchLink($alamat, $nama_kafe)
{
    $search = urlencode($nama_kafe . " Tasikmalaya");
    return "https://www.google.com/maps/search/?api=1&query={$search}";
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
        <div class="header-wrapper">
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
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

        </div>
    </header>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- Tombol Kembali -->
            <div class="mb-3">
                <a href="sesi_lama.php" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali ke Riwayat Sesi
                </a>
            </div>

            <!-- Info Sesi -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white text-white">
                    <h5 class="mb-0">
                        <i class="ti ti-calculator me-2"></i>
                        <?= htmlspecialchars($sesi['nama_sesi']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Tanggal Perhitungan</label>
                            <div class="fw-semibold">
                                <i class="ti ti-calendar text-success me-2"></i>
                                <?= date('d-m-Y H:i', strtotime($sesi['tanggal_perhitungan'])) ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Dibuat Oleh</label>
                            <div class="fw-semibold">
                                <i class="ti ti-user text-primary me-2"></i>
                                <?= htmlspecialchars($sesi['created_by']) ?>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Keterangan</label>
                            <div class="fw-semibold">
                                <i class="ti ti-info-circle text-warning me-2"></i>
                                <?= htmlspecialchars($sesi['keterangan'] ?? '-') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bobot Kriteria -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ti ti-adjustments me-2"></i>
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
                                                    <?= $row['kode_kriteria'] ?> - <?= $row['nama_kriteria'] ?>
                                                </h6>
                                                <span class="badge bg-success">
                                                    <?= $row['bobot'] ?>%
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-info"
                                                    role="progressbar"
                                                    style="width: <?= $row['bobot'] ?>%;">
                                                </div>
                                            </div>
                                            <div class="mt-2 text-muted small">
                                                Tingkat kepentingan: <strong><?= $row['bobot'] ?>%</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Belum ada bobot yang ditentukan untuk sesi ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hasil Perankingan -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ti ti-trophy me-2"></i>
                            Hasil Perankingan Kafe Terbaik
                        </h5>
                        <span class="badge bg-light text-dark fs-6">
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

                        <!-- TOP 1 - Card Kafe Terbaik -->
                        <div class="card border-warning bg-warning bg-opacity-10 mb-4">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="ti ti-crown text-warning" style="font-size: 48px;"></i>
                                </div>
                                <h4 class="fw-bold text-warning">🏆 Kafe Terbaik</h4>
                                <h3 class="fw-bold"><?= htmlspecialchars($top['nama_kafe']) ?></h3>
                                <p class="text-muted mb-2">
                                    <i class="ti ti-map-pin"></i> <?= htmlspecialchars($top['kecamatan'] ?? '') ?><br>
                                    <?= htmlspecialchars($top['alamat']) ?>
                                </p>
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    Skor: <?= number_format($top['skor_normal'], 4) ?>
                                </span>

                                <!-- Tombol Maps untuk Kafe Terbaik -->
                                <div class="mt-3">
                                    <?php if ($top['latitude'] && $top['longitude'] && $top['latitude'] != 'NULL' && $top['longitude'] != 'NULL'): ?>
                                        <a href="<?= getGoogleMapsLink($top['latitude'], $top['longitude'], $top['nama_kafe']) ?>"
                                            target="_blank" class="btn btn-success">
                                            <i class="ti ti-map"></i> Buka Google Maps (Koordinat)
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= getGoogleMapsSearchLink($top['alamat'], $top['nama_kafe']) ?>"
                                            target="_blank" class="btn btn-info">
                                            <i class="ti ti-map"></i> Google Maps
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Semua Kafe dalam Card (Mobile Friendly) -->
                        <h6 class="mb-3">Daftar Semua Kafe</h6>
                        <div class="row">
                            <?php
                            mysqli_data_seek($hasil, 0);
                            while ($row = fetch_one($hasil)):
                                $hasCoordinates = ($row['latitude'] && $row['longitude'] && $row['latitude'] != 'NULL' && $row['longitude'] != 'NULL');
                            ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 shadow-sm">
                                        <!-- Foto Kafe -->
                                        <div class="card-img-top-wrapper" style="height: 150px; overflow: hidden;">
                                            <?php if ($row['foto'] && file_exists("../uploads/" . $row['foto'])): ?>
                                                <img src="../uploads/<?= $row['foto'] ?>"
                                                    class="card-img-top"
                                                    alt="<?= $row['nama_kafe'] ?>"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                                    <i class="ti ti-cup" style="font-size: 48px; color: #ccc;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body">
                                            <!-- Peringkat -->
                                            <div class="mb-2">
                                                <?php if ($row['peringkat'] == 1): ?>
                                                    <span class="badge bg-warning text-dark fs-6">🥇 Peringkat #1</span>
                                                <?php elseif ($row['peringkat'] == 2): ?>
                                                    <span class="badge bg-secondary fs-6">🥈 Peringkat #2</span>
                                                <?php elseif ($row['peringkat'] == 3): ?>
                                                    <span class="badge bg-info fs-6">🥉 Peringkat #3</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">#<?= $row['peringkat'] ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Nama Kafe -->
                                            <h6 class="card-title mb-1"><?= htmlspecialchars($row['nama_kafe']) ?></h6>

                                            <!-- Alamat -->
                                            <p class="small text-muted mb-2">
                                                <i class="ti ti-map-pin"></i> <?= htmlspecialchars(substr($row['alamat'], 0, 60)) ?>
                                            </p>

                                            <!-- Skor -->
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between small">
                                                    <span>Skor Normal</span>
                                                    <span class="fw-bold"><?= number_format($row['skor_normal'], 4) ?></span>
                                                </div>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-success" style="width: <?= $row['skor_normal'] * 100 ?>%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex gap-2">
                                                <a href="kafe_detail.php?id=<?= $row['id_kafe'] ?>"
                                                    class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="ti ti-eye"></i> Detail
                                                </a>

                                                <!-- Tombol Google Maps -->
                                                <?php if ($hasCoordinates): ?>
                                                    <a href="<?= getGoogleMapsLink($row['latitude'], $row['longitude'], $row['nama_kafe']) ?>"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm"
                                                        title="Buka Google Maps">
                                                        <i class="ti ti-map"></i> Google Maps
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= getGoogleMapsSearchLink($row['alamat'], $row['nama_kafe']) ?>"
                                                        target="_blank"
                                                        class="btn btn-info btn-sm"
                                                        title="Cari di Google Maps">
                                                        <i class="ti ti-map"></i> Google Maps
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                    <?php else: ?>

                        <div class="text-center py-5">
                            <i class="ti ti-calculator" style="font-size: 64px; color: #6c757d;"></i>
                            <h4 class="mt-3">Belum Ada Hasil Perhitungan</h4>
                            <p class="text-muted">Sesi ini belum memiliki hasil perhitungan OCRA.</p>
                            <div class="alert alert-warning text-start mt-4">
                                <strong>Langkah yang perlu dilakukan:</strong>
                                <ol class="mb-0 mt-2">
                                    <li>Pastikan data kafe sudah tersedia.</li>
                                    <li>Pastikan nilai setiap kriteria telah diinput.</li>
                                    <li>Lakukan proses perhitungan OCRA.</li>
                                </ol>
                            </div>
                            <a href="../proses/hitung_ulang.php?id_sesi=<?= $id_sesi ?>" class="btn btn-primary mt-3">
                                <i class="ti ti-refresh me-2"></i> Hitung Ulang OCRA
                            </a>
                        </div>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <style>
        .card-img-top-wrapper {
            background-color: #f8f9fa;
        }

        .hover-card {
            transition: transform 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-3px);
        }

        .bg-warning-opacity {
            background-color: rgba(255, 193, 7, 0.1);
        }

        @media (max-width: 768px) {
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.7rem;
            }

            .card-title {
                font-size: 0.9rem;
            }
        }
    </style>

    <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/pcoded.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>

    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>

</body>

</html>