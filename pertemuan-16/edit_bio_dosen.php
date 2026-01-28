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
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
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
    redirect_ke('read_bio_dos.php');
  }

  /*
    Ambil data lama dari DB menggunakan prepared statement, 
    jika ada kesalahan, tampilkan penanda error.
  */
  $stmt = mysqli_prepare($conn, "SELECT id, kodedos, nama, alamat, tanggal, jja, prodi, nohp, pasangan, anak, ilmu
                                    FROM pengunjung_biodata_dosen WHERE id = ? LIMIT 1");
  if (!$stmt) {
    $_SESSION['flash_error'] = 'Query tidak benar.';
    redirect_ke('read_bio_dos.php');
  }

  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$row) {
    $_SESSION['flash_error'] = 'Record tidak ditemukan.';
    redirect_ke('read_bio_dos.php');
  }

  #Nilai awal (prefill form)
  $kodedos = $row['kodedos'] ?? '';
  $nama = $row['nama'] ?? '';
  $alamat = $row['alamat'] ?? '';
  $tanggal = $row['tanggal'] ?? '';
  $jja = $row['jja'] ?? '';
  $prodi = $row['prodi'] ?? '';
  $nohp = $row['nohp'] ?? '';
  $pasangan = $row['pasangan'] ?? '';
  $anak = $row['anak'] ?? '';
  $ilmu = $row['ilmu'] ?? '';

  #Ambil error dan nilai old input kalau ada
  $flash_error = $_SESSION['flash_error'] ?? '';
  $old = $_SESSION['old'] ?? [];
  unset($_SESSION['flash_error'], $_SESSION['old']);
  if (!empty($old)) {
    $kodedos = $old['kodedos'] ?? $kodedos;
    $nama = $old['nama'] ?? $nama;
    $alamat = $old['alamat'] ?? $alamat;
    $tanggal = $old['tanggal'] ?? $tanggal;
    $jja = $old['jja'] ?? $jja;
    $prodi = $old['prodi'] ?? $prodi;
    $nohp = $old['nohp'] ?? $nohp;
    $pasangan = $old['pasangan'] ?? $pasangan;
    $anak = $old['anak'] ?? $anak;
    $ilmu = $old['ilmu'] ?? $ilmu;
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
      <section id="contact">
        <h2>Edit Buku Tamu</h2>
        <?php if (!empty($flash_error)): ?>
          <div style="padding:10px; margin-bottom:10px; 
            background:#f8d7da; color:#721c24; border-radius:6px;">
            <?= $flash_error; ?>
          </div>
        <?php endif; ?>
        <form action="proses_update_bio_dos.php" method="POST">

          <input type="text" name="id" value="<?= (int)$id; ?>">

          <label for="txtKodeDos"><span>Kode Dosen:</span>
            <input type="text" id="txtKodeDos" name="txtKodeDos" 
              placeholder="Masukkan kode dosen" required autocomplete="name"
              value="<?= !empty($kodedos) ? $kodedos : '' ?>">
          </label>

          <label for="txtNmDosen"><span>Nama Dosen:</span>
            <input type="text" id="txtNmDosen" name="txtNmDosen" 
              placeholder="Masukkan nama dosen" required autocomplete="name"
              value="<?= !empty($nama) ? $nama : '' ?>">
          </label>

          <label for="txtAlRmh"><span>Alamat:</span>
            <input type="text" id="txtAlRmh" name="txtAlRmh"
              placeholder="Masukkan alamat dosen" required
              value="<?= !empty($alamat) ? $alamat : '' ?>">
          </label>

          <label for="txtTglDosen"><span>Tanggal Jadi Dosen:</span>
            <input type="date" id="txtTglDosen" name="txtTglDosen"
              placeholder="" required
              value="<?= !empty($tanggal) ? $tanggal : '' ?>">
          </label>

          <label for="txtJJA"><span>JJA:</span>
            <input type="text" id="txtJJA" name="txtJJA"
              placeholder="" required
              value="<?= !empty($jja) ? $jja : '' ?>">
          </label>

          <label for="txtProdi"><span>Prodi:</span>
            <input type="text" id="txtProdi" name="txtProdi"
              placeholder="" required
              value="<?= !empty($prodi) ? $prodi : '' ?>">
          </label>

          <label for="txtNoHP"><span>No HP:</span>
            <input type="text" id= "txtNoHP" name= "txtNoHP"
              placeholder="" required
              value="<?= !empty($nohp) ? $nohp : '' ?>">
          </label>

          <label for= "txNamaPasangan"><span>Nama Pasangan:</span>
            <input type= "text" id= "txNamaPasangan" name= "txNamaPasangan"
              placeholder="" required
              value="<?= !empty($pasangan) ? $pasangan : '' ?>">
          </label>

          <label for= "txtNmAnak"><span>Nama Anak:</span>
            <input type= "text" id= "txtNmAnak" name= "txtNmAnak"
              placeholder="" required
              value="<?= !empty($anak) ? $anak : '' ?>">
          </label>

          <label for= "txtBidangIlmu"><span>Bidang Ilmu:</span>
            <input type= "text" id= "txtBidangIlmu" name= "txtBidangIlmu"
              placeholder="" required
              value="<?= !empty($ilmu) ? $ilmu : '' ?>">
          </label>
              placeholder="Masukkan nama" required autocomplete="name"
              value="<?= !empty($nama) ? $nama : '' ?>">
          </label>

          <label for="txtEmail"><span>Email:</span>
            <input type="email" id="txtEmail" name="txtEmailEd" 
              placeholder="Masukkan email" required autocomplete="email"
              value="<?= !empty($email) ? $email : '' ?>">
          </label>

          <label for="txtPesan"><span>Pesan Anda:</span>
            <textarea id="txtPesan" name="txtPesanEd" rows="4" 
              placeholder="Tulis pesan anda..." 
              required><?= !empty($pesan) ? $pesan : '' ?></textarea>
          </label>

          <label for="txtCaptcha"><span>Captcha 2 x 3 = ?</span>
            <input type="number" id="txtCaptcha" name="txtCaptcha" 
              placeholder="Jawab Pertanyaan..." required>
          </label>

          <button type="submit">Kirim</button>
          <button type="reset">Batal</button>
          <a href="read_bio_dos.php" class="reset">Kembali</a>
        </form>
      </section>
    </main>

    <script src="script.js"></script>
  </body>
</html>