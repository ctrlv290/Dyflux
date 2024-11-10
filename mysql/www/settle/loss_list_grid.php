<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산예정금 관리 리스트 JSON
 */
//Page Info
$pageMenuIdx = 268;
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

$order_by = "A.loss_date ASC, A.market_order_no ASC, S.settle_date ASC";
//if($_GET["sidx"] && $_GET["sord"])
//{
//	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
//}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
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

		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			 (
				loss_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And loss_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";
	}else{
		exit;
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "  A.seller_idx = N'".$_search_paramAryList["seller_idx"]."'";
	}else{
		exit;
	}
}else{
	exit;
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
$args['qry_table_idx'] 	= "loss_idx";
$args['qry_get_colum'] 	= " 
							A.*, O.order_name as order_name2
							, S.market_order_no as market_order_no2
							, S.market_product_name as market_product_name2
							, S.market_product_option as market_product_option2
							, S.product_name
							, S.product_option_name
							, S.settle_date
							, S.product_option_cnt
							, S.settle_sale_supply
							, S.settle_delivery_in_vat
							, (S.settle_sale_supply + S.settle_delivery_in_vat) as sale_sum
							, S.settle_sale_commission_in_vat
							, S.settle_delivery_commission_in_vat
							, (S.settle_sale_supply + S.settle_delivery_in_vat) - (S.settle_sale_commission_in_vat + S.settle_delivery_commission_in_vat) as total_sum
							, S.settle_settle_amt
							, ROW_NUMBER() Over (Partition By A.seller_idx, A.market_order_no Order by A.loss_date, A.market_order_no, S.settle_date) as part_no 
";

$args['qry_table_name'] 	= " 
								DY_SETTLE_LOSS A
									Left Outer Join DY_SETTLE S On A.seller_idx = S.seller_idx And A.market_order_no = S.market_order_no
									Left Outer Join DY_ORDER O On S.order_idx = O.order_idx
							";

$args['qry_where']			= " 
								A.loss_is_del = N'N' 
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
$WholeGetListResult['sortLink'][];
*/
//******************************* 리스트 기본 설정 끝 ******************************//
//print_r($WholeGetListResult);

$userdata = array();
$userdata["period"] = date('m/d', $start_date) . " ~ " . date('m/d', $end_date);
$userdata["date_count"] = $_search_date_count;

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>