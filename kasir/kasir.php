<?php

include "../koneksi.php";


// ==========================================
// AMBIL SEMUA TRANSAKSI
// ==========================================

$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT
        transaksi.TransaksiID,
        transaksi.TanggalTransaksi,
        transaksi.MetodePembayaran,
        transaksi.StatusTransaksi,
        transaksi.UangBayar,
        transaksi.Kembalian,
        pelanggan.NamaPelanggan,
        penjualan.PenjualanID,
        penjualan.TotalHarga
    FROM transaksi
    LEFT JOIN pelanggan
        ON transaksi.PelangganID = pelanggan.PelangganID
    LEFT JOIN penjualan
        ON transaksi.TransaksiID = penjualan.TransaksiID
    ORDER BY transaksi.TransaksiID DESC"
);


// ==========================================
// TRANSAKSI YANG DIPILIH
// ==========================================

$pilih_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


// Kalau belum pilih transaksi,
// ambil transaksi yang masih Proses

if ($pilih_id == 0) {

    $cek = mysqli_query(
        $koneksi,
        "SELECT TransaksiID
         FROM transaksi
         WHERE StatusTransaksi = 'Proses'
         ORDER BY TransaksiID DESC
         LIMIT 1"
    );

    if ($cek && mysqli_num_rows($cek) > 0) {

        $data_pertama = mysqli_fetch_assoc($cek);

        $pilih_id = $data_pertama['TransaksiID'];

    }

}


// ==========================================
// DETAIL TRANSAKSI
// ==========================================

$transaksi = null;
$detail_produk = [];


