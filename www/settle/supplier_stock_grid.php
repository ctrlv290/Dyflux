<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처별정산(재고) 리스트 JSON
 */
//Page Info
$pageMenuIdx = 129;
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

$order_by = "supplier_name ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

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
			if(false){
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
		$qryStockConfirmDate = "	 
				And stock_is_confirm_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
				And stock_is_confirm_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "S.supplier_idx = N'" . $_search_paramAryList["seller_idx"] . "'";
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
$args['qry_table_idx'] 	= "S.member_idx";
$args['qry_get_colum'] 	= " 
							STOCK_IN.*
							, STOCK_OUT.*
							, S.supplier_name
							, S.member_idx as supplier_idx
";
$args['qry_table_name'] 	= " 
								
								(
								Select
									P.supplier_idx as i_supplier_idx
									, isNull(Sum(stock_unit_price * stock_amount), 0) as stock_in_sum
									, isNull(Sum(stock_amount), 0) as stock_in_amount
									, isNull(Sum(Case 
										When stock_kind = 'BACK' Then stock_unit_price * stock_amount
											Else 0 End), 0) as stock_in_back_sum
									, isNull(Sum(Case 
										When stock_kind = 'BACK' Then stock_amount
											Else 0 End), 0) as stock_in_back_amount
								From DY_STOCK S
								Left Outer Join DY_PRODUCT P On P.product_idx = S.product_idx
								Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									And stock_kind in (N'STOCK_ORDER', N'BACK')
									And stock_status in (N'NORMAL', N'ABNORMAL', N'HOLD', N'BAD', N'LOSS', N'DISPOSAL')
									$qryStockConfirmDate
								Group by P.supplier_idx
								) STOCK_IN
								Full Outer Join
								(
								Select
									P.supplier_idx as o_supplier_idx
									, isNull(Sum(stock_unit_price * stock_amount), 0) as stock_out_sum
									, isNull(Sum(stock_amount), 0) as stock_out_amount
									, isNull(Sum(Case 
										When stock_status = 'BAD_OUT_RETURN' Then stock_unit_price * stock_amount
											Else 0 End), 0) as stock_out_back_sum
									, isNull(Sum(Case 
										When stock_status = 'BAD_OUT_RETURN' Then stock_amount
											Else 0 End), 0) as stock_out_back_amount
								From DY_STOCK S
								Left Outer Join DY_PRODUCT P On P.product_idx = S.product_idx
								Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									And stock_kind in (N'STOCK_ORDER', N'BACK', N'ORDER', N'ORDER', N'MOVE')
									And stock_status in (N'SHIPPED', N'FAC_RETURN_EXCHNAGE', N'FAC_RETURN_BACK', N'BAD_OUT_EXCHANGE', N'BAD_OUT_RETURN', N'LOSS', N'DISPOSAL_PERMANENT', N'BUYER_OUT_NO_EXCHANGE', N'BUYER_OUT_NO_BACK', N'ETC')
									And stock_type = 1
									$qryStockConfirmDate
								Group by P.supplier_idx
								) STOCK_OUT On STOCK_IN.i_supplier_idx = STOCK_OUT.o_supplier_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = STOCK_IN.i_supplier_idx Or S.member_idx = STOCK_OUT.o_supplier_idx
";
$args['qry_where']			= " 1 = 1 ";

if(count($qryWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryWhereAry);
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