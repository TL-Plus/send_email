<?php
require 'vendor/autoload.php';
require_once 'send_email/includes/config.php';
require_once 'export_list_numbers.php';

// Function to export data to Excel
function exportToExcel($sql1, $sql2, $filename)
{
    // Perform the first query
    $result_query1 = connectAndQueryDatabase($sql1, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    // Connect to the database for the second query
    $result_query2 = connectAndQueryDatabase($sql2, DB_HOSTNAME_DIGINEXT, DB_USERNAME_DIGINEXT, DB_PASSWORD_DIGINEXT, DB_DATABASE_DIGINEXT);

    // Check if both queries were successful
    if ($result_query1 && $result_query2) {
        // Create a new Excel file
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the title in the Excel file
        $header = ['CustomerName', 'ContractCode', 'Caller', 'Callee', 'SL'];
        $sheet->fromArray([$header], NULL, 'A1');

        // Process the result of the first query
        $row = 2;
        while ($row_data1 = $result_query1->fetch_assoc()) {
            // Set values from the first query
            $sheet->setCellValue('C' . $row, $row_data1['Caller']);
            $sheet->setCellValue('D' . $row, $row_data1['Callee']);
            $sheet->setCellValue('E' . $row, $row_data1['SL']);
            $row++;
        }

        // Store the result of the second query in a temporary array
        $data2Array = [];
        while ($row_data2 = $result_query2->fetch_assoc()) {
            $data2Array[] = $row_data2;
        }

        // Match and update values in the Excel file
        foreach ($data2Array as $row_data2) {
            foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $excelRow) {
                $callerColumn = $spreadsheet->getActiveSheet()->getCell('C' . $excelRow->getRowIndex())->getValue();
                $extNumberWithPrefix = "84" . $row_data2['Caller'];

                if ($callerColumn == $extNumberWithPrefix) {
                    $spreadsheet->getActiveSheet()->setCellValue('A' . $excelRow->getRowIndex(), $row_data2['CustomerName']);
                    $spreadsheet->getActiveSheet()->setCellValue('B' . $excelRow->getRowIndex(), $row_data2['ContractCode']);
                }
            }
        }

        // Save the Excel file
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filename);
        echo "File $filename exported successfully.";
    } else {
        // Handle query error
        echo "Error executing query.";
    }
}
