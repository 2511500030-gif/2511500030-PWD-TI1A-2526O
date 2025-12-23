<?php
session_start();
require __DIR__ . './koneksi.php';
require_once __DIR__ . './fungsi.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    redirect_ke('read.php');
}


$cid = filter_input(INPUT_POST, 'cid', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if (!$cid) {
    $_SESSION['flash_error'] = 'ID tidak valid.';
    redirect_ke('read.php');
}


$nama = bersihkan($_POST['txtnamaEd'] ?? '');
$email = bersihkan($_POST['txtemailEd'] ?? '');
$pesan = bersihkan($_POST['txtpesanEd'] ?? '');
$captcha = bersihkan($_POST['txtcaptchaEd'] ?? '');


$errors = [];

if (empty($nama)) {
    $errors[] = 'Nama wajib diisi.';
}

if (empty($email)) {
    $errors[] = 'Email wajib diisi.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}

if (empty($pesan)) {
    $errors[] = 'Pesan wajib diisi.';
}

if ($captcha === '') {
    $errors[] = 'pertanyaan wajib diisi.';
}

if (mb_strlen($nama) < 3) {
    $errors[] = 'Nama minimal 3 karakter.';
}

if (mb_strlen($pesan) < 10) {
    $errors[] = 'Pesan minimal 10 karakter.';
}

if ($captcha!== "6") {
    $errors[] = 'Jawaban '. $captcha . ' captcha salah.';
}





if (!empty($errors)) {
    $_SESSION['old'] = [
        'nama' => $nama,
        'email' => $email,
        'pesan' => $pesan,
    ];

    $_SESSION['flash_error'] = implode('<br>', $errors);
    redirect_ke("edit.php?cid='. (int)$cid");
}






$stmt = mysqli_prepare($conn, "UPDATE tbl_tamu 
                                SET cnama = ?, cemail = ?, cpesan = ? 
                                WHERE cid = ?");
if (!$stmt) {

    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('edit.php?cid=' . (int)$cid);
}


mysqli_stmt_bind_param($stmt, 'sssi', $nama, $email, $pesan, $cid);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['old']);



    $_SESSION['flash_sukses'] = 'Terima Kasih, data Anda sudah diperbarui.';
    redirect_ke('read.php');
} else {
    $_SESSION['old'] = [
        'nama' => $nama,
        'email' => $email,
        'pesan' => $pesan,
    ];
    $_SESSION['flash_error'] = 'Gagal memperbarui data tamu. Silakan coba lagi.';
    redirect_ke("edit.php?cid=' . (int)$cid");
}

mysqli_stmt_close($stmt);

redirect_ke('edit.php?cid=' . (int)$cid);