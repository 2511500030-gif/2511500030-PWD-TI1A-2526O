<?php
  session_start();
  require __DIR__ . '/koneksi.php';
  require_once __DIR__ . '/fungsi.php';

  #cek method form, hanya izinkan POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    redirect_ke('read_bio_dos.php');
  }

  #validasi id wajib angka dan > 0
  $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
  ]);

  if (!$id) {
    $_SESSION['flash_error'] = 'ID Tidak Valid.';
    redirect_ke('edit_bio_dosen.php?id='. (int)$id);
  }

  #ambil dan bersihkan (sanitasi) nilai dari form
  $kodedos  = bersihkan($_POST['txtKodeDos']  ?? '');
  $nama = bersihkan($_POST['txtNmDosen'] ?? '');
  $alamat = bersihkan($_POST['txtAlRmh'] ?? '');
  $tanggal = bersihkan($_POST['txtTglDosen'] ?? '');
  $jja = bersihkan($_POST['txtJJA'] ?? '');
  $prodi = bersihkan($_POST['txtProdi'] ?? '');
  $nohp = bersihkan($_POST['txtNoHP'] ?? '');
  $pasangan = bersihkan($_POST['txNamaPasangan'] ?? '');
  $anak = bersihkan($_POST['txtNmAnak'] ?? '');
  $ilmu = bersihkan($_POST['txtBidangIlmu'] ?? '');

  #Validasi sederhana
  $errors = []; #ini array untuk menampung semua error yang ada

  if ($kodedos === '') {
    $errors[] = 'Kode Dosen wajib diisi.';
  }

  if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
  }

  if ($alamat === '') {
    $errors[] = 'alamat wajib diisi.';
  }

  if ($tanggal_jadi_dossen === '') {
    $errors[] = 'Tanggal Jadi Dosen wajib diisi.';
  }

  if ($jja === '') {
    $errors[] = 'JJA   wajib diisi.';
  }

  if ($prodi === '') {
    $errors[] = 'Prodi wajib diisi.';
  }

  if ($nohp === '') {
    $errors[] = 'No HP wajib diisi.';
  }

  if ($pasangan === '') {
    $errors[] = 'Nama Pasangan wajib diisi.';
  }

  if ($anak === '') {
    $errors[] = 'Nama Anak wajib diisi.';
  }

  if ($ilmu === '') {
    $errors[] = 'Bidang Ilmu wajib diisi.';
  }

  if (mb_strlen($nama) < 3) {
    $errors[] = 'Nama minimal 3 karakter.';
  }

  if (mb_strlen($alamat) < 10) {
    $errors[] = 'Alamat minimal 10 karakter.';
  }

  /*
  kondisi di bawah ini hanya dikerjakan jika ada error, 
  simpan nilai lama dan pesan error, lalu redirect (konsep PRG)
  */
  if (!empty($errors)) {
    $_SESSION['old'] = [
      'kode'  => $kodedos,
      'nama'  => $nama,
      'alamat' => $alamat,
      'tanggal' => $tanggal,
      'jja' => $jja,
      'prodi' => $prodi,
      'nohp' => $nohp,
      'pasangan' => $pasangan,
      'anak' => $anak,
      'ilmu' => $ilmu
    ];

    $_SESSION['flash_error'] = implode('<br>', $errors);
    redirect_ke('edit_bio_dos.php?id='. (int)$id);
  }

  /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE dengan prepared statement 
    (WAJIB WHERE cid = ?)
  */
  $stmt = mysqli_prepare($conn, "UPDATE pengunjung_biodata_dosen
                                SET kode_dosen = ?, nama_dosen = ?, alamat = ?, tanggal_jadi_dosen = ?, jja = ?, prodi = ?, no_hp = ?, nama_pasangan = ?, nama_anak = ?, bidang_ilmu = ? 
                                WHERE id = ?");
  if (!$stmt) {
    #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('edit_bio_dos.php?id='. (int)$id);
  }

  #bind parameter dan eksekusi (s = string, i = integer)
  mysqli_stmt_bind_param($stmt, "ssssssssssi", $kodedos, $nama, $alamat, $tanggal, $jja, $prodi, $nohp, $pasangan, $anak, $ilmu, $id);
  if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
    unset($_SESSION['old']);
    /*
      Redirect balik ke read.php dan tampilkan info sukses.
    */
    $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah diperbaharui.';
    redirect_ke('read.php'); #pola PRG: kembali ke data dan exit()
  } else { #jika gagal, simpan kembali old value dan tampilkan error umum
    $_SESSION['old'] = [
      'kode'  => $kodedos,
      'nama'  => $nama,
      'alamat' => $alamat,
      'tanggal' => $tanggal,
      'jja' => $jja,
      'prodi' => $prodi,
      'nohp' => $nohp,
      'pasangan' => $pasangan,
      'anak' => $anak,
      'ilmu' => $ilmu
    ];
    $_SESSION['flash_error'] = 'Data gagal diperbaharui. Silakan coba lagi.';
    redirect_ke('edit_bio_dos.php?id='. (int)$id);
  }
  #tutup statement
  mysqli_stmt_close($stmt);

  redirect_ke('edit_bio_dos.php?id='. (int)$id);