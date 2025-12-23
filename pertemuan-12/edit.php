<?php
    session_start();
    require 'koneksi.php';
    require 'fungsi.php';

    $cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT, [
      'options'=> ['min_range' => 1]
      ]);

      if (!$cid) {
        $_SESSION['flash_error'] = 'Akses tidak valid.';
        redirect_ke('read.php');
      }

      $stmt = mysqli_prepare($conn, "SELECT cid , cnama, cemail, cpesan
                                                      FROM tbl_tamu WHERE cid = ? LIMIT 1");
    if (!$stmt) {
      $_SESSION['flash_error'] = 'Query tidak benar.';
      redirect_ke('read.php');
    }

    mysqli_stmt_bind_param($stmt, "i", $cid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$row) {
      $_SESSION['flash_error'] = 'Record tidak ditemukan.';
      redirect_ke('read.php');
    }

    $nama = $row['cnama'] ?? '';
    $email = $row['cemail'] ?? '';
    $pesan = $row['cpesan'] ?? '';

    $flash_error = $_SESSION['flash_error'] ?? '';
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['flash_error'], $_SESSION['old']);
    if (!empty($old)) {
      $nama = $old['nama'] ?? $nama;
      $email = $old['email'] ?? $email;
      $pesan = $old['pesan'] ?? $pesan;
    }
    ?>