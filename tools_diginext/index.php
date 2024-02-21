<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tools DigiNext</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Add Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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
            <h1 class="text-center">Tools DigiNext</h1>
            <h4 class="text-center">Công cụ hỗ trợ cho DigiNext</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="text-center mb-4">
                <a href="/check_customer" class="btn btn-primary btn-lg">Kiểm tra thông tin Khách hàng</a>
            </div>
            <div class="text-center mb-4">
                <a href="/release_number" class="btn btn-primary btn-lg">Kiểm tra và nhả số</a>
            </div>
            <div class="text-center mb-4">
                <a href="/update_contract_details" class="btn btn-primary btn-lg">Kiểm tra và Cập nhật chi tiết hợp
                    đồng</a>
            </div>
            <div class="text-center mb-4">
                <a href="/export_ctc_by_contract" class="btn btn-primary btn-lg">Kiểm tra và xuất chi tiết cước theo
                    hợp đồng</a>
            </div>
            <div class="text-center mb-4">
                <a href="/report_ctc" class="btn btn-primary btn-lg">BÁO CÁO CUỘC GỌI HỆ THỐNG VOS</a>
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

</body>

</html>