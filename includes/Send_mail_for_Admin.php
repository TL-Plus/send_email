<?php

require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_smtp_mail($aFormValues = array())
{

	date_default_timezone_set("Asia/Ho_Chi_Minh");
	$from = EMAIL_NAME;
	$subject = 'CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT';
	$body = "";
	if ($aFormValues['SendMail'] == '1') {
		$body = prepare($aFormValues);
	} elseif ($aFormValues['SendMail'] == '2') {
		$body = prepareRunOut($aFormValues);
	}

	if (isset($aFormValues['C_Email1']) && isset($aFormValues['C_EmailCC'])) {
		$to = $aFormValues['C_Email1'];
		$cc = $aFormValues['C_EmailCC'];
	}
	$postArray = array(
		'to' => $to,
		'cc' => $cc,
		'from' => $from,
		'sub' => $subject,
		'message' => $body
	);
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "tls";
	$mail->Host = EMAIL_HOST;
	$mail->Port = EMAIL_PORT;

	$mail->SMTPDebug = 2;


	if (($aFormValues['SendMail'] == '10000000')) {
		$mail->Username = EMAIL_USER;
		$mail->Password = EMAIL_PWD;
		$webmaster_email = EMAIL_NAME;
	} else {
		$mail->Username = EMAIL_USER2;
		$mail->Password = EMAIL_PWD2;
		$webmaster_email = EMAIL_NAME2;
	}

	$mail->From = $webmaster_email;
	$mail->FromName = "CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT";
	$email_list_to = explode(',', $to);

	foreach ($email_list_to as $val) {
		$mail->AddAddress($val, $val);
	}


	$email_list_cc = explode(',', $cc);

	foreach ($email_list_cc as $val) {
		$mail->AddCC($val, $val);
	}

	$mail->AddReplyTo($webmaster_email, "BILLING DIGINEXT");
	$mail->WordWrap = 50;

	$mail->IsHTML(true);

	if ($aFormValues['SendMail'] == '1') {
		$mail->Subject = "[DIGINEXT] - THÔNG BÁO TÀI KHOẢN SẮP HẾT SỐ DƯ HỢP ĐỒNG " . $aFormValues['contract_code'] . " MÃ DỊCH VỤ " . $aFormValues['categories_code'] . " PHỤ LỤC " . $aFormValues['addendum'] . "";
	}
	if ($aFormValues['SendMail'] == '2') {
		$mail->Subject = "[DIGINEXT] - THÔNG BÁO TÀI KHOẢN ĐÃ HẾT SỐ DƯ SỐ HỢP ĐỒNG " . $aFormValues['contract_code'] . " MÃ DỊCH VỤ " . $aFormValues['categories_code'] . " PHỤ LỤC " . $aFormValues['addendum'] . "";
	}

	$mail->Body = $body;
	$mail->AltBody = $body;
	$result = $mail->Send();
	return $result;
}

