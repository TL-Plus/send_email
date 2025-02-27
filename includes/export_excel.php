<?php
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';

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

        return "File $filename exported successfully.\n";
    } else {
        return "There is no data to export for this query.\n";
    }
}

function exportToExcelMain($sql, $dbName, $header, $filename)
{
    $result = connectAndQueryDatabase(
        $sql,
        $_ENV['DB_HOSTNAME_MAIN'],
        $_ENV['DB_USERNAME_MAIN'],
        $_ENV['DB_PASSWORD_MAIN'],
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

        return "File $filename exported successfully.\n";
    } else {
        return "There is no data to export for this query.\n";
    }
}

function exportToExcels($queries, $dbName, $header, $filename)
{
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

    // Data row index
    $row = 2;
    $columnWidths = [];

    foreach ($queries as $query) {
        $result = connectAndQueryDatabase(
            $query,
            $_ENV['DB_HOSTNAME_DIGINEXT'],
            $_ENV['DB_USERNAME_DIGINEXT'],
            $_ENV['DB_PASSWORD_DIGINEXT'],
            $dbName
        );

        if ($result && $result->num_rows > 0) {
            while ($row_data = $result->fetch_assoc()) {
                $column = 'A';
                foreach ($row_data as $key => $value) {
                    // Handle time formats
                    if ($key === 'DateStarted' || $key === 'DateEnded') {
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
        }
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

    return "File $filename exported successfully.\n";
}


function exportToExcels152($queries, $dbName, $header, $filename)
{
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

    // Data row index
    $row = 2;
    $columnWidths = [];

    foreach ($queries as $query) {
        $result = connectAndQueryDatabase(
            $query,
            $_ENV['DB_HOSTNAME_DIGINEXT_TEST'],
            $_ENV['DB_USERNAME_DIGINEXT_TEST'],
            $_ENV['DB_PASSWORD_DIGINEXT_TEST'],
            $dbName
        );

        if ($result && $result->num_rows > 0) {
            while ($row_data = $result->fetch_assoc()) {
                $column = 'A';
                foreach ($row_data as $key => $value) {
                    // Handle time formats
                    if ($key === 'DateStarted' || $key === 'DateEnded') {
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
        }
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

    return "File $filename exported successfully.\n";
}
