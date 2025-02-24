<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처상품별통계 리스트 JSON
 */
//Page Info
$pageMenuIdx = 130;
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

$order_by = "A.product_option_cnt DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"seller_idx",
	"search_column",
);
$available_search_col = array(
	"product_name",
	"product_option_name",
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
		if($val && in_array($col, $available_col)) {
			if(
				$col == "seller_idx"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif($col == "search_column" And in_array($val, $available_search_col)){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$date_start = $_search_paramAryList["date_start"];
		$date_end = $_search_paramAryList["date_end"];
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "seller_idx = N'".$_search_paramAryList["seller_idx"]."'";
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
$args['qry_table_idx'] 	= "A.product_option_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, P.product_img_main
							, P.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_1) as product_img_filename_1
							, P.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_2) as product_img_filename_2
							, P.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_3) as product_img_filename_3
							, P.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_4) as product_img_filename_4
							, P.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_5) as product_img_filename_5
							, P.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_6) as product_img_filename_6
							, P.product_name
							, PO.product_option_name
							, isNull(S.stock_NORMAL, 0) as stock_NORMAL
							, MS.supplier_name
";
$args['qry_table_name'] 	= " 
								(
									Select 
										product_idx, product_option_idx, sum(product_option_cnt) as product_option_cnt
										, settle_purchase_unit_supply, product_option_sale_price, product_sale_type, sum(settle_sale_profit) AS settle_sale_profit
									From DY_SETTLE
									Where settle_is_del = N'N'
									AND settle_type != N'AD_COST_CHARGE'
									And settle_date between N'$date_start' And N'$date_end'
							";
if(count($qryWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_table_name'] 	.= " 
									Group by product_idx, product_option_idx, settle_purchase_unit_supply, product_option_sale_price, product_sale_type
								) as A
								Left Outer Join DY_PRODUCT P On P.product_idx = A.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = A.product_option_idx
								Left Outer Join (
									Select 
										isNull(Sum(stock_amount * stock_type), 0) as stock_NORMAL
										, product_idx, product_option_idx, stock_unit_price
									From DY_STOCK
									Where stock_is_del = N'N' And stock_status = N'NORMAL'
											And stock_is_confirm = N'Y'
									Group by product_idx, product_option_idx, stock_unit_price
								) as S On S.product_idx = A.product_idx And S.product_option_idx = A.product_option_idx And S.stock_unit_price = A.settle_purchase_unit_supply
								Left Outer Join DY_MEMBER_SUPPLIER MS On MS.member_idx = P.supplier_idx
								
";
if(count($qryWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_where']			= "";

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


$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
$grid_response["rows"] = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>