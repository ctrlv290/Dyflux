<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 35;
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

$order_by = "A.product_regdate ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_sale_type",
	"supplier_idx",
	"search_column",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"market_product_name_no",
	"order_idx",
	"market_product_no",
	"market_product_name",
	"market_product_option",
);
$qryWhereAry = array();

foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $available_col)) {
		if(
			$col == "product_category_l_idx"
			|| $col == "product_category_m_idx"
			|| $col == "product_sale_type"
		) {
			$qryWhereAry[] = $col . " = N'" . $val . "'";
		}elseif(trim($col) == "search_column"){
			if(trim($_GET["search_keyword"]) != "" && in_array($val, $available_val)) {
				if($val == "product_option_name") {
					$qryWhereAry[] = "A.product_idx in (Select product_idx From DY_PRODUCT_OPTION Where product_option_is_del = N'N' And product_option_name like N'%".trim($_GET["search_keyword"])."%')";
				}elseif($val == "product_option_idx") {
					$qryWhereAry[] = "A.product_idx in (Select product_idx From DY_PRODUCT_OPTION Where product_option_is_del = N'N' And product_option_idx = N'".trim($_GET["search_keyword"])."')";
				}else{
					$qryWhereAry[] = $val . " like N'%" . trim($_GET["search_keyword"]) . "%'";
				}
			}
		}else {
			$qryWhereAry[] = $col . " like N'%" . $val . "%'";
		}
	}
}

$date_search_col = "";
if($_GET["period_search_type"] == "regdate"){
	$qryWhereAry[] = "	 
			A.product_regdate >= '".$_GET["date_start"]." 00:00:00' 
			And A.product_regdate <= '".$_GET["date_end"]." 23:59:59' 
		";
}elseif($_GET["period_search_type"] == "soldoutdate"){
	$qryWhereAry[] = "	 
			A.product_regdate >= '".$_GET["date_start"]." 00:00:00' 
			And A.product_regdate <= '".$_GET["date_end"]." 23:59:59' 
		";
}
//공급처
if($_GET["supplier_idx"]){
	$qryWhereAry[] = "A.supplier_idx in (" . implode(",", $_GET["supplier_idx"]) . ")";
}

//옵션포함
$include_option = false;
if($_GET["include_option"] == "Y"){
	$include_option = true;
}

//벤더사 노출 여부 확인
if(!isDYLogin()){
	$qryWhereAry[] = " 
			(
				A.product_vendor_show = N'ALL'
				Or
				(
					A.product_vendor_show = N'SELECTED' And A.product_idx in (Select product_idx From DY_PRODUCT_VENDOR_SHOW Where product_vendor_show_is_del = N'N' And vendor_idx = N'".$GL_Member["member_idx"]."')
				)
			)
		";
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.product_idx";
$args['qry_get_colum'] 	= " A.product_idx
							, A.product_img_main
							, A.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_1) as product_img_filename_1
							, A.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_2) as product_img_filename_2
							, A.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_3) as product_img_filename_3
							, A.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_4) as product_img_filename_4
							, A.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_5) as product_img_filename_5
							, A.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_6) as product_img_filename_6
							, A.product_name
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = A.product_category_l_idx), '') as category_l_name, A.product_category_l_idx 
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = A.product_category_m_idx), '') as category_m_name, A.product_category_m_idx
							, P.member_idx, P.supplier_name
							, S.seller_idx, S.seller_name
							, A.product_supplier_name, A.product_regdate
							, Case 
								When A.product_vendor_show = 'SHOW' Then 'Y'
								When A.product_vendor_show = 'HIDE' Then 'N'
								When A.product_vendor_show = 'ALL' Then '전체노출'
								When A.product_vendor_show = 'SELECTED' Then '특정업체노출'
							End as product_vendor_show
							, Case
								When product_sale_type = 'SELF' Then '사입/자체'
								When product_sale_type = 'CONSIGNMENT' Then '위탁'
							End as product_sale_type
							, (Select count(*) From DY_PRODUCT_OPTION PO Where PO.product_idx = A.product_idx And PO.product_option_is_del = N'N' And product_option_soldout = N'Y') as soldout_cnt 
							, (Select count(*) From DY_PRODUCT_OPTION PO2 Where PO2.product_idx = A.product_idx And PO2.product_option_is_del = N'N' And product_option_soldout_temp = N'Y') as soldout_temp_cnt 
								
                            ";

if($include_option){
	$args['qry_get_colum'] .= ", O.product_option_idx, O.product_option_name, O.product_option_purchase_price ";
	$args['qry_get_colum'] .= ", O.product_option_sale_price_A, O.product_option_sale_price_B, O.product_option_sale_price_C, O.product_option_sale_price_D, O.product_option_sale_price_E ";
	$args['qry_get_colum'] .= ", O.product_option_warning_count, O.product_option_danger_count, O.product_option_soldout, O.product_option_soldout_temp, O.product_option_regdate ";
}

$args['qry_table_name'] 	= " DY_PRODUCT A 
								Left Outer Join DY_MEMBER_SUPPLIER P On A.supplier_idx = P.member_idx
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
";
if($include_option){
	$args['qry_table_name'] 	.= " Inner Join DY_PRODUCT_OPTION O On A.product_idx = O.product_idx ";
}
$args['qry_where']			= " A.product_is_del = 'N' And A.product_is_trash = 'N' And A.product_is_use = 'Y'";

if($include_option){
	$args['qry_where']		.= " And O.product_option_is_del = N'N' ";
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

//기본 엑셀 항목 설정 가져오기
include "../common/_xls_default_column_array.php";
$xls_header_default = $xls_column_ary["PRODUCT_LIST"];

if($include_option){
	$xls_header_default = array_merge($xls_column_ary["PRODUCT_LIST"], $xls_column_ary["PRODUCT_OPTION_LIST"]);
}

//사용자 엑셀 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumnXls("PRODUCT_LIST", $GL_Member["member_idx"]);

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

		if($field_name == "product_image"){
			if($row["product_img_main"] != "0"){
				$img_no = $row["product_img_main"];
				if($row["product_img_filename_".$img_no] != ""){
					$cellValue = '<img src="'.DY_DOMAIN.'/_data/product/'.$row["product_img_filename_".$img_no].'" style="width: 200px;" />';
				}

				$file_path = DY_PRODUCT_UPLOAD_URL.'/'.$row["product_img_filename_".$img_no];

				$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing->setPath($file_path);
				$drawing->setCoordinates($cod);
				$drawing->setWidth(200);
				$drawing->setHeight(200);
				$drawing->setWorksheet($spreadsheet->getActiveSheet());

				$data_type = "image";
			}
		}elseif($field_name == "category_name_full"){
			$category = array();
			if($row["category_l_name"] != ""){
				$category[] = $row["category_l_name"];
				if($row["category_m_name"] != ""){
					$category[] = $row["category_m_name"];
				}
			}

			$cellValue = implode(" > ", $category);

		}elseif($field_name == "product_soldout"){
			if($row["soldout_cnt"] != "" && $row["soldout_cnt"] != "0"){
				$cellValue = "품절 (" . $row["soldout_cnt"] . ")";
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
$user_filename = "상품목록.xlsx";
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
$_SESSION["PRODUCT_LIST"] = "";

ob_end_clean();
?>