<?php

session_start();

require_once '/var/www/html/send_email/config.php';


// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng đến trang index
if (isset($_SESSION['user'])) {
    header('Location: /tools_diginext');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $_ENV['USERNAME'] && $password == $_ENV['PASSWORD']) {
        $_SESSION['user'] = $username;
        header('Location: /tools_diginext');
        exit();
    } else {
        $error_message = 'Thông tin đăng nhập không chính xác.';
    }
}