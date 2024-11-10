<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입매출현황 [판매일보] 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 122;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["XLS_TRANSACTION_LIST"] = "Y";

//기존 Grid List 불러오기 및 엑셀 출력용 변수 설정
$gridPrintForExcelDownload = true;
require "./transaction_list_grid.php";

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
		"header_name" => "관리번호",
		"field_name" => "order_idx",
		"width" => 12,
	),
	array(
		"header_name" => $date_search_col_name_han,
		"field_name" => "search_date",
		"width" => 12,
	),
	array(
		"header_name" => "처리",
		"field_name" => "order_cs_status",
		"width" => 12,
	),
	array(
		"header_name" => "사유",
		"field_name" => "cs_reason_cancel_text",
		"width" => 19,
	),
	array(
		"header_name" => "마켓",
		"field_name" => "seller_name",
		"width" => 30,
	),
	array(
		"header_name" => "판매처 주문번호",
		"field_name" => "market_order_no",
		"width" => 30,
	),
	array(
		"header_name" => "판매처 상품코드",
		"field_name" => "market_product_no",
		"width" => 30,
	),
	array(
		"header_name" => "수취인",
		"field_name" => "receive_name",
		"width" => 22,
	),
	array(
		"header_name" => "전화번호",
		"field_name" => "receive_tp_num",
		"width" => 16,
	),
	array(
		"header_name" => "핸드폰",
		"field_name" => "receive_hp_num",
		"width" => 16,
	),
	array(
		"header_name" => "우편번호",
		"field_name" => "receive_zipcode",
		"width" => 12,
	),
	array(
		"header_name" => "주소",
		"field_name" => "receive_addr1",
		"width" => 80,
		"halign" => "left",
	),
	array(
		"header_name" => "배송메세지",
		"field_name" => "receive_memo",
		"width" => 60,
		"halign" => "left",
	),
	array(
		"header_name" => "상품명",
		"field_name" => "product_name",
		"width" => 60,
		"halign" => "left",
	),
	array(
		"header_name" => "옵션",
		"field_name" => "product_option_name",
		"width" => 60,
		"halign" => "left",
	),
	array(
		"header_name" => "상품세금종류",
		"field_name" => "product_tax_type",
		"width" => 12,
	),
	array(
		"header_name" => "판매수량",
		"field_name" => "product_option_cnt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "거래처",
		"field_name" => "supplier_name",
		"width" => 30,
	),
	array(
		"header_name" => "판매단가",
		"field_name" => "order_unit_price",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "판매가",
		"field_name" => "settle_sale_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "판매가-공급가액",
		"field_name" => "settle_sale_supply_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "판매수수료",
		"field_name" => "settle_sale_commission_in_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "판매수수료-공급가액",
		"field_name" => "settle_sale_commission_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출배송비",
		"field_name" => "settle_delivery_in_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출배송비-공급가액",
		"field_name" => "settle_delivery_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "배송비수수료",
		"field_name" => "settle_delivery_commission_in_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "배송비수수료-공급가액",
		"field_name" => "settle_delivery_commission_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출합계",
		"field_name" => "sale_sum",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매출합계-공급가액",
		"field_name" => "sale_sum_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입단가",
		"field_name" => "settle_purchase_unit_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입단가-공급가액",
		"field_name" => "settle_purchase_unit_supply_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입가",
		"field_name" => "settle_purchase_supply",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입가-공급가액",
		"field_name" => "settle_purchase_supply_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입배송비",
		"field_name" => "settle_purchase_delivery_in_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입배송비-공급가액",
		"field_name" => "settle_purchase_delivery_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입합계",
		"field_name" => "purchase_sum",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "매입합계-공급가액",
		"field_name" => "purchase_sum_ex_vat",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "정산/배송비",
		"field_name" => "settle_settle_amt",
		"width" => 20,
		"data_type" => "number",
		"halign" => "right",
	),
	array(
		"header_name" => "광고비",
		"field_name" => "settle_ad_amt",
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
	array(
		"header_name" => "메모",
		"field_name" => "settle_memo",
		"width" => 60,
		"halign" => "left",
	),
//	array(
//		"header_name" => "선불택배비",
//		"field_name" => "delivery_fee",
//		"width" => 20,
//		"data_type" => "number",
//		"halign" => "right",
//	),
);

