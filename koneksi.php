<?php

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_kasir"
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>