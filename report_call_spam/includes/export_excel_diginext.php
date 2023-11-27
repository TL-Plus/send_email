<?php
require 'vendor/autoload.php';
require_once 'send_email/includes/config.php';

function exportToExcelDiginext($sql, $filename)
{
    $result = connectAndQueryDatabase($sql, DB_HOSTNAME_DIGINEXT, DB_USERNAME_DIGINEXT, DB_PASSWORD_DIGINEXT, DB_DATABASE_DIGINEXT);

    if ($result && $result->num_rows > 0) {
        // Create or reuse a spreadsheet object
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Table title
        $header = ['CustomerName', 'ContractCode', 'Number'];

        // Set the title in the Excel file
        $sheet->fromArray([$header], NULL, 'A1');

        // Data from the database
        $row = 2;
        while ($row_data = $result->fetch_assoc()) {
            $column = 'A';
            foreach ($row_data as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        // Save the Excel file
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filename);
        echo "File $filename exported successfully.";
    } else {
        echo "There is no data to export for this query.";
    }
}
