<?php

include "../koneksi.php";

if (!isset($_GET['id'])) {
    die("Invoice tidak ditemukan.");
}

$id = (int) $_GET['id'];

if ($id <= 0) {
    die("Invoice tidak ditemukan.");
}

/* =========================
   DATA TRANSAKSI
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT
        transaksi.TransaksiID,
        transaksi.TanggalTransaksi,
        transaksi.MetodePembayaran,
        transaksi.StatusTransaksi,
        transaksi.UangBayar,
        transaksi.Kembalian,

        pelanggan.NamaPelanggan,
        pelanggan.NomorTelepon,
        pelanggan.Alamat,

        penjualan.PenjualanID,
        penjualan.TotalHarga

     FROM transaksi

     LEFT JOIN pelanggan
        ON transaksi.PelangganID = pelanggan.PelangganID

     LEFT JOIN penjualan
        ON transaksi.TransaksiID = penjualan.TransaksiID

     WHERE transaksi.TransaksiID = '$id'

     LIMIT 1"
);

if (!$query) {
    die(
        "Query transaksi gagal: " .
        mysqli_error($koneksi)
    );
}

if (mysqli_num_rows($query) == 0) {
    die("Data invoice tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);


/* =========================
   DATA DETAIL PRODUK
========================= */

$penjualan_id = (int) $data['PenjualanID'];

$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        detailpenjualan.ProdukID,
        detailpenjualan.JumlahProduk,
        detailpenjualan.Subtotal,

        produk.NamaProduk,
        produk.Harga

     FROM detailpenjualan

     LEFT JOIN produk
        ON detailpenjualan.ProdukID = produk.ProdukID

     WHERE detailpenjualan.PenjualanID = '$penjualan_id'

     ORDER BY detailpenjualan.ProdukID ASC"
);

