<?php
require_once 'config.php';

// Function to execute a SQL query
function executeQuery($sql)
{
    $conn = connectDatabase();

    $result = $conn->query($sql);

    // Close the database connection
    $conn->close();

    return $result;
}
