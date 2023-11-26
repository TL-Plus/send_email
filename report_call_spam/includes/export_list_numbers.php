<?php
require_once 'send_email/includes/config.php';
require_once 'send_email/includes/database_connection.php';
require 'query_report_call_spam_by_number.php';


// Connect to the database
$conn = connectDatabase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$result = $conn->query($query_report_call_spam_by_number);

// Close the database connection
$conn->close();

// Check if the query was successful
if ($result) {
    // Fetch the distinct callers and store them in an array
    $callers = [];
    while ($row = $result->fetch_assoc()) {
        $callers[] = $row['Caller'];
    }

    if (!empty($callers)) {
        // Remove extra spaces and extract numbers
        $numbers = array_map(function ($caller) {
            // Check if "84" is at the beginning of the number
            if (substr($caller, 0, 2) === "84") {
                // Remove "84" from the beginning
                $caller = substr($caller, 2);
            }
            return $caller;
        }, $callers);

        // Put the numbers in double quotes
        $quoted_numbers = array_map(function ($number) {
            return '"' . $number . '"';
        }, $numbers);

        // Display the result list in a textarea for easy copying
        $result_list_numbers = "(" . implode(", ", $quoted_numbers) . ')';
    } else {
        echo "Error: No callers found.";
    }
} else {
    // Handle query error
    echo "Error executing query: " . $conn->error;
}
