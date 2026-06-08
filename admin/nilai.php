<?php
require_once '../config/database.php';

$tahun = $_GET['tahun'] ?? date('Y');
$id_kafe = $_GET['id_kafe'] ?? 0;

// Di bagian proses simpan (admin/nilai.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kafe_post = (int)$_POST['id_kafe'];
    $tahun_post = (int)$_POST['tahun'];

    foreach ($_POST['nilai'] as $id_kriteria => $nilai) {
        // Handle nilai kosong menjadi NULL
        if ($nilai === '' || $nilai === null) {
            // Hapus record jika ada, atau set NULL
            $sql = "DELETE FROM nilai_kafe 
                    WHERE id_kafe = $id_kafe_post 
                    AND id_kriteria = $id_kriteria 
                    AND tahun_penilaian = $tahun_post";
            query($sql);
        } else {
            $nilai = (float)str_replace(',', '.', $nilai);
            $sql = "INSERT INTO nilai_kafe (id_kafe, id_kriteria, nilai, tahun_penilaian) 
                    VALUES ($id_kafe_post, $id_kriteria, $nilai, $tahun_post)
                    ON DUPLICATE KEY UPDATE nilai = $nilai";
            query($sql);
        }
    }

    $_SESSION['success'] = "Nilai berhasil disimpan!";
    header("Location: nilai.php?tahun=$tahun_post&id_kafe=$id_kafe_post");
    exit();
}

// Ambil data
$kafe_list = query("SELECT id_kafe, nama_kafe FROM kafe WHERE status = 'aktif' ORDER BY nama_kafe");
$kriteria_list = query("SELECT * FROM kriteria WHERE is_active = 1 ORDER BY id_kriteria");

// Jika id_kafe belum dipilih, ambil yang pertama
if ($id_kafe == 0 && mysqli_num_rows($kafe_list) > 0) {
    $first = fetch_one($kafe_list);
    $id_kafe = $first['id_kafe'];
}

// Ambil nilai yang sudah ada
$nilai_existing = [];
$nilai_details = []; // Untuk menyimpan detail tambahan
if ($id_kafe > 0) {
    $sql_nilai = "SELECT n.*, k.kode_kriteria, k.nama_kriteria, k.jenis_kriteria, k.satuan
                  FROM nilai_kafe n 
                  JOIN kriteria k ON n.id_kriteria = k.id_kriteria
                  WHERE n.id_kafe = $id_kafe AND n.tahun_penilaian = $tahun";
    $result_nilai = query($sql_nilai);
    while ($row = fetch_one($result_nilai)) {
        $nilai_existing[$row['id_kriteria']] = $row['nilai'];
        $nilai_details[$row['id_kriteria']] = $row;
    }
}

