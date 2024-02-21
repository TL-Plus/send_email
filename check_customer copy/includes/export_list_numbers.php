<?php

require '/var/www/html/send_email/config.php';

function convertNumberSequence($number_sequence)
{
    preg_match_all('/\b\d+\b/', $number_sequence, $matches);
    $numbers = $matches[0];

    $list_numbers = array();

    foreach ($numbers as $number) {
        if (substr($number, 0, 2) === "84") {
            $number = substr($number, 2);
        }

        $quoted_number = '"' . $number . '"';
        $list_numbers[] = $quoted_number;
    }

    if (empty($list_numbers)) {
        return "";
    }

    $result_list_numbers = "(" . implode(", ", $list_numbers) . ")";
    return $result_list_numbers;
}