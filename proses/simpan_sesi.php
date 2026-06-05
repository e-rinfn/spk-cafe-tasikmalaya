<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sesi = escape($_POST['nama_sesi']);
    $bobot = $_POST['bobot'];
    $created_by = $_SESSION['nama_lengkap'];
    $keterangan = escape($_POST['keterangan'] ?? '');

    // Validasi total bobot
    $total_bobot = array_sum($bobot);
    if ($total_bobot != 100) {
        $_SESSION['error'] = "Total bobot harus 100%! Saat ini: $total_bobot%";
        header("Location: ../user/sesi_baru.php");
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Simpan sesi
        $sql_sesi = "INSERT INTO bobot_sesi (nama_sesi, created_by, keterangan) 
                     VALUES ('$nama_sesi', '$created_by', '$keterangan')";
        query($sql_sesi);
        $id_sesi = mysqli_insert_id($conn);

        // Simpan detail bobot
        foreach ($bobot as $id_kriteria => $nilai_bobot) {
            $sql_bobot = "INSERT INTO detail_bobot_sesi (id_sesi, id_kriteria, bobot) 
                          VALUES ($id_sesi, $id_kriteria, $nilai_bobot)";
            query($sql_bobot);
        }

        mysqli_commit($conn);
        $_SESSION['success'] = "Sesi berhasil disimpan! Silakan lanjutkan ke perhitungan OCRA.";
        header("Location: ../user/hitung_ocra.php?id_sesi=$id_sesi");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Gagal menyimpan sesi: " . $e->getMessage();
        header("Location: ../user/sesi_baru.php");
    }
} else {
    header("Location: ../user/sesi_baru.php");
}
