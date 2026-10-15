<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    die("Invoice tidak ditemukan.");
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT penjualan.*, pelanggan.NamaPelanggan,
            pelanggan.NomorTelepon, pelanggan.Alamat,
            transaksi.MetodePembayaran
     FROM penjualan
     LEFT JOIN pelanggan
        ON penjualan.PelangganID = pelanggan.PelangganID
     LEFT JOIN transaksi
        ON penjualan.TransaksiID = transaksi.TransaksiID
     WHERE penjualan.PenjualanID = $id"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data invoice tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Invoice SweetBake</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body {
            margin: 0;
            background: #f5eeee;
            font-family: 'Poppins', sans-serif;
            padding: 40px 15px;
        }

        .struk {
            width: 430px;
            max-width: 100%;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(112, 70, 74, 0.15);
            position: relative;
        }

        .logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo i {
            font-size: 42px;
            color: #b86f78;
        }

        .nama-toko {
            text-align: center;
            color: #70464a;
            font-size: 25px;
            font-weight: 700;
            margin: 5px 0 2px;
        }

        .alamat {
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .garis {
            border-top: 2px dashed #d9bfc2;
            margin: 20px 0;
        }

        .judul {
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: #70464a;
            letter-spacing: 1px;
        }

        .info {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
        }

        .info strong {
            color: #70464a;
        }

        .produk {
            width: 100%;
            font-size: 12px;
        }

        .produk td {
            padding: 7px 0;
        }

        .produk .harga {
            text-align: right;
            font-weight: 600;
            color: #70464a;
        }

        .total-box {
            background: #fff4f4;
            padding: 14px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total span {
            font-size: 14px;
            font-weight: 600;
            color: #70464a;
        }

        .total strong {
            font-size: 20px;
            color: #b86f78;
        }

        .terima-kasih {
            text-align: center;
            margin-top: 25px;
            color: #70464a;
            font-size: 13px;
            font-weight: 600;
        }

        .pesan {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }

        .btn-area {
            width: 430px;
            max-width: 100%;
            margin: 20px auto 0;
            display: flex;
            gap: 10px;
        }

        .btn-print,
        .btn-kembali {
            flex: 1;
            padding: 11px;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-print {
            border: none;
            background: #b86f78;
            color: white;
        }

        .btn-print:hover {
            background: #70464a;
            color: white;
        }

        .btn-kembali {
            background: white;
            border: 1px solid #b86f78;
            color: #b86f78;
        }

        .btn-kembali:hover {
            background: #fff0f1;
            color: #70464a;
        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .struk {
                width: 80mm;
                box-shadow: none;
                padding: 15px;
            }

            .btn-area {
                display: none;
            }
        }

    </style>

</head>

<body>

    <div class="struk">

        <!-- LOGO -->

        <div class="logo">

            <i class="fa-solid fa-cake-candles"></i>

        </div>


        <div class="nama-toko">
            SweetBake
        </div>


        <div class="alamat">
            Toko Kue SweetBake<br>
            Terima kasih sudah berbelanja ♡
        </div>


        <div class="garis"></div>


        <div class="judul">
            INVOICE PEMBELIAN
        </div>


        <div class="garis"></div>


        <!-- INFORMASI -->

        <div class="info">

            <div>

                <strong>No. Invoice :</strong>

                #<?= str_pad(
                    $data['PenjualanID'],
                    5,
                    '0',
                    STR_PAD_LEFT
                ); ?>

            </div>


            <div>

                <strong>Tanggal :</strong>

                <?= date(
                    'd-m-Y',
                    strtotime($data['TanggalPenjualan'])
                ); ?>

            </div>


            <div>

                <strong>Pelanggan :</strong>

                <?= htmlspecialchars(
                    $data['NamaPelanggan'] ?? '-'
                ); ?>

            </div>


            <div>

                <strong>No. Telepon :</strong>

                <?= htmlspecialchars(
                    $data['NomorTelepon'] ?? '-'
                ); ?>

            </div>


            <div>

                <strong>Pembayaran :</strong>

                <?= htmlspecialchars(
                    $data['MetodePembayaran'] ?? '-'
                ); ?>

            </div>

        </div>


        <div class="garis"></div>


        <!-- PRODUK -->

        <table class="produk">

            <tr>

                <td>
                    <strong>Pesanan</strong>
                </td>

                <td class="harga">
                    Harga
                </td>

            </tr>


            <tr>

                <td>
                    Pembelian Produk
                </td>

                <td class="harga">

                    Rp
                    <?= number_format(
                        $data['TotalHarga'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

        </table>


        <div class="garis"></div>


        <!-- TOTAL -->

        <div class="total-box">

            <div class="total">

                <span>
                    TOTAL
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

        </div>


        <div class="terima-kasih">
            ♡ Terima kasih sudah berbelanja ♡
        </div>


        <div class="pesan">
            Semoga harimu semanis kue kami 🍰
        </div>

    </div>


    <!-- BUTTON -->

    <div class="btn-area">

        <a
            href="penjualan.php"
            class="btn-kembali">

            <i class="fa-solid fa-arrow-left me-1"></i>

            Kembali

        </a>


        <button
            onclick="window.print()"
            class="btn-print">

            <i class="fa-solid fa-print me-1"></i>

            Cetak Invoice

        </button>

    </div>

</body>

</html>