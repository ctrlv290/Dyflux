<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별송장등록 리스트 JSON
 */
//Page Info
$pageMenuIdx = 80;
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

$order_by = "A.order_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_progress_step",
	"delivery_code",
	"A.order_idx",
	"A.market_order_no",
	"A.invoice_no",
	"A.receive_name",
	"A.receive_hp_num",
	"A.receive_addr1",
	"A.market_product_name",
	"A.market_product_option",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"A.order_idx"
);
$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_progress_step") {
				$val_ary       = explode(",", $val);
				$val_ary_quote = array_map(function ($val) {
					return "'" . $val . "'";
				}, $val_ary);
				$val_join      = implode(", ", $val_ary_quote);
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif(trim($col) == "delivery_code"){
				$qryWhereAry[] = " A.delivery_code = N'" . $val . "'";
			}elseif(trim($col) == "search_column" && in_array($val, $available_val)){
				if($val == "order_idx")
				{
					$qryWhereAry[] = $val . " = N'" . trim($_search_paramAryList["search_keyword"]) . "'";
				}else{
					if(trim($_search_paramAryList["search_keyword"]) != "") {
						$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
					}
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["period_type"] == "order_accept_regdate"){
		$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "'
		";
	}elseif($_search_paramAryList["period_type"] == "shipping_date"){
		$qryWhereAry[] = "	 
			A.shipping_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.shipping_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "' 
		";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = " A.seller_idx  = N'" .$_search_paramAryList["seller_idx"]. "'";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = " P.supplier_idx  = N'" .$_search_paramAryList["supplier_idx"]. "'";
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
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, D.delivery_name
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
								Left Outer Join (
									Select distinct delivery_code, delivery_name
									From DY_DELIVERY_CODE
									Where market_code = 'DY'
								) D On D.delivery_code = A.delivery_code
";
$args['qry_where']			= " A.order_is_del = N'N' And OPM.order_matching_is_del = N'N' And A.order_progress_step in (N'ORDER_INVOICE', N'ORDER_SHIPPED') ";
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

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
$grid_response["rows"]     = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>