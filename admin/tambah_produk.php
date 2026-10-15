<?php
include "../koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk | SweetBake</title>

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
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #fff8f5;
            color: #4d3838;
        }

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

        .content {
            margin-left: 240px;
            padding: 40px;
            min-height: 100vh;
        }

        .page-title {
            color: #70464a;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #8f6b6e;
            margin-bottom: 30px;
        }

        .card-form {
            background: white;
            border-radius: 18px;
            padding: 35px;
            width: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 22px;
            margin-bottom: 25px;
            border-bottom: 1px solid #f1dddd;
        }

        .form-icon {
            width: 50px;
            height: 50px;
            background: #f7e2e0;
            color: #b86f78;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .form-header h5 {
            color: #70464a;
            font-weight: 700;
            margin: 0;
        }

        .form-header small {
            color: #999;
        }

        .form-label {
            color: #70464a;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #ead7d7;
            border-radius: 10px;
            padding: 12px 14px;
            min-height: 48px;
        }

        .form-control:focus {
            border-color: #b86f78;
            box-shadow: 0 0 0 0.2rem rgba(184, 111, 120, 0.15);
        }

        .form-text {
            color: #999;
            font-size: 12px;
            margin-top: 7px;
        }

        .btn-simpan {
            background: #b86f78;
            border: none;
            color: white;
            padding: 12px 22px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-simpan:hover {
            background: #a65f68;
            color: white;
        }

        .btn-kembali {
            background: white;
            border: 1px solid #b86f78;
            color: #b86f78;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-kembali:hover {
            background: #f7dfe0;
            color: #70464a;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
                padding: 25px;
            }

            .card-form {
                padding: 25px;
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

    <div class="content">

        <div class="mb-4">

            <h3 class="page-title">
                <i class="fa-solid fa-cake-candles me-2"></i>
                Tambah Produk Kue
            </h3>

            <p class="page-subtitle">
                Tambahkan produk baru ke dalam daftar SweetBake.
            </p>

        </div>

        <div class="card-form">

            <div class="form-header">

                <div class="form-icon">
                    <i class="fa-solid fa-cake-candles"></i>
                </div>

                <div>
                    <h5>Informasi Produk</h5>
                    <small>
                        Masukkan data produk kue dengan lengkap.
                    </small>
                </div>

            </div>

            <form
                action="simpan_produk.php"
                method="POST"
                enctype="multipart/form-data">

                <div class="mb-4">

                    <label class="form-label">
                        Nama Produk
                    </label>

                    <input
                        type="text"
                        name="NamaProduk"
                        class="form-control"
                        placeholder="Contoh: Caramel Peanut Cheesecake"
                        required>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Harga
                        </label>

                        <input
                            type="number"
                            name="Harga"
                            class="form-control"
                            placeholder="Contoh: 50000"
                            min="0"
                            required>

                        <div class="form-text">
                            Masukkan harga dalam rupiah.
                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Stok
                        </label>

                        <input
                            type="number"
                            name="Stok"
                            class="form-control"
                            placeholder="Contoh: 10"
                            min="0"
                            required>

                        <div class="form-text">
                            Masukkan jumlah stok produk.
                        </div>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Foto Produk
                    </label>

                    <input
                        type="file"
                        name="foto"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.jfif,.webp"
                        required>

                    <div class="form-text">
                        Format foto: JPG, JPEG, PNG, JFIF, atau WEBP.
                    </div>

                </div>

                <div class="d-flex gap-2 pt-2">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn-simpan">

                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        Simpan Produk

                    </button>

                    <a
                        href="produk.php"
                        class="btn-kembali">

                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</body>

</html>