<?php

// Function to update status_email after sending email and add log entry
function updateStatusEmail7Days($orderNumber, $threshold, $orderNumberCondition, $userCode)
{
    try {
        // Connect to the database
        $conn = connectDatabase(
            $_ENV['DB_HOSTNAME_MAIN'],
            $_ENV['DB_USERNAME_MAIN'],
            $_ENV['DB_PASSWORD_MAIN'],
            $_ENV['DB_DATABASE_BILLING_MAIN']
        );

        // Start a transaction
        $conn->begin_transaction();

        // Update status_email and add log entry
        $updateQueryOrderNumber = "UPDATE order_numbers
            SET updated_at = NOW(), last_updated_at=NOW(), status_email = 1,
                log = CONCAT(log, NOW(), '__', 'SysDigi-auto-update-status_email-0-1 | ')
            WHERE order_number IN ('$orderNumber') 
                AND DATEDIFF(NOW(), order_time) >= $threshold 
                AND status = 'holding'
                AND status_email = 0 
                AND note = ''
                AND IsShow = 1
                $orderNumberCondition
                AND customer_code = '$userCode'
            ORDER BY order_time DESC";
        $conn->query($updateQueryOrderNumber);

        // Check if any rows were affected
        if ($conn->affected_rows > 0) {
            echo "SysDigi - Update successfully status_email for order_numbers with number $orderNumber at $threshold days. \n";
        } else {
            echo "SysDigi - No rows were updated for order_numbers with number $orderNumber. \n";
        }

        // Commit the transaction
        $conn->commit();

        // Close the database connection
        $conn->close();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "SysDigi - Error updating status_email for order_numbers with number '$orderNumber' at '$threshold' days: " . $e->getMessage() . "\n";
    }
}

// function updateStatusEmail7DaysDIGITEL($orderNumber, $threshold)
// {
//     try {
//         // Connect to the database
//         $conn = connectDatabase(
//             $_ENV['DB_HOSTNAME_DIGITEL'],
//             $_ENV['DB_USERNAME_DIGITEL'],
//             $_ENV['DB_PASSWORD_DIGITEL'],
//             $_ENV['DB_DATABASE_DIGITEL']
//         );

//         $next_three_digits_2 = substr($orderNumber, 2, 3);
//         $next_three_digits_3 = substr($orderNumber, 3, 3);

//         // Determine which table to update based on orderNumber prefix
//         $orderTableName = '';
//         if (substr($orderNumber, 0, 4) == '1900' || substr($orderNumber, 0, 4) == '1800') {
//             $orderTableName = 'OrderNumberDVGTGT';
//         } elseif (substr($orderNumber, 0, 1) == '2' && (strpos($next_three_digits_2, '888') !== false || strpos($next_three_digits_3, '888') !== false)) {
//             $orderTableName = 'OrderNumber';
//         }

//         // Start a transaction
//         $conn->begin_transaction();

//         // Update status_email and add log entry
//         if ($orderTableName !== '') {
//             $updateQueryOrderNumber = "UPDATE $orderTableName
//                 SET UpdationTime = NOW(), StatusEmail = 1, 
//                     HistoryLog = CONCAT(HistoryLog, NOW(), '__', 'Diginext-update-StatusEmail-0-1 | ')
//                 WHERE OrderNumber IN ('$orderNumber') 
//                     AND DATEDIFF(NOW(), OrderTime) >= $threshold 
//                     AND StatusNumber = 1
//                     AND StatusEmail = 0";
//             $conn->query($updateQueryOrderNumber);

//             // Check if any rows were affected
//             if ($conn->affected_rows > 0) {
//                 echo "Digitel - Update successfully StatusEmail for $orderTableName with number $orderNumber at $threshold days. \n";
//             } else {
//                 echo "Digitel - No rows were updated for $orderTableName with number $orderNumber. \n";
//             }
//         } else {
//             echo "Digitel - OrderNumber prefix not recognized.";
//         }

//         // Commit the transaction
//         $conn->commit();

//         // Close the database connection
//         $conn->close();
//     } catch (Exception $e) {
//         // Rollback the transaction if an error occurs
//         $conn->rollback();
//         echo "Digitel - Error updating StatusEmail for $orderTableName with number $orderNumber at $threshold days: " . $e->getMessage() . "\n";
//     }
// }

