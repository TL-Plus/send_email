<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Customer DigiNext</title>
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
            <h1 class="title-table mb-4 mx-auto">Check Customer DigiNext</h1>
            <h4 class="text-center">Kiểm tra đầu số và thông tin khách hàng của Diginext</h4>
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

    <form method="POST" action="" class="mt-4">
        <div class="mb-3">
            <label for="number_sequence" class="form-label">EXT/Number:</label>
            <input type="text" name="number_sequence" id="number_sequence" class="form-control"
                placeholder="Enter a number sequence (separated by spaces) - e.g., 123 456 789"
                value="<?php echo isset($_POST['number_sequence']) ? htmlspecialchars($_POST['number_sequence']) : ''; ?>">
        </div>
        <button type="submit" name="convert" class="btn btn-primary">Check and Convert Number Sequence</button>
        <button type="submit" name="check_data" class="btn btn-primary">Check Data</button>
        <button type="submit" name="export_excel" class="btn btn-success">Export to Excel and Send Telegram</button>
    </form>

    <?php include 'includes/body_index.php'; ?>

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script src="../static/js/check_customer/index.js"></script>

</body>

</html>