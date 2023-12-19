<?php
require 'vendor/autoload.php';
require_once 'send_email/config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;


function exportToExcel($sql, $dbName, $header, $filename)
{
    $result = connectAndQueryDatabase(
        $sql,
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName
    );

    if ($result && $result->num_rows > 0) {
        // Create or reuse a spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the title in the Excel file
        $sheet->fromArray([$header], NULL, 'A1');

        // Make header row bold
        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);

        // Calculate header column widths based on the length of the longest string
        $column = 'A';
        $headerWidths = [];
        foreach ($header as $headerItem) {
            $headerWidths[$column] = strlen($headerItem) + 2;
            $column++;
        }

        // Data from the database
        $row = 2;
        $columnWidths = [];

        while ($row_data = $result->fetch_assoc()) {
            $column = 'A';
            foreach ($row_data as $key => $value) {
                if ($key === 'DateStarted' || $key === 'DateEnded') {
                    // Handle time formats
                    $value = date('Y-m-d H:i:s', strtotime($value));
                }

                // Set cell value explicitly to handle different data types
                $sheet->setCellValueExplicit($column . $row, $value, DataType::TYPE_STRING);

                // Calculate column width based on the length of the longest string
                $columnWidths[$column] = max(strlen($value) + 2, isset($columnWidths[$column]) ? $columnWidths[$column] : 0);

                $column++;
            }
            $row++;
        }

        // Update header column widths with the maximum of header and data lengths
        foreach ($headerWidths as $column => $width) {
            $headerWidths[$column] = max($headerWidths[$column], isset($columnWidths[$column]) ? $columnWidths[$column] : 0);
        }

        // Set column widths
        foreach ($headerWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Create a table
        $lastColumn = $sheet->getHighestColumn();
        $table = new Table("A1:$lastColumn$row");
        $table->setRange('A1:' . $lastColumn . $row);

        // Create a table style
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
        $tableStyle->setShowRowStripes(true);

        // Apply table style to the table
        $table->setStyle($tableStyle);

        // Add the table to the worksheet
        $sheet->addTable($table);

        // Save the Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        echo "File $filename exported successfully.\n";
        return true;
    } else {
        echo "There is no data to export for this query.\n";
        return false;
    }
}