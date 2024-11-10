<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 월별판매처통계 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 128;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["SELLER_MONTHLY"] = "Y";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_year                  = $_GET["date_year"];
$monthly_type              = $_GET["monthly_type"];

$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_year){
	$date_year_prev = (int) $date_year - 1;
	if($monthly_type == "settle_sale_supply"){
		$_list = $C_Settle->getSellerMonthlyStatistics($date_year, $seller_idx);
	}else {

		$_list = $C_Settle->getSellerMonthlyOrderCntStatistics($date_year, $seller_idx);
	}
}

$C_Settle = "";

//합계 추가
$sumAry = array();
foreach($_list as $row){
	for ($g = 0;$g < 13;$g++){
		$col = $date_year_prev . "-" . $g;
		$val = 0;
		if(!isset($sumAry[$col])){
			$sumAry[$col] = 0;
		}
		$sumAry[$col] += $row[$col];
	}

	for ($g = 0;$g < 13;$g++){
		$col = $date_year . "-" . $g;
		$val = 0;
		if(!isset($sumAry[$col])){
			$sumAry[$col] = 0;
		}
		$sumAry[$col] += $row[$col];
	}
}
$sumAry["seller_name"] = "합계";

array_unshift($_list, $sumAry);

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
		"header_name" => "판매처",
		"field_name" => "seller_name",
		"width" => 30,
	),
	array(
		"header_name" => "년도",
		"field_name" => "year",
		"width" => 20,
		"data_type" => "text",
		"halign" => "center",
	),
);

for($i=0;$i<13;$i++) {
	$hname = ($i == 0) ? "합계" : $i."월";
	$xls_header[] = array(
		"header_name" => $hname,
		"field_name" => $i,
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	);
}

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

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);

	for($q=0;$q<2;$q++) {
		$currentColumn = 0;

		$year_text = "";
		if($q==0){
			$year_text = $date_year;

			$mergeCodRange = getNameFromNumber(0) . $i . ":" . getNameFromNumber(0) . ($i+1);
			$activesheet->mergeCells($mergeCodRange);
		}else{
			$year_text = $date_year_prev;
		}

		$cod = getNameFromNumber(1) . $i;
		$activesheet->setCellValueExplicit($cod, $year_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

		foreach ($xls_row as $cellValue) {
			$field_name = $xls_header[$currentColumn]["field_name"];
			$cod        = getNameFromNumber($currentColumn) . $i;
			$data_type  = $xls_header[$currentColumn]["data_type"];
			$halign     = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
			$valign     = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

			if($currentColumn == 1){
				$currentColumn++;
				continue;
			}

			if ($currentColumn < 2) {
			} else {
				$cellValue = $row[$year_text . "-" . $field_name];
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
}

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
$user_filename = "월별판매처통계.xlsx";
if (is_ie()) $user_filename = utf2euc($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["SELLER_MONTHLY"] = "";

ob_end_clean();
?>
