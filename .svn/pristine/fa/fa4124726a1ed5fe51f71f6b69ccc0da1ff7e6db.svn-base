<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일괄합포제외 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 77;

//Euc-Kr 사용 설정
//$GL_Enable_EUCKR = true;
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

$order_by = " A.order_pack_idx ASC, A.order_idx ASC  ";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_cnt_start",
	"order_cnt_end",
	"product_option_cnt_start",
	"product_option_cnt_end",
	"include_single",
	"include_soldout",
	"include_soldout_temp",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"P.product_name",
	"PO.product_option_name",
);

//단품주문포함 Flag
$searchSingleOrder = false;

$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_cnt_start") {
				$qryWhereAry[] = " order_cnt >= N'".$val."'";
			}elseif(trim($col) == "order_cnt_end"){
				$qryWhereAry[] = " order_cnt <= N'".$val."'";
			}elseif(trim($col) == "product_option_cnt_start"){
				$qryWhereAry[] = " PM.product_option_cnt <= N'".$val."'";
			}elseif(trim($col) == "product_option_cnt_end"){
				$qryWhereAry[] = " PM.product_option_cnt <= N'".$val."'";
			}elseif(trim($col) == "include_single"){
				$searchSingleOrder = true;
			}elseif(trim($col) == "include_soldout"){
				$qryWhereAry[] = " PO.product_option_soldout = N'Y'";
			}elseif(trim($col) == "include_soldout_temp"){
				$qryWhereAry[] = " PO.product_option_soldout_temp = N'Y'";
			}elseif(trim($col) == "search_column" && in_array($val, $available_val)){
				if($val == "order_idx")
				{
					$qryWhereAry[] = $val . " = N'" . trim($_search_paramAryList["search_keyword"]) . "'";
				}else{
					if(trim($_search_paramAryList["search_keyword"]) != "") {
						$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
					}
				}
			}else{
				//$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["hour_start"] . ":00:00'
			And order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["hour_end"] . ":59:59'
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	}
}


// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 99999;
$args['show_page'] 	= 99999;

// make select query
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, OPM.order_matching_idx, OPM.product_option_cnt
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, S.supplier_name
							, SL.seller_name
							, IFNULL((Select
								Sum(stock_amount * stock_type) as stock_amount_NORMAL
								From DY_STOCK S
								Where S.product_option_idx = PO.product_option_idx 
										And S.stock_is_del = N'N'
										And S.stock_is_confirm = N'Y'
										And S.stock_status = 'NORMAL'
							), 0) as current_stock_amount
							, ROW_NUMBER() OVER(PARTITION BY A.order_idx Order by OPM.order_matching_idx ASC) as inner_no
							, Count(*) OVER(PARTITION BY A.order_idx) as inner_no2
							, DATE_FORMAT(order_progress_step_accept_date, '%Y-%m-%d %H:%i:%s') as order_progress_step_accept_date
                            ";

$args['qry_table_name'] 	= " 

								DY_ORDER A
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
";

$args['qry_where']			= " A.order_progress_step = N'ORDER_ACCEPT' And A.order_is_del = N'N' And OPM.order_matching_is_del = N'N' ";

//단품 주문 포함이 아닐 경우
if(!$searchSingleOrder){
	//$args['qry_where']	    .= " And order_pack_cnt > 1 ";
	$args['qry_where']	    .= " And A.order_pack_idx in (
									Select order_pack_idx 
									From DY_ORDER OO 
									Inner Join DY_ORDER_PRODUCT_MATCHING OOP On OO.order_idx = OOP.order_idx 
									Where 
										OO.order_is_del = N'N' 
										And OOP.order_matching_is_del = N'N' 
										And OO.order_progress_step = N'ORDER_ACCEPT' 
										And OOP.product_option_cnt > 1
	) ";
}
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
//******************************* 리스트 기본 설정 끝 ******************************//

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
		"header_name" => "수령자",
		"field_name" => "receive_name",
		"width" => 20,
		"valign" => "middle",
	),
	array(
		"header_name" => "관리번호",
		"field_name" => "order_idx",
		"width" => 20,
	),
	array(
		"header_name" => "발주일",
		"field_name" => "order_progress_step_accept_date",
		"width" => 20,
	),
	array(
		"header_name" => "판매처",
		"field_name" => "seller_name",
		"width" => 30,
	),
	array(
		"header_name" => "상품코드",
		"field_name" => "product_idx",
		"width" => 20,
	),
	array(
		"header_name" => "옵션코드",
		"field_name" => "product_option_idx",
		"width" => 20,
	),
	array(
		"header_name" => "상품명",
		"field_name" => "product_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "옵션",
		"field_name" => "product_option_name",
		"width" => 40,
		"halign" => "left",
	),
	array(
		"header_name" => "수량",
		"field_name" => "product_option_cnt",
		"width" => 16,
	),
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
$header_ary_euckr = array();
foreach($xls_header as $hh)
{
	//$header_ary[] = $hh["header_name"];
	$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh["header_name"]);
	//$header_ary_euckr[] = iconv("UTF-8", "cp949", $hh["header_name"]);
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


//List Apply
$i = 2;
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	//$xls_row[] = $row["inner_no"];
	//$xls_row[] = $row["inner_no2"];
	//echo $row["inner_no2"] . "<br>";
	$inner_no = $row["inner_no"];
	$inner_no2 = $row["inner_no2"];

	if($inner_no == 1) {
		$activesheet->mergeCells('A' . $i . ':A' . ($i + ($inner_no2 - $inner_no)));
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($inner_no > 1) {
			if ($field_name == "order_idx" || $field_name == "order_progress_step_accept_date" || $field_name == "seller_name") {
				$cellValue = "";
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

		if($inner_no == $inner_no2) {
			$activesheet->getStyle($cod)->getBorders()->getBottom()->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
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
function utf2euc($str) { return iconv("UTF-8","euc-kr", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}
$user_filename = "일괄합포제외.xlsx";
//if (is_ie()) $user_filename = utf2euc($user_filename);
if (is_ie()) $user_filename = urlencode($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');


?>