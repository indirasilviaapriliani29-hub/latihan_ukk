<?php

session_start();

include "koneksi.php";

if (!isset($_POST['login'])) {
    header("Location: login.php");
    exit;
}

$username = mysqli_real_escape_string(
    $koneksi,
    $_POST['username']
);

$password = md5($_POST['password']);

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM admin
     WHERE Username = '$username'
     AND Password = '$password'
     LIMIT 1"
);

if (mysqli_num_rows($query) > 0) {

    $data = mysqli_fetch_assoc($query);

    $_SESSION['AdminID'] = $data['AdminID'];
    $_SESSION['Username'] = $data['Username'];
    $_SESSION['Level'] = $data['Level'];

    // ADMIN
    if ($data['Level'] == 'Admin' || $data['Level'] == 'admin') {

        header("Location: admin/index.php");
        exit;

    }

    // KASIR
    elseif ($data['Level'] == 'Kasir' || $data['Level'] == 'kasir') {

        header("Location: kasir/kasir.php");
        exit;

    }

    // LEVEL TIDAK DIKENAL
    else {

        session_destroy();
        header("Location: login.php?error=level");
        exit;
    }

} else {

    header("Location: login.php?error=1");
    exit;
}

?>