<?php

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/Ho_Chi_Minh");

define('DB_HOST', '103.112.209.141');
define('DB_USER', 'billing');
define('DB_PWD', 'Admin@diginext2023');
define('DB_NAME_Bill', 'billing');
define('DB_NAME_Voice', 'VoiceReport');

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;

require_once '/var/www/VoiceReport/ToolDaily/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';

$context = stream_context_create(array(
	'http' => array(
		'timeout' => 3   // Timeout in seconds
	)
));

function InsertToDB_Voice($query)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	mysqli_set_charset($dbh, 'utf8');
	$select_db_1 = mysqli_select_db($dbh, DB_NAME_Voice) or die("getRole() Could not select database");

	$result = mysqli_query($dbh, $query, MYSQLI_ASSOC);
	mysqli_close($dbh);
	return $result;
}

function ExportToArray_Voice($query)
{
	$ret = array();
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	mysqli_set_charset($dbh, 'utf8');
	$select_db_1 = mysqli_select_db($dbh, DB_NAME_Voice) or die("getRole() Could not select database");

	$result = mysqli_query($dbh, $query, MYSQLI_ASSOC);
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	mysqli_close($dbh);
	return $ret;
}

function InsertToDB_Bill($query)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	mysqli_set_charset($dbh, 'utf8');
	$select_db_1 = mysqli_select_db($dbh, DB_NAME_Bill) or die("getRole() Could not select database");

	$result = mysqli_query($dbh, $query, MYSQLI_ASSOC);
	mysqli_close($dbh);
	return $result;
}

function ExportToArray_Bill($query)
{
	$ret = array();
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	mysqli_set_charset($dbh, 'utf8');
	$select_db_1 = mysqli_select_db($dbh, DB_NAME_Bill) or die("getRole() Could not select database");

	$result = mysqli_query($dbh, $query, MYSQLI_ASSOC);
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	mysqli_close($dbh);
	return $ret;
}

function ExportToExcel($query_report_ctc, $query_report_ctc_688, $today)
{

	$FileName = '/var/www/html/report_ctc/files/Report_CTC_' . $today . '.xlsx';

	$style = new Style();
	$style->setFontBold();
	$style->setFontSize(13);
	$style->setShouldWrapText();
	$style->setCellAlignment(CellAlignment::LEFT);
	$style->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM);
	$style->setBackgroundColor('00BFFF');

	$headr_style = new Style();
	$headr_style->setFontBold();
	$headr_style->setFontSize(13);
	$headr_style->setShouldWrapText();
	$headr_style->setCellAlignment(CellAlignment::CENTER);
	$headr_style->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM);
	$headr_style->setBackgroundColor('318cf5');

	$style1 = new Style();
	$style1->setFontSize(12);
	$style1->setShouldWrapText();
	$style1->setCellAlignment(CellAlignment::LEFT);
	$style1->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM);
	$style1->setBackgroundColor('FFFFFF');


	$style2 = new Style();
	$style2->setFontSize(12);
	$style2->setShouldWrapText();
	$style2->setCellAlignment(CellAlignment::LEFT);
	$style2->setCellVerticalAlignment(CellVerticalAlignment::BOTTOM);
	$style2->setBackgroundColor('dcf7f7');


	$options = new Options();
	$writer = new Writer($options);

	if (!file_exists('/var/www/html/report_ctc/files/')) {
		mkdir('/var/www/html/report_ctc/files/', 0755, true);
	}

	unlink('/var/www/html/report_ctc/files/Report_CTC_' . $today . '.xlsx');
	$writer->openToFile('/var/www/html/report_ctc/files/Report_CTC_' . $today . '.xlsx');

	$sheet = $writer->getCurrentSheet();
	$sheet->setName('CTC');

	$sheet->setColumnWidth(60, 1);
	$sheet->setColumnWidth(40, 2);
	$sheet->setColumnWidth(25, 3);
	$sheet->setColumnWidth(25, 4);
	$sheet->setColumnWidth(25, 5);
	$sheet->setColumnWidth(25, 6);

	$ArrayExcel = Row::fromValues(['CustomerName', 'SalerName', 'TotalCost', 'TotalCurrentCall', 'BlockViettel', 'ActiveViettel'], $style);
	$writer->addRow($ArrayExcel);
	$count_color = 1;

	foreach ($query_report_ctc_688 as $row) {
		if ($count_color % 2 == 0) {

			$Array = Row::fromValues([$row['CustomerName'] . ' [Số 688]', $row['SalerName'], $row['TotalCost'], $row['TotalCurrentCall'], $row['BlockViettel'], $row['ActiveViettel']], $style1);
		} elseif ($count_color % 2 != 0) {

			$Array = Row::fromValues([$row['CustomerName'] . ' [Số 688]', $row['SalerName'], $row['TotalCost'], $row['TotalCurrentCall'], $row['BlockViettel'], $row['ActiveViettel']], $style2);
		}
		$count_color++;
		$writer->addRow($Array);
	}

	foreach ($query_report_ctc as $row) {
		if ($count_color % 2 == 0) {

			$Array = Row::fromValues([$row['CustomerName'], $row['SalerName'], $row['TotalCost'], $row['TotalCurrentCall'], $row['BlockViettel'], $row['ActiveViettel']], $style1);
		} elseif ($count_color % 2 != 0) {

			$Array = Row::fromValues([$row['CustomerName'], $row['SalerName'], $row['TotalCost'], $row['TotalCurrentCall'], $row['BlockViettel'], $row['ActiveViettel']], $style2);
		}
		$count_color++;
		$writer->addRow($Array);
	}

	$writer->close();

	return $FileName;
}

