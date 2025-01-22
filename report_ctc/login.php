<?php include 'templates/login.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DIGINEXT | LOGIN</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="/static/css/report_ctc/style.css">
    <link rel="stylesheet" href="/static/css/report_ctc/modal.css">

    <!-- Favicon -->
    <link rel="icon" href="/static/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/static/images/favicon.ico" type="image/x-icon">

</head>

<body>
    <div id="form-login" class="container">

        <div class="row">
            <div class="col-md-2 logo-container mt-3">
                <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid" width="300px"
                    height="300px">
            </div>
            <div class="col-md-10 text-center mt-5">
                <h3 class="title-table mb-4 mx-auto">CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</h3>
            </div>
        </div>

        <?php include 'templates/notification.php'; ?>

        <div class="container login-container">
            <div class="row">
                <div class="col-md-6 offset-md-3 mt-5">
                    <h2>Đăng Nhập</h2>

                    <form method="post" action="">
                        <div>
                            <label class="font-weight-bold mt-3" for="username">Tên người dùng:</label>
                            <input type="text" id="username" name="username" class="form-control login-input" required>
                        </div>
                        <div class="mt-3">
                            <label class="font-weight-bold" for="password">Mật khẩu:</label>
                            <input type="password" id="password" name="password" class="form-control login-input"
                                required>
                        </div>
                        <div class="mt-4 mb-5">
                            <button type="submit" class="btn btn-primary btn-lg login-button">Đăng nhập</button>
                        </div>
                        <?php if (isset($error_message)): ?>
                            <p class="error-message">
                                <?php echo $error_message; ?>
                            </p>
                        <?php endif; ?>
                    </form>

                </div>
            </div>
        </div>

        <?php include 'templates/footer.php'; ?>

    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- Include your custom index.js file -->
    <script src="/static/js/report_ctc/index.js"></script>

</body>

</html>