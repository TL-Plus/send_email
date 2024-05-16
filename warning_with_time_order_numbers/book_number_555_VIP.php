<?php

// Thông tin kết nối
define('DB_HOST', '103.112.209.132');
define('DB_USER', 'Billing');
define('DB_PWD', 'Digitel@123');
define('DB_NAME_Bill', 'Billing_Diginext');

// Mảng chứa danh sách các số
$numbers = array("2455550222");

// Tạo kết nối tới cơ sở dữ liệu
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME_Bill);

// Kiểm tra kết nối
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Bắt đầu transaction
$mysqli->begin_transaction();

try {
    // Update status của các số trong service_numbers
    $query_service_numbers = "UPDATE service_numbers 
    SET status = 'holding', 
        comment = CONCAT('Book_For_admin_', NOW(), '_for_Diginext'),
        log = CONCAT(log, NOW(), '__', 'BOOK SỐ DỰ PHÒNG DIGITEL |'),
        apikey = 'DigiNext5FU9EDMkxud6DiginextLock270298',
        updated_at = NOW()
    WHERE status = 'inStock' AND number IN ('" . implode("', '", $numbers) . "')";

    $mysqli->query($query_service_numbers);

    // Insert các số vào order_numbers
    $query_order_numbers = "INSERT INTO `order_numbers` (`customer_name`, `customer_code`, `customer_address`, `customer_email`, `customer_phone`, `user_name`, `user_code`, `user_group`, `order_number`, `service_type`, `order_time`, `note`, `status`, `extend`, `time_confirm`, `status_email`, `provider`, `last_updated_at`, `user_created`, `user_updated`, `log`, `IsShow`, `created_at`, `updated_at`) VALUES ";

    foreach ($numbers as $number) {
        $query_order_numbers .= "('DigiNext', 'admin', '', 'admin@diginext.com,lan.lt@diginext.com.vn', '0123456789', 'DigiNext', 'admin', NULL, '$number', '0', NOW(), '', 'holding', 'no', NOW(), '0', 'DIGINEXT', NOW(), 'DigiNext', 'DigiNext', '', '1', NOW(), NOW()),";
    }

    // Loại bỏ dấu phẩy cuối cùng
    $query_order_numbers = rtrim($query_order_numbers, ",");

    // Thực thi câu lệnh INSERT
    if ($mysqli->query($query_order_numbers)) {
        $total_inserted = $mysqli->affected_rows;
        echo "Total numbers inserted: $total_inserted";
    } else {
        echo "Error inserting numbers: " . $mysqli->error;
    }

    // Commit transaction
    $mysqli->commit();
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    $mysqli->rollback();
    echo "Error: " . $e->getMessage();
}

// Đóng kết nối
$mysqli->close();
