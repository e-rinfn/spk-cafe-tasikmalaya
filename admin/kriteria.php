<?php
require_once '../config/database.php';

// Handle tambah kriteria
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $kode = escape($_POST['kode_kriteria']);
    $nama = escape($_POST['nama_kriteria']);
    $jenis = $_POST['jenis_kriteria'];
    $satuan = escape($_POST['satuan']);
    $bobot = (float)$_POST['bobot_default'];

    $sql = "INSERT INTO kriteria (kode_kriteria, nama_kriteria, jenis_kriteria, satuan, bobot_default) 
            VALUES ('$kode', '$nama', '$jenis', '$satuan', $bobot)";

    if (query($sql)) {
        $_SESSION['success'] = "Kriteria berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan kriteria!";
    }
    header("Location: kriteria.php");
    exit();
}

// Handle edit kriteria
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id_kriteria'];
    $kode = escape($_POST['kode_kriteria']);
    $nama = escape($_POST['nama_kriteria']);
    $jenis = $_POST['jenis_kriteria'];
    $satuan = escape($_POST['satuan']);
    $bobot = (float)$_POST['bobot_default'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "UPDATE kriteria SET 
            kode_kriteria = '$kode',
            nama_kriteria = '$nama',
            jenis_kriteria = '$jenis',
            satuan = '$satuan',
            bobot_default = $bobot,
            is_active = $is_active
            WHERE id_kriteria = $id";

    if (query($sql)) {
        $_SESSION['success'] = "Kriteria berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate kriteria!";
    }
    header("Location: kriteria.php");
    exit();
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $sql = "DELETE FROM kriteria WHERE id_kriteria = $id";
    if (query($sql)) {
        $_SESSION['success'] = "Kriteria berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus kriteria!";
    }
    header("Location: kriteria.php");
    exit();
}

$kriteria = query("SELECT * FROM kriteria ORDER BY id_kriteria");

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
                <div class="card mb-4">
                    <div class="card-header text-white">
                        <h5 class="mb-0">Tambah Kriteria Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-1">
                                    <label>Kode</label>
                                    <input type="text" name="kode_kriteria" class="form-control" placeholder="C7" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Nama Kriteria</label>
                                    <input type="text" name="nama_kriteria" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Jenis</label>
                                    <select name="jenis_kriteria" class="form-control">
                                        <option value="benefit">Benefit</option>
                                        <option value="cost">Cost</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Satuan</label>
                                    <input type="text" name="satuan" class="form-control" placeholder="Rp, km, dll">
                                </div>
                                <div class="col-md-2">
                                    <label>Bobot Default (%)</label>
                                    <input type="number" name="bobot_default" class="form-control" min="0" max="100" required>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" name="tambah" class="btn btn-primary form-control">Tambah</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Kriteria</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>ID</th>
                                    <th>Kode</th>
                                    <th>Nama Kriteria</th>
                                    <th>Jenis</th>
                                    <th>Satuan</th>
                                    <th>Bobot Default</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = fetch_one($kriteria)): ?>
                                    <tr>
                                        <form method="POST">
                                            <input type="hidden" name="id_kriteria" value="<?= $row['id_kriteria'] ?>">
                                            <td><?= $row['id_kriteria'] ?></td>
                                            <td><input type="text" name="kode_kriteria" value="<?= $row['kode_kriteria'] ?>" class="form-control form-control-sm" style="width:80px"></td>
                                            <td><input type="text" name="nama_kriteria" value="<?= $row['nama_kriteria'] ?>" class="form-control form-control-sm"></td>
                                            <td>
                                                <select name="jenis_kriteria" class="form-control form-control-sm">
                                                    <option value="benefit" <?= $row['jenis_kriteria'] == 'benefit' ? 'selected' : '' ?>>Benefit</option>
                                                    <option value="cost" <?= $row['jenis_kriteria'] == 'cost' ? 'selected' : '' ?>>Cost</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="satuan" value="<?= $row['satuan'] ?>" class="form-control form-control-sm"></td>
                                            <td><input type="number" name="bobot_default" value="<?= $row['bobot_default'] ?>" class="form-control form-control-sm" style="width:80px" step="0.5"></td>
                                            <td>
                                                <input type="checkbox" name="is_active" value="1" <?= $row['is_active'] ? 'checked' : '' ?>>
                                                <small>Aktif</small>
                                            </td>
                                            <td class="text-center">
                                                <button type="submit" name="edit" class="btn btn-warning btn-sm">Update</button>
                                                <a href="?hapus=<?= $row['id_kriteria'] ?>" onclick="return confirm('Yakin hapus kriteria ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                            </td>
                                        </form>
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