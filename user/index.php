<?php
require_once '../config/database.php';

$total_kafe = fetch_one(query("SELECT COUNT(*) as total FROM kafe WHERE status='aktif'"))['total'];
$total_sesi = fetch_one(query("SELECT COUNT(*) as total FROM bobot_sesi"))['total'];

// Ambil 3 kafe terbaik berdasarkan rata-rata skor OCRA
$top_kafe = query("SELECT k.id_kafe, k.nama_kafe, k.foto, AVG(h.skor_normal) as avg_skor 
                   FROM hasil_ocra h 
                   JOIN kafe k ON h.id_kafe = k.id_kafe 
                   GROUP BY h.id_kafe 
                   ORDER BY avg_skor DESC 
                   LIMIT 3");
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

            <!-- Welcome Banner -->
            <div class="alert alert-primary border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ti ti-cup" style="font-size: 40px;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1">Selamat Datang di SPK Kafe Tasikmalaya!</h5>
                        <p class="mb-0">Sistem Pendukung Keputusan untuk memilih kafe terbaik menggunakan metode OCRA.</p>
                    </div>
                </div>
            </div>

            <!-- Menu Utama -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card text-center shadow-sm h-100 hover-card">
                        <div class="card-body py-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="ti ti-plus" style="font-size: 32px; color: #667eea;"></i>
                            </div>
                            <h4>Pengujian Baru</h4>
                            <p class="text-muted">Buat perhitungan dengan bobot kustom sesuai preferensi Anda</p>
                            <a href="sesi_baru.php" class="btn btn-primary">Mulai <i class="ti ti-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card text-center shadow-sm h-100 hover-card">
                        <div class="card-body py-4">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="ti ti-history" style="font-size: 32px; color: #17a2b8;"></i>
                            </div>
                            <h4>Riwayat Pengujian</h4>
                            <p class="text-muted">Lihat hasil pengujian sebelumnya dan bandingkan</p>
                            <a href="sesi_lama.php" class="btn btn-info">Lihat <i class="ti ti-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi OCRA -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-info-circle"></i> Tentang Metode OCRA</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h6><i class="ti ti-file-description"></i> Apa itu OCRA?</h6>
                                    <p class="text-muted small">
                                        <strong>OCRA (Operational Competitiveness Rating Analysis)</strong> adalah metode pengambilan keputusan
                                        multikriteria yang dikembangkan untuk mengevaluasi dan meranking alternatif berdasarkan beberapa kriteria
                                        yang saling bertentangan. Metode ini sangat cocok digunakan untuk pemilihan kafe terbaik karena
                                        mempertimbangkan berbagai aspek seperti harga, rating, fasilitas, dan lokasi.
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6><i class="ti ti-calculator"></i> Rumus Normalisasi OCRA</h6>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-2"><strong>Untuk Kriteria Benefit (semakin besar semakin baik):</strong></p>
                                        <p class="font-monospace text-primary mb-2">
                                            r<sub>ij</sub> = (X<sub>ij</sub> - min X<sub>j</sub>) / (max X<sub>j</sub> - min X<sub>j</sub>)
                                        </p>
                                        <p class="mb-0 mt-3"><strong>Untuk Kriteria Cost (semakin kecil semakin baik):</strong></p>
                                        <p class="font-monospace text-danger mb-0">
                                            r<sub>ij</sub> = (max X<sub>j</sub> - X<sub>ij</sub>) / (max X<sub>j</sub> - min X<sub>j</sub>)
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6><i class="ti ti-chart-bar"></i> Rumus Skor Akhir</h6>
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-2"><strong>Skor Alternatif:</strong></p>
                                        <p class="font-monospace text-success mb-2">
                                            S<sub>i</sub> = Σ (W<sub>j</sub> × r<sub>ij</sub>)
                                        </p>
                                        <p class="mb-0 mt-3"><strong>Normalisasi Skor (0-1):</strong></p>
                                        <p class="font-monospace text-info mb-0">
                                            S<sub>normal</sub> = (S<sub>i</sub> - min S) / (max S - min S)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6><i class="ti ti-list-check"></i> Kriteria Penilaian Kafe</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Kriteria</th>
                                                    <th>Jenis</th>
                                                    <th>Penjelasan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>C1</td>
                                                    <td>Harga</td>
                                                    <td><span class="badge bg-danger">Cost</span></td>
                                                    <td>Semakin murah semakin baik</td>
                                                </tr>
                                                <tr>
                                                    <td>C2</td>
                                                    <td>Rating</td>
                                                    <td><span class="badge bg-success">Benefit</span></td>
                                                    <td>Semakin tinggi rating semakin baik</td>
                                                </tr>
                                                <tr>
                                                    <td>C3</td>
                                                    <td>Fasilitas</td>
                                                    <td><span class="badge bg-success">Benefit</span></td>
                                                    <td>Semakin lengkap fasilitas semakin baik</td>
                                                </tr>
                                                <tr>
                                                    <td>C4</td>
                                                    <td>Kapasitas</td>
                                                    <td><span class="badge bg-success">Benefit</span></td>
                                                    <td>Semakin besar kapasitas semakin baik</td>
                                                </tr>
                                                <tr>
                                                    <td>C5</td>
                                                    <td>Jam Operasional</td>
                                                    <td><span class="badge bg-success">Benefit</span></td>
                                                    <td>Semakin lama buka semakin baik</td>
                                                </tr>
                                                <tr>
                                                    <td>C6</td>
                                                    <td>Jarak</td>
                                                    <td><span class="badge bg-danger">Cost</span></td>
                                                    <td>Semakin dekat semakin baik</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-secondary small">
                                        <i class="ti ti-lightbulb"></i> <strong>Tahapan Perhitungan OCRA:</strong>
                                        <ol class="mb-0 mt-2">
                                            <li>Menentukan kriteria dan bobot (total 100%)</li>
                                            <li>Normalisasi matriks keputusan (benefit dan cost dipisah)</li>
                                            <li>Menghitung skor setiap alternatif (kafe)</li>
                                            <li>Normalisasi skor akhir ke rentang 0-1</li>
                                            <li>Perankingan (semakin tinggi skor, semakin baik)</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 Kafe Terbaik -->
            <?php if (mysqli_num_rows($top_kafe) > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="ti ti-trophy"></i> Top 3 Kafe Terbaik (Berdasarkan OCRA)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $medals = ['🥇', '🥈', '🥉'];
                                    $colors = ['warning', 'secondary', 'bronze'];
                                    $no = 0;
                                    while ($top = fetch_one($top_kafe)):
                                    ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card text-center h-100 border-<?= $colors[$no] ?>">
                                                <div class="card-body">
                                                    <div class="display-1 mb-2"><?= $medals[$no] ?></div>
                                                    <h6><?= htmlspecialchars($top['nama_kafe']) ?></h6>
                                                    <div class="progress mt-2" style="height: 8px;">
                                                        <div class="progress-bar bg-<?= $colors[$no] ?>"
                                                            style="width: <?= round($top['avg_skor'] * 100) ?>%"></div>
                                                    </div>
                                                    <small class="text-muted">Skor: <?= round($top['avg_skor'] * 100, 1) ?>%</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        $no++;
                                    endwhile;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Statistik -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0"><?= $total_kafe ?></h2>
                                    <small>Total Kafe Terdaftar</small>
                                </div>
                                <i class="ti ti-building-store" style="font-size: 40px; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0"><?= $total_sesi ?></h2>
                                    <small>Total Sesi Perhitungan</small>
                                </div>
                                <i class="ti ti-calculator" style="font-size: 40px; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0">OCRA</h2>
                                    <small>Metode yang Digunakan</small>
                                </div>
                                <i class="ti ti-chart-bar" style="font-size: 40px; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .border-bronze {
            border-color: #cd7f32;
        }

        .bg-bronze {
            background-color: #cd7f32;
        }

        .font-monospace {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .display-1 {
                font-size: 2.5rem;
            }

            .table-sm {
                font-size: 0.75rem;
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