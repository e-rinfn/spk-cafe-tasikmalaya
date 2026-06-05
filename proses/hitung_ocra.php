<?php
session_start();
require_once '../config/database.php';

function normalisasiOcra($data, $jenis)
{
    $min_val = min($data);
    $max_val = max($data);
    $hasil = [];
    foreach ($data as $nilai) {
        if ($max_val == $min_val) {
            $hasil[] = 0;
        } else {
            if ($jenis == "benefit") {
                $hasil[] = ($nilai - $min_val) / ($max_val - $min_val);
            } else {
                $hasil[] = ($max_val - $nilai) / ($max_val - $min_val);
            }
        }
    }
    return $hasil;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sesi = escape($_POST['nama_sesi']);
    $bobot = $_POST['bobot'];
    $tahun = $_POST['tahun'] ?? date('Y');
    $created_by = $_SESSION['nama_lengkap'];

    // Mulai transaksi
    mysqli_begin_transaction($GLOBALS['conn']);

    try {
        // 1. Simpan sesi
        $sql_sesi = "INSERT INTO bobot_sesi (nama_sesi, created_by, keterangan) VALUES ('$nama_sesi', '$created_by', 'Perhitungan OCRA $tahun')";
        query($sql_sesi);
        $id_sesi = mysqli_insert_id($GLOBALS['conn']);

        // 2. Simpan bobot
        foreach ($bobot as $id_kriteria => $nilai_bobot) {
            $sql_bobot = "INSERT INTO detail_bobot_sesi (id_sesi, id_kriteria, bobot) VALUES ($id_sesi, $id_kriteria, $nilai_bobot)";
            query($sql_bobot);
        }

        // 3. Ambil data kafe dan nilai
        $sql_kafe = "SELECT k.id_kafe, k.nama_kafe FROM kafe k WHERE k.status = 'aktif'";
        $kafe_list = query($sql_kafe);

        $sql_kriteria = "SELECT k.id_kriteria, k.kode_kriteria, k.nama_kriteria, k.jenis_kriteria, 
                                COALESCE(d.bobot, k.bobot_default) as bobot
                         FROM kriteria k
                         LEFT JOIN detail_bobot_sesi d ON d.id_kriteria = k.id_kriteria AND d.id_sesi = $id_sesi
                         WHERE k.is_active = 1";
        $kriteria_list = query($sql_kriteria);

        // Siapkan data untuk perhitungan
        $kafe_data = [];
        $kriteria_info = [];
        $nilai_matrix = [];

        while ($kafe = fetch_one($kafe_list)) {
            $kafe_data[$kafe['id_kafe']] = $kafe['nama_kafe'];
        }

        $idx_kriteria = 0;
        while ($krit = fetch_one($kriteria_list)) {
            $kriteria_info[$idx_kriteria] = [
                'id' => $krit['id_kriteria'],
                'nama' => $krit['nama_kriteria'],
                'jenis' => $krit['jenis_kriteria'],
                'bobot' => $krit['bobot'] / 100
            ];

            // Ambil nilai untuk setiap kafe
            foreach ($kafe_data as $id_kafe => $nama) {
                $sql_nilai = "SELECT nilai FROM nilai_kafe WHERE id_kafe = $id_kafe AND id_kriteria = {$krit['id_kriteria']} AND tahun_penilaian = $tahun";
                $nilai_res = query($sql_nilai);
                $nilai_row = fetch_one($nilai_res);
                $nilai_matrix[$id_kafe][$idx_kriteria] = $nilai_row ? $nilai_row['nilai'] : 0;
            }
            $idx_kriteria++;
        }

        $jumlah_kafe = count($kafe_data);
        $jumlah_kriteria = count($kriteria_info);

        // Normalisasi OCRA untuk setiap kriteria
        $normal_matrix = [];
        for ($j = 0; $j < $jumlah_kriteria; $j++) {
            $kolom = [];
            foreach ($kafe_data as $id_kafe => $nama) {
                $kolom[] = $nilai_matrix[$id_kafe][$j];
            }
            $normal = normalisasiOcra($kolom, $kriteria_info[$j]['jenis']);
            $idx = 0;
            foreach ($kafe_data as $id_kafe => $nama) {
                $normal_matrix[$id_kafe][$j] = $normal[$idx++];
            }
        }

        // Hitung skor OCRA
        $skor_ocra = [];
        foreach ($kafe_data as $id_kafe => $nama) {
            $total = 0;
            for ($j = 0; $j < $jumlah_kriteria; $j++) {
                $total += $normal_matrix[$id_kafe][$j] * $kriteria_info[$j]['bobot'];
            }
            $skor_ocra[$id_kafe] = $total;
        }

        // Normalisasi akhir
        $min_skor = min($skor_ocra);
        $max_skor = max($skor_ocra);
        $skor_normal = [];
        foreach ($skor_ocra as $id_kafe => $skor) {
            if ($max_skor == $min_skor) {
                $skor_normal[$id_kafe] = 1;
            } else {
                $skor_normal[$id_kafe] = ($skor - $min_skor) / ($max_skor - $min_skor);
            }
        }

        // Urutkan untuk peringkat
        arsort($skor_normal);
        $peringkat = 1;
        foreach ($skor_normal as $id_kafe => $skor) {
            $sql_hasil = "INSERT INTO hasil_ocra (id_sesi, id_kafe, skor_mentah, skor_normal, peringkat) 
                          VALUES ($id_sesi, $id_kafe, {$skor_ocra[$id_kafe]}, $skor, $peringkat)";
            query($sql_hasil);
            $peringkat++;
        }

        mysqli_commit($GLOBALS['conn']);

        $_SESSION['success'] = "Perhitungan OCRA berhasil!";
        header("Location: ../user/hasil_detail.php?id=$id_sesi");
    } catch (Exception $e) {
        mysqli_rollback($GLOBALS['conn']);
        $_SESSION['error'] = "Gagal: " . $e->getMessage();
        header("Location: ../user/sesi_baru.php");
    }
}
