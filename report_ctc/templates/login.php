<?php

session_start();

require_once '/var/www/html/send_email/config.php';

$session_expire_time = $_ENV['SESSION_EXPIRE_TIME'];

// check user login, session lifetime and redirect index
if (isset($_SESSION['user']) && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) < $session_expire_time) {
    header('Location: /report_ctc');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $_ENV['USERNAME'] && $password == $_ENV['PASSWORD']) {
        $_SESSION['user'] = $username;
        $_SESSION['last_activity'] = time();
        header('Location: /report_ctc');
        exit();
    } else {
        $error_message = 'Thông tin đăng nhập không chính xác.';
    }
}