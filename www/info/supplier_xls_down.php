<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처 리스트 엑셀 다운로드
 *
 */
//Page Info
$pageMenuIdx = 49;
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
	"is_use",
	"supplier_status",
	"manage_group_idx",
	"supplier_name",
);
foreach($_GET as $col => $val) {
	if(trim($val) && in_array($col, $avaliable_col)) {
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
$args['qry_table_idx'] 	= "A.idx";
$args['qry_get_colum'] 	= " A.idx, A.member_id, A.regdate, A.is_use
							, V.supplier_name, V.supplier_ceo_name, V.supplier_license_number
							, V.supplier_fax, V.supplier_startdate, V.supplier_enddate
                            , V.supplier_officer1_name, V.supplier_officer1_tel, V.supplier_officer1_mobile, V.supplier_officer1_email
                            , V.supplier_officer2_name, V.supplier_officer2_tel, V.supplier_officer2_mobile, V.supplier_officer2_email
                            , V.supplier_officer3_name, V.supplier_officer3_tel, V.supplier_officer3_mobile, V.supplier_officer3_email
                            , V.supplier_officer4_name, V.supplier_officer4_tel, V.supplier_officer4_mobile, V.supplier_officer4_email
                            , V.supplier_email_default, V.supplier_bank_account_number, V.supplier_bank_name, V.supplier_bank_holder_name
                            , V.supplier_zipcode, V.supplier_addr1, V.supplier_addr2, V.supplier_md, V.supplier_etc
                            , (Select manage_group_name From DY_MANAGE_GROUP B Where V.manage_group_idx = B.manage_group_idx) as manage_group_name
                            ";

$args['qry_table_name'] 	= " DY_MEMBER A Left Outer Join DY_MEMBER_SUPPLIER V On A.idx = V.member_idx ";
$args['qry_where']			= " A.is_del = 'N' And A.member_type = 'SUPPLIER' ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= "";
$args['qry_orderby']		= "A.idx";

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
	"공급처코드"      => "idx",
	"공급처 명"       => "supplier_name",
	"공급처 그룹"     => "manage_group_name",
	"로그인 아이디"   => "member_id",
	"대표이사"        => "supplier_ceo_name",
	"사업자등록번호"  => "supplier_license_number",
	"주소-우편번호"   => "supplier_zipcode",
	"주소-기본주소"   => "supplier_addr1",
	"주소-상세주소"   => "supplier_addr2",
	"팩스번호"        => "supplier_fax",
	"거래시작일"      => "supplier_startdate",
	"거래종료일"      => "supplier_enddate",
	"계좌번호"        => "supplier_bank_account_number",
	"은행명"          => "supplier_bank_name",
	"예금주"          => "supplier_bank_holder_name",
	"대표 이메일"     => "supplier_email_default",
	"회계용 이메일"   => "supplier_email_account",
	"발주용 이메일"   => "supplier_email_order",
	"담당자"          => "supplier_officer1_name",
	"연락처"          => "supplier_officer1_tel",
	"휴대폰번호"      => "supplier_officer1_mobile",
	"이메일"          => "supplier_officer1_email",
	"담당자2"         => "supplier_officer2_name",
	"연락처2"         => "supplier_officer2_tel",
	"휴대폰번호2"     => "supplier_officer2_mobile",
	"이메일2"         => "supplier_officer2_email",
	"담당자3"         => "supplier_officer3_name",
	"연락처3"         => "supplier_officer3_tel",
	"휴대폰번호3"     => "supplier_officer3_mobile",
	"이메일3"         => "supplier_officer3_email",
	"담당자4"         => "supplier_officer4_name",
	"연락처4"         => "supplier_officer4_tel",
	"휴대폰번호4"     => "supplier_officer4_mobile",
	"이메일4"         => "supplier_officer4_email",
	"담당MD"          => "supplier_md",
	"비고"            => "supplier_etc",
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
$user_filename = "공급처_목록.xlsx";
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