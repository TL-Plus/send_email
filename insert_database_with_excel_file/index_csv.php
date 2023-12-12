<?php

require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'csv_insert_database_blacklist.php';

// List of CSV files to process
$csvFiles = [
    'List_DNC_1.csv',
    'List_DNC_2.csv',
    'List_DNC_3.csv',
    'List_DNC_4.csv',
    'List_DNC_5.csv',
    'List_DNC_6.csv',
    'List_DNC_7.csv',
    'List_DNC_8.csv',
    'List_DNC_9.csv',
    'List_DNC_10.csv',
];

// Insert data from each CSV file
foreach ($csvFiles as $csvFile) {
    // Insert data from CSV
    insertDataFromCsv($csvFile);

    // Display a message after processing each file
    echo "Data from '$csvFile' inserted successfully!\n";
}