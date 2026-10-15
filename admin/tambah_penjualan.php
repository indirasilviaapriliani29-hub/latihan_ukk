<?php
include "../koneksi.php";

if (isset($_POST['simpan'])) {

    $tanggal = $_POST['TanggalPenjualan'];
    $pelanggan = $_POST['PelangganID'];
    $transaksi = $_POST['TransaksiID'];

    $produk_id = $_POST['ProdukID'];
    $jumlah_produk = $_POST['JumlahProduk'];

    $total = 0;
    $data_pesanan = [];

    // Cek produk dan stok
    for ($i = 0; $i < count($produk_id); $i++) {

        if (empty($produk_id[$i]) || empty($jumlah_produk[$i])) {
            continue;
        }

        $id_produk = $produk_id[$i];
        $jumlah = (int) $jumlah_produk[$i];

        if ($jumlah <= 0) {
            echo "Jumlah produk harus lebih dari 0.";
            exit;
        }

        $query_produk = mysqli_query(
            $koneksi,
            "SELECT * FROM produk
             WHERE ProdukID = '$id_produk'"
        );

        if (!$query_produk) {
            echo "Gagal mengambil data produk: " .
                mysqli_error($koneksi);
            exit;
        }

        $produk = mysqli_fetch_assoc($query_produk);

        if (!$produk) {
            echo "Produk tidak ditemukan.";
            exit;
        }

        if ($jumlah > $produk['Stok']) {
            echo "Stok produk " .
                htmlspecialchars($produk['NamaProduk']) .
                " tidak cukup. Stok tersedia: " .
                $produk['Stok'];
            exit;
        }

        $harga = (float) $produk['Harga'];

        $subtotal = $harga * $jumlah;

        $total += $subtotal;

        $data_pesanan[] = [
            'ProdukID' => $id_produk,
            'JumlahProduk' => $jumlah,
            'Subtotal' => $subtotal
        ];
    }

    // Minimal harus ada satu produk
    if (count($data_pesanan) == 0) {
        echo "Silakan pilih minimal satu produk.";
        exit;
    }

    // Simpan penjualan
    $query_penjualan = mysqli_query(
        $koneksi,
        "INSERT INTO penjualan
        (
            TanggalPenjualan,
            TotalHarga,
            PelangganID,
            TransaksiID
        )
        VALUES
        (
            '$tanggal',
            '$total',
            '$pelanggan',
            '$transaksi'
        )"
    );

    if (!$query_penjualan) {
        echo "Data penjualan gagal disimpan: " .
            mysqli_error($koneksi);
        exit;
    }

    // Ambil ID penjualan
    $penjualan_id = mysqli_insert_id($koneksi);

    // Simpan detail penjualan dan kurangi stok
    foreach ($data_pesanan as $item) {

        $id_produk = $item['ProdukID'];
        $jumlah = $item['JumlahProduk'];
        $subtotal = $item['Subtotal'];

        $query_detail = mysqli_query(
            $koneksi,
            "INSERT INTO detailpenjualan
            (
                PenjualanID,
                ProdukID,
                JumlahProduk,
                Subtotal
            )
            VALUES
            (
                '$penjualan_id',
                '$id_produk',
                '$jumlah',
                '$subtotal'
            )"
        );

        if (!$query_detail) {
            echo "Detail penjualan gagal disimpan: " .
                mysqli_error($koneksi);
            exit;
        }

        // Kurangi stok
        mysqli_query(
            $koneksi,
            "UPDATE produk
             SET Stok = Stok - $jumlah
             WHERE ProdukID = '$id_produk'"
        );
    }

    header("Location: penjualan.php");
    exit;
}


// Ambil data pelanggan
$data_pelanggan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pelanggan
     ORDER BY NamaPelanggan ASC"
);

if (!$data_pelanggan) {
    die("Gagal mengambil data pelanggan: " . mysqli_error($koneksi));
}


