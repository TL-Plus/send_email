<?php

header('Content-Type: text/html; charset=utf-8');

require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/includes/config.php';

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

function getRowDataTables($table, $sort, $sorttype, $arr_field, $request_page, $query)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_Bill) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');

	if ($arr_field == '') {
		$arr_field = "*";
	}

	$sql = "SELECT " . $arr_field . " FROM " . $table . " " . $query . " order by " . $sort . "  " . $sorttype . "  ";

	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	mysqli_close($dbh);
	return $ret;
}

function getRowDataTables_DIGITEL($table, $sort, $sorttype, $arr_field, $request_page, $query)
{
	$dbh = mysqli_connect(DB_HOST_37, DB_USER_37, DB_PWD_37) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_37) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');

	if ($arr_field == '') {
		$arr_field = "*";
	}

	$sql = "SELECT " . $arr_field . " FROM " . $table . " " . $query . " order by " . $sort . "  " . $sorttype . "  ";

	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	mysqli_close($dbh);
	return $ret;
}

function getJoinRowDataTables($table, $join, $sort, $sorttype, $arr_field, $request_page, $query)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_Bill) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');

	if ($arr_field == '') {
		$arr_field = "*";
	}

	$sql = "SELECT " . $arr_field . " FROM " . $table . " " . $join . " " . $query . " order by " . $sort . "  " . $sorttype . "  ";

	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	mysqli_close($dbh);
	return $ret;
}

function DCNReportIN($serviceNumber, $year, $month, $day, $dayFrom, $dayTo, $categories_expand)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_Voice) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');
	$str_month_id = "";
	$str_day_id = "";
	if ((int) $month < 10) {
		$str_month_id = "0" . (int) $month;
	} else {
		$str_month_id = (int) $month;
	}
	if ((int) $day < 10) {
		$str_day_id = "0" . (int) $day;
	} else {
		$str_day_id = (int) $day;
	}

	$nameCDR = "";
	$callee_object = "fuckall";
	if ($categories_expand == "DIGIFONE") {
		$callee_object = "DIGINEXT_FIXED";
		$nameCDR = "cdr" . $year . $str_month_id . $str_day_id;
	} elseif ($categories_expand == "DIGISIP") {
		$callee_object = "DIGINEXT_SIP";
		$nameCDR = "cdrdsip" . $year . $str_month_id;
	} elseif ($categories_expand == "1800") {
		$callee_object = "DIGINEXT_VAS";
		$nameCDR = "cdrdvgtgt" . $year . $str_month_id;
	} elseif ($categories_expand == "1900") {
		$callee_object = "DIGINEXT_VAS";
		$nameCDR = "cdrdvgtgt" . $year . $str_month_id;
	} else {

		$nameCDR = "cdr";
	}

	$sql = "";
	$condition = "";
	$condition .= " AND DAY(time) = " . $day . " ";
	$condition .= " AND DAY(time)>= " . $dayFrom . " ";
	$condition .= " AND DAY(time)<= " . $dayTo . " ";
	$condition .= " AND MONTH(time)= " . $month . " ";
	$condition .= " AND YEAR(time)= " . $year . " ";
	$condition .= " AND call_type LIKE 'IN_%' ";

	if ($serviceNumber != "") {
		$condition .= " AND  Callee = '84" . $serviceNumber . "' ";
	} else {
		$condition .= " AND Callee LIKE  '%999999999999999999999999999999999999999%' ";
	}

	$sql = " SELECT  ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',1,0)) AS TotalCallsIN, ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',duration,0)) AS TotalDurationIN, ";
	$sql .= " (SUM(IF(call_type LIKE 'IN%',duration,0))/60) AS TotalMinutesIN, ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',provider_cost,0)) AS TotalCostIN ";


	$sql .= " FROM " . $nameCDR . " WHERE duration > 0 AND caller_object != '' AND callee_object='" . $callee_object . "' " . $condition . " ";
	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	return $ret;
}

