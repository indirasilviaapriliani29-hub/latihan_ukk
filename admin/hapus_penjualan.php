<?php

include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id = (int) $_GET['id'];


// Ambil detail penjualan
$query_detail = mysqli_query(
    $koneksi,
    "SELECT ProdukID, JumlahProduk
     FROM detailpenjualan
     WHERE PenjualanID = '$id'"
);

if (!$query_detail) {
    echo "Data detail penjualan gagal ditemukan: " .
         mysqli_error($koneksi);
    exit;
}


// Kembalikan stok produk
while ($detail = mysqli_fetch_assoc($query_detail)) {

    $produk_id = (int) $detail['ProdukID'];
    $jumlah = (int) $detail['JumlahProduk'];

    $update_stok = mysqli_query(
        $koneksi,
        "UPDATE produk
         SET Stok = Stok + $jumlah
         WHERE ProdukID = '$produk_id'"
    );

    if (!$update_stok) {
        echo "Stok produk gagal dikembalikan: " .
             mysqli_error($koneksi);
        exit;
    }
}


// Hapus detail penjualan terlebih dahulu
$hapus_detail = mysqli_query(
    $koneksi,
    "DELETE FROM detailpenjualan
     WHERE PenjualanID = '$id'"
);

if (!$hapus_detail) {
    echo "Detail penjualan gagal dihapus: " .
         mysqli_error($koneksi);
    exit;
}


// Hapus data penjualan
$hapus_penjualan = mysqli_query(
    $koneksi,
    "DELETE FROM penjualan
     WHERE PenjualanID = '$id'"
);

if (!$hapus_penjualan) {
    echo "Data penjualan gagal dihapus: " .
         mysqli_error($koneksi);
    exit;
}


// Kembali ke halaman penjualan
header("Location: penjualan.php");
exit;

?>