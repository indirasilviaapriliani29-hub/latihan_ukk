<?php
include "../koneksi.php";

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk
     WHERE NamaProduk LIKE '%$keyword_safe%'
     ORDER BY ProdukID DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produk Kue - SweetBake</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

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
            font-size: 24px;
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
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .sidebar a i {
            width: 25px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #e8a9ad;
            color: #70464a;
        }

        .content {
            margin-left: 240px;
            padding: 35px;
        }

        .data-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .page-title {
            color: #70464a;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #70464a;
            font-size: 16px;
        }

        .produk-header {
            position: relative;
            margin-bottom: 30px;
        }

        .btn-add {
            position: absolute;
            right: 0;
            top: 0;
            background: #b86f78;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 11px 18px;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-add:hover {
            background: #a65f68;
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
            font-family: 'Poppins', sans-serif;
        }

        .btn-search {
            width: 55px;
            height: 48px;
            border: none;
            background: #b86f78;
            color: white;
            border-radius: 0 12px 12px 0;
        }

        .produk-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #f0dddd;
            box-shadow: 0 5px 15px rgba(112, 70, 74, 0.08);
            height: 100%;
        }

        .produk-foto {
            width: 100%;
            height: 210px;
            background: #fdf0ee;
            overflow: hidden;
        }

        .produk-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .foto-kosong {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b86f78;
            font-size: 45px;
        }

        .produk-info {
            padding: 18px;
        }

        .produk-info h5 {
            color: #70464a;
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .produk-harga {
            color: #b86f78;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .produk-stok {
            display: inline-block;
            background: #f7e2e0;
            color: #70464a;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 13px;
        }

        .aksi {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        .btn-edit {
            flex: 1;
            background: #e8a9ad;
            color: #70464a;
            border: none;
            padding: 8px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-size: 13px;
        }

        .btn-edit:hover {
            background: #d9959b;
            color: #70464a;
        }

        .btn-hapus {
            flex: 1;
            background: #70464a;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-size: 13px;
        }

        .btn-hapus:hover {
            background: #56363a;
            color: white;
        }

        .empty-data {
            padding: 50px;
            text-align: center;
            color: #999;
            background: #fff;
            border: 1px solid #f0dddd;
            border-radius: 15px;
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

        @media (max-width: 576px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .content {
                margin-left: 0;
                padding: 15px;
            }

            .data-card {
                padding: 20px;
            }

            .search-box {
                margin-top: 20px;
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

        <a href="produk.php" class="active">
            <i class="fa-solid fa-cake-candles"></i>
            Produk Kue
        </a>

        <a href="transaksi.php">
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

        <div class="data-card">

            <div class="produk-header">

                <h2 class="page-title">
                    <i class="fa-solid fa-cake-candles me-2"></i>
                    Daftar Produk
                </h2>

                <p class="page-subtitle">
                    Data produk kue toko
                </p>

                <a href="tambah_produk.php" class="btn-add">
                    <i class="fa-solid fa-plus me-2"></i>
                    Tambah Produk
                </a>

                <form method="GET" class="search-box">

                    <input
                        type="text"
                        name="keyword"
                        class="search-input"
                        placeholder="Cari produk..."
                        value="<?= htmlspecialchars($keyword); ?>">

                    <button type="submit" class="btn-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                </form>

            </div>


            <!-- CARD PRODUK -->
            <div class="row g-4">

                <?php if (mysqli_num_rows($query) > 0) { ?>

                    <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                        <div class="col-xl-3 col-lg-4 col-md-6">

                            <div class="produk-card">

                                <!-- FOTO -->
                                <div class="produk-foto">

                                    <?php if (!empty($data['foto'])) { ?>

                                        <img
                                            src="../assets/img/<?= htmlspecialchars($data['foto']); ?>"
                                            alt="<?= htmlspecialchars($data['NamaProduk']); ?>">

                                    <?php } else { ?>

                                        <div class="foto-kosong">
                                            <i class="fa-solid fa-cake-candles"></i>
                                        </div>

                                    <?php } ?>

                                </div>


                                <!-- INFO -->
                                <div class="produk-info">

                                    <h5>
                                        <?= htmlspecialchars($data['NamaProduk']); ?>
                                    </h5>

                                    <div class="produk-harga">
                                        Rp <?= number_format(
                                            $data['Harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </div>

                                    <div class="produk-stok">

                                        <i class="fa-solid fa-box"></i>

                                        Stok:
                                        <?= htmlspecialchars($data['Stok']); ?>

                                    </div>


                                    <!-- AKSI CRUD -->
                                    <div class="aksi">

                                        <a
                                            href="edit_produk.php?id=<?= $data['ProdukID']; ?>"
                                            class="btn-edit">

                                            <i class="fa-solid fa-pen"></i>
                                            Edit

                                        </a>

                                        <a
                                            href="hapus_produk.php?id=<?= $data['ProdukID']; ?>"
                                            class="btn-hapus"
                                            onclick="return confirm('Yakin ingin menghapus produk ini?');">

                                            <i class="fa-solid fa-trash"></i>
                                            Hapus

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="col-12">

                        <div class="empty-data">

                            <i
                                class="fa-solid fa-box-open fa-2x mb-3"
                                style="color:#b86f78;">
                            </i>

                            <br>

                            <?php if ($keyword != '') { ?>

                                Produk
                                "<strong><?= htmlspecialchars($keyword); ?></strong>"
                                tidak ditemukan.

                            <?php } else { ?>

                                Belum ada data produk.

                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

</body>

</html>