$now_day_full_time = date('Y-m-d H:i:s');
$today = date('Y_m_d');
$year = date('Y', strtotime($now_day_full_time));
$month = date('m', strtotime($now_day_full_time));
$table_name = "dcn" . $year . $month;

$query_report_ctc = "SELECT 
    $table_name.customer_name AS CustomerName,
    $table_name.user_name AS SalerName,
    SUM($table_name.TotalCost) AS TotalCost,
    NULL AS TotalCurrentCall,
    (
        SELECT COUNT(ext_number)
        FROM billing.report_number_block rnb
        WHERE rnb.customer_name = $table_name.customer_name
        AND DATE(rnb.time_update) = CURDATE()
    ) AS BlockViettel,
    (
        SELECT COUNT(ext_number)
        FROM billing.report_number_active rna
        WHERE rna.customer_name = $table_name.customer_name
        AND DATE(rna.time_update) = CURDATE()
    ) AS ActiveViettel
FROM
    $table_name
WHERE            
    DATE($table_name.TimeUpdate) = CURDATE() 
    AND day = DAY(CURDATE())
    AND $table_name.company_code = 'DIGINEXT'
    AND (
        (SUBSTRING($table_name.ext_number, 1, 2) IN ('24', '28') AND SUBSTRING($table_name.ext_number, 3, 3) != '688') 
        OR 
        (SUBSTRING($table_name.ext_number, 1, 2) NOT IN ('24', '28') AND SUBSTRING($table_name.ext_number, 4, 3) != '688')
    )
GROUP BY
    $table_name.customer_name
ORDER BY 
    TotalCost DESC 
LIMIT 30;";

$query_report_ctc_688 = "SELECT 
    $table_name.customer_name AS CustomerName,
    $table_name.user_name AS SalerName,
    SUM($table_name.TotalCost) AS TotalCost,
    NULL AS TotalCurrentCall,
    (
        SELECT COUNT(ext_number)
        FROM billing.report_number_block rnb
        WHERE rnb.customer_name = $table_name.customer_name
        AND DATE(rnb.time_update) = CURDATE()
    ) AS BlockViettel,
    (
        SELECT COUNT(ext_number)
        FROM billing.report_number_active rna
        WHERE rna.customer_name = $table_name.customer_name
        AND DATE(rna.time_update) = CURDATE()
    ) AS ActiveViettel
FROM
    $table_name
WHERE            
    DATE($table_name.TimeUpdate) = CURDATE() 
    AND day = DAY(CURDATE())
    AND $table_name.company_code = 'DIGINEXT'
	AND $table_name.ext_number LIKE '2%' 
    AND (
        (SUBSTRING($table_name.ext_number, 1, 2) IN ('24', '28') AND SUBSTRING($table_name.ext_number, 3, 3) = '688') 
        OR 
        (SUBSTRING($table_name.ext_number, 1, 2) NOT IN ('24', '28') AND SUBSTRING($table_name.ext_number, 4, 3) = '688')
    )
GROUP BY
    $table_name.customer_name
ORDER BY 
    TotalCost DESC 
LIMIT 30;";

$get_report_ctc = ExportToArray_Voice($query_report_ctc);
$get_report_ctc_688 = ExportToArray_Voice($query_report_ctc_688);

$FileName = ExportToExcel($get_report_ctc, $get_report_ctc_688, $today);


$currentTime = date('d-m-Y H:i');
$userName = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];

$message = "";
$message .= "Dữ liệu Báo Cáo Cuộc Gọi Hệ Thống VOS DIGINEXT ngày: $currentTime đã được cập nhật xong! \n";
$message .= "Kính mời đội ngũ vận hành vào website: http://103.112.209.152/report_ctc/ để xem và cập nhật thêm dữ liệu! \n";
$message .= "Hãy đăng nhập với tài khoản sau đây:\n";
$message .= "Tài khoản: $userName\n";
$message .= "Mật khẩu: $password\n";

$apiToken = $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_REPORT_CCU'];
$data = [
	'chat_id' => $_ENV['TELEGRAM_CHAT_ID_REPORT_CCU'],
	'text' => "$message",
	"parse_mode" => "html"
];
$responseNewCustomer = @file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), 0, $context);


$apiToken = $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];
$data = [
	'chat_id' => $_ENV['TELEGRAM_CHAT_ID'],
	'text' => "$message",
	"parse_mode" => "html"
];
$responseNewCustomer = @file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), 0, $context);

// $curl = curl_init();

// curl_setopt_array(
// 	$curl,
// 	array(
// 		CURLOPT_URL => "https://api.telegram.org/bot$apiToken/sendDocument?chat_id=$chatId",
// 		CURLOPT_RETURNTRANSFER => true,
// 		CURLOPT_ENCODING => '',
// 		CURLOPT_MAXREDIRS => 10,
// 		CURLOPT_TIMEOUT => 0,
// 		CURLOPT_FOLLOWLOCATION => true,
// 		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 		CURLOPT_CUSTOMREQUEST => 'POST',
// 		CURLOPT_POSTFIELDS => array(
// 			'document' => new CURLFILE($FileName),
// 			'caption' => $message,
// 			'parse_mode' => 'html'
// 		)
// 	)
// );
// $response = curl_exec($curl);
// curl_close($curl);
// echo $response;
