<?php
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/includes/email_notifications.php';


function sendEmailForDay($sql, $dbName, $header, $attachment, $subject, $bodyContent, $recipients, $cc_recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification($attachment, $subject, $bodyContent, $recipients, $cc_recipients);
        }
    } catch (Exception $e) {
        echo 'Error send mail: ' . $e->getMessage();
    }
}

// Function to send email notification
function sendEmailForDays($sql, $dbName, $header, $attachment, $subject, $bodyContent, $recipients, $cc_recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification($attachment, $subject, $bodyContent, $recipients, $cc_recipients);
        }
    } catch (Exception $e) {
        echo 'Error send mail: ' . $e->getMessage();
    }
}

function sendEmailForDaysMain($sql, $dbName, $header, $attachment, $subject, $bodyContent, $recipients, $cc_recipients)
{
    try {
        $exportSuccessful = exportToExcelMain($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotificationMain($attachment, $subject, $bodyContent, $recipients, $cc_recipients);
        }
    } catch (Exception $e) {
        echo 'Error send mail: ' . $e->getMessage();
    }
}

// Function to send email notification
function sendEmailForDaysTest($sql, $dbName, $header, $attachment, $subject, $bodyContent, $recipients, $cc_recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotificationTest($attachment, $subject, $bodyContent, $recipients, $cc_recipients);
        }
    } catch (Exception $e) {
        echo 'Error send mail: ' . $e->getMessage();
    }
}

// warning liabilities
function bodyWarningLiabilities($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:17px;font-family:Times New Roman,arial,helvetica;color:#222;">';

    $body .= '<table style="border-collapse:collapse;width:100%;color:#222;" cellpadding="10" >';
    $body .= '<tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    DIGINEXT BÁO CÁO CẢNH BÁO CÔNG NỢ THÁNG  ' . $FormValues['twoMonthsAgoMonth'] . '/' . $FormValues['twoMonthsAgoYear'] . '.
                    <br>
                </td>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    <i>Chi tiết xem ở file báo cáo.</i>
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

// order number
function bodyEmailOrderNumber($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:17px;font-family:Times New Roman,arial,helvetica;color:#222;">';

    // Table 1
    $body .= '<table style="border-collapse:collapse;width:100%;color:#222;" cellpadding="10" >';
    $body .= '<tr>
                <th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="3">
                    Kính gửi ' . $FormValues['userName'] . '
                </th>
            </tr>
            <tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="3">
                    SYSTEM DIGI thông báo về việc đặt số <font color="red"> ' . $FormValues['note'] . ' hết hạn</font>.
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

    return $body;
}

// fee payment cycle
function bodyEmailFeePaymentCycle($FormValues)
{
    $body = "";
    $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
    $body .= '<div style="margin-top: 15px; line-height: 1.6; font-size: 18px; font-family: Times New Roman, Arial, Helvetica; color: #222;">';

    // Header
    $body .= '<div style="text-align:LEFT;font-weight:bold;font-size:20px;color:#1c9ad6"><center>DANH SÁCH ĐẦU SỐ  ' . $FormValues['categoriesCode'] . '  ĐÃ ĐẾN CHU KỲ CƯỚC THÁNG ' . $FormValues['currentMonthYear'] . '</center> </div>';
    $body .= '<div style="margin-top:20px;line-height:1.8;font-size:17px;font-family:Times New Roman,arial,helvetica">';
    $body .= '<table>';
    $body .= '<tbody>';
    $body .= '<tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    <i>Kính gửi quý khách hàng <font style="font-weight:650">' . $FormValues['customerName'] . '</font> </i>
                    <br>
                </td>
            </tr>';
    $body .= '<tr>
                <td style="text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #f2f2f2">
                    <i><font style="border-bottom:1px solid #f2f2f2"><font style="color:#199cd9;font-weight:650">DIGINEXT</font></font></i> xin gửi danh sách những đầu số dịch vụ ' . $FormValues['categoriesCode'] . ' mà quý khách sử dụng đã đến chu kỳ thanh toán cước tháng <font style="font-weight:bold">' . $FormValues['currentMonthYear'] . '.</font>
                    <br> 
                    <font style="font-weight:500;border-bottom:1px solid #f2f2f2">Quý khách vui lòng thanh toán trước ngày <font style="font-weight:bold">' . $FormValues['currentDayMonth'] . '</font> để tránh bị gián đoạn dịch vụ.</font>
                </td>				
                <td><br></td>
            </tr>';

    // Table
    $body .= '<tr><td><br><table width="100%" style="border-collapse:collapse;padding:8px;border:2px solid black">';
    $body .= '<tbody>';
    $body .= '<tr>
                <th colspan="6" bgcolor="#00BFFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center;border-bottom:1.5px solid black"> DỊCH VỤ ' . $FormValues['categoriesCode'] . ' </th>
            </tr>';
    $body .= '<tr>
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">ĐẦU SỐ </th>
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">THỜI ĐIỂM TRIỂN KHAI ĐẦU SỐ</th>																
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">CHU KỲ HIỆN TẠI (LẦN THỨ) </th>
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">CHU KỲ THANH TOÁN ĐẦU SỐ (THÁNG) </th>
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">PHÍ/THÁNG</th>
                <th bgcolor="#2CCBFF" style="font-weight:bold;padding:8px;border:0.5px solid black;text-align:center">TỔNG CƯỚC</th>
            </tr>';

    foreach ($FormValues['customerDetails'] as $index => $value) {
        $body .= '<tr style="background-color:#ffffff">
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['extNumber'] . '</td>
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['activatedAt'] . '</td>
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['currentCycle'] . '</td>
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['paymentCycle'] . '</td>
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['costExpand'] . '</td>
                    <td style="padding:8px;border:0.5px solid black;text-align:center">' . $value['totalCost'] . '</td>
                </tr>';
    }
    $body .= '</tbody></table></td></tr>';


    // Footer
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

// holiday schedule
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
