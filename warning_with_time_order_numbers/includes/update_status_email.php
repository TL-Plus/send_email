<?php

// Function to update status_email after sending email and add log entry
function updateStatusEmail($sql)
{
    try {
        // Connect to the database
        $conn = connectDatabase(
            $_ENV['DB_HOSTNAME_DIGINEXT'],
            $_ENV['DB_USERNAME_DIGINEXT'],
            $_ENV['DB_PASSWORD_DIGINEXT'],
            $_ENV['DB_DATABASE_BILLING_DIGINEXT']
        );

        // Fetch the order numbers matching the given criteria
        $selectQuery = "SELECT `order_number` FROM ($sql) AS subquery";
        $result = $conn->query($selectQuery);

        if ($result) {
            $orderNumbers = [];
            while ($row = $result->fetch_assoc()) {
                $orderNumbers[] = "'" . $row['order_number'] . "'";
            }

            // Update status_email to 0 for the matched order numbers
            if (!empty($orderNumbers)) {
                $orderNumbersString = implode(',', $orderNumbers);

                // Update status_email and add log entry
                $updateQuery = "UPDATE `order_numbers` 
                                SET 
                                    `status_email` = 1,
                                    `log` = CONCAT(`log`, 'admin-status_email_0-status_email_1-', NOW(), ' | ')
                                WHERE `order_number` IN ($orderNumbersString) AND `status_email` = 0";
                $conn->query($updateQuery);
            }
        }

        // Close the database connection
        $conn->close();
    } catch (Exception $e) {
        echo 'Error updating status_email: ' . $e->getMessage();
    }
}