// Hitung persentase kelengkapan data
$total_kriteria = mysqli_num_rows($kriteria_list);
$terisi = count($nilai_existing);
$persentase = $total_kriteria > 0 ? round(($terisi / $total_kriteria) * 100) : 0;
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

            <!-- Notifikasi -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check-circle"></i> <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">

                        <div class="row p-3">
                            <div class="col-md-4">
                                <h5>Input Nilai Kriteria Kafe</h5>
                                <small class="text-muted">Isi nilai untuk setiap kriteria kafe yang dipilih</small>
                            </div>
                            <div class="col-md-8"><!-- Form Pilih Kafe dan Tahun -->
                                <form method="GET" class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Pilih Kafe</label>
                                        <select name="id_kafe" class="form-select" onchange="this.form.submit()">
                                            <option value="">-- Pilih Kafe --</option>
                                            <?php
                                            mysqli_data_seek($kafe_list, 0);
                                            while ($kafe = fetch_one($kafe_list)):
                                            ?>
                                                <option value="<?= $kafe['id_kafe'] ?>" <?= $id_kafe == $kafe['id_kafe'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($kafe['nama_kafe']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Penilaian</label>
                                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                                            <?php for ($y = 2020; $y <= date('Y') + 1; $y++): ?>
                                                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 align-self-end">
                                        <a href="nilai.php" class="btn btn-secondary w-100">
                                            <i class="ti ti-refresh"></i> Reset
                                        </a>
                                    </div>
                                </form>

                                <?php if ($id_kafe > 0 && mysqli_num_rows($kriteria_list) > 0):
                                    $kafe_data = fetch_one(query("SELECT nama_kafe FROM kafe WHERE id_kafe = $id_kafe"));
                                    $nama_kafe = $kafe_data ? $kafe_data['nama_kafe'] : '';
                                ?>
                            </div>
                        </div>









                        <!-- Informasi Status Pengisian -->
                        <!-- <div class="alert alert-info mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <strong><i class="ti ti-building"></i> Kafe:</strong> <?= htmlspecialchars($nama_kafe) ?><br>
                                        <strong><i class="ti ti-calendar"></i> Tahun:</strong> <?= $tahun ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-<?= $persentase == 100 ? 'success' : ($persentase > 0 ? 'warning' : 'danger') ?>"
                                                style="width: <?= $persentase ?>%">
                                                <?= $persentase ?>% Terisi (<?= $terisi ?>/<?= $total_kriteria ?>)
                                            </div>
                                        </div>
                                        <?php if ($persentase == 100): ?>
                                            <small class="text-success mt-1">✓ Semua nilai sudah terisi</small>
                                        <?php elseif ($persentase > 0): ?>
                                            <small class="text-warning mt-1">⚠ Masih ada <?= $total_kriteria - $terisi ?> kriteria yang belum diisi</small>
                                        <?php else: ?>
                                            <small class="text-danger mt-1">✗ Belum ada nilai, silakan input di bawah</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div> -->

                        <form class="p-3" method="POST">
                            <input type="hidden" name="id_kafe" value="<?= $id_kafe ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="8%">Kode</th>
                                            <th class="text-center" width="22%">Kriteria</th>
                                            <th class="text-center" width="10%">Jenis</th>
                                            <th class="text-center" width="10%">Satuan</th>
                                            <th class="text-center" width="25%">Nilai Saat Ini</th>
                                            <!-- <th class="text-center" width="10%">Status</th> -->
                                            <!-- <th class="text-center" width="15%">Keterangan</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        mysqli_data_seek($kriteria_list, 0);
                                        while ($kriteria = fetch_one($kriteria_list)):
                                            $existing_value = $nilai_existing[$kriteria['id_kriteria']] ?? '';
                                            $has_value = ($existing_value !== '' && $existing_value !== null);
                                            $row_class = $has_value ? '' : 'table-warning';
                                        ?>
                                            <tr class="<?= $row_class ?>">
                                                <td><strong><?= $kriteria['kode_kriteria'] ?></strong></td>
                                                <td><?= htmlspecialchars($kriteria['nama_kriteria']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $kriteria['jenis_kriteria'] == 'benefit' ? 'success' : 'danger' ?>">
                                                        <?= $kriteria['jenis_kriteria'] ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($kriteria['satuan'] ?? '-') ?></td>
                                                <td>
                                                    <input type="number"
                                                        name="nilai[<?= $kriteria['id_kriteria'] ?>]"
                                                        class="form-control"
                                                        step="any"
                                                        value="<?= htmlspecialchars($existing_value) ?>"
                                                        placeholder="Kosongkan jika tidak ada"
                                                        style="border-color: <?= $has_value ? '#28a745' : '#ffc107' ?>;">
                                                    <small class="text-muted">Biarkan kosong jika tidak ada data</small>
                                                </td>
                                                <!-- <td class="text-center">
                                                        <?php if ($has_value): ?>
                                                            <span class="badge bg-success">
                                                                <i class="ti ti-check"></i> Tersimpan
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">Terakhir: <?= date('d/m/Y') ?></small>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="ti ti-alert-triangle"></i> Belum diisi
                                                            </span>
                                                        <?php endif; ?>
                                                    </td> -->
                                                <!-- <td>
                                                        <?php if ($kriteria['jenis_kriteria'] == 'benefit'): ?>
                                                            <small class="text-success">
                                                                <i class="ti ti-arrow-up"></i> Semakin besar semakin baik
                                                            </small>
                                                            <?php if ($has_value): ?>
                                                                <div class="text-muted small mt-1">
                                                                    Nilai sebelumnya: <?= number_format($existing_value, 2) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <small class="text-danger">
                                                                <i class="ti ti-arrow-down"></i> Semakin kecil semakin baik
                                                            </small>
                                                            <?php if ($has_value): ?>
                                                                <div class="text-muted small mt-1">
                                                                    Nilai sebelumnya: <?= number_format($existing_value, 2) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td> -->
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i> Simpan Nilai
                                </button>
                                <!-- <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                        <i class="ti ti-reload"></i> Reset Form
                                    </button> -->
                                <!-- <?php if ($persentase == 100): ?>
                                        <a href="nilai.php" class="btn btn-success">
                                            <i class="ti ti-check"></i> Semua Data Lengkap
                                        </a>
                                    <?php endif; ?> -->
                            </div>
                        </form>
                    <?php elseif ($id_kafe == 0): ?>
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle"></i> Silakan pilih kafe terlebih dahulu
                        </div>
                    <?php elseif (mysqli_num_rows($kriteria_list) == 0): ?>
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle"></i> Belum ada kriteria yang aktif.
                            <a href="kriteria.php" class="alert-link">Tambahkan kriteria terlebih dahulu</a>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>


                <div class="col-md-4"> <!-- Tabel Ringkasan Semua Nilai (Opsional) -->
                    <?php if ($id_kafe > 0 && !empty($nilai_details)): ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Ringkasan Nilai <?= htmlspecialchars($nama_kafe) ?> - Tahun <?= $tahun ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kriteria</th>
                                                    <th>Satuan</th>
                                                    <th>Nilai</th>
                                                    <!-- <th>Status</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($nilai_details as $nilai): ?>
                                                    <tr>
                                                        <td><?= $nilai['nama_kriteria'] ?></td>
                                                        <td><?= $nilai['satuan'] ?></td>
                                                        <td><strong><?= number_format($nilai['nilai'], 2) ?></strong></td>
                                                        <!-- <td>
                                                    <span class="badge bg-success">Tersimpan</span>
                                                </td> -->
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>


            </div>




        </div>
    </div>

    <script>
        function resetForm() {
            if (confirm('Yakin ingin mereset semua nilai yang belum disimpan?')) {
                var inputs = document.querySelectorAll('input[type="number"]');
                inputs.forEach(function(input) {
                    // Hanya reset jika nilai saat ini kosong atau user konfirmasi
                    if (input.value === '') {
                        input.value = '';
                    }
                });
            }
        }

        // Auto-hide alert
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                setTimeout(function() {
                    bsAlert.close();
                }, 3000);
            });
        }, 3000);
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