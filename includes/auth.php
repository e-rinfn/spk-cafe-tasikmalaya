<?php

/**
 * File autentikasi untuk memeriksa akses pengguna
 * Digunakan di halaman yang memerlukan login
 */

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role untuk akses admin
function checkAdmin()
{
    if ($_SESSION['role'] != 'admin') {
        header("Location: ../user/index.php");
        exit();
    }
}

// Cek role untuk akses user
function checkUser()
{
    if ($_SESSION['role'] != 'user') {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}

// Ambil informasi user saat ini
function getCurrentUser()
{
    global $conn;
    $user_id = $_SESSION['user_id'];
    $result = query("SELECT * FROM user WHERE id_user = $user_id");
    return fetch_one($result);
}

// Log aktivitas user
function logActivity($aktivitas)
{
    global $conn;
    $user_id = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    query("INSERT INTO log_aktivitas (id_user, aktivitas, ip_address) VALUES ($user_id, '$aktivitas', '$ip')");
}
