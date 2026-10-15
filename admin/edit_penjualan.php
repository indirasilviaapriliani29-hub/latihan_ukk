<?php
include "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: penjualan.php");
    exit;
}

$id = (int) $_GET['id'];

/* DATA PENJUALAN */
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM penjualan WHERE PenjualanID = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data penjualan tidak ditemukan.";
    exit;
}

/* DATA PELANGGAN */
$data_pelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan ORDER BY NamaPelanggan ASC"
);

/* DATA TRANSAKSI */
$data_transaksi = mysqli_query(
    $koneksi,
    "SELECT transaksi.*, pelanggan.NamaPelanggan
     FROM transaksi
     LEFT JOIN pelanggan
     ON transaksi.PelangganID = pelanggan.PelangganID
     ORDER BY transaksi.TransaksiID DESC"
);

/* DATA PRODUK */
$data_produk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk ORDER BY NamaProduk ASC"
);

/* DETAIL PENJUALAN LAMA */
$data_detail = mysqli_query(
    $koneksi,
    "SELECT detailpenjualan.*, produk.NamaProduk, produk.Harga, produk.Stok
     FROM detailpenjualan
     LEFT JOIN produk
     ON detailpenjualan.ProdukID = produk.ProdukID
     WHERE detailpenjualan.PenjualanID = '$id'"
);

$detail_lama = [];

while ($detail = mysqli_fetch_assoc($data_detail)) {
    $detail_lama[] = $detail;
}


