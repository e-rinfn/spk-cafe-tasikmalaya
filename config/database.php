<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'spk_kafe_tasikmalaya';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8");

// Fungsi helper
function query($sql)
{
    global $conn;
    return mysqli_query($conn, $sql);
}

function fetch_all($result)
{
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetch_one($result)
{
    return mysqli_fetch_assoc($result);
}

function escape($string)
{
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}
