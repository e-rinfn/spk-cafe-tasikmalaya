<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID sesi tidak ditemukan!";
    header("Location: ../user/sesi_lama.php");
    exit();
}

$id_sesi = (int)$_GET['id'];

// Cek apakah sesi dengan id tersebut ada
$check_sesi = query("SELECT * FROM bobot_sesi WHERE id_sesi = $id_sesi");
if (mysqli_num_rows($check_sesi) == 0) {
    $_SESSION['error'] = "Sesi tidak ditemukan!";
    header("Location: ../user/sesi_lama.php");
    exit();
}

$sesi_data = fetch_one($check_sesi);

// Mulai transaksi untuk memastikan semua data terhapus
// mysqli_begin_transaction($conn);

try {
    // 1. Hapus hasil OCRA terlebih dahulu (foreign key ke detail_bobot_sesi & sesi)
    $delete_hasil = query("DELETE FROM hasil_ocra WHERE id_sesi = $id_sesi");
    if (!$delete_hasil) {
        throw new Exception("Gagal menghapus data hasil OCRA");
    }

    // 2. Hapus detail bobot sesi (foreign key ke bobot_sesi)
    $delete_bobot = query("DELETE FROM detail_bobot_sesi WHERE id_sesi = $id_sesi");
    if (!$delete_bobot) {
        throw new Exception("Gagal menghapus data bobot sesi");
    }

    // 3. Hapus sesi utama
    $delete_sesi = query("DELETE FROM bobot_sesi WHERE id_sesi = $id_sesi");
    if (!$delete_sesi) {
        throw new Exception("Gagal menghapus data sesi");
    }

    // Commit transaksi jika semua berhasil
    // mysqli_commit($conn);

    // Log aktivitas
    $nama_sesi = $sesi_data['nama_sesi'];
    $user = $_SESSION['nama_lengkap'];

    $_SESSION['success'] = "Sesi '$nama_sesi' berhasil dihapus!";

    // Redirect berdasarkan role
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/sesi_lama.php");
    } else {
        header("Location: ../user/sesi_lama.php");
    }
    exit();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    // mysqli_rollback($conn);

    $_SESSION['error'] = "Gagal menghapus sesi: " . $e->getMessage();

    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/sesi_lama.php");
    } else {
        header("Location: ../user/sesi_lama.php");
    }
    exit();
}
