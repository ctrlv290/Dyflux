<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 충전금관리 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 138;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["XLS_VENDOR_CHARGE"] = "Y";

//기존 Grid List 불러오기 및 엑셀 출력용 변수 설정
$gridPrintForExcelDownload = true;
require "./vendor_charge_grid.php";

$_list = $listRst;

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

//$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//$writer->save("????_???.xlsx");

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();


//Header ????
$xls_header = array(

	array(
		"header_name" => "벤더사",
		"field_name" => "vendor_name",
		"width" => 30,
	),
	array(
		"header_name" => "벤더사 코드",
		"field_name" => "member_idx",
		"width" => 22,
	),
	array(
		"header_name" => "벤더사 등급",
		"field_name" => "vendor_grade",
		"width" => 22,
	),
	array(
		"header_name" => "마지막 입금일",
		"field_name" => "last_charge_date",
		"width" => 24,
	),
	array(
		"header_name" => "충전금 잔액",
		"field_name" => "remain_amount2",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "마지막 입금 비고",
		"field_name" => "last_memo",
		"width" => 40,
		"halign" => "left",
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
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

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
$user_filename = "충전금관리.xlsx";
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
$_SESSION["XLS_VENDOR_CHARGE"] = "";

ob_end_clean();
?>