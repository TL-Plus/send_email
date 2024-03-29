<?php

require '/var/www/html/send_email/config.php';

// convert chuỗi số bỏ đầu 84
function convertNumberSequence($number_sequence)
{
    preg_match_all('/\b\d+\b/', $number_sequence, $matches);
    $numbers = $matches[0];

    $list_numbers = array();

    foreach ($numbers as $number) {
        if (substr($number, 0, 2) === "84") {
            $number = substr($number, 2);
        }

        // if (substr($number, 0, 1) === "0") {
        //     $number = substr($number, 1);
        // }

        $quoted_number = '"' . $number . '"';
        $list_numbers[] = $quoted_number;
    }

    if (empty($list_numbers)) {
        return "";
    }

    $result_list_numbers = "(" . implode(", ", $list_numbers) . ")";
    return $result_list_numbers;
}


// convert chuỗi số thêm đầu 84
function convertNumberSequence84($number_sequence)
{
    // Kiểm tra nếu số bắt đầu bằng "0" thì thêm "84" vào đầu số
    if (substr($number_sequence, 0, 1) === "0") {
        $number_sequence = "84" . substr($number_sequence, 1);
    }

    // Kiểm tra nếu số bắt đầu bằng "84" thì giữ nguyên, ngược lại thêm "84" vào đầu số
    if (substr($number_sequence, 0, 2) !== "84") {
        $number_sequence = "84" . $number_sequence;
    }

    preg_match_all('/\b\d+\b/', $number_sequence, $matches);
    $numbers = $matches[0];

    $list_numbers = array();

    foreach ($numbers as $number) {
        $quoted_number = '"' . $number . '"';
        $list_numbers[] = $quoted_number;
    }

    if (empty($list_numbers)) {
        return "";
    }

    $result_list_numbers = "(" . implode(", ", $list_numbers) . ")";
    return $result_list_numbers;
}