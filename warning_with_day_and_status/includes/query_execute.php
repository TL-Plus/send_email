<?php
require_once 'send_email/includes/config.php';

// Function to execute a SQL query
function executeQuery($sql)
{
    $conn = connectDatabase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    $result = $conn->query($sql);

    // Close the database connection
    $conn->close();

    return $result;
}