function DCNReport($serviceNumber, $year, $month, $day, $dayFrom, $dayTo, $categories_expand)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_Voice) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');
	$str_month_id = "";
	$str_day_id = "";
	if ((int) $month < 10) {
		$str_month_id = "0" . (int) $month;
	} else {
		$str_month_id = (int) $month;
	}
	if ((int) $day < 10) {
		$str_day_id = "0" . (int) $day;
	} else {
		$str_day_id = (int) $day;
	}

	$nameCDR = "";
	$caller_object = "fuckall";
	if ($categories_expand == "DIGIFONE") {
		$caller_object = "DIGINEXT_FIXED";
		$nameCDR = "cdr" . $year . $str_month_id . $str_day_id;
	} elseif ($categories_expand == "DIGISIP") {
		$caller_object = "DIGINEXT_SIP";
		$nameCDR = "cdrdsip" . $year . $str_month_id;
	} else {

		$nameCDR = "cdr";
	}

	$sql = "";
	$condition = "";
	$condition .= " AND DAY(time) = " . $day . " ";
	$condition .= " AND DAY(time)>= " . $dayFrom . " ";
	$condition .= " AND DAY(time)<= " . $dayTo . " ";
	$condition .= " AND MONTH(time)= " . $month . " ";
	$condition .= " AND YEAR(time)= " . $year . " ";
	$condition .= " AND call_type LIKE 'OUT_%' ";

	if ($serviceNumber != "") {
		$condition .= " AND  Caller = '84" . $serviceNumber . "' ";
	} else {
		$condition .= " AND Caller LIKE  '%999999999999999999999999999999999999999%' ";
	}

	$sql = " SELECT  ";
	$sql .= " SUM(IF(call_type LIKE 'OUT%',1,0)) AS TotalCalls, ";
	$sql .= " SUM(IF(call_type LIKE 'OUT%',duration,0)) AS TotalDuration, ";
	$sql .= " (SUM(IF(call_type LIKE 'OUT%',duration,0))/60) AS TotalMinutes, ";
	$sql .= " SUM(IF(call_type LIKE 'OUT%',cost,0)) AS TotalCost, ";
	$sql .= " SUM(IF(call_type LIKE 'OUT%',agent_cost,0)) AS TotalCostAgentCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VMS',1,0))  AS VMSCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VMS',duration,0))  AS VMSDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND callee_object='VMS',duration,0))/60)  AS VMSMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VMS',cost,0))  AS VMSCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee NOT LIKE '8487%' AND callee_object='GPC',1,0))  AS GPCCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee NOT LIKE '8487%' AND callee_object='GPC',duration,0))  AS GPCDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND Callee NOT LIKE '8487%' AND callee_object='GPC',duration,0))/60)  AS GPCMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee NOT LIKE '8487%' AND callee_object='GPC',cost,0))  AS GPCCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee LIKE '8487%' AND callee_object='GPC',1,0))  AS ITELCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee LIKE '8487%' AND callee_object='GPC',duration,0))  AS ITELDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND Callee LIKE '8487%' AND callee_object='GPC',duration,0))/60)  AS ITELMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND Callee LIKE '8487%' AND callee_object='GPC',cost,0))  AS ITELCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VIETTEL',1,0))  AS VIETTELCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VIETTEL',duration,0))  AS VIETTELDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND callee_object='VIETTEL',duration,0))/60)  AS VIETTELMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VIETTEL',cost,0))  AS VIETTELCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='GTEL_MOBILE',1,0))  AS GTELCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='GTEL_MOBILE',duration,0))  AS GTELDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND callee_object='GTEL_MOBILE',duration,0))/60)  AS GTELMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='GTEL_MOBILE',cost,0))  AS GTELCost, ";

	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VNM',1,0))  AS VNMCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VNM',duration,0))  AS VNMDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE' AND callee_object='VNM',duration,0))/60)  AS VNMMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE' AND callee_object='VNM',cost,0))  AS VNMCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='CMC_FIXED',1,0))  AS FixedCMCLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='CMC_FIXED',duration,0))  AS FixedCMCLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='CMC_FIXED',duration,0))/60)  AS FixedCMCLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='CMC_FIXED',cost,0))  AS FixedCMCLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VIETTEL_FIXED',1,0))  AS FixedViettelLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VIETTEL_FIXED',duration,0))  AS FixedViettelLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VIETTEL_FIXED',duration,0))/60)  AS FixedViettelLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VIETTEL_FIXED',cost,0))  AS FixedViettelLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='SPT_FIXED',1,0))  AS FixedSPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='SPT_FIXED',duration,0))  AS FixedSPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='SPT_FIXED',duration,0))/60)  AS FixedSPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='SPT_FIXED',cost,0))  AS FixedSPTLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='FPT_FIXED',1,0))  AS FixedFPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='FPT_FIXED',duration,0))  AS FixedFPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='FPT_FIXED',duration,0))/60)  AS FixedFPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='FPT_FIXED',cost,0))  AS FixedFPTLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VNPT_FIXED',1,0))  AS FixedVNPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VNPT_FIXED',duration,0))  AS FixedVNPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VNPT_FIXED',duration,0))/60)  AS FixedVNPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VNPT_FIXED',cost,0))  AS FixedVNPTLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',1,0))  AS FixedVTCLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',duration,0))  AS FixedVTCLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',duration,0))/60)  AS FixedVTCLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',cost,0))  AS FixedVTCLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',1,0))  AS FixedGTELLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',duration,0))  AS FixedGTELLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',duration,0))/60)  AS FixedGTELLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL' AND callee_object='VTC_FIXED',cost,0))  AS FixedGTELLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='CMC_FIXED',1,0))  AS FixedCMCForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='CMC_FIXED',duration,0))  AS FixedCMCForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='CMC_FIXED',duration,0))/60)  AS FixedCMCForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='CMC_FIXED',cost,0))  AS FixedCMCForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VIETTEL_FIXED',1,0))  AS FixedViettelForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VIETTEL_FIXED',duration,0))  AS FixedViettelForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VIETTEL_FIXED',duration,0))/60)  AS FixedViettelForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VIETTEL_FIXED',cost,0))  AS FixedViettelForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='SPT_FIXED',1,0))  AS FixedSPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='SPT_FIXED',duration,0))  AS FixedSPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='SPT_FIXED',duration,0))/60)  AS FixedSPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='SPT_FIXED',cost,0))  AS FixedSPTForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='FPT_FIXED',1,0))  AS FixedFPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='FPT_FIXED',duration,0))  AS FixedFPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='FPT_FIXED',duration,0))/60)  AS FixedFPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='FPT_FIXED',cost,0))  AS FixedFPTForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VNPT_FIXED',1,0))  AS FixedVNPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VNPT_FIXED',duration,0))  AS FixedVNPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VNPT_FIXED',duration,0))/60)  AS FixedVNPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VNPT_FIXED',cost,0))  AS FixedVNPTForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',1,0))  AS FixedVTCForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',duration,0))  AS FixedVTCForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',duration,0))/60)  AS FixedVTCForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',cost,0))  AS FixedVTCForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',1,0))  AS FixedGTELForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',duration,0))  AS FixedGTELForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',duration,0))/60)  AS FixedGTELForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN' AND callee_object='VTC_FIXED',cost,0))  AS FixedGTELForeignCost, ";

	$sql .= " SUM(IF(call_type='OUT_INTERNATIONAL',1,0))  AS InternationalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_INTERNATIONAL',duration,0))  AS InternationalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_INTERNATIONAL',duration,0))/60)  AS InternationalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_INTERNATIONAL',cost,0))  AS InternationalCost ,";

	$sql .= " SUM(IF(call_type='OUT_MOBILE',1,0))  AS MobileCalls, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE',duration,0))  AS MobileDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_MOBILE',duration,0))/60)  AS MobileMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_MOBILE',cost,0))  AS MobileCost, ";

	$sql .= " SUM(IF(call_type='OUT_VAS',1,0))  AS VASCalls, ";
	$sql .= " SUM(IF(call_type='OUT_VAS',duration,0))  AS VASDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_VAS',duration,0))/60)  AS VASMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_VAS',cost,0))  AS VASCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL',1,0))  AS FixedLocalCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL',duration,0))  AS FixedLocalDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL',duration,0))/60)  AS FixedLocalMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='LOCAL',cost,0))  AS FixedLocalCost, ";

	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN',1,0))  AS FixedForeignCalls, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN',duration,0)) AS FixedForeignDuration, ";
	$sql .= " (SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN',duration,0))/60) AS FixedForeignMinutes, ";
	$sql .= " SUM(IF(call_type='OUT_FIXED' AND fixed_type='FOREIGN',cost,0))  AS FixedForeignCost ";

	$sql .= " FROM  " . $nameCDR . "  WHERE duration > 0 AND callee_object != '' AND caller_object='" . $caller_object . "' " . $condition . " ";

	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	return $ret;
}

