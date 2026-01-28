<?php
  session_start();
  require 'koneksi.php';
  require 'fungsi.php';

  /*
    Ambil nilai cid dari GET dan lakukan validasi untuk 
    mengecek cid harus angka dan lebih besar dari 0 (> 0).
    'options' => ['min_range' => 1] artinya cid harus ≥ 1 
    (bukan 0, bahkan bukan negatif, bukan huruf, bukan HTML).
  */
  $nim = filter_input(INPUT_GET, 'nim', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
  ]);
  /*
    Skrip di atas cara penulisan lamanya adalah:
    $cid = $_GET['cid'] ?? '';
    $cid = (int)$cid;

    Cara lama seperti di atas akan mengambil data mentah 
    kemudian validasi dilakukan secara terpisah, sehingga 
    rawan lupa validasi. Untuk input dari GET atau POST, 
    filter_input() lebih disarankan daripada $_GET atau $_POST.
  */

  /*
    Cek apakah $cid bernilai valid:
    Kalau $cid tidak valid, maka jangan lanjutkan proses, 
    kembalikan pengguna ke halaman awal (read.php) sembari 
    mengirim penanda error.
  */
  if (!$nim) {
    $_SESSION['flash_error_bio'] = 'Akses tidak valid.';
    redirect_ke('read_bio.php');
  }

  /*
    Ambil data lama dari DB menggunakan prepared statement, 
    jika ada kesalahan, tampilkan penanda error.
  */
  $stmt = mysqli_prepare($conn, "SELECT nim, nama_lengkap, tempat_lahir, tanggal_lahir, hobi, pasangan, pekerjaan, nama_ortu, nama_kakak, nama_adik 
                                    FROM tbl_pengunjung WHERE nim = ? LIMIT 1");
  if (!$stmt) {
    $_SESSION['flash_error_bio'] = 'Query tidak benar.';
    redirect_ke('read_bio.php');
  }

  mysqli_stmt_bind_param($stmt, "i", $nim);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$row) {
    $_SESSION['flash_error_bio'] = 'Record tidak ditemukan.';
    redirect_ke('read_bio.php');
  }

  #Nilai awal (prefill form)
  $nim  = $row['nim'] ?? '';
  $nama = $row['nama lengkap'] ?? '';
  $tempat_lahir = $row['tempat_lahir'] ?? '';
  $tanggal_lahir = $row['tanggal_lahir'] ?? '';
  $hobi = $row['hobi'] ?? '';
  $pasangan = $row['pasangan'] ?? '';
  $pekerjaan = $row['pekerjaan'] ?? '';
  $nama_ortu = $row['nama_ortu'] ?? '';
  $nama_kakak = $row['nama_kakak'] ?? '';
  $nama_adik = $row['nama_adik'] ?? '';

  #Ambil error dan nilai old input kalau ada
  $flash_error = $_SESSION['flash_error_bio'] ?? '';
  $old = $_SESSION['old_bio'] ?? [];
  unset($_SESSION['flash_error_bio'], $_SESSION['old_bio']);
  if (!empty($old)) {
    $nim  = $old['nim'] ?? $nim;
    $nama = $old['nama'] ?? $nama;
    $tempat_lahir = $old['tempat_lahir'] ?? $tempat_lahir;
    $tanggal_lahir = $old['tanggal_lahir'] ?? $tanggal_lahir;
    $hobi = $old['hobi'] ?? $hobi;
    $pasangan = $old['pasangan'] ?? $pasangan;
    $pekerjaan = $old['pekerjaan'] ?? $pekerjaan;
    $nama_ortu = $old['nama_ortu'] ?? $nama_ortu;
    $nama_kakak = $old['nama_kakak'] ?? $nama_kakak;
    $nama_adik = $old['nama_adik'] ?? $nama_adik;
  }
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judul Halaman</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <header>
      <h1>Ini Header</h1>
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
        &#9776;
      </button>
      <nav>
        <ul>
          <li><a href="#home">Beranda</a></li>
          <li><a href="#about">Tentang</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
      </nav>
    </header>

    <main>
      <section id="about">
        <h2>Edit Buku Pengunjung</h2>
        <?php if (!empty($flash_error)): ?>
          <div style="padding:10px; margin-bottom:10px; 
            background:#f8d7da; color:#721c24; border-radius:6px;">
            <?= $flash_error; ?>
          </div>
        <?php endif; ?>
      <form action="proses_update_bio.php" method="POST" class="edit-form">
    <input type="hidden" name="nim" value="<?= (int)$nim; ?>">

    <div class="form-group">
        <label for="txtNim">NIM:</label>
        <input type="text" id="txtNim" name="txtNimEd" required value="<?= htmlspecialchars($nim) ?>">
    </div>

    <div class="form-group">
        <label for="txtNmLengkap">Nama Lengkap:</label>
        <input type="text" id="txtNmLengkap" name="txtNmLengkapEd" required value="<?= htmlspecialchars($nama) ?>">
    </div>

    <div class="form-group">
        <label for="txtT4Lhr">Tempat Lahir:</label>
        <input type="text" id="txtT4Lhr" name="txtT4LhrEd" required value="<?= htmlspecialchars($tempat_lahir) ?>">
    </div>

    <div class="form-group">
        <label for="txtTglLhr">Tanggal Lahir:</label>
        <input type="date" id="txtTglLhr" name="txtTglLhrEd" required value="<?= htmlspecialchars($tanggal_lahir) ?>">
    </div>

    <div class="form-group">
        <label for="txtHobi">Hobi:</label>
        <input type="text" id="txtHobi" name="txtHobiEd" required value="<?= htmlspecialchars($hobi) ?>">
    </div>

    <div class="form-group">
        <label for="txtPasangan">Pasangan:</label>
        <input type="text" id="txtPasangan" name="txtPasanganEd" required value="<?= htmlspecialchars($pasangan) ?>">
    </div>

    <div class="form-group">
        <label for="txtkerja">Pekerjaan:</label>
        <input type="text" id="txtkerja" name="txtkerjaEd" required value="<?= htmlspecialchars($pekerjaan) ?>">
    </div>

    <div class="form-group">
        <label for="txtNmOrtu">Nama Orang Tua:</label>
        <input type="text" id="txtNmOrtu" name="txtNmOrtuEd" required value="<?= htmlspecialchars($nama_ortu) ?>">
    </div>

    <div class="form-group">
        <label for="txtNmKakak">Nama Kakak:</label>
        <input type="text" id="txtNmKakak" name="txtNmKakakEd" required value="<?= htmlspecialchars($nama_kakak) ?>">
    </div>

    <div class="form-group">
        <label for="txtNmAdik">Nama Adik:</label>
        <input type="text" id="txtNmAdik" name="txtNmAdikEd" required value="<?= htmlspecialchars($nama_adik) ?>">
    </div>

    <div class="button-group">
        <button type="submit" class="btn-submit">Simpan Perubahan</button>
        <button type="reset" class="btn-reset">Batal</button>
        <a href="read_bio.php" class="btn-back">Kembali</a>
    </div>
</form>
      </section>
    </main>

    <script src="script.js"></script>
  </body>
</html>