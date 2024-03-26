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
    header('Location: /report_ctc/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DIGINEXT</title>
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

    <div id="form-report" class="container">
        <div class="row mt-3">
            <div class="col-md-2 logo-container mt-3">
                <img src="/static/images/logo-diginext.png" alt="Diginext Logo" class="img-fluid">
            </div>
            <div class="col-md-10 text-center mt-5">
                <h3 class="title-table mb-4 mx-auto">BÁO CÁO CUỘC GỌI HỆ THỐNG VOS</h3>
            </div>
        </div>

        <?php include 'templates/notification.php'; ?>

        <?php include 'templates/body_index.php'; ?>

        <?php include 'templates/footer.php'; ?>

        <?php include 'templates/modal.php'; ?>

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