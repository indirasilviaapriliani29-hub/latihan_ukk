<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id = $_GET['id'];

/* AMBIL DATA PENJUALAN */
$data_penjualan = mysqli_query(
    $koneksi,
    "SELECT PenjualanID
     FROM penjualan
     WHERE TransaksiID = '$id'"
);

/* HAPUS DETAIL PENJUALAN */
while ($penjualan = mysqli_fetch_assoc($data_penjualan)) {

    $penjualan_id = $penjualan['PenjualanID'];

    mysqli_query(
        $koneksi,
        "DELETE FROM detailpenjualan
         WHERE PenjualanID = '$penjualan_id'"
    );
}

/* HAPUS PENJUALAN */
$hapus_penjualan = mysqli_query(
    $koneksi,
    "DELETE FROM penjualan
     WHERE TransaksiID = '$id'"
);

if (!$hapus_penjualan) {
    echo "Data penjualan gagal dihapus: "
        . mysqli_error($koneksi);
    exit;
}

/* HAPUS TRANSAKSI */
$hapus_transaksi = mysqli_query(
    $koneksi,
    "DELETE FROM transaksi
     WHERE TransaksiID = '$id'"
);

if ($hapus_transaksi) {

    header("Location: transaksi.php");
    exit;

} else {

    echo "Data transaksi gagal dihapus: "
        . mysqli_error($koneksi);
}
?>