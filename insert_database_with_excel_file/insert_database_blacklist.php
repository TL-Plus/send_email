<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function insertDataFromExcel($inputFileName)
{
    // Establish a database connection
    $conn = connectDatabase($_ENV['DB_HOSTNAME_DIGINEXT'], $_ENV['DB_USERNAME_DIGINEXT'], $_ENV['DB_PASSWORD_DIGINEXT'], $_ENV['DB_DATABASE_BLACKLIST']);

    // Prepare the SQL statement for data insertion
    $sql = "INSERT INTO `BlackList` (`msisdn`, `telco`, `shortcode`, `info`, `mo_time`, `cmd_code`, `error_code`, `error_desc`, `updated_at`, `created_at`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    // Prepare the SQL statement once outside the loop for better performance
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $msisdn, $telco, $shortcode, $info, $mo_time, $cmd_code, $error_code, $error_desc);

        // Array to store unique msisdn values
        $uniqueMsisdns = [];

        // Create a new PhpSpreadsheet object
        try {
            $spreadsheet = IOFactory::load($inputFileName);
        } catch (Exception $e) {
            die('Error loading Excel file: ' . $e->getMessage());
        }

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

                // Check if msisdn already exists in the database
                $checkIfExistsQuery = "SELECT COUNT(*) FROM `BlackListBK` WHERE `msisdn` = ?";
                $checkIfExistsStmt = $conn->prepare($checkIfExistsQuery);
                $checkIfExistsStmt->bind_param("s", $msisdn);
                $checkIfExistsStmt->execute();
                $checkIfExistsStmt->bind_result($count);
                $checkIfExistsStmt->fetch();
                $checkIfExistsStmt->close();

                if ($count == 0) {
                    // Execute the prepared statement for insertion
                    if (!$stmt->execute()) {
                        echo "Error: " . $stmt->error . "<br>";
                    }
                } else {
                    echo "Duplicate msisdn found in the database: $msisdn. Skipping insertion.<br>";
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
