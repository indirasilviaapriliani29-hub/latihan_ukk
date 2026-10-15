<?php

include "../koneksi.php";

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$query = mysqli_query(
    $koneksi,
    "SELECT transaksi.*, pelanggan.NamaPelanggan
     FROM transaksi
     LEFT JOIN pelanggan
     ON transaksi.PelangganID = pelanggan.PelangganID
     WHERE pelanggan.NamaPelanggan LIKE '%$keyword_safe%'
     OR transaksi.MetodePembayaran LIKE '%$keyword_safe%'
     OR transaksi.StatusTransaksi LIKE '%$keyword_safe%'
     ORDER BY transaksi.TransaksiID DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaksi - SweetBake</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Poppins -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>

        body {
            background-color: #fff8f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #8f5f63;
            position: fixed;
            left: 0;
            top: 0;
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
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #e8a9ad;
            color: #70464a;
        }

        .sidebar a i {
            width: 25px;
        }

        /* CONTENT */
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

        /* CARD */
        .data-card {
            background-color: white;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        /* HEADER */
        .header-transaksi {
            position: relative;
            margin-bottom: 30px;
        }

        /* TOMBOL TAMBAH */
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
            white-space: nowrap;
            font-size: 16px;
        }

        .btn-add:hover {
            background-color: #a65f68;
            color: white;
        }

        /* SEARCH */
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
            font-family: 'Poppins', sans-serif;
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

        /* TABLE */
        .table {
            width: 100%;
            margin-bottom: 0;
        }

        .table thead tr th {
            background-color: #956267 !important;
            color: white !important;
            padding: 15px;
            border: none !important;
            font-weight: 600;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #4f3b3d;
        }

        .table tbody tr:hover td {
            background-color: #fff8f5;
        }

        /* ID TRANSAKSI */
        .id-transaksi {
            color: #70464a;
            font-weight: 600;
        }

        /* METODE */
        .metode {
            display: inline-block;
            background-color: #f7e2e0;
            color: #70464a;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        /* STATUS */
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

        /* AKSI */
        .aksi {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-edit,
        .btn-hapus {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            text-decoration: none;
            border: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-edit {
            background-color: #f4c95d;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e5b644;
            color: white;
            transform: translateY(-1px);
        }

        .btn-hapus {
            background-color: #d98f96;
            color: white;
        }

        .btn-hapus:hover {
            background-color: #c8757d;
            color: white;
            transform: translateY(-1px);
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {

            .btn-add {
                position: static;
                display: inline-block;
                margin-top: 20px;
            }

            .search-box {
                width: 100%;
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

        <a href="transaksi.php" class="active">

            <i class="fa-solid fa-cart-shopping"></i>
            Transaksi

        </a>

        <a href="penjualan.php">

            <i class="fa-solid fa-receipt"></i>
            Penjualan

        </a>

    </div>


    <!-- CONTENT -->
    <div class="content">

        <h2 class="page-title">
            Transaksi
        </h2>

        <p class="page-subtitle">
            Kelola data transaksi SweetBake
        </p>


        <div class="data-card">

            <div class="header-transaksi">

                <div>

                    <h5
                        class="mb-1"
                        style="
                            color:#70464a;
                            font-size:24px;
                            font-weight:500;
                        ">

                        <i class="fa-solid fa-cart-shopping me-2"></i>
                        Daftar Transaksi

                    </h5>

                    <small
                        class="text-muted"
                        style="font-size:16px;">

                        Data transaksi pelanggan SweetBake

                    </small>

                </div>


                <a
                    href="tambah_transaksi.php"
                    class="btn-add">

                    <i class="fa-solid fa-plus me-2"></i>
                    Tambah Transaksi

                </a>


                <!-- SEARCH TETAP -->
                <form
                    method="GET"
                    class="search-box">

                    <input
                        type="text"
                        name="keyword"
                        class="search-input"
                        placeholder="Cari transaksi..."
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

                            <th width="60">
                                No
                            </th>

                            <th>
                                ID Transaksi
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Pelanggan
                            </th>

                            <th>
                                Metode Pembayaran
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php

                        $no = 1;

                        if (mysqli_num_rows($query) > 0) {

                            while ($data = mysqli_fetch_assoc($query)) {

                                $status = strtolower(
                                    $data['StatusTransaksi']
                                );

                        ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <td class="id-transaksi">

                                #<?= $data['TransaksiID']; ?>

                            </td>


                            <td>

                                <?= date(
                                    'd-m-Y',
                                    strtotime(
                                        $data['TanggalTransaksi']
                                    )
                                ); ?>

                            </td>


                            <td>

                                <strong style="color:#70464a;">

                                    <?= htmlspecialchars(
                                        $data['NamaPelanggan'] ?? 'Umum'
                                    ); ?>

                                </strong>

                            </td>


                            <td>

                                <span class="metode">

                                    <?= htmlspecialchars(
                                        $data['MetodePembayaran']
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

                                    <span class="status status-proses">

                                        <?= htmlspecialchars(
                                            $data['StatusTransaksi']
                                        ); ?>

                                    </span>

                                <?php } ?>

                            </td>


                            <!-- AKSI -->
                            <td>

                                <div class="aksi">

                                    <a
                                        href="edit_transaksi.php?id=<?= $data['TransaksiID']; ?>"
                                        class="btn-edit"
                                        title="Edit">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>

                                    <a
                                        href="hapus_transaksi.php?id=<?= $data['TransaksiID']; ?>"
                                        class="btn-hapus"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus transaksi ini?')">

                                        <i class="fa-solid fa-trash"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                        <?php

                            }

                        } else {

                        ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5">

                                <i
                                    class="fa-solid fa-cart-shopping fa-2x mb-3"
                                    style="color:#d98f96;">
                                </i>

                                <p class="mb-0 text-muted">

                                    <?php if ($keyword != '') { ?>

                                        Transaksi
                                        "<strong>
                                            <?= htmlspecialchars($keyword); ?>
                                        </strong>"
                                        tidak ditemukan.

                                    <?php } else { ?>

                                        Belum ada data transaksi.

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