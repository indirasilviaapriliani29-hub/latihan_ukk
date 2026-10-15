<?php

include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk
     WHERE ProdukID = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data produk tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk - SweetBake</title>

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

        body {
            background-color: #fff8f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
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

        .form-card {
            background-color: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .form-label {
            color: #70464a;
            font-weight: 600;
        }

        .form-control {
            border: 1px solid #ead7d7;
            border-radius: 10px;
            padding: 11px 13px;
        }

        .form-control:focus {
            border-color: #b86f78;
            box-shadow: 0 0 0 0.2rem rgba(184, 111, 120, 0.15);
        }

        .foto-lama {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid #f0dada;
            margin-bottom: 10px;
        }

        .btn-simpan {
            background-color: #b86f78;
            color: white;
            border: none;
            padding: 11px 22px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-simpan:hover {
            background-color: #9f5d66;
            color: white;
        }

        .btn-kembali {
            background-color: white;
            color: #70464a;
            border: 1px solid #d9b7ba;
            padding: 11px 22px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-kembali:hover {
            background-color: #f7e2e0;
            color: #70464a;
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

        <h2 class="page-title">
            Edit Produk
        </h2>

        <p class="page-subtitle">
            Ubah data produk kue SweetBake
        </p>

        <div class="form-card">

            <form
                action="update_produk.php"
                method="POST"
                enctype="multipart/form-data">

                <!-- ID -->

                <input
                    type="hidden"
                    name="ProdukID"
                    value="<?= $data['ProdukID']; ?>">


                <!-- NAMA -->

                <div class="mb-3">

                    <label class="form-label">
                        Nama Produk
                    </label>

                    <input
                        type="text"
                        name="NamaProduk"
                        class="form-control"
                        value="<?= htmlspecialchars($data['NamaProduk']); ?>"
                        required>

                </div>


                <!-- HARGA & STOK -->

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Harga
                        </label>

                        <input
                            type="number"
                            name="Harga"
                            class="form-control"
                            value="<?= $data['Harga']; ?>"
                            min="0"
                            required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Stok
                        </label>

                        <input
                            type="number"
                            name="Stok"
                            class="form-control"
                            value="<?= $data['Stok']; ?>"
                            min="0"
                            required>

                    </div>

                </div>


                <!-- FOTO -->

                <div class="mb-4">

                    <label class="form-label">
                        Foto Produk
                    </label>

                    <div>

                        <?php if (!empty($data['foto'])) { ?>

                            <img
                                src="../assets/img/<?= htmlspecialchars($data['foto']); ?>"
                                class="foto-lama">

                            <br>

                            <small class="text-muted">
                                Foto saat ini
                            </small>

                        <?php } else { ?>

                            <p class="text-muted">
                                Belum ada foto.
                            </p>

                        <?php } ?>

                    </div>

                    <input
                        type="file"
                        name="foto"
                        class="form-control mt-2"
                        accept=".jpg,.jpeg,.png,.jfif,.webp">

                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti foto.
                    </small>

                </div>


                <!-- BUTTON -->

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn-simpan">

                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Simpan Perubahan

                    </button>

                    <a
                        href="produk.php"
                        class="btn-kembali">

                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</body>

</html>