function DCNReportDVGTGT($serviceNumber, $year, $month, $day, $dayFrom, $dayTo, $categories_expand)
{
	$dbh = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die("Unable to connect to MySQL");
	$select_db = mysqli_select_db($dbh, DB_NAME_Voice) or die("getRole() Could not select database");
	mysqli_set_charset($dbh, 'utf8');
	$str_month_id = "";
	$str_day_id = "";
	if ((int) $month < 10) {
		$str_month_id = "0" . (int) $month;
	} else {
		$str_month_id = (int) $month;
	}
	if ((int) $day < 10) {
		$str_day_id = "0" . (int) $day;
	} else {
		$str_day_id = (int) $day;
	}

	$nameCDR = "";
	if ($categories_expand == "1900") {

		$nameCDR = "cdrdvgtgt" . $year . $str_month_id;
	} elseif ($categories_expand == "1800") {

		$nameCDR = "cdrdvgtgt" . $year . $str_month_id;
	} else {

		$nameCDR = "cdr";
	}

	$sql = "";
	$condition = "";
	$condition .= " AND DAY(time) = " . $day . " ";
	$condition .= " AND DAY(time)>= " . $dayFrom . " ";
	$condition .= " AND DAY(time)<= " . $dayTo . " ";
	$condition .= " AND MONTH(time)= " . $month . " ";
	$condition .= " AND YEAR(time)= " . $year . " ";
	$condition .= " AND call_type LIKE 'IN_%' ";

	if ($serviceNumber != "") {
		$condition .= " AND  Callee = '" . $serviceNumber . "' ";
	} else {
		$condition .= " AND Callee LIKE  '%999999999999999999999999999999999999999%' ";
	}

	$sql = " SELECT  ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',1,0)) AS TotalCalls, ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',duration,0)) AS TotalDuration, ";
	$sql .= " (SUM(IF(call_type LIKE 'IN%',duration,0))/60) AS TotalMinutes, ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',cost,0)) AS TotalCost, ";
	$sql .= " SUM(IF(call_type LIKE 'IN%',agent_cost,0)) AS TotalCostAgentCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VMS',1,0))  AS VMSCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VMS',duration,0))  AS VMSDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND caller_object='VMS',duration,0))/60)  AS VMSMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VMS',cost,0))  AS VMSCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller NOT LIKE '8487%' AND caller_object='GPC',1,0))  AS GPCCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller NOT LIKE '8487%' AND caller_object='GPC',duration,0))  AS GPCDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND Caller NOT LIKE '8487%' AND caller_object='GPC',duration,0))/60)  AS GPCMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller NOT LIKE '8487%' AND caller_object='GPC',cost,0))  AS GPCCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller LIKE '8487%' AND caller_object='GPC',1,0))  AS ITELCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller LIKE '8487%' AND caller_object='GPC',duration,0))  AS ITELDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND Caller LIKE '8487%' AND caller_object='GPC',duration,0))/60)  AS ITELMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND Caller LIKE '8487%' AND caller_object='GPC',cost,0))  AS ITELCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VIETTEL',1,0))  AS VIETTELCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VIETTEL',duration,0))  AS VIETTELDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND caller_object='VIETTEL',duration,0))/60)  AS VIETTELMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VIETTEL',cost,0))  AS VIETTELCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='GTEL_MOBILE',1,0))  AS GTELCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='GTEL_MOBILE',duration,0))  AS GTELDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND caller_object='GTEL_MOBILE',duration,0))/60)  AS GTELMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='GTEL_MOBILE',cost,0))  AS GTELCost, ";

	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VNM',1,0))  AS VNMCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VNM',duration,0))  AS VNMDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE' AND caller_object='VNM',duration,0))/60)  AS VNMMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE' AND caller_object='VNM',cost,0))  AS VNMCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='CMC_FIXED',1,0))  AS FixedCMCLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='CMC_FIXED',duration,0))  AS FixedCMCLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='CMC_FIXED',duration,0))/60)  AS FixedCMCLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='CMC_FIXED',cost,0))  AS FixedCMCLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VIETTEL_FIXED',1,0))  AS FixedViettelLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VIETTEL_FIXED',duration,0))  AS FixedViettelLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VIETTEL_FIXED',duration,0))/60)  AS FixedViettelLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VIETTEL_FIXED',cost,0))  AS FixedViettelLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='SPT_FIXED',1,0))  AS FixedSPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='SPT_FIXED',duration,0))  AS FixedSPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='SPT_FIXED',duration,0))/60)  AS FixedSPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='SPT_FIXED',cost,0))  AS FixedSPTLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='FPT_FIXED',1,0))  AS FixedFPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='FPT_FIXED',duration,0))  AS FixedFPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='FPT_FIXED',duration,0))/60)  AS FixedFPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='FPT_FIXED',cost,0))  AS FixedFPTLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VNPT_FIXED',1,0))  AS FixedVNPTLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VNPT_FIXED',duration,0))  AS FixedVNPTLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VNPT_FIXED',duration,0))/60)  AS FixedVNPTLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VNPT_FIXED',cost,0))  AS FixedVNPTLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',1,0))  AS FixedVTCLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',duration,0))  AS FixedVTCLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',duration,0))/60)  AS FixedVTCLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',cost,0))  AS FixedVTCLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',1,0))  AS FixedGTELLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',duration,0))  AS FixedGTELLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',duration,0))/60)  AS FixedGTELLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL' AND caller_object='VTC_FIXED',cost,0))  AS FixedGTELLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='CMC_FIXED',1,0))  AS FixedCMCForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='CMC_FIXED',duration,0))  AS FixedCMCForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='CMC_FIXED',duration,0))/60)  AS FixedCMCForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='CMC_FIXED',cost,0))  AS FixedCMCForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VIETTEL_FIXED',1,0))  AS FixedViettelForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VIETTEL_FIXED',duration,0))  AS FixedViettelForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VIETTEL_FIXED',duration,0))/60)  AS FixedViettelForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VIETTEL_FIXED',cost,0))  AS FixedViettelForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='SPT_FIXED',1,0))  AS FixedSPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='SPT_FIXED',duration,0))  AS FixedSPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='SPT_FIXED',duration,0))/60)  AS FixedSPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='SPT_FIXED',cost,0))  AS FixedSPTForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='FPT_FIXED',1,0))  AS FixedFPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='FPT_FIXED',duration,0))  AS FixedFPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='FPT_FIXED',duration,0))/60)  AS FixedFPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='FPT_FIXED',cost,0))  AS FixedFPTForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VNPT_FIXED',1,0))  AS FixedVNPTForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VNPT_FIXED',duration,0))  AS FixedVNPTForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VNPT_FIXED',duration,0))/60)  AS FixedVNPTForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VNPT_FIXED',cost,0))  AS FixedVNPTForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',1,0))  AS FixedVTCForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',duration,0))  AS FixedVTCForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',duration,0))/60)  AS FixedVTCForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',cost,0))  AS FixedVTCForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',1,0))  AS FixedGTELForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',duration,0))  AS FixedGTELForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',duration,0))/60)  AS FixedGTELForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN' AND caller_object='VTC_FIXED',cost,0))  AS FixedGTELForeignCost, ";

	$sql .= " SUM(IF(call_type='IN_INTERNATIONAL',1,0))  AS InternationalCalls, ";
	$sql .= " SUM(IF(call_type='IN_INTERNATIONAL',duration,0))  AS InternationalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_INTERNATIONAL',duration,0))/60)  AS InternationalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_INTERNATIONAL',cost,0))  AS InternationalCost ,";

	$sql .= " SUM(IF(call_type='IN_MOBILE',1,0))  AS MobileCalls, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE',duration,0))  AS MobileDuration, ";
	$sql .= " (SUM(IF(call_type='IN_MOBILE',duration,0))/60)  AS MobileMinutes, ";
	$sql .= " SUM(IF(call_type='IN_MOBILE',cost,0))  AS MobileCost, ";

	$sql .= " SUM(IF(call_type='IN_VAS',1,0))  AS VASCalls, ";
	$sql .= " SUM(IF(call_type='IN_VAS',duration,0))  AS VASDuration, ";
	$sql .= " (SUM(IF(call_type='IN_VAS',duration,0))/60)  AS VASMinutes, ";
	$sql .= " SUM(IF(call_type='IN_VAS',cost,0))  AS VASCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL',1,0))  AS FixedLocalCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL',duration,0))  AS FixedLocalDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL',duration,0))/60)  AS FixedLocalMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='LOCAL',cost,0))  AS FixedLocalCost, ";

	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN',1,0))  AS FixedForeignCalls, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN',duration,0)) AS FixedForeignDuration, ";
	$sql .= " (SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN',duration,0))/60) AS FixedForeignMinutes, ";
	$sql .= " SUM(IF(call_type='IN_FIXED' AND fixed_type='FOREIGN',cost,0))  AS FixedForeignCost ";

	$sql .= " FROM " . $nameCDR . " WHERE duration > 0 AND caller_object != '' AND callee_object = 'DIGINEXT_VAS' " . $condition . " ";
	$result = mysqli_query($dbh, $sql, MYSQLI_ASSOC);
	$ret = array();
	while ($row = mysqli_fetch_array($result)) {
		$ret[] = $row;
	}
	return $ret;
}

