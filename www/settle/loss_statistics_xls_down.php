<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산예정금 통계 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 270;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["LOASS_STATISTICS"] = "Y";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];

$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_start && $date_end && $seller_idx){
	$_list = $C_Settle -> getLossStatistics($seller_idx, $date_start, $date_end);
}


require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();

//Header ????
$xls_header = array(
	array(
		"header_name" => "입금예정일",
		"field_name" => "settle_date",
		"width" => 30,
	),
	array(
		"header_name" => "수집정보 합계\n(판매가+배송비, 수수료 제외)",
		"field_name" => "settle_sum",
		"width" => 40,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "사이트 합계\n(판매가+배송비, 수수료 제외)",
		"field_name" => "site_sum",
		"width" => 40,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "공제/환급액 등",
		"field_name" => "commission_etc",
		"width" => 40,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "실입금액 합계",
		"field_name" => "tran_amount",
		"width" => 40,
		"data_type" => "number",
		"halign" => "right",
	),
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
$header_ary_euckr = array();
foreach($xls_header as $hh)
{
	$header_ary[] = $hh["header_name"];
}
$activesheet->fromArray($header_ary, NULL, 'A1');
$activesheet->getStyle('A1:'.$xls_header_end.'1')->applyFromArray(
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


$activesheet->getRowDimension("1")->setRowHeight(30);
$activesheet->getStyle('A1:'.$xls_header_end.'1')->getAlignment()->setWrapText(true);

//List Apply
$i = 2;

/*
 * Total Calculate
 */
$settle_total     = 0;
$site_total       = 0;
$commission_total = 0;
$tran_total       = 0;
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$settle_date    = $row["settle_date"];

	if(!$settle_date) $settle_date = $row["loss_date"];
	if(!$settle_date) $settle_date = $row["tran_date"];

	$settle_sum     = $row["settle_sum"];
	$site_sum       = $row["site_sum"];
	$commission_etc = $row["commission_etc"];
	$tran_amount    = $row["tran_amount"];

	$settle_total     += $settle_sum;
	$site_total       += $site_sum;
	$commission_total += $commission_etc;
	$tran_total       += $tran_amount;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "settle_date"){
			$cellValue = $settle_date;
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

//합계 설정
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "합계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");
//수집
$cod = getNameFromNumber(1) . $i;
$activesheet->setCellValueExplicit($cod, $settle_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//사이트
$cod = getNameFromNumber(2) . $i;
$activesheet->setCellValueExplicit($cod, $site_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//공제
$cod = getNameFromNumber(3) . $i;
$activesheet->setCellValueExplicit($cod, $commission_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//실입금
$cod = getNameFromNumber(4) . $i;
$activesheet->setCellValueExplicit($cod, $tran_total, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
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

//foreach(range('A', $xls_header_end) as $columnID) {
//$activesheet->getColumnDimension($columnID)->setAutoSize(true);
//}

foreach($xls_header as $key => $hh)
{
	$columnID = getNameFromNumber($key);
	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}


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
$user_filename = "정산예정금_통계.xlsx";
if (is_ie()) $user_filename = utf2euc($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["LOASS_STATISTICS"] = "";

ob_end_clean();
?>
