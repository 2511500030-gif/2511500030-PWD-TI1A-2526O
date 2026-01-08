<?php
require 'koneksi.php';

//nambahin field biodata buat data biodata
$fieldBiodata = [
  "nim" => ["label" => "NIM:", "suffix" => ""],
  "nama_lengkap" => ["label" => "Nama Lengkap:", "suffix" => ""],
  "tempat_lahir" => ["label" => "Tempat Lahir:", "suffix" => ""],
  "tanggal_lahir" => ["label" => "Tanggal Lahir:", "suffix" => ""],
  "hobi" => ["label" => "Hobi:", "suffix" => ""],
  "pasangan" => ["label" => "Pasangan:", "suffix" => ""],
  "pekerjaan" => ["label" => "Pekerjaan:", "suffix" => ""],
  "nama_ortu" => ["label" => "Nama Orang Tua:", "suffix" => ""],
  "nama_kakak" => ["label" => "Nama Kakak:", "suffix" => ""],
  "nama_adik" => ["label" => "Nama Adik:", "suffix" => ""]
];

$sql = "SELECT * FROM tbl_pengunjung_biodata_mahasiswa ORDER BY id DESC";
$q = mysqli_query($conn, $sql);
if (!$q) {
  echo "<p>Gagal membaca data pengunjung: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
} elseif (mysqli_num_rows($q) === 0) {
  echo "<p>Belum ada data pengunjung yang tersimpan.</p>";
} else {
  while ($row = mysqli_fetch_assoc($q)) {
    $arrBiodata = [
      "nim" => $row["nim"] ?? "",
      "nama_lengkap" => $row["nama_lengkap"] ?? "",
      "tempat_lahir" => $row["tempat_lahir"] ?? "",
      "tanggal_lahir" => $row["tanggal_lahir"] ?? "",
      "hobi" => $row["hobi"] ?? "",
      "pasangan" => $row["pasangan"] ?? "",
      "pekerjaan" => $row["pekerjaan"] ?? "",
      "nama_ortu" => $row["nama_ortu"] ?? "",
      "nama_kakak" => $row["nama_kakak"] ?? "",
      "nama_adik" => $row["nama_adik"] ?? ""
    ];
    echo tampilkanBiodata($fieldBiodata, $arrBiodata);
  }
}
?>