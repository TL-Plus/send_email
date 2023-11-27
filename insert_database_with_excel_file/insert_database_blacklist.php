<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function insertDataFromExcel($inputFileName)
{
    // Create a new PhpSpreadsheet object
    try {
        $spreadsheet = IOFactory::load($inputFileName);
    } catch (Exception $e) {
        die('Error loading Excel file: ' . $e->getMessage());
    }

    // Establish a database connection
    $conn = connectDatabase(DB_HOSTNAME_DIGINEXT, DB_USERNAME_DIGINEXT, DB_PASSWORD_DIGINEXT, DB_DATABASE_DIGINEXT);

    // Prepare the SQL statement
    $sql = "INSERT INTO `BlackList` (`msisdn`, `telco`, `shortcode`, `info`, `mo_time`, `cmd_code`, `error_code`, `error_desc`, `updated_at`, `created_at`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    // Prepare the SQL statement once outside the loop for better performance
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $msisdn, $telco, $shortcode, $info, $mo_time, $cmd_code, $error_code, $error_desc);

        // Array to store unique msisdn values
        $uniqueMsisdns = [];

        // Loop through each row in the Excel file
        foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row) {
            // Skip the header row
            if ($row->getRowIndex() == 1) {
                continue;
            }

            // Get cell values
            $data = [];
            foreach ($row->getCellIterator() as $cell) {
                $data[] = $cell->getValue();
            }

            // Assign values to variables
            list($msisdn, $telco, $shortcode, $info, $mo_time, $cmd_code, $error_code, $error_desc) = $data;

            // Check if msisdn is unique before inserting
            if (!in_array($msisdn, $uniqueMsisdns)) {
                $uniqueMsisdns[] = $msisdn;

                // Ensure telco is a two-digit number
                $telco = str_pad($telco, 2, '0', STR_PAD_LEFT);

                // Execute the prepared statement
                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error . "<br>";
                }
            } else {
                echo "Duplicate msisdn found: $msisdn. Skipping insertion.<br>";
            }
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $conn->error . "<br>";
    }

    $conn->close();
    echo "Data inserted successfully!";
}
