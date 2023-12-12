<?php

function createTable($conn)
{
    // Prepare the SQL statement for table creation
    $sql = "CREATE TABLE IF NOT EXISTS `BlackListBK` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `msisdn` varchar(50) NOT NULL,
        `telco` varchar(50) DEFAULT NULL,
        `shortcode` varchar(50) NOT NULL,
        `info` text DEFAULT NULL,
        `mo_time` varchar(255) DEFAULT NULL,
        `cmd_code` varchar(255) DEFAULT NULL,
        `error_code` varchar(255) DEFAULT NULL,
        `error_desc` varchar(255) DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    // Execute the table creation SQL statement
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }

    echo "Table created successfully!\n";
}