<?php

session_start();

require_once '/var/www/html/send_email/config.php';

// Function to check if the session has expired
function isSessionExpired()
{
    $session_expire_time = $_ENV['SESSION_EXPIRE_TIME'];
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_expire_time) {
        session_unset();
        session_destroy();
        return true;
    }
    return false;
}

// Check if the user is logged in or session has expired
if (!isset($_SESSION['user']) || isSessionExpired()) {
    header('Location: /tools_diginext/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DIGINEXT | CDR LOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Favicon -->
    <link rel="icon" href="/static/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/static/images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="/tools_diginext/static/css/style.css">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body class="container mt-3">

    <div class="row mt-3 mb-5">
        <div class="col-md-2 logo-container mt-3">
            <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid">
        </div>
        <div class="col-md-10 text-center mt-3">
            <h1 class="title-table mb-4 mx-auto">Check CDR Log</h1>
            <h4 class="text-center">Kiểm tra thông tin cuộc gọi</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="text-center">
                <a href="/tools_diginext" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i> Back to Tools
                    DigiNext</a>
            </div>
        </div>
        <div class="col-md-9 text-md-end">
            <h5 id="currentTime" class="mt-4">
                Thời gian kiểm tra:
                <?php date_default_timezone_set("Asia/Ho_Chi_Minh");
                $currentTime = date('d-m-Y H:i:s');
                echo $currentTime; ?>
            </h5>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h4 class="">CDR Log</h4>

            <!-- check-data-cdr -->
            <form method="POST" class="mt-4" id="check-data-cdr">
                <input type="hidden" name="check_type" value="cdr"> <!-- Thêm tham số ẩn -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="Caller" class="form-label">Caller (Can be left blank):</label>
                        <input type="text" name="Caller" id="Caller" class="form-control" placeholder="Enter Caller"
                            value="<?php echo isset($_POST['Caller']) ? htmlspecialchars($_POST['Caller']) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Callee" class="form-label">Callee (Can be left blank):</label>
                        <input type="text" name="Callee" id="Callee" class="form-control" placeholder="Enter Callee"
                            value="<?php echo isset($_POST['Callee']) ? htmlspecialchars($_POST['Callee']) : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_at_cdr">Start At:</label>
                        <?php
                        // Tạo giá trị mặc định là ngày đầu của tháng hoặc giá trị đã nhập nếu có
                        $start_at_default = isset($_POST['start_at_cdr']) ? $_POST['start_at_cdr'] : date('Y-m-01');
                        ?>
                        <input type="date" name="start_at_cdr" id="start_at_cdr" class="form-control" required
                            placeholder="Enter Start At" step="1" value="<?php echo $start_at_default; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_at_cdr">End At:</label>
                        <?php
                        // Tạo giá trị mặc định là ngày hiện tại hoặc giá trị đã nhập nếu có
                        $end_at_default = isset($_POST['end_at_cdr']) ? $_POST['end_at_cdr'] : date('Y-m-d');
                        ?>
                        <input type="date" name="end_at_cdr" id="end_at_cdr" class="form-control" required
                            placeholder="Enter End At" step="1" value="<?php echo $end_at_default; ?>">
                    </div>
                </div>

                <button type="submit" name="check_data_cdr" class="btn btn-primary">Check CDR Log</button>
                <button type="submit" name="export_excel" class="btn btn-success mb-3">Export to Excel & Send
                    Telegram</button>
            </form>

            <?php include 'includes/body_data_cdr.php'; ?>

        </div>

    </div>

    <?php include '../footer.php'; ?>

    <!-- Add Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


</body>

</html>