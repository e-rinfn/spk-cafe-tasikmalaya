<?php
session_start();
require_once '../config/database.php';

$id_kafe = (int)$_GET['id'];

// Ambil nama foto untuk dihapus
$kafe = fetch_one(query("SELECT foto FROM kafe WHERE id_kafe = $id_kafe"));

// Hapus data kafe (akan cascade ke nilai_kafe)
$sql = "DELETE FROM kafe WHERE id_kafe = $id_kafe";

if (query($sql)) {
    // Hapus file foto jika ada
    if ($kafe['foto'] && file_exists("../uploads/" . $kafe['foto'])) {
        unlink("../uploads/" . $kafe['foto']);
    }
    $_SESSION['success'] = "Kafe berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus kafe!";
}

header("Location: kafe.php");
exit();
