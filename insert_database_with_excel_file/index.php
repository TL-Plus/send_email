<?php
require 'send_email/includes/config.php';
require 'send_email/includes/database_connection.php';
require 'insert_database_blacklist.php';

// change path input excel file
$inputFileName = 'test_import_excel.xlsx';
insertDataFromExcel($inputFileName);