<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 134;
//Init
include_once "../_init_.php";

$C_SETTLE = new Settle();

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["XLS_TRANSACTION_STATE"] = "Y";

$_list_set_ary = array();

$period = $_GET["period"];
$periodAry = array("day", "week", "month");
if(!in_array($period, $periodAry)){
	$period = $periodAry[0];
}

$show = $_GET["show"];
$showAry = array("show_all", "show_sale", "show_purchase");
if(!in_array($show, $showAry)){
    $show = $showAry[0];
}


$date = $_GET["date"];
$date_text = $date;

if($period == "week"){
	$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));

	$date_text = $prev_date . " ~ " . $date_text;
}elseif($period == "month"){
	$date_text = $_GET["date_year"] . "년 "  . $_GET["date_month"] . "월";
}

//매출현황(외상매출금)
if($period == "month"){
	$date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
	$date = date('Y-m-t', strtotime($date));
}
$_list_sale_credit = $C_SETTLE->getTransactionStateSaleCredit($period, $date, "N",false);
$_list_set_ary[] = array("type" => "SALE_CREDIT_IN_AMOUNT", "title" => "매출현황(외상매출금)" , "list" => $_list_sale_credit, "PREV" => "미수금액", "TOTAL" => "매출합계", "TRAN" => "입금액");

//매출현황(일반거래처)
if($period == "month"){
    $date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
    $date = date('Y-m-t', strtotime($date));
}
$_list_sale_prepay = $C_SETTLE->getTransactionStateSaleCredit($period, $date, "N", true);
$_list_set_ary[] = array("type" => "SALE_CREDIT_IN_AMOUNT", "title" => "매출현황(일반거래처)" , "list" => $_list_sale_prepay, "PREV" => "잔액", "TOTAL" => "매출합계", "TRAN" => "입금액");

//매출현황(선입금)
if($period == "month"){
	$date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
	$date = date('Y-m-t', strtotime($date));
}
$_list_sale_prepay = $C_SETTLE->getTransactionStateSaleCredit($period, $date, "Y", true);
$_list_set_ary[] = array("type" => "SALE_PREPAY_IN_AMOUNT", "title" => "매출현황(선입금)" , "list" => $_list_sale_prepay, "PREV" => "잔액", "TOTAL" => "매출합계", "TRAN" => "입금액");

if($period == "day") {
	//매출현황(기타)
	$_list_sale_etc  = $C_SETTLE->getTransactionEtc("SALE_ETC", $_GET["date"]);
	$_list_set_ary[] = array("type" => "SALE_ETC", "title" => "매출현황(기타)", "list" => $_list_sale_etc, "PREV" => "미수금액", "TOTAL" => "판매금액", "TRAN" => "입금액");
}

//매입현황(외상매입금) - 일결제 업체
if($period == "month"){
	$date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
	$date = date('Y-m-t', strtotime($date));
}
$supplier_payment_type = "DAY";
$_list_purchase_credit_d = $C_SETTLE->getTransactionStatePurchaseCredit($period, $date, "N", $supplier_payment_type);
$_list_set_ary[] = array("type" => "PURCHASE_CREDIT_IN_AMOUNT", "title" => "매입현황(외상매입금) - 일 결제 업체" , "list" => $_list_purchase_credit_d, "PREV" => "미지급금액", "TOTAL" => "매입합계", "TRAN" => "송금액");

//매입현황(외상매입금) - 월결제 업체
if($period == "month"){
	$date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
	$date = date('Y-m-t', strtotime($date));
}
$supplier_payment_type = "MONTH";
$_list_purchase_credit_m = $C_SETTLE->getTransactionStatePurchaseCredit($period, $date, "N", $supplier_payment_type);
$_list_set_ary[] = array("type" => "PURCHASE_CREDIT_IN_AMOUNT", "title" => "매입현황(외상매입금) - 월 결제 업체" , "list" => $_list_purchase_credit_m, "PREV" => "미지급금액", "TOTAL" => "매입합계", "TRAN" => "송금액");


//매입현황(선급금)
if($period == "month"){
	$date = $_GET["date_year"] . "-" . $_GET["date_month"] . "-01";
	$date = date('Y-m-t', strtotime($date));
}
$supplier_payment_type = "";
$_list_purchase_prepay = $C_SETTLE->getTransactionStatePurchaseCredit($period, $date, "Y", $supplier_payment_type);
$_list_set_ary[] = array("type" => "PURCHASE_PREPAY_IN_AMOUNT", "title" => "매입현황(선급금)" , "list" => $_list_purchase_prepay, "PREV" => "잔액", "TOTAL" => "매입합계", "TRAN" => "송금액");

if($period == "day") {
	//매입현황(기타)
	$_list_purchase_etc = $C_SETTLE->getTransactionEtc("PURCHASE_ETC", $_GET["date"]);
	$_list_set_ary[]    = array("type" => "PURCHASE_ETC", "title" => "매입현황(기타)", "list" => $_list_purchase_etc, "PREV" => "미지금금액", "TOTAL" => "발생금액", "TRAN" => "송금액");
}
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();

$i = 1;

//날짜 세팅
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "날짜 : " . $date_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

$i++;
$i++;
//Header ????
$xls_header = array(
	array(
		"header_name" => "거래처명",
		"field_name" => "customer_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => ($period == "day") ? "전일 {{PREV}}" : "이월 {{PREV}}",
		"field_name" => "prev_total",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "{{TOTAL}}",
		"field_name" => "today_settle_amount",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "{{TRAN}}",
		"field_name" => "today_tran_amount",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "현재잔액",
		"field_name" => "today_total",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "비고",
		"field_name" => "today_tran_memo",
		"width" => 60,
		"halign" => "left",
	),
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
foreach($xls_header as $hh)
{
	$header_ary[] = $hh["header_name"];
}


//세트 Loop 시작
foreach($_list_set_ary as $_list_one) {

    //전체,매출,매입 선택 유형별 처리
    if($show == "show_sale" && ($_list_one["type"] == 'PURCHASE_CREDIT_IN_AMOUNT' || $_list_one["type"] == 'PURCHASE_PREPAY_IN_AMOUNT' || $_list_one["type"] == 'PURCHASE_ETC')){
        continue;
    }else if($show == "show_purchase" && ($_list_one["type"] == 'SALE_CREDIT_IN_AMOUNT' || $_list_one["type"] == 'SALE_PREPAY_IN_AMOUNT' || $_list_one["type"] == 'SALE_ETC')){
        continue;
    }


	//제목 세팅
	$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
	//$activesheet->mergeCells($mergeCodRange);
	$cod = getNameFromNumber(0) . $i;
	$activesheet->setCellValueExplicit($cod, $_list_one["title"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$i++;


	//헤더 이름 치환
	$new_header_ary = array();
	foreach($header_ary as $h_tmp){
		$h_tmp = str_replace("{{PREV}}", $_list_one["PREV"], $h_tmp);
		$h_tmp = str_replace("{{TOTAL}}", $_list_one["TOTAL"], $h_tmp);
		$h_tmp = str_replace("{{TRAN}}", $_list_one["TRAN"], $h_tmp);

		$new_header_ary[] = $h_tmp;
	}

	//각 영역별 헤더 설정
	$activesheet->fromArray($new_header_ary, NULL, 'A'.$i);
	$activesheet->getStyle('A'.$i.':'.$xls_header_end.$i)->applyFromArray(
		array(
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'FFED7D31',
				],
				'endColor' => [
					'argb' => 'FFED7D31',
				],
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
			]
		)
	);

	//$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
	//$activesheet->mergeCells($mergeCodRange);

	foreach($xls_header as $key => $hh)
	{
		$columnID = getNameFromNumber($key);
		$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
	}


	$i++;

	$_list = $_list_one["list"];
	/*
	 * Total Calculate
	 */
	$total_prev_total          = 0;
	$total_today_settle_amount = 0;
	$total_today_tran_amount   = 0;
	$total_today_total         = 0;

	foreach ($_list as $row_num => $row) {

		//hardcoding for 회계 20200316
		if(strpos($row["customer_name"],"덕윤(") !== false){
			continue;
		}

		$xls_row = array();
		foreach ($xls_header as $key => $val) {
			$xls_row[] = $row[$val["field_name"]];
		}

		$prev_settle_amount  = $row["prev_settle_amount"];
		$today_settle_amount = $row["today_settle_amount"];
		$prev_tran_amount    = $row["prev_tran_amount"];
		$today_tran_amount   = $row["today_tran_amount"];
		$tran_remain_amount  = $row["tran_remain_amount"];

		$prev_total = 0;
		$today_total = 0;

		if($_list_one["type"] == "SALE_ETC" || $_list_one["type"] == "PURCHASE_ETC"){
			$prev_total = $prev_settle_amount;
			$today_total = $tran_remain_amount;
		}else{
			$prev_total = $prev_settle_amount - $prev_tran_amount;
			$today_total = $prev_total + $today_settle_amount - $today_tran_amount;
		}

		$total_prev_total          += $prev_total;
		$total_today_settle_amount += $today_settle_amount;
		$total_today_tran_amount   += $today_tran_amount;
		$total_today_total         += $today_total;

		//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
		if($_list_one["type"] == "SALE_PREPAY_IN_AMOUNT"){
			$prev_total = $prev_total * -1;
			$today_total = $today_total * -1;
		}

		//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
		$currentColumn = 0;
		$isTotal       = false;
		foreach ($xls_row as $cellValue) {
			$field_name = $xls_header[$currentColumn]["field_name"];
			$cod        = getNameFromNumber($currentColumn) . $i;
			$data_type  = $xls_header[$currentColumn]["data_type"];
			$halign     = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
			$valign     = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

			if ($field_name == "prev_total") {
				$cellValue = $prev_total;
			} elseif ($field_name == "today_total") {
				$cellValue = $today_total;
			}

			if ($data_type == "money") {
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
			} elseif ($data_type == "number") {
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
			} elseif ($data_type == "image") {

			} else {
				//강제 텍스트 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			}

			$activesheet->getStyle($cod)->getAlignment()->setHorizontal($halign);
			$activesheet->getStyle($cod)->getAlignment()->setVertical($halign);

			++$currentColumn;
		}

		$i++;
	}


//합계
	$cod = getNameFromNumber(0) . $i;
	$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

	$last_row                      = end($_list);
	$settle_sale_supply            = $last_row["settle_sale_supply"];
	$settle_sale_supply_ex_vat     = $last_row["settle_sale_supply_ex_vat"];
	$settle_purchase_supply        = $last_row["settle_purchase_supply"];
	$settle_purchase_supply_ex_vat = $last_row["settle_purchase_supply_ex_vat"];
	$settle_sale_profit            = $last_row["settle_sale_profit"];


	//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
	if($_list_one["type"] == "SALE_PREPAY_IN_AMOUNT"){
		$total_prev_total = $total_prev_total * -1;
		$total_today_total = $total_today_total * -1;
	}


	$cod = getNameFromNumber(1) . $i;
	$activesheet->setCellValueExplicit($cod, $total_prev_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
	$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

	$cod = getNameFromNumber(2) . $i;
	$activesheet->setCellValueExplicit($cod, $total_today_settle_amount, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
	$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

	$cod = getNameFromNumber(3) . $i;
	$activesheet->setCellValueExplicit($cod, $total_today_tran_amount, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
	$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

	$cod = getNameFromNumber(4) . $i;
	$activesheet->setCellValueExplicit($cod, $total_today_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
	$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

	$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(5) . $i;
	$activesheet->getStyle($totalRowRangeCod)->applyFromArray(
		array(
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'FFC4C4C4',
				],
				'endColor' => [
					'argb' => 'FFC4C4C4',
				],
			]
		)
	);

	$i++;
	$i++;
}
//foreach(range('A', $xls_header_end) as $columnID) {
//$activesheet->getColumnDimension($columnID)->setAutoSize(true);
//}

//foreach($xls_header as $key => $hh)
//{
//	$columnID = getNameFromNumber($key);
//	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
//}

ob_end_flush();
$_SESSION["XLS_TRANSACTION_STATE"] = "";

ob_end_clean();


//$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/

function mb_basename($path) { return end(explode('/',$path)); }
function utf2euc($str) { return iconv("UTF-8","cp949//TRANSLIT", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}
$user_filename = "거래현황.xlsx";
//if (is_ie()) $user_filename = utf2euc($user_filename);
if (is_ie()) $user_filename = urlencode($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');


?>