function updateStatusEmail14Days($orderNumber, $threshold, $orderNumberCondition, $userCode)
{
    try {
        // Connect to the database
        $conn = connectDatabase(
            $_ENV['DB_HOSTNAME_MAIN'],
            $_ENV['DB_USERNAME_MAIN'],
            $_ENV['DB_PASSWORD_MAIN'],
            $_ENV['DB_DATABASE_BILLING_MAIN']
        );

        // Start a transaction
        $conn->begin_transaction();

        // Update status_email and add log entry for order_numbers table
        $updateQueryOrderNumber = "UPDATE order_numbers
            SET updated_at = NOW(), last_updated_at=NOW(), status = 'expired',
                log = CONCAT(log, NOW(), '__', 'SysDigi-auto-update-status-holding-expired | ')
            WHERE order_number IN ('$orderNumber') 
                AND DATEDIFF(NOW(), order_time) >= $threshold 
                AND status = 'holding'
                AND status_email = 1
                AND note = ''
                AND IsShow = 1
                $orderNumberCondition
                AND customer_code = '$userCode'
            ORDER BY order_time DESC";
        $conn->query($updateQueryOrderNumber);

        // Check if any rows were affected for order_numbers table
        if ($conn->affected_rows > 0) {
            echo "SysDigi - Update successfully status for order_numbers with number $orderNumber at $threshold days. \n";
        } else {
            echo "SysDigi - No rows were updated for order_numbers with number $orderNumber. \n";
        }

        // Update status and add log entry for service_numbers table
        $updateQueryServiceNumber = "UPDATE service_numbers
            SET updated_at = NOW(), last_updated_at=NOW(), status = 'inStock',
                log = CONCAT(log, NOW(), '__', 'SysDigi-auto-update-status-holding-inStock | ')
            WHERE number IN ('$orderNumber') 
                AND status = 'holding'
                AND IsShow = 1
                AND apikey = ''";
        $conn->query($updateQueryServiceNumber);

        // Check if any rows were affected for service_numbers table
        if ($conn->affected_rows > 0) {
            echo "SysDigi - Update successfully status for service_numbers with number $orderNumber at $threshold days. \n";
        } else {
            echo "SysDigi - No rows were updated for service_numbers with number $orderNumber. \n";
        }

        // Commit the transaction
        $conn->commit();

        // Close the database connection
        $conn->close();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "SysDigi - Error updating status with number $orderNumber at $threshold days: " . $e->getMessage() . "\n";
    }
}

// function updateStatusEmail14DaysDIGITEL($orderNumber, $threshold)
// {
//     try {
//         // Connect to the database
//         $conn = connectDatabase(
//             $_ENV['DB_HOSTNAME_DIGITEL'],
//             $_ENV['DB_USERNAME_DIGITEL'],
//             $_ENV['DB_PASSWORD_DIGITEL'],
//             $_ENV['DB_DATABASE_DIGITEL']
//         );

//         $next_three_digits_2 = substr($orderNumber, 2, 3);
//         $next_three_digits_3 = substr($orderNumber, 3, 3);

//         // Determine which table to update based on orderNumber prefix
//         $orderTableName = '';
//         $serviceTableName = '';
//         if (substr($orderNumber, 0, 4) == '1900' || substr($orderNumber, 0, 4) == '1800') {
//             $orderTableName = 'OrderNumberDVGTGT';
//             $serviceTableName = 'ServiceNumbersDVGTGT';
//         } elseif (substr($orderNumber, 0, 1) == '2' && (strpos($next_three_digits_2, '888') !== false || strpos($next_three_digits_3, '888') !== false)) {
//             $orderTableName = 'OrderNumber';
//             $serviceTableName = 'ServiceNumbers';
//         }

//         // Start a transaction
//         $conn->begin_transaction();

//         // Update status_email and add log entry
//         if ($orderTableName !== '' && $serviceTableName !== '') {
//             $updateQueryOrderNumber = "UPDATE $orderTableName
//                 SET UpdationTime = NOW(), StatusNumber = 2,
//                     HistoryLog = CONCAT(HistoryLog, NOW(), '__', 'Diginext-update-StatusNumber-1-2 | ')
//                 WHERE OrderNumber IN ('$orderNumber') 
//                     AND DATEDIFF(NOW(), OrderTime) >= $threshold 
//                     AND StatusNumber = 1 
//                     AND StatusEmail = 1 ";
//             $conn->query($updateQueryOrderNumber);

//             // Check if any rows were affected for orderTableName
//             if ($conn->affected_rows > 0) {
//                 echo "Digitel - Update successfully StatusNumber for $orderTableName with number $orderNumber at $threshold days. \n";
//             } else {
//                 echo "Digitel - No rows were updated for $orderTableName with number $orderNumber. \n";
//             }

//             $updateQueryServiceNumber = "UPDATE $serviceTableName
//                 SET UpdationTime = NOW(), StatusNumber = 0,
//                     HistoryLog = CONCAT(HistoryLog, NOW(), '__', 'Diginext-update-StatusNumber-1-0 | ')
//                 WHERE Number IN ('$orderNumber') 
//                     AND StatusNumber = 1
//                     AND APIKey = ''";
//             $conn->query($updateQueryServiceNumber);

//             // Check if any rows were affected for serviceTableName
//             if ($conn->affected_rows > 0) {
//                 echo "Digitel - Update successfully StatusNumber for $serviceTableName with number $orderNumber at $threshold days. \n";
//             } else {
//                 echo "Digitel - No rows were updated for $serviceTableName with number $orderNumber. \n";
//             }
//         } else {
//             echo "Digitel - OrderNumber prefix not recognized.";
//         }

//         // Commit the transaction
//         $conn->commit();

//         // Close the database connection
//         $conn->close();
//     } catch (Exception $e) {
//         // Rollback the transaction if an error occurs
//         $conn->rollback();
//         echo "Digitel - Error updating StatusNumber with number $orderNumber at $threshold days: " . $e->getMessage() . "\n";
//     }
// }
