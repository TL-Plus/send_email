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
            sendEmailNotification($attachment, $subject, "Excel Files for {$subject}", $recipients);
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
            sendEmailNotification($attachment, $subject, $bodyContent, $recipients);
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
    $body .= '<table style="border-collapse: collapse;background-color:#e6ffff;  width:60%; color:#222;" cellpadding="10" border-spacing: 35px; >';
    $body .= '<tr>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:5%;vertical-align:middle" bgcolor="#00BFFF" colspan="1">STT</td>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:30%;vertical-align:middle" bgcolor="#00BFFF" colspan="1">OrderNumber</td>
                <td style="text-align:center;border-bottom:1px solid #cfcfcf;border-right:1px solid #cfcfcf;font-weight:bold;width:30%;min-width:200px;vertical-align:middle" bgcolor="#00BFFF" colspan="1">OrderTime</td>
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
        <div style='margin-bottom:10px; margin-top:20px; color:#222;'><span><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của DigiNext</i></span></div>
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

function sendEmailHolidaySchedule($subject, $bodyContent, $recipients, $imagePath)
{
    try {
        sendEmailNotificationHolidaySchedule($subject, $bodyContent, $recipients, $imagePath);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function bodyEmailHolidaySchedule($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 15px; line-height: 1.6; font-size: 18px; font-family: Times New Roman, Arial, Helvetica; color: #222;">';

    // Table 1
    $body .= '<table style="border-collapse: collapse; width: 100%; color: #222; margin-bottom: 15px;" cellpadding="10" >';
    $body .= '<tr>
                <th style="text-align: left; font-family: Times New Roman, Arial, Helvetica;" colspan="3">
                    Kính gửi Quý khách hàng ' . $FormValues['customerName'] . '
                </th>
            </tr>';

    // Image
    $body .= '<tr>
                <td style="text-align: left;" colspan="3">
                    <img src="cid:holiday-schedule" alt="Holiday Schedule" style="width: 100%; max-width: 800px; height: auto; border-radius: 10px;">
                </td>
            </tr>';

    // Additional Information
    $body .= '<tr>
                <td style="text-align: left; margin-top: 10px; font-style: italic; padding: 5px;" colspan="3">
                    <p style="width: 100%; max-width: 800px;">Trong thời gian nghỉ lễ, Công ty vẫn bố trí các chuyên viên, nhân viên trực và làm việc. Nếu quý khách hàng, Quý đối tác cần hỗ trợ bất kỳ dịch vụ nào xin vui lòng liên hệ hotline 19005055.</p>
                    <p style="width: 100%; max-width: 800px;">DIGINEXT xin trân trọng cảm ơn Quý khách hàng, đối tác đã tin tưởng chúng tôi trong suốt thời gian qua. Một lần nữa kính chúc Quý khách hàng, Quý đối tác có một kỳ nghỉ lễ nhiều sức khỏe, niềm vui bên gia đình, người thân và bạn bè.</p>
                </td>
            </tr>';
    $body .= '</table>';
    $body .= '</div>';

    // Footer
    $body .= "<div style='margin-top: 10px; color: #222;'><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của DigiNext</i></div>
            <table style='color: #222; width: 100%; margin-top: 10px;'>
                <tr>
                    <td style='width:110px; border-right: 2px solid #cfcfcf; padding: 5px;'>
                        <img width='100' src='https://billing.diginext.com.vn/backend/images/logo-small.png' alt='Diginext' style='border-radius: 10px;'>
                    </td>
                    
                    <td style='text-align: top; padding-left: 10px;'>
                        <div style='color: #ffffff; font-size: 1px;'>CÔNG TY CỔ PHẦN CÔNG NGHỆ SỐ DIGINEXT</div></br>
                        <p style='font-weight: bold;'><a href='https://billing.diginext.com.vn/'>CÔNG TY CỔ PHẦN CÔNG NGHỆ SỐ DIGINEXT</a><br /><br /></p>
                        Địa chỉ giao dịch: Tầng 3, Tòa W1-W2 Vinhomes West Point, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội.<br />
                        Tel: (024-028) 5555 1111 | 19005055 | <a href='http://diginext.com.vn'>http://diginext.com.vn</a><br>
                        Email: cskh@diginext.com.vn
                    </td>
                </tr>
            </table>";

    return $body;
}