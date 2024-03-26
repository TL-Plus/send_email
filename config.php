<?php
require_once '/var/www/html/send_email/vendor/autoload.php';

try {
    // date default timezone
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    // Specify the correct path to the .env file
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    // Output the error message for debugging
    die ('Error: ' . $e->getMessage());
}