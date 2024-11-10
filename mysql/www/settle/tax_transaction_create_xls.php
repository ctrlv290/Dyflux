<?php
/**
 * User: ysh
 * Date: 2020-04-17
 * Desc: 월 정산 판매일보 파일생성
 */
//Page Info
$pageMenuIdx = 78;
//Init
include_once "../_init_.php";

$tax_type   = $_POST["tax_type"];
$date_ym    = $_POST["date_ym"];
$target_idx = $_POST["target_idx"];

$time = strtotime($date_ym . "-01");

$date_start = date('Y-m-d', $time);
$date_end = date('Y-m-t', $time);

$date_title = date('Y년 m월', $time);


$qry = "
            SELECT T.*, CASE WHEN T.settle_date != DATE_FORMAT(T.settle_regdate,'%Y-%m-%d') Then T.settle_date ELSE T.settle_regdate End AS search_date
                    , S.seller_name , SUPPLIER.supplier_name, T.order_cs_status
                    , Case When T.order_cs_status = 'ORDER_CANCEL' Then
                        (Select code_name From DY_CODE C_I Where C_I.parent_code = N'CS_REASON_CANCEL' And C_I.code = T.cs_reason_cancel)
                         End as cs_reason_cancel_text 
                    , (Select code_name From DY_CODE C_I Where C_I.parent_code = N'PRODUCT_TAX_TYPE' And C_I.code = T.product_tax_type) as product_tax_type_text 
                    , O.receive_name, O.receive_tp_num, O.receive_hp_num
                    , IFNULL(O.receive_zipcode, '') as receive_zipcode
                    , IFNULL(O.receive_addr1, '') as receive_addr1
                    , IFNULL(O.receive_addr2, '') as receive_addr2
                    , IFNULL(O.receive_zipcode, '') as receive_zipcode
                    , O.receive_memo
            FROM DY_SETTLE T
                Left Outer Join DY_ORDER O On T.order_idx = O.order_idx 								
                Left Outer Join DY_SELLER S ON T.seller_idx = S.seller_idx 								
                Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = T.supplier_idx 
            WHERE T.settle_is_del = N'N'
            And 	 
                (
                    settle_date >= '$date_start' 
                    And settle_date <= '$date_end'
                ) 
		 ";

if($tax_type == 'SALE'){
    $qry .= "And T.seller_idx = N'$target_idx'";
    $xls_header = array(
        array(
            "header_name" => '날짜',
            "field_name" => "search_date",
            "width" => 20,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "처리",
            "field_name" => "order_cs_status",
            "width" => 10,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "사유",
            "field_name" => "cs_reason_cancel_text",
            "width" => 12,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "마켓",
            "field_name" => "seller_name",
            "width" => 20,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "수취인",
            "field_name" => "receive_name",
            "width" => 10,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "전화번호",
            "field_name" => "receive_tp_num",
            "width" => 16,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "핸드폰",
            "field_name" => "receive_hp_num",
            "width" => 16,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "우편번호",
            "field_name" => "receive_zipcode",
            "width" => 12,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "주소",
            "field_name" => "receive_addr1",
            "width" => 60,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "배송메세지",
            "field_name" => "receive_memo",
            "width" => 40,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "세금",
            "field_name" => "product_tax_type_text",
            "width" => 6,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "상품명",
            "field_name" => "product_name",
            "width" => 30,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "옵션",
            "field_name" => "product_option_name",
            "width" => 30,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "판매수량",
            "field_name" => "product_option_cnt",
            "width" => 10,
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
            "header_name" => "매출배송비",
            "field_name" => "settle_delivery_in_vat",
            "width" => 20,
            "data_type" => "number",
            "halign" => "right",
        ),
        array(
            "header_name" => "매출합계",
            "field_name" => "settle_sale_sum",
            "width" => 20,
            "data_type" => "number",
            "halign" => "right",
        ),
        array(
            "header_name" => "비고",
            "field_name" => "settle_memo",
            "width" => 40,
            "data_type" => "text",
            "halign" => "left",
        )
    );
}else{
    $qry .= "And T.supplier_idx = N'$target_idx'";
    $xls_header = array(
        array(
            "header_name" => '날짜',
            "field_name" => "search_date",
            "width" => 20,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "처리",
            "field_name" => "order_cs_status",
            "width" => 10,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "사유",
            "field_name" => "cs_reason_cancel_text",
            "width" => 12,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "수취인",
            "field_name" => "receive_name",
            "width" => 10,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "전화번호",
            "field_name" => "receive_tp_num",
            "width" => 16,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "핸드폰",
            "field_name" => "receive_hp_num",
            "width" => 16,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "우편번호",
            "field_name" => "receive_zipcode",
            "width" => 12,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "주소",
            "field_name" => "receive_addr1",
            "width" => 60,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "배송메세지",
            "field_name" => "receive_memo",
            "width" => 40,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "세금",
            "field_name" => "product_tax_type_text",
            "width" => 6,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "상품명",
            "field_name" => "product_name",
            "width" => 30,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "옵션",
            "field_name" => "product_option_name",
            "width" => 30,
            "data_type" => "text",
            "halign" => "left",
        ),
        array(
            "header_name" => "판매수량",
            "field_name" => "product_option_cnt",
            "width" => 10,
            "data_type" => "number",
            "halign" => "right",
        ),
        array(
            "header_name" => "거래처",
            "field_name" => "supplier_name",
            "width" => 20,
            "data_type" => "text",
            "halign" => "center",
        ),
        array(
            "header_name" => "매입단가",
            "field_name" => "settle_purchase_unit_supply",
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
            "header_name" => "매입배송비",
            "field_name" => "settle_purchase_delivery_in_vat",
            "width" => 20,
            "data_type" => "number",
            "halign" => "right",
        ),
        array(
            "header_name" => "매입합계",
            "field_name" => "settle_purchase_sum",
            "width" => 20,
            "data_type" => "number",
            "halign" => "right",
        ),
        array(
            "header_name" => "비고",
            "field_name" => "settle_memo",
            "width" => 40,
            "data_type" => "text",
            "halign" => "left",
        )
    );
}

