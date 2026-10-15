<?php
include "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id = $_GET['id'];

// Ambil data penjualan
$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT penjualan.*, pelanggan.NamaPelanggan
     FROM penjualan
     LEFT JOIN pelanggan
     ON penjualan.PelangganID = pelanggan.PelangganID
     WHERE penjualan.PenjualanID = '$id'"
);

$data_penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$data_penjualan) {
    echo "Data penjualan tidak ditemukan.";
    exit;
}

// Ambil detail produk yang dibeli
$query_detail = mysqli_query(
    $koneksi,
    "SELECT detailpenjualan.*, produk.NamaProduk, produk.Harga
     FROM detailpenjualan
     LEFT JOIN produk
     ON detailpenjualan.ProdukID = produk.ProdukID
     WHERE detailpenjualan.PenjualanID = '$id'
     ORDER BY detailpenjualan.DetailID ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Penjualan | SweetBake</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            background: #fff8f5;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #8f5f63;
            padding: 25px 15px;
        }

        .brand {
            color: white;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 35px;
        }

        .brand i {
            color: #e8a9ad;
            margin-right: 8px;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: white;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 10px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #70464a;
        }

        .sidebar a i {
            width: 25px;
        }

        .content {
            margin-left: 240px;
            padding: 35px;
        }

        .header-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-detail h4 {
            color: #70464a;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .btn-back {
            background: #8f5f63;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
        }

        .btn-back:hover {
            background: #70464a;
            color: white;
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(112, 70, 74, 0.08);
        }

        .info-title {
            color: #70464a;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            color: #888;
            font-size: 13px;
        }

        .info-value {
            color: #444;
            font-weight: 500;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(112, 70, 74, 0.08);
        }

        .table thead th {
            background: #f5e4e2;
            color: #70464a;
            border: none;
            font-size: 13px;
        }

        .table tbody td {
            vertical-align: middle;
            font-size: 14px;
        }

        .total-box {
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #f1dddd;
        }

        .total-label {
            color: #777;
            font-size: 14px;
        }

        .total-price {
            color: #70464a;
            font-size: 24px;
            font-weight: 700;
        }

        .empty-data {
            text-align: center;
            padding: 35px;
            color: #999;
        }

    </style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fa-solid fa-cake-candles"></i>
        SweetBake
    </div>

    <a href="index.php">
        <i class="fa-solid fa-house"></i>
        Dashboard
    </a>

    <a href="pelanggan.php">
        <i class="fa-solid fa-users"></i>
        Pelanggan
    </a>

    <a href="produk.php">
        <i class="fa-solid fa-cake-candles"></i>
        Produk Kue
    </a>

    <a href="transaksi.php">
        <i class="fa-solid fa-money-bill-transfer"></i>
        Transaksi
    </a>

    <a href="penjualan.php" class="active">
        <i class="fa-solid fa-cart-shopping"></i>
        Penjualan
    </a>

</div>


<!-- CONTENT -->
<div class="content">

    <!-- HEADER -->
    <div class="header-detail">

        <div>
            <h4>
                <i class="fa-solid fa-receipt me-2"></i>
                Detail Penjualan
            </h4>

            <small class="text-muted">
                Detail pesanan kue SweetBake
            </small>
        </div>

        <a href="penjualan.php" class="btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i>
            Kembali
        </a>

    </div>


    <!-- INFORMASI PENJUALAN -->
    <div class="info-card">

        <h6 class="info-title">
            <i class="fa-solid fa-circle-info me-2"></i>
            Informasi Penjualan
        </h6>

        <div class="row">

            <div class="col-md-6">

                <div class="info-item">
                    <div class="info-label">
                        ID Penjualan
                    </div>

                    <div class="info-value">
                        #<?= htmlspecialchars($data_penjualan['PenjualanID']); ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        Tanggal Penjualan
                    </div>

                    <div class="info-value">
                        <?= htmlspecialchars($data_penjualan['TanggalPenjualan']); ?>
                    </div>
                </div>

            </div>

            <div class="col-md-6">

                <div class="info-item">
                    <div class="info-label">
                        Pelanggan
                    </div>

                    <div class="info-value">
                        <?= htmlspecialchars($data_penjualan['NamaPelanggan'] ?? '-'); ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        ID Transaksi
                    </div>

                    <div class="info-value">
                        #<?= htmlspecialchars($data_penjualan['TransaksiID'] ?? '-'); ?>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- DETAIL PESANAN -->
    <div class="table-card">

        <h6 class="info-title">
            <i class="fa-solid fa-cart-shopping me-2"></i>
            Detail Pesanan Kue
        </h6>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th width="60">No</th>
                        <th>Produk Kue</th>
                        <th>Harga</th>
                        <th width="120">Jumlah</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;
                $ada_data = false;

                while ($detail = mysqli_fetch_assoc($query_detail)):

                    $ada_data = true;

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($detail['NamaProduk']); ?>
                            </strong>
                        </td>

                        <td>
                            Rp <?= number_format($detail['Harga'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($detail['JumlahProduk']); ?>
                        </td>

                        <td>
                            <strong>
                                Rp <?= number_format($detail['Subtotal'], 0, ',', '.'); ?>
                            </strong>
                        </td>

                    </tr>

                <?php endwhile; ?>


                <?php if (!$ada_data): ?>

                    <tr>

                        <td colspan="5">

                            <div class="empty-data">

                                <i class="fa-solid fa-box-open fa-2x mb-2"></i>

                                <br>

                                Belum ada detail produk pada penjualan ini.

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- TOTAL -->
        <div class="total-box">

            <div class="total-label">
                Total Harga
            </div>

            <div class="total-price">
                Rp <?= number_format($data_penjualan['TotalHarga'], 0, ',', '.'); ?>
            </div>

        </div>

    </div>

</div>

</body>
</html>