<?php

require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/report_ctc/includes/custom_tcpdf.php';
require_once '/var/www/html/send_email/report_ctc/includes/helpers.php';


use PhpOffice\PhpSpreadsheet\IOFactory;

function convertExcelToPDF($excelFilePath, $pdfFileName, $userName, $ccuValues, $ccuTotals)
{
    try {
        // Load Excel file
        $spreadsheet = IOFactory::load($excelFilePath);
        $currentTime = date('H:i d-m-Y');

        // Create PDF object
        $headerInfo = "Thời gian kiểm tra: " . $currentTime . " - Nhân viên: " . $userName;
        $pdf = new CustomTCPDF($headerInfo);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($userName);
        $pdf->SetTitle('Excel to PDF Conversion');
        $pdf->SetSubject('Excel to PDF Conversion');
        $pdf->SetFont('dejavusans', 'I', 11);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);


        // Get all sheets in the Excel file
        $sheets = $spreadsheet->getAllSheets();

        foreach ($sheets as $sheet) {
            // Get the highest column and row indexes
            $highestColumn = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();

            // Extract data from the sheet
            $data = $sheet->rangeToArray('A1:' . $highestColumn . $highestRow, NULL, TRUE, FALSE);

            // Add header row with a different background color
            $pdf->SetFillColor(135, 206, 250); // Lightest shade of blue
            $pdf->setDrawColor(200, 250, 250); // Light blue background

            // Dynamically calculate cell width
            $cellWidth = 170 / (count($data[0])); // Adding one for the STT column

            foreach ($data[0] as $headerValue) {
                $headerValue = translateHeader($headerValue);

                switch ($headerValue) {
                    case 'Khách Hàng':
                        $pdf->Cell($cellWidth + 30, 10, $headerValue, 1, 0, 'C', true);
                        break;

                    case 'CCU':
                        $pdf->Cell($cellWidth - 15, 10, $headerValue, 1, 0, 'C', true);
                        break;

                    case 'Số Tiền':
                        $pdf->Cell($cellWidth + 5, 10, $headerValue, 1, 0, 'C', true);
                        break;

                    default:
                        // Decrease width for other columns
                        $pdf->Cell($cellWidth, 10, $headerValue, 1, 0, 'C', true);
                        break;
                }
            }
            $pdf->Ln();

            $totalCost = 0;
            $totalCCU = 0;
            $totalBlockViettel = 0;
            $totalBlockMobifone = 0;

            $rowCounter = 0;

            foreach (array_slice($data, 1) as $row) {
                $pdf->SetFillColor(242, 255, 255);

                $rowCounter++;

                $ccuValue = isset($_POST['ccu'][$rowCounter]) ? $_POST['ccu'][$rowCounter] : '';
                if (empty($ccuValue)) {
                    continue; // Skip the row if CCU value is empty
                }

                foreach ($row as $key => $cell) {
                    $cellInfo = modifyValue($data[0][$key], $cell);

                    switch ($data[0][$key]) {
                        case 'CustomerName':
                            $pdf->Cell($cellWidth + 30, 10, $cellInfo['value'], 1, 0, '', getRowColor($rowCounter - 1));
                            break;

                        case 'SalerName':
                            $pdf->Cell($cellWidth, 10, $cellInfo['value'], 1, 0, 'C', getRowColor($rowCounter - 1));
                            break;

                        case 'TotalCost':
                            $totalCost += $row[$key];
                            $pdf->Cell($cellWidth + 5, 10, $cellInfo['value'], 1, 0, $cellInfo['align'], getRowColor($rowCounter - 1));
                            break;

                        case 'BlockViettel':
                            $totalBlockViettel += $row[$key];
                            $cellValue = ($cell === null) ? 0 : $cellInfo['value'];
                            $pdf->Cell($cellWidth, 10, $cellValue, 1, 0, $cellInfo['align'], getRowColor($rowCounter - 1));
                            break;

                        case 'BlockMobifone':
                            $totalBlockMobifone += $row[$key];
                            $cellValue = ($cell === null) ? 0 : $cellInfo['value'];
                            $pdf->Cell($cellWidth, 10, $cellValue, 1, 0, $cellInfo['align'], getRowColor($rowCounter - 1));
                            break;

                        case 'TotalCurrentCall':
                            $totalCCU += (int)$ccuValue;
                            $pdf->Cell($cellWidth - 15, 10, $ccuValue, 1, 0, $cellInfo['align'], getRowColor($rowCounter - 1));
                            break;

                        default:
                            $pdf->Cell($cellWidth, 10, $cellInfo['value'], 1, 0, '', getRowColor($rowCounter - 1));
                            break;
                    }
                }
                $pdf->Ln();
            }


            $pdf->SetFont('dejavusans', 'I', 11);
            // Add three additional rows at the end

            $pdf->SetFillColor(135, 206, 250); // Lightest shade of blue
            $pdf->Cell($cellWidth * (count($data[0]) - 4) + 30, 10, 'TỔNG KHÁCH HÀNG LỚN', 1, 0, 'C', true); // STT column
            $pdf->SetTextColor(255, 0, 0); // Set text color to red
            $pdf->Cell($cellWidth + 5, 10, number_format($totalCost, 0, '.', ','), 1, 0, 'C', true);
            $pdf->Cell($cellWidth - 15, 10, $totalCCU, 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, $totalBlockViettel, 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, number_format($totalBlockMobifone, 0, '.', ','), 1, 0, 'C', true);
            $pdf->SetTextColor(0, 0, 0); // Reset text color to black (optional, if you want to revert to black for subsequent cells)
            $pdf->Ln();

            // Set the fill color for the second row (lighter shade of blue)
            $pdf->SetFillColor(173, 216, 250);
            $pdf->Cell($cellWidth * (count($data[0]) - 4) + 30, 10, 'TỔNG KHÁCH HÀNG CÒN LẠI', 1, 0, 'C', true); // Merge cells for the first three columns
            $pdf->SetTextColor(255, 0, 0); // Set text color to red
            $pdf->Cell($cellWidth + 5, 10, '', 1, 0, 'C', true);
            $pdf->Cell($cellWidth - 15, 10, number_format($ccuTotals - $totalCCU, 0, '.', ','), 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, '', 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, '', 1, 0, 'C', true);
            $pdf->SetTextColor(0, 0, 0); // Reset text color to black (optional, if you want to revert to black for subsequent cells)
            $pdf->Ln();

            // Set the fill color for the third row (lightest shade of blue)
            $pdf->SetFillColor(200, 220, 250); // Lightest shade of blue
            $pdf->Cell($cellWidth * (count($data[0]) - 4) + 30, 10, 'TỔNG HỆ THỐNG', 1, 0, 'C', true); // Merge cells for the first three columns
            $pdf->SetTextColor(255, 0, 0); // Set text color to red
            $pdf->Cell($cellWidth + 5, 10, '', 1, 0, 'C', true);
            $pdf->Cell($cellWidth - 15, 10, number_format($ccuTotals, 0, '.', ','), 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, '', 1, 0, 'C', true);
            $pdf->Cell($cellWidth, 10, '', 1, 0, 'C', true);
            $pdf->SetTextColor(0, 0, 0); // Reset text color to black (optional, if you want to revert to black for subsequent cells)
            $pdf->Ln();
        }

        $pdfFilePath = '/var/www/html/' . $pdfFileName;

        // Output PDF to file
        $pdf->Output($pdfFilePath, 'F');

        echo "File $pdfFileName converted successfully.\n";
        return $pdfFilePath; // Return the file path
    } catch (Exception $e) {
        echo "Error converting Excel to PDF: " . $e->getMessage() . "\n";
        return false;
    }
}