<?php
session_start();
require __DIR__ . './koneksi.php';
require_once __DIR__ . '/fungsi.php';

#cek method form, hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $_SESSION['flash_error'] = 'Akses tidak valid.';
  redirect_ke('index.php#about');
}

$nim = bersihkan ($_POST['txtNIM'] ?? '');
$nama_lengkap = bersihkan($_POST['txtNmLengkap']  ?? '');
$tempat_lahir = bersihkan($_POST['txtT4Lhr'] ?? '');
$tanggal_Lahir = bersihkan($_POST['txtTglLhr'] ?? '');
$hobi = bersihkan($_POST['txtHobi'] ?? '');
$pasangan = bersihkan($_POST['txtPasangan'] ?? '');
$pekerjaan = bersihkan($_POST['txtPekerjaan'] ?? '');
$nama_ortu = bersihkan($_POST['txtNmOrtu'] ?? '');
$nama_kakak = bersihkan($_POST['txtNmKakak'] ?? '');
$nama_adik = bersihkan($_POST['txtNmAdik'] ?? '');

$errors = []; #ini array untuk menampung semua error yang ada

if ($nim === '') {
  $errors[] = 'NIM wajib diisi.';
}

if ($nama_lengkap === '') {
  $errors[] = 'Nama lengkap wajib diisi.';
}

if ($tempat_lahir === '') {
  $errors[] = 'Tempat lahir wajib diisi.';
}

if ($tanggal_lahir === '') {
  $errors[] = 'Tanggal lahir wajib diisi.';
}

if ($hobi === '') {
  $errors[] = 'Hobi wajib diisi.';
}

if ($pasangan === '') {
  $errors[] = 'Pasangan wajib diisi.';
}

if ($pekerjaan === '') {
  $errors[] = 'Pekerjaan wajib diisi.';
}

if ($nama_ortu === '') {
  $errors[] = 'Nama orang tua wajib diisi.';
}

if ($nama_kakak === '') {
  $errors[] = 'Nama kakak wajib diisi.';
}

if ($nama_adik === '') {
  $errors[] = 'Nama adik wajib diisi.';
}

if ($pesan === '') {
  $errors[] = 'Pesan wajib diisi.';
}

if (mb_strlen($nim) < 3) {
  $errors[] = 'Nama minimal 25 karakter.';
}


/*
kondisi di bawah ini hanya dikerjakan jika ada error, 
simpan nilai lama dan pesan error, lalu redirect (konsep PRG)
*/
if (!empty($errors)) {
  $_SESSION['old'] = [
    'nim'  => $nim,
    'nama_lengkap' => $nama_lengkap,
    'tempat_lahir' => $tempat_lahir,
    'tanggal_lahir' => $tanggal_lahir,
    'hobi' => $hobi,
    'pasangan' => $pasangan,
    'pekerjaan' => $pekerjaan,
    'nama_ortu' => $nama_ortu,
    'nama_kakak' => $nama_kakak,
    'nama_adik' => $nama_adik,
  ];

  $_SESSION['flash_error'] = implode('<br>', $errors);
  redirect_ke('index.php#about');
}

#menyiapkan query INSERT dengan prepared statement
$sql = "INSERT INTO tbl_tamu (cnim, cnama_lengkap, ctempat_lahir, ctanggal_lahir, chobi, cpasangan, cpekerjaan, cnama_ortu, cnama_kakak, cnama_adik) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  #jika gagal prepare, kirim pesan error ke pengguna (tanpa detail sensitif)
  $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
  redirect_ke('index.php#about');
}
#bind parameter dan eksekusi (s = string)
mysqli_stmt_bind_param($stmt, "ssssssssss", $nim, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $hobi, $pasangan, $pekerjaan, $nama_ortu, $nama_kakak, $nama_adik);

if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value, beri pesan sukses
  unset($_SESSION['old']);
  $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah tersimpan.';
  redirect_ke('index.php#about'); #pola PRG: kembali ke form / halaman home
} else { #jika gagal, simpan kembali old value dan tampilkan error umum
  $_SESSION['old'] = [
    'nim'  => $nim,
    'nama_lengkap' => $nama_lengkap,
    'tempat_lahir' => $tempat_lahir,
    'tanggal_lahir' => $tanggal_lahir,
    'hobi' => $hobi,
    'pasangan' => $pasangan,
    'pekerjaan' => $pekerjaan,
    'nama_ortu' => $nama_ortu,
    'nama_kakak' => $nama_kakak,
    'nama_adik' => $nama_adik,
  ];
  $_SESSION['flash_error'] = 'Data gagal disimpan. Silakan coba lagi.';
  redirect_ke('index.php#about');
}