function prepare($aFormValues = array())
{

	$body = "";
	$body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
	$body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:18px;font-family:Times New Roman,arial,helvetica;">';


	$body .= '	<table style = "border-collapse: collapse;background-color:#e6ffff;  width:100% "cellpadding="10" border-spacing: 35px; >';
	$body .= '	<tr>
						<th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Kính gửi quý khách hàng ' . $aFormValues['customer_name'] . '</th>
				</tr>
				<tr>
						<td style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5"><p>Diginext thông báo về việc quý khách hàng <font color="red"> sắp </font> hết số dư. (Nếu quý khách sử dụng dịch vụ có đầu số cam kết cước mà cước phát sinh chưa vượt mức cam kết cước, quý khách có thể bỏ qua email này hoặc đóng thêm phí để đảm bảo duy trì cuộc gọi một cách tốt nhất.)</i></p></td>
				</tr>				';


	$body .= ' 	<tr>
						<th style="background: #00BFFF;text-align:center;font-family:Times New Roman,arial,helvetica;" colspan="5"> GIỚI HẠN CƯỚC KHÁCH HÀNG</th>
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:center;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" > Thời gian kiểm tra</td>  						
						<td style="border-bottom:1px solid #CFCFCF;text-align:center;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="4" >: ' . $aFormValues['now_day_full_time'] . '</td>	
				</tr>';
	$body .= '<tr bgcolor="#CEF2F6">
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Mã hợp đồng </td>  						
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="2">: ' . $aFormValues['contract_code'] . '</td>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" >Mã dịch vụ</td>  					
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">: ' . $aFormValues['categories_code'] . '</td>												
				</tr>';
	$body .= '<tr>
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Mã dịch vụ</td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Tổng chi phí đã thanh toán (VNĐ) (1)</td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Công nợ chốt đến hết tháng <font color="red"> ' . $aFormValues['last_month'] . '/' . $aFormValues['last_year'] . ' </font>(VNĐ) (2)</td>
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" >Chi phí tháng <font color="red">' . $aFormValues['year'] . '/' . $aFormValues['month'] . ' </font> (VNĐ) (3) </td>  					
						<td style="border-bottom:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Số dư (VNĐ) =  (1)- (2) - (3) </td>												
				</tr>';
	$body .= '<tr bgcolor="#CEF2F6">
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['categories_code'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['sum_pa'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['sum_li'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['total_cost'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> <font color="red">' . $aFormValues['final_cost'] . ' </font> </td>												
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Quý khách vui lòng nạp thêm cước theo định mức để tiếp tục sử dụng dịch vụ</td>
				</tr>';
	$body .= ' 	<tr bgcolor="#CEF2F6">
						<th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">THÔNG TIN CHI PHÍ PHÁT SINH THÁNG HIỆN TẠI : </th>
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Cước sẽ được chốt vào cuối tháng, quý khách hàng sẽ nhận được thông báo cước qua email trước ngày 5/' . $aFormValues['next_month'] . '/' . $aFormValues['next_year'] . '</td>
				</tr>';
	$body .= $aFormValues['htmlNotice'];

	$body .= '</table>';
	$body .= '	<table style = "border-collapse: collapse;width:100% "cellpadding="10" border-spacing: 35px; >';
	$body .= '<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;"><font color="blue">Thông tin Chuyển Khoản</font> <br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Nội dung thanh toán : ' . $aFormValues['contract_code'] . ' THANH TOAN TRA TRUOC;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Đơn vị thụ hưởng: CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Tài khoản số: 83526868 ;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Tại Ngân hàng: NH TMCP Á Châu- Chi nhánh Kim Đồng - TP.Hà Nội (ACB).<br /></td></tr>	
		</table></div>';

	$body .= "</br><div style='font-weight:bold; margin-bottom:10px; margin-top:20px'>Thông tin tra cứu chi tiết cước quý khách vui lòng truy cập :</div>";

	$body .= '<div style="margin-bottom:10px; margin-top:20px;"><span><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của Diginext</i></span></div>';

	$body .= '<div style="margin-top: 20px;margin-right: 40px;margin-bottom: 10px;font-family:cursive;text-align: right;"><span><i><marquee>Trân trọng cảm ơn quý khách hàng đã sử dụng dịch vụ của Digiel</marquee></i></span></div>';


	$body .= "<div style='margin-bottom:10px; margin-top:20px;'><span>Địa chỉ tra cứu thông tin dịch vụ: <a href='https://billing.diginext.com.vn'>https://billing.diginext.com.vn</a></span></div>		
	<div style='margin-bottom:10px; margin-top:20px;''><span><i>Trân trọng cảm ơn quý khách hàng đã sử dụng dịch vụ của DigiNext</i></span></div>
    <table width='100%' cellpadding='0' cellspacing='0' >
        <tr>
            <td style='width:160px; border-right:2px solid #CFCFCF;'>
                <img width='150px' src='https://billing.diginext.com.vn//storage/media/small-logo-dark.png'>
            </td>
            <td style='text-align:top; padding-left:10px'>
                <div style='color:#ffffff;font-size:1px'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</div></br>
                <p style='font-weight:bold;'><a href = 'https://diginext.com.vn'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</a><br /><br /></p>
                    Địa chỉ giao dịch: Tầng 3, Tòa W1-W2 Vinhomes West Point, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội.<br />
                    Tel: (024-028) 5555 1111 | 19005055 | https://diginext.com.vn<br />
                    Email: cskh@diginext.com.vn
            </td>
        </tr>
    </table>";

	return $body;
}

function prepareRunOut($aFormValues = array())
{

	$body = "";
	$body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
	$body .= '<div style="margin-top: 20px;line-height: 1.8;font-size:18px;font-family:Times New Roman,arial,helvetica;">';


	$body .= '	<table style = "border-collapse: collapse;background-color:#e6ffff;  width:100% "cellpadding="10" border-spacing: 35px; >';
	$body .= '	<tr>
						<th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Kính gửi quý khách hàng ' . $aFormValues['customer_name'] . '</th>
				</tr>
				<tr>
						<td style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5"><p>Diginext thông báo về việc quý khách hàng <font color="red"> đã </font> hết số dư. (Nếu quý khách sử dụng dịch vụ có đầu số cam kết cước mà cước phát sinh chưa vượt mức cam kết cước, quý khách có thể bỏ qua email này hoặc đóng thêm phí để đảm bảo duy trì cuộc gọi một cách tốt nhất.)</i></p></td>
				</tr>				';


	$body .= ' 	<tr>
						<th style="background: #00BFFF;text-align:center;font-family:Times New Roman,arial,helvetica;" colspan="5"> GIỚI HẠN CƯỚC KHÁCH HÀNG</th>
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:center;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" > Thời gian kiểm tra</td>  						
						<td style="border-bottom:1px solid #CFCFCF;text-align:center;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="4" >: ' . $aFormValues['now_day_full_time'] . '</td>	
				</tr>';
	$body .= '<tr bgcolor="#CEF2F6">
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Mã hợp đồng </td>  						
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="2">: ' . $aFormValues['contract_code'] . '</td>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" >Mã dịch vụ</td>  					
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">: ' . $aFormValues['categories_code'] . '</td>												
				</tr>';
	$body .= '<tr>
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Mã dịch vụ</td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Tổng chi phí đã thanh toán (VNĐ) (1)</td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Công nợ chốt đến hết tháng <font color="red"> ' . $aFormValues['last_month'] . '/' . $aFormValues['last_year'] . ' </font>(VNĐ) (2)</td>
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1" >Chi phí tháng <font color="red">' . $aFormValues['year'] . '/' . $aFormValues['month'] . ' </font> (VNĐ) (3) </td>  					
						<td style="border-bottom:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1">Số dư (VNĐ) =  (1)- (2) - (3) </td>												
				</tr>';
	$body .= '<tr bgcolor="#CEF2F6">
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['categories_code'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['sum_pa'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['sum_li'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;border-right:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> ' . $aFormValues['total_cost'] . ' </td>  						
						<td style="border-bottom:2px solid #000000;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="1"> <font color="red">' . $aFormValues['final_cost'] . ' </font> </td>												
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Quý khách vui lòng nạp thêm cước theo định mức để tiếp tục sử dụng dịch vụ</td>
				</tr>';
	$body .= ' 	<tr bgcolor="#CEF2F6">
						<th style="text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">THÔNG TIN CHI PHÍ PHÁT SINH THÁNG HIỆN TẠI : </th>
				</tr>';
	$body .= ' 	<tr>
						<td style="border-bottom:1px solid #CFCFCF;text-align:left;font-family:Times New Roman,arial,helvetica;" colspan="5">Cước sẽ được chốt vào cuối tháng, quý khách hàng sẽ nhận được thông báo cước qua email trước ngày 5/' . $aFormValues['next_month'] . '/' . $aFormValues['next_year'] . '</td>
				</tr>';
	$body .= $aFormValues['htmlNotice'];

	$body .= '</table>';
	$body .= '	<table style = "border-collapse: collapse;width:100% "cellpadding="10" border-spacing: 35px; >';
	$body .= '<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;"><font color="blue">Thông tin Chuyển Khoản</font> <br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Nội dung thanh toán : ' . $aFormValues['contract_code'] . ' THANH TOAN TRA TRUOC;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Đơn vị thụ hưởng: CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Tài khoản số: 83526868 ;<br /></td></tr>
		<tr><td style=" text-align:left;font-family:Times New Roman,arial,helvetica;border-bottom:1px solid #CFCFCF;">- Tại Ngân hàng: NH TMCP Á Châu- Chi nhánh Kim Đồng - TP.Hà Nội (ACB).<br /></td></tr>	
		</table></div>';

	$body .= "</br><div style='font-weight:bold; margin-bottom:10px; margin-top:20px'>Thông tin tra cứu chi tiết cước quý khách vui lòng truy cập :</div>";

	$body .= '<div style="margin-bottom:10px; margin-top:20px;"><span><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch vụ của Diginext</i></span></div>';

	$body .= '<div style="margin-top: 20px;margin-right: 40px;margin-bottom: 10px;font-family:cursive;text-align: right;"><span><i><marquee>Trân trọng cảm ơn quý khách hàng đã sử dụng dịch vụ của Digiel</marquee></i></span></div>';


	$body .= "<div style='margin-bottom:10px; margin-top:20px;'><span>Địa chỉ tra cứu thông tin dịch vụ: <a href='https://billing.diginext.com.vn'>https://billing.diginext.com.vn</a></span></div>		
	<div style='margin-bottom:10px; margin-top:20px;''><span><i>Trân trọng cảm ơn quý khách hàng đã sử dụng dịch vụ của DigiNext</i></span></div>
    <table width='100%' cellpadding='0' cellspacing='0' >
        <tr>
            <td style='width:160px; border-right:2px solid #CFCFCF;'>
                <img width='150px' src='https://billing.diginext.com.vn//storage/media/small-logo-dark.png'>
            </td>
            <td style='text-align:top; padding-left:10px'>
                <div style='color:#ffffff;font-size:1px'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</div></br>
                <p style='font-weight:bold;'><a href = 'https://diginext.com.vn'>CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT</a><br /><br /></p>
                    Địa chỉ giao dịch: Tầng 3, Tòa W1-W2 Vinhomes West Point, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội.<br />
                    Tel: (024-028) 5555 1111 | 19005055 | https://diginext.com.vn<br />
                    Email: cskh@diginext.com.vn
            </td>
        </tr>
    </table>";

	return $body;
}
