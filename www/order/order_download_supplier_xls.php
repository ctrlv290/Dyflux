<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문다운로드 - 공급처 포맷설정 클릭 시 엑셀 생성
 */
//Page Info
$pageMenuIdx = 78;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$C_Order = new Order();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "O.order_regdate ASC";
$qryWhereAry = array();

//수령자
if($_POST["receive_name"]){
	$qryWhereAry[] = "O.receive_name = N'".$_POST["receive_name"]."'";
	$receive_name = $_POST["receive_name"];
}

//배송비
if($_POST["delivery_type"]){
	$qryWhereAry[] = "O.delivery_type = N'".$_POST["delivery_type"]."'";
	$delivery_type = $_POST["delivery_type"];
}

//발주상태
if($_POST["order_progress_step"]){
	$qryWhereAry[] = "O.order_progress_step = N'".$_POST["order_progress_step"]."'";
	$order_progress_step = $_POST["order_progress_step"];
}

//발주일
if($_POST["date_start"] != "" && $_POST["date_end"] != ""){
	$qryWhereAry[] = "	 
		O.order_progress_step_accept_date >= '".$_POST["date_start"]." " .  $_POST["time_start"] . "'
		And O.order_progress_step_accept_date <= '".$_POST["date_end"]." " .  $_POST["time_end"] . "'
	";

	$date_start = $_POST["date_start"]." " .  $_POST["time_start"];
	$date_end = $_POST["date_end"]." " .  $_POST["time_end"];
}

//공급처
if($_POST["supplier_idx"]){
	//$qryWhereAry[] = "S.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	$qryWhereAry[] = "S.member_idx = N'".$_POST["supplier_idx"]."'";
	$supplier_idx = $_POST["supplier_idx"];
}else{
	$supplier_idx = 0;
}

//판매처
if($_POST["seller_idx"]){
	//$qryWhereAry[] = "O.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	$qryWhereAry[] = "O.seller_idx = N'".$_POST["seller_idx"]."'";
	$seller_idx = $_POST["seller_idx"];
}

$_supplier_idx = $_GET["supplier_idx"];

if(isset($_POST["detail_list"]) && $_POST["detail_list"]){

	$qryWhereAry[] = "OPM.order_matching_idx in (".$_POST["detail_list"].")";

}

// make select query
$args['qry_table_idx'] 	= "OPM.product_option_idx";
$args['qry_get_colum'] 	= " 
							O.seller_idx
							, SL.seller_name
							, O.order_regdate
							, convert(varchar(19), order_progress_step_accept_date, 120) as order_progress_step_accept_date
							, O.order_idx, O.market_order_no
							, O.receive_name, O.receive_tp_num, O.receive_hp_num
							, O.receive_addr1, O.receive_addr2, O.receive_zipcode, O.receive_memo
							, (O.receive_addr1 + ' ' + O.receive_addr2) as receive_addr
							, O.delivery_is_free, O.delivery_fee
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, OPM.product_option_cnt, OPM.product_option_sale_price, OPM.product_option_purchase_price
							, ROW_NUMBER() Over(order by $order_by) as row_num
							, ROW_NUMBER() OVER(PARTITION BY O.order_idx Order by OPM.order_matching_idx ASC) as inner_no
                            ";

$args['qry_table_name'] 	= " 
								DY_ORDER O
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = O.seller_idx
";
$args['qry_where']			= " O.order_is_del = N'N' And OPM.order_cs_status <> N'ORDER_CANCEL'
								And OPM.order_matching_is_del = N'N'
								And O.order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
								And S.member_idx = N'$_supplier_idx'
 
 ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_orderby']		= $order_by;

/**
 * ListTable 사용 안함
 * 일반 쿼리로 실행
 */
$qry = "";
$qry = "Select " . $args['qry_get_colum']  . " From " . $args['qry_table_name']  . " Where " . $args['qry_where'] . " Order By " . $args['qry_orderby'];
$C_Dbconn = new Dbconn();
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

