<?php
  session_start();
  require __DIR__ . './koneksi.php';
  require_once __DIR__ . '/fungsi.php';

  #validasi cid wajib angka dan > 0
  // $cid = filter_input(INPUT_POST, 'cid', FILTER_VALIDATE_INT, [
  //   'options' => ['min_range' => 1]
  // ]);

  // if (!$cid) {
  //   $_SESSION['flash_error'] = 'CID Tidak Valid.';
  //   redirect_ke('edit.php?cid='. (int)$cid);
  // }

  /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE dengan prepared statement 
    (WAJIB WHERE cid = ?)
  */
  $stmt = mysqli_prepare($conn, "DELETE FROM tbl_tamu"); 
                                #WHERE cid = ?");
  if (!$stmt) {
    #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('read.php');
  }

  #bind parameter dan eksekusi (s = string, i = integer)
  #mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $pesan, $cid);

  if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
    /*
      Redirect balik ke read.php dan tampilkan info sukses.
    */
    $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah dihapus.';
    redirect_ke('read.php'); #pola PRG: kembali ke data dan exit()
  } else {
    $_SESSION['flash_error'] = 'Data gagal dihapus. Silakan coba lagi.';
  }
  #tutup statement
  mysqli_stmt_close($stmt);

  redirect_ke('read.php');