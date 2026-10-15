<?php

include "koneksi.php";


// ==========================================
// AMBIL ID PRODUK
// ==========================================

if (isset($_GET['id'])) {

    $produk_id = (int) $_GET['id'];

} elseif (isset($_POST['ProdukID'])) {

    $produk_id = (int) $_POST['ProdukID'];

} else {

    header("Location: index.php");
    exit;
}


// ==========================================
// PROSES PESAN
// ==========================================

if (isset($_POST['pesan'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $telepon = mysqli_real_escape_string(
        $koneksi,
        $_POST['telepon']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $jumlah = (int) $_POST['jumlah'];


    // Ambil produk
    $query_produk = mysqli_query(
        $koneksi,
        "SELECT *
         FROM produk
         WHERE ProdukID = '$produk_id'
         LIMIT 1"
    );


    if (!$query_produk || mysqli_num_rows($query_produk) == 0) {

        echo "<script>
                alert('Produk tidak ditemukan!');
                window.location='index.php';
              </script>";
        exit;
    }


    $produk = mysqli_fetch_assoc($query_produk);

    $harga = (float) $produk['Harga'];
    $stok = (int) $produk['Stok'];


    // Cek jumlah
    if ($jumlah <= 0) {

        echo "<script>
                alert('Jumlah pesanan tidak valid!');
                history.back();
              </script>";
        exit;
    }


    // Cek stok
    if ($jumlah > $stok) {

        echo "<script>
                alert('Stok tidak mencukupi!');
                history.back();
              </script>";
        exit;
    }


    // ==========================================
    // CARI PELANGGAN BERDASARKAN NOMOR TELEPON
    // ==========================================

    $query_pelanggan = mysqli_query(
        $koneksi,
        "SELECT *
         FROM pelanggan
         WHERE NomorTelepon = '$telepon'
         LIMIT 1"
    );


    if (mysqli_num_rows($query_pelanggan) > 0) {

        $data_pelanggan = mysqli_fetch_assoc(
            $query_pelanggan
        );

        $pelanggan_id = $data_pelanggan['PelangganID'];


        // Update data pelanggan
        mysqli_query(
            $koneksi,
            "UPDATE pelanggan SET
                NamaPelanggan = '$nama',
                Alamat = '$alamat'
             WHERE PelangganID = '$pelanggan_id'"
        );

    } else {

        // Tambah pelanggan baru
        $insert_pelanggan = mysqli_query(
            $koneksi,
            "INSERT INTO pelanggan
            (
                NamaPelanggan,
                Alamat,
                NomorTelepon
            )
            VALUES
            (
                '$nama',
                '$alamat',
                '$telepon'
            )"
        );


        if (!$insert_pelanggan) {

            die(
                "Pelanggan gagal ditambahkan: "
                . mysqli_error($koneksi)
            );
        }


        $pelanggan_id = mysqli_insert_id($koneksi);
    }


    // ==========================================
    // HITUNG TOTAL
    // ==========================================

    $subtotal = $harga * $jumlah;


    // ==========================================
    // BUAT TRANSAKSI
    // ==========================================

    $insert_transaksi = mysqli_query(
        $koneksi,
        "INSERT INTO transaksi
        (
            TanggalTransaksi,
            PelangganID,
            MetodePembayaran,
            StatusTransaksi
        )
        VALUES
        (
            NOW(),
            '$pelanggan_id',
            'Belum Dipilih',
            'Proses'
        )"
    );


    if (!$insert_transaksi) {

        die(
            "Transaksi gagal dibuat: "
            . mysqli_error($koneksi)
        );
    }


    $transaksi_id = mysqli_insert_id($koneksi);


    // ==========================================
    // BUAT PENJUALAN
    // ==========================================

    $insert_penjualan = mysqli_query(
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
            NOW(),
            '$subtotal',
            '$pelanggan_id',
            '$transaksi_id'
        )"
    );


    if (!$insert_penjualan) {

        die(
            "Penjualan gagal dibuat: "
            . mysqli_error($koneksi)
        );
    }


    $penjualan_id = mysqli_insert_id($koneksi);


    // ==========================================
    // BUAT DETAIL PENJUALAN
    // ==========================================

    $insert_detail = mysqli_query(
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
            '$produk_id',
            '$jumlah',
            '$subtotal'
        )"
    );


    if (!$insert_detail) {

        die(
            "Detail penjualan gagal dibuat: "
            . mysqli_error($koneksi)
        );
    }


    // ==========================================
    // KURANGI STOK
    // ==========================================

    mysqli_query(
        $koneksi,
        "UPDATE produk
         SET Stok = Stok - $jumlah
         WHERE ProdukID = '$produk_id'"
    );


    // ==========================================
    // BERHASIL
    // ==========================================

    echo "<script>

            alert('Pesanan berhasil dikirim!');

            window.location.href='index.php';

          </script>";

    exit;
}


// ==========================================
// AMBIL DATA PRODUK
// ==========================================

$query_produk = mysqli_query(
    $koneksi,
    "SELECT *
     FROM produk
     WHERE ProdukID = '$produk_id'
     LIMIT 1"
);


if (!$query_produk || mysqli_num_rows($query_produk) == 0) {

    echo "<script>
            alert('Produk tidak ditemukan!');
            window.location='index.php';
          </script>";

    exit;
}


$produk = mysqli_fetch_assoc($query_produk);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pesan <?= htmlspecialchars($produk['NamaProduk']); ?> | SweetBake</title>


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


    <!-- Poppins -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        body {
            font-family: 'Poppins', sans-serif;
            background: #fff8f5;
            color: #5a3e42;
        }

        .navbar {
            background: #8f5f63;
        }

        .order-box {
            max-width: 900px;
            margin: 70px auto;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .product-preview {
            background: #f8e8e5;
            border-radius: 18px;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-preview img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .btn-order {
            background: #8f5f63;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
        }

        .btn-order:hover {
            background: #70464a;
            color: white;
        }

        .form-control {
            border-radius: 10px;
        }

        footer {
            background: #8f5f63;
            color: white;
        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-dark">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold"
        >

            <i class="fa-solid fa-cake-candles me-2"></i>

            SweetBake

        </a>

    </div>

</nav>


<!-- FORM PESAN -->

<div class="container">

    <div class="order-box">

        <div class="row g-4">


            <!-- PRODUK -->

            <div class="col-md-5">

                <div class="product-preview">

                    <?php

                    $foto = $produk['foto'] ?? '';

                    $path_foto = "assets/img/" . $foto;

                    ?>


                    <?php if (
                        !empty($foto)
                        &&
                        file_exists($path_foto)
                    ) { ?>

                        <img
                            src="<?= htmlspecialchars($path_foto); ?>"
                            alt="<?= htmlspecialchars($produk['NamaProduk']); ?>"
                        >

                    <?php } else { ?>

                        <i
                            class="fa-solid fa-cake-candles"
                            style="
                                font-size:100px;
                                color:#8f5f63;
                            "
                        ></i>

                    <?php } ?>

                </div>


                <h3 class="fw-bold mt-4">

                    <?= htmlspecialchars(
                        $produk['NamaProduk']
                    ); ?>

                </h3>


                <h5 class="fw-bold">

                    Rp
                    <?= number_format(
                        $produk['Harga'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </h5>


                <p class="text-muted">

                    Stok tersedia:
                    <?= $produk['Stok']; ?>

                </p>

            </div>


            <!-- FORM -->

            <div class="col-md-7">

                <h3 class="fw-bold mb-4">

                    <i class="fa-solid fa-cart-shopping me-2"></i>

                    Form Pemesanan

                </h3>


                <form
                    method="POST"
                    action=""
                >

                    <input
                        type="hidden"
                        name="ProdukID"
                        value="<?= $produk['ProdukID']; ?>"
                    >


                    <!-- NAMA -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Nama Pelanggan

                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama"
                            required
                        >

                    </div>


                    <!-- TELEPON -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Nomor Telepon

                        </label>

                        <input
                            type="text"
                            name="telepon"
                            class="form-control"
                            placeholder="Masukkan nomor telepon"
                            required
                        >

                    </div>


                    <!-- ALAMAT -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Alamat

                        </label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat"
                            required
                        ></textarea>

                    </div>


                    <!-- JUMLAH -->

                    <div class="mb-4">

                        <label class="form-label fw-semibold">

                            Jumlah Pesanan

                        </label>

                        <input
                            type="number"
                            name="jumlah"
                            class="form-control"
                            min="1"
                            max="<?= $produk['Stok']; ?>"
                            value="1"
                            required
                        >

                    </div>


                    <div class="d-flex gap-2">

                        <a
                            href="index.php"
                            class="btn btn-secondary w-50"
                        >

                            <i class="fa-solid fa-arrow-left me-1"></i>

                            Kembali

                        </a>


                        <button
                            type="submit"
                            name="pesan"
                            class="btn btn-order w-50"
                        >

                            <i class="fa-solid fa-paper-plane me-1"></i>

                            Kirim Pesanan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<!-- FOOTER -->

<footer class="py-4 text-center">

    <div class="container">

        <strong>
            SweetBake
        </strong>

        <p class="mb-0 mt-2">
            Toko Kue SweetBake
        </p>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>