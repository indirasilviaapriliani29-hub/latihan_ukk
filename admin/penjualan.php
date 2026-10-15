<?php
include "../koneksi.php";

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$query = mysqli_query(
    $koneksi,
    "SELECT 
        penjualan.*,
        pelanggan.NamaPelanggan,
        transaksi.MetodePembayaran,
        transaksi.StatusTransaksi
     FROM penjualan
     LEFT JOIN pelanggan
        ON penjualan.PelangganID = pelanggan.PelangganID
     LEFT JOIN transaksi
        ON penjualan.TransaksiID = transaksi.TransaksiID
     WHERE pelanggan.NamaPelanggan LIKE '%$keyword_safe%'
        OR transaksi.MetodePembayaran LIKE '%$keyword_safe%'
        OR transaksi.StatusTransaksi LIKE '%$keyword_safe%'
     ORDER BY penjualan.PenjualanID DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penjualan - SweetBake</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background-color: #fff8f5;
            color: #70464a;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #8f5f63;
            padding: 25px 15px;
        }

        .brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
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
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #e8a9ad;
            color: #70464a;
        }

        .sidebar a i {
            width: 25px;
        }

        .content {
            margin-left: 240px;
            padding: 35px;
        }

        .page-title {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #8f6b6e;
            margin-bottom: 25px;
        }

        .data-card {
            background-color: white;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .header-penjualan {
            position: relative;
            margin-bottom: 30px;
        }

        .judul-penjualan h5 {
            color: #70464a;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .judul-penjualan small {
            color: #777;
            font-size: 16px;
        }

        .btn-add {
            position: absolute;
            right: 0;
            top: 0;
            background-color: #b86f78;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 18px;
            text-decoration: none;
            font-size: 16px;
        }

        .btn-add:hover {
            background-color: #a65f68;
            color: white;
        }

        .search-box {
            display: flex;
            width: 500px;
            margin-top: 30px;
        }

        .search-input {
            width: 100%;
            height: 48px;
            border: 1px solid #d9dfe5;
            border-right: none;
            border-radius: 12px 0 0 12px;
            padding: 10px 15px;
            outline: none;
            background-color: white;
            font-size: 16px;
        }

        .search-input:focus {
            border-color: #b86f78;
        }

        .btn-search {
            width: 55px;
            height: 48px;
            border: none;
            background-color: #b86f78;
            color: white;
            border-radius: 0 12px 12px 0;
            font-size: 18px;
        }

        .btn-search:hover {
            background-color: #a65f68;
        }

        .table-responsive {
            margin-top: 5px;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table thead tr th {
            background-color: #956267 !important;
            color: white !important;
            padding: 15px;
            border: none !important;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 15px;
            font-size: 14px;
            color: #5f4548;
            border-bottom: 1px solid #f3e5e2;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            background-color: #fffaf9;
        }

        .id-penjualan {
            color: #70464a;
            font-weight: 600;
        }

        .total-harga {
            color: #70464a;
            font-weight: 600;
        }

        .metode {
            display: inline-block;
            background-color: #f7e2e0;
            color: #70464a;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-selesai {
            background-color: #dcefdc;
            color: #4f7d4f;
        }

        .status-proses {
            background-color: #fff0c9;
            color: #92752c;
        }

        .status-batal {
            background-color: #f7d8d8;
            color: #9b4f4f;
        }

        .status-default {
            background-color: #eee;
            color: #666;
        }

        .btn-invoice {
            background-color: #d8e7f3;
            color: #456b85;
            border: none;
        }

        .btn-invoice:hover {
            background-color: #c4dbe9;
            color: #456b85;
        }

        .btn-edit {
            background-color: #fff0c9;
            color: #92752c;
            border: none;
        }

        .btn-edit:hover {
            background-color: #ffe5a5;
            color: #92752c;
        }

        .btn-delete {
            background-color: #f7d8d8;
            color: #9b4f4f;
            border: none;
        }

        .btn-delete:hover {
            background-color: #efc0c0;
            color: #9b4f4f;
        }

        @media (max-width: 900px) {

            .search-box {
                width: 100%;
            }

            .btn-add {
                position: static;
                display: inline-block;
                margin-top: 20px;
            }

        }

        @media (max-width: 768px) {

            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
                padding: 20px;
            }

        }

    </style>

</head>

<body>

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
        <i class="fa-solid fa-cart-shopping"></i>
        Transaksi
    </a>

    <a href="penjualan.php" class="active">
        <i class="fa-solid fa-receipt"></i>
        Penjualan
    </a>

</div>


<div class="content">

    <h2 class="page-title">
        Penjualan
    </h2>

    <p class="page-subtitle">
        Kelola data penjualan SweetBake
    </p>

    <div class="data-card">

        <div class="header-penjualan">

            <div class="judul-penjualan">

                <h5>
                    <i class="fa-solid fa-receipt me-2"></i>
                    Daftar Penjualan
                </h5>

                <small>
                    Data penjualan SweetBake
                </small>

            </div>

            <a
                href="tambah_penjualan.php"
                class="btn-add">

                <i class="fa-solid fa-plus me-2"></i>
                Tambah Penjualan

            </a>

            <form
                method="GET"
                class="search-box">

                <input
                    type="text"
                    name="keyword"
                    class="search-input"
                    placeholder="Cari penjualan..."
                    value="<?= htmlspecialchars($keyword); ?>">

                <button
                    type="submit"
                    class="btn-search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </button>

            </form>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th width="50">No</th>

                        <th>ID Penjualan</th>

                        <th>Tanggal</th>

                        <th>Pelanggan</th>

                        <th>Total Harga</th>

                        <th>Metode Pembayaran</th>

                        <th>Status</th>

                        <th width="150">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($query) > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                        $status = strtolower(
                            $data['StatusTransaksi'] ?? ''
                        );

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td class="id-penjualan">
                            #<?= $data['PenjualanID']; ?>
                        </td>

                        <td>
                            <?= date(
                                'd-m-Y',
                                strtotime($data['TanggalPenjualan'])
                            ); ?>
                        </td>

                        <td>
                            <strong style="color:#70464a;">
                                <?= htmlspecialchars(
                                    $data['NamaPelanggan'] ?? '-'
                                ); ?>
                            </strong>
                        </td>

                        <td class="total-harga">

                            Rp
                            <?= number_format(
                                $data['TotalHarga'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>

                        <td>

                            <span class="metode">

                                <?= htmlspecialchars(
                                    $data['MetodePembayaran'] ?? '-'
                                ); ?>

                            </span>

                        </td>

                        <td>

                            <?php if ($status == 'selesai') { ?>

                                <span class="status status-selesai">
                                    Selesai
                                </span>

                            <?php } elseif (
                                $status == 'proses' ||
                                $status == 'diproses'
                            ) { ?>

                                <span class="status status-proses">
                                    Diproses
                                </span>

                            <?php } elseif ($status == 'batal') { ?>

                                <span class="status status-batal">
                                    Batal
                                </span>

                            <?php } else { ?>

                                <span class="status status-default">
                                    <?= htmlspecialchars(
                                        $data['StatusTransaksi'] ?? '-'
                                    ); ?>
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <a
                                href="invoice.php?id=<?= $data['PenjualanID']; ?>"
                                class="btn btn-invoice btn-sm"
                                title="Invoice">

                                <i class="fa-solid fa-file-invoice"></i>

                            </a>

                            <a
                                href="edit_penjualan.php?id=<?= $data['PenjualanID']; ?>"
                                class="btn btn-edit btn-sm"
                                title="Edit">

                                <i class="fa-solid fa-pen"></i>

                            </a>

                            <a
                                href="hapus_penjualan.php?id=<?= $data['PenjualanID']; ?>"
                                class="btn btn-delete btn-sm"
                                title="Hapus"
                                onclick="return confirm('Yakin ingin menghapus penjualan ini?')">

                                <i class="fa-solid fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5">

                            <i
                                class="fa-solid fa-receipt fa-2x mb-3"
                                style="color:#d98f96;">
                            </i>

                            <p class="mb-0 text-muted">

                                <?php if ($keyword != '') { ?>

                                    Penjualan
                                    "<strong>
                                        <?= htmlspecialchars($keyword); ?>
                                    </strong>"
                                    tidak ditemukan.

                                <?php } else { ?>

                                    Belum ada data penjualan.

                                <?php } ?>

                            </p>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>