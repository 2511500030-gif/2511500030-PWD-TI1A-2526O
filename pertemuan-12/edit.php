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

    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kontak</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation">☰</button>
    <nav>
        <a href="home">Beranda</a>
        <a href="about">Tentang</a>
        <a href="contact">Kontak</a>
    </nav>
</header>

<section id="contact">
    <h2>Kontak Kami</h2>

    <?php
    if (isset($_SESSION['flash_error'])) {
        echo '<div class="flash error">' . $_SESSION['flash_error'] . '</div>';
        unset($_SESSION['flash_error']);
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
    <?php if (!empty($flash_error)) : ?>
        <div style="padding:10px; margin-bottom:10px;
        background:#f8d7da; color:#721c24; border-radius:6px;">
            <?= $flash_error; ?>
        </div>
    <?php endif; ?>

    <form action="proses_update.php" method="POST">

        <input type="text" name="cid" value="<?= (int)$cid; ?>">

        <label for="txtNama"><span>Nama:</span>
            <input type="text" id="txtNama" name="txtNamaEd"
            placeholder="Masukkan nama" required autocomplete="name"
            value="<?= !empty($nama) ? $nama : ''; ?>">
        </label>

        <label for="txtEmail"><span>Email:</span>
            <input type="email" id="txtEmail" name="txtEmailEd"
            placeholder="Masukkan email" required autocomplete="email"
            value="<?= !empty($email) ? $email : ''; ?>">
        </label>

        <label for="txtPesan"><span>Pesan Anda:</span>
            <textarea id="txtPesan" name="txtPesanEd" rows="4"
            placeholder="Tulis pesan anda..." required><?= !empty($pesan) ? $pesan : ''; ?></textarea>
        </label>

        <label for="txtCaptcha"><span>Captcha 3 x 4 = ?</span>
            <input type="number" id="txtCaptcha" name="txtCaptcha"
            placeholder="Jawab Pertanyaan..." required>
        </label>

        <button type="submit">Kirim</button>
        <button type="reset">Batal</button>
        <a href="read.php" class="reset">Kembali</a>
    </form>
</section>
</main>

<script src="script.js"></script>
</body>
</html>