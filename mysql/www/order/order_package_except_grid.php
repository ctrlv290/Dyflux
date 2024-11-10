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

$order_by = " A.order_pack_idx ASC, A.order_idx ASC  ";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_cnt_start",
	"order_cnt_end",
	"product_option_cnt_start",
	"product_option_cnt_end",
	"include_single",
	"include_soldout",
	"include_soldout_temp",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"P.product_name",
	"PO.product_option_name",
);

//단품주문포함 Flag
$searchSingleOrder = false;

$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_cnt_start") {
				$qryWhereAry[] = " order_cnt >= N'".$val."'";
			}elseif(trim($col) == "order_cnt_end"){
				$qryWhereAry[] = " order_cnt <= N'".$val."'";
			}elseif(trim($col) == "product_option_cnt_start"){
				$qryWhereAry[] = " PM.product_option_cnt <= N'".$val."'";
			}elseif(trim($col) == "product_option_cnt_end"){
				$qryWhereAry[] = " PM.product_option_cnt <= N'".$val."'";
			}elseif(trim($col) == "include_single"){
				$searchSingleOrder = true;
			}elseif(trim($col) == "include_soldout"){
				$qryWhereAry[] = " PO.product_option_soldout = N'Y'";
			}elseif(trim($col) == "include_soldout_temp"){
				$qryWhereAry[] = " PO.product_option_soldout_temp = N'Y'";
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
				//$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["hour_start"] . ":00:00'
			And order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["hour_end"] . ":59:59'
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
							, OPM.order_matching_idx, OPM.product_option_cnt
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, S.supplier_name
							, SL.seller_name
							, IFNULL((Select
								Sum(stock_amount * stock_type) as stock_amount_NORMAL
								From DY_STOCK S
								Where S.product_option_idx = PO.product_option_idx 
										And S.stock_is_del = N'N'
										And S.stock_is_confirm = N'Y'
										And S.stock_status = 'NORMAL'
							), 0) as current_stock_amount
							, ROW_NUMBER() OVER(PARTITION BY A.order_idx Order by OPM.order_matching_idx ASC) as inner_no
                            ";

$args['qry_table_name'] 	= " 

								DY_ORDER A
								Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
";

$args['qry_where']			= " A.order_progress_step = N'ORDER_ACCEPT' And A.order_is_del = N'N' And OPM.order_matching_is_del = N'N' ";

//단품 주문 포함이 아닐 경우
if(!$searchSingleOrder){
	//$args['qry_where']	    .= " And order_pack_cnt > 1 ";
	$args['qry_where']	    .= " And A.order_pack_idx in (
									Select order_pack_idx 
									From DY_ORDER OO 
									Inner Join DY_ORDER_PRODUCT_MATCHING OOP On OO.order_idx = OOP.order_idx 
									Where 
										OO.order_is_del = N'N' 
										And OOP.order_matching_is_del = N'N' 
										And OO.order_progress_step = N'ORDER_ACCEPT' 
										And OOP.product_option_cnt > 1
	) ";
}
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
$WholeGetListResult['sortLink'][];
*/
//******************************* 리스트 기본 설정 끝 ******************************//
//print_r($WholeGetListResult);

//판매금액 합산
$C_Dbconn = new DBConn();
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