//Header ????
$xls_header = array(
	array(
		"header_name" => "순번",
		"field_name" => "row_num",
		"width" => 8,
	),

//	array(
//		"header_name" => "판매처",
//		"field_name" => "seller_name",
//		"width" => 16,
//	),
//	array(
//		"header_name" => "발주일",
//		"field_name" => "order_progress_step_accept_date",
//		"width" => 21,
//	),
//	array(
//		"header_name" => "주문번호",
//		"field_name" => "market_order_no",
//		"width" => 16,
//	),
	array(
		"header_name" => "관리번호",
		"field_name" => "order_idx",
		"width" => 16,
	),
	array(
		"header_name" => "수령자",
		"field_name" => "receive_name",
		"width" => 14,
	),
	array(
		"header_name" => "전화번호",
		"field_name" => "receive_tp_num",
		"width" => 14,
	),
	array(
		"header_name" => "핸드폰",
		"field_name" => "receive_hp_num",
		"width" => 14,
	),
	array(
		"header_name" => "우편번호",
		"field_name" => "receive_zipcode",
		"width" => 10,
	),
	array(
		"header_name" => "주소",
		"field_name" => "receive_addr",
		"width" => 32,
	),
	array(
		"header_name" => "배송메세지",
		"field_name" => "receive_memo",
		"width" => 32,
	),
	array(
		"header_name" => "상품명",
		"field_name" => "product_name",
		"width" => 32,
	),
	array(
		"header_name" => "옵션",
		"field_name" => "product_option_name",
		"width" => 24,
	),
	array(
		"header_name" => "수량",
		"field_name" => "product_option_cnt",
		"width" => 8,
		"data_type" => "number"
	),
	array(
		"header_name" => "공급가",
		"field_name" => "product_option_purchase_price",
		"width" => 8,
		"data_type" => "number"
	),
	array(
		"header_name" => "배송비",
		"field_name" => "delivery_is_free",
		"width" => 8,
		"data_type" => "number"
	),
	array(
		"header_name" => "합계",
		"field_name" => "order_sum",
		"width" => 8,
		"data_type" => "number"
	)
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
$header_ary_euckr = array();
foreach($xls_header as $hh)
{
	$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh["header_name"]);
}
$activesheet->fromArray($header_ary_euckr, NULL, 'A1');
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

$seller_name = $_list[0]["seller_name"];

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

		if($field_name == "product_option_purchase_price"){
			$cellValue = $row["product_option_purchase_price"] * $row["product_option_cnt"];
		}elseif($field_name == "delivery_is_free"){
			if($row["inner_no"] == "1"){
				$cellValue = $row["delivery_fee"];
			}else{
				$cellValue = 0;
			}
		}elseif($field_name == "order_sum"){
			$_tmp_df = 0;

			if($row["inner_no"] == "1"){
				$_tmp_df = $row["delivery_fee"];
			}

			$cellValue = ($row["product_option_purchase_price"] * $row["product_option_cnt"]) + $_tmp_df;
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


//엑셀 파일명
list($usec, $sec) = explode(" ",microtime());
$create_filename = (round(((float)$usec + (float)$sec))).rand(1,10000);		// 날짜에 따라 변환
$create_filename .= ".xlsx";

//엑셀 생성
//저장 위치 DY_STOCK_ORDER_PATH
$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$Excel_writer->save(DY_ORDER_DOWNLOAD_PATH."/".$create_filename);
//$Excel_writer->save('php://output');

$response = array();
$response["result"] = false;

//파일 생성 확인
if(file_exists(DY_ORDER_DOWNLOAD_PATH."/".$create_filename)){

	//파일생성로그 입력
	$inserted_idx = $C_Order->insertOrderDownloadFileLog($create_filename, $_supplier_idx, $delivery_type, $order_progress_step, $supplier_idx, $seller_idx, $date_start, $date_end, $receive_name);
	$response["result"] = true;
	$response["order_download_file_idx"] = $inserted_idx;
	$response["filename"] = $create_filename;

}
echo json_encode($response);

?>