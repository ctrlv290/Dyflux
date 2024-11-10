<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통으로 사용되는 정보 변경이력  리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 165;

//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["ChangeLogViewerXls"] = "Y";

$C_ListTable = new ListTable();

$manage_group_type = $_GET["manage_group_type"];


//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.regdate ASC";

if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"view",
	"date_start",
	"date_end",
	"search_column",
	"keyword"
);
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	$_search_paramAry_KeyVal = array();
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $avaliable_col)) {

			$_search_paramAry_KeyVal[$col] = $val;

			if(trim($col) == "view")
			{
				$view = trim($val);
			}elseif(trim($col) == "date_start"){
				$qryWhereAry[] = " A.regdate >= '" . $val . " 00:00:00'";
			}elseif(trim($col) == "date_end"){
				$qryWhereAry[] = "A.regdate <= '" . $val . " 23:59:59.998'";
			}
		}
	}

	if(trim($_search_paramAry_KeyVal["keyword"]) != "") {
		if ($_search_paramAry_KeyVal["search_column"] == "member_idx") {
			$qryWhereAry[] = "
			(
				A.table_idx1 = '" . $_search_paramAry_KeyVal["keyword"] . "'
				Or
				A.table_idx2 = '" . $_search_paramAry_KeyVal["keyword"] . "'
			)
			";
		} elseif ($_search_paramAry_KeyVal["search_column"] == "memo") {
			$qryWhereAry[] = "A.memo like '%" . $_search_paramAry_KeyVal["keyword"] . "%'";
		} elseif ($_search_paramAry_KeyVal["search_column"] == "member_id") {
			$qryWhereAry[] = "M.member_id like '%" . $_search_paramAry_KeyVal["keyword"] . "%'";

		}
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
$args['qry_table_idx'] 	= "A.h_idx";
$args['qry_get_colum'] 	= " A.* ";

$table_name = "";
$xls_name = "";
switch ($view)
{
	case "seller" :
		$xls_name = "판매처";
		$table_name = "
			DY_SELLER_HISTORY A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.*
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select seller_name From DY_SELLER S Where S.seller_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '판매처 추가'
					When A.dml_flag = 'U' Then '판매처 수정'
					When A.dml_flag = 'D' Then '판매처 삭제'
				End
				) as action_type
		";

		$qryWhereAry[] = " A.table_idx1 in (Select seller_idx From DY_SELLER S Where S.seller_type = 'MARKET_SELLER')";

		break;
	case "vendor" :
		$xls_name = "벤더사";
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'VENDOR' And is_del = 'N')
				Union all
				Select * From DY_MEMBER_VENDOR_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'VENDOR' And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, (
				Case When A.column_mn = 'vendor_license_file' Or A.column_mn = 'vendor_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'vendor_license_file' Or A.column_mn = 'vendor_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select vendor_name From DY_MEMBER_VENDOR V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '벤더사 추가'
					When A.dml_flag = 'U' Then '벤더사 수정'
					When A.dml_flag = 'D' Then '벤더사 삭제'
				End
				) as action_type
		";
		break;
	case "supplier" :
		$xls_name = "공급처";
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'SUPPLIER' And is_del = 'N')
				Union all
				Select * From DY_MEMBER_SUPPLIER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'SUPPLIER' And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, (
				Case When A.column_mn = 'supplier_license_file' Or A.column_mn = 'supplier_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'supplier_license_file' Or A.column_mn = 'supplier_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select supplier_name From DY_MEMBER_SUPPLIER V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '공급처 추가'
					When A.dml_flag = 'U' Then '공급처 수정'
					When A.dml_flag = 'D' Then '공급처 삭제'
				End
				) as action_type
		";
		break;
	case "user" :
		$xls_name = "사용자";
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where (member_type = 'USER' Or member_type = 'ADMIN') And is_del = 'N')
				Union all
				Select * From DY_MEMBER_USER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where (member_type = 'USER' Or member_type = 'ADMIN') And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, A.after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select name From DY_MEMBER_USER V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사용자 추가'
					When A.dml_flag = 'U' Then '사용자 수정'
					When A.dml_flag = 'D' Then '사용자 삭제'
				End
				) as action_type
		";
		break;
	case "product" :
		$xls_name = "상품";
		$table_name = "
			
			DY_PRODUCT_HISTORY A
			Left Outer Join DY_PRODUCT P On A.table_idx1= P.product_idx
			
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn, A.table_nm
			, (
				Case When A.column_mn = 'product_img_1' 
						Or A.column_mn = 'product_img_2' 
						Or A.column_mn = 'product_img_3' 
						Or A.column_mn = 'product_img_4' 
						Or A.column_mn = 'product_img_5' 
						Or A.column_mn = 'product_img_6' 
					Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'product_img_1'
				        Or A.column_mn = 'product_img_2' 
				        Or A.column_mn = 'product_img_3' 
				        Or A.column_mn = 'product_img_4' 
				        Or A.column_mn = 'product_img_5' 
				        Or A.column_mn = 'product_img_6' 
			        Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx)  as member_id
			, (Select product_name From DY_PRODUCT P Where P.product_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'I' Then '상품 추가'
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'U' Then '상품 수정'
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'D' Then '상품 삭제'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'I' Then '상세페이지 추가'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'U' Then '상세페이지 수정'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'D' Then '상세페이지 삭제'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'I' Then '벤더사노출 추가'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'U' Then '벤더사노출 수정'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'D' Then '벤더사노출 삭제'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'I' Then '상품 옵션 추가'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'U' Then '상품 옵션 수정'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'D' Then '상품 옵션 삭제'
				End
				) as action_type
		";
		break;
	case "site_info" :
		$xls_name = "사이트정보";
		$table_name = "
			DY_SITE_INFO_HISTORY A
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, A.after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, '사이트정보' as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사이트정보 추가'
					When A.dml_flag = 'U' Then '사이트정보 수정'
					When A.dml_flag = 'D' Then '사이트정보 삭제'
				End
				) as action_type
		";
		break;
	case "gift" :
		$xls_name = "사은품";
		$table_name = "
			DY_ORDER_GIFT_HISTORY A
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, A.after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, '사은품정보' as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사은품정보 추가'
					When A.dml_flag = 'U' Then '사은품정보 수정'
					When A.dml_flag = 'D' Then '사은품정보 삭제'
				End
				) as action_type
		";
		break;
}

