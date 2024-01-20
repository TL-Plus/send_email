<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DIGINEXT</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/send_email/static/css/report_ctc/style.css">
    <link rel="stylesheet" href="/send_email/static/css/report_ctc/modal.css">

    <!-- Favicon -->
    <link rel="icon" href="/static/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/static/images/favicon.ico" type="image/x-icon">
</head>

<body>

    <div id="form-report" class="container">
        <div class="row mt-3">
            <div class="col-md-2 logo-container mt-3">
                <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid">
            </div>
            <div class="col-md-10 text-center mt-5">
                <h3 class="title-table mb-4 mx-auto">BÁO CÁO CUỘC GỌI HỆ THỐNG VOS</h3>
            </div>
        </div>

        <?php include 'templates/body_index.php'; ?>

        <?php include 'templates/footer.php'; ?>

        <?php include 'templates/modal.php'; ?>

    </div>

    <div id="form-login" class="container">
        <div class="row">
            <div class="col-md-2 logo-container mt-3">
                <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid" width="300px"
                    height="300px">
            </div>
            <div class="col-md-10 text-center mt-5">
                <h3 class="title-table mb-4 mx-auto">CÔNG TY CỔ PHẦN CÔNG NGHỆ SỐ DIGINEXT</h3>
            </div>
        </div>
        <div class="container login-container">
            <div class="row">
                <div class="col-md-6 offset-md-3 mt-5">
                    <h3 class="mb-4">Đăng Nhập</h3>
                    <form id="loginForm" action="post">
                        <div>
                            <label class="font-weight-bold" for="username">Tên người dùng</label>
                            <input type="text" id="username" class="form-control login-input" required>
                        </div>
                        <div class="mt-3">
                            <label class="font-weight-bold" for="password">Mật khẩu</label>
                            <input type="password" id="password" class="form-control login-input" required>
                        </div>
                        <div class="mt-4 mb-5">
                            <button type="button" class="btn btn-primary btn-lg login-button" onclick="login()">Đăng
                                nhập</button>
                        </div>
                        <p id="error-message" class="mt-3 text-danger"></p>
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

    <!-- Include your custom modal.js file -->
    <script src="/send_email/static/js/report_ctc/login.js"></script>
    <script src="/send_email/static/js/report_ctc/index.js"></script>

</body>

</html>