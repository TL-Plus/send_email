<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_email_for_days.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Define Excel header
$header = [
    'ĐẦU SỐ', 'THỜI ĐIỂM TRIỂN KHAI ĐẦU SỐ', 'CHU KỲ HIỆN TẠI (LẦN THỨ)',
    'CHU KỲ THANH TOÁN ĐẦU SỐ (THÁNG)', 'PHÍ/THÁNG', 'TỔNG CƯỚC'
];

// Define $dbName
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];

function getInfoCustomersFromDatabase($dbName)
{
    $infoCustomers = [];

    // Connection to the database
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName
    );

    // SQL query
    $query = "SELECT DISTINCT
            contracts_details.contract_code, 
            contracts_details.customer_name, 
            contracts_details.customer_code, 
            customers.email AS customer_email,
            contracts_details.categories_code, 
            contracts_details.categories_expand
        FROM 
            contracts_details
        JOIN 
            customers ON contracts_details.customer_code = customers.customer_code
        WHERE 
            DATEDIFF(CURRENT_DATE(), contracts_details.activated_at) / 30 >= contracts_details.payment_cycle
            AND contracts_details.categories_expand IN ('1900', '1800', 'CALLCENTER')
            AND contracts_details.status = 'actived'
            AND contracts_details.cost_expand > 0
        ORDER BY contracts_details.contract_code";

    $result = $conn->query($query);

    $conn->close();
    // Process the result
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $infoCustomers[] = [
                'contractCode' => $row['contract_code'],
                'customerName' => $row['customer_name'],
                'customerCode' => $row['customer_code'],
                'customerEmail' => $row['customer_email'],
                'categoriesCode' => $row['categories_code'],
                'categoriesExpand' => $row['categories_expand'],
            ];
        }
    }

    return $infoCustomers;
}

function fetchCustomerDetails($query, $dbName)
{
    $customerDetails = [];

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName
    );

    $result = $conn->query($query);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customerDetails[] = [
                'extNumber' => $row['ext_number'],
                'activatedAt' => $row['activated_at'],
                'currentCycle' => $row['current_cycle'],
                'paymentCycle' => $row['payment_cycle'],
                'costExpand' => $row['cost_expand'],
                'totalCost' => $row['total_cost'],
            ];
        }
    }

    return $customerDetails;
}

function processEmails($dbName, $header, $fileName, $title)
{

    $infoCustomers = getInfoCustomersFromDatabase($dbName);

    // Define an array to store processed contract codes and categories codes
    $processedContracts = [];

    foreach ($infoCustomers as $infoCustomer) {
        // Extract information from $infoCustomer
        $contractCode = $infoCustomer['contractCode'];
        $categoriesCode = $infoCustomer['categoriesCode'];
        $customerEmail = $infoCustomer['customerEmail'];
        $customerCode = $infoCustomer['customerCode'];

        // Create a unique key based on contract code and categories code
        $key = $contractCode . '_' . $categoriesCode;

        // Build the query to retrieve customer details
        $query = "SELECT 
            contracts_details.ext_number, 
            DATE_FORMAT(contracts_details.activated_at, '%d/%m/%Y') AS activated_at,
            FLOOR(DATEDIFF(CURRENT_DATE(), contracts_details.activated_at) / 30) AS current_cycle,
            contracts_details.payment_cycle, 
            contracts_details.cost_expand, 
            contracts_details.payment_cycle * contracts_details.cost_expand AS total_cost
        FROM 
            contracts_details
        WHERE 
            DATEDIFF(CURRENT_DATE(), contracts_details.activated_at) / 30 >= contracts_details.payment_cycle
            AND contracts_details.categories_expand IN ('1900', '1800', 'CALLCENTER')
            AND status = 'actived'
            AND contracts_details.cost_expand > 0
            AND contracts_details.contract_code = '$contractCode'
            AND contracts_details.categories_code = '$categoriesCode'";

        // Check if this contract has already been processed for this category
        if (!in_array($key, $processedContracts)) {
            // Add the current key to the list of processed contracts
            $processedContracts[] = $key;

            $customerDetails = fetchCustomerDetails($query, $dbName);

            $FormValues = [
                'customerName' => $infoCustomer['customerName'],
                'customerEmail' => $customerEmail,
                'categoriesCode' => $categoriesCode,
                'categoriesExpand' => $infoCustomer['categoriesExpand'],
                'customerDetails' => $customerDetails,
                'currentMonthYear' => date('m/Y'),
                'currentDayMonth' => date('t/m'),
            ];

            // Send email with image and holiday announcement
            sendEmailForDays(
                $query,
                $dbName,
                $header,
                "$fileName{$customerCode}_{$categoriesCode}.xlsx",
                "$title {$categoriesCode}",
                bodyEmailFeePaymentCycle($FormValues),
                $customerEmail
            );
        }
    }
}

// process emails
processEmails(
    $dbName,
    $header,
    "/var/www/html/send_email/fee_payment_cycle/files/Billing_Cycle_Notification_For_",
    "[DIGINEXT] - THÔNG BÁO CHU KỲ THANH TOÁN CƯỚC ĐẦU SỐ DỊCH VỤ",
);