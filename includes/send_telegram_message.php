<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'export_excel.php';

use TelegramBot\Api\BotApi;
use PhpOffice\PhpSpreadsheet\IOFactory;

function sendTelegramMessage($sql, $header, $filename, $textMessage, $chatId)
{
    try {
        exportToExcel($sql, $header, $filename);

        // Read content from the Excel file
        $spreadsheet = IOFactory::load($filename);
        $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Initialize the Telegram API object with your bot token
        $telegram = new BotApi(TELEGRAM_BOT_TOKEN);

        // Prepare the document for sending
        $document = new \CURLFile($filename);

        // Send the text message along with the document
        $telegram->sendDocument($chatId, $document, $textMessage);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
