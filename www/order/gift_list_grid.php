<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사은품 목록 리스트 Grid
 */
//Page Info
$pageMenuIdx = 87;
//Init
include_once "../_init_.php";

$last_member_idx = $GL_Member["member_idx"];

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

$order_by = "gift_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
);

$available_search_col = array(
	"gift_name",
);

$qryWhereAry = array();
$qryOrderWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);
		$col = trim($col);
		$val = trim($val);
		if(trim($val) && in_array($col, $available_col)) {
			if(
				$col == "order_cnt"
				|| $col == "order_progress_step"
				|| $col == "order_seller_cancel_confirm"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif($col == "search_column"){
				if(in_array($val, $available_search_col) && trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}
		}
	}

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "
			A.gift_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And gift_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59'
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = " supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}
	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = " seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	}

}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

//엑셀다운로드 시 표시 행수 무한
if($gridPrintForExcelDownload) {
	// paging set
	$args['show_row'] 	= 9999;
	$args['show_page'] 	= 9999;
}

// make select query
$args['qry_table_idx'] 	= "A.gift_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, S.seller_name
							, M.member_id
							, Convert(varchar(30), A.gift_regdate, 120) as gift_regdate2
							, Convert(varchar(30), A.gift_moddate, 120) as gift_moddate2
							, (Select isNull(Sum(product_option_cnt), 0) From DY_ORDER_PRODUCT_MATCHING M Where M.gift_idx = A.gift_idx And M.order_matching_is_del = N'N') as use_cnt
							, Case When A.gift_status = 'N' Then '준비중'
								When A.gift_status = 'Y' Then '진행중'
								When A.gift_status = 'X' Then '종료'
							End as gift_status_han
";
$args['qry_table_name'] 	= " 
								DY_ORDER_GIFT A
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER M On A.gift_regidx = M.idx
";
$args['qry_where'] = " A.gift_is_del = N'N' ";
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

$userdata = array();

$userdata["date_start"] = $date_start;
$userdata["date_end"] = $date_end;

$_list = $WholeGetListResult['listRst'];


$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
$grid_response["rows"] = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>