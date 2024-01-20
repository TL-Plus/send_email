<?php

require_once '/var/www/html/send_email/config.php';


if ($_ENV['USERNAME'] && $_ENV['PASSWORD']) {
    $storedUsername = $_ENV['USERNAME'];
    $storedPassword = $_ENV['PASSWORD'];

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === $storedUsername && $password === $storedPassword) {
        $_SESSION['username'] = $username;
        echo 'success';
    } else {
        echo 'failure';
    }
} else {
    echo 'Invalid .env file configuration.';
}