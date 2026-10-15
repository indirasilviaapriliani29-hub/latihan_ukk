<?php

include "../koneksi.php";

if (!isset($_POST['ProdukID'])) {
    header("Location: produk.php");
    exit;
}

$ProdukID = (int) $_POST['ProdukID'];

$NamaProduk = mysqli_real_escape_string(
    $koneksi,
    $_POST['NamaProduk']
);

$Harga = (float) $_POST['Harga'];
$Stok = (int) $_POST['Stok'];


if (!empty($_FILES['foto']['name'])) {

    $foto = basename($_FILES['foto']['name']);
    $tmp = $_FILES['foto']['tmp_name'];

    /* Folder foto berada di luar folder admin */
    $folder = "../assets/img/";

    if (move_uploaded_file($tmp, $folder . $foto)) {

        $query = mysqli_query(
            $koneksi,
            "UPDATE produk SET
                NamaProduk = '$NamaProduk',
                Harga = '$Harga',
                Stok = '$Stok',
                foto = '$foto'
             WHERE ProdukID = '$ProdukID'"
        );

    } else {

        die("Foto gagal diupload.");

    }

} else {

    $query = mysqli_query(
        $koneksi,
        "UPDATE produk SET
            NamaProduk = '$NamaProduk',
            Harga = '$Harga',
            Stok = '$Stok'
         WHERE ProdukID = '$ProdukID'"
    );

}


if ($query) {

    header("Location: produk.php");
    exit;

} else {

    echo "Gagal mengubah produk: " . mysqli_error($koneksi);

}

?>