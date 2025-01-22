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
        'XUẤT NHẬP KHẨU MÁY MÓC THIẾT BỊ ',
        'SẢN XUẤT - THƯƠNG MẠI - DỊCH VỤ ',
        'GIÁO DỤC TƯ DUY VÀ SÁNG TẠO ',
        'TƯ VẤN DỊCH VỤ VÀ GIẢI PHÁP ',
        'TẬP ĐOÀN Y KHOA QUỐC TẾ ',
        'DỊCH VỤ TƯ VẤN SỨC KHỎE ',
        'TRUYỀN HÌNH KỸ THUẬT SỐ ',
        'VẬN TẢI VÀ THIẾT BỊ MỎ ',
        'GIẢI PHÁP CÔNG NGHỆ SỐ ',
        'THƯƠNG MẠI VÀ GIÁO DỤC ',
        'BÁN BUÔN, BÁN LẺ TRÊN ',
        'THƯƠNG MẠI VÀ DỊCH VỤ ',
        'ĐẦU TƯ VÀ PHÁT TRIỂN ',
        'DỊCH VỤ VÀ GIẢI PHÁP ',
        'CÔNG NGHỆ VIỄN THÔNG ',
        'THƯƠNG MẠI - DỊCH VỤ ',
        'CÔNG NGHỆ TÀI CHÍNH ',
        'TRÁCH NHIỆM HỮU HẠN ',
        'GIẢI PHÁP CÔNG NGHỆ ',
        'THƯƠNG MẠI DỊCH VỤ ',
        'DỊCH VỤ - TƯ VẤN ',
        'DT & PT THẨM MỸ ',
        'ĐẦU TƯ XÂY DỰNG ',
        'TRUYỀN HÌNH CÁP ',
        'MỘT THÀNH VIÊN ',
        'XUẤT NHẬP KHẨU ',
        'HỘ KINH DOANH ',
        'XỬ LÝ DỮ LIỆU ',
        'TƯ VẤN ĐẦU TƯ ',
        'CÔNG NGHỆ SỐ ',
        'DƯỢC MỸ PHẨM ',
        'TRUYỀN THÔNG ',
        'DOANH NGHIỆP ',
        'PHÁT TRIỂN ',
        'THƯƠNG MẠI ',
        'TRỰC TUYẾN ',
        'VIỄN THÔNG ',
        'CÔNG NGHỆ ',
        'GIẢI PHÁP ',
        'GIAO NHẬN ',
        'SẢN XUẤT ',
        'TẬP ĐOÀN ',
        'CỬA HÀNG ',
        'QUẢN TRỊ ',
        'TỔNG HỢP ',
        'XÂY DỰNG ',
        'GIÁO DỤC ',
        'DU LỊCH ',
        'VĂN HÓA ',
        'CÔNG TY ',
        'CỔ PHẦN ',
        'DỊCH VỤ ',
        'MỸ PHẨM ',
        'TẠP HÓA ',
        'ĐIỆN TỬ ',
        'VẬN TẢI ',
        'VẬN TẢI',
        'TƯ VẤN ',
        'ĐẦU TƯ ',
        'TM&DV ',
        'TNHH ',
        'LUẬT ',
        'TMDV ',
        'MTV ',
        'XNK ',
        'TM ',
        'DV ',
        'TB ',
        'DT ',
        'CP ',
        'VÀ ',
        ' - ',
        '- ',
    ];

    // Define wanted words/phrases
    $wantedWords = [
        'TRONG VÀ NGOÀI NƯỚC ',
        'VIỆT- PHÁP ',
        'TDT ',
    ];

    // Temporarily replace wanted phrases with placeholders to protect them
    $placeholders = [];
    foreach ($wantedWords as $index => $word) {
        $placeholder = "{{WANTED_$index}}";
        $placeholders[$placeholder] = $word;
        $value = str_replace($word, $placeholder, $value);
    }

    // Remove unwanted words
    $value = str_replace($unwantedWords, '', $value);

    // Restore wanted phrases from placeholders
    $value = str_replace(array_keys($placeholders), array_values($placeholders), $value);

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

    if ($column == 'TotalCost' || $column == 'TotalCurrentCall' || $column == 'BlockViettel' || $column == 'ActiveViettel') {
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
        'ActiveViettel' => 'Số Mở VTL',
    ];

    return $vietnameseMapping[$text] ?? $text;
}
