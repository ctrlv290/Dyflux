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

$order_by = "accept_date ASC, seller_name ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"market_product_no",
	"market_product_name",
	"market_product_option",
	"market_product_all",
	"order_progress_step",
	"order_matching_cs",
	"product_category_l_idx",
	"product_category_m_idx",
	"product_name",
	"product_option_name",
	"search_column",
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
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "market_product_all"){
				$qryWhereAry[] = " ( market_product_name like N'%" . trim($val) . "%' OR market_product_option like N'%" . trim($val) . "%' )";
			}elseif(trim($col) == "order_progress_step"){
				$val_ary = explode(",", $val);
				$val_ary_quote = array_map(function($val){
					return "'" . $val . "'";
				}, $val_ary);
				$val_join = implode(", ", $val_ary_quote);
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif(trim($col) == "order_matching_cs"){

				$tmpSearch = " order_idx in (
									Select order_idx From DY_ORDER_PRODUCT_MATCHING
									Where order_matching_is_del = 'N' And 
				";

				if(trim($val) == "NORMAL"){
					$tmpSearch .= " order_cs_status = N'NORMAL' ";
				}elseif(trim($val) == "ORDER_CANCEL_N"){
					$tmpSearch .= " order_cs_status = N'ORDER_CANCEL' And product_cancel_shipped <> N'Y' ";
				}elseif(trim($val) == "ORDER_CANCEL_Y"){
					$tmpSearch .= " order_cs_status = N'ORDER_CANCEL' And product_cancel_shipped = N'Y' ";
				}elseif(trim($val) == "PRODUCT_CHANGE_N"){
					$tmpSearch .= " order_cs_status = N'PRODUCT_CHANGE' And product_change_shipped <> N'Y' ";
				}elseif(trim($val) == "PRODUCT_CHANGE_Y"){
					$tmpSearch .= " order_cs_status = N'PRODUCT_CHANGE' And product_change_shipped = N'Y' ";
				}elseif(trim($val) == "ORDER_CANCEL"){
					$tmpSearch .= " order_cs_status = N'ORDER_CANCEL' ";
				}elseif(trim($val) == "PRODUCT_CHANGE"){
					$tmpSearch .= " order_cs_status = N'PRODUCT_CHANGE'";
				}elseif(trim($val) == "NORMAL_PRODUCT_CHANGE"){
					$tmpSearch .= " ( order_cs_status = N'NORMAL' Or order_cs_status = N'PRODUCT_CHANGE' )";
				}

				$tmpSearch .= ")";
				$qryWhereAry[] = $tmpSearch;

			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		if($_search_paramAryList["period_type"] == "accept_date") {
			$qryWhereAry[] = "	 
				O.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
				And O.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			";
		}elseif($_search_paramAryList["period_type"] == "invoice_date") {
			$qryWhereAry[] = "	 
				O.invoice_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
				And O.invoice_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			";
		}elseif($_search_paramAryList["period_type"] == "shipping_date") {
			$qryWhereAry[] = "	 
				O.shipping_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
				And O.shipping_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			";
		}elseif($_search_paramAryList["period_type"] == "cancel_date") {
			$qryWhereAry[] = "	 
				M.product_cancel_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
				And M.product_cancel_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			";
		}
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
$args['qry_table_idx'] 	= "A.accept_date";
$args['qry_get_colum'] 	= " 
							A.*
							, S.seller_name
";
$args['qry_table_name'] 	= " 
								(
								Select
									settle_date as accept_date
									, O.seller_idx
									, market_product_no, market_product_name, market_product_option
									, count(distinct order_idx) as order_cnt
									, SUM(product_option_cnt) as order_product_cnt
									, SUM(settle_sale_supply) as product_option_sale_price
									, SUM(settle_purchase_supply) as product_option_purchase_price
									, SUM(settle_delivery_in_vat) as delivery_fee
									, SUM(settle_sale_supply) - SUM(settle_sale_commission_ex_vat) as order_calculation_amt
								From DY_SETTLE O
								Where settle_is_del = N'N'
									And settle_type in (N'SHIPPED', N'CANCEL', N'EXCHANGE')
";
if(count($qryWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_table_name'] 	.= "
								Group by settle_date, O.seller_idx, market_product_no, market_product_name, market_product_option 
								) as A
								Left Outer Join DY_SELLER S On S.seller_idx = A.seller_idx
";
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
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>