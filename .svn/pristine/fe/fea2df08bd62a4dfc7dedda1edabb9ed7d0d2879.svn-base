<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주관리 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 116;

//Euc-Kr 사용 설정
$GL_Enable_EUCKR = true;
//Init
include_once "../_init_.php";


ini_set('memory_limit','-1');

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

$order_by = "A.stock_order_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"supplier_idx",
	"search_column",
);
$avaliable_search_column = array(
	"stock_order_idx",
	"stock_order_officer_name",
	"stock_order_officer_tel",
	"stock_order_supplier_name",
	"stock_order_supplier_tel",
);
$qryWhereAry = array();

foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $avaliable_col)) {
		if(in_array($val, $avaliable_search_column) && isset($_GET["search_keyword"]) && trim($_GET["search_keyword"]) != "") {
			$qryWhereAry[] = $val . " like N'%" . $_GET["search_keyword"] . "%'";
		}
	}
}

if(trim($_GET["date_start"]) != "" && trim($_GET["date_end"]) != ""){
	$qryWhereAry[] = "	 
			A.stock_order_regdate >= '".$_GET["date_start"]." 00:00:00' 
			And A.stock_order_regdate <= '".$_GET["date_end"]." 23:59:59' 
		";
}

//공급처
if(isset($_GET["supplier_idx"]) && is_array($_GET["supplier_idx"]) && count($_GET["supplier_idx"]) > 0){
	$qryWhereAry[] = "A.supplier_idx in (" . implode(",", $_GET["supplier_idx"]) . ")";
}


// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 10000;
$args['show_page'] 	= 10000;

// make select query
$args['qry_table_idx'] 	= "A.stock_order_idx";
$args['qry_get_colum'] 	= " A.*
							, S.supplier_name
							, (S.supplier_addr1 + ' ' + S.supplier_addr2) as supplier_addr
							, (Select member_id From DY_MEMBER M Where A.member_idx = M.idx) as member_id
                            ";

$args['qry_table_name'] 	= " DY_STOCK_ORDER A Left Outer Join DY_MEMBER_SUPPLIER S On A.supplier_idx = S.member_idx ";
$args['qry_where']			= " A.stock_order_is_del = 'N' 
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
	"발주코드" => "stock_order_idx",
	"발주일" => "stock_order_date",
	"입고예정일" => "stock_order_in_date",
	"공급처" => "supplier_name",
	"공급처 주소" => "supplier_addr",
	"담당자" => "stock_order_supplier_name",
	"연락처" => "stock_order_supplier_tel",
	"작업자" => "member_id",
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array_keys($xls_header);
$header_ary_euckr = array();
foreach($header_ary as $hh)
{
	$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh);
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
	)
);


//List Apply
$i = 2;
foreach($_list as $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $col) {
		$xls_row[] = $row[$col];
	}

	$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$i++;
}

foreach(range('A', $xls_header_end) as $columnID) {
	$activesheet->getColumnDimension($columnID)->setAutoSize(true);
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
$user_filename = "발주목록.xlsx";
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