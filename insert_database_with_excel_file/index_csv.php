<?php

require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'csv_insert_database_blacklist.php';

// Insert data from CSV
insertDataFromCsv('List_DNC_1.csv');
insertDataFromCsv('List_DNC_2.csv');
insertDataFromCsv('List_DNC_3.csv');
insertDataFromCsv('List_DNC_4.csv');
insertDataFromCsv('List_DNC_5.csv');
insertDataFromCsv('List_DNC_6.csv');
insertDataFromCsv('List_DNC_7.csv');
insertDataFromCsv('List_DNC_8.csv');
insertDataFromCsv('List_DNC_9.csv');
insertDataFromCsv('List_DNC_10.csv');
insertDataFromCsv('List_DNC_11.csv');