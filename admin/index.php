<?php
include "../koneksi.php";

$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM pelanggan"
);

$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);

$query_produk = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM produk"
);

$data_produk = mysqli_fetch_assoc($query_produk);

$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM transaksi"
);

$data_transaksi = mysqli_fetch_assoc($query_transaksi);

$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM penjualan"
);

$data_penjualan = mysqli_fetch_assoc($query_penjualan);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin | SweetBake</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff8f5;
            color: #4d3838;
        }


        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #8f5f63;
            padding: 25px 18px;
            color: white;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 8px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: #e8a9ad;
            color: #70464a;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f3dede;
            margin: 0 0 10px 10px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
            color: #fceeee;
            padding: 13px 14px;
            margin-bottom: 7px;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a i {
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar a.active {
            background: #e8a9ad;
            color: #70464a;
            font-weight: bold;
        }


        .logout {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.25);
        }

        .logout a {
            background: rgba(255, 255, 255, 0.08);
        }

        .logout a:hover {
            background: #e8a9ad;
            color: #70464a;
        }


        .content {
            margin-left: 250px;
            padding: 30px 35px;
            min-height: 100vh;
        }


        .topbar {
            margin-bottom: 25px;
        }

        .topbar h3 {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 5px;
        }


        /* WELCOME */

        .welcome {
            background: linear-gradient(
                135deg,
                #d98f96,
                #b86f78
            );
            color: white;
            border-radius: 18px;
            padding: 28px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(143, 95, 99, 0.15);
        }

        .welcome h4 {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .welcome p {
            margin: 0;
            opacity: 0.9;
        }


        /* STAT CARD */

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            margin-bottom: 15px;
        }

        .stat-icon.pink {
            background: #f7dfe0;
            color: #b86f78;
        }

        .stat-icon.peach {
            background: #fde3d0;
            color: #d1845d;
        }

        .stat-icon.cream {
            background: #f8edcf;
            color: #b18a45;
        }

        .stat-icon.brown {
            background: #eadbd3;
            color: #8b6655;
        }

        .stat-card h6 {
            color: #806666;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .stat-card h3 {
            color: #4d3838;
            font-weight: bold;
            margin: 0;
        }


        /* SECTION */

        .section-title {
            color: #70464a;
            font-weight: bold;
            margin-top: 35px;
            margin-bottom: 18px;
        }


        /* MENU CARD */

        .menu-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            height: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .menu-card h5 {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .menu-card p {
            color: #806666;
            font-size: 14px;
            min-height: 42px;
        }

        .btn-pink {
            background: #b86f78;
            border-color: #b86f78;
            color: white;
        }

        .btn-pink:hover {
            background: #a65f68;
            border-color: #a65f68;
            color: white;
        }

        .btn-green {
            background: #9a9b70;
            border-color: #9a9b70;
            color: white;
        }

        .btn-green:hover {
            background: #85865f;
            border-color: #85865f;
            color: white;
        }

        .btn-orange {
            background: #d1845d;
            border-color: #d1845d;
            color: white;
        }

        .btn-orange:hover {
            background: #b96e4a;
            border-color: #b96e4a;
            color: white;
        }

        .btn-brown {
            background: #8f6b61;
            border-color: #8f6b61;
            color: white;
        }

        .btn-brown:hover {
            background: #76554d;
            border-color: #76554d;
            color: white;
        }


        /* FOOTER */

        .footer {
            margin-top: 50px;
            background: #8f5f63;
            color: #fceeee;
            border-radius: 16px;
            padding: 30px;
        }

        .footer-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer-title i {
            color: #e8a9ad;
            margin-right: 8px;
        }

        .footer-heading {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .footer-text {
            color: #f8dddd;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .social-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .social-links a {
            color: #fceeee;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .social-links a:hover {
            color: #e8a9ad;
        }

        .social-links i {
            width: 22px;
            font-size: 17px;
        }

        .location-link {
            color: #fceeee;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .location-link:hover {
            color: #e8a9ad;
        }

        .location-link i {
            color: #e8a9ad;
        }

        .footer hr {
            border-color: rgba(255, 255, 255, 0.25);
            margin: 25px 0 18px;
        }


        /* RESPONSIVE */

        @media (max-width: 768px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .brand span,
            .menu-title,
            .sidebar a span {
                display: none;
            }

            .brand {
                justify-content: center;
                padding: 0;
            }

            .sidebar a {
                justify-content: center;
                padding: 13px 5px;
            }

            .content {
                margin-left: 70px;
                padding: 25px 20px;
            }

            .welcome {
                padding: 24px;
            }

            .footer {
                padding: 25px 20px;
            }

            .topbar {
                align-items: flex-start !important;
                gap: 15px;
            }

        }

    </style>

</head>


<body>


    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                <i class="fa-solid fa-cake-candles"></i>
            </div>

            <span>SweetBake</span>

        </div>


        <div class="menu-title">
            Menu Admin
        </div>


        <a href="index.php" class="active">

            <i class="fa-solid fa-house"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a href="pelanggan.php">

            <i class="fa-solid fa-users"></i>

            <span>
                Pelanggan
            </span>

        </a>


        <a href="produk.php">

            <i class="fa-solid fa-cake-candles"></i>

            <span>
                Produk Kue
            </span>

        </a>


        <a href="transaksi.php">

            <i class="fa-solid fa-cart-shopping"></i>

            <span>
                Transaksi
            </span>

        </a>


        <a href="penjualan.php">

            <i class="fa-solid fa-receipt"></i>

            <span>
                Penjualan
            </span>

        </a>


        <!-- LOGOUT -->

        <div class="logout">

            <a
                href="../logout.php"
                onclick="return confirm('Yakin ingin logout?')"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">


        <!-- TOPBAR -->

        <div class="topbar">

            <div>

                <h3>
                    Dashboard Admin
                </h3>

                <small class="text-muted">
                    Ringkasan toko kue SweetBake
                </small>

            </div>

        </div>


        <!-- WELCOME -->

        <div class="welcome">

            <h4>
                Selamat Datang di SweetBake! 🍰
            </h4>

            <p>
                Kelola pelanggan, produk kue, transaksi,
                dan penjualan dengan mudah.
            </p>

        </div>


        <!-- STATISTIK -->

        <div class="row g-4">


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon pink">

                        <i class="fa-solid fa-users"></i>

                    </div>

                    <h6>
                        Total Pelanggan
                    </h6>

                    <h3>
                        <?= $data_pelanggan['total']; ?>
                    </h3>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon peach">

                        <i class="fa-solid fa-cake-candles"></i>

                    </div>

                    <h6>
                        Total Produk Kue
                    </h6>

                    <h3>
                        <?= $data_produk['total']; ?>
                    </h3>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon cream">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>

                    <h6>
                        Total Transaksi
                    </h6>

                    <h3>
                        <?= $data_transaksi['total']; ?>
                    </h3>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon brown">

                        <i class="fa-solid fa-receipt"></i>

                    </div>

                    <h6>
                        Total Penjualan
                    </h6>

                    <h3>
                        <?= $data_penjualan['total']; ?>
                    </h3>

                </div>

            </div>


        </div>


        <!-- MENU UTAMA -->

        <h5 class="section-title">
            Menu Utama
        </h5>


        <div class="row g-4">


            <div class="col-md-6 col-xl-3">

                <div class="menu-card">

                    <h5>

                        <i class="fa-solid fa-users me-2"></i>

                        Pelanggan

                    </h5>

                    <p>
                        Kelola data pelanggan toko.
                    </p>

                    <a
                        href="pelanggan.php"
                        class="btn btn-pink w-100"
                    >
                        Kelola Pelanggan
                    </a>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="menu-card">

                    <h5>

                        <i class="fa-solid fa-cake-candles me-2"></i>

                        Produk Kue

                    </h5>

                    <p>
                        Kelola brownies, cookies, cake,
                        dan dessert.
                    </p>

                    <a
                        href="produk.php"
                        class="btn btn-green w-100"
                    >
                        Kelola Produk
                    </a>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="menu-card">

                    <h5>

                        <i class="fa-solid fa-cart-shopping me-2"></i>

                        Transaksi

                    </h5>

                    <p>
                        Buat transaksi pembelian kue.
                    </p>

                    <a
                        href="transaksi.php"
                        class="btn btn-orange w-100"
                    >
                        Kelola Transaksi
                    </a>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="menu-card">

                    <h5>

                        <i class="fa-solid fa-receipt me-2"></i>

                        Penjualan

                    </h5>

                    <p>
                        Lihat riwayat penjualan kue.
                    </p>

                    <a
                        href="penjualan.php"
                        class="btn btn-brown w-100"
                    >
                        Lihat Penjualan
                    </a>

                </div>

            </div>


        </div>


        <!-- FOOTER -->

        <div class="footer">

            <div class="row g-4">


                <div class="col-md-5">

                    <div class="footer-title">

                        <i class="fa-solid fa-cake-candles"></i>

                        SweetBake

                    </div>

                    <p class="footer-text">

                        Toko kue dengan berbagai pilihan
                        brownies, cookies, cake, dan dessert.

                    </p>

                </div>


                <div class="col-md-3">

                    <h6 class="footer-heading">
                        Ikuti Kami
                    </h6>

                    <div class="social-links">

                        <a href="#">

                            <i class="fa-brands fa-whatsapp"></i>

                            083167020193

                        </a>


                        <a href="#">

                            <i class="fa-brands fa-instagram"></i>

                            @SweetBakeQu

                        </a>


                        <a href="#">

                            <i class="fa-brands fa-tiktok"></i>

                            @SweetBakeQu

                        </a>

                    </div>

                </div>


                <div class="col-md-4">

                    <h6 class="footer-heading">
                        Lokasi Toko
                    </h6>

                    <a
                        href="https://www.google.com/maps/search/?api=1&query=SMKN+3+Kendal"
                        target="_blank"
                        class="location-link"
                    >

                        <i class="fa-solid fa-location-dot me-2"></i>

                        SMKN 3 Kendal

                    </a>

                    <p class="footer-text mt-2">

                        Klik alamat untuk melihat lokasi
                        di Google Maps.

                    </p>

                </div>


            </div>


            <hr>


            <div class="text-center">

                <p class="mb-0">
                    © SweetBake
                </p>

            </div>

        </div>


    </div>


</body>

</html>