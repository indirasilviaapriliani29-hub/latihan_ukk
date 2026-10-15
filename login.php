<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin | SweetBake</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            background: #fff8f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 420px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(112, 70, 74, 0.12);
        }

        .brand-icon {
            width: 65px;
            height: 65px;
            background: #f7dfe0;
            color: #b86f78;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 18px;
        }

        .login-title {
            color: #70464a;
            font-weight: 700;
            text-align: center;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #806666;
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-label {
            color: #70464a;
            font-weight: 600;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #b86f78;
            box-shadow: 0 0 0 0.2rem rgba(184, 111, 120, 0.15);
        }

        .btn-login {
            width: 100%;
            background: #b86f78;
            border-color: #b86f78;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #a65f68;
            border-color: #a65f68;
            color: white;
        }

        .btn-back {
            width: 100%;
            background: white;
            border: 1px solid #b86f78;
            color: #b86f78;
            padding: 11px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 12px;
        }

        .btn-back:hover {
            background: #f7dfe0;
            color: #70464a;
        }

        .footer-login {
            text-align: center;
            color: #806666;
            font-size: 13px;
            margin-top: 25px;
        }
    </style>

</head>

<body>

    <div class="login-card">

        <div class="brand-icon">
            <i class="fa-solid fa-cake-candles"></i>
        </div>

        <h3 class="login-title">
            SweetBake
        </h3>

        <p class="login-subtitle">
            Login Admin
        </p>

        <?php if (isset($_GET['error'])) { ?>

            <div class="alert alert-danger text-center">
                <i class="fa-solid fa-circle-exclamation me-1"></i>
                Username atau password salah.
            </div>

        <?php } ?>

        <form action="proses_login.php" method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Masukkan username"
                    required
                    autocomplete="username"
                >

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >

            </div>

            <button
                type="submit"
                name="login"
                class="btn btn-login"
            >
                <i class="fa-solid fa-right-to-bracket me-2"></i>
                Login
            </button>

            <a href="index.php" class="btn-back">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Kembali ke SweetBake
            </a>

        </form>

        <div class="footer-login">
            © SweetBake
        </div>

    </div>

</body>

</html>