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
                <div class="card-header bg-primary ">
                    <h5 class="mb-0 text-white"><i class="ti ti-settings"></i> Form Sesi Perhitungan</h5>
                </div>
                <div class="card-body">
                    <form action="../proses/hitung_ocra.php" method="POST">
                        <div class="row">
                            <!-- Nama Sesi -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="ti ti-tag"></i> Nama Sesi
                                </label>
                                <input type="text" name="nama_sesi" class="form-control form-control-sm"
                                    required>
                                <small class="text-muted">Berikan nama yang mudah diingat untuk sesi ini</small>
                            </div>

                            <div class="col-md-6">
                                <!-- Tahun Penilaian -->
                                <label class="form-label fw-bold">
                                    <i class="ti ti-calendar"></i> Tahun Penilaian
                                </label>
                                <select name="tahun" class="form-select form-select-sm">
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>

                        </div>

                        <hr class="my-4">

                        <!-- Bobot Kriteria -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="ti ti-chart-pie"></i> Bobot Kriteria
                            </label>
                            <p class="small text-muted">Total bobot harus 100%. Gunakan tombol + / - untuk memudahkan</p>
                        </div>

                        <!-- Card Grid untuk Bobot Kriteria (Dengan Tombol + dan -) -->
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

                                            <!-- Input Group dengan Tombol + dan - -->
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Tombol Minus -->
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-decrement"
                                                    data-id="<?= $row['id_kriteria'] ?>"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="ti ti-minus"></i>
                                                </button>

                                                <!-- Slider -->
                                                <input type="range"
                                                    name="bobot[<?= $row['id_kriteria'] ?>]"
                                                    class="form-range bobot-slider flex-grow-1"
                                                    value="<?= $row['bobot_default'] ?>"
                                                    min="0" max="100" step="1"
                                                    data-id="<?= $row['id_kriteria'] ?>">

                                                <!-- Tombol Plus -->
                                                <button type="button"
                                                    class="btn btn-outline-success btn-sm btn-increment"
                                                    data-id="<?= $row['id_kriteria'] ?>"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>

                                            <!-- Number Input dan Progress -->
                                            <div class="mt-2">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <small class="text-muted">Nilai:</small>
                                                    <div class="input-group" style="width: 100px;">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center bobot-number"
                                                            id="bobot_<?= $row['id_kriteria'] ?>"
                                                            value="<?= $row['bobot_default'] ?>"
                                                            min="0" max="100" step="1"
                                                            style="font-size: 14px;">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-primary"
                                                        id="progress_<?= $row['id_kriteria'] ?>"
                                                        style="width: <?= $row['bobot_default'] ?>%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- Tombol Aksi Cepat -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary" id="resetAllBobot">
                                        <i class="ti ti-refresh"></i> Reset ke Default
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="equalizeBobot">
                                        <i class="ti ti-chart-pie"></i> Rata-rata Bobot
                                    </button>
                                    <button type="button" class="btn btn-outline-success" id="maxFirstCriteria">
                                        <i class="ti ti-star"></i> Fokus Kriteria 1
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Total Bobot Card -->
                        <div class="card bg-light mb-4">
                            <div class="card-body text-center">
                                <h5 class="mb-2">Total Bobot</h5>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="decrementTotal" style="width: 40px;">
                                        <i class="ti ti-minus"></i>
                                    </button>
                                    <div class="display-4 fw-bold" id="total_bobot">0</div>
                                    <span class="fs-3">%</span>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="incrementTotal" style="width: 40px;">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                                <div class="progress mt-3" style="height: 10px;">
                                    <div class="progress-bar bg-danger" id="total_progress" style="width: 0%"></div>
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
            cursor: pointer;
        }

        .btn-decrement,
        .btn-increment {
            transition: all 0.2s ease;
        }

        .btn-decrement:hover {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .btn-increment:hover {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .bobot-number {
            -moz-appearance: textfield;
        }

        .bobot-number::-webkit-inner-spin-button,
        .bobot-number::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        @media (max-width: 768px) {
            .kriteria-card {
                margin-bottom: 0;
            }

            .btn-group {
                flex-wrap: wrap;
            }

            .btn-group .btn {
                font-size: 12px;
                padding: 6px 8px;
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
                let selisih = 100 - total;
                let pesan = selisih > 0 ? `Kurang ${selisih}% lagi` : `Kelebihan ${Math.abs(selisih)}%`;
                document.getElementById('totalMessage').innerHTML = `<span class="text-danger"><i class="ti ti-alert-triangle"></i> Total bobot harus 100%. ${pesan}</span>`;
                document.getElementById('btnSubmit').disabled = true;
            }
        }

        // Update nilai dari slider ke number dan progress
        function updateFromSlider(slider, numberId, progressId) {
            const number = document.getElementById(numberId);
            const progress = document.getElementById(progressId);

            slider.addEventListener('input', function() {
                let val = parseInt(this.value);
                number.value = val;
                if (progress) progress.style.width = val + '%';
                hitungTotal();
            });
        }

        // Update dari number ke slider dan progress
        function updateFromNumber(number, sliderId, progressId) {
            const slider = document.getElementById(sliderId);
            const progress = document.getElementById(progressId);

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

        // Tombol increment
        function setupIncrementButton(btn, sliderId, numberId) {
            const slider = document.getElementById(sliderId);
            const number = document.getElementById(numberId);

            btn.addEventListener('click', function() {
                let currentVal = parseInt(slider.value);
                let newVal = Math.min(currentVal + 1, 100);
                slider.value = newVal;
                number.value = newVal;
                let progress = document.getElementById(progressId);
                if (progress) progress.style.width = newVal + '%';
                hitungTotal();
            });
        }

        // Tombol decrement
        function setupDecrementButton(btn, sliderId, numberId) {
            const slider = document.getElementById(sliderId);
            const number = document.getElementById(numberId);

            btn.addEventListener('click', function() {
                let currentVal = parseInt(slider.value);
                let newVal = Math.max(currentVal - 1, 0);
                slider.value = newVal;
                number.value = newVal;
                let progress = document.getElementById(progressId);
                if (progress) progress.style.width = newVal + '%';
                hitungTotal();
            });
        }

        // Reset semua bobot ke default
        function resetAllBobot() {
            const slides = document.querySelectorAll('.bobot-slider');
            slides.forEach(slide => {
                let defaultVal = slide.getAttribute('value');
                if (!defaultVal) defaultVal = 0;
                slide.value = defaultVal;
                let numberId = `bobot_${slide.getAttribute('data-id')}`;
                let number = document.getElementById(numberId);
                if (number) number.value = defaultVal;
                let progress = document.getElementById(`progress_${slide.getAttribute('data-id')}`);
                if (progress) progress.style.width = defaultVal + '%';
            });
            hitungTotal();
        }

        // Rata-rata bobot
        function equalizeBobot() {
            const slides = document.querySelectorAll('.bobot-slider');
            const totalKriteria = slides.length;
            const avgValue = Math.floor(100 / totalKriteria);
            let remainder = 100 - (avgValue * totalKriteria);

            slides.forEach((slide, index) => {
                let newVal = avgValue;
                if (index < remainder) newVal++;
                slide.value = newVal;
                let numberId = `bobot_${slide.getAttribute('data-id')}`;
                let number = document.getElementById(numberId);
                if (number) number.value = newVal;
                let progress = document.getElementById(`progress_${slide.getAttribute('data-id')}`);
                if (progress) progress.style.width = newVal + '%';
            });
            hitungTotal();
        }

        // Fokus pada kriteria pertama (bobot besar)
        function maxFirstCriteria() {
            const slides = document.querySelectorAll('.bobot-slider');
            if (slides.length === 0) return;

            // Set kriteria pertama = 40%
            slides[0].value = 40;
            let numberId = `bobot_${slides[0].getAttribute('data-id')}`;
            let number = document.getElementById(numberId);
            if (number) number.value = 40;
            let progress = document.getElementById(`progress_${slides[0].getAttribute('data-id')}`);
            if (progress) progress.style.width = '40%';

            // Sisa bobot dibagi ke kriteria lain
            let remaining = 60;
            let otherCount = slides.length - 1;
            let avgOther = Math.floor(remaining / otherCount);
            let remainder = remaining - (avgOther * otherCount);

            for (let i = 1; i < slides.length; i++) {
                let newVal = avgOther;
                if (i - 1 < remainder) newVal++;
                slides[i].value = newVal;
                let numId = `bobot_${slides[i].getAttribute('data-id')}`;
                let numInput = document.getElementById(numId);
                if (numInput) numInput.value = newVal;
                let prog = document.getElementById(`progress_${slides[i].getAttribute('data-id')}`);
                if (prog) prog.style.width = newVal + '%';
            }
            hitungTotal();
        }

        // Inisialisasi semua komponen
        document.addEventListener('DOMContentLoaded', function() {
            // Setup semua slider dan number input
            const slides = document.querySelectorAll('.bobot-slider');

            slides.forEach(slide => {
                const id = slide.getAttribute('data-id');
                const numberInput = document.getElementById(`bobot_${id}`);
                const progressBar = document.getElementById(`progress_${id}`);
                const decrementBtn = document.querySelector(`.btn-decrement[data-id="${id}"]`);
                const incrementBtn = document.querySelector(`.btn-increment[data-id="${id}"]`);

                if (slide && numberInput) {
                    // Set nilai awal
                    numberInput.value = slide.value;
                    if (progressBar) progressBar.style.width = slide.value + '%';

                    // Event slider
                    slide.addEventListener('input', function() {
                        numberInput.value = this.value;
                        if (progressBar) progressBar.style.width = this.value + '%';
                        hitungTotal();
                    });

                    // Event number input
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

                    // Tombol decrement
                    if (decrementBtn) {
                        decrementBtn.addEventListener('click', function() {
                            let currentVal = parseInt(slide.value);
                            let newVal = Math.max(currentVal - 1, 0);
                            slide.value = newVal;
                            numberInput.value = newVal;
                            if (progressBar) progressBar.style.width = newVal + '%';
                            hitungTotal();
                        });
                    }

                    // Tombol increment
                    if (incrementBtn) {
                        incrementBtn.addEventListener('click', function() {
                            let currentVal = parseInt(slide.value);
                            let newVal = Math.min(currentVal + 1, 100);
                            slide.value = newVal;
                            numberInput.value = newVal;
                            if (progressBar) progressBar.style.width = newVal + '%';
                            hitungTotal();
                        });
                    }
                }
            });

            // Tombol aksi cepat
            const resetBtn = document.getElementById('resetAllBobot');
            const equalizeBtn = document.getElementById('equalizeBobot');
            const maxFirstBtn = document.getElementById('maxFirstCriteria');
            const decrementTotalBtn = document.getElementById('decrementTotal');
            const incrementTotalBtn = document.getElementById('incrementTotal');

            if (resetBtn) resetBtn.addEventListener('click', resetAllBobot);
            if (equalizeBtn) equalizeBtn.addEventListener('click', equalizeBobot);
            if (maxFirstBtn) maxFirstBtn.addEventListener('click', maxFirstCriteria);

            // Tombol increment/decrement total (menambah 1 ke semua kriteria)
            if (decrementTotalBtn) {
                decrementTotalBtn.addEventListener('click', function() {
                    const slides = document.querySelectorAll('.bobot-slider');
                    slides.forEach(slide => {
                        let currentVal = parseInt(slide.value);
                        let newVal = Math.max(currentVal - 1, 0);
                        slide.value = newVal;
                        let numberId = `bobot_${slide.getAttribute('data-id')}`;
                        let number = document.getElementById(numberId);
                        if (number) number.value = newVal;
                        let progress = document.getElementById(`progress_${slide.getAttribute('data-id')}`);
                        if (progress) progress.style.width = newVal + '%';
                    });
                    hitungTotal();
                });
            }

            if (incrementTotalBtn) {
                incrementTotalBtn.addEventListener('click', function() {
                    const slides = document.querySelectorAll('.bobot-slider');
                    slides.forEach(slide => {
                        let currentVal = parseInt(slide.value);
                        let newVal = Math.min(currentVal + 1, 100);
                        slide.value = newVal;
                        let numberId = `bobot_${slide.getAttribute('data-id')}`;
                        let number = document.getElementById(numberId);
                        if (number) number.value = newVal;
                        let progress = document.getElementById(`progress_${slide.getAttribute('data-id')}`);
                        if (progress) progress.style.width = newVal + '%';
                    });
                    hitungTotal();
                });
            }

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