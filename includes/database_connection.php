<?php
require_once 'config.php';

// Function to establish a database connection
function connectDatabase($hostname, $username, $password, $database)
{
    $conn = new mysqli($hostname, $username, $password, $database);
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to connect to the database, execute a query, and return the result
function connectAndQueryDatabase($sql, $hostname, $username, $password, $database)
{
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Connect to the database
        $conn = connectDatabase($hostname, $username, $password, $database);

        // Execute the SQL query
        $result = $conn->query($sql);

        // Close the database connection
        $conn->close();

        return $result;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
