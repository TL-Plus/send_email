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
        <div class="form-group">
            <label for="contract_code">Contract Code:</label>
            <input type="text" name="contract_code" id="contract_code" class="form-control" required
                placeholder="Enter Contract Code">
        </div>
        <div class="form-group">
            <label for="number_status">EXT/Number Status:</label>
            <input type="text" name="number_status" id="number_status" class="form-control" required
                placeholder="Enter EXT/Number Status">
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
            <input type="datetime-local" name="activated_at" id="activated_at" class="form-control" required
                placeholder="Enter Activated At" step="1">
        </div>
        <div class="form-group">
            <label for="contract_details_log">Log:</label>
            <input type="text" name="contract_details_log" id="contract_details_log" class="form-control" required
                placeholder="Enter Contract Details Log - e.g., admin_update-activated_at">
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