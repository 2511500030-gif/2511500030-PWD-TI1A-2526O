<?php
require 'koneksi.php';

// 1. Definisi Field agar tampilan rapi
$fieldBiodata = [
  "nim" => ["label" => "NIM", "suffix" => ""],
  "nama_lengkap" => ["label" => "Nama Lengkap", "suffix" => ""],
  "tempat_lahir" => ["label" => "Tempat Lahir", "suffix" => ""],
  "tanggal_lahir" => ["label" => "Tanggal Lahir", "suffix" => ""],
  "hobi" => ["label" => "Hobi", "suffix" => ""],
  "pasangan" => ["label" => "Pasangan", "suffix" => ""],
  "pekerjaan" => ["label" => "Pekerjaan", "suffix" => ""],
  "nama_ortu" => ["label" => "Nama Orang Tua", "suffix" => ""],
  "nama_kakak" => ["label" => "Nama Kakak", "suffix" => ""],
  "nama_adik" => ["label" => "Nama Adik", "suffix" => ""]
];

require 'koneksi.php';

$sql = "SELECT * FROM tbl_pengunjung ORDER BY id DESC";
$q = mysqli_query($conn, $sql);

if ($q && mysqli_num_rows($q) > 0) {
  while ($row = mysqli_fetch_assoc($q)) {
    echo "<div class='bio-box' style='border:1px solid #ddd; padding:10px; margin-bottom:10px;'>";
    echo "<strong>Nim:</strong> " . htmlspecialchars($row['nim']) . "<br>";
    echo "<strong>Nama Lengkap:</strong> " . htmlspecialchars($row['nama_lengkap']) . "<br>";
    echo "<strong>Tempat Lahir:</strong> " . htmlspecialchars($row['tempat_lahir']) . "<br>";
    echo "<strong>Tanggal Lahir:</strong> " . htmlspecialchars($row['tanggal_lahir']) . "<br>";
    echo "<strong>Hobi:</strong> " . htmlspecialchars($row['hobi']) . "<br>";
    echo "<strong>Pasangan:</strong> " . htmlspecialchars($row['pasangan']) . "<br>";
    echo "<strong>Pekerjaan:</strong> " . htmlspecialchars($row['pekerjaan']) . "<br>";
    echo "<strong>Nama Orang Tua:</strong> " . htmlspecialchars($row['nama_ortu']) . "<br>";
    echo "<strong>Nama Kakak:</strong> " . htmlspecialchars($row['nama_kakak']) . "<br>";
    echo "<strong>Nama Adik:</strong> " . htmlspecialchars($row['nama_adik']) . "<br>";
    echo "</div>";
  }
} else {
  echo "<p>Belum ada data pengunjung yang tersimpan.</p>"; // Ini yang muncul di fotomu
}
?>