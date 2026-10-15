<?php
include "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id = $_GET['id'];

// Ambil data penjualan
$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT * FROM penjualan
     WHERE PenjualanID = '$id'"
);

$data_penjualan = mysqli_fetch_assoc($query_penjualan);

if (!$data_penjualan) {
    echo "Data penjualan tidak ditemukan.";
    exit;
}


// Ambil data pelanggan
$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan
     ORDER BY NamaPelanggan ASC"
);


// Ambil data produk
$query_produk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk
     ORDER BY NamaProduk ASC"
);


// Ambil detail penjualan
$query_detail = mysqli_query(
    $koneksi,
    "SELECT detailpenjualan.*, produk.NamaProduk, produk.Harga
     FROM detailpenjualan
     LEFT JOIN produk
     ON detailpenjualan.ProdukID = produk.ProdukID
     WHERE detailpenjualan.PenjualanID = '$id'
     ORDER BY detailpenjualan.DetailID ASC"
);


// Simpan perubahan
if (isset($_POST['update'])) {

    $tanggal = $_POST['TanggalPenjualan'];
    $pelanggan = $_POST['PelangganID'];

    $produk_id = $_POST['ProdukID'];
    $jumlah = $_POST['JumlahProduk'];

    $total_harga = 0;


    // Hitung total baru
    for ($i = 0; $i < count($produk_id); $i++) {

        if ($produk_id[$i] == "" || $jumlah[$i] <= 0) {
            continue;
        }

        $id_produk = $produk_id[$i];
        $qty = $jumlah[$i];

        $query_harga = mysqli_query(
            $koneksi,
            "SELECT Harga
             FROM produk
             WHERE ProdukID = '$id_produk'"
        );

        $data_produk = mysqli_fetch_assoc($query_harga);

        $harga = $data_produk['Harga'];

        $subtotal = $harga * $qty;

        $total_harga += $subtotal;
    }


    // Kembalikan stok dari detail lama
    $query_detail_lama = mysqli_query(
        $koneksi,
        "SELECT ProdukID, JumlahProduk
         FROM detailpenjualan
         WHERE PenjualanID = '$id'"
    );

    while ($detail_lama = mysqli_fetch_assoc($query_detail_lama)) {

        $produk_lama = $detail_lama['ProdukID'];
        $jumlah_lama = $detail_lama['JumlahProduk'];

        mysqli_query(
            $koneksi,
            "UPDATE produk
             SET Stok = Stok + $jumlah_lama
             WHERE ProdukID = '$produk_lama'"
        );
    }


    // Hapus detail lama
    mysqli_query(
        $koneksi,
        "DELETE FROM detailpenjualan
         WHERE PenjualanID = '$id'"
    );


    // Update penjualan
    $query_update = mysqli_query(
        $koneksi,
        "UPDATE penjualan
         SET
            TanggalPenjualan = '$tanggal',
            TotalHarga = '$total_harga',
            PelangganID = '$pelanggan'
         WHERE PenjualanID = '$id'"
    );


    if ($query_update) {

        // Masukkan detail baru
        for ($i = 0; $i < count($produk_id); $i++) {

            if ($produk_id[$i] == "" || $jumlah[$i] <= 0) {
                continue;
            }

            $id_produk = $produk_id[$i];
            $qty = $jumlah[$i];

            $query_harga = mysqli_query(
                $koneksi,
                "SELECT Harga
                 FROM produk
                 WHERE ProdukID = '$id_produk'"
            );

            $data_produk = mysqli_fetch_assoc($query_harga);

            $harga = $data_produk['Harga'];

            $subtotal = $harga * $qty;


            // Simpan detail
            mysqli_query(
                $koneksi,
                "INSERT INTO detailpenjualan
                (PenjualanID, ProdukID, JumlahProduk, Subtotal)
                VALUES
                ('$id', '$id_produk', '$qty', '$subtotal')"
            );


            // Kurangi stok
            mysqli_query(
                $koneksi,
                "UPDATE produk
                 SET Stok = Stok - $qty
                 WHERE ProdukID = '$id_produk'"
            );
        }


        header("Location: penjualan.php");
        exit;

    } else {

        echo "Data penjualan gagal diubah: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Penjualan | SweetBake</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

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
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 10px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #70464a;
        }

        .sidebar a i {
            width: 25px;
        }

        .content {
            margin-left: 240px;
            padding: 35px;
        }

        .header-page {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-page h4 {
            color: #70464a;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .btn-back {
            background: #8f5f63;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
        }

        .btn-back:hover {
            background: #70464a;
            color: white;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(112, 70, 74, 0.08);
        }

        .form-title {
            color: #70464a;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-label {
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            border-radius: 9px;
            border: 1px solid #ddd;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c58b90;
            box-shadow: 0 0 0 0.15rem rgba(197, 139, 144, 0.2);
        }

        .produk-row {
            background: #fff8f5;
            border: 1px solid #f0dddd;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .btn-tambah {
            background: #8f5f63;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 9px;
        }

        .btn-tambah:hover {
            background: #70464a;
            color: white;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 12px;
        }

        .btn-simpan {
            background: #70464a;
            color: white;
            border: none;
            padding: 11px 25px;
            border-radius: 9px;
        }

        .btn-simpan:hover {
            background: #5c393d;
            color: white;
        }

        .total-box {
            background: #f8e8e6;
            border-radius: 12px;
            padding: 18px 20px;
            text-align: right;
            margin-top: 20px;
        }

        .total-box small {
            color: #777;
        }

        .total-harga {
            color: #70464a;
            font-size: 25px;
            font-weight: 700;
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
        <i class="fa-solid fa-money-bill-transfer"></i>
        Transaksi
    </a>

    <a href="penjualan.php" class="active">
        <i class="fa-solid fa-cart-shopping"></i>
        Penjualan
    </a>

</div>


<!-- CONTENT -->
<div class="content">

    <div class="header-page">

        <div>

            <h4>
                <i class="fa-solid fa-pen-to-square me-2"></i>
                Edit Penjualan
            </h4>

            <small class="text-muted">
                Ubah data pesanan kue SweetBake
            </small>

        </div>

        <a href="penjualan.php" class="btn-back">

            <i class="fa-solid fa-arrow-left me-1"></i>
            Kembali

        </a>

    </div>


    <div class="form-card">

        <form method="POST">


            <!-- INFORMASI -->
            <h6 class="form-title">

                <i class="fa-solid fa-circle-info me-2"></i>
                Informasi Penjualan

            </h6>


            <div class="row mb-4">


                <!-- TANGGAL -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Tanggal Penjualan
                    </label>

                    <input
                        type="date"
                        name="TanggalPenjualan"
                        class="form-control"
                        value="<?= htmlspecialchars($data_penjualan['TanggalPenjualan']); ?>"
                        required
                    >

                </div>


                <!-- PELANGGAN -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Pelanggan
                    </label>

                    <select
                        name="PelangganID"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Pelanggan --
                        </option>

                        <?php while ($pelanggan = mysqli_fetch_assoc($query_pelanggan)): ?>

                            <option
                                value="<?= $pelanggan['PelangganID']; ?>"
                                <?= ($pelanggan['PelangganID'] == $data_penjualan['PelangganID']) ? 'selected' : ''; ?>
                            >

                                <?= htmlspecialchars($pelanggan['NamaPelanggan']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>


            <!-- PRODUK -->
            <h6 class="form-title">

                <i class="fa-solid fa-cake-candles me-2"></i>
                Pesanan Kue

            </h6>


            <div id="produk-container">


                <?php while ($detail = mysqli_fetch_assoc($query_detail)): ?>


                    <div class="produk-row">

                        <div class="row align-items-end">


                            <!-- PRODUK -->
                            <div class="col-md-6 mb-2">

                                <label class="form-label">
                                    Produk
                                </label>

                                <select
                                    name="ProdukID[]"
                                    class="form-select produk-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Produk --
                                    </option>


                                    <?php

                                    mysqli_data_seek($query_produk, 0);

                                    while ($produk = mysqli_fetch_assoc($query_produk)):

                                    ?>

                                        <option
                                            value="<?= $produk['ProdukID']; ?>"
                                            data-harga="<?= $produk['Harga']; ?>"
                                            <?= ($produk['ProdukID'] == $detail['ProdukID']) ? 'selected' : ''; ?>
                                        >

                                            <?= htmlspecialchars($produk['NamaProduk']); ?>

                                            -
                                            Rp <?= number_format($produk['Harga'], 0, ',', '.'); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <!-- JUMLAH -->
                            <div class="col-md-3 mb-2">

                                <label class="form-label">
                                    Jumlah
                                </label>

                                <input
                                    type="number"
                                    name="JumlahProduk[]"
                                    class="form-control jumlah-input"
                                    min="1"
                                    value="<?= $detail['JumlahProduk']; ?>"
                                    required
                                >

                            </div>


                            <!-- SUBTOTAL -->
                            <div class="col-md-2 mb-2">

                                <label class="form-label">
                                    Subtotal
                                </label>

                                <input
                                    type="text"
                                    class="form-control subtotal-input"
                                    value="Rp <?= number_format($detail['Subtotal'], 0, ',', '.'); ?>"
                                    readonly
                                >

                            </div>


                            <!-- HAPUS -->
                            <div class="col-md-1 mb-2">

                                <button
                                    type="button"
                                    class="btn-hapus"
                                    onclick="hapusProduk(this)"
                                >

                                    <i class="fa-solid fa-trash"></i>

                                </button>

                            </div>

                        </div>

                    </div>


                <?php endwhile; ?>


            </div>


            <!-- TAMBAH PRODUK -->
            <button
                type="button"
                class="btn-tambah mb-3"
                onclick="tambahProduk()"
            >

                <i class="fa-solid fa-plus me-1"></i>
                Tambah Produk

            </button>


            <!-- TOTAL -->
            <div class="total-box">

                <small>
                    Total Harga
                </small>

                <div
                    class="total-harga"
                    id="totalHarga"
                >
                    Rp <?= number_format($data_penjualan['TotalHarga'], 0, ',', '.'); ?>
                </div>

            </div>


            <!-- BUTTON -->
            <div class="text-end mt-4">

                <a
                    href="penjualan.php"
                    class="btn btn-light me-2"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    name="update"
                    class="btn-simpan"
                >

                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Simpan Perubahan

                </button>

            </div>


        </form>

    </div>

</div>


<script>

    // Tambah produk
    function tambahProduk() {

        const container =
            document.getElementById("produk-container");

        const barisPertama =
            document.querySelector(".produk-row");

        const barisBaru =
            barisPertama.cloneNode(true);


        barisBaru.querySelector(".produk-select").value = "";

        barisBaru.querySelector(".jumlah-input").value = 1;

        barisBaru.querySelector(".subtotal-input").value = "Rp 0";


        container.appendChild(barisBaru);

        hitungTotal();

    }


    // Hapus produk
    function hapusProduk(button) {

        const semuaBaris =
            document.querySelectorAll(".produk-row");


        if (semuaBaris.length > 1) {

            button.closest(".produk-row").remove();

        } else {

            alert("Minimal harus ada satu produk.");

        }


        hitungTotal();

    }


    // Hitung total
    function hitungTotal() {

        const semuaBaris =
            document.querySelectorAll(".produk-row");

        let total = 0;


        semuaBaris.forEach(function(baris) {

            const select =
                baris.querySelector(".produk-select");

            const jumlahInput =
                baris.querySelector(".jumlah-input");

            const subtotalInput =
                baris.querySelector(".subtotal-input");


            const option =
                select.options[select.selectedIndex];


            const harga =
                option
                ? parseFloat(option.getAttribute("data-harga")) || 0
                : 0;


            const jumlah =
                parseInt(jumlahInput.value) || 0;


            const subtotal =
                harga * jumlah;


            subtotalInput.value =
                "Rp " + subtotal.toLocaleString("id-ID");


            total += subtotal;

        });


        document.getElementById("totalHarga").innerText =
            "Rp " + total.toLocaleString("id-ID");

    }


    // Produk berubah
    document.addEventListener("change", function(e) {

        if (
            e.target.classList.contains("produk-select") ||
            e.target.classList.contains("jumlah-input")
        ) {

            hitungTotal();

        }

    });


    // Jumlah berubah
    document.addEventListener("input", function(e) {

        if (
            e.target.classList.contains("jumlah-input")
        ) {
h
            hitungTotal();

        }

    });


    // Hitung saat halaman dibuka
    hitungTotal();

</script>


</body>
</html>