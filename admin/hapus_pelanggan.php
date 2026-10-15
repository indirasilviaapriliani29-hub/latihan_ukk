<?php

include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}

$id = (int) $_GET['id'];

/* Hapus detail penjualan yang berhubungan */
mysqli_query(
    $koneksi,
    "DELETE detailpenjualan
     FROM detailpenjualan
     INNER JOIN penjualan
     ON detailpenjualan.PenjualanID = penjualan.PenjualanID
     WHERE penjualan.PelangganID = '$id'"
);

/* Hapus penjualan */
mysqli_query(
    $koneksi,
    "DELETE FROM penjualan
     WHERE PelangganID = '$id'"
);

/* Hapus transaksi */
mysqli_query(
    $koneksi,
    "DELETE FROM transaksi
     WHERE PelangganID = '$id'"
);

/* Hapus pelanggan */
$query = mysqli_query(
    $koneksi,
    "DELETE FROM pelanggan
     WHERE PelangganID = '$id'"
);

if ($query) {
    header("Location: pelanggan.php");
    exit;
} else {
    echo "Data pelanggan gagal dihapus: " . mysqli_error($koneksi);
}

?>