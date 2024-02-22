<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng đến trang index
if (!isset($_SESSION['user'])) {
    header('Location: /tools_diginext/login.php');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Release Number DigiNext</title>
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
            <h1 class="title-table mb-4 mx-auto">Release Number DigiNext</h1>
            <h4 class="text-center">Kiểm tra và Nhả số</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="text-center">
                <a href="/tools_diginext" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i> Back Tools
                    DigiNext</a>
            </div>
        </div>
    </div>

    <!-- check-data -->
    <form method="POST" action="" class="mt-4" id="check-data">
        <div class="form-group">
            <label for="number_sequence">EXT/Number:</label>
            <input type="text" name="number_sequence" id="number_sequence" class="form-control" required
                placeholder="Enter a number sequence (separated by spaces) - e.g., 123 456 789">
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
            <label for="status_number">Status Number:</label>
            <input type="text" name="status_number" id="status_number" class="form-control" required
                placeholder="Enter Status Number" step="1">
        </div>
        <div class="form-group">
            <label for="order_numbers_log">Log:</label>
            <input type="text" name="order_numbers_log" id="order_numbers_log" class="form-control" required
                placeholder="Enter Order Number Log - e.g., admin_update-status-order-number">
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

</body>

</html>