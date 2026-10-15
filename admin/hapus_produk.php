<?php

include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "DELETE FROM produk
     WHERE ProdukID = '$id'"
);

if ($query) {

    header("Location: produk.php");
    exit;

} else {

    echo "Gagal menghapus produk: " . mysqli_error($koneksi);

}

?>