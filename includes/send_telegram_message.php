<?php
require_once 'vendor/autoload.php';
require_once 'send_email/config.php';
require_once 'export_excel.php';

use TelegramBot\Api\BotApi;
use PhpOffice\PhpSpreadsheet\IOFactory;


function sendTelegramMessageWithSql($sql, $dbName, $header, $filename, $textMessage, $botToken, $chatId)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $filename);

        // Check if export was successful before sending telegram message
        if ($exportSuccessful) {
            // Read content from the Excel file
            $spreadsheet = IOFactory::load($filename);
            $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Initialize the Telegram API object with your bot token
            $telegram = new BotApi($botToken);

            // Prepare the document for sending
            $document = new \CURLFile($filename);

            // Send the text message along with the document
            $telegram->sendDocument($chatId, $document, $textMessage);

            echo "Telegram message successfully sent file $filename \n";
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function sendTelegramMessages($filename, $textMessage, $botToken, $chatId)
{
    try {
        // Read content from the Excel file
        $spreadsheet = IOFactory::load($filename);
        $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi($botToken);

        // Prepare the document for sending
        $document = new \CURLFile($filename);

        // Send the text message along with the document
        $telegram->sendDocument($chatId, $document, $textMessage);

        echo "Telegram message successfully sent file $filename \n";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}