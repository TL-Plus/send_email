<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_telegram_message.php';
require 'send_email/includes/export_excel_large_files.php';

// Query for fetching data
$query_report_all_do_not_call_blacklist_for_weeks = "
    SELECT `msisdn`, `telco`, `shortcode`, `info`, `mo_time`, `cmd_code`, 
    `error_code`, `error_desc`, `updated_at`, `created_at`
    FROM `BlackList`
    WHERE `cmd_code` = 'DK'
    AND (`msisdn`, `updated_at`) IN (
        SELECT `msisdn`, MAX(`updated_at`) AS latest_time
        FROM `BlackList`
        GROUP BY `msisdn`
    )
    ORDER BY `BlackList`.`updated_at` ASC
";

// Define Excel header
$header = [
    'msisdn', 'telco', 'shortcode', 'info', 'mo_time', 'cmd_code', 'error_code', 'error_desc', 'updated_at', 'created_at'
];

// Define $dbName, $chatId
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

// Define the base filename for the exported files
$baseFilename = "Report_all_DNC";

// Call the function to export data to Excel
$exportedFiles = exportToExcelLargeFiles($query_report_all_do_not_call_blacklist_for_weeks, $dbName, $header, $baseFilename);

// Check if export was successful before iterating over the result
if ($exportedFiles !== false) {
    // Define $subject
    $subject = "Report all DNC DIGINEXT";

    // Send each exported file to Telegram
    foreach ($exportedFiles as $exportedFile) {
        // Call the function to send a message via Telegram
        sendTelegramMessages($exportedFile, $subject, $chatId);
    }
} else {
    echo "Export failed or no data to export.\n";
}