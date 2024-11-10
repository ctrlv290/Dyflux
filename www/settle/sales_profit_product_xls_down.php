<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품별 매출이익 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 263;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["XLS_SALE_PROFIT_PRODUCT"] = "Y";

$date_start               = $_GET["date_start"];
$date_end                 = $_GET["date_end"];
$product_seller_group_idx = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx               = $_GET["seller_idx"];
$product_idx              = $_GET["product_idx"];

$C_Settle = new Settle();

if($date_start && $date_end){
	if(validateDate($date_start, 'Y-m-d') && validateDate($date_end, 'Y-m-d')) {
		$_list = $C_Settle->getSalesProfitByProduct($date_start, $date_end, $seller_idx, $product_idx);
	}
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
		"field_name" => "settle_date",
		"width" => 16,
	),
	array(
		"header_name" => "상품코드",
		"field_name" => "product_idx",
		"width" => 16,
	),
	array(
		"header_name" => "상품명",
		"field_name" => "product_name",
		"width" => 60,
		"halign" => "left",
	),

	array(
		"header_name" => "옵션코드",
		"field_name" => "product_idx",
		"width" => 16,
	),
	array(
		"header_name" => "옵션",
		"field_name" => "product_option_name",
		"width" => 60,
		"halign" => "left",
	),

	array(
		"header_name" => "판매수량",
		"field_name" => "product_option_cnt",
		"width" => 16,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출공급가액",
		"field_name" => "settle_sale_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출공급가액\n(부가세제외)",
		"field_name" => "settle_sale_supply_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출공급가액\n부가세",
		"field_name" => "settle_sale_supply_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출원가공급가액",
		"field_name" => "settle_purchase_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출원가공급가액\n(부가세제외)",
		"field_name" => "settle_purchase_supply_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출원가공급가액\n부가세",
		"field_name" => "settle_purchase_supply_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출이익",
		"field_name" => "settle_sale_profit",
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

$activesheet->getRowDimension("1")->setRowHeight(30);
$activesheet->getStyle('A1:'.$xls_header_end.'1')->getAlignment()->setWrapText(true);
//List Apply
$i = 2;

/*
 * Total Calculate
 */
$profit_sum = 0;
foreach($_list as $row_num => $row) {

	if($row_num > (count($_list) -2)){
		continue;
	}

	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$product_name = $row["product_name"];

	$settle_sale_supply = $row["settle_sale_supply"];
	$settle_sale_supply_ex_vat = $row["settle_sale_supply_ex_vat"];
	$settle_purchase_supply = $row["settle_purchase_supply"];
	$settle_purchase_supply_ex_vat = $row["settle_purchase_supply_ex_vat"];
	$settle_sale_profit            = $row["settle_sale_profit"];

	if($product_name != "") {
		$profit_sum += $settle_sale_profit;
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	$isTotal = false;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";



		if($field_name == "settle_sale_supply_vat"){
			$cellValue = $settle_sale_supply - $settle_sale_supply_ex_vat;
		}elseif($field_name == "settle_purchase_supply_vat"){
			$cellValue = $settle_purchase_supply - $settle_purchase_supply_ex_vat;
		}elseif($field_name == "settle_date"){
			if($product_name == ""){
				$isTotal = true;
				$cellValue = "합계";
				$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(3) . $i;
				$activesheet->mergeCells($mergeCodRange);
				$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(12) . $i;
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

	if($isTotal){
		$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(11) . $i;
		$activesheet->mergeCells($mergeCodRange);

		$cod = getNameFromNumber(0) . $i;
		$activesheet->setCellValueExplicit($cod, "누계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(12) . $i;
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

		$cod = getNameFromNumber(8) . $i;

		//통화 셀 서식 지정
		$activesheet->setCellValueExplicit($cod, $profit_sum, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

		$i++;
	}
}


$i++;
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
$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(4) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

$activesheet->getRowDimension($i)->setRowHeight(30);
$activesheet->getStyle('A'.$i.':'.$xls_header_end.$i)->getAlignment()->setWrapText(true);

$i++;
$totalRowRangeCod = getNameFromNumber(0) . $i . ":" . getNameFromNumber(12) . $i;
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


$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(4) . $i;
$activesheet->mergeCells($mergeCodRange);
$cod = getNameFromNumber(0) . $i;
$activesheet->setCellValueExplicit($cod, "월 합 계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

$last_row                      = end($_list);
$product_option_cnt            = $last_row["product_option_cnt"];
$settle_sale_supply            = $last_row["settle_sale_supply"];
$settle_sale_supply_ex_vat     = $last_row["settle_sale_supply_ex_vat"];
$settle_purchase_supply        = $last_row["settle_purchase_supply"];
$settle_purchase_supply_ex_vat = $last_row["settle_purchase_supply_ex_vat"];
$settle_sale_profit            = $last_row["settle_sale_profit"];

$cod = getNameFromNumber(5) . $i;
$activesheet->setCellValueExplicit($cod, $product_option_cnt, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(6) . $i;
$activesheet->setCellValueExplicit($cod, $settle_sale_supply, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(7) . $i;
$activesheet->setCellValueExplicit($cod, $settle_sale_supply_ex_vat, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(8) . $i;
$activesheet->setCellValueExplicit($cod, $settle_sale_supply - $settle_sale_supply_ex_vat, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(9) . $i;
$activesheet->setCellValueExplicit($cod, $settle_purchase_supply, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(10) . $i;
$activesheet->setCellValueExplicit($cod, $settle_purchase_supply_ex_vat, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(11) . $i;
$activesheet->setCellValueExplicit($cod, $settle_purchase_supply - $settle_purchase_supply_ex_vat, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');

$cod = getNameFromNumber(12) . $i;
$activesheet->setCellValueExplicit($cod, $settle_sale_profit, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');


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
$user_filename = "상품별매출이익.xlsx";
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
$_SESSION["XLS_SALE_PROFIT_PRODUCT"] = "";

ob_end_clean();
?>