$args['qry_get_colum'] .= ", DATE_FORMAT(A.regdate, '%Y-%m-%d %H:%i:%s') as regdate2, A.table_nm ";

$args['qry_table_name'] 	= $table_name;
$args['qry_where']			= " 1=1 ";
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
		"header_name" => "항목",
		"field_name" => "memo",
		"width" => 60,
		"halign" => "left",
	),
	array(
		"header_name" => "기존값",
		"field_name" => "before_data",
		"width" => 30,
		"halign" => "left",
	),
	array(
		"header_name" => "변경값",
		"field_name" => "after_data",
		"width" => 30,
		"halign" => "left",
	),
	array(
		"header_name" => "변경일",
		"field_name" => "regdate2",
		"width" => 30,
		"halign" => "center",
	),
	array(
		"header_name" => "대상",
		"field_name" => "target_name",
		"width" => 60,
		"halign" => "left",
	),
	array(
		"header_name" => "대상 코드",
		"field_name" => "table_idx1",
		"width" => 40,
	),
	array(
		"header_name" => "작업자",
		"field_name" => "member_id",
		"width" => 32,
	),
	array(
		"header_name" => "비고",
		"field_name" => "action_type",
		"width" => 40,
		"halign" => "left",
	),
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
$header_ary_euckr = array();
foreach($xls_header as $hh)
{
	$header_ary[] = $hh["header_name"];
	//$header_ary_euckr[] = iconv("cp949", "UTF-8", $hh["header_name"]);
	//$header_ary_euckr[] = iconv("UTF-8", "cp949", $hh["header_name"]);
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

	$table_nm = $row["table_nm"];
	$table_idx2 = $row["table_idx2"];
	$target_name = $row["target_name"];

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["field_name"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";

		if($table_nm == "DY_PRODUCT_OPTION" && $field_name == "table_idx1"){
			$cellValue = "[" . $cellValue. "] [" . $table_idx2 . "]";
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
$user_filename = "정보변경이력_".$xls_name.".xlsx";
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
$_SESSION["ChangeLogViewerXls"] = "";

ob_end_clean();
?>
