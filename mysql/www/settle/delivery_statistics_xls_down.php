<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 배송통계 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 144;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["DELIVERY_STATISTICS"] = "Y";

$C_Delivery = new Delivery();
$_delivery_list = $C_Delivery->getDeliveryCodeList();

$period_type                = $_GET["period_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_seller_group_idx   = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx                 = $_GET["seller_idx"];
$product_supplier_group_idx = (isset($_GET["product_supplier_group_idx"])) ? $_GET["product_supplier_group_idx"] : "0";
$supplier_idx               = $_GET["supplier_idx"];
$delivery_code              = (isset($_GET["product_supplier_group_idx"])) ? $_GET["delivery_code"] : "CJGLS";

$C_Settle = new Settle();

if($date_start && $date_end){

	$_list = $C_Settle->getDeliveryStatistics($period_type, $date_start, $date_end, $delivery_code, $seller_idx, $supplier_idx);

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
		"header_name" => "일자",
		"field_name" => "date",
		"width" => 16,
	),
	array(
		"header_name" => "선불",
		"field_name" => "prepay_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "착불",
		"field_name" => "afterpay_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "합포",
		"field_name" => "pack_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "개별",
		"field_name" => "single_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "합계",
		"field_name" => "sum_cancel_change_cnt",
		"width" => 20,
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


//List Apply
$i = 2;

/*
 * Total Calculate
 */
$prepay_sum = 0;
$afterpay_sum = 0;
$pack_sum = 0;
$single_sum = 0;
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$prepay_cnt = $row["prepay_cnt"];
	$afterpay_cnt = $row["afterpay_cnt"];
	$pack_cnt = $row["pack_cnt"];
	$single_cnt = $row["single_cnt"];

	$row_sum = $pack_cnt + $single_cnt;

	$prepay_sum   += $prepay_cnt;
	$afterpay_sum += $afterpay_cnt;
	$pack_sum     += $pack_cnt;
	$single_sum   += $single_cnt;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "sum_cancel_change_cnt"){
			$cellValue = $row_sum;
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
//선불
$cod = getNameFromNumber(1) . $i;
$activesheet->setCellValueExplicit($cod, $prepay_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//착불
$cod = getNameFromNumber(2) . $i;
$activesheet->setCellValueExplicit($cod, $afterpay_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//합포
$cod = getNameFromNumber(3) . $i;
$activesheet->setCellValueExplicit($cod, $pack_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//개별
$cod = getNameFromNumber(4) . $i;
$activesheet->setCellValueExplicit($cod, $single_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//합계
$cod = getNameFromNumber(5) . $i;
$activesheet->setCellValueExplicit($cod, $pack_sum+$single_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
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
$user_filename = "배송통계.xlsx";
//if (is_ie()) $user_filename = utf2euc($user_filename);
if (is_ie()) $user_filename = urlencode($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["DELIVERY_STATISTICS"] = "";

ob_end_clean();
?>
