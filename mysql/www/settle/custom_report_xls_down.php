<?php
include_once "../_init_.php";
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control:no-cache");
header("Pragma:no-cache");

$crm = new CustomReportManager();

ob_start();

$_SESSION["XLS_CUSTOM_REPORT"] = "Y";

$data = $_GET;
parse_str($data["param"][0], $output);
unset($output["user_template"]);
unset($output["user_template_name"]);
foreach ($output as &$out){
	if(is_array($out) == 1){
		$out = implode( ',', $out);
	}
}

$report = $crm->createReport($output);

$_list = $report;

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;



$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();

$xls_header = array();
foreach ($_list[0] as $key => $value){
    array_push($xls_header,
        array(
        "header_name" => $key,
        "field_name" => $key,
        "width" => 22,
        )
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

$i = 2;
foreach($_list as $row_num => $row) {
    $xls_row = array();
    foreach ($xls_header as $key => $val) {
        $xls_row[] = $row[$val["field_name"]];
    }
    $currentColumn = 0;
    foreach ($xls_row as $cellValue) {
        $field_name = $xls_header[$currentColumn]["field_name"];
        $cod = getNameFromNumber($currentColumn) . $i;

        if(preg_match("/^[+-]?\d*(\.?\d*)$/",$cellValue)){
            $activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        }else {
            $activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }
        ++$currentColumn;
    }

    $i++;
}

foreach($xls_header as $key => $hh)
{
    $columnID = getNameFromNumber($key);
    $activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}


$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
//$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);  /*----- Excel (Xls) Object*/
//$Excel_writer->setUseBOM(true);

function mb_basename($path) { return end(explode('/',$path)); }
function utf2euc($str) { return iconv("UTF-8","cp949//TRANSLIT", $str); }
function is_ie() {
    if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
    return false;
}
$user_filename = "사용자정의보고서.xlsx";

if (is_ie()) $user_filename = urlencode($user_filename);

if(is_ie()){
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["XLS_CUSTOM_REPORT"] = "";
ob_end_clean();

?>