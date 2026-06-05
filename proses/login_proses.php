<?php
session_start();
require_once '../config/database.php';

$username = escape($_POST['username']);
$password = md5($_POST['password']);

$sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
$result = query($sql);

if (mysqli_num_rows($result) == 1) {
    $user = fetch_one($result);

    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];

    // Update last login
    $update = "UPDATE user SET last_login = NOW() WHERE id_user = " . $user['id_user'];
    query($update);

    if ($user['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/index.php");
    }
} else {
    header("Location: ../login.php?error=1");
}
