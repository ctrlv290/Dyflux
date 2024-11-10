<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭정보조회 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 66;

//Euc-Kr 사용 설정
$GL_Enable_EUCKR = true;
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

$order_by = "A.matching_info_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}
$order_by .= ", L.matching_list_idx ASC";

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"market_product_name",
	"market_product_option",
	"product_name",
	"product_option_name",
);
$qryWhereAry = array();
///?date_start=2019-03-14&date_end=2019-04-12&product_seller_group_idx=0&product_supplier_group_idx=0&search_column=market_product_name&search_keyword=

if(in_array($_GET["search_column"], $available_col)){
	if(trim($_GET["search_keyword"])){
		$qryWhereAry[] = trim($_GET["search_column"]) . " like N'%".trim($_GET["search_keyword"])."%'";
	}
}

$date_search_col = "";
if($_GET["date_start"] != "" && $_GET["date_end"] != ""){
	$qryWhereAry[] = "	 
		A.matching_info_regdate >= '".$_GET["date_start"]." 00:00:00' 
		And A.matching_info_regdate <= '".$_GET["date_end"]." 23:59:59' 
	";
}

//판매처
if($_GET["seller_idx"]){
	$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_GET["supplier_idx"]) . ")";
}

//공급처
if($_GET["supplier_idx"]){
	$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_GET["supplier_idx"]) . ")";
}


// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= 9999;
$args['show_page'] 	= 9999;

// make select query
$args['qry_table_idx'] 	= "A.matching_info_idx";
$args['qry_get_colum'] 	= " A.matching_info_idx
							, A.seller_idx, S.seller_name
							, A.market_product_no, A.market_product_name, A.market_product_option, A.matching_info_regdate
							, L.product_option_cnt
							, P.supplier_idx, SP.supplier_name
							, P.product_idx, P.product_name, O.product_option_name 
							, A.member_idx
							, (Select member_id From DY_MEMBER M Where A.member_idx = M.idx) as member_id
							, L.matching_list_idx
                            ";

$args['qry_table_name'] 	= " DY_PRODUCT_MATCHING_INFO A 
								Left Outer Join DY_PRODUCT_MATCHING_LIST L On A.matching_info_idx = L.matching_info_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = L.product_idx
								Left Outer Join DY_PRODUCT_OPTION O On O.product_option_idx = L.product_option_idx
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
";
$args['qry_where']			= " A.matching_info_is_del = N'N' ";
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

$totalRows = $WholeGetListResult["pageInfo"]["total"];


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
	"매칭일련번호"      => "matching_info_idx",
	"판매처"            => "seller_name",
	"판매처 상품코드"   => "market_product_no",
	"판매처 상품명"     => "market_product_name",
	"판매처 옵션"       => "market_product_option",
	"공급처"            => "supplier_name",
	"상품코드"          => "product_idx",
	"상품명"            => "product_name",
	"옵션명"            => "product_option_name",
	"수량"              => "product_option_cnt",
	"등록일"            => "matching_info_regdate",
	"등록계정"          => "member_id",
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

//세로 정렬 가운데
$style = array(
	'alignment' => array(
		'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
	)
);

$activesheet->getStyle('A1:'.$xls_header_end.($totalRows+1))->applyFromArray($style);

//문자열입력서식
//$activesheet->getStyle('A2:'.$xls_header_end.$totalRows)->setQuotePrefix(true);

//List Apply
$startColumn = "A";
$i = 2;
foreach($WholeGetListResult['listRst'] as $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $col) {
		$xls_row[] = $row[$col];
	}

	$currentColumn = $startColumn;
	foreach ($xls_row as $cellValue) {
		$activesheet->setCellValueExplicit($currentColumn.$i, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		++$currentColumn;
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$i++;
}

/*
foreach(range('A', $xls_header_end) as $columnID) {
	$activesheet->getColumnDimension($columnID)->setAutoSize(true);
}
$activesheet->calculateColumnWidths();
*/


//동일한 내용 셀 합치기
//이전 Row의 Key와 비교해서 같다면 셀 합병
//Key : matching_info_idx
//대상 셀 : 판매처(B), 판매처 상품코드(C), 판매처 상품명(D), 판매처 옵션(E), 등록일(K), 등록계정(L)
$matching_info_idx_prev = 0;
$rowSpan = 0;
$xlsTotalRow = $totalRows + 1;

//셀합치기를 위하여 Row 수 보다 +1 더 확인한다
$xlsTotalRow = $xlsTotalRow + 1;
foreach(range(2, $xlsTotalRow) as $row){
	if($row <= $xlsTotalRow)
	{
		$current_idx = $WholeGetListResult['listRst'][$row - 2]["matching_info_idx"];
	}else{
		$current_idx = "";
	}

	if($matching_info_idx_prev != $current_idx){
		$matching_info_idx_prev = $current_idx;
		if($rowSpan > 0){
			$prevRowNum = $row - 1;
			$activesheet->mergeCells('B'.($prevRowNum-$rowSpan).':B'.$prevRowNum);
			$activesheet->mergeCells('C'.($prevRowNum-$rowSpan).':C'.$prevRowNum);
			$activesheet->mergeCells('D'.($prevRowNum-$rowSpan).':D'.$prevRowNum);
			$activesheet->mergeCells('E'.($prevRowNum-$rowSpan).':E'.$prevRowNum);
			$activesheet->mergeCells('K'.($prevRowNum-$rowSpan).':K'.$prevRowNum);
			$activesheet->mergeCells('L'.($prevRowNum-$rowSpan).':L'.$prevRowNum);
			$activesheet->mergeCells('M'.($prevRowNum-$rowSpan).':M'.$prevRowNum);
			$activesheet->mergeCells('N'.($prevRowNum-$rowSpan).':N'.$prevRowNum);
			$rowSpan = 0;
		}
	}else{
		$rowSpan++;
	}
}


//$activesheet->mergeCells('B2:B4');

//A열 숨김!!
//$activesheet->getColumnDimension('A')->setVisible(false);
//Or
//A열삭제!!
$activesheet->removeColumn('A');

//셀너비 설정
//상단에서 setAutoSize : true 시에는 너비 설정이 반영되지 않음
$cellWidthAry = array(
	"A" => 20   //판매처
	, "B" => 20   //판매처 상품코드
	, "C" => 20   //판매처 상품명
	, "D" => 20   //판매처 옵션
	, "E" => 20   //공급처
	, "F" => 12   //상품코드
	, "G" => 30   //상품명
	, "H" => 30   //옵션명
	, "I" => 10   //수량
	, "J" => 20   //등록일
	, "K" => 12   //등록계정
);
foreach($cellWidthAry as $cellName => $widthVal) {
	$activesheet->getColumnDimension($cellName)->setWidth($widthVal);
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
$user_filename = "매칭정보조회.xlsx";
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