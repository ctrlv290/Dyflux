<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자 리스트 엑셀 다운로드
 *
 */
//Page Info
$pageMenuIdx = 56;
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
$avaliable_col = array(
	"nameid"
);
foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $avaliable_col)) {
		if($col == "nameid")
		{
			$qryWhereAry[] = " ( name like N'%" . $val . "%' Or A.member_id like N'%" . $val . "%')";
		}else {
			$qryWhereAry[] = $col . " like N'%" . $val . "%'";
		}
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
$args['qry_table_idx'] 	= "A.idx";
$args['qry_get_colum'] 	= " A.idx, A.member_id, A.member_type, A.is_use, A.regdate, U.name, U.tel, U.mobile, U.email, U.etc,
                            (Case When member_type = 'user' Then '" . iconv("euc-kr", "utf-8", "사용자") ."'
                                When member_type = 'admin' Then '" . iconv("euc-kr", "utf-8", "관리자") ."'
                            End) as member_type_han
                            ";

$args['qry_table_name'] 	= " DY_MEMBER A Left Outer Join DY_MEMBER_USER U On A.idx = U.member_idx ";
$args['qry_where']			= " A.is_del = 'N' And A.member_type = 'USER' ";
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
$WholeGetListResult['searchForm'];
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
	"사용자코드"      => "idx",
	"이름"            => "name",
	"로그인 아이디"   => "member_id",
	"등급"            => "member_type_han",
	"연락처"          => "tel",
	"휴대폰번호"      => "mobile",
	"이메일"          => "email",
	"비고"            => "etc",
	"사용여부"        => "is_use",

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
foreach($WholeGetListResult['listRst'] as $row) {
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
$user_filename = "사용자목록.xlsx";
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