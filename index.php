<?php
include "koneksi.php";

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$keyword_safe = mysqli_real_escape_string(
    $koneksi,
    $keyword
);

if ($keyword != '') {

    $query_produk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk
         WHERE NamaProduk LIKE '%$keyword_safe%'
         ORDER BY ProdukID DESC"
    );

} else {

    $query_produk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk
         ORDER BY ProdukID DESC"
    );

}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SweetBake | Toko Kue</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Poppins -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>

        * {
            font-family: 'Poppins', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: #fff8f5;
            color: #70464a;
        }

        /* NAVBAR */

        .navbar-custom {
            background: #8f5f63;
            padding: 14px 0;
        }

        .navbar-brand {
            color: white !important;
            font-size: 23px;
            font-weight: 700;
        }

        .navbar-brand i {
            color: #f2c4c7;
        }

        .nav-link {
            color: white !important;
            font-size: 14px;
            margin-left: 12px;
        }

        .nav-link:hover {
            color: #f2c4c7 !important;
        }

        .btn-login {
            background: #f2c4c7;
            color: #70464a !important;
            border-radius: 9px;
            padding: 8px 15px !important;
            font-weight: 600;
        }

        .btn-login:hover {
            background: white;
            color: #70464a !important;
        }

        /* HERO */

        .hero {
            padding: 85px 0;
            background: linear-gradient(
                135deg,
                #f6d9d8,
                #fff8f5
            );
        }

        .hero-title {
            font-size: 48px;
            line-height: 1.2;
            font-weight: 700;
            color: #70464a;
        }

        .hero-title span {
            color: #b86f78;
        }

        .hero-text {
            color: #80686a;
            font-size: 16px;
            line-height: 1.8;
            max-width: 560px;
        }

        .btn-hero {
            background: #d98f96;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 22px;
            font-weight: 600;
        }

        .btn-hero:hover {
            background: #b86f78;
            color: white;
        }

        .hero-icon {
            width: 330px;
            height: 330px;
            border-radius: 50%;
            background: #f1c4c6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .hero-icon i {
            font-size: 135px;
            color: #fff8f5;
        }

        /* SECTION */

        .section {
            padding: 70px 0;
        }

        .section-title {
            color: #70464a;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section-subtitle {
            color: #8c7779;
            margin-bottom: 40px;
        }

        /* SEARCH */

        .search-box {
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .search-box .input-group {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
            overflow: hidden;
        }

        .search-box .input-group-text {
            background: white;
            border: none;
            color: #b86f78;
            padding-left: 18px;
        }

        .search-box input {
            border: none;
            box-shadow: none !important;
            padding: 12px 10px;
            color: #70464a;
        }

        .search-box input::placeholder {
            color: #a89496;
        }

        .btn-search {
            background: #d98f96;
            color: white;
            border: none;
            padding: 0 22px;
        }

        .btn-search:hover {
            background: #b86f78;
            color: white;
        }

        .btn-reset {
            background: #f5e7e5;
            color: #70464a;
            border: none;
            padding: 0 18px;
        }

        .btn-reset:hover {
            background: #e8d0ce;
            color: #70464a;
        }

        /* PRODUK */

        .product-card {
            background: white;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
            transition: 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image,
        .product-placeholder {
            width: 100%;
            height: 220px;
        }

        .product-image {
            object-fit: cover;
            background: #f5e7e5;
        }

        .product-placeholder {
            background: #f5e7e5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-placeholder i {
            font-size: 65px;
            color: #d98f96;
        }

        .product-body {
            padding: 20px;
        }

        .product-name {
            color: #70464a;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .product-price {
            color: #b86f78;
            font-size: 18px;
            font-weight: 700;
        }

        .product-stock {
            color: #8c7779;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .btn-order {
            width: 100%;
            background: #d98f96;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 10px;
        }

        .btn-order:hover {
            background: #b86f78;
            color: white;
        }

        /* TENTANG */

        .about-section {
            background: white;
            padding: 70px 0;
        }

        .about-box {
            text-align: center;
            padding: 20px;
        }

        .about-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #f8dfe0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b86f78;
            font-size: 25px;
        }

        .about-box h5 {
            color: #70464a;
            font-weight: 600;
        }

        .about-box p {
            color: #8c7779;
            font-size: 14px;
            line-height: 1.7;
        }

        /* FOOTER */

        footer {
            background: #70464a;
            color: white;
            padding: 35px 0 20px;
        }

        footer h5 {
            font-weight: 600;
        }

        footer p {
            color: #f5dfe0;
            font-size: 14px;
        }

        footer a {
            color: #f5dfe0;
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 18px;
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #f5dfe0;
        }

        /* RESPONSIVE */

        @media (max-width: 768px) {

            .hero {
                padding: 60px 0;
                text-align: center;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-text {
                margin: auto;
            }

            .hero-icon {
                width: 240px;
                height: 240px;
                margin-top: 40px;
            }

            .hero-icon i {
                font-size: 95px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">

    <div class="container">

        <a class="navbar-brand" href="index.php">

            <i class="fa-solid fa-cake-candles me-2"></i>

            SweetBake

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
            aria-controls="navbarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarMenu">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">

                    <a class="nav-link" href="#beranda">
                        Beranda
                    </a>

                </li>


                <li class="nav-item">

                    <a class="nav-link" href="#produk">
                        Kue Kami
                    </a>

                </li>


                <li class="nav-item">

                    <a class="nav-link" href="#tentang">
                        Tentang Kami
                    </a>

                </li>


                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">

                    <a
                        href="login.php"
                        class="nav-link btn-login">

                        <i class="fa-solid fa-right-to-bracket me-1"></i>

                        Login

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>



<!-- HERO -->

<section class="hero" id="beranda">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-7">

                <h1 class="hero-title">

                    Temukan Kue Favoritmu
                    di <span>SweetBake</span> 🍰

                </h1>


                <p class="hero-text mt-3">

                    Nikmati berbagai pilihan kue lezat
                    yang dibuat dengan bahan berkualitas.
                    Pilih kue favoritmu dan pesan dengan
                    mudah di SweetBake.

                </p>


                <a
                    href="#produk"
                    class="btn btn-hero mt-3">

                    Lihat Kue

                    <i class="fa-solid fa-arrow-right ms-2"></i>

                </a>

            </div>


            <div class="col-lg-5">

                <div class="hero-icon">

                    <i class="fa-solid fa-cake-candles"></i>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- PRODUK -->

<section class="section" id="produk">

    <div class="container">

        <div class="text-center">

            <h2 class="section-title">
                Kue Kami
            </h2>

            <p class="section-subtitle">
                Pilihan kue SweetBake yang siap menemani harimu
            </p>

        </div>


        <!-- SEARCH PRODUK -->

        <form
            method="GET"
            action="index.php"
            class="search-box">

            <div class="input-group">

                <span class="input-group-text">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </span>


                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Cari nama kue..."
                    value="<?= htmlspecialchars($keyword); ?>">


                <button
                    type="submit"
                    class="btn btn-search">

                    <i class="fa-solid fa-magnifying-glass me-1"></i>

                    Cari

                </button>


                <?php if ($keyword != '') { ?>

                    <a
                        href="index.php#produk"
                        class="btn btn-reset d-flex align-items-center">

                        <i class="fa-solid fa-xmark"></i>

                    </a>

                <?php } ?>

            </div>

        </form>


        <?php if ($keyword != '') { ?>

            <div class="text-center mb-4">

                <small class="text-muted">

                    Hasil pencarian untuk:
                    <strong>
                        <?= htmlspecialchars($keyword); ?>
                    </strong>

                </small>

            </div>

        <?php } ?>


        <div class="row g-4">

            <?php if (mysqli_num_rows($query_produk) > 0) { ?>


                <?php while ($produk = mysqli_fetch_assoc($query_produk)) { ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="product-card">


                            <?php

                            if (
                                !empty($produk['foto']) &&
                                file_exists(
                                    "assets/img/" . $produk['foto']
                                )
                            ) {

                            ?>

                                <img
                                    src="assets/img/<?= htmlspecialchars($produk['foto']); ?>"
                                    class="product-image"
                                    alt="<?= htmlspecialchars($produk['NamaProduk']); ?>">


                            <?php

                            } else {

                            ?>

                                <div class="product-placeholder">

                                    <i class="fa-solid fa-cake-candles"></i>

                                </div>


                            <?php

                            }

                            ?>


                            <div class="product-body">


                                <div class="product-name">

                                    <?= htmlspecialchars(
                                        $produk['NamaProduk']
                                    ); ?>

                                </div>


                                <div class="product-price">

                                    Rp
                                    <?= number_format(
                                        $produk['Harga'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </div>


                                <div class="product-stock">

                                    <i class="fa-solid fa-box me-1"></i>

                                    Stok:
                                    <?= $produk['Stok']; ?>

                                </div>


                                <?php if ($produk['Stok'] > 0) { ?>


                                    <a
                                        href="pesanan.php?id=<?= $produk['ProdukID']; ?>"
                                        class="btn btn-order">

                                        <i class="fa-solid fa-cart-plus me-1"></i>

                                        Pesan Sekarang

                                    </a>


                                <?php } else { ?>


                                    <button
                                        class="btn btn-secondary w-100"
                                        disabled>

                                        <i class="fa-solid fa-ban me-1"></i>

                                        Stok Habis

                                    </button>


                                <?php } ?>


                            </div>

                        </div>

                    </div>

                <?php } ?>


            <?php } else { ?>


                <div class="col-12">

                    <div class="alert alert-light text-center">

                        <i class="fa-solid fa-magnifying-glass me-2"></i>

                        Produk
                        <strong>
                            <?= htmlspecialchars($keyword); ?>
                        </strong>
                        tidak ditemukan.

                    </div>

                </div>


            <?php } ?>

        </div>

    </div>

</section>



<!-- TENTANG -->

<section
    class="about-section"
    id="tentang">

    <div class="container">


        <div class="text-center mb-5">

            <h2 class="section-title">

                Kenapa SweetBake?

            </h2>


            <p class="section-subtitle mb-0">

                Kami ingin membuat setiap momen menjadi lebih manis.

            </p>

        </div>


        <div class="row g-4">


            <div class="col-md-4">

                <div class="about-box">

                    <div class="about-icon">

                        <i class="fa-solid fa-cake-candles"></i>

                    </div>


                    <h5>
                        Kue Berkualitas
                    </h5>


                    <p>

                        Menggunakan bahan pilihan
                        untuk menghasilkan kue
                        yang lezat dan berkualitas.

                    </p>

                </div>

            </div>



            <div class="col-md-4">

                <div class="about-box">

                    <div class="about-icon">

                        <i class="fa-solid fa-heart"></i>

                    </div>


                    <h5>
                        Dibuat dengan Cinta
                    </h5>


                    <p>

                        Setiap kue dibuat dengan
                        perhatian agar memberikan
                        rasa terbaik untuk pelanggan.

                    </p>

                </div>

            </div>



            <div class="col-md-4">

                <div class="about-box">

                    <div class="about-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>


                    <h5>
                        Mudah Dipesan
                    </h5>


                    <p>

                        Pilih kue favoritmu dan
                        lakukan pemesanan dengan
                        mudah melalui SweetBake.

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- FOOTER -->

<footer>

    <div class="container">

        <div class="row">


            <div class="col-md-6 mb-4">

                <h5>

                    <i class="fa-solid fa-cake-candles me-2"></i>

                    SweetBake

                </h5>


                <p class="mt-3">

                    Toko kue dengan berbagai pilihan
                    kue lezat untuk menemani setiap
                    momen spesialmu.

                </p>

            </div>



            <div class="col-md-6 mb-4">

                <h5>
                    Hubungi Kami
                </h5>


                <p class="mt-3">

                    <i class="fa-brands fa-whatsapp me-2"></i>

                    083167020193

                </p>


                <p>

                    <i class="fa-brands fa-instagram me-2"></i>

                    @SweetBakeQu

                </p>


                <p>

                    <i class="fa-brands fa-tiktok me-2"></i>

                    @SweetBakeQu

                </p>


                <p>

                    <i class="fa-solid fa-location-dot me-2"></i>

                    SMKN 3 Kendal

                </p>

            </div>

        </div>


        <div class="footer-bottom">

            &copy; 2026 SweetBake.
            All Rights Reserved.

        </div>

    </div>

</footer>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>