<?php
include "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id = $_GET['id'];

// Kembalikan stok produk dari detail penjualan
$query_detail = mysqli_query(
    $koneksi,
    "SELECT ProdukID, JumlahProduk
     FROM detailpenjualan
     WHERE PenjualanID = '$id'"
);

while ($detail = mysqli_fetch_assoc($query_detail)) {

    $produk_id = $detail['ProdukID'];
    $jumlah = $detail['JumlahProduk'];

    mysqli_query(
        $koneksi,
        "UPDATE produk
         SET Stok = Stok + $jumlah
         WHERE ProdukID = '$produk_id'"
    );
}

// Hapus detail penjualan terlebih dahulu
$query_detail_hapus = mysqli_query(
    $koneksi,
    "DELETE FROM detailpenjualan
     WHERE PenjualanID = '$id'"
);

// Hapus data penjualan
$query_penjualan = mysqli_query(
    $koneksi,
    "DELETE FROM penjualan
     WHERE PenjualanID = '$id'"
);

if ($query_penjualan) {

    header("Location: penjualan.php");
    exit;

} else {

    echo "Data penjualan gagal dihapus: " . mysqli_error($koneksi);
}
?>