if(!isDYLogin()){
	$xls_header = array(
		array(
			"header_name" => "관리번호",
			"field_name" => "order_idx",
			"width" => 12,
		),
		array(
			"header_name" => "처리",
			"field_name" => "order_cs_status",
			"width" => 12,
		),
		array(
			"header_name" => "사유",
			"field_name" => "cs_reason_cancel_text",
			"width" => 19,
		),
		array(
			"header_name" => "마켓",
			"field_name" => "seller_name",
			"width" => 30,
		),
		array(
			"header_name" => "수취인",
			"field_name" => "receive_name",
			"width" => 22,
		),
		array(
			"header_name" => "전화번호",
			"field_name" => "receive_tp_num",
			"width" => 16,
		),
		array(
			"header_name" => "핸드폰",
			"field_name" => "receive_hp_num",
			"width" => 16,
		),
		array(
			"header_name" => "우편번호",
			"field_name" => "receive_zipcode",
			"width" => 12,
		),
		array(
			"header_name" => "주소",
			"field_name" => "receive_addr1",
			"width" => 80,
			"halign" => "left",
		),
		array(
			"header_name" => "배송메세지",
			"field_name" => "receive_memo",
			"width" => 60,
			"halign" => "left",
		),
		array(
			"header_name" => "상품명",
			"field_name" => "product_name",
			"width" => 60,
			"halign" => "left",
		),
		array(
			"header_name" => "옵션",
			"field_name" => "product_option_name",
			"width" => 60,
			"halign" => "left",
		),
		array(
			"header_name" => "상품세금종류",
			"field_name" => "product_tax_type",
			"width" => 12,
		),
		array(
			"header_name" => "판매수량",
			"field_name" => "product_option_cnt",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "판매단가",
			"field_name" => "order_unit_price",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "판매가",
			"field_name" => "settle_sale_supply",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "판매가-공급가액",
			"field_name" => "settle_sale_supply_ex_vat",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "매출배송비",
			"field_name" => "settle_delivery_in_vat",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "매출배송비-공급가액",
			"field_name" => "settle_delivery_ex_vat",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "매출합계",
			"field_name" => "sale_sum",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
		array(
			"header_name" => "매출합계-공급가액",
			"field_name" => "sale_sum_ex_vat",
			"width" => 20,
			"data_type" => "number",
			"halign" => "right",
		),
//	array(
//		"header_name" => "선불택배비",
//		"field_name" => "delivery_fee",
//		"width" => 20,
//		"data_type" => "number",
//		"halign" => "right",
//	),
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
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$settle_type = $row["settle_type"];
	$addr2 = $row["receive_addr2"];

	//sum += Number(rowobject.settle_sale_supply) - Number(rowobject.settle_sale_commission_in_vat)
	// + Number(rowobject.settle_delivery_in_vat) - Number(rowobject.settle_delivery_commission_in_vat);
	$sale_sum = 0;
	$settle_sale_supply = $row["settle_sale_supply"];
	$settle_sale_commission_in_vat = $row["settle_sale_commission_in_vat"];
	$settle_delivery_in_vat = $row["settle_delivery_in_vat"];
	$settle_delivery_commission_in_vat = $row["settle_delivery_commission_in_vat"];

	//sum += Number(rowobject.settle_sale_supply_ex_vat) - Number(rowobject.settle_sale_commission_ex_vat)
	// + Number(rowobject.settle_delivery_ex_vat) - Number(rowobject.settle_delivery_commission_ex_vat);
	$sale_sum_ex_vat = 0;
	$settle_sale_supply_ex_vat = $row["settle_sale_supply_ex_vat"];
	$settle_sale_commission_ex_vat = $row["settle_sale_commission_ex_vat"];
	$settle_delivery_ex_vat = $row["settle_delivery_ex_vat"];
	$settle_delivery_commission_ex_vat = $row["settle_delivery_commission_ex_vat"];

	//sum += Number(rowobject.settle_purchase_supply) + Number(rowobject.settle_purchase_delivery_in_vat);
	$purchase_sum = 0;
	$settle_purchase_supply = $row["settle_purchase_supply"];
	$settle_purchase_delivery_in_vat = $row["settle_purchase_delivery_in_vat"];

	//sum += Number(rowobject.settle_purchase_supply_ex_vat) + Number(rowobject.settle_purchase_delivery_ex_vat);
	$purchase_sum_ex_vat = 0;
	$settle_purchase_supply_ex_vat = $row["settle_purchase_supply_ex_vat"];
	$settle_purchase_delivery_ex_vat = $row["settle_purchase_delivery_ex_vat"];

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($field_name == "order_idx" && $cellValue == "0") {
			$cellValue = "";
		}elseif($field_name == "sale_sum"){
			$cellValue = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
		}elseif($field_name == "sale_sum_ex_vat"){
			$cellValue = $settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat;
		}elseif($field_name == "purchase_sum"){
			$cellValue = $settle_purchase_supply + $settle_purchase_delivery_in_vat;
		}elseif($field_name == "purchase_sum_ex_vat"){
			$cellValue = $settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat;
		}elseif($field_name == "receive_addr1"){
			$cellValue .= " " . $addr2;
		}elseif($field_name == "order_cs_status"){
			if($cellValue == "NORMAL") {
				$cellValue = "";
			}else {
				if ($settle_type == "ADJUST_SALE") {
					$cellValue = "매출보정";
				} elseif ($settle_type == "ADJUST_PURCHASE") {
					$cellValue = "매입보정";
				} elseif ($settle_type == "CANCEL") {
					$cellValue = "취소";
				} elseif ($settle_type == "AD_COST_CHARGE") {
					$cellValue = "광고비";
				} elseif ($settle_type == "EXCHANGE") {
					$cellValue = "교환";
				}
			}
		}elseif($field_name == "product_tax_type"){
			if($cellValue == "TAXATION"){
				$cellValue = "과세";
			}elseif($cellValue == "FREE"){
				$cellValue = "면세";
			}elseif($cellValue == "SMALL"){
				$cellValue = "영세";
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
$user_filename = "매입매출현황.xlsx";
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
$_SESSION["XLS_TRANSACTION_LIST"] = "";

ob_end_clean();
?>