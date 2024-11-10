<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별통계 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 127;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["SELLER_SALE"] = "Y";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_start && $date_end){

	$_list = $C_Settle -> getSellerSaleStatistics($period_type, $date_start, $date_end, $product_seller_group_idx, $seller_idx, $search_column, $search_keyword);

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
		"header_name" => "판매처명",
		"field_name" => "seller_name",
		"width" => 30,
	),
	array(
		"header_name" => "주문수량",
		"field_name" => "order_count",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "상품수량",
		"field_name" => "sum_product_option_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "취소주문",
		"field_name" => "order_cancel_count",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "취소상품수",
		"field_name" => "sum_cancel_product_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "교환상품수",
		"field_name" => "sum_cancel_change_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "주문-취소주문",
		"field_name" => "remain_order_count",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "상품-취소수량",
		"field_name" => "remain_product_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "판매금",
		"field_name" => "sum_settle_sale_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "취소금액",
		"field_name" => "sum_settle_sale_supply_cancel",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "실매출금액",
		"field_name" => "remain_sale",
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
$total_settle_sale_supply = 0;
$total_settle_sale_supply_cancel = 0;
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	//$xls_row[] = $row["inner_no"];
	//$xls_row[] = $row["inner_no2"];
	//echo $row["inner_no2"] . "<br>";
	$order_count = $row["order_count"];
	$order_cancel_count = $row["order_cancel_count"];
	$sum_product_option_cnt = $row["sum_product_option_cnt"];
	$sum_cancel_product_cnt = $row["sum_cancel_product_cnt"];
	$sum_settle_sale_supply = $row["sum_settle_sale_supply"];
	$sum_settle_sale_supply_cancel = $row["sum_settle_sale_supply_cancel"];

	$total_settle_sale_supply += $sum_settle_sale_supply;
	$total_settle_sale_supply_cancel += $sum_settle_sale_supply_cancel;

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "remain_order_count"){
			$cellValue = $order_count - $order_cancel_count;
		}elseif($field_name == "remain_product_cnt"){
			$cellValue = $sum_product_option_cnt - $sum_cancel_product_cnt;
		}elseif($field_name == "remain_sale"){
			$cellValue = $sum_settle_sale_supply - $sum_settle_sale_supply_cancel;
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
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(7) . $i;
$activesheet->setCellValueExplicit($cod, "합계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");
$activesheet->mergeCells($mergeCodRange);
//판매금액
$cod = getNameFromNumber(8) . $i;
$activesheet->setCellValueExplicit($cod, $total_settle_sale_supply, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//취소금액
$cod = getNameFromNumber(9) . $i;
$activesheet->setCellValueExplicit($cod, $total_settle_sale_supply_cancel, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
//실매출금액
$cod = getNameFromNumber(10) . $i;
$activesheet->setCellValueExplicit($cod, $total_settle_sale_supply-$total_settle_sale_supply_cancel, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');


$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(10) . $i;
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
$user_filename = "판매처별통계.xlsx";
if (is_ie()) $user_filename = utf2euc($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["SELLER_SALE"] = "";

ob_end_clean();
?>
