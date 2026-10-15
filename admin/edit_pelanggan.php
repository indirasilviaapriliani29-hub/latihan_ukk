<?php

include "../koneksi.php";

/* Ambil ID pelanggan */
if (!isset($_GET['id'])) {
    header("Location: pelanggan.php");
    exit;
}

$id = (int) $_GET['id'];

/* Ambil data pelanggan */
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     WHERE PelangganID = '$id'"
);

$data = mysqli_fetch_assoc($query);

/* Jika data tidak ditemukan */
if (!$data) {
    echo "Data pelanggan tidak ditemukan.";
    exit;
}

/* Proses update */
if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['NamaPelanggan']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['Alamat']
    );

    $telepon = mysqli_real_escape_string(
        $koneksi,
        $_POST['NomorTelepon']
    );

    $update = mysqli_query(
        $koneksi,
        "UPDATE pelanggan SET
            NamaPelanggan = '$nama',
            Alamat = '$alamat',
            NomorTelepon = '$telepon'
         WHERE PelangganID = '$id'"
    );

    if ($update) {
        header("Location: pelanggan.php");
        exit;
    } else {
        echo "Data gagal diubah: " . mysqli_error($koneksi);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Pelanggan | SweetBake</title>

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

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #8f5f63;
            padding: 25px 18px;
            color: white;
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
            font-size: 20px;
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

        .content {
            margin-left: 250px;
            padding: 35px;
            min-height: 100vh;
        }

        .page-title {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #806666;
            margin-bottom: 30px;
        }

        .form-card {
            background: white;
            border-radius: 18px;
            padding: 35px;
            width: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .form-title {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .form-label {
            color: #70464a;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #eadbd3;
            border-radius: 10px;
            padding: 13px 15px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #b86f78;
            box-shadow: 0 0 0 0.2rem rgba(184, 111, 120, 0.15);
        }

        textarea.form-control {
            min-height: 130px;
            resize: vertical;
        }

        .btn-update {
            background: #b86f78;
            border-color: #b86f78;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
        }

        .btn-update:hover {
            background: #a65f68;
            border-color: #a65f68;
            color: white;
        }

        .btn-kembali {
            background: #eadbd3;
            border-color: #eadbd3;
            color: #70464a;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
        }

        .btn-kembali:hover {
            background: #dbc9c0;
            border-color: #dbc9c0;
            color: #70464a;
        }

        .footer {
            margin-top: 30px;
            background: #8f5f63;
            color: #fceeee;
            border-radius: 16px;
            padding: 20px;
        }

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

            .form-card {
                padding: 25px;
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
            Menu
        </div>

        <a href="index.php">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>

        <a href="pelanggan.php" class="active">
            <i class="fa-solid fa-users"></i>
            <span>Pelanggan</span>
        </a>

        <a href="produk.php">
            <i class="fa-solid fa-cake-candles"></i>
            <span>Produk Kue</span>
        </a>

        <a href="transaksi.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Transaksi</span>
        </a>

        <a href="penjualan.php">
            <i class="fa-solid fa-receipt"></i>
            <span>Penjualan</span>
        </a>

    </div>

    <!-- CONTENT -->

    <div class="content">

        <h3 class="page-title">
            Edit Pelanggan
        </h3>

        <p class="page-subtitle">
            Ubah data pelanggan SweetBake.
        </p>

        <div class="form-card">

            <h5 class="form-title">
                <i class="fa-solid fa-user-pen me-2"></i>
                Form Edit Pelanggan
            </h5>

            <form method="POST">

                <div class="mb-4">

                    <label class="form-label">
                        Nama Pelanggan
                    </label>

                    <input
                        type="text"
                        name="NamaPelanggan"
                        class="form-control"
                        value="<?= htmlspecialchars($data['NamaPelanggan']); ?>"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea
                        name="Alamat"
                        class="form-control"
                        required
                    ><?= htmlspecialchars($data['Alamat']); ?></textarea>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Nomor Telepon
                    </label>

                    <input
                        type="tel"
                        name="NomorTelepon"
                        class="form-control"
                        value="<?= htmlspecialchars($data['NomorTelepon']); ?>"
                        required
                    >

                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <a
                        href="pelanggan.php"
                        class="btn btn-kembali"
                    >
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Kembali
                    </a>

                    <button
                        type="submit"
                        name="update"
                        class="btn btn-update"
                    >
                        <i class="fa-solid fa-save me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

        <div class="footer text-center">

            <i class="fa-solid fa-cake-candles me-2"></i>

            <strong>SweetBake</strong>

            <span class="mx-2">|</span>

            Sistem Kasir Toko Kue

            <br>

            <small>
                © 2026 SweetBake. All Rights Reserved.
            </small>

        </div>

    </div>

</body>

</html>