if (!$query_detail) {
    die(
        "Query detail gagal: " .
        mysqli_error($koneksi)
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Invoice #TRX<?= $data['TransaksiID']; ?> | SweetBake
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #fff8f5;
            font-family: 'Poppins', sans-serif;
            color: #70464a;
        }

        .invoice {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 5px 25px rgba(112, 70, 74, 0.12);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px dashed #e8a9ad;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 55px;
            height: 55px;
            background: #b86f78;
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .brand h2 {
            margin: 0;
            color: #70464a;
            font-size: 25px;
            font-weight: 700;
        }

        .brand p {
            margin: 3px 0 0;
            color: #999;
            font-size: 13px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h3 {
            margin: 0;
            color: #b86f78;
            font-weight: 700;
        }

        .invoice-title p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #777;
        }

        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            background: #fff8f5;
            padding: 18px;
            border-radius: 12px;
        }

        .info-box h6 {
            margin-bottom: 10px;
            color: #b86f78;
            font-weight: 700;
        }

        .info-box p {
            margin: 4px 0;
            font-size: 14px;
        }

        .table {
            margin-top: 15px;
        }

        .table thead th {
            background: #8f5f63;
            color: white;
            border: none;
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .total-area {
            margin-top: 25px;
            border-top: 2px dashed #e8a9ad;
            padding-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .total-row.grand {
            font-size: 20px;
            font-weight: 700;
            color: #b86f78;
            margin-top: 12px;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            background: #e8a9ad;
            color: #70464a;
            font-size: 12px;
            font-weight: 600;
        }

        .button-area {
            margin-top: 30px;
        }

        .btn-kembali {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #b86f78;
            border-radius: 10px;
            background: white;
            color: #b86f78;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .btn-kembali:hover {
            background: #fff0f1;
            color: #70464a;
        }

        .btn-print {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #b86f78;
            color: white;
            font-weight: 600;
        }

        .btn-print:hover {
            background: #8f5f63;
        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .invoice {
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }

            .button-area {
                display: none;
            }

        }

        @media (max-width: 600px) {

            body {
                padding: 10px;
            }

            .invoice {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-title {
                text-align: left;
            }

            .info {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="invoice">

    <!-- HEADER -->
    <div class="header">

        <div class="logo-area">

            <div class="logo">
                <i class="fa-solid fa-cake-candles"></i>
            </div>

            <div class="brand">
                <h2>SweetBake</h2>
                <p>Toko Kue & Bakery</p>
            </div>

        </div>

        <div class="invoice-title">

            <h3>INVOICE</h3>

            <p>
                #TRX<?= $data['TransaksiID']; ?>
            </p>

            <p>
                <?= date(
                    'd-m-Y',
                    strtotime($data['TanggalTransaksi'])
                ); ?>
            </p>

        </div>

    </div>


    <!-- INFORMASI -->
    <div class="info">

        <div class="info-box">

            <h6>
                <i class="fa-solid fa-user me-2"></i>
                Pelanggan
            </h6>

            <p>
                <strong>
                    <?= !empty($data['NamaPelanggan'])
                        ? htmlspecialchars($data['NamaPelanggan'])
                        : 'Umum'; ?>
                </strong>
            </p>

            <p>
                <i class="fa-solid fa-phone me-2"></i>

                <?= !empty($data['NomorTelepon'])
                    ? htmlspecialchars($data['NomorTelepon'])
                    : '-'; ?>
            </p>

            <p>
                <i class="fa-solid fa-location-dot me-2"></i>

                <?= !empty($data['Alamat'])
                    ? htmlspecialchars($data['Alamat'])
                    : '-'; ?>
            </p>

        </div>


        <div class="info-box">

            <h6>
                <i class="fa-solid fa-wallet me-2"></i>
                Pembayaran
            </h6>

            <p>
                Metode:
                <strong>
                    <?= htmlspecialchars(
                        $data['MetodePembayaran']
                    ); ?>
                </strong>
            </p>

            <p>
                Status:

                <span class="status">
                    <?= htmlspecialchars(
                        $data['StatusTransaksi']
                    ); ?>
                </span>
            </p>

        </div>

    </div>


    <!-- DETAIL PRODUK -->

    <h6
        style="
            color:#70464a;
            font-weight:700;
            margin-bottom:10px;
        "
    >
        Detail Pesanan
    </h6>

    <div class="table-responsive">

        <table class="table">

            <thead>

                <tr>

                    <th width="5%">
                        No
                    </th>

                    <th>
                        Produk
                    </th>

                    <th class="text-center">
                        Harga
                    </th>

                    <th class="text-center">
                        Jumlah
                    </th>

                    <th class="text-end">
                        Subtotal
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php

                $no = 1;

                while (
                    $detail = mysqli_fetch_assoc(
                        $query_detail
                    )
                ) {

                ?>

                <tr>

                    <td>
                        <?= $no++; ?>
                    </td>

                    <td>
                        <strong>
                            <?= htmlspecialchars(
                                $detail['NamaProduk']
                            ); ?>
                        </strong>
                    </td>

                    <td class="text-center">

                        Rp
                        <?= number_format(
                            $detail['Harga'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>

                    <td class="text-center">
                        <?= $detail['JumlahProduk']; ?>
                    </td>

                    <td class="text-end">

                        Rp
                        <?= number_format(
                            $detail['Subtotal'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


    <!-- TOTAL -->

    <div class="total-area">

        <div class="total-row">

            <span>
                Total Harga
            </span>

            <strong>

                Rp
                <?= number_format(
                    $data['TotalHarga'],
                    0,
                    ',',
                    '.'
                ); ?>

            </strong>

        </div>


        <div class="total-row">

            <span>
                Uang Bayar
            </span>

            <strong>

                Rp
                <?= number_format(
                    $data['UangBayar'],
                    0,
                    ',',
                    '.'
                ); ?>

            </strong>

        </div>


        <div class="total-row">

            <span>
                Kembalian
            </span>

            <strong>

                Rp
                <?= number_format(
                    $data['Kembalian'],
                    0,
                    ',',
                    '.'
                ); ?>

            </strong>

        </div>


        <div class="total-row grand">

            <span>
                TOTAL
            </span>

            <span>

                Rp
                <?= number_format(
                    $data['TotalHarga'],
                    0,
                    ',',
                    '.'
                ); ?>

            </span>

        </div>

    </div>


    <!-- TOMBOL -->

    <div class="button-area">

        <a
            href="kasir.php"
            class="btn-kembali"
        >
            <i class="fa-solid fa-arrow-left me-2"></i>
            Kembali
        </a>


        <button
            onclick="window.print()"
            class="btn-print"
        >
            <i class="fa-solid fa-print me-2"></i>
            Cetak Invoice
        </button>

    </div>

</div>

</body>

</html>