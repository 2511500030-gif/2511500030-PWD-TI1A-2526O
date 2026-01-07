<?php
  session_start();
  require __DIR__ . '/koneksi.php';
  require_once __DIR__ . '/fungsi.php';

  #cek method form, hanya izinkan POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    redirect_ke('read_bio.php');
  }

  #validasi cid wajib angka dan > 0
  $cid = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
  ]);

  if (!$cid) {
    $_SESSION['flash_error'] = 'ID Tidak Valid.';
    redirect_ke('edit_bio.php?id='. (int)$id);
  }

  #ambil dan bersihkan (sanitasi) nilai dari form
  $nim  = bersihkan($_POST['txtNimEd']  ?? '');
  $nama_lengkap = bersihkan($_POST['txtNamaLengkapEd'] ?? '');
  $tempat_lahir = bersihkan($_POST['txtTempatLahirEd'] ?? '');
  $tanggal_lahir = bersihkan($_POST['txtTanggalLahirEd'] ?? '');
  $hobi = bersihkan($_POST['txtHobiEd'] ?? '');
  $pasangan = bersihkan($_POST['txtPasanganEd'] ?? '');
  $pekerjaan = bersihkan($_POST['txtPekerjaanEd'] ?? '');
  $nama_ortu = bersihkan($_POST['txtNamaOrtuEd'] ?? '');
  $nama_kakak = bersihkan($_POST['txtNamaKakakEd'] ?? '');
  $nama_adik = bersihkan($_POST['txtNamaAdikEd'] ?? '');

  #Validasi sederhana
  $errors = []; #ini array untuk menampung semua error yang ada

  if ($nim === '') {
    $errors[] = 'NIM wajib diisi.';
  }

  if ($nama_lengkap === '') {
    $errors[] = 'Nama Lengkap wajib diisi.';
  }

  if ($tempat_lahir === '') {
    $errors[] = 'Tempat Lahir wajib diisi.';
  }

  if ($tanggal_lahir === '') {
    $errors[] = 'Tanggal Lahir wajib diisi.';
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
    $errors[] = 'Nama Orang Tua wajib diisi.';
  }

  if ($nama_kakak === '') {
    $errors[] = 'Nama Kakak wajib diisi.';
  }

  if ($nama_adik === '') {
    $errors[] = 'Nama Adik wajib diisi.';
  }


  if (mb_strlen($nim) < 25) {
    $errors[] = 'NIM minimal 25 karakter.';
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
      'nama_adik' => $nama_adik
    ];

    $_SESSION['flash_error'] = implode('<br>', $errors);
    redirect_ke('edit_bio.php?id='. (int)$id);
  }

  /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE dengan prepared statement 
    (WAJIB WHERE cid = ?)
  */
  $stmt = mysqli_prepare($conn, "UPDATE tbl_tamu 
                                SET nim = ?, nama_lengkap = ?, tempat_lahir = ?, tanggal_lahir = ?, hobi = ?, pasangan = ?, pekerjaan = ?, nama_ortu = ?, nama_kakak = ?, nama_adik = ? 
                                WHERE id = ?");
  if (!$stmt) {
    #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('edit_bio.php?id='. (int)$id);
  }

  #bind parameter dan eksekusi (s = string, i = integer)
  mysqli_stmt_bind_param($stmt, "sssi", $nim, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $hobi, $pasangan, $pekerjaan, $nama_ortu, $nama_kakak, $nama_adik, $id);

  if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
    unset($_SESSION['old']);
    /*
      Redirect balik ke read.php dan tampilkan info sukses.
    */
    $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah diperbaharui.';
    redirect_ke('read.php'); #pola PRG: kembali ke data dan exit()
  } else { #jika gagal, simpan kembali old value dan tampilkan error umum
    $_SESSION['old'] = [
      'nama'  => $nama,
      'email' => $email,
      'pesan' => $pesan,
    ];
    $_SESSION['flash_error'] = 'Data gagal diperbaharui. Silakan coba lagi.';
    redirect_ke('edit_bio.php?id='. (int)$cid);
  }
  #tutup statement
  mysqli_stmt_close($stmt);

  redirect_ke('edit_bio.php?id='. (int)$cid);