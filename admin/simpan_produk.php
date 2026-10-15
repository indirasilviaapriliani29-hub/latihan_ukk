<?php

include "../koneksi.php";

if (!isset($_POST['NamaProduk'])) {
    header("Location: tambah_produk.php");
    exit;
}

$NamaProduk = mysqli_real_escape_string(
    $koneksi,
    $_POST['NamaProduk']
);

$Harga = (float) $_POST['Harga'];
$Stok = (int) $_POST['Stok'];

$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];

/* Folder foto */
$folder = "../assets/img/";

/* Upload foto */
if (!move_uploaded_file($tmp, $folder . $foto)) {
    die("Foto gagal diupload.");
}

/* Simpan data produk */
$query = mysqli_query(
    $koneksi,
    "INSERT INTO produk
    (NamaProduk, Harga, Stok, foto)
    VALUES
    ('$NamaProduk', '$Harga', '$Stok', '$foto')"
);

if ($query) {

    header("Location: produk.php");
    exit;

} else {

    echo "Gagal menyimpan data: " . mysqli_error($koneksi);

}

?>