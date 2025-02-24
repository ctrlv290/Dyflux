<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고예정 목록 엑셀 다운
 */
//Page Info
$pageMenuIdx = 119;
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

$order_by = "stock_request_date DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"supplier_idx",
	"stock_is_proc",
	"supplier_idx",
	"search_column",
);
$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $avaliable_col)) {
			if(
				$col == "stock_status"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif($col == "stock_is_proc"){

				if($val == "NA"){
					$qryWhereAry[] = $col . " = N'N'";
					$qryWhereAry[] = " stock_order_is_ready = N'N'";
				}else{
					$qryWhereAry[] = $col . " = N'" . $val . "'";
				}

			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			stock_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 100000;
$args['show_page'] 	= 100000;

// make select query
$args['qry_table_idx'] 	= "A.stock_idx";
$args['qry_get_colum'] 	= " A.*
							, SO.stock_order_regdate
							, SO.stock_order_date
							, SO.stock_order_in_date
							, S.supplier_name
							, P.product_name, PO.product_option_name
							, (S.supplier_addr1 + ' ' + S.supplier_addr2) as supplier_addr
							, (Select member_id From DY_MEMBER M Where A.stock_request_member_idx = M.idx) as member_id
							, (Select top 1 code_name From DY_CODE DC Where DC.parent_code = 'STOCK_KIND' And DC.code = A.stock_kind) as stock_kind_han
							, C.code_name as stock_status_name
							, (Case
								When A.stock_is_proc = 'N' And stock_order_is_ready = 'Y' Then '".iconv("cp949", "UTF-8", "미처리")."'
								When A.stock_is_proc = 'N' And stock_order_is_ready = 'N' Then '".iconv("cp949", "UTF-8", "미처리(추가입고)")."'
								When A.stock_is_proc = 'Y' Then '".iconv("cp949", "UTF-8", "처리완료")."'
							End) as stock_is_proc_han
							, (Case
								When A.stock_is_proc = 'Y' Then stock_amount
								When A.stock_is_proc = 'N' Then '-'
							End) as stock_amount_cal
							, (Case
								When A.order_idx = 0 And A.stock_order_idx <> 0 Then A.stock_order_idx
								When A.order_idx <> 0 And A.stock_order_idx = 0 Then A.order_idx
							End)
							as stock_code
							, convert(varchar(20), stock_request_date, 120) as stock_request_date_convert
                            ";

$args['qry_table_name'] 	= " DY_STOCK A 
								Left Outer Join DY_STOCK_ORDER SO On A.stock_order_idx = SO.stock_order_idx 
								Left Outer Join DY_ORDER O On A.order_idx = O.order_idx 
								Left Outer Join DY_PRODUCT P On A.product_idx = P.product_idx 
								Left Outer Join DY_PRODUCT_OPTION PO On A.product_option_idx = PO.product_option_idx 
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join DY_CODE C On C.parent_code = N'STOCK_STATUS' And C.code = A.stock_status 
							";
$args['qry_where']			= " A.stock_kind in ('STOCK_ORDER', 'RETURN', 'EXCHANGE')
								And (
									A.stock_order_idx = 0
									Or
									(SO.stock_order_is_del = 'N' And SO.stock_order_is_order in (N'Y', N'T'))
								)
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
		"header_name" => "구분",
		"field_name" => "stock_kind_han",
		"width" => 10,
	),
	array(
		"header_name" => "코드",
		"field_name" => "stock_code",
		"width" => 10,
	),
	array(
		"header_name" => "생성일",
		"field_name" => "stock_request_date_convert",
		"width" => 20,
	),
	array(
		"header_name" => "입고예정일",
		"field_name" => "stock_due_date",
		"width" => 14,
	),
	array(
		"header_name" => "작업자",
		"field_name" => "member_id",
		"width" => 10,
	),
	array(
		"header_name" => "공급처",
		"field_name" => "supplier_name",
		"width" => 20,
	),
	array(
		"header_name" => "상품옵션코드",
		"field_name" => "product_option_idx",
		"width" => 16,
	),
	array(
		"header_name" => "상품명",
		"field_name" => "product_name",
		"width" => 30,
	),
	array(
		"header_name" => "옵션명",
		"field_name" => "product_option_name",
		"width" => 30,
	),
	array(
		"header_name" => "원가",
		"field_name" => "stock_unit_price",
		"width" => 16,
	),
	array(
		"header_name" => "구매자정보",
		"field_name" => "receiver_info",
		"width" => 20,
	),
	array(
		"header_name" => "예정수량",
		"field_name" => "stock_due_amount",
		"width" => 12,
	),
	array(
		"header_name" => "입고수량",
		"field_name" => "stock_amount_cal",
		"width" => 12,
	),
	array(
		"header_name" => "상태",
		"field_name" => "stock_status_name",
		"width" => 20,
	),
	array(
		"header_name" => "작업",
		"field_name" => "stock_is_proc_han",
		"width" => 20,
	),
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


//List Apply
$i = 2;
foreach($_list as $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["field_name"]];
	}

	$activesheet->fromArray($xls_row, NULL, 'A'.$i);
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
$user_filename = "입고예정목록.xlsx";
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