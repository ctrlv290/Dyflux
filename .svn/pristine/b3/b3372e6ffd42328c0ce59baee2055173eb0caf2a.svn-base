<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 리스트 엑셀 다운로드
 *
 */
//Page Info
$pageMenuIdx = 43;
//Permission IDX
$pagePermissionIdx = 43;

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

$qryWhereAry = array();

//검색 가능한 컬럼 지정
$available_col = array(
	"seller_is_use",
	"manage_group_idx",
	"seller_name"
);
foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $available_col)) {
		$qryWhereAry[] = $col . " like N'%" . $val . "%'";
	}
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 9999;
$args['show_page'] 	= 9999;

// make select query
$args['qry_table_idx'] 	= "A.seller_idx";
$args['qry_get_colum'] 	= " A.*
							, Case When seller_type = 'MARKET_SELLER' Then
									(Select code_name From DY_CODE C Where C.parent_code = 'MARKET_SELLER' And C.code = A.market_code)
								When seller_type = 'CUSTOM_SELLER' Then
									(Select code_name From DY_CODE C Where C.parent_code = 'CUSTOM_SELLER' And C.code = A.market_code)
								End as market_name
							, (Select manage_group_name From DY_MANAGE_GROUP B Where A.manage_group_idx = B.manage_group_idx) as manage_group_name
                            ";

$args['qry_table_name'] 	= " DY_SELLER A ";
$args['qry_where']			= " A.seller_is_del = 'N' And A.seller_type != 'VENDOR_SELLER' ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= "";
$args['qry_orderby']		= "A.seller_idx";

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;


$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));
/*
$WholeGetListResult['listRst'];
$WholeGetListResult['pageInfo'][''];
array("startpage"=>$startpage,"endpage"=>$endpage,"prevpage"=>$prevpage,"nextpage"=>$nextpage,"total"=>$total,"searchVar"=>$searchVar,"totalpages"=>$totalpages);
$WholeGetListResult['listPageLink'];
$WholeGetListResult['sortLink'][];
*/
//******************************* 리스트 기본 설정 끝 ******************************//
//print_r($WholeGetListResult);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

//$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//$writer->save("판매처_목록.xlsx");

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();


//Header 세팅
$xls_header = array(
	"판매처코드",
	"판매처타입", "판매처",
	"판매처명", "판매처 그룹",
	"로그인 아이디", "로그인 비밀번호",
	"보안코드", "보안코드2",
	"관리자 URL", "관리자 URL", "관리자 URL",
	"자동발주 사용", "송장출력 - 상품명", "송장출력 - 옵션",
	"API 사용여부",
	"사용여부"
);
$xls_header_end = getNameFromNumber(count($xls_header)-1);


$header_ary = array_values($xls_header);
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
foreach($WholeGetListResult['listRst'] as $row) {
	$xls_row = array();
	$xls_row[] = $row["seller_idx"];
	$xls_row[] = $row["seller_type"];
	$xls_row[] = $row["market_name"];
	$xls_row[] = $row["seller_name"];
	$xls_row[] = $row["manage_group_name"];
	$xls_row[] = $row["market_login_id"];
	$xls_row[] = $row["market_login_pw"];
	$xls_row[] = $row["market_auth_code"];
	$xls_row[] = $row["market_auth_code2"];
	$xls_row[] = $row["market_admin_url"];
	$xls_row[] = $row["market_mall_url"];
	$xls_row[] = $row["market_product_url"];
	$xls_row[] = $row["seller_auto_order"];
	$xls_row[] = $row["seller_invoice_product"];
	$xls_row[] = $row["seller_invoice_option"];
	$xls_row[] = $row["seller_use_api"];
	$xls_row[] = $row["seller_is_use"];

	$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$i++;
}

foreach(range('A', $xls_header_end) as $columnID) {
	$activesheet->getColumnDimension($columnID)->setAutoSize(true);
}

$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/

function mb_basename($path) { return end(explode('/',$path)); }
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}
$user_filename = "판매처_목록.xlsx";
//if (is_ie()) $user_filename = utf2euc($user_filename);
if (is_ie()) $user_filename = urlencode($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');

ob_end_clean();
$Excel_writer->save('php://output');



?>