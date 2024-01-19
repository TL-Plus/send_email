<?php
require_once '/var/www/html/vendor/autoload.php';


try {
    // Specify the correct path to the .env file
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    // Output the error message for debugging
    die('Error: ' . $e->getMessage());
}