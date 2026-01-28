<?php
  session_start();
  require __DIR__ . '/koneksi.php';
  require_once __DIR__ . '/fungsi.php';

  #cek method form, hanya izinkan POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error_bio'] = 'Akses tnimak valnim.';
    redirect_ke('read_bio.php');
  }

  #valnimasi nim wajib angka dan > 0
  $nim = filter_input(INPUT_POST, 'nim', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
  ]);

  if (!$nim) {
    $_SESSION['flash_error_bio'] = 'nim Tnimak Valnim.';
    redirect_ke('edit_bio.php?nim='. (int)$nim);
  }

  #ambil dan bersihkan (sanitasi) nilai dari form
  $nim = bersihkan ($_POST['txtNimEd'] ?? '');
  $nama_lengkap = bersihkan($_POST['txtNmLengkapEd']  ?? '');
  $tempat_lahir = bersihkan($_POST['txtT4LahirEd'] ?? '');   # ✅ typo diperbaiki
  $tanggal_lahir = bersihkan($_POST['txtTglLhrEd'] ?? '');   # ✅ konsisten huruf kecil
  $hobi = bersihkan($_POST['txtHobiEd'] ?? '');
  $pasangan = bersihkan($_POST['txtPasanganEd'] ?? '');
  $pekerjaan = bersihkan($_POST['txtkerjaEd'] ?? '');
  $nama_ortu = bersihkan($_POST['txtNmOrtuEd'] ?? '');
  $nama_kakak = bersihkan($_POST['txtNmKakakEd'] ?? '');
  $nama_adik = bersihkan($_POST['txtNmAdikEd'] ?? '');

  #Valnimasi sederhana
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

  if (mb_strlen($nim) > 30) {
    $errors[] = 'NIM maksimal 30 karakter.';
  }

  /*
  kondisi di bawah ini hanya dikerjakan jika ada error, 
  simpan nilai lama dan pesan error, lalu redirect (konsep PRG)
  */
  if (!empty($errors)) {
    $_SESSION['old_bio'] = [
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

    $_SESSION['flash_error_bio'] = implode('<br>', $errors);
    redirect_ke('edit_bio.php?nim='. (int)$nim);
  }

  /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE dengan prepared statement 
    (WAJIB WHERE nim = ?)
  */
  $stmt = mysqli_prepare($conn, "UPDATE tbl_pengunjung
                                SET nim = ?, nama_lengkap = ?, tempat_lahir = ?, tanggal_lahir = ?, hobi = ?, pasangan = ?, pekerjaan = ?, nama_ortu = ?, nama_kakak = ?, nama_adik = ? 
                                WHERE nim = ?");
  if (!$stmt) {
    #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
    $_SESSION['flash_error_bio'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('edit_bio.php?nim='. (int)$nim);
  }

  #bind parameter dan eksekusi (s = string, i = integer)
  mysqli_stmt_bind_param($stmt, "ssssssssssi", $nim, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $hobi, $pasangan, $pekerjaan, $nama_ortu, $nama_kakak, $nama_adik);

  if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
    unset($_SESSION['old_bio']);
    /*
      Redirect balik ke read.php dan tampilkan info sukses.
    */
    $_SESSION['flash_sukses_bio'] = 'Terima kasih, data Anda sudah diperbaharui.';
    redirect_ke('read_bio.php'); #pola PRG: kembali ke data dan exit()
  } else { #jika gagal, simpan kembali old value dan tampilkan error umum
    $_SESSION['old_bio'] = [
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
    $_SESSION['flash_error_bio'] = 'Data gagal diperbarui. Silakan coba lagi.';
    redirect_ke('edit_bio.php?nim='. (int)$nim);
  }
  #tutup statement
  mysqli_stmt_close($stmt);

  redirect_ke('edit_bio.php?nim='. (int)$nim);
?>