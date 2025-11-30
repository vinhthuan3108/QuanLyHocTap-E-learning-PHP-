<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            padding-top: 70px;
            background-color: white;
        }

        /* Menu item styling */
        .navbar-nav .nav-link {
            padding: 8px 14px;
            border-radius: 6px;
            color: #000 !important;
            transition: background-color 0.2s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(91, 87, 87, 0.1);
        }

        .navbar-brand {
            color: #000 !important;
        }
    </style>
</head>

<body class="with-navbar">

    <nav class="navbar navbar-expand-sm bg-white border-bottom fixed-top">
        <div class="container-fluid">

            <a class="navbar-brand" href="#">TNT</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Trang chủ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../account/login.php">Đăng nhập</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../account/register.php">Đăng ký</a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
