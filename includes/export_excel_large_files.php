<?php
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;


function exportToExcelLargeFiles($sql, $dbName, $header, $baseFilename)
{
    // Set the maximum number of rows to fetch in each iteration
    $batchSize = 250000;

    // Create or reuse a spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the title in the Excel file
    $sheet->fromArray([$header], NULL, 'A1');
    $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
    $headerStyle->getFont()->setBold(true);

    // Initialize variables for pagination
    $offset = 0;
    $limit = $batchSize;
    $fileCounter = 1;
    $exportedFiles = [];

    while (($result = fetchDataBatch($sql, $dbName, $offset, $limit)) !== false) {
        if ($result->num_rows > 0) {
            // Process data and export to Excel
            $exportedFiles[] = processAndExportData($result, $header, $spreadsheet, $baseFilename, $fileCounter);

            // Move to the next batch and increment file counter
            $offset += $batchSize;
            $limit = min($batchSize, $result->num_rows);
            $fileCounter++;
        } else {
            echo "There is no more data to export for this query.\n";
            break;
        }
    }

    return $exportedFiles;
}

function processAndExportData($result, $header, $spreadsheet, $baseFilename, $fileCounter)
{
    $columnWidths = [];

    // Data from the database
    $row = 2;
    while ($row_data = $result->fetch_assoc()) {
        $column = 'A';
        foreach ($row_data as $key => $value) {
            if ($key === 'DateStarted' || $key === 'DateEnded') {
                // Handle time formats
                $value = date('Y-m-d H:i:s', strtotime($value));
            }

            // Set cell value explicitly to handle different data types
            $spreadsheet->getActiveSheet()->setCellValueExplicit($column . $row, $value, DataType::TYPE_STRING);

            // Calculate column width based on the length of the longest string
            $columnWidths[$column] = max(strlen($value) + 2, isset($columnWidths[$column]) ? $columnWidths[$column] : 0);

            $column++;
        }
        $row++;
    }

    // Calculate header column widths based on the length of the longest string
    $column = 'A';
    $headerWidths = [];
    foreach ($header as $headerItem) {
        $headerWidths[$column] = strlen($headerItem) + 2;
        $column++;
    }

    // Update header column widths with the maximum of header and data lengths
    foreach ($headerWidths as $column => $width) {
        $headerWidths[$column] = max($headerWidths[$column], isset($columnWidths[$column]) ? $columnWidths[$column] : 0);
    }

    // Set column widths
    foreach ($headerWidths as $column => $width) {
        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($width);
    }

    // Create a table
    $lastColumn = $spreadsheet->getActiveSheet()->getHighestColumn();
    $table = new Table("A1:$lastColumn$row");
    $table->setRange('A1:' . $lastColumn . $row);

    // Create a table style
    $tableStyle = new TableStyle();
    $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
    $tableStyle->setShowRowStripes(true);

    // Apply table style to the table
    $table->setStyle($tableStyle);

    // Add the table to the worksheet
    $spreadsheet->getActiveSheet()->addTable($table);

    // Save the Excel file with a dynamic filename
    $filename = $baseFilename . '_part' . $fileCounter . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);

    echo "File $filename exported successfully.\n";

    return $filename;
}

function fetchDataBatch($sql, $dbName, $offset, $limit)
{
    // Append LIMIT and OFFSET to the original SQL query
    $sql .= " LIMIT $limit OFFSET $offset";

    return connectAndQueryDatabase(
        $sql,
        $_ENV['DB_HOSTNAME_MAIN'],
        $_ENV['DB_USERNAME_MAIN'],
        $_ENV['DB_PASSWORD_MAIN'],
        $dbName
    );
}
