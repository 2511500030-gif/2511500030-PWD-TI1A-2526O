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
  $cid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
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
  if (!$id) {
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    redirect_ke('read_bio.php');
  }

  /*
    Ambil data lama dari DB menggunakan prepared statement, 
    jika ada kesalahan, tampilkan penanda error.
  */
  $stmt = mysqli_prepare($conn, "SELECT id, nim, nama lengkap, tempat_lahir, tanggal_lahir, hobi, pasangan, pekerjaan, nama_ortu, nama_kakak, nama_adik 
                                    FROM tbl_tamu WHERE id = ? LIMIT 1");
  if (!$stmt) {
    $_SESSION['flash_error'] = 'Query tidak benar.';
    redirect_ke('read.php');
  }

  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$row) {
    $_SESSION['flash_error'] = 'Record tidak ditemukan.';
    redirect_ke('read.php');
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
  $flash_error = $_SESSION['flash_error'] ?? '';
  $old = $_SESSION['old'] ?? [];
  unset($_SESSION['flash_error'], $_SESSION['old']);
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
        <form action="proses_update.php" method="POST">

          <input type="text" name="id" value="<?= (int)$id; ?>">

          <label for="txtNim"><span>Nim:</span>
            <input type="text" id="txtNim" name="txtNimEd" 
              placeholder="Masukkan nim" required autocomplete="name"
              value="<?= !empty($nim) ? $nim : '' ?>">
          </label>

          <label for="txtNmLengkap"><span>Nama Lengkap:</span>
            <input type="text" id="txtNmLengkap" name="txtNmLengkapEd" 
              placeholder="Masukkan nama lengkap" required autocomplete="name"
              value="<?= !empty($nama) ? $nama : '' ?>">

          <label for="txtTempatLahir"><span>Tempat Lahir:</span>
            <input type="text" id="txtTempatLahir" name="txtTempatLahirEd" 
              placeholder="Masukkan tempat lahir" required autocomplete="name"
              value="<?= !empty($tempat_lahir) ? $tempat_lahir : '' ?>">

          <label for="txtTanggalLahir"><span>Tanggal Lahir:</span>
            <input type="date" id="txtTanggalLahir" name="txtTanggalLahirEd" 
              placeholder="Masukkan tanggal lahir" required
              value="<?= !empty($tanggal_lahir) ? $tanggal_lahir : '' ?>">

          <label for="txtHobi"><span>Hobi:</span>
            <input type="text" id="txtHobi" name="txtHobiEd" 
              placeholder="Masukkan hobi" required autocomplete="name"
              value="<?= !empty($hobi) ? $hobi : '' ?>">

          <label for="txtPasangan"><span>Pasangan:</span>
            <input type="text" id="txtPasangan" name="txtPasanganEd" 
              placeholder="Masukkan pasangan" required autocomplete="name"
              value="<?= !empty($pasangan) ? $pasangan : '' ?>">

          <label for="txtPekerjaan"><span>Pekerjaan:</span>
            <input type="text" id="txtPekerjaan" name="txtPekerjaanEd" 
              placeholder="Masukkan pekerjaan" required autocomplete="name"
              value="<?= !empty($pekerjaan) ? $pekerjaan : '' ?>">

          <label for="txtNamaOrtu"><span>Nama Orang Tua:</span>
            <input type="text" id="txtNamaOrtu" name="txtNamaOrtuEd" 
              placeholder="Masukkan nama orang tua" required autocomplete=""
              value="<?= !empty($nama_ortu) ? $nama_ortu : '' ?>">

          <label for="txtNamaKakak"><span>Nama Kakak:</span>
            <input type="" id="" name="" 
              placeholder="" required autocomplete=""
              value="<?= !empty($nama_kakak) ? $nama_kakak : '' ?>">

          <label for=""><span>Nama Adik:</span>
            <input type="text" id="txtNamaAdik" name="txtNamaAdikEd" 
              placeholder="Masukkan nama adik" required autocomplete=""
              value="<?= !empty($nama_adik) ? $nama_adik : '' ?>">
          </label>


          <button type="submit">Kirim</button>
          <button type="reset">Batal</button>
          <a href="read_bio.php" class="reset">Kembali</a>
        </form>
      </section>
    </main>

    <script src="script.js"></script>
  </body>
</html>