function ExportToExcel($ArrayNumberInfo, $customer_code, $contract_code, $categories_code, $user_code, $file_intime)
{
	$DayExport = date('d-m-Y');
	$FileName = '/var/www/VoiceReport/ToolDaily/CM/Excel/' . $file_intime . '_Liquidated_Contract_' . str_replace('/', '_', $contract_code) . '_' . str_replace('/', '_', $categories_code) . '_' . $user_code . '_' . str_replace('-', '_', $DayExport) . '.xlsx';
	$count_n = 0;
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
	//php /var/www/VoiceReport/ToolDaily/Export/BookNumber.php
	if (!file_exists('/var/www/VoiceReport/ToolDaily/CM/Excel/')) {
		mkdir('/var/www/VoiceReport/ToolDaily/CM/Excel/', 0755, true);
	}

	unlink('/var/www/VoiceReport/ToolDaily/CM/Excel/' . $file_intime . '_Liquidated_Contract_' . str_replace('/', '_', $contract_code) . '_' . str_replace('/', '_', $categories_code) . '_' . $user_code . '_' . str_replace('-', '_', $DayExport) . '.xlsx');
	$writer->openToFile('/var/www/VoiceReport/ToolDaily/CM/Excel/' . $file_intime . '_Liquidated_Contract_' . str_replace('/', '_', $contract_code) . '_' . str_replace('/', '_', $categories_code) . '_' . $user_code . '_' . str_replace('-', '_', $DayExport) . '.xlsx');

	$sheet = $writer->getCurrentSheet();
	$sheet->setName('AddContract');

	$options->mergeCells(0, 1, 8, 1, $writer->getCurrentSheet()->getIndex());
	$Header = Row::fromValues(['Diginext - Cuộc sống số'], $headr_style);
	$writer->addRow($Header);

	$sheet->setColumnWidth(45, 1);
	$sheet->setColumnWidth(20, 2);
	$sheet->setColumnWidth(35, 3);
	$sheet->setColumnWidth(15, 4);
	$sheet->setColumnWidth(35, 5);
	$sheet->setColumnWidth(15, 6);
	$sheet->setColumnWidth(25, 7);
	$sheet->setColumnWidth(25, 8);

	$ArrayExcel = Row::fromValues(['KHÁCH HÀNG', 'MÃ KHÁCH HÀNG', 'MÃ HỢP ĐỒNG', 'PHỤ LỤC', 'MÃ DỊCH VỤ', 'KINH DOANH', 'ĐẦU SỐ/ EXT', 'NGÀY TẠM NGƯNG', 'LÝ DO'], $style);
	$writer->addRow($ArrayExcel);
	$count_color = 2;


	foreach ($ArrayNumberInfo as $row) {

		if ($count_color % 2 == 0) {

			$Array = Row::fromValues([$row['customer_name'], $row['customer_code'], $row['contract_code'], $row['addendum'], $row['categories_code'], $row['user_name'], $row['ext_number'], date('d-m-Y', strtotime($row['suspension_at'])), $row['suspension_reason']], $style1);
		} elseif ($count_color % 2 != 0) {

			$Array = Row::fromValues([$row['customer_name'], $row['customer_code'], $row['contract_code'], $row['addendum'], $row['categories_code'], $row['user_name'], $row['ext_number'], date('d-m-Y', strtotime($row['suspension_at'])), $row['suspension_reason']], $style2);
		}
		$count_color++;
		$writer->addRow($Array);
	}
	$writer->close();
	return $FileName;
}
