<?php

include "../koneksi.php";

$keyword = "";

if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     WHERE NamaPelanggan LIKE '%$keyword_safe%'
     OR NomorTelepon LIKE '%$keyword_safe%'
     OR Alamat LIKE '%$keyword_safe%'
     ORDER BY PelangganID DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pelanggan | SweetBake</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet">

    <style>

        body {
            margin: 0;
            background: #fff8f5;
            font-family: Arial, sans-serif;
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
            color: #f3dede;
            margin-bottom: 10px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #fceeee;
            text-decoration: none;
            padding: 13px 14px;
            margin-bottom: 7px;
            border-radius: 10px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #e8a9ad;
            color: #70464a;
        }

        .content {
            margin-left: 250px;
            padding: 35px;
        }

        .page-title {
            color: #70464a;
            font-weight: bold;
        }

        .card-custom {
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(112,70,74,0.08);
            border: none;
        }

        .btn-pink {
            background: #b86f78;
            color: white;
            border: none;
        }

        .btn-pink:hover {
            background: #a65f68;
            color: white;
        }

        .table th {
            background: #8f5f63;
            color: white;
        }

        .table td {
            vertical-align: middle;
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

            .sidebar a {
                justify-content: center;
            }

            .content {
                margin-left: 70px;
                padding: 25px 20px;
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
        MENU ADMIN
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

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="page-title">
                <i class="fa-solid fa-users me-2"></i>
                Data Pelanggan
            </h3>

            <p class="text-muted mb-0">
                Kelola data pelanggan SweetBake
            </p>

        </div>

        <a href="tambah_pelanggan.php" class="btn btn-pink">
            <i class="fa-solid fa-plus me-1"></i>
            Tambah Pelanggan
        </a>

    </div>


    <!-- SEARCH -->
    <div class="card-custom p-3 mb-4">

        <form method="GET">

            <div class="input-group">

                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Cari nama, nomor telepon, atau alamat..."
                    value="<?= htmlspecialchars($keyword); ?>"
                >

                <button class="btn btn-pink" type="submit">
                    <i class="fa-solid fa-search"></i>
                    Cari
                </button>

            </div>

        </form>

    </div>


    <!-- TABLE -->
    <div class="card-custom p-4">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead>

                    <tr>

                        <th width="60">No</th>
                        <th>Nama Pelanggan</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th width="150">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($query) > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['NamaPelanggan']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['NomorTelepon']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['Alamat']); ?>
                        </td>

                        <td>

                            <a
                                href="edit_pelanggan.php?id=<?= $data['PelangganID']; ?>"
                                class="btn btn-warning btn-sm"
                            >
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a
                                href="hapus_pelanggan.php?id=<?= $data['PelangganID']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </a>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="5" class="text-center text-muted">
                            Data pelanggan belum ada.
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