/* PROSES UPDATE */
if (isset($_POST['update'])) {

    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['TanggalPenjualan']
    );

    $pelanggan = (int) $_POST['PelangganID'];
    $transaksi = (int) $_POST['TransaksiID'];

    $produk_id = $_POST['ProdukID'] ?? [];
    $jumlah_produk = $_POST['JumlahProduk'] ?? [];


    /* KEMBALIKAN STOK LAMA */
    foreach ($detail_lama as $detail) {

        $produk_lama = (int) $detail['ProdukID'];
        $jumlah_lama = (int) $detail['JumlahProduk'];

        mysqli_query(
            $koneksi,
            "UPDATE produk
             SET Stok = Stok + $jumlah_lama
             WHERE ProdukID = '$produk_lama'"
        );
    }


    $total = 0;
    $detail_baru = [];
    $valid = true;
    $pesan = "";


    /* HITUNG PRODUK BARU */
    for ($i = 0; $i < count($produk_id); $i++) {

        $id_produk = (int) $produk_id[$i];
        $jumlah = (int) $jumlah_produk[$i];

        if ($id_produk == 0 || $jumlah <= 0) {
            continue;
        }


        $query_produk = mysqli_query(
            $koneksi,
            "SELECT * FROM produk
             WHERE ProdukID = '$id_produk'"
        );

        $produk = mysqli_fetch_assoc($query_produk);


        if (!$produk) {
            $valid = false;
            $pesan = "Produk tidak ditemukan.";
            break;
        }


        if ($jumlah > $produk['Stok']) {

            $valid = false;

            $pesan =
                "Stok produk " .
                $produk['NamaProduk'] .
                " tidak mencukupi.";

            break;
        }


        $harga = (float) $produk['Harga'];
        $subtotal = $harga * $jumlah;

        $total += $subtotal;


        $detail_baru[] = [
            'ProdukID' => $id_produk,
            'JumlahProduk' => $jumlah,
            'Subtotal' => $subtotal
        ];
    }


    /* MINIMAL 1 PRODUK */
    if (count($detail_baru) == 0) {

        $valid = false;
        $pesan = "Minimal pilih satu produk.";
    }


    /* JIKA VALIDASI GAGAL */
    if (!$valid) {

        foreach ($detail_lama as $detail) {

            $produk_lama = (int) $detail['ProdukID'];
            $jumlah_lama = (int) $detail['JumlahProduk'];

            mysqli_query(
                $koneksi,
                "UPDATE produk
                 SET Stok = Stok - $jumlah_lama
                 WHERE ProdukID = '$produk_lama'"
            );
        }

        echo "<script>
            alert(" . json_encode($pesan) . ");
            window.location.href = 'edit_penjualan.php?id=$id';
        </script>";

        exit;
    }


    /* UPDATE PENJUALAN */
    $update = mysqli_query(
        $koneksi,
        "UPDATE penjualan SET
            TanggalPenjualan = '$tanggal',
            TotalHarga = '$total',
            PelangganID = '$pelanggan',
            TransaksiID = '$transaksi'
         WHERE PenjualanID = '$id'"
    );


    if (!$update) {

        echo "Data penjualan gagal diubah: " .
             mysqli_error($koneksi);

        exit;
    }


    /* HAPUS DETAIL LAMA */
    $hapus_detail = mysqli_query(
        $koneksi,
        "DELETE FROM detailpenjualan
         WHERE PenjualanID = '$id'"
    );


    if (!$hapus_detail) {

        echo "Detail penjualan gagal diperbarui: " .
             mysqli_error($koneksi);

        exit;
    }


    /* INSERT DETAIL BARU */
    foreach ($detail_baru as $detail) {

        $produk = (int) $detail['ProdukID'];
        $jumlah = (int) $detail['JumlahProduk'];
        $subtotal = (float) $detail['Subtotal'];


        mysqli_query(
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
                '$id',
                '$produk',
                '$jumlah',
                '$subtotal'
            )"
        );


        /* KURANGI STOK */
        mysqli_query(
            $koneksi,
            "UPDATE produk
             SET Stok = Stok - $jumlah
             WHERE ProdukID = '$produk'"
        );
    }


    header("Location: penjualan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Penjualan - SweetBake</title>

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
            width: 240px;
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
            margin-left: 240px;
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

        .produk-row {
            background: #fff8f5;
            border: 1px solid #ead6d4;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
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

        .btn-add {
            background: #8f5f63;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 9px 15px;
        }

        .btn-add:hover {
            background: #70464a;
            color: white;
        }

        .btn-remove {
            border-radius: 9px;
        }

        .total-box {
            background: #fff1f0;
            border-radius: 12px;
            padding: 15px 20px;
            text-align: right;
        }

        .total-label {
            font-size: 14px;
            color: #777;
        }

        .total-harga {
            font-size: 24px;
            font-weight: 700;
            color: #70464a;
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

    <a href="penjualan.php" class="active">
        <i class="fa-solid fa-receipt"></i>
        Penjualan
    </a>

</div>


<!-- CONTENT -->

<div class="content">

    <div class="form-card">

        <h4 class="page-title">

            <i class="fa-solid fa-pen me-2"></i>

            Edit Penjualan

        </h4>

        <p class="text-muted mb-4">
            Ubah data penjualan SweetBake
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
                    value="<?= htmlspecialchars($data['TanggalPenjualan']); ?>"
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

                    <?php while ($pelanggan = mysqli_fetch_assoc($data_pelanggan)) { ?>

                        <option
                            value="<?= $pelanggan['PelangganID']; ?>"
                            <?= $pelanggan['PelangganID'] == $data['PelangganID'] ? 'selected' : ''; ?>>

                            <?= htmlspecialchars(
                                $pelanggan['NamaPelanggan']
                            ); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- TRANSAKSI -->

            <div class="mb-4">

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

                    <?php while ($transaksi = mysqli_fetch_assoc($data_transaksi)) { ?>

                        <option
                            value="<?= $transaksi['TransaksiID']; ?>"
                            <?= $transaksi['TransaksiID'] == $data['TransaksiID'] ? 'selected' : ''; ?>>

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

            </div>


            <!-- PRODUK -->

            <div class="mb-3">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <label class="form-label mb-0">
                        Produk Kue
                    </label>

                    <button
                        type="button"
                        class="btn btn-add"
                        onclick="tambahProduk()">

                        <i class="fa-solid fa-plus me-1"></i>
                        Tambah Produk

                    </button>

                </div>


                <div id="produk-container">

                    <?php foreach ($detail_lama as $detail) { ?>

                        <div class="produk-row">

                            <div class="row g-2 align-items-end">

                                <div class="col-md-5">

                                    <label class="form-label">
                                        Produk
                                    </label>

                                    <select
                                        name="ProdukID[]"
                                        class="form-select produk-select"
                                        required>

                                        <option value="">
                                            -- Pilih Produk --
                                        </option>

                                        <?php

                                        mysqli_data_seek(
                                            $data_produk,
                                            0
                                        );

                                        while ($produk = mysqli_fetch_assoc($data_produk)) {

                                        ?>

                                            <option
                                                value="<?= $produk['ProdukID']; ?>"
                                                data-harga="<?= $produk['Harga']; ?>"
                                                <?= $produk['ProdukID'] == $detail['ProdukID'] ? 'selected' : ''; ?>>

                                                <?= htmlspecialchars(
                                                    $produk['NamaProduk']
                                                ); ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>


                                <div class="col-md-3">

                                    <label class="form-label">
                                        Harga
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control harga"
                                        value="Rp <?= number_format($detail['Harga'], 0, ',', '.'); ?>"
                                        readonly>

                                </div>


                                <div class="col-md-2">

                                    <label class="form-label">
                                        Jumlah
                                    </label>

                                    <input
                                        type="number"
                                        name="JumlahProduk[]"
                                        class="form-control jumlah"
                                        value="<?= $detail['JumlahProduk']; ?>"
                                        min="1"
                                        required>

                                </div>


                                <div class="col-md-2">

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-remove w-100"
                                        onclick="hapusProduk(this)">

                                        <i class="fa-solid fa-trash"></i>

                                    </button>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            </div>


            <!-- TOTAL -->

            <div class="total-box mb-4">

                <div class="total-label">
                    Total Harga
                </div>

                <div
                    class="total-harga"
                    id="total-harga">

                    Rp 0

                </div>

            </div>


            <!-- BUTTON -->

            <div class="d-flex gap-2">

                <a
                    href="penjualan.php"
                    class="btn btn-back">

                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Kembali

                </a>

                <button
                    type="submit"
                    name="update"
                    class="btn btn-save">

                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>


<script>

function formatRupiah(angka) {

    return new Intl.NumberFormat(
        'id-ID'
    ).format(angka);

}


function hitungTotal() {

    let total = 0;

    document.querySelectorAll('.produk-row').forEach(function(row) {

        let select = row.querySelector('.produk-select');
        let jumlah = row.querySelector('.jumlah');

        if (!select || !jumlah) {
            return;
        }

        let option =
            select.options[select.selectedIndex];

        let harga =
            parseFloat(
                option.getAttribute('data-harga')
            ) || 0;

        let qty =
            parseInt(jumlah.value) || 0;

        total += harga * qty;

    });


    document.getElementById('total-harga').innerText =
        'Rp ' + formatRupiah(total);

}


function pasangEvent(row) {

    let select =
        row.querySelector('.produk-select');

    let jumlah =
        row.querySelector('.jumlah');

    let harga =
        row.querySelector('.harga');


    select.addEventListener(
        'change',
        function() {

            let option =
                select.options[select.selectedIndex];

            let nilai =
                parseFloat(
                    option.getAttribute('data-harga')
                ) || 0;

            harga.value =
                'Rp ' + formatRupiah(nilai);

            hitungTotal();

        }
    );


    jumlah.addEventListener(
        'input',
        hitungTotal
    );

}


function tambahProduk() {

    let container =
        document.getElementById('produk-container');

    let row =
        document.createElement('div');

    row.className = 'produk-row';


    row.innerHTML = `

        <div class="row g-2 align-items-end">

            <div class="col-md-5">

                <label class="form-label">
                    Produk
                </label>

                <select
                    name="ProdukID[]"
                    class="form-select produk-select"
                    required>

                    <option value="">
                        -- Pilih Produk --
                    </option>

                    <?php

                    mysqli_data_seek(
                        $data_produk,
                        0
                    );

                    while ($produk = mysqli_fetch_assoc($data_produk)) {

                    ?>

                        <option
                            value="<?= $produk['ProdukID']; ?>"
                            data-harga="<?= $produk['Harga']; ?>">

                            <?= htmlspecialchars(
                                $produk['NamaProduk']
                            ); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Harga
                </label>

                <input
                    type="text"
                    class="form-control harga"
                    value="Rp 0"
                    readonly>

            </div>


            <div class="col-md-2">

                <label class="form-label">
                    Jumlah
                </label>

                <input
                    type="number"
                    name="JumlahProduk[]"
                    class="form-control jumlah"
                    value="1"
                    min="1"
                    required>

            </div>


            <div class="col-md-2">

                <button
                    type="button"
                    class="btn btn-danger btn-remove w-100"
                    onclick="hapusProduk(this)">

                    <i class="fa-solid fa-trash"></i>

                </button>

            </div>

        </div>

    `;


    container.appendChild(row);

    pasangEvent(row);

}


function hapusProduk(button) {

    let row =
        button.closest('.produk-row');

    let container =
        document.getElementById('produk-container');


    if (container.children.length > 1) {

        row.remove();

        hitungTotal();

    }

}


document.querySelectorAll('.produk-row').forEach(function(row) {

    pasangEvent(row);

});


hitungTotal();

</script>

</body>

</html>