if ($pilih_id > 0) {

    $query_detail = mysqli_query(
        $koneksi,
        "SELECT
            transaksi.TransaksiID,
            transaksi.TanggalTransaksi,
            transaksi.MetodePembayaran,
            transaksi.StatusTransaksi,
            transaksi.UangBayar,
            transaksi.Kembalian,

            pelanggan.NamaPelanggan,
            pelanggan.NomorTelepon,
            pelanggan.Alamat,

            penjualan.PenjualanID,
            penjualan.TotalHarga

        FROM transaksi

        LEFT JOIN pelanggan
            ON transaksi.PelangganID = pelanggan.PelangganID

        LEFT JOIN penjualan
            ON transaksi.TransaksiID = penjualan.TransaksiID

        WHERE transaksi.TransaksiID = '$pilih_id'

        LIMIT 1"
    );


    if ($query_detail) {

        $transaksi = mysqli_fetch_assoc(
            $query_detail
        );

    }


    // ======================================
    // DETAIL PRODUK
    // ======================================

    if ($transaksi) {

        $penjualan_id =
            $transaksi['PenjualanID'];


        $query_produk = mysqli_query(
            $koneksi,
            "SELECT
                detailpenjualan.ProdukID,
                detailpenjualan.JumlahProduk,
                detailpenjualan.Subtotal,

                produk.NamaProduk,
                produk.Harga,
                produk.foto

            FROM detailpenjualan

            LEFT JOIN produk
                ON detailpenjualan.ProdukID = produk.ProdukID

            WHERE detailpenjualan.PenjualanID = '$penjualan_id'"
        );


        if ($query_produk) {

            while (
                $row = mysqli_fetch_assoc(
                    $query_produk
                )
            ) {

                $detail_produk[] = $row;

            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kasir Pembayaran | SweetBake</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


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
            color: #70464a;
        }

        /* SIDEBAR */

        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #8f5f63;
            color: white;
            padding: 25px 15px;
            z-index: 1000;
        }

        .brand {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 35px;
        }

        .brand i {
            color: #f3c4c7;
            margin-right: 5px;
        }

        .menu {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 7px;
            transition: .2s;
        }

        .menu:hover {
            background: rgba(255,255,255,.12);
            color: white;
        }

        .menu.active {
            background: #e8a9ad;
            color: #70464a;
            font-weight: 600;
        }

        .menu i {
            width: 25px;
            text-align: center;
        }

        .logout-box {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,.25);
        }

        /* CONTENT */

        .content {
            margin-left: 240px;
            padding: 30px;
            min-height: 100vh;
        }

        .top-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .top-title h2 {
            margin: 0;
            font-weight: 700;
            color: #70464a;
        }

        /* CARD */

        .card-box {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow:
                0 5px 20px
                rgba(112,70,74,.08);
        }

        /* PESANAN */

        .order-item {
            display: block;
            text-decoration: none;
            color: #70464a;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 12px;
            margin-bottom: 10px;
            transition: .2s;
        }

        .order-item:hover {
            background: #fff5f4;
            border-color: #e8a9ad;
        }

        .order-item.active {
            background: #f8e0e1;
            border-color: #d99ba1;
        }

        .order-id {
            font-weight: 700;
        }

        .order-name {
            font-size: 14px;
        }

        .order-total {
            color: #b86f78;
            font-weight: 700;
        }

        .badge-proses {
            background: #f5c76d;
            color: #70464a;
        }

        .badge-selesai {
            background: #a8d5ba;
            color: #315a40;
        }

        /* PELANGGAN */

        .customer-box {
            background: #fff7f5;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .customer-box h6 {
            font-weight: 700;
        }

        .customer-box p {
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* PRODUK */

        .product-row {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .product-img {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
        }

        .product-price {
            font-size: 13px;
            color: #8b7375;
        }

        .subtotal {
            font-weight: 700;
            color: #b86f78;
            white-space: nowrap;
        }

        /* TOTAL */

        .total-box {
            background: #fff0ef;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .total-label {
            font-size: 13px;
            color: #8b7375;
        }

        .total-price {
            font-size: 28px;
            font-weight: 700;
            color: #b86f78;
        }

        /* FORM */

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 11px;
            border-color: #e5d5d5;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #d99ba1;
            box-shadow:
                0 0 0 .2rem
                rgba(217,155,161,.2);
        }

        .btn-bayar {
            width: 100%;
            background: #b86f78;
            border: none;
            color: white;
            padding: 13px;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 15px;
        }

        .btn-bayar:hover {
            background: #8f5f63;
            color: white;
        }

        .btn-pesanan {
            background: #b86f78;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 18px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-pesanan:hover {
            background: #8f5f63;
            color: white;
        }

        /* KOSONG */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #9a8587;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 75px;
                padding: 20px 10px;
            }

            .brand {
                font-size: 0;
            }

            .brand i {
                font-size: 22px;
                margin: 0;
            }

            .menu {
                text-align: center;
                padding: 12px 5px;
                font-size: 0;
            }

            .menu i {
                width: auto;
                font-size: 18px;
            }

            .content {
                margin-left: 75px;
                padding: 20px;
            }

            .top-title h2 {
                font-size: 20px;
            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     SIDEBAR
========================================== -->

<div class="sidebar">

    <div class="brand">

        <i class="fa-solid fa-cake-candles"></i>

        SweetBake

    </div>


    <a
        href="../index.php"
        class="menu"
    >

        <i class="fa-solid fa-house"></i>

        Toko

    </a>


    <a
        href="kasir.php"
        class="menu active"
    >

        <i class="fa-solid fa-cash-register"></i>

        Kasir

    </a>


    <div class="logout-box">

        <a
            href="../logout.php"
            class="menu"
            onclick="return confirm('Yakin ingin logout?')"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</div>


<!-- ==========================================
     CONTENT
========================================== -->

<div class="content">


    <div class="top-title">

        <div>

            <h2>

                <i class="fa-solid fa-cash-register me-2"></i>

                Kasir Pembayaran

            </h2>

            <small class="text-muted">

                Kelola pesanan pelanggan SweetBake

            </small>

        </div>


        <!-- PESANAN BARU -->

        <a
            href="pesanan_baru.php"
            class="btn-pesanan"
        >

            <i class="fa-solid fa-plus me-2"></i>

            Pesanan Baru

        </a>

    </div>


    <div class="row g-4">


        <!-- ======================================
             DAFTAR PESANAN
        ======================================= -->

        <div class="col-lg-5">

            <div class="card-box">

                <h5 class="fw-bold mb-3">

                    <i class="fa-solid fa-receipt me-2"></i>

                    Daftar Pesanan

                </h5>


                <?php if (
                    $query_transaksi &&
                    mysqli_num_rows($query_transaksi) > 0
                ) { ?>


                    <?php while (
                        $data =
                        mysqli_fetch_assoc(
                            $query_transaksi
                        )
                    ) { ?>


                        <a
                            href="kasir.php?id=<?= $data['TransaksiID']; ?>"
                            class="order-item <?= (
                                $pilih_id ==
                                $data['TransaksiID']
                            )
                                ? 'active'
                                : ''; ?>"
                        >

                            <div class="d-flex justify-content-between">

                                <span class="order-id">

                                    #TRX<?= $data['TransaksiID']; ?>

                                </span>


                                <?php if (
                                    $data['StatusTransaksi']
                                    == 'Selesai'
                                ) { ?>

                                    <span class="badge badge-selesai">

                                        Selesai

                                    </span>

                                <?php } else { ?>

                                    <span class="badge badge-proses">

                                        Proses

                                    </span>

                                <?php } ?>

                            </div>


                            <div class="order-name mt-2">

                                <i class="fa-solid fa-user me-1"></i>

                                <?= htmlspecialchars(
                                    $data['NamaPelanggan']
                                    ?? 'Umum'
                                ); ?>

                            </div>


                            <div class="d-flex justify-content-between mt-2">

                                <small class="text-muted">

                                    <?= date(
                                        'd-m-Y',
                                        strtotime(
                                            $data['TanggalTransaksi']
                                        )
                                    ); ?>

                                </small>


                                <span class="order-total">

                                    Rp
                                    <?= number_format(
                                        $data['TotalHarga'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </span>

                            </div>

                        </a>


                    <?php } ?>


                <?php } else { ?>


                    <div class="empty">

                        <i
                            class="fa-solid fa-cart-shopping"
                            style="font-size:45px;"
                        ></i>

                        <p class="mt-3">

                            Belum ada pesanan.

                        </p>

                    </div>


                <?php } ?>

            </div>

        </div>


        <!-- ======================================
             DETAIL TRANSAKSI
        ======================================= -->

        <div class="col-lg-7">

            <div class="card-box">


                <?php if ($transaksi) { ?>


                    <div class="d-flex justify-content-between mb-3">

                        <div>

                            <h5 class="fw-bold mb-1">

                                Detail Pesanan

                            </h5>

                            <small class="text-muted">

                                #TRX<?= $transaksi['TransaksiID']; ?>

                            </small>

                        </div>


                        <span class="badge
                            <?= (
                                $transaksi['StatusTransaksi']
                                == 'Selesai'
                            )
                                ? 'badge-selesai'
                                : 'badge-proses'; ?>"
                        >

                            <?= (
                                $transaksi['StatusTransaksi']
                                == 'Selesai'
                            )
                                ? 'Sudah Bayar'
                                : 'Belum Bayar'; ?>

                        </span>

                    </div>


                    <!-- DATA PELANGGAN -->

                    <div class="customer-box">

                        <h6>

                            <i class="fa-solid fa-user me-2"></i>

                            Data Pelanggan

                        </h6>


                        <p>

                            <strong>Nama:</strong>

                            <?= htmlspecialchars(
                                $transaksi['NamaPelanggan']
                                ?? 'Umum'
                            ); ?>

                        </p>


                        <p>

                            <strong>No. HP:</strong>

                            <?= htmlspecialchars(
                                $transaksi['NomorTelepon']
                                ?? '-'
                            ); ?>

                        </p>


                        <p>

                            <strong>Alamat:</strong>

                            <?= htmlspecialchars(
                                $transaksi['Alamat']
                                ?? '-'
                            ); ?>

                        </p>

                    </div>


                    <!-- PRODUK -->

                    <h6 class="fw-bold">

                        <i class="fa-solid fa-box me-2"></i>

                        Produk Pesanan

                    </h6>


                    <?php if (
                        count($detail_produk) > 0
                    ) { ?>


                        <?php foreach (
                            $detail_produk as $item
                        ) { ?>


                            <div class="product-row">


                                <?php

                                $foto =
                                    $item['foto'] ?? '';

                                $foto_path =
                                    "../assets/img/" .
                                    $foto;

                                ?>


                                <?php if (
                                    !empty($foto) &&
                                    file_exists($foto_path)
                                ) { ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                            $foto_path
                                        ); ?>"
                                        class="product-img"
                                        alt="Produk"
                                    >

                                <?php } else { ?>

                                    <div
                                        class="product-img
                                        d-flex
                                        align-items-center
                                        justify-content-center"
                                        style="
                                            background:#f8e8e5;
                                            color:#b86f78;
                                        "
                                    >

                                        <i class="fa-solid fa-cake-candles"></i>

                                    </div>

                                <?php } ?>


                                <div class="product-info">

                                    <div class="product-name">

                                        <?= htmlspecialchars(
                                            $item['NamaProduk']
                                            ?? '-'
                                        ); ?>

                                    </div>


                                    <div class="product-price">

                                        Rp
                                        <?= number_format(
                                            $item['Harga'] ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                        ×

                                        <?= $item['JumlahProduk']; ?>

                                    </div>

                                </div>


                                <div class="subtotal">

                                    Rp
                                    <?= number_format(
                                        $item['Subtotal'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </div>


                            </div>


                        <?php } ?>


                    <?php } else { ?>

                        <div class="empty">

                            Detail produk tidak ditemukan.

                        </div>

                    <?php } ?>


                    <!-- TOTAL -->

                    <div class="total-box">

                        <div class="total-label">

                            TOTAL PEMBAYARAN

                        </div>


                        <div class="total-price">

                            Rp
                            <?= number_format(
                                $transaksi['TotalHarga'] ?? 0,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </div>

                    </div>


                    <!-- ==================================
                         JIKA BELUM BAYAR
                    =================================== -->

                    <?php if (
                        $transaksi['StatusTransaksi']
                        != 'Selesai'
                    ) { ?>


                        <form
                            method="POST"
                            action="proses_bayar.php"
                            onsubmit="return cekBayar()"
                            class="mt-3"
                        >


                            <input
                                type="hidden"
                                name="TransaksiID"
                                value="<?= $transaksi['TransaksiID']; ?>"
                            >


                            <!-- METODE PEMBAYARAN -->

                            <div class="mb-3">

                                <label class="form-label fw-semibold">

                                    <i class="fa-solid fa-wallet me-1"></i>

                                    Metode Pembayaran

                                </label>


                                <select
                                    name="MetodePembayaran"
                                    id="metodePembayaran"
                                    class="form-select"
                                    onchange="ubahMetode()"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Pembayaran --
                                    </option>

                                    <option value="Cash">
                                        Cash
                                    </option>

                                    <option value="Transfer">
                                        Transfer
                                    </option>

                                    <option value="QRIS">
                                        QRIS
                                    </option>

                                </select>

                            </div>


                            <!-- UANG CASH -->

                            <div
                                class="mb-3"
                                id="cashArea"
                                style="display:none;"
                            >

                                <label class="form-label fw-semibold">

                                    <i class="fa-solid fa-money-bill-wave me-1"></i>

                                    Uang Dibayar

                                </label>


                                <input
                                    type="number"
                                    id="uangBayar"
                                    class="form-control"
                                    placeholder="Masukkan uang pelanggan"
                                    min="0"
                                    oninput="hitungKembalian()"
                                >

                            </div>


                            <!-- KEMBALIAN -->

                            <div
                                class="total-box"
                                id="kembalianBox"
                                style="display:none;"
                            >

                                <div class="total-label">

                                    KEMBALIAN

                                </div>


                                <div
                                    class="total-price"
                                    id="kembalian"
                                >

                                    Rp 0

                                </div>

                            </div>


                            <!-- HIDDEN UANG BAYAR -->

                            <input
                                type="hidden"
                                name="UangBayar"
                                id="inputUangBayar"
                                value="0"
                            >


                            <button
                                type="submit"
                                class="btn btn-bayar"
                            >

                                <i class="fa-solid fa-check me-2"></i>

                                PROSES BAYAR

                            </button>


                        </form>


                    <?php } else { ?>


                        <!-- ==================================
                             SUDAH BAYAR
                        =================================== -->

                        <div class="alert alert-success mt-3">

                            <i class="fa-solid fa-circle-check me-2"></i>

                            Pembayaran sudah dilakukan.

                        </div>


                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <small class="text-muted">

                                    Metode Pembayaran

                                </small>

                                <div class="fw-bold">

                                    <?= htmlspecialchars(
                                        $transaksi['MetodePembayaran']
                                        ?: '-'
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted">

                                    Uang Dibayar

                                </small>

                                <div class="fw-bold">

                                    Rp
                                    <?= number_format(
                                        $transaksi['UangBayar'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <small class="text-muted">

                                    Kembalian

                                </small>

                                <div class="fw-bold">

                                    Rp
                                    <?= number_format(
                                        $transaksi['Kembalian'] ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </div>

                            </div>

                        </div>


                        <a
                            href="invoice.php?id=<?= $transaksi['TransaksiID']; ?>"
                            target="_blank"
                            class="btn btn-bayar"
                        >

                            <i class="fa-solid fa-print me-2"></i>

                            CETAK STRUK

                        </a>


                    <?php } ?>


                <?php } else { ?>


                    <div class="empty">

                        <i
                            class="fa-solid fa-receipt"
                            style="font-size:55px;"
                        ></i>


                        <h5 class="mt-3">

                            Belum ada pesanan dipilih

                        </h5>


                        <p>

                            Klik tombol
                            <strong>Pesanan Baru</strong>
                            untuk membuat pesanan.

                        </p>


                        <a
                            href="pesanan_baru.php"
                            class="btn btn-pesanan"
                        >

                            <i class="fa-solid fa-plus me-1"></i>

                            Pesanan Baru

                        </a>

                    </div>


                <?php } ?>


            </div>

        </div>

    </div>

</div>


<script>

function ubahMetode() {

    const metode =
        document.getElementById(
            'metodePembayaran'
        ).value;

    const cashArea =
        document.getElementById(
            'cashArea'
        );

    const kembalianBox =
        document.getElementById(
            'kembalianBox'
        );

    const uang =
        document.getElementById(
            'uangBayar'
        );

    const hidden =
        document.getElementById(
            'inputUangBayar'
        );


    if (metode === 'Cash') {

        cashArea.style.display = 'block';

        kembalianBox.style.display = 'block';

        uang.required = true;

    } else {

        cashArea.style.display = 'none';

        kembalianBox.style.display = 'none';

        uang.required = false;

        uang.value = '';

        hidden.value = 0;

    }

}


function hitungKembalian() {

    const total =
        <?= $transaksi
            ? (float) $transaksi['TotalHarga']
            : 0; ?>;


    const uang =
        parseFloat(
            document.getElementById(
                'uangBayar'
            ).value
        ) || 0;


    const hasil =
        document.getElementById(
            'kembalian'
        );


    if (uang <= 0) {

        hasil.innerHTML = 'Rp 0';

        return;

    }


    const kembalian =
        uang - total;


    if (kembalian < 0) {

        hasil.innerHTML =
            '<span style="color:#dc3545;">' +
            'Uang kurang Rp ' +
            Math.abs(kembalian)
                .toLocaleString('id-ID') +
            '</span>';

    } else {

        hasil.innerHTML =
            'Rp ' +
            kembalian.toLocaleString(
                'id-ID'
            );

    }

}


function cekBayar() {

    const metode =
        document.getElementById(
            'metodePembayaran'
        ).value;


    const total =
        <?= $transaksi
            ? (float) $transaksi['TotalHarga']
            : 0; ?>;


    const uangInput =
        document.getElementById(
            'uangBayar'
        );


    const hidden =
        document.getElementById(
            'inputUangBayar'
        );


    if (metode === '') {

        alert(
            'Silakan pilih metode pembayaran.'
        );

        return false;

    }


    // CASH

    if (metode === 'Cash') {

        const uang =
            parseFloat(
                uangInput.value
            ) || 0;


        if (uang <= 0) {

            alert(
                'Masukkan uang yang dibayar.'
            );

            uangInput.focus();

            return false;

        }


        if (uang < total) {

            alert(
                'Uang pembayaran masih kurang!'
            );

            uangInput.focus();

            return false;

        }


        hidden.value = uang;

    }


    // TRANSFER / QRIS

    else {

        hidden.value = total;

    }


    return true;

}

</script>


</body>

</html>