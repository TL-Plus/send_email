<?php

function getRowColor($rowCounter)
{
    if ((int) (($rowCounter) / 5) % 2 == 0) {
        return array(230, 240, 255); // Lighter Blue
    } else {
        return array();
    }
}

// Function to modify values based on column names
function modifyValue($column, $value)
{
    // Ensure $value is a string
    if (!is_string($value)) {
        return $value;
    }

    // Remove unwanted words from the column values
    $unwantedWords = [
        'DỊCH VỤ VÀ GIẢI PHÁP XỬ LÝ DỮ LIỆU ',
        'THƯƠNG MẠI VÀ DỊCH VỤ VIỄN THÔNG ',
        'SẢN XUẤT - THƯƠNG MẠI - DỊCH VỤ ',
        'GIÁO DỤC TƯ DUY VÀ SÁNG TẠO ',
        'TƯ VẤN DỊCH VỤ VÀ GIẢI PHÁP ',
        'TẬP ĐOÀN Y KHOA QUỐC TẾ ',
        'DỊCH VỤ TƯ VẤN SỨC KHỎE ',
        'TRUYỀN HÌNH KỸ THUẬT SỐ ',
        'VẬN TẢI VÀ THIẾT BỊ MỎ ',
        'GIẢI PHÁP CÔNG NGHỆ SỐ ',
        'THƯƠNG MẠI VÀ GIÁO DỤC ',
        'THƯƠNG MẠI VÀ DỊCH VỤ ',
        'ĐẦU TƯ VÀ PHÁT TRIỂN ',
        'DỊCH VỤ VÀ GIẢI PHÁP ',
        'CÔNG NGHỆ VIỄN THÔNG ',
        'THƯƠNG MẠI - DỊCH VỤ ',
        'CÔNG NGHỆ TÀI CHÍNH ',
        'GIẢI PHÁP CÔNG NGHỆ ',
        'THƯƠNG MẠI DỊCH VỤ ',
        'DỊCH VỤ - TƯ VẤN ',
        'DT & PT THẨM MỸ ',
        'ĐẦU TƯ XÂY DỰNG ',
        'TRUYỀN HÌNH CÁP ',
        'MỘT THÀNH VIÊN ',
        'HỘ KINH DOANH ',
        'XỬ LÝ DỮ LIỆU ',
        'TƯ VẤN ĐẦU TƯ ',
        'CÔNG NGHỆ SỐ ',
        'DƯỢC MỸ PHẨM ',
        'TRUYỀN THÔNG ',
        'THƯƠNG MẠI ',
        'TRỰC TUYẾN ',
        'VIỄN THÔNG ',
        'CÔNG NGHỆ ',
        'GIẢI PHÁP ',
        'SẢN XUẤT ',
        'TẬP ĐOÀN ',
        'CỬA HÀNG ',
        'TỔNG HỢP ',
        'XÂY DỰNG ',
        'GIÁO DỤC ',
        'CÔNG TY ',
        'CỔ PHẦN ',
        'DỊCH VỤ ',
        'MỸ PHẨM ',
        'TẠP HÓA ',
        'TƯ VẤN ',
        'ĐẦU TƯ ',
        'TNHH ',
        'LUẬT ',
        'MTV ',
        'TM ',
        'TB ',
        'DT ',
        'CP ',
        ' - ',
        '- ',
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
