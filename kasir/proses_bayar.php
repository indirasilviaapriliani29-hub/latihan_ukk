<?php

include "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kasir.php");
    exit;
}


/* =========================
   AMBIL DATA
========================= */

$transaksi_id = isset($_POST['TransaksiID'])
    ? (int) $_POST['TransaksiID']
    : 0;

$metode = isset($_POST['MetodePembayaran'])
    ? trim($_POST['MetodePembayaran'])
    : '';

$uang_bayar = isset($_POST['UangBayar'])
    ? (float) $_POST['UangBayar']
    : 0;


/* =========================
   CEK TRANSAKSI
========================= */

if ($transaksi_id <= 0) {
    die("ID transaksi tidak ditemukan.");
}


/* =========================
   NORMALISASI METODE
========================= */

$metode = strtolower($metode);

if ($metode == 'cash') {
    $metode = 'Cash';
} elseif ($metode == 'transfer') {
    $metode = 'Transfer';
} elseif ($metode == 'qris') {
    $metode = 'QRIS';
} else {
    die("Metode pembayaran tidak valid: " . htmlspecialchars($metode));
}


/* =========================
   AMBIL TOTAL
========================= */

$query = mysqli_query($koneksi, "
    SELECT
        transaksi.TransaksiID,
        transaksi.StatusTransaksi,
        penjualan.TotalHarga
    FROM transaksi
    LEFT JOIN penjualan
        ON transaksi.TransaksiID = penjualan.TransaksiID
    WHERE transaksi.TransaksiID = '$transaksi_id'
    LIMIT 1
");


if (!$query) {
    die(
        "Gagal mengambil transaksi:<br>" .
        mysqli_error($koneksi)
    );
}


if (mysqli_num_rows($query) == 0) {
    die("Transaksi tidak ditemukan.");
}


$data = mysqli_fetch_assoc($query);

$total = (float) $data['TotalHarga'];


/* =========================
   HITUNG PEMBAYARAN
========================= */

$kembalian = 0;


/* CASH */

if ($metode == 'Cash') {

    if ($uang_bayar <= 0) {
        die("Masukkan uang yang dibayar.");
    }

    if ($uang_bayar < $total) {

        die(
            "Uang pembayaran kurang.<br><br>" .
            "Total: Rp " .
            number_format($total, 0, ',', '.') .
            "<br>" .
            "Dibayar: Rp " .
            number_format($uang_bayar, 0, ',', '.')
        );
    }

    $kembalian = $uang_bayar - $total;
}


/* TRANSFER / QRIS */

if (
    $metode == 'Transfer' ||
    $metode == 'QRIS'
) {

    $uang_bayar = $total;
    $kembalian = 0;
}


/* =========================
   SIMPAN KE DATABASE
========================= */

$update = mysqli_query($koneksi, "
    UPDATE transaksi
    SET
        MetodePembayaran = '$metode',
        UangBayar = '$uang_bayar',
        Kembalian = '$kembalian',
        StatusTransaksi = 'Selesai'
    WHERE TransaksiID = '$transaksi_id'
");


/* =========================
   CEK UPDATE
========================= */

if (!$update) {

    die(
        "Pembayaran gagal disimpan.<br><br>" .
        "Error MySQL:<br>" .
        mysqli_error($koneksi)
    );
}


/* =========================
   BERHASIL
========================= */

header(
    "Location: kasir.php?id=" . $transaksi_id
);

exit;

?>