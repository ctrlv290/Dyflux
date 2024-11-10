<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별송장등록 리스트 JSON
 */
//Page Info
$pageMenuIdx = 80;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();


//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.order_idx ASC";
$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_progress_step",
	"delivery_code",
	"A.order_idx",
	"A.market_order_no",
	"A.invoice_no",
	"A.receive_name",
	"A.receive_hp_num",
	"A.receive_addr1",
	"A.market_product_name",
	"A.market_product_option",
	"search_column",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"A.order_idx"
);
$qryWhereAry = array();
	foreach($available_col as $sitem) {

		$col = $sitem;
		$col_c = str_replace("A.", "A_", $col);
		$val = trim($_GET[$col_c]);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_progress_step") {
				$val_ary       = explode(",", $val);
				$val_ary_quote = array_map(function ($val) {
					return "'" . $val . "'";
				}, $val_ary);
				$val_join      = implode(", ", $val_ary_quote);
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif(trim($col) == "delivery_code"){
				$qryWhereAry[] = " A.delivery_code = N'" . $val . "'";
			}elseif(trim($col) == "search_column" && in_array($val, $available_val)){
				if($val == "order_idx")
				{
					$qryWhereAry[] = $val . " = N'" . trim($_GET["search_keyword"]) . "'";
				}else{
					if(trim($_GET["search_keyword"]) != "") {
						$qryWhereAry[] = $val . " like N'%" . trim($_GET["search_keyword"]) . "%'";
					}
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_GET["period_type"] == "order_accept_regdate"){
		$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_GET["date_start"]." " .  $_GET["time_start"] . "'
			And A.order_progress_step_accept_date <= '".$_GET["date_end"]." " .  $_GET["time_end"] . "'
		";
	}elseif($_GET["period_type"] == "shipping_date"){
		$qryWhereAry[] = "	 
			A.shipping_date >= '".$_GET["date_start"]." " .  $_GET["time_start"] . "'
			And A.shipping_date <= '".$_GET["date_end"]." " .  $_GET["time_end"] . "' 
		";
	}

	//판매처
	if($_GET["seller_idx"]){
		$qryWhereAry[] = " A.seller_idx  = N'" .$_GET["seller_idx"]. "'";
		$seller_idx = $_GET["seller_idx"];
	}

	//공급처
	if($_GET["supplier_idx"]){
		$qryWhereAry[] = " P.supplier_idx  = N'" .$_GET["supplier_idx"]. "'";
	}


// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 100000;
$args['show_page'] 	= 100000;

// make select query
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, D.delivery_name
							, ROW_NUMBER() Over(ORDER BY $order_by) as num
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
								Left Outer Join (
									Select distinct delivery_code, delivery_name
									From DY_DELIVERY_CODE
								) D On D.delivery_code = A.delivery_code
";
$args['qry_where']			= " A.order_is_del = N'N' And OPM.order_matching_is_del = N'N' And A.order_progress_step in (N'ORDER_INVOICE', N'ORDER_SHIPPED') ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= "";
$args['qry_orderby']		= $order_by;

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "1";

$args['addFormStr'] 		= '';

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


$C_Seller = new Seller();
if($seller_idx) {
	$_format = $C_Seller->getSellerInvoiceFormat($seller_idx);

	if (!$_format) {
		$_format = $GL_SELLER_INVOICE_FORMAT;
	}

	$_view_seller = $C_Seller->getAllSellerData($seller_idx);
	$seller_name = $_view_seller["seller_name"];

}else{
	exit;
}

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
$xls_header = array();

foreach(excelColumnRange('A', 'AZ') as $char) {
	$val = explode("|", $_format[$char]);
	$header = $val[0];
	$value = $val[1];
	if($header && $value != "|") {
		$xls_header[] = array(
			"header_name" => $header,
			"field_name" => $value,
			"width" => 22,
		);
	}

}


$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
$header_ary_euckr = array();
foreach($xls_header as $hh)
{
	//$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh["header_name"]);
	$header_ary_euckr[] = $hh["header_name"];
}

$i = 1;
if($_format["margin_top"]){
	$i += $_format["margin_top"];
}


if($_format["header_print"] == "Y") {

	$activesheet->fromArray($header_ary_euckr, NULL, 'A'.$i);
	$activesheet->getStyle('A'.$i.':' . $xls_header_end . $i)->applyFromArray(
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
}

//List Apply
$i++;

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


		if($field_name == "stock_unit_price"  || $field_name == "stock_price_NORMAL" || $field_name == "stock_price_BAD"){

			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
		}else {

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
$user_filename = $seller_name.".xlsx";
if (is_ie()) $user_filename = utf2euc($user_filename);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');
?>