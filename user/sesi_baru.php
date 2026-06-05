<?php
require_once '../config/database.php';

// Ambil data kriteria
$kriteria = query("SELECT * FROM kriteria WHERE is_active = 1");
$kafe_list = query("SELECT COUNT(*) as total FROM kafe WHERE status='aktif'");
$total_kafe = fetch_one($kafe_list)['total'];

if ($total_kafe == 0) {
    echo "<div class='alert alert-warning'>Belum ada data kafe. Silakan hubungi admin.</div>";
    require_once '../includes/footer.php';
    exit();
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

            <!-- Header -->
            <div class="mb-4">
                <h4><i class="ti ti-calculator"></i> Sesi Perhitungan OCRA</h4>
                <p class="text-muted">Buat sesi baru dengan mengatur bobot kriteria di bawah ini</p>
            </div>

            <!-- Form Card Utama -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ti ti-settings"></i> Form Sesi Perhitungan</h5>
                </div>
                <div class="card-body">
                    <form action="../proses/hitung_ocra.php" method="POST">
                        <!-- Nama Sesi -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="ti ti-tag"></i> Nama Sesi
                            </label>
                            <input type="text" name="nama_sesi" class="form-control form-control-lg"
                                required placeholder="Contoh: Sesi Desember 2024">
                            <small class="text-muted">Berikan nama yang mudah diingat untuk sesi ini</small>
                        </div>

                        <!-- Tahun Penilaian -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="ti ti-calendar"></i> Tahun Penilaian
                            </label>
                            <select name="tahun" class="form-select form-select-lg">
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                                <option value="2024" selected>2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>

                        <hr class="my-4">

                        <!-- Bobot Kriteria -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="ti ti-chart-pie"></i> Bobot Kriteria
                            </label>
                            <p class="small text-muted">Total bobot harus 100%</p>
                        </div>

                        <!-- Card Grid untuk Bobot Kriteria -->
                        <div class="row g-3 mb-4" id="kriteriaContainer">
                            <?php
                            mysqli_data_seek($kriteria, 0);
                            while ($row = fetch_one($kriteria)):
                            ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-2 kriteria-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge bg-secondary"><?= $row['kode_kriteria'] ?></span>
                                                    <h6 class="mt-2 mb-1"><?= $row['nama_kriteria'] ?></h6>
                                                </div>
                                                <span class="badge bg-<?= $row['jenis_kriteria'] == 'benefit' ? 'success' : 'danger' ?>">
                                                    <i class="ti ti-arrow-<?= $row['jenis_kriteria'] == 'benefit' ? 'up' : 'down' ?>"></i>
                                                    <?= $row['jenis_kriteria'] ?>
                                                </span>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted">Bobot Default: <?= $row['bobot_default'] ?>%</small>
                                            </div>

                                            <div class="input-group">
                                                <input type="range"
                                                    name="bobot[<?= $row['id_kriteria'] ?>]"
                                                    class="form-range bobot-slider"
                                                    value="<?= $row['bobot_default'] ?>"
                                                    min="0" max="100" step="1"
                                                    data-id="<?= $row['id_kriteria'] ?>">
                                                <input type="number"
                                                    class="form-control bobot-number"
                                                    id="bobot_<?= $row['id_kriteria'] ?>"
                                                    value="<?= $row['bobot_default'] ?>"
                                                    min="0" max="100" step="1"
                                                    style="width: 80px;">
                                            </div>

                                            <div class="mt-2">
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-primary"
                                                        id="progress_<?= $row['id_kriteria'] ?>"
                                                        style="width: <?= $row['bobot_default'] ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- Total Bobot Card -->
                        <div class="card bg-light mb-4">
                            <div class="card-body text-center">
                                <h5 class="mb-2">Total Bobot</h5>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <div class="display-4 fw-bold" id="total_bobot">0</div>
                                    <span class="fs-3">%</span>
                                </div>
                                <div class="progress mt-3" style="height: 10px;">
                                    <div class="progress-bar <?= $total == 100 ? 'bg-success' : 'bg-danger' ?>"
                                        id="total_progress" style="width: 0%"></div>
                                </div>
                                <div id="totalMessage" class="small mt-2"></div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill" id="btnSubmit" disabled>
                                <i class="ti ti-calculator"></i> Hitung OCRA
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="ti ti-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <style>
        .kriteria-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }

        .kriteria-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-range {
            width: calc(100% - 90px);
            display: inline-block;
            vertical-align: middle;
        }

        .bobot-number {
            display: inline-block;
            width: 80px;
            text-align: center;
        }

        .input-group {
            flex-wrap: nowrap;
        }

        @media (max-width: 768px) {
            .kriteria-card {
                margin-bottom: 0;
            }

            .form-range {
                width: calc(100% - 70px);
            }

            .bobot-number {
                width: 60px;
                font-size: 12px;
            }

            .btn-lg {
                padding: 10px 16px;
                font-size: 14px;
            }

            .display-4 {
                font-size: 2rem;
            }
        }

        .progress-bar {
            transition: width 0.3s ease;
        }
    </style>

    <script>
        // Fungsi untuk menghitung total bobot
        function hitungTotal() {
            let slides = document.querySelectorAll('.bobot-slider');
            let total = 0;

            slides.forEach(slide => {
                total += parseInt(slide.value) || 0;
            });

            // Update total display
            document.getElementById('total_bobot').innerText = total;

            // Update progress bar total
            let totalProgress = document.getElementById('total_progress');
            totalProgress.style.width = total + '%';

            if (total === 100) {
                totalProgress.classList.remove('bg-danger');
                totalProgress.classList.add('bg-success');
                document.getElementById('totalMessage').innerHTML = '<span class="text-success"><i class="ti ti-check-circle"></i> Bobot sudah 100%! Silakan hitung OCRA.</span>';
                document.getElementById('btnSubmit').disabled = false;
            } else {
                totalProgress.classList.remove('bg-success');
                totalProgress.classList.add('bg-danger');
                document.getElementById('totalMessage').innerHTML = '<span class="text-danger"><i class="ti ti-alert-triangle"></i> Total bobot harus 100%. Saat ini: ' + total + '%</span>';
                document.getElementById('btnSubmit').disabled = true;
            }
        }

        // Sinkronisasi antara slider dan number input
        function syncInputs(sliderId, numberId, progressId) {
            const slider = document.getElementById(sliderId);
            const number = document.getElementById(numberId);
            const progress = document.getElementById(progressId);

            if (slider && number) {
                // Slider berubah -> update number dan progress
                slider.addEventListener('input', function() {
                    number.value = this.value;
                    if (progress) progress.style.width = this.value + '%';
                    hitungTotal();
                });

                // Number berubah -> update slider dan progress
                number.addEventListener('input', function() {
                    let val = parseInt(this.value);
                    if (isNaN(val)) val = 0;
                    if (val < 0) val = 0;
                    if (val > 100) val = 100;
                    slider.value = val;
                    this.value = val;
                    if (progress) progress.style.width = val + '%';
                    hitungTotal();
                });
            }
        }

        // Inisialisasi semua komponen
        document.addEventListener('DOMContentLoaded', function() {
            // Dapatkan semua slider dan number input
            const slides = document.querySelectorAll('.bobot-slider');

            slides.forEach(slide => {
                const id = slide.getAttribute('data-id');
                const numberInput = document.getElementById(`bobot_${id}`);
                const progressBar = document.getElementById(`progress_${id}`);

                if (slide && numberInput) {
                    // Set nilai awal
                    numberInput.value = slide.value;
                    if (progressBar) progressBar.style.width = slide.value + '%';

                    // Event untuk slider
                    slide.addEventListener('input', function() {
                        numberInput.value = this.value;
                        if (progressBar) progressBar.style.width = this.value + '%';
                        hitungTotal();
                    });

                    // Event untuk number input
                    numberInput.addEventListener('input', function() {
                        let val = parseInt(this.value);
                        if (isNaN(val)) val = 0;
                        if (val < 0) val = 0;
                        if (val > 100) val = 100;
                        slide.value = val;
                        this.value = val;
                        if (progressBar) progressBar.style.width = val + '%';
                        hitungTotal();
                    });
                }
            });

            // Hitung total awal
            hitungTotal();
        });
    </script>

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