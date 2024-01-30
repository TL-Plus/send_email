<?php

require_once 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';


// Define $imagePath
$imagePath = '/var/www/html/send_email/holiday_schedule/holiday-schedule.jpg';

function getInfoCustomersFromDatabase()
{
    $infoCustomers = [];

    $query_customers = "SELECT customer_name, email, status, image_path
                        FROM customers
                        WHERE status = 'actived'";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT'],
    );

    $result = $conn->query($query_customers);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $infoCustomers[] = [
                'customerName' => $row['customer_name'],
                'customerEmail' => $row['email'],
            ];
        }
    }

    return $infoCustomers;
}

$infoCustomers = getInfoCustomersFromDatabase();

// Define an array to store processed customer emails
$processedCustomerEmails = [];

foreach ($infoCustomers as $infoCustomer) {
    $customerEmail = $infoCustomer['customerEmail'];

    // Check if the customer email has been processed already
    if (!in_array($customerEmail, $processedCustomerEmails)) {
        // Mark the customer email as processed
        $processedCustomerEmails[] = $customerEmail;

        // Create $FormValues array with necessary data
        $FormValues = [
            'customerName' => $infoCustomer['customerName'],
            'customerEmail' => $infoCustomer['customerEmail'],
        ];

        // Send email with image and holiday announcement
        sendEmailHolidaySchedule(
            "[DIGINEXT] THÔNG BÁO LỊCH NGHỈ LỄ TẾT NGHUYÊN ĐÁN NĂM 2024",
            bodyEmailHolidaySchedule($FormValues),
            $FormValues['customerEmail'],
            $imagePath
        );
    }
}