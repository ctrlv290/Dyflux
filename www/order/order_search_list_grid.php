<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 확장주문검색 리스트 JSON
 */
//Page Info
$pageMenuIdx = 77;
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
	"order_cs_status",
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

		$col = trim($col);
		$val = trim($val);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_progress_step") {
				$val_ary       = explode(",", $val);
				$val_ary_quote = array_map(function ($val) {
					return "'" . $val . "'";
				}, $val_ary);
				$val_join      = implode(", ", $val_ary_quote);
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif($col == "order_cs_status"){
				if($val == "NORMAL") {
					$qryWhereAry[] = " OPM.order_matching_idx IN (
						Select C_OP.order_matching_idx 
						From DY_ORDER_PRODUCT_MATCHING C_OP  
						Where C_OP.order_cs_status = N'NORMAL'
					)";
				}elseif($val == "EXCEPT_PART_CANCEL") {
					$qryWhereAry[] = " A.order_idx not  IN (
						Select C_OP.order_idx 
						From DY_ORDER_PRODUCT_MATCHING C_OP 
						Where C_OP.order_cs_status = N'ORDER_CANCEL'
					)";
				}
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

	if($_search_paramAryList["period_type"] == "order_regdate"){
		$qryWhereAry[] = "	 
			A.order_regdate >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.order_regdate <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "'
		";
	}elseif($_search_paramAryList["period_type"] == "order_accept_regdate"){
		$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "' 
		";
	}elseif($_search_paramAryList["period_type"] == "invoice_date"){
		$qryWhereAry[] = "	 
			A.invoice_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.invoice_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "' 
		";
	}elseif($_search_paramAryList["period_type"] == "shipping_date"){
		$qryWhereAry[] = "	 
			A.shipping_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.shipping_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "' 
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
							, OPM.product_option_cnt
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, S.supplier_name
							, SL.seller_name
							, isNull((Select
								Sum(stock_amount * stock_type) as stock_amount_NORMAL
								From DY_STOCK S
								Where S.product_option_idx = PO.product_option_idx 
										And S.stock_is_del = N'N'
										And S.stock_is_confirm = N'Y'
										And S.stock_status = 'NORMAL'
							), 0) as current_stock_amount
							, C.code_name as order_progress_step_han
							, (Select code_name From DY_CODE DCC WITH (NOLOCK) Where DCC.parent_code = N'ORDER_MATCHING_CS' And DCC.code = OPM.order_cs_status) as order_cs_status_han
                            ";

$args['qry_table_name'] 	= " DY_ORDER A WITH (NOLOCK) 
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM WITH (NOLOCK) On A.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P  WITH (NOLOCK) On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO  WITH (NOLOCK) On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S  WITH (NOLOCK) On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL  WITH (NOLOCK) On SL.seller_idx = A.seller_idx
								Left Outer Join DY_CODE C  WITH (NOLOCK) On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
";
$args['qry_where']			= " A.order_is_del = N'N' 
								And OPM.order_matching_is_del = N'N' 
								And A.order_progress_step not in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING') 
							";
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

//판매금액 합산
$C_Dbconn = new Dbconn();
$qry = "
	Select 
	Sum(order_amt) as order_amt_sum
	, Sum(order_calculation_amt) as order_calculation_amt_sum
";
$qry .= " From ";
$qry .= $args['qry_table_name'];
$qry .= " Where ";
$qry .= $args['qry_where'];

$C_Dbconn->db_connect();
$summary_result = $C_Dbconn->execSqlOneRow($qry);
$C_Dbconn->db_close();

$userdata["order_amt_sum"] = $summary_result["order_amt_sum"];
$userdata["order_calculation_amt_sum"] = $summary_result["order_calculation_amt_sum"];

$qry = "
	Select 
	isNull(Sum(Case When order_progress_step = 'ORDER_SHIPPED' Then 1 Else 0 End), 0) as sum_shipped
	, count(distinct A.order_idx) as cnt_order
	, Sum(OPM.product_option_cnt) as sum_product_option_cnt
";
$qry .= " From ";
$qry .= $args['qry_table_name'];
$qry .= " Where ";
$qry .= $args['qry_where'];

$C_Dbconn->db_connect();
$summary_result2 = $C_Dbconn->execSqlOneRow($qry);
$C_Dbconn->db_close();

$userdata["sum_shipped"] = $summary_result2["sum_shipped"];
$userdata["cnt_order"] = $summary_result2["cnt_order"];
$userdata["sum_product_option_cnt"] = $summary_result2["sum_product_option_cnt"];

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