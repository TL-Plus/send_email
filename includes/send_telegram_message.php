<?php
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/export_excel.php';

use TelegramBot\Api\BotApi;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Send telegram message with SQL and file
function sendTelegramMessageWithSql($sql, $dbName, $header, $attachment, $textMessage, $botToken, $chatId)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending telegram message
        if ($exportSuccessful) {
            // Read content from the Excel file
            $spreadsheet = IOFactory::load($attachment);
            $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Initialize the Telegram API object with your bot token
            $telegram = new BotApi($botToken);

            // Prepare the document for sending
            $document = new \CURLFile($attachment);

            // Send the text message along with the document
            $telegram->sendDocument($chatId, $document, $textMessage);

            echo "Telegram message successfully sent file $attachment \n";
        }
    } catch (Exception $e) {
        echo 'Error send telegram: ' . $e->getMessage();
    }
}

function sendTelegramMessageWithSqlMain($sql, $dbName, $header, $attachment, $textMessage, $botToken, $chatId)
{
    try {
        $exportSuccessful = exportToExcelMain($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending telegram message
        if ($exportSuccessful) {
            // Read content from the Excel file
            $spreadsheet = IOFactory::load($attachment);
            $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Initialize the Telegram API object with your bot token
            $telegram = new BotApi($botToken);

            // Prepare the document for sending
            $document = new \CURLFile($attachment);

            // Send the text message along with the document
            $telegram->sendDocument($chatId, $document, $textMessage);

            echo "Telegram message successfully sent file $attachment \n";
        }
    } catch (Exception $e) {
        echo 'Error send telegram: ' . $e->getMessage();
    }
}

// Send telegram message with file excel
function sendTelegramMessages($attachment, $textMessage, $botToken, $chatId)
{
    try {
        // Read content from the Excel file
        $spreadsheet = IOFactory::load($attachment);
        $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi($botToken);

        // Prepare the document for sending
        $document = new \CURLFile($attachment);

        // Send the text message along with the document
        $telegram->sendDocument($chatId, $document, $textMessage);

        return "Telegram message successfully sent file $attachment \n";
    } catch (Exception $e) {
        return 'Error send telegram: ' . $e->getMessage();
    }
}

// Send telegram message with sql and file pdf
function sendTelegramMessagesWithSqlAndFileExcel($sql, $dbName, $header, $excelFilePath, $textMessage, $botToken, $chatId)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $excelFilePath);

        // Check if export was successful before sending telegram message
        if ($exportSuccessful) {
            // Initialize the Telegram API object with your bot token
            $telegram = new BotApi($botToken);

            // Prepare the document for sending
            $document = new \CURLFile($excelFilePath);

            // Send the text message along with the document
            $telegram->sendDocument($chatId, $document, $textMessage);

            echo "Telegram message successfully sent file $excelFilePath \n";
        }
    } catch (Exception $e) {
        echo 'Error send telegram: ' . $e->getMessage();
    }
}

// Send telegram message with file pdf
function sendTelegramMessagesWithFilePDF($pdfFilePath, $textMessage, $botToken, $chatId)
{
    try {
        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi($botToken);

        // Prepare the document for sending
        $document = new \CURLFile(realpath($pdfFilePath), 'application/pdf');

        // Send the text message along with the document
        $telegram->sendDocument($chatId, $document, $textMessage);

        echo "Telegram message successfully sent with file $pdfFilePath\n";
    } catch (Exception $e) {
        echo 'Error send telegram: ' . $e->getMessage();
    }
}

// Send telegram message with zip file attachment
function sendTelegramMessagesWithFileZip($zipFilePath, $textMessage, $botToken, $chatId)
{
    try {
        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi($botToken);

        // Prepare the document for sending
        $document = new \CURLFile(realpath($zipFilePath), 'application/zip');

        // Send the text message along with the document
        $telegram->sendDocument($chatId, $document, $textMessage);

        return "Telegram message successfully sent with file $zipFilePath\n";
    } catch (Exception $e) {
        return 'Error send telegram: ' . $e->getMessage();
    }
}

// Send telegram message
function sendTelegramMessage($textMessage, $botToken, $chatId)
{
    try {
        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi($botToken);

        // Send the text message
        $telegram->sendMessage($chatId, $textMessage);

        echo "The telegram message was sent.\n";
    } catch (Exception $e) {
        echo 'Error send telegram: ' . $e->getMessage();
    }
}
