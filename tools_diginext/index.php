<?php

session_start();

require_once '/var/www/html/send_email/config.php';

// Function to check if the session has expired
function isSessionExpired()
{
    $session_expire_time = $_ENV['SESSION_EXPIRE_TIME'];
    if (isset ($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_expire_time) {
        session_unset();
        session_destroy();
        return true;
    }
    return false;
}

// Check if the user is logged in or session has expired
if (!isset ($_SESSION['user']) || isSessionExpired()) {
    header('Location: /tools_diginext/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools DigiNext</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/tools_diginext/static/css/style.css">
    <!-- Favicon -->
    <link rel="icon" href="/static/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/static/images/favicon.ico" type="image/x-icon">
</head>

<body class="container mt-3">
    <div class="row mt-3 mb-5">
        <div class="col-md-2 logo-container mt-3">
            <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid">
        </div>
        <div class="col-md-10 text-center mt-3">
            <h1 class="title-table mb-4 mx-auto">Tools DigiNext</h1>
            <h4 class="text-center">Công cụ hỗ trợ cho DigiNext</h4>
        </div>
    </div>

    <div class="row mt-3 mb-5">
        <div class="col-md-12">
            <h5 id="currentTime" class="header-current-time mt-4">
                Thời gian kiểm tra:
                <?php date_default_timezone_set("Asia/Ho_Chi_Minh");
                $currentTime = date('d-m-Y H:i:s');
                echo $currentTime; ?>
            </h5>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="text-center">
                <a href="/tools_diginext/check_customer"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-users me-2"></i>Kiểm tra thông tin Khách hàng</a>
                <a href="/tools_diginext/release_number"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-phone-square-alt me-2"></i>Kiểm tra và nhả số</a>
                <a href="/tools_diginext/update_contract_details"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-file-contract me-2"></i>Kiểm tra và Cập nhật chi tiết hợp đồng</a>
            </div>

            <div class="text-center">
                <a href="/tools_diginext/check_totalcost"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-file-contract me-2"></i>Kiểm tra tổng cước</a>
                <a href="/tools_diginext/check_cdr_cdrdsip"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-file-contract me-2"></i>Kiểm tra sản lượng CDR và CDRDSIP</a>
            </div>

            <div class="text-center mb-4">
                <a href="/tools_diginext/export_ctc_by_contract"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-file-export me-2"></i>Kiểm tra và xuất chi tiết cước theo hợp đồng</a>
                <a href="/report_ctc"
                    class="btn btn-outline-primary btn-lg rounded-pill px-md-5 py-md-3 px-3 py-2 me-3 mb-4"><i
                        class="fas fa-file-alt me-2"></i>BÁO CÁO CUỘC GỌI HỆ THỐNG VOS</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Add Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="/static/js/report_ctc/index.js"></script>

</body>

</html>