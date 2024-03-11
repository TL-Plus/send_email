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
    <title>Update Contract Details DigiNext</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Add Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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
            <h1 class="title-table mb-4 mx-auto">Update Contract Details DigiNext</h1>
            <h4 class="text-center">Kiểm tra và Cập nhật ngày kích hoạt hợp đồng trong chi tiết hợp đồng</h4>
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

    <!-- check-data -->
    <form method="POST" action="" class="mt-4" id="check-data">
        <div class="form-group">
            <label for="number_sequence">EXT/Number:</label>
            <input type="text" name="number_sequence" id="number_sequence" class="form-control" required
                placeholder="Enter a number sequence (separated by spaces) - e.g., 123 456 789"
                value="<?php echo isset($_POST['number_sequence']) ? htmlspecialchars($_POST['number_sequence']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="contract_code">Contract Code:</label>
            <input type="text" name="contract_code" id="contract_code" class="form-control" required
                placeholder="Enter Contract Code"
                value="<?php echo isset($_POST['contract_code']) ? htmlspecialchars($_POST['contract_code']) : ''; ?>">

        </div>
        <div class="form-group">
            <label for="number_status">EXT/Number Status (inStock - holding - pending - actived - liquidated -
                expired):</label>
            <input type="text" name="number_status" id="number_status" class="form-control" required
                placeholder="Enter EXT/Number Status"
                value="<?php echo isset($_POST['number_status']) ? htmlspecialchars($_POST['number_status']) : ''; ?>">
        </div>

        <button type="submit" name="check_data" class="btn btn-primary">Check Data</button>
    </form>

    <?php
    $showUpdateForm = isset($_POST['check_data']);

    if ($showUpdateForm) {
    ?>

    <!-- update-data -->
    <form method="POST" action="" class="mt-4" id="update-data">
        <div class="form-group">
            <label for="activated_at">Activated At:</label>
            <?php
            date_default_timezone_set("Asia/Ho_Chi_Minh");
            // Tạo giá trị mặc định là thời gian hiện tại hoặc giá trị đã nhập nếu có
            $activated_at_default = isset($_POST['activated_at']) ? $_POST['activated_at'] : date('Y-m-d H:i:s');
            ?>
            <input type="datetime-local" name="activated_at" id="activated_at" class="form-control" required
                placeholder="Enter Activated At" step="1" value="<?php echo $activated_at_default; ?>">
        </div>
        <div class="form-group">
            <label for="contract_details_log">Log:</label>
            <input type="text" name="contract_details_log" id="contract_details_log" class="form-control" required
                placeholder="Enter Contract Details Log - e.g., admin_update-activated_at"
                value="<?php echo isset($_POST['contract_details_log']) ? htmlspecialchars($_POST['contract_details_log']) : ''; ?>">
        </div>

        <button type="submit" name="update_data" class="btn btn-success">Update Data</button>
    </form>

    <?php
    }
    ?>

    <?php include 'includes/body_data.php'; ?>

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