<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문다운로드 - 공급처별 주문현황 리스트 JSON
 */
//Page Info
$pageMenuIdx = 78;
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

$order_by = "S.supplier_name ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"receive_name",
	"delivery_type",
	"order_progress_step",
);
$available_search_col = array(

);
$qryWhereAry = array();
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
			if(trim($col) == "search_column") {
				if (trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}elseif($col == "delivery_type" || $col == "order_progress_step"){
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			O.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And O.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "'
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		//$qryWhereAry[] = "S.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
		$qryWhereAry[] = "S.member_idx = N'".$_search_paramAryList["supplier_idx"]."'";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		//$qryWhereAry[] = "O.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
		$qryWhereAry[] = "O.seller_idx = N'".$_search_paramAryList["seller_idx"]."'";
	}
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "G.manage_group_idx";
$args['qry_get_colum'] 	= " 
							G.manage_group_idx, G.manage_group_name 
							, S.member_idx, S.supplier_name
							, count(*) as order_cnt
							, Sum(OPM.product_option_cnt) as option_cnt
							, Sum(Case When O.order_idx <> O.order_pack_idx Then 1 Else 0 End)  as package_cnt
                            ";

$args['qry_table_name'] 	= " 
								DY_ORDER O
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = O.seller_idx
								Left Outer Join DY_MANAGE_GROUP G On G.manage_group_idx = S.manage_group_idx
";
$args['qry_where']			= " O.order_is_del = N'N' And OPM.order_matching_is_del = N'N' And O.order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED') ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= " G.manage_group_idx, G.manage_group_name, S.member_idx, S.supplier_name ";
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

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"]     = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>