<?php

function getRowColor($rowCounter)
{
    if ((int)(($rowCounter) / 5) % 2 == 0) {
        return array(230, 240, 255); // Lighter Blue
    } else {
        return array();
    }
}

// Function to modify values based on column names
function modifyValue($column, $value)
{
    // Remove unwanted words from the column values
    $unwantedWords = [
        'SẢN XUẤT - THƯƠNG MẠI - DỊCH VỤ ',
        'GIÁO DỤC TƯ DUY VÀ SÁNG TẠO ',
        'TƯ VẤN DỊCH VỤ VÀ GIẢI PHÁP ',
        'DỊCH VỤ TƯ VẤN SỨC KHỎE ',
        'VẬN TẢI VÀ THIẾT BỊ MỎ ',
        'GIẢI PHÁP CÔNG NGHỆ SỐ ',
        'THƯƠNG MẠI VÀ GIÁO DỤC ',
        'THƯƠNG MẠI VÀ DỊCH VỤ ',
        'ĐẦU TƯ VÀ PHÁT TRIỂN ',
        'CÔNG NGHỆ VIỄN THÔNG ',
        'THƯƠNG MẠI - DỊCH VỤ ',
        'THƯƠNG MẠI DỊCH VỤ ',
        'TRUYỀN HÌNH CÁP ',
        'MỘT THÀNH VIÊN ',
        'HỘ KINH DOANH ',
        'TƯ VẤN ĐẦU TƯ ',
        'CÔNG NGHỆ SỐ ',
        'DƯỢC MỸ PHẨM ',
        'THƯƠNG MẠI ',
        'TRỰC TUYẾN ',
        'CÔNG NGHỆ ',
        'GIẢI PHÁP ',
        'SẢN XUẤT ',
        'GIÁO DỤC ',
        'CÔNG TY ',
        'CỔ PHẦN ',
        'DỊCH VỤ ',
        'MỸ PHẨM ',
        'ĐẦU TƯ ',
        'TNHH ',
    ];
    $value = str_replace($unwantedWords, '', $value);

    // Extract the last two words from the name
    if ($column == 'SalerName') {
        if (preg_match('/([^\s]+)\s+([^\s]+)$/', $value, $matches)) {
            $value = $matches[1] . ' ' . $matches[2];
        }
    }

    if (is_numeric($value)) {
        // Format the numeric value with commas
        $value = number_format($value, 0, '.', ',');
    }

    if ($column == 'TotalCost' || $column == 'TotalCurrentCall' || $column == 'BlockViettel' || $column == 'BlockMobifone') {
        return ['value' => $value, 'align' => 'C'];
    }

    return ['value' => $value, 'align' => 'L'];
}

function translateHeader($text)
{
    $vietnameseMapping = [
        'CustomerName' => 'Khách Hàng',
        'SalerName' => 'Sale',
        'TotalCost' => 'Số Tiền',
        'TotalCurrentCall' => 'CCU',
        'BlockViettel' => 'Số Khóa VTL',
        'BlockMobifone' => 'Số Khóa MBF',
    ];

    return $vietnameseMapping[$text] ?? $text;
}