// Ambil data transaksi
$data_transaksi = mysqli_query(
    $koneksi,
    "SELECT transaksi.*,
            pelanggan.NamaPelanggan
     FROM transaksi
     LEFT JOIN pelanggan
     ON transaksi.PelangganID = pelanggan.PelangganID
     ORDER BY transaksi.TransaksiID DESC"
);

if (!$data_transaksi) {
    die("Gagal mengambil data transaksi: " . mysqli_error($koneksi));
}


// Ambil data produk
$data_produk = mysqli_query(
    $koneksi,
    "SELECT *
     FROM produk
     ORDER BY NamaProduk ASC"
);

if (!$data_produk) {
    die("Gagal mengambil data produk: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Tambah Penjualan - SweetBake</title>

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
            background: #fff8f5;
            color: #70464a;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #8f5f63;
            padding: 25px 18px;
        }

        .brand {
            color: white;
            font-size: 22px;
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
            text-decoration: none;
            color: white;
            padding: 13px 16px;
            margin-bottom: 8px;
            border-radius: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #70464a;
        }

        .sidebar a i {
            width: 25px;
        }

        .content {
            margin-left: 250px;
            padding: 35px;
        }

        .form-card {
            background: white;
            border-radius: 18px;
            padding: 35px;
            width: 100%;
            box-shadow: 0 5px 18px rgba(112, 70, 74, 0.08);
        }

        .page-title {
            color: #70464a;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-label {
            font-weight: 500;
            color: #70464a;
        }

        .form-control,
        .form-select {
            border: 1px solid #ead6d4;
            border-radius: 10px;
            padding: 11px 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #d98f96;
            box-shadow: 0 0 0 0.2rem rgba(217, 143, 150, 0.15);
        }

        .pesanan-box {
            background: #fff8f5;
            border-radius: 14px;
            padding: 20px;
            margin-top: 20px;
        }

        .pesanan-title {
            color: #70464a;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .btn-save {
            background: #d98f96;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 20px;
        }

        .btn-save:hover {
            background: #b86f78;
            color: white;
        }

        .btn-back {
            background: #eee;
            color: #555;
            border: none;
            border-radius: 10px;
            padding: 11px 20px;
        }

        .btn-back:hover {
            background: #ddd;
            color: #444;
        }

        .btn-add-product {
            background: #8f5f63;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 9px 15px;
        }

        .btn-add-product:hover {
            background: #70464a;
            color: white;
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 12px;
        }

        .btn-remove:hover {
            background: #bb2d3b;
        }

        .harga,
        .subtotal {
            background: #f5f5f5;
        }

        .total-box {
            background: #f8e7e5;
            border-radius: 12px;
            padding: 15px 20px;
            text-align: right;
            margin-top: 15px;
        }

        .total-box span {
            font-size: 18px;
            font-weight: 600;
            color: #70464a;
        }

        .total-harga {
            font-size: 22px;
            font-weight: 700;
            color: #b86f78;
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


    <a href="transaksi.php">

        <i class="fa-solid fa-cart-shopping"></i>

        Transaksi

    </a>


    <a
        href="penjualan.php"
        class="active">

        <i class="fa-solid fa-receipt"></i>

        Penjualan

    </a>

</div>


<!-- CONTENT -->

<div class="content">

    <div class="form-card">

        <h4 class="page-title">

            <i class="fa-solid fa-cart-plus me-2"></i>

            Tambah Penjualan

        </h4>


        <p class="text-muted mb-4">

            Tambahkan pesanan kue pelanggan SweetBake

        </p>


        <form method="POST">

            <!-- TANGGAL -->

            <div class="mb-3">

                <label class="form-label">

                    Tanggal Penjualan

                </label>

                <input
                    type="date"
                    name="TanggalPenjualan"
                    class="form-control"
                    value="<?= date('Y-m-d'); ?>"
                    required>

            </div>


            <!-- PELANGGAN -->

            <div class="mb-3">

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

                    <?php while (
                        $pelanggan = mysqli_fetch_assoc(
                            $data_pelanggan
                        )
                    ) { ?>

                        <option
                            value="<?= $pelanggan['PelangganID']; ?>">

                            <?= htmlspecialchars(
                                $pelanggan['NamaPelanggan']
                            ); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- TRANSAKSI -->

            <div class="mb-3">

                <label class="form-label">

                    Transaksi

                </label>

                <select
                    name="TransaksiID"
                    class="form-select"
                    required>

                    <option value="">

                        -- Pilih Transaksi --

                    </option>

                    <?php while (
                        $transaksi = mysqli_fetch_assoc(
                            $data_transaksi
                        )
                    ) { ?>

                        <option
                            value="<?= $transaksi['TransaksiID']; ?>">

                            #<?= $transaksi['TransaksiID']; ?>

                            -

                            <?= htmlspecialchars(
                                $transaksi['NamaPelanggan'] ?? '-'
                            ); ?>

                            -

                            <?= htmlspecialchars(
                                $transaksi['MetodePembayaran']
                            ); ?>

                            -

                            <?= htmlspecialchars(
                                $transaksi['StatusTransaksi']
                            ); ?>

                        </option>

                    <?php } ?>

                </select>

                <small class="text-muted">

                    Pilih transaksi yang sesuai dengan pelanggan.

                </small>

            </div>


            <!-- PESANAN -->

            <div class="pesanan-box">

                <div class="pesanan-title">

                    <i class="fa-solid fa-cake-candles me-2"></i>

                    Pesanan Kue

                </div>


                <div id="produk-container">

                    <!-- BARIS PRODUK -->

                    <div class="row g-2 produk-row mb-3">

                        <!-- PRODUK -->

                        <div class="col-md-4">

                            <label class="form-label">

                                Produk

                            </label>

                            <select
                                name="ProdukID[]"
                                class="form-select produk"
                                required>

                                <option value="">

                                    -- Pilih Produk --

                                </option>

                                <?php

                                mysqli_data_seek(
                                    $data_produk,
                                    0
                                );

                                while (
                                    $produk = mysqli_fetch_assoc(
                                        $data_produk
                                    )
                                ) {

                                ?>

                                    <option
                                        value="<?= $produk['ProdukID']; ?>"
                                        data-harga="<?= $produk['Harga']; ?>"
                                        data-stok="<?= $produk['Stok']; ?>">

                                        <?= htmlspecialchars(
                                            $produk['NamaProduk']
                                        ); ?>

                                        - Stok:
                                        <?= $produk['Stok']; ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- HARGA -->

                        <div class="col-md-3">

                            <label class="form-label">

                                Harga

                            </label>

                            <input
                                type="text"
                                class="form-control harga"
                                value="Rp0"
                                readonly>

                        </div>


                        <!-- JUMLAH -->

                        <div class="col-md-2">

                            <label class="form-label">

                                Jumlah

                            </label>

                            <input
                                type="number"
                                name="JumlahProduk[]"
                                class="form-control jumlah"
                                min="1"
                                value="1"
                                required>

                        </div>


                        <!-- SUBTOTAL -->

                        <div class="col-md-2">

                            <label class="form-label">

                                Subtotal

                            </label>

                            <input
                                type="text"
                                class="form-control subtotal"
                                value="Rp0"
                                readonly>

                        </div>


                        <!-- HAPUS -->

                        <div class="col-md-1 d-flex align-items-end">

                            <button
                                type="button"
                                class="btn btn-remove w-100"
                                onclick="hapusProduk(this)">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </div>

                    </div>

                </div>


                <!-- TAMBAH PRODUK -->

                <button
                    type="button"
                    class="btn btn-add-product"
                    onclick="tambahProduk()">

                    <i class="fa-solid fa-plus me-1"></i>

                    Tambah Produk

                </button>


                <!-- TOTAL -->

                <div class="total-box">

                    <span>

                        Total Harga :

                    </span>

                    <div class="total-harga">

                        Rp0

                    </div>

                </div>

            </div>


            <!-- BUTTON -->

            <div class="d-flex gap-2 mt-4">

                <a
                    href="penjualan.php"
                    class="btn btn-back">

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Kembali

                </a>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-save">

                    <i class="fa-solid fa-floppy-disk me-1"></i>

                    Simpan Pesanan

                </button>

            </div>

        </form>

    </div>

</div>


<script>

function formatRupiah(angka) {

    return 'Rp' +
        angka.toLocaleString('id-ID');

}


function hitungBaris(row) {

    const produk =
        row.querySelector('.produk');

    const jumlah =
        row.querySelector('.jumlah');

    const hargaInput =
        row.querySelector('.harga');

    const subtotalInput =
        row.querySelector('.subtotal');

    const option =
        produk.options[
            produk.selectedIndex
        ];

    if (!option || !option.value) {

        hargaInput.value = 'Rp0';

        subtotalInput.value = 'Rp0';

        hitungTotal();

        return;
    }

    const harga =
        parseFloat(
            option.dataset.harga
        ) || 0;

    const stok =
        parseInt(
            option.dataset.stok
        ) || 0;

    let jumlahValue =
        parseInt(
            jumlah.value
        ) || 1;

    if (stok <= 0) {

        alert(
            'Stok produk ini habis.'
        );

        jumlah.value = 1;

        hargaInput.value =
            formatRupiah(harga);

        subtotalInput.value =
            'Rp0';

        hitungTotal();

        return;
    }

    if (jumlahValue > stok) {

        alert(
            'Jumlah melebihi stok yang tersedia. Stok: '
            + stok
        );

        jumlahValue = stok;

        jumlah.value = stok;
    }

    if (jumlahValue < 1) {

        jumlahValue = 1;

        jumlah.value = 1;
    }

    const subtotal =
        harga * jumlahValue;

    hargaInput.value =
        formatRupiah(harga);

    subtotalInput.value =
        formatRupiah(subtotal);

    hitungTotal();
}


function hitungTotal() {

    let total = 0;

    document
        .querySelectorAll('.produk-row')
        .forEach(function(row) {

            const produk =
                row.querySelector('.produk');

            const jumlah =
                row.querySelector('.jumlah');

            const option =
                produk.options[
                    produk.selectedIndex
                ];

            if (
                option &&
                option.value
            ) {

                const harga =
                    parseFloat(
                        option.dataset.harga
                    ) || 0;

                const qty =
                    parseInt(
                        jumlah.value
                    ) || 0;

                total +=
                    harga * qty;
            }

        });

    document.querySelector(
        '.total-harga'
    ).innerText =
        formatRupiah(total);
}


function tambahProduk() {

    const container =
        document.getElementById(
            'produk-container'
        );

    const rows =
        document.querySelectorAll(
            '.produk-row'
        );

    const firstRow =
        rows[0];

    const newRow =
        firstRow.cloneNode(true);

    newRow.querySelector(
        '.produk'
    ).selectedIndex = 0;

    newRow.querySelector(
        '.jumlah'
    ).value = 1;

    newRow.querySelector(
        '.harga'
    ).value = 'Rp0';

    newRow.querySelector(
        '.subtotal'
    ).value = 'Rp0';

    container.appendChild(
        newRow
    );

    pasangEvent(newRow);
}


function hapusProduk(button) {

    const rows =
        document.querySelectorAll(
            '.produk-row'
        );

    if (rows.length <= 1) {

        alert(
            'Minimal harus ada satu produk.'
        );

        return;
    }

    button
        .closest('.produk-row')
        .remove();

    hitungTotal();
}


function pasangEvent(row) {

    row.querySelector(
        '.produk'
    ).addEventListener(
        'change',
        function() {

            hitungBaris(row);

        }
    );

    row.querySelector(
        '.jumlah'
    ).addEventListener(
        'input',
        function() {

            hitungBaris(row);

        }
    );
}


document
    .querySelectorAll('.produk-row')
    .forEach(function(row) {

        pasangEvent(row);

    });

</script>

</body>

</html>