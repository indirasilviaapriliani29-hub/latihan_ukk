<?php

include "../koneksi.php";

$pesan = "";

/* =====================================================
   PROSES PESANAN
===================================================== */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /* =================================================
       DATA PELANGGAN
    ================================================= */

    $pelanggan_id = isset($_POST['PelangganID'])
        ? (int) $_POST['PelangganID']
        : 0;

    $nama_baru = isset($_POST['NamaPelangganBaru'])
        ? trim($_POST['NamaPelangganBaru'])
        : "";

    $alamat_baru = isset($_POST['AlamatBaru'])
        ? trim($_POST['AlamatBaru'])
        : "";

    $telepon_baru = isset($_POST['NomorTeleponBaru'])
        ? trim($_POST['NomorTeleponBaru'])
        : "";


    /* =================================================
       DATA PRODUK
    ================================================= */

    $produk_ids = isset($_POST['ProdukID'])
        ? $_POST['ProdukID']
        : [];

    $jumlah_produk = isset($_POST['JumlahProduk'])
        ? $_POST['JumlahProduk']
        : [];


    /* =================================================
       VALIDASI PELANGGAN
    ================================================= */

    if ($pelanggan_id <= 0 && $nama_baru == "") {

        $pesan = "Silakan pilih pelanggan atau masukkan pelanggan baru.";

    } else {

        /* =================================================
           CEK PRODUK
        ================================================= */

        $produk_dipilih = [];

        if (is_array($produk_ids)) {

            foreach ($produk_ids as $id) {

                $id = (int) $id;

                /*
                 * JUMLAH SEKARANG DIAMBIL BERDASARKAN ProdukID
                 * Jadi tidak akan salah ketika memilih produk
                 * yang berada di urutan tertentu.
                 */

                $qty = isset($jumlah_produk[$id])
                    ? (int) $jumlah_produk[$id]
                    : 0;

                if ($id > 0 && $qty > 0) {

                    $produk_dipilih[] = [
                        'id' => $id,
                        'jumlah' => $qty
                    ];
                }
            }
        }


        if (count($produk_dipilih) == 0) {

            $pesan = "Silakan pilih minimal satu produk.";

        } else {

            mysqli_begin_transaction($koneksi);

            try {

                /* =================================================
                   PELANGGAN BARU
                ================================================= */

                if ($pelanggan_id <= 0) {

                    $nama_baru = mysqli_real_escape_string(
                        $koneksi,
                        $nama_baru
                    );

                    $alamat_baru = mysqli_real_escape_string(
                        $koneksi,
                        $alamat_baru
                    );

                    $telepon_baru = mysqli_real_escape_string(
                        $koneksi,
                        $telepon_baru
                    );


                    $query_pelanggan = mysqli_query(
                        $koneksi,
                        "
                        INSERT INTO pelanggan
                        (
                            NamaPelanggan,
                            Alamat,
                            NomorTelepon
                        )
                        VALUES
                        (
                            '$nama_baru',
                            '$alamat_baru',
                            '$telepon_baru'
                        )
                        "
                    );


                    if (!$query_pelanggan) {

                        throw new Exception(
                            "Gagal menambahkan pelanggan: " .
                            mysqli_error($koneksi)
                        );
                    }


                    $pelanggan_id = mysqli_insert_id($koneksi);
                }


                /* =================================================
                   CEK PRODUK DAN HITUNG TOTAL
                ================================================= */

                $total_harga = 0;

                $detail_produk = [];


                foreach ($produk_dipilih as $item) {

                    $produk_id = (int) $item['id'];

                    $jumlah = (int) $item['jumlah'];


                    $query_produk = mysqli_query(
                        $koneksi,
                        "
                        SELECT
                            ProdukID,
                            NamaProduk,
                            Harga,
                            Stok
                        FROM produk
                        WHERE ProdukID = '$produk_id'
                        LIMIT 1
                        "
                    );


                    if (!$query_produk) {

                        throw new Exception(
                            "Gagal mengambil data produk: " .
                            mysqli_error($koneksi)
                        );
                    }


                    if (mysqli_num_rows($query_produk) == 0) {

                        throw new Exception(
                            "Produk dengan ID " .
                            $produk_id .
                            " tidak ditemukan."
                        );
                    }


                    $produk = mysqli_fetch_assoc($query_produk);

                    $stok = (int) $produk['Stok'];

                    $harga = (float) $produk['Harga'];


                    /* =================================================
                       CEK STOK
                    ================================================= */

                    if ($jumlah > $stok) {

                        throw new Exception(
                            "Stok " .
                            $produk['NamaProduk'] .
                            " tidak mencukupi. Stok tersedia: " .
                            $stok
                        );
                    }


                    $subtotal = $harga * $jumlah;

                    $total_harga += $subtotal;


                    $detail_produk[] = [

                        'ProdukID' => $produk_id,

                        'NamaProduk' => $produk['NamaProduk'],

                        'Harga' => $harga,

                        'Jumlah' => $jumlah,

                        'Subtotal' => $subtotal
                    ];
                }


                /* =================================================
                   SIMPAN TRANSAKSI
                ================================================= */

                $query_transaksi = mysqli_query(
                    $koneksi,
                    "
                    INSERT INTO transaksi
                    (
                        TanggalTransaksi,
                        PelangganID,
                        MetodePembayaran,
                        StatusTransaksi,
                        UangBayar,
                        Kembalian
                    )
                    VALUES
                    (
                        CURDATE(),
                        '$pelanggan_id',
                        '',
                        'Proses',
                        0,
                        0
                    )
                    "
                );


                if (!$query_transaksi) {

                    throw new Exception(
                        "Gagal membuat transaksi: " .
                        mysqli_error($koneksi)
                    );
                }


                $transaksi_id = mysqli_insert_id($koneksi);


                /* =================================================
                   SIMPAN PENJUALAN
                ================================================= */

                $query_penjualan = mysqli_query(
                    $koneksi,
                    "
                    INSERT INTO penjualan
                    (
                        TanggalPenjualan,
                        TotalHarga,
                        PelangganID,
                        TransaksiID
                    )
                    VALUES
                    (
                        CURDATE(),
                        '$total_harga',
                        '$pelanggan_id',
                        '$transaksi_id'
                    )
                    "
                );


                if (!$query_penjualan) {

                    throw new Exception(
                        "Gagal menyimpan penjualan: " .
                        mysqli_error($koneksi)
                    );
                }


                $penjualan_id = mysqli_insert_id($koneksi);


                /* =================================================
                   DETAIL PENJUALAN + STOK
                ================================================= */

                foreach ($detail_produk as $item) {

                    $produk_id = (int) $item['ProdukID'];

                    $jumlah = (int) $item['Jumlah'];

                    $subtotal = (float) $item['Subtotal'];


                    /* =============================================
                       SIMPAN DETAIL
                    ============================================= */

                    $query_detail = mysqli_query(
                        $koneksi,
                        "
                        INSERT INTO detailpenjualan
                        (
                            PenjualanID,
                            ProdukID,
                            JumlahProduk,
                            Subtotal
                        )
                        VALUES
                        (
                            '$penjualan_id',
                            '$produk_id',
                            '$jumlah',
                            '$subtotal'
                        )
                        "
                    );


                    if (!$query_detail) {

                        throw new Exception(
                            "Gagal menyimpan detail penjualan: " .
                            mysqli_error($koneksi)
                        );
                    }


                    /* =============================================
                       KURANGI STOK
                    ============================================= */

                    $query_stok = mysqli_query(
                        $koneksi,
                        "
                        UPDATE produk
                        SET Stok = Stok - '$jumlah'
                        WHERE ProdukID = '$produk_id'
                        "
                    );


                    if (!$query_stok) {

                        throw new Exception(
                            "Gagal mengurangi stok produk: " .
                            mysqli_error($koneksi)
                        );
                    }
                }


                /* =================================================
                   COMMIT
                ================================================= */

                mysqli_commit($koneksi);


                /* =================================================
                   REDIRECT
                ================================================= */

                header(
                    "Location: kasir.php?id=" . $transaksi_id
                );

                exit;


            } catch (Exception $e) {

                mysqli_rollback($koneksi);

                $pesan = $e->getMessage();
            }
        }
    }
}


/* =====================================================
   DATA PELANGGAN
===================================================== */

$query_pelanggan = mysqli_query(
    $koneksi,
    "
    SELECT
        PelangganID,
        NamaPelanggan,
        NomorTelepon
    FROM pelanggan
    ORDER BY NamaPelanggan ASC
    "
);


/* =====================================================
   DATA PRODUK
===================================================== */

$query_produk = mysqli_query(
    $koneksi,
    "
    SELECT
        ProdukID,
        NamaProduk,
        Harga,
        Stok,
        foto
    FROM produk
    WHERE Stok > 0
    ORDER BY NamaProduk ASC
    "
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pesanan Baru | SweetBake</title>


    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- FONT AWESOME -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >


    <!-- POPPINS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #fff8f5;
            color: #4b3832;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #8f5f63;
            padding: 25px 15px;
            color: white;
            z-index: 1000;
        }


        .logo {
            text-align: center;
            margin-bottom: 35px;
        }


        .logo i {
            font-size: 30px;
            margin-bottom: 8px;
        }


        .logo h4 {
            margin: 0;
            font-weight: 600;
        }


        .menu {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            padding: 13px 15px;
            margin-bottom: 8px;
            border-radius: 10px;
            transition: 0.2s;
        }


        .menu:hover {
            background: rgba(255,255,255,0.15);
            color: white;
        }


        .menu.active {
            background: #70464a;
        }


        .menu i {
            width: 22px;
            text-align: center;
        }


        .logout-box {
            position: absolute;
            left: 15px;
            right: 15px;
            bottom: 25px;
        }


        .logout {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            padding: 13px 15px;
            border-radius: 10px;
            background: rgba(0,0,0,0.08);
        }


        .logout:hover {
            background: rgba(0,0,0,0.15);
            color: white;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {
            margin-left: 250px;
            padding: 35px;
        }


        .page-title {
            margin-bottom: 25px;
        }


        .page-title h2 {
            font-weight: 600;
            margin-bottom: 5px;
        }


        .page-title p {
            color: #8c7771;
            margin: 0;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .card-custom {
            background: white;
            border: none;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(112,70,74,0.08);
            padding: 25px;
        }


        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #70464a;
            margin-bottom: 20px;
        }


        /* =====================================================
           FORM
        ===================================================== */

        .form-label {
            font-weight: 500;
        }


        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #eadbd7;
            padding: 11px 13px;
        }


        .form-control:focus,
        .form-select:focus {
            border-color: #d98f96;
            box-shadow: 0 0 0 0.2rem rgba(217,143,150,0.15);
        }


        .new-customer {
            background: #fff8f5;
            border: 1px solid #f0dfda;
            border-radius: 14px;
            padding: 20px;
            margin-top: 15px;
        }


        /* =====================================================
           PRODUK
        ===================================================== */

        .produk-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 18px;
        }


        .produk-card {
            position: relative;
            background: white;
            border: 2px solid #eee2de;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: 0.2s;
            height: 100%;
        }


        .produk-card:hover {
            transform: translateY(-3px);
            border-color: #d98f96;
            box-shadow: 0 7px 18px rgba(112,70,74,0.12);
        }


        .produk-card.selected {
            border-color: #8f5f63;
            background: #fff8f5;
            box-shadow: 0 0 0 2px rgba(143,95,99,0.15);
        }


        .produk-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
            width: 23px;
            height: 23px;
            accent-color: #8f5f63;
        }


        /* =====================================================
           FOTO PRODUK
        ===================================================== */

        .produk-gambar {
            display: block;
            width: 100%;
            height: 165px;
            object-fit: cover;
            object-position: center;
            background: #f5e8e4;
        }


        .produk-info {
            padding: 13px;
        }


        .produk-nama {
            font-size: 15px;
            font-weight: 600;
            color: #70464a;
            margin-bottom: 5px;
        }


        .produk-harga {
            font-size: 14px;
            font-weight: 600;
            color: #8f5f63;
            margin-bottom: 4px;
        }


        .produk-stok {
            font-size: 12px;
            color: #8c7771;
            margin-bottom: 12px;
        }


        .jumlah-box {
            display: none;
            padding-top: 8px;
        }


        .produk-card.selected .jumlah-box {
            display: block;
        }


        .jumlah-input {
            width: 100%;
            border: 1px solid #dfcfca;
            border-radius: 8px;
            padding: 7px;
            text-align: center;
        }


        /* =====================================================
           TOTAL
        ===================================================== */

        .total-box {
            background: #f8e6e3;
            border-radius: 14px;
            padding: 20px;
            margin-top: 25px;
        }


        .total-label {
            color: #806a65;
            font-size: 14px;
        }


        .total-value {
            color: #70464a;
            font-size: 28px;
            font-weight: 700;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .btn-simpan {
            background: #8f5f63;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
        }


        .btn-simpan:hover {
            background: #70464a;
            color: white;
        }


        .btn-batal {
            background: #eee4e1;
            color: #604b46;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
        }


        .btn-batal:hover {
            background: #e2d5d1;
            color: #604b46;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }


            .logo h4,
            .menu span,
            .logout span {
                display: none;
            }


            .logo {
                margin-bottom: 25px;
            }


            .menu {
                justify-content: center;
                padding: 13px 5px;
            }


            .menu i {
                width: auto;
            }


            .logout {
                justify-content: center;
                padding: 13px 5px;
            }


            .content {
                margin-left: 70px;
                padding: 20px;
            }


            .produk-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }


        @media (max-width: 450px) {

            .produk-container {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">

    <div class="logo">

        <i class="fa-solid fa-cake-candles"></i>

        <h4>SweetBake</h4>

    </div>


    <a
        href="../index.php"
        class="menu"
    >

        <i class="fa-solid fa-house"></i>

        <span>Toko</span>

    </a>


    <a
        href="kasir.php"
        class="menu"
    >

        <i class="fa-solid fa-cash-register"></i>

        <span>Kasir</span>

    </a>


    <a
        href="pesanan_baru.php"
        class="menu active"
    >

        <i class="fa-solid fa-cart-plus"></i>

        <span>Pesanan Baru</span>

    </a>


    <div class="logout-box">

        <a
            href="../logout.php"
            class="logout"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>Logout</span>

        </a>

    </div>

</div>


<!-- =====================================================
     CONTENT
===================================================== -->

<div class="content">


    <div class="page-title">

        <h2>

            <i class="fa-solid fa-cart-plus me-2"></i>

            Pesanan Baru

        </h2>


        <p>
            Pilih pelanggan dan produk yang ingin dipesan.
        </p>

    </div>


    <!-- PESAN ERROR -->

    <?php if ($pesan != "") { ?>

        <div class="alert alert-danger">

            <i class="fa-solid fa-circle-exclamation me-2"></i>

            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8'); ?>

        </div>

    <?php } ?>


    <!-- FORM -->

    <form
        method="POST"
        id="formPesanan"
    >


        <div class="row g-4">


            <!-- =================================================
                 PELANGGAN
            ================================================== -->

            <div class="col-lg-4">

                <div class="card-custom">


                    <div class="card-title">

                        <i class="fa-solid fa-user me-2"></i>

                        Data Pelanggan

                    </div>


                    <label class="form-label">

                        Pilih Pelanggan

                    </label>


                    <select
                        name="PelangganID"
                        id="PelangganID"
                        class="form-select"
                        onchange="cekPelanggan()"
                    >

                        <option value="0">

                            -- Pelanggan Baru --

                        </option>


                        <?php while ($pelanggan = mysqli_fetch_assoc($query_pelanggan)) { ?>

                            <option
                                value="<?= (int) $pelanggan['PelangganID']; ?>"
                            >

                                <?= htmlspecialchars(
                                    $pelanggan['NamaPelanggan'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>


                                <?php if (!empty($pelanggan['NomorTelepon'])) { ?>

                                    -
                                    <?= htmlspecialchars(
                                        $pelanggan['NomorTelepon'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                <?php } ?>

                            </option>

                        <?php } ?>

                    </select>


                    <!-- PELANGGAN BARU -->

                    <div
                        class="new-customer"
                        id="pelangganBaru"
                    >

                        <div class="mb-3">

                            <label class="form-label">

                                Nama Pelanggan

                            </label>


                            <input
                                type="text"
                                name="NamaPelangganBaru"
                                id="NamaPelangganBaru"
                                class="form-control"
                                placeholder="Nama pelanggan"
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">

                                Nomor Telepon

                            </label>


                            <input
                                type="text"
                                name="NomorTeleponBaru"
                                class="form-control"
                                placeholder="08123456789"
                            >

                        </div>


                        <div>

                            <label class="form-label">

                                Alamat

                            </label>


                            <textarea
                                name="AlamatBaru"
                                class="form-control"
                                rows="3"
                                placeholder="Alamat pelanggan"
                            ></textarea>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 PRODUK
            ================================================== -->

            <div class="col-lg-8">

                <div class="card-custom">


                    <div class="card-title">

                        <i class="fa-solid fa-box-open me-2"></i>

                        Pilih Produk

                    </div>


                    <p
                        class="text-muted"
                        style="font-size: 13px;"
                    >

                        Klik produk yang ingin dipesan.
                        Kamu bisa memilih lebih dari satu produk.

                    </p>


                    <div class="produk-container">


                        <?php while ($produk = mysqli_fetch_assoc($query_produk)) { ?>


                            <?php

                            /* =================================================
                               FOTO PRODUK
                            ================================================= */

                            $foto = trim($produk['foto'] ?? '');

                            $foto = urldecode($foto);

                            /*
                             * Kalau database berisi:
                             * brownies.jpg
                             *
                             * maka yang dipakai:
                             * brownies.jpg
                             */

                            $nama_file = basename(
                                str_replace(
                                    '\\',
                                    '/',
                                    $foto
                                )
                            );


                            /*
                             * DEFAULT FOTO
                             */

                            $foto_path = "../assets/img/no-image.jpg";


                            /*
                             * LOKASI FOTO DI SERVER
                             */

                            $folder_foto =
                                __DIR__ .
                                "/../assets/img/";


                            /*
                             * CEK FOTO
                             */

                            if ($nama_file != '') {

                                $file_server =
                                    $folder_foto .
                                    $nama_file;


                                if (is_file($file_server)) {

                                    /*
                                     * Untuk nama file yang mempunyai
                                     * spasi atau karakter khusus.
                                     */

                                    $foto_path =
                                        "../assets/img/" .
                                        rawurlencode($nama_file);
                                }
                            }

                            ?>


                            <!-- KARTU PRODUK -->

                            <div
                                class="produk-card"
                                id="card-<?= (int) $produk['ProdukID']; ?>"
                                onclick="pilihProduk(<?= (int) $produk['ProdukID']; ?>)"
                            >


                                <!-- CHECKBOX -->

                                <input
                                    type="checkbox"
                                    class="produk-checkbox"
                                    name="ProdukID[]"
                                    value="<?= (int) $produk['ProdukID']; ?>"
                                    id="produk-<?= (int) $produk['ProdukID']; ?>"
                                    onclick="event.stopPropagation();"
                                    onchange="ubahProduk(<?= (int) $produk['ProdukID']; ?>)"
                                >


                                <!-- FOTO PRODUK -->

                                <img
                                    src="<?= htmlspecialchars(
                                        $foto_path,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="produk-gambar"
                                    alt="<?= htmlspecialchars(
                                        $produk['NamaProduk'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    onerror="this.onerror=null; this.src='../assets/img/no-image.jpg';"
                                >


                                <!-- INFO PRODUK -->

                                <div class="produk-info">


                                    <div class="produk-nama">

                                        <?= htmlspecialchars(
                                            $produk['NamaProduk'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </div>


                                    <div class="produk-harga">

                                        Rp
                                        <?= number_format(
                                            (float) $produk['Harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                    </div>


                                    <div class="produk-stok">

                                        Stok:
                                        <?= (int) $produk['Stok']; ?>

                                    </div>


                                    <!-- JUMLAH -->

                                    <div class="jumlah-box">

                                        <label
                                            class="form-label"
                                            style="font-size:12px;"
                                        >

                                            Jumlah

                                        </label>


                                        <!--
                                            PENTING:
                                            JumlahProduk[ProdukID]

                                            Bukan:
                                            JumlahProduk[]

                                            Ini memperbaiki bug ketika
                                            memilih produk tertentu.
                                        -->

                                        <input
                                            type="number"
                                            name="JumlahProduk[<?= (int) $produk['ProdukID']; ?>]"
                                            id="jumlah-<?= (int) $produk['ProdukID']; ?>"
                                            class="jumlah-input"
                                            value="1"
                                            min="1"
                                            max="<?= (int) $produk['Stok']; ?>"
                                            onclick="event.stopPropagation();"
                                            oninput="hitungTotal();"
                                        >

                                    </div>

                                </div>

                            </div>


                        <?php } ?>


                    </div>


                    <!-- TOTAL -->

                    <div class="total-box">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <div class="total-label">

                                    Total Pesanan

                                </div>


                                <small class="text-muted">

                                    Semua produk yang dipilih

                                </small>

                            </div>


                            <div
                                class="total-value"
                                id="totalHarga"
                            >

                                Rp 0

                            </div>

                        </div>

                    </div>


                    <!-- BUTTON -->

                    <div
                        class="d-flex justify-content-end gap-2 mt-4"
                    >

                        <a
                            href="kasir.php"
                            class="btn btn-batal"
                        >

                            <i class="fa-solid fa-arrow-left me-2"></i>

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn btn-simpan"
                        >

                            <i class="fa-solid fa-check me-2"></i>

                            Buat Pesanan

                        </button>

                    </div>


                </div>

            </div>

        </div>

    </form>

</div>


<script>


/* =====================================================
   PILIH PRODUK
===================================================== */

function pilihProduk(id) {

    const checkbox =
        document.getElementById(
            "produk-" + id
        );


    checkbox.checked =
        !checkbox.checked;


    ubahProduk(id);
}



/* =====================================================
   UBAH PRODUK
===================================================== */

function ubahProduk(id) {

    const checkbox =
        document.getElementById(
            "produk-" + id
        );


    const card =
        document.getElementById(
            "card-" + id
        );


    if (checkbox.checked) {

        card.classList.add("selected");

    } else {

        card.classList.remove("selected");

        document.getElementById(
            "jumlah-" + id
        ).value = 1;
    }


    hitungTotal();
}



/* =====================================================
   HITUNG TOTAL
===================================================== */

function hitungTotal() {

    let total = 0;


    document
        .querySelectorAll(".produk-checkbox")
        .forEach(function(checkbox) {


            if (checkbox.checked) {


                const id =
                    checkbox.value;


                const card =
                    document.getElementById(
                        "card-" + id
                    );


                const hargaText =
                    card
                        .querySelector(".produk-harga")
                        .innerText
                        .replace("Rp", "")
                        .replace(/\./g, "")
                        .trim();


                const harga =
                    parseFloat(hargaText) || 0;


                const jumlah =
                    parseInt(
                        document.getElementById(
                            "jumlah-" + id
                        ).value
                    ) || 0;


                total +=
                    harga * jumlah;
            }

        });


    document.getElementById(
        "totalHarga"
    ).innerHTML =
        "Rp " +
        total.toLocaleString("id-ID");
}



/* =====================================================
   PELANGGAN
===================================================== */

function cekPelanggan() {

    const pelanggan =
        document.getElementById(
            "PelangganID"
        ).value;


    const pelangganBaru =
        document.getElementById(
            "pelangganBaru"
        );


    const nama =
        document.getElementById(
            "NamaPelangganBaru"
        );


    if (pelanggan == "0") {

        pelangganBaru.style.display =
            "block";

        nama.required = true;

    } else {

        pelangganBaru.style.display =
            "none";

        nama.required = false;

        nama.value = "";
    }
}



/* =====================================================
   VALIDASI FORM
===================================================== */

document
    .getElementById("formPesanan")
    .addEventListener(
        "submit",
        function(event) {


            const produkDipilih =
                document.querySelectorAll(
                    ".produk-checkbox:checked"
                );


            if (produkDipilih.length == 0) {

                event.preventDefault();

                alert(
                    "Silakan pilih minimal satu produk."
                );

                return false;
            }


            let valid = true;


            produkDipilih.forEach(
                function(checkbox) {


                    const id =
                        checkbox.value;


                    const jumlah =
                        parseInt(
                            document.getElementById(
                                "jumlah-" + id
                            ).value
                        ) || 0;


                    if (jumlah <= 0) {

                        valid = false;
                    }

                }
            );


            if (!valid) {

                event.preventDefault();

                alert(
                    "Jumlah produk harus lebih dari 0."
                );

                return false;
            }


            return true;

        }
    );



/* =====================================================
   SAAT HALAMAN DIBUKA
===================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        cekPelanggan();

        hitungTotal();

    }
);

</script>


</body>

</html>