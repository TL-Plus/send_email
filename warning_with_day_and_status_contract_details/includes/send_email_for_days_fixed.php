<?php
require_once 'send_email/config.php';
require_once 'send_email/includes/export_excel.php';
require_once 'send_email/includes/email_notifications.php';


// Function to send email notification
function sendEmailForDayFixed($sql1, $sql2, $dbName, $header, $attachment, $subject, $bodyContent, $recipients, $cc_recipients)
{
    try {
        $numbers = array();
        $result = connectAndQueryDatabase(
            $sql1,
            $_ENV['DB_HOSTNAME_DIGITEL'],
            $_ENV['DB_USERNAME_DIGITEL'],
            $_ENV['DB_PASSWORD_DIGITEL'],
            $dbName
        );

        // Check if $result is an object before proceeding
        if (is_object($result)) {
            // Fetch each row and store the 'Number' in the array
            while ($row = $result->fetch_assoc()) {
                $numbers[] = $row['Number'];
            }
            // Free the result set
            $result->free();
        } else {
            // Handle the case where $result is not an object (e.g., it's an array)
            echo 'Error: Invalid result type.';
            return;
        }

        // Check if $numbers array is not empty before proceeding
        if (!empty($numbers)) {
            $detailsQuery = str_replace('{numbers}', implode(',', $numbers), $sql2);

            exportToExcel($detailsQuery, $dbName, $header, $attachment);
            sendEmailNotification("/root/{$attachment}", $subject, $bodyContent, $recipients, $cc_recipients);
        } else {
            echo 'No valid numbers found.';
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function bodyWarning($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:17px;font-family:Times New Roman,arial,helvetica;color:#222;">';

    $body .= '<table style="border-collapse:collapse;width:100%;color:#222;" cellpadding="10" >';
    $body .= '<tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    DIGINEXT GỬI BÁO CÁO CẢNH BÁO THÔNG TIN TRẠNG THÁI HỢP ĐỒNG.
                    <br>
                </td>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    <i>Chi tiết xem ở file báo cáo. </i>
                    <br>
                </td>
            </tr>
        </table>
    </div>';

    // Additional Information and Footer
    $body .= "<div style='font-weight:bold; margin-bottom:10px; margin-top:20px;color:#222;'>Thông tin tra cứu chi tiết quý khách vui lòng truy cập :</div>
        <div style='margin-left:10px;color:#222;'><span>- Địa chỉ : https://billing.diginext.com.vn</span></div>
        <div style='margin-bottom:10px; margin-top:20px; color:#222;'><span><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của DigiNext</i></span></div>
        <table style='color:#222;'>
            <tr>
                <td style='width:160px; border-right:2px solid #cfcfcf;'>
                    <img width='160' src='http://103.112.209.152//storage/media/small-logo-dark.png' alt='Diginext'/>
                </td>
                
                <td style='text-align:top;padding-left:10px'>
                    <div style='color:#ffffff;font-size:1px'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</div></br>
                    <p style='font-weight:bold;'><a href='https://billing.diginext.com.vn'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</a><br /><br /></p>
                    Địa chỉ giao dịch: Lô OF03-19, Tầng 3 - Office, Vinhomes West Point, Đường Phạm Hùng, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội.<br />
                    Tel: (024-028) 5555 1111 | 19005055 | <a href='https://diginext.com.vn'>https://diginext.com.vn</a><br>
                    Email: cskh@diginext.com.vn
                </td>
            </tr>
        </table>";

    return $body;
}
