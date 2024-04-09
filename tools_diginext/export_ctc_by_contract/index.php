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
    <title>DIGINEXT | CTC BY CONTRACT</title>
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
            <h1 class="title-table mb-4 mx-auto">Export CTC By Contract</h1>
            <h4 class="text-center">Kiểm tra và xuất chi tiết cước theo Hợp đồng</h4>
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

    <form method="POST" action="" class="mt-4">
        <div class="mb-3">
            <label for="contract_code" class="form-label">Contract Code:</label>
            <input type="text" name="contract_code" id="contract_code" class="form-control" required
                placeholder="Enter Contract Code"
                value="<?php echo isset($_POST['contract_code']) ? htmlspecialchars($_POST['contract_code']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="Caller" class="form-label">Caller (Can be left blank):</label>
            <input type="text" name="Caller" id="Caller" class="form-control" placeholder="Enter Caller"
                value="<?php echo isset($_POST['Caller']) ? htmlspecialchars($_POST['Caller']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="caller_object" class="form-label">Caller Object (Can be left blank):</label>
            <input type="text" name="caller_object" id="caller_object" class="form-control"
                placeholder="Enter Caller Object"
                value="<?php echo isset($_POST['caller_object']) ? htmlspecialchars($_POST['caller_object']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="call_type" class="form-label">Call Type (Can be left blank):</label>
            <input type="text" name="call_type" id="call_type" class="form-control" placeholder="Enter Call Type"
                value="<?php echo isset($_POST['call_type']) ? htmlspecialchars($_POST['call_type']) : ''; ?>">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="day_start" class="form-label">Start Day:</label>
                    <?php
                    // Tạo giá trị mặc định là ngày đầu của tháng
                    $day_start_default = isset($_POST['day_start']) ? $_POST['day_start'] : date('Y-m-01');
                    ?>
                    <input type="date" name="day_start" id="day_start" class="form-control" required
                        value="<?php echo $day_start_default; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="day_end" class="form-label">End Day:</label>
                    <?php
                    // Tạo giá trị mặc định là ngày đầu của tháng
                    $day_end_default = isset($_POST['day_end']) ? $_POST['day_end'] : date('Y-m-d');
                    ?>
                    <input type="date" name="day_end" id="day_end" class="form-control" required
                        value="<?php echo $day_end_default; ?>">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" name="check_data" class="btn btn-primary">Check Data</button>
            <button type="submit" name="export_excel" class="btn btn-success">Export Excel & Send
                Telegram</button>
        </div>
    </form>

    <?php include 'includes/body_index.php'; ?>

    <?php include '../footer.php'; ?>

    <!-- Add Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <script src="/static/js/report_ctc/index.js"></script>

</body>

</html>