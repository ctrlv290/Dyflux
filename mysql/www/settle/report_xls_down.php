<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자금일보 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 137;
//Init
include_once "../_init_.php";

$C_Bank = new Bank();
$C_Loan = new Loan();
$C_Report = new Report();

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["XLS_REPORT"] = "Y";


$period = $_GET["period"];
$periodAry = array("day", "week", "month");
if(!in_array($period, $periodAry)){
	$period = $periodAry[0];
}

$tran_date = $_GET["date"];

if($period == "week"){
	$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
}

//국내계좌
$bank_type = "DOMESTIC";
if($period == "month"){
	$tran_date = $_GET["date_year"] . "-" . make2digit($_GET["date_month"]) . "-01";
	$tran_date = date('Y-m-t', strtotime($tran_date));
}
$_list = $C_Bank->getTodayBankTransactionDetail($period, $tran_date, $bank_type);


require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();

$i = 1;

//국내계좌
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "예금 및 현금 입출금 현황 - 국내계좌", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

$i++;
//Header ????
$xls_header = array(
	array(
		"header_name" => "예금기관명",
		"field_name" => "bank_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "",
		"field_name" => "",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "전월이월",
		"field_name" => "prev_sum",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "입금",
		"field_name" => "tran_in",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "출금",
		"field_name" => "tran_out",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "금일잔액",
		"field_name" => "today_sum",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "비고",
		"field_name" => "tran_memo",
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
$activesheet->fromArray($header_ary, NULL, 'A'.$i);
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

$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);

foreach($xls_header as $key => $hh)
{
	$columnID = getNameFromNumber($key);
	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}


$i++;

/*
 * Total Calculate
 */
$prev_sum = 0;
$in_sum = 0;
$out_sum = 0;
$remain_sum = 0;
foreach($_list as $row_num => $row) {

	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$prev = $row["prev_sum"];
	$in_amt = $row["tran_in"];
	$out_amt = $row["tran_out"];
	$sum_amt = $row["tran_sum"];
	$today_sum = (($prev * 100) + ($sum_amt * 100)) / 100;

	$prev_sum += $prev;
	$in_sum += $in_amt;
	$out_sum += $out_amt;
	$remain_sum += $today_sum;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	$isTotal = false;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "today_sum"){
			$cellValue = $today_sum;
		}elseif($field_name == "bank_name"){
			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
			$activesheet->mergeCells($mergeCodRange);
		}

		if($data_type == "money") {
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
		}elseif($data_type == "number"){
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		}elseif($data_type == "image") {

		}else{
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
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

$last_row                      = end($_list);
$settle_sale_supply            = $last_row["settle_sale_supply"];
$settle_sale_supply_ex_vat     = $last_row["settle_sale_supply_ex_vat"];
$settle_purchase_supply        = $last_row["settle_purchase_supply"];
$settle_purchase_supply_ex_vat = $last_row["settle_purchase_supply_ex_vat"];
$settle_sale_profit            = $last_row["settle_sale_profit"];


$cod = getNameFromNumber(2) . $i;
$activesheet->setCellValueExplicit($cod, $prev_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(3) . $i;
$activesheet->setCellValueExplicit($cod, $in_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(4) . $i;
$activesheet->setCellValueExplicit($cod, $out_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(5) . $i;
$activesheet->setCellValueExplicit($cod, $remain_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(6) . $i;
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

//외환계좌
$bank_type = "FOREIGN";
$tran_date = $_GET["date"];
if($period == "month"){
	$tran_date = $_GET["date_year"] . "-" . make2digit($_GET["date_month"]) . "-01";
	$tran_date = date('Y-m-t', strtotime($tran_date));
}
$_list = $C_Bank->getTodayBankTransactionDetail($period, $tran_date, $bank_type);

$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "예금 및 현금 입출금 현황 - 외환계좌", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

$i++;
//Header ????
$xls_header = array(
	array(
		"header_name" => "예금기관명",
		"field_name" => "bank_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "",
		"field_name" => "",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "전월이월",
		"field_name" => "prev_sum",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "입금",
		"field_name" => "tran_in",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "출금",
		"field_name" => "tran_out",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "금일잔액",
		"field_name" => "today_sum",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "비고",
		"field_name" => "tran_memo",
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
$activesheet->fromArray($header_ary, NULL, 'A'.$i);
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

$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);

$i++;

/*
 * Total Calculate
 */
$prev_sum = 0;
$in_sum = 0;
$out_sum = 0;
$remain_sum = 0;
foreach($_list as $row_num => $row) {

	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$prev = $row["prev_sum"];
	$in_amt = $row["tran_in"];
	$out_amt = $row["tran_out"];
	$sum_amt = $row["tran_sum"];
	$today_sum = (($prev * 100) + ($sum_amt * 100)) / 100;

	$prev_sum += $prev;
	$in_sum += $in_amt;
	$out_sum += $out_amt;
	$remain_sum += $today_sum;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	$isTotal = false;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "today_sum"){
			$cellValue = $today_sum;
		}elseif($field_name == "bank_name"){
			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
			$activesheet->mergeCells($mergeCodRange);
		}

		if($data_type == "money") {
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
		}elseif($data_type == "number"){
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0.00');
		}elseif($data_type == "image") {

		}else{
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
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

$last_row                      = end($_list);
$settle_sale_supply            = $last_row["settle_sale_supply"];
$settle_sale_supply_ex_vat     = $last_row["settle_sale_supply_ex_vat"];
$settle_purchase_supply        = $last_row["settle_purchase_supply"];
$settle_purchase_supply_ex_vat = $last_row["settle_purchase_supply_ex_vat"];
$settle_sale_profit            = $last_row["settle_sale_profit"];


$cod = getNameFromNumber(2) . $i;
$activesheet->setCellValueExplicit($cod, $prev_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0.00');

$cod = getNameFromNumber(3) . $i;
$activesheet->setCellValueExplicit($cod, $in_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0.00');

$cod = getNameFromNumber(4) . $i;
$activesheet->setCellValueExplicit($cod, $out_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0.00');

$cod = getNameFromNumber(5) . $i;
$activesheet->setCellValueExplicit($cod, $remain_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0.00');

$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(6) . $i;
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

//각 내역들 시작

$list_call_ary = array(
	array("tran_type" => "CASH_CUSTOMER_IN", "tran_inout" => "IN", "title" => "현금출납내역 - 수입(거래처별)"),
	array("tran_type" => "CASH_CUSTOMER_OUT", "tran_inout" => "OUT", "title" => "현금출납내역 - 지출(거래처별)"),
	array("tran_type" => "CASH_IN", "tran_inout" => "IN", "title" => "현금출납내역 - 수입"),
	array("tran_type" => "CASH_OUT", "tran_inout" => "OUT", "title" => "현금출납내역 - 지출"),
	array("tran_type" => "BANK_CUSTOMER_IN", "tran_inout" => "IN", "title" => "통장 입출금 내역 - 수입(거래처별)"),
	array("tran_type" => "BANK_CUSTOMER_OUT", "tran_inout" => "OUT", "title" => "통장 입출금 내역 - 지출(거래처별)"),
	array("tran_type" => "BANK_ETC_IN", "tran_inout" => "IN", "title" => "통장 입출금 내역 - 수입(기타)"),
	array("tran_type" => "BANK_ETC_OUT", "tran_inout" => "OUT", "title" => "통장 입출금 내역 - 지출(기타)"),
	array("tran_type" => "TRANSFER_IN", "tran_inout" => "IN", "title" => "계좌간이체 - 수입"),
	array("tran_type" => "TRANSFER_OUT", "tran_inout" => "OUT", "title" => "계좌간이체 - 지출"),
	array("tran_type" => "CARD_OUT", "tran_inout" => "OUT", "title" => "카드 사용내역"),
);

foreach($list_call_ary as $ll) {

	$tran_type = $ll["tran_type"];
	$tran_inout = $ll["tran_inout"];

	$tran_inout_text = ($tran_inout == "IN") ? "입금액" : "출금액";

	$tran_date = $_GET["date"];
	if ($period == "month") {
		$tran_date = $_GET["date_year"] . "-" . make2digit($_GET["date_month"]) . "-01";
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	$_list = $C_Report->getReportDataByDate($tran_date, $tran_type, $tran_inout, $period);

	//카드사용내역일 경우 월, 년 합계 구하기
	if ($tran_type == "CARD_OUT") {
		$_month_sum = $C_Report->getReportDataByMonth($tran_date, $tran_type, $tran_inout);
	}

	//상단 타이틀
	$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(2) . $i;
	$activesheet->mergeCells($mergeCodRange);
	$cod = getNameFromNumber(0) . $i;
	$activesheet->setCellValueExplicit($cod, $ll["title"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$i++;


	$xls_header = array(
		array(
			"header_name" => "계정과목",
			"field_name" => "account_name",
			"width" => 30,
			"halign" => "left",
		),
		array(
			"header_name" => "거래처",
			"field_name" => "target_name",
			"width" => 30,
		),
		array(
			"header_name" => $tran_inout_text,
			"field_name" => "tran_amount",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "적요",
			"field_name" => "tran_memo",
			"width" => 20,
			"halign" => "left",
		),
		array(
			"header_name" => "적요",
			"field_name" => "",
			"width" => 20,
			"halign" => "left",
		),
	);

	$xls_header_card = array(
		array(
			"header_name" => "사용자",
			"field_name" => "tran_user",
		),
		array(
			"header_name" => "카드번호",
			"field_name" => "tran_card_no",
		),
		array(
			"header_name" => "지출처",
			"field_name" => "tran_purpose",
		),
		array(
			"header_name" => "계정과목",
			"field_name" => "account_name",
			"halign" => "left",
		),
		array(
			"header_name" => "지출금액",
			"field_name" => "tran_amount",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "적요",
			"field_name" => "tran_memo",
			"halign" => "left",
		),
	);

	if($tran_type == "CARD_OUT"){
		$xls_header = $xls_header_card;
	}

	$xls_header_end = getNameFromNumber(count($xls_header)-1);
	$header_ary = array();
	foreach($xls_header as $hh)
	{
		$header_ary[] = $hh["header_name"];
	}
	$activesheet->fromArray($header_ary, NULL, 'A'.$i);
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

	//계정과목 merge
	if($tran_type != "BANK_CUSTOMER_IN" && $tran_type != "BANK_CUSTOMER_OUT" && $tran_type != "CARD_OUT" && $tran_type != "CASH_CUSTOMER_IN" && $tran_type != "CASH_CUSTOMER_OUT") {
		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
		$activesheet->mergeCells($mergeCodRange);
	}

	//적요 merge
	if($tran_type != "CARD_OUT") {
		$mergeCodRange = getNameFromNumber(3) . $i . ":" . getNameFromNumber(4) . $i;
		$activesheet->mergeCells($mergeCodRange);
	}

	$i++;

	/*
	 * Total Calculate
	 */
	$tran_sum = 0;
	foreach($_list as $row_num => $row) {

		$xls_row = array();
		foreach ($xls_header as $key => $val) {
			$xls_row[] = $row[$val["field_name"]];
		}
		if($row["tran_type"] == 'BANK_CUSTOMER_IN' || 'BANK_CUSTOMER_OUT' || 'CASH_CUSTOMER_IN' || 'CASH_CUSTOMER_OUT' && $xls_row[4] < 0){
			$xls_row[4] = abs($xls_row[4]);
			$row["tran_amount"] = abs($row["tran_amount"]);
		}

		$tran_amount = $row["tran_amount"];
		$tran_sum += $tran_amount;

		//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
		$currentColumn = 0;
		$isTotal = false;
		foreach ($xls_row as $cellValue) {
			$field_name = $xls_header[$currentColumn]["field_name"];
			$cod = getNameFromNumber($currentColumn) . $i;
			$data_type = $xls_header[$currentColumn]["data_type"];
			$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
			$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

			if($field_name == "today_sum"){
				$cellValue = $today_sum;
			}elseif($field_name == "bank_name"){
				$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
				$activesheet->mergeCells($mergeCodRange);
			}

			if($data_type == "money") {
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
			}elseif($data_type == "number"){
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
			}elseif($data_type == "image") {

			}else{
				//강제 텍스트 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			}

			$activesheet->getStyle($cod)->getAlignment()->setHorizontal($halign);
			$activesheet->getStyle($cod)->getAlignment()->setVertical($halign);

			++$currentColumn;
		}

		if($tran_type != "BANK_CUSTOMER_IN" && $tran_type != "BANK_CUSTOMER_OUT" && $tran_type != "CARD_OUT" && $tran_type != "CASH_CUSTOMER_IN" && $tran_type != "CASH_CUSTOMER_OUT") {
			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
			$activesheet->mergeCells($mergeCodRange);
		}

		if($tran_type != "CARD_OUT") {
			$mergeCodRange = getNameFromNumber(3) . $i . ":" . getNameFromNumber(4) . $i;
			$activesheet->mergeCells($mergeCodRange);
			$activesheet->getStyle(getNameFromNumber(3) . $i)->getAlignment()->setHorizontal("left");
		}
		$activesheet->getStyle(getNameFromNumber(0) . $i)->getAlignment()->setHorizontal("left");
		$activesheet->getStyle(getNameFromNumber(1) . $i)->getAlignment()->setHorizontal("left");
		$i++;
	}

	//합계
	if($tran_type != "CARD_OUT") {
		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
		$activesheet->mergeCells($mergeCodRange);
		$cod = getNameFromNumber(0) . $i;
		$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		$cod = getNameFromNumber(2) . $i;
		$activesheet->setCellValueExplicit($cod, $tran_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

		$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(4) . $i;
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
	}else{
		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(3) . $i;
		$activesheet->mergeCells($mergeCodRange);
		$cod = getNameFromNumber(0) . $i;
		$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		$cod = getNameFromNumber(4) . $i;
		$activesheet->setCellValueExplicit($cod, $tran_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
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

		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(2) . $i;
		$activesheet->mergeCells($mergeCodRange);
		$cod = getNameFromNumber(0) . $i;
		$activesheet->setCellValueExplicit($cod, "월 합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		$cod = getNameFromNumber(3) . $i;
		$activesheet->setCellValueExplicit($cod, $_month_sum["month"]["text"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

		$cod = getNameFromNumber(4) . $i;
		$activesheet->setCellValueExplicit($cod, $_month_sum["month"]["sum"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
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

		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(2) . $i;
		$activesheet->mergeCells($mergeCodRange);
		$cod = getNameFromNumber(0) . $i;
		$activesheet->setCellValueExplicit($cod, "년 합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		$cod = getNameFromNumber(3) . $i;
		$activesheet->setCellValueExplicit($cod, $_month_sum["year"]["text"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

		$cod = getNameFromNumber(4) . $i;
		$activesheet->setCellValueExplicit($cod, $_month_sum["year"]["sum"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
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
	}


	$i++;
	$i++;

}

//차입계좌 시작
//국내계좌
$bank_type = "DOMESTIC";
if($period == "month"){
	$tran_date = $_GET["date_year"] . "-" . make2digit($_GET["date_month"]) . "-01";
	$tran_date = date('Y-m-t', strtotime($tran_date));
}
$_list = $C_Loan->getTodayLoanTransactionDetail($period, $tran_date);

$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "차입금계좌", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

$i++;
//Header ????
$xls_header = array(
	array(
		"header_name" => "계좌명",
		"field_name" => "loan_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "만기일/상환일정",
		"field_name" => "loan_detail",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "대출액",
		"field_name" => "loan_amount",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "전일잔액",
		"field_name" => "yesterday_remain",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "상환액",
		"field_name" => "tran_in",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "금일잔액",
		"field_name" => "today_remain",
		"width" => 26,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "비고",
		"field_name" => "tran_memo",
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
$activesheet->fromArray($header_ary, NULL, 'A'.$i);
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

foreach($xls_header as $key => $hh)
{
	$columnID = getNameFromNumber($key);
	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}


$i++;

/*
 * Total Calculate
 */
$prev_sum = 0;
$in_sum = 0;
$out_sum = 0;
$remain_sum = 0;
$yesterday_remain = 0;
$today_remain = 0;
$loan_amount_sum = 0;
foreach($_list as $row_num => $row) {

	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$prev = $row["prev_sum"];
	$in_amt = $row["tran_in"];
	$out_amt = $row["tran_out"];
	$sum_amt = $row["tran_sum"];
	$today_sum = (($prev * 100) + ($sum_amt * 100)) / 100;

	$loan_amount_sum += $row["loan_amount"];
	$yesterday_remain += $row["yesterday_remain"];
	$today_remain += $row["today_remain"];

	$prev_sum += $prev;
	$in_sum += $in_amt;
	$out_sum += $out_amt;
	$remain_sum += $today_sum;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	$isTotal = false;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "today_sum"){
			$cellValue = $today_sum;
		}elseif($field_name == "bank_name"){
			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
			$activesheet->mergeCells($mergeCodRange);
		}

		if($data_type == "money") {
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
		}elseif($data_type == "number"){
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		}elseif($data_type == "image") {

		}else{
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
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

$cod = getNameFromNumber(2) . $i;
$activesheet->setCellValueExplicit($cod, $loan_amount_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(3) . $i;
$activesheet->setCellValueExplicit($cod, $yesterday_remain, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(4) . $i;
$activesheet->setCellValueExplicit($cod, $in_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(5) . $i;
$activesheet->setCellValueExplicit($cod, $today_remain, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(6) . $i;
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


//-차입계좌 끝

//계정과목별 시작

//각 내역들 시작

$list_call_ary = array(
	array("tran_type" => "", "tran_inout" => "IN", "title" => "계정과목별 집계 - 수입"),
	array("tran_type" => "", "tran_inout" => "OUT", "title" => "계정과목별 집계 - 지출"),
);

foreach($list_call_ary as $ll) {

	$tran_type = $ll["tran_type"];
	$tran_inout = $ll["tran_inout"];

	$tran_inout_text = ($tran_inout == "IN") ? "입금액" : "출금액";

	$tran_date = $_GET["date"];
	if ($period == "month") {
		$tran_date = $_GET["date_year"] . "-" . make2digit($_GET["date_month"]) . "-01";
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	//$_list = $C_Report->getReportSumDataByAccount($tran_date, $tran_inout, $period);
	$rst = $C_Report->getReportSumDataByAccount($tran_date, $tran_inout, $period);
	$_list = $rst["list"];
	$_cash_sum = $rst["cash_sum"];

	//상단 타이틀
	$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(2) . $i;
	$activesheet->mergeCells($mergeCodRange);
	$cod = getNameFromNumber(0) . $i;
	$activesheet->setCellValueExplicit($cod, $ll["title"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$i++;


	$xls_header = array(
		array(
			"header_name" => "계정과목",
			"field_name" => "account_name",
			"width" => 30,
			"halign" => "left",
		),
		array(
			"header_name" => "",
			"field_name" => "",
			"width" => 30,
			"halign" => "left",
		),
		array(
			"header_name" => "금액",
			"field_name" => "tran_amount",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
	);

	$xls_header_end = getNameFromNumber(count($xls_header)-1);
	$header_ary = array();
	foreach($xls_header as $hh)
	{
		$header_ary[] = $hh["header_name"];
	}
	$activesheet->fromArray($header_ary, NULL, 'A'.$i);
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

	//계정과목 merge
	if($tran_type != "BANK_CUSTOMER_IN" && $tran_type != "BANK_CUSTOMER_OUT" && $tran_type != "CARD_OUT" && $tran_type != "CASH_CUSTOMER_IN" && $tran_type != "CASH_CUSTOMER_OUT") {
		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
		$activesheet->mergeCells($mergeCodRange);
	}

	$i++;

	/*
	 * Total Calculate
	 */
	$tran_sum = 0;
	foreach($_list as $row_num => $row) {

		$xls_row = array();
		foreach ($xls_header as $key => $val) {
			$xls_row[] = $row[$val["field_name"]];
		}

		$tran_amount = $row["tran_amount"];

		$tran_sum += $tran_amount;

		//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
		$currentColumn = 0;
		$isTotal = false;
		foreach ($xls_row as $cellValue) {
			$field_name = $xls_header[$currentColumn]["field_name"];
			$cod = getNameFromNumber($currentColumn) . $i;
			$data_type = $xls_header[$currentColumn]["data_type"];
			$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
			$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

			if($field_name == "today_sum"){
				$cellValue = $today_sum;
			}elseif($field_name == "bank_name"){
				$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
				$activesheet->mergeCells($mergeCodRange);
			}

			if($data_type == "money") {
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
			}elseif($data_type == "number"){
				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
			}elseif($data_type == "image") {

			}else{
				//강제 텍스트 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			}

			$activesheet->getStyle($cod)->getAlignment()->setHorizontal($halign);
			$activesheet->getStyle($cod)->getAlignment()->setVertical($halign);

			++$currentColumn;
		}

		if($tran_type != "BANK_CUSTOMER_IN" && $tran_type != "BANK_CUSTOMER_OUT" && $tran_type != "CARD_OUT" && $tran_type != "CASH_CUSTOMER_IN" && $tran_type != "CASH_CUSTOMER_OUT") {
			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
			$activesheet->mergeCells($mergeCodRange);
		}

		$i++;
	}

	//합계
	$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(1) . $i;
	$activesheet->mergeCells($mergeCodRange);
	$cod = getNameFromNumber(0) . $i;
	$activesheet->setCellValueExplicit($cod, "합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

	$cod = getNameFromNumber(2) . $i;
	$activesheet->setCellValueExplicit($cod, $tran_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
	$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

	$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(2) . $i;
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
$_SESSION["XLS_REPORT"] = "";

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
$user_filename = "자금일보.xlsx";
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