$C_Dbconn = new DBConn();
$C_Dbconn -> db_connect();
$_list = $C_Dbconn -> execSqlList($qry);
$C_Dbconn -> db_close();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

//$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//$writer->save("????_???.xlsx");

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();


//Header
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

	$settle_sale_supply = $row["settle_sale_supply"];
	$settle_delivery_in_vat = $row["settle_delivery_in_vat"];

	$settle_purchase_supply = $row["settle_purchase_supply"];
	$settle_purchase_delivery_in_vat = $row["settle_purchase_delivery_in_vat"];

	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
        $halign = $xls_header[$currentColumn]["halign"];
		if($field_name == "receive_addr1"){
			$cellValue .= " " . $addr2;
		}elseif($field_name == "order_cs_status") {
            if ($cellValue == "NORMAL") {
                $cellValue = "";
            } elseif ($settle_type == "ADJUST_SALE") {
                $cellValue = "매출보정";
            } elseif ($settle_type == "ADJUST_PURCHASE") {
                $cellValue = "매입보정";
            } elseif ($settle_type == "CANCEL") {
                $cellValue = "취소";
            } elseif ($settle_type == "AD_COST_CHARGE") {
                $cellValue = "광고비";
            } elseif ($settle_type == "EXCHANGE") {
                $cellValue = "교환";
            }else{
                $cellValue = "";
            }
        }

        if($data_type == "number") {
            //통화 셀 서식 지정
            $activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
        }else{
            $activesheet->setCellValue($cod, $cellValue);
            $activesheet->getStyle($cod)->getAlignment()->setHorizontal($halign);
        }
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

list($usec, $sec) = explode(" ", microtime());
$create_filename = (round(((float)$usec + (float)$sec))) . rand(1, 10000);        // 날짜에 따라 변환
$create_filename .= ".xlsx";
//if (is_ie()) $user_filename = utf2euc($user_filename);
if (is_ie()) $create_filename = urlencode($create_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$create_filename.'"');

//엑셀 생성
//저장 위치 DY_STOCK_ORDER_PATH
$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$Excel_writer->save(DY_TAX_XLS_PATH . "/" . $create_filename);

$response = array();
$response["result"] = false;

//파일 생성 확인
if(file_exists(DY_TAX_XLS_PATH."/".$create_filename)){

    //파일생성로그 입력
    $C_Settle = new Settle();
    $inserted_idx = $C_Settle->insertTaxFileLog($create_filename, $target_idx, $tax_type, "", $date_title);
    $response["result"] = true;
    $response["target_idx"] = $target_idx;
    $response["file_idx"] = $inserted_idx;
    $response["filename"] = $create_filename;

}
echo json_encode($response);

?>