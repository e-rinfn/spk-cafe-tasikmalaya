<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kafe = escape($_POST['nama_kafe']);
    $alamat = escape($_POST['alamat']);
    $kecamatan = escape($_POST['kecamatan']);
    $latitude = $_POST['latitude'] ?? NULL;
    $longitude = $_POST['longitude'] ?? NULL;
    $no_telepon = escape($_POST['no_telepon']);
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    $deskripsi = escape($_POST['deskripsi']);
    $status = $_POST['status'];

    // Upload foto
    $foto = '';
    if ($_FILES['foto']['name']) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $foto = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }

    $sql = "INSERT INTO kafe (nama_kafe, alamat, kecamatan, latitude, longitude, no_telepon, 
            jam_buka, jam_tutup, deskripsi, foto, status) 
            VALUES ('$nama_kafe', '$alamat', '$kecamatan', '$latitude', '$longitude', 
            '$no_telepon', '$jam_buka', '$jam_tutup', '$deskripsi', '$foto', '$status')";

    if (query($sql)) {
        $_SESSION['success'] = "Kafe berhasil ditambahkan!";
        header("Location: kafe.php");
    } else {
        $_SESSION['error'] = "Gagal menambahkan kafe!";
    }
}

// Ambil daftar kecamatan di Tasikmalaya
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
                        <h5>Tambah Data Kafe Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Nama Kafe *</label>
                                        <input type="text" name="nama_kafe" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Alamat *</label>
                                        <textarea name="alamat" class="form-control" rows="3" required></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Kecamatan *</label>
                                        <select name="kecamatan" class="form-control" required>
                                            <option value="">Pilih Kecamatan</option>
                                            <?php foreach ($kecamatan_list as $kec): ?>
                                                <option value="<?= $kec ?>"><?= $kec ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>No. Telepon</label>
                                        <input type="text" name="no_telepon" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label>Jam Buka</label>
                                        <input type="time" name="jam_buka" class="form-control" value="08:00">
                                    </div>

                                    <div class="mb-3">
                                        <label>Jam Tutup</label>
                                        <input type="time" name="jam_tutup" class="form-control" value="22:00">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Latitude (Google Maps)</label>
                                        <input type="text" name="latitude" class="form-control" placeholder="Contoh: -7.3276">
                                        <small class="text-muted">Opsional, untuk peta</small>
                                    </div>

                                    <div class="mb-3">
                                        <label>Longitude (Google Maps)</label>
                                        <input type="text" name="longitude" class="form-control" placeholder="Contoh: 108.2212">
                                        <small class="text-muted">Opsional, untuk peta</small>
                                    </div>

                                    <div class="mb-3">
                                        <label>Foto Kafe</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                    </div>

                                    <div class="mb-3">
                                        <label>Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Fasilitas, suasana, dll"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="aktif">Aktif</option>
                                            <option value="nonaktif">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
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