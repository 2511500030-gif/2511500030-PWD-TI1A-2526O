<?php
require 'koneksi.php';

$fieldContact = [
  "kodedos" => ["label" => "Kode Dosen:", "suffix" => ""],
  "nama" => ["label" => "Nama Dosen:", "suffix" => ""],
  "alamat" => ["label" => "Alamat:", "suffix" => ""],
  "tanggal_jadi_dosen" => ["label" => "Tanggal Jadi Dosen:", "suffix" => ""],
  "jja" => ["label" => "JJA:", "suffix" => ""],
  "prodi" => ["label" => "Prodi:", "suffix" => ""],
  "no_hp" => ["label" => "No HP:", "suffix" => ""],
  "nama_pasangan" => ["label" => "Nama Pasangan:", "suffix" => ""],
  "nama_anak" => ["label" => "Nama Anak:", "suffix" => ""],
  "bidang_ilmu" => ["label" => "Bidang Ilmu:", "suffix" => ""]
];

$sql = "SELECT * FROM pengunjung_biodata_dosen ORDER BY id DESC";
$q = mysqli_query($conn, $sql);
if (!$q) {
  echo "<p>Gagal membaca data dosen: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
} elseif (mysqli_num_rows($q) === 0) {
  echo "<p>Belum ada data dosen yang tersimpan.</p>";
} else {
  while ($row = mysqli_fetch_assoc($q)) {
    $arrContact = [
      "kodedos" => $row["kode_dosen"] ?? "",
      "nama" => $row["nama_dosen"] ?? "",
      "alamat" => $row["alamat"] ?? "",
      "tanggal_jadi_dosen" => $row["tanggal_jadi_dosen"] ?? "",
      "jja" => $row["jja"] ?? "",
      "prodi" => $row["prodi"] ?? "",
      "no_hp" => $row["no_hp"] ?? "",
      "nama_pasangan" => $row["nama_pasangan"] ?? "",
      "nama_anak" => $row["nama_anak"] ?? "",
      "bidang_ilmu" => $row["bidang_ilmu"] ?? ""
    ];
    echo tampilkanBiodata($fieldContact, $arrContact);
  }
}
?>
