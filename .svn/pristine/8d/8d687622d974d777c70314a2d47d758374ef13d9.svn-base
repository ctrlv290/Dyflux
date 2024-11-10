<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 확장주문검색 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 77;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["PRODUCT_LIST"] = "Y";

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

$order_by = "A.order_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_progress_step",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"A.order_idx"
);
$qryWhereAry = array();

foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $available_col)) {
		if(trim($col) == "order_progress_step") {
			$val_ary = explode(",", $val);
			$val_ary_quote = array_map(function($val){
				return "'" . $val . "'";
			}, $val_ary);
			$val_join = implode(", ", $val_ary_quote);
			$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
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

$date_search_col = "";
if($_GET["period_type"] == "order_regdate"){
	$qryWhereAry[] = "	 
			A.order_regdate >= '".$_GET["date_start"]." " .  $_GET["time_start"] . "'
			And A.order_regdate <= '".$_GET["date_end"]." " .  $_GET["time_end"] . "'
		";
}elseif($_GET["period_type"] == "order_accept_regdate"){
	$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_GET["date_start"]." " .  $_GET["time_start"] . "'
			And A.order_progress_step_accept_date <= '".$_GET["date_end"]." " .  $_GET["time_end"] . "' 
		";
}

//공급처
if($_GET["supplier_idx"]){
	$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_GET["supplier_idx"]) . ")";
}

//판매처
if($_GET["seller_idx"]){
	$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_GET["seller_idx"]) . ")";
}


// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, OPM.product_option_cnt, OPM.product_option_sale_price
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, S.supplier_name
							, SL.seller_name
							, (Select
								Sum(stock_amount * stock_type) as stock_amount_NORMAL
								From DY_STOCK S
								Where S.product_option_idx = PO.product_option_idx 
										And S.stock_is_del = N'N'
										And S.stock_is_confirm = N'Y'
										And S.stock_status = 'NORMAL'
							) as current_stock_amount
							, C.code_name as order_progress_step_han
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
								Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
";
$args['qry_where']			= " 
								A.order_is_del = N'N' And OPM.order_matching_is_del = N'N'
								";
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
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

/**
 * ListTable 사용 안함
 * 일반 쿼리로 실행
 */
$qry = "";
$qry = "Select " . $args['qry_get_colum']  . " From " . $args['qry_table_name']  . " Where " . $args['qry_where'] . " Order By " . $args['qry_orderby'];
$C_Dbconn = new DBConn();
$C_Dbconn -> db_connect();
$_list = $C_Dbconn -> execSqlList($qry);
$C_Dbconn -> db_close();



//기본 엑셀 항목 설정 가져오기
include "../common/_xls_default_column_array.php";
$xls_header_default = $xls_column_ary["ORDER_SEARCH_LIST"];

if($include_option){
	$xls_header_default = array_merge($xls_column_ary["ORDER_SEARCH_LIST"], $xls_column_ary["ORDER_SEARCH_LIST"]);
}

//사용자 엑셀 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumnXls("ORDER_SEARCH_LIST", $GL_Member["member_idx"]);

//조합 - 기준은 기본 엑셀 항목
$xls_header_list = array();
if($userColumnList) {
	foreach ($userColumnList as $u) {
		if($u["col_user_is_use"] == "Y") {
			$user_key = $u["col_field_name"];
			$exists   = array_filter($xls_header_default, function ($val, $key) use ($user_key) {

				return ($val["col"] == $user_key) ? true : false;

			}, ARRAY_FILTER_USE_BOTH);

			if ($exists) {
				$tmp           = reset($exists);
				$tmp["name"]   = $u["col_user_visible_name"];
				$tmp["is_use"] = ($u["col_user_is_use"] == "Y") ? true : false;
			} else {
				$tmp = $u;
			}
			$xls_header_list[] = $tmp;
		}
	}
}

//조합 - 사용자 설정에 없는 항목 넣기
foreach($xls_header_default as $d){
	$col_key = $d["col"];
	$exists   = array_filter($userColumnList, function ($val, $key) use ($col_key) {

		return ($val["col_field_name"] == $col_key) ? true : false;

	}, ARRAY_FILTER_USE_BOTH);

	if (!$exists) {
		$xls_header_list[] = $d;
	}
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

$xls_header = $xls_header_list;
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
//$header_ary_euckr = array();
//foreach($xls_header as $hh)
//{
//	$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh["header_name"]);
//}


foreach ($xls_header as $hh)
{
	$header_ary[] = $hh["name"];
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
		$xls_row[] = $row[$val["col"]];
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["col"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];

		if($field_name == "receive_addr") {
			$addr = array();
			if ($row["receive_addr1"] != "") {
				$addr[] = $row["receive_addr1"];
				if ($row["receive_addr2"] != "") {
					$addr[] = $row["receive_addr2"];
				}
			}

			$cellValue = implode(" ", $addr);
		}elseif($field_name == "product_option_sale_price"){
			$cellValue = $row["product_option_sale_price"] * $row["product_option_cnt"];
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
$user_filename = "확장주문검색.xlsx";
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
$_SESSION["ORDER_SEARCH_LIST"] = "";

ob_end_clean();
?>