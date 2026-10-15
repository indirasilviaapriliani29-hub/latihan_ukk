<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id = $_GET['id'];

/* AMBIL DATA TRANSAKSI */
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM transaksi
     WHERE TransaksiID = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data transaksi tidak ditemukan.";
    exit;
}

/* AMBIL DATA PELANGGAN */
$data_pelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     ORDER BY NamaPelanggan ASC"
);

/* UPDATE DATA */
if (isset($_POST['update'])) {

    $tanggal = $_POST['TanggalTransaksi'];
    $pelanggan = $_POST['PelangganID'];
    $metode = $_POST['MetodePembayaran'];
    $status = $_POST['StatusTransaksi'];

    $update = mysqli_query(
        $koneksi,
        "UPDATE transaksi SET
            TanggalTransaksi = '$tanggal',
            PelangganID = '$pelanggan',
            MetodePembayaran = '$metode',
            StatusTransaksi = '$status'
         WHERE TransaksiID = '$id'"
    );

    if ($update) {
        header("Location: transaksi.php");
        exit;
    } else {
        echo "Data transaksi gagal diubah: "
            . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Transaksi - SweetBake</title>

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

        /* FORM CARD */

        .form-card {
            background-color: white;
            border-radius: 18px;
            padding: 35px;
            width: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .form-label {
            color: #70464a;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border: 1px solid #ead7d7;
            border-radius: 10px;
            padding: 11px 13px;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #d98f96;
            box-shadow: 0 0 0 0.2rem rgba(217, 143, 150, 0.15);
        }

        /* BUTTON */

        .btn-update {
            background-color: #d98f96;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 20px;
        }

        .btn-update:hover {
            background-color: #b86f78;
            color: white;
        }

        .btn-back {
            background-color: #f1e4e2;
            color: #70464a;
            border: none;
            border-radius: 10px;
            padding: 11px 20px;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #ead7d7;
            color: #70464a;
        }

        /* ICON */

        .form-icon {
            width: 45px;
            height: 45px;
            background-color: #f7e2e0;
            color: #b86f78;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
                padding: 20px;
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
            Edit Transaksi
        </h2>

        <p class="page-subtitle">
            Ubah data transaksi SweetBake
        </p>


        <!-- FORM -->

        <div class="form-card">

            <div class="d-flex align-items-center gap-3 mb-4">

                <div class="form-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <div>

                    <h5 class="mb-1" style="color:#70464a;">
                        Edit Transaksi
                    </h5>

                    <small class="text-muted">
                        Perbarui data transaksi
                    </small>

                </div>

            </div>


            <form method="POST">

                <!-- TANGGAL -->

                <div class="mb-4">

                    <label class="form-label">
                        Tanggal Transaksi
                    </label>

                    <input
                        type="date"
                        name="TanggalTransaksi"
                        class="form-control"
                        value="<?= htmlspecialchars($data['TanggalTransaksi']); ?>"
                        required>

                </div>


                <!-- PELANGGAN -->

                <div class="mb-4">

                    <label class="form-label">
                        Pelanggan
                    </label>

                    <select
                        name="PelangganID"
                        class="form-select"
                        required>

                        <option value="">
                            -- Pilih Pelanggan --
                        </option>

                        <?php while ($pelanggan = mysqli_fetch_assoc($data_pelanggan)) { ?>

                            <option
                                value="<?= $pelanggan['PelangganID']; ?>"
                                <?= ($pelanggan['PelangganID'] == $data['PelangganID']) ? 'selected' : ''; ?>>

                                <?= htmlspecialchars($pelanggan['NamaPelanggan']); ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- METODE -->

                <div class="mb-4">

                    <label class="form-label">
                        Metode Pembayaran
                    </label>

                    <select
                        name="MetodePembayaran"
                        class="form-select"
                        required>

                        <option value="">
                            -- Pilih Metode Pembayaran --
                        </option>

                        <option
                            value="Cash"
                            <?= ($data['MetodePembayaran'] == 'Cash') ? 'selected' : ''; ?>>
                            Cash
                        </option>

                        <option
                            value="Transfer"
                            <?= ($data['MetodePembayaran'] == 'Transfer') ? 'selected' : ''; ?>>
                            Transfer
                        </option>

                        <option
                            value="QRIS"
                            <?= ($data['MetodePembayaran'] == 'QRIS') ? 'selected' : ''; ?>>
                            QRIS
                        </option>

                    </select>

                </div>


                <!-- STATUS -->

                <div class="mb-4">

                    <label class="form-label">
                        Status Transaksi
                    </label>

                    <select
                        name="StatusTransaksi"
                        class="form-select"
                        required>

                        <option value="">
                            -- Pilih Status --
                        </option>

                        <option
                            value="Proses"
                            <?= ($data['StatusTransaksi'] == 'Proses') ? 'selected' : ''; ?>>
                            Proses
                        </option>

                        <option
                            value="Selesai"
                            <?= ($data['StatusTransaksi'] == 'Selesai') ? 'selected' : ''; ?>>
                            Selesai
                        </option>

                        <option
                            value="Batal"
                            <?= ($data['StatusTransaksi'] == 'Batal') ? 'selected' : ''; ?>>
                            Batal
                        </option>

                    </select>

                </div>


                <!-- BUTTON -->

                <div class="d-flex gap-2 mt-4">

                    <a
                        href="transaksi.php"
                        class="btn-back">

                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Kembali

                    </a>

                    <button
                        type="submit"
                        name="update"
                        class="btn-update">

                        <i class="fa-solid fa-pen me-1"></i>
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</body>

</html>