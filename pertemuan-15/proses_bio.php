<?php
session_start();
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect_ke('index.php#biodata');
}

// Ambil data dan bersihkan
$nim           = bersihkan($_POST['txtNim'] ?? '');
$nama_lengkap  = bersihkan($_POST['txtNmLengkap'] ?? '');
$tempat_lahir  = bersihkan($_POST['txtT4Lhr'] ?? '');
$tanggal_lahir = bersihkan($_POST['txtTglLhr'] ?? ''); // Pastikan variabel ini sama
$hobi          = bersihkan($_POST['txtHobi'] ?? '');
$pasangan      = bersihkan($_POST['txtPasangan'] ?? '');
$pekerjaan     = bersihkan($_POST['txtkerja'] ?? '');
$nama_ortu     = bersihkan($_POST['txtNmOrtu'] ?? '');
$nama_kakak    = bersihkan($_POST['txtNmKakak'] ?? '');
$nama_adik     = bersihkan($_POST['txtNmAdik'] ?? '');

$errors = [];

// Validasi
if ($nim === '') $errors[] = 'NIM wajib diisi.';
if ($nama_lengkap === '') $errors[] = 'Nama lengkap wajib diisi.';
if (mb_strlen($nim) < 5) $errors[] = 'NIM terlalu pendek.'; // Minimal disesuaikan

if (!empty($errors)) {
  $_SESSION['old_bio'] = $_POST;
  $_SESSION['flash_error_bio'] = implode('<br>', $errors);
  redirect_ke('index.php#biodata');
}

// Query SQL
$sql = "INSERT INTO tbl_pengunjung
        (nim, nama_lengkap, tempat_lahir, tanggal_lahir, hobi, pasangan, pekerjaan, nama_ortu, nama_kakak, nama_adik) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  $_SESSION['flash_error_bio'] = 'Kesalahan database: ' . mysqli_error($conn); // Tampilkan error asli jika gagal
  redirect_ke('index.php#biodata');
}

mysqli_stmt_bind_param($stmt, "ssssssssss", $nim, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $hobi, $pasangan, $pekerjaan, $nama_ortu, $nama_kakak, $nama_adik);

if (mysqli_stmt_execute($stmt)) {
  unset($_SESSION['old_bio']);
  $_SESSION['flash_sukses_bio'] = 'Terima kasih, data sudah tersimpan.';
} else {
  $_SESSION['flash_error_bio'] = 'Gagal menyimpan data.';
}

mysqli_stmt_close($stmt);
redirect_ke('index.php#biodata');