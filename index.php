<?php
session_start();
include './config.php';

if (($_SESSION['loggedin'] ?? false) === true) {
    header("Location: " . SITE_PATH . "/login/");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to Our Website">
    <meta name="keywords" content="iqbolshoh, website, modern design, bootstrap">
    <meta name="author" content="iqbolshoh.uz">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#343a40">

    <title>Welcome</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            text-align: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #343a40;
            border-radius: 20px;
            padding: 30px;
            color: #f8f9fa;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-light {
            background-color: #f8f9fa;
            color: #212529;
        }

        .btn-outline-light {
            border-color: #f8f9fa;
            color: #f8f9fa;
        }

        .btn-outline-light:hover {
            background-color: #f8f9fa;
            color: #212529;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <h1 class="mb-3">Welcome to Language Platform</h1>
                    <p>Explore our amazing features and enjoy seamless experience.</p>
                    <a href="./login/" class="btn btn-light btn-lg mt-3">Login</a>
                    <a href="./signup/" class="btn btn-outline-light btn-lg mt-3">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>