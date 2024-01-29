<?php
require_once 'send_email/config.php';
require_once 'export_excel.php';
require_once 'email_notifications.php';


function sendEmailForDay($sql, $dbName, $header, $attachment, $subject, $recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// Function to send email notification
function sendEmailForDays($sql, $dbName, $header, $attachment, $subject, $bodyContent, $recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification("/root/{$attachment}", $subject, $bodyContent, $recipients);
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function bodyEmailOrderNumber($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:17px;font-family:Times New Roman,arial,helvetica;color:#222;">';

    // Table 1
    $body .= '<table style="border-collapse:collapse;width:100%;color:#222;" cellpadding="10" >';
    $body .= '<tr>
                <th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="3">
                    Kính gửi Sale ' . $FormValues['userName'] . '
                </th>
            </tr>
            <tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="3">
                    DIGINEXT thông báo về việc đặt số <font color="red"> sắp hết hạn</font>.
                </td>
            </tr>
        </table>
    </div>';

    // Table 2
    $body .= '<table style="border-collapse: collapse;background-color:#e6ffff;  width:40%; color:#222;" cellpadding="10" border-spacing: 35px; >';
    $body .= '<tr>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:5%;vertical-align:middle" bgcolor="#00BFFF" colspan="1">STT</td>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:20%;vertical-align:middle" bgcolor="#00BFFF" colspan="1">OrderNumber</td>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:20%;vertical-align:middle" bgcolor="#00BFFF" colspan="1">OrderTime</td>
            </tr>';

    // Order Data Rows
    foreach ($FormValues['orderNumberDatas'] as $index => $order) {
        $body .= '<tr>
                    <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf" colspan="1">' . ($index + 1) . '</td>
                    <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf" colspan="1">' . $order['orderNumber'] . '</td>
                    <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf" colspan="1">' . $order['orderTime'] . '</td>
                </tr>';
    }

    $body .= '</table>';

    // Additional Information and Footer
    $body .= "<div style='font-weight:bold; margin-bottom:10px; margin-top:20px;color:#222;'>Thông tin tra cứu chi tiết quý khách vui lòng truy cập :</div>
        <div style='margin-left:10px;color:#222;'><span>- Địa chỉ : https://billing.diginext.com.vn/</span></div>
        <div style='margin-bottom:10px; margin-top:20px;color:#222;'><span><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của Diginext</i></span></div>
        <table style='color:#222;'>
            <tr>
                <td style='width:160px; border-right:2px solid #cfcfcf;'>
                    <img width='160' src='https://billing.diginext.com.vn/backend/images/logo-small.png' alt='Diginext'/>
                </td>
                
                <td style='text-align:top;padding-left:10px'>
                    <div style='color:#ffffff;font-size:1px'>CÔNG TY CỔ PHẦN CÔNG NGHỆ SỐ DIGINEXT</div></br>
                    <p style='font-weight:bold;'><a href='https://billing.diginext.com.vn/'>CÔNG TY CỔ PHẦN CÔNG NGHỆ SỐ DIGINEXT</a><br /><br /></p>
                    Địa chỉ giao dịch: Tầng 3, Tòa W1-W2 Vinhomes West Point, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội.<br />
                    Tel: (024-028) 5555 1111 | 19005055 | <a href='http://diginext.com.vn'>http://diginext.com.vn</a><br>
                    Email: cskh@diginext.com.vn
                </td>
            </tr>
        </table>";

    return $body;
}