<?php
require_once '../config/database.php';

$id_kafe = (int)$_GET['id'];
$kafe = fetch_one(query("SELECT * FROM kafe WHERE id_kafe = $id_kafe"));

if (!$kafe) {
    $_SESSION['error'] = "Kafe tidak ditemukan!";
    header("Location: kafe.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kafe = escape($_POST['nama_kafe']);
    $alamat = escape($_POST['alamat']);
    $kecamatan = escape($_POST['kecamatan']);
    $latitude = $_POST['latitude'] ?: 'NULL';
    $longitude = $_POST['longitude'] ?: 'NULL';
    $no_telepon = escape($_POST['no_telepon']);
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    $deskripsi = escape($_POST['deskripsi']);
    $status = $_POST['status'];

    // Upload foto baru jika ada
    $foto_sql = "";
    if ($_FILES['foto']['name']) {
        $target_dir = "../uploads/";
        $foto = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
        $foto_sql = ", foto = '$foto'";

        // Hapus foto lama
        if ($kafe['foto'] && file_exists("../uploads/" . $kafe['foto'])) {
            unlink("../uploads/" . $kafe['foto']);
        }
    }

    $sql = "UPDATE kafe SET 
            nama_kafe = '$nama_kafe',
            alamat = '$alamat',
            kecamatan = '$kecamatan',
            latitude = $latitude,
            longitude = $longitude,
            no_telepon = '$no_telepon',
            jam_buka = '$jam_buka',
            jam_tutup = '$jam_tutup',
            deskripsi = '$deskripsi',
            status = '$status'
            $foto_sql
            WHERE id_kafe = $id_kafe";

    if (query($sql)) {
        $_SESSION['success'] = "Kafe berhasil diupdate!";
        header("Location: kafe.php");
    } else {
        $_SESSION['error'] = "Gagal mengupdate kafe!";
    }
}

$kecamatan_list = [
    'Bungursari',
    'Cibeureum',
    'Cihideung',
    'Cipedes',
    'Indihiang',
    'Kawalu',
    'Mangkubumi',
    'Purbaratu',
    'Tamansari',
    'Tawang'
];

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
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Data Kafe</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Nama Kafe *</label>
                                        <input type="text" name="nama_kafe" class="form-control" value="<?= $kafe['nama_kafe'] ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Alamat *</label>
                                        <textarea name="alamat" class="form-control" rows="3" required><?= $kafe['alamat'] ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Kecamatan *</label>
                                        <select name="kecamatan" class="form-control" required>
                                            <option value="">Pilih Kecamatan</option>
                                            <?php foreach ($kecamatan_list as $kec): ?>
                                                <option value="<?= $kec ?>" <?= $kafe['kecamatan'] == $kec ? 'selected' : '' ?>><?= $kec ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>No. Telepon</label>
                                        <input type="text" name="no_telepon" class="form-control" value="<?= $kafe['no_telepon'] ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label>Jam Buka</label>
                                        <input type="time" name="jam_buka" class="form-control" value="<?= $kafe['jam_buka'] ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label>Jam Tutup</label>
                                        <input type="time" name="jam_tutup" class="form-control" value="<?= $kafe['jam_tutup'] ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Latitude</label>
                                        <input type="text" name="latitude" class="form-control" value="<?= $kafe['latitude'] ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" class="form-control" value="<?= $kafe['longitude'] ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label>Foto Saat Ini</label>
                                        <?php if ($kafe['foto'] && file_exists("../uploads/" . $kafe['foto'])): ?>
                                            <div>
                                                <img src="../uploads/<?= $kafe['foto'] ?>" width="150" class="img-thumbnail">
                                                <br><small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">Belum ada foto</p>
                                        <?php endif; ?>
                                        <input type="file" name="foto" class="form-control mt-2" accept="image/*">
                                    </div>

                                    <div class="mb-3">
                                        <label>Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3"><?= $kafe['deskripsi'] ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="aktif" <?= $kafe['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="nonaktif" <?= $kafe['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="kafe.php" class="btn btn-secondary">Batal</a>
                        </form>
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