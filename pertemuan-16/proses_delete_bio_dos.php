<?php
  session_start();
  require __DIR__ . '/koneksi.php';
  require_once __DIR__ . '/fungsi.php';

  #validasi id wajib angka dan > 0
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
  ]);

  if (!$id) {
    $_SESSION['flash_error'] = 'ID Tidak Valid.';
    redirect_ke('read_bio_dos.php');
  }

  /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE dengan prepared statement 
    (WAJIB WHERE cid = ?)
  */
  $stmt = mysqli_prepare($conn, "DELETE FROM pengunjung_biodata_dosen
                                WHERE id = ?");
  if (!$stmt) {
    #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('read_bio_dos.php');
  }

  #bind parameter dan eksekusi (s = string, i = integer)
  mysqli_stmt_bind_param($stmt, "i", $id);

  if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
    /*
      Redirect balik ke read.php dan tampilkan info sukses.
    */
    $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah dihapus.';
  } else { #jika gagal, simpan kembali old value dan tampilkan error umum
    $_SESSION['flash_error'] = 'Data gagal dihapus. Silakan coba lagi.';
  }
  #tutup statement
  mysqli_stmt_close($stmt);

  redirect_ke('read_bio_dos.php');