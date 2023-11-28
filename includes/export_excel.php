<?php
require 'vendor/autoload.php';
require_once 'config.php';

function exportToExcel($sql, $header, $filename)
{
    $result = connectAndQueryDatabase($sql, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    if ($result && $result->num_rows > 0) {
        // Create or reuse a spreadsheet object
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the title in the Excel file
        $sheet->fromArray([$header], NULL, 'A1');

        // Data from the database
        $row = 2;
        while ($row_data = $result->fetch_assoc()) {
            $column = 'A';
            foreach ($row_data as $key => $value) {
                if ($key === 'DateStarted' || $key === 'DateEnded') {
                    // Handle time formats
                    $value = date('Y-m-d H:i:s', strtotime($value));
                }
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
