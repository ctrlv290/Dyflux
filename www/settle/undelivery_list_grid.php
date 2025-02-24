<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 미출고요약표 리스트 JSON
 */
//Page Info
$pageMenuIdx = 143;
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

$order_by = "Z.product_option_idx desc";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_progress_step",
	"order_is_hold",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
	"product_name",
	"product_option_name"
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
				$qryWhereAry[] = " A.order_progress_step = N'" . $val . "'";
			}elseif(trim($col) == "search_column") {
				if(in_array($val, $available_val)) {
					if ($val == "order_idx") {
						$qryWhereAry[] = $val . " = N'" . trim($_search_paramAryList["search_keyword"]) . "'";
					} else {
						if (trim($_search_paramAryList["search_keyword"]) != "") {
							$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
						}
					}
				}
			}elseif($col == "order_is_hold"){
				$qryWhereAry[] = " order_is_hold = N'" . $val . "'";
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
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx = '" . $_search_paramAryList["supplier_idx"]. "'";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx = '" . $_search_paramAryList["seller_idx"] . "'";
	}

	//합포여부
	if($_search_paramAryList["order_is_pack"]){
		if($_search_paramAryList["order_is_pack"] == "Y"){

			$qryWhereAry[] = " A.order_idx in (
				Select order_idx
				From DY_ORDER
				Where order_is_del = N'N'
				Group by order_idx
				Having count(order_idx) > 1
			)
			";

		}else{

			$qryWhereAry[] = " A.order_idx in (
				Select order_idx
				From DY_ORDER
				Where order_is_del = N'N'
				Group by order_idx
				Having count(order_idx) = 1
				)
			";
		}

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
$args['qry_table_idx'] 	= "Z.product_option_idx";
$args['qry_get_colum'] 	= " 
							PP.product_name, OO.product_option_name
							,STT.stock_amount_NORMAL
							,STT.stock_amount_BAD
							
";

if(isDYLogin()){
	$args['qry_get_colum'] 	.= " 
							, SP.supplier_name, PP.product_supplier_name, Z.*
	";
}else{
	$args['qry_get_colum'] 	.= " 
							, Z.product_idx, Z.product_option_idx, Z.product_option_cnt
	";
}

$args['qry_table_name'] 	= " 
								(
								Select
								M.product_idx, M.product_option_idx
								, SUM(M.product_option_cnt) as product_option_cnt 
								, STOCK.stock_unit_price
								From 
								DY_ORDER A
								Inner Join DY_ORDER_PRODUCT_MATCHING M On A.order_idx = M.order_idx
								Left Outer Join DY_STOCK STOCK On STOCK.order_idx = A.order_idx And STOCK.product_option_idx = M.product_option_idx
								Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
								Left Outer Join DY_PRODUCT_OPTION O On O.product_option_idx = M.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Where A.order_is_del = N'N' And A.order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE')
								And M.order_matching_is_del = N'N'
								";
if(count($qryWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryWhereAry);
}

//벤더사 로그인일 경우
if(!isDYLogin()){
	$args['qry_table_name'] .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
}

$args['qry_table_name'] .= "
								Group by M.product_idx, M.product_option_idx, STOCK.stock_unit_price
								) as Z
								Left Outer Join DY_PRODUCT PP On PP.product_idx = Z.product_idx 
								Left Outer Join DY_PRODUCT_OPTION OO On OO.product_option_idx = Z.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = PP.supplier_idx
								Left Outer Join 
								(
								Select 
									ST.product_idx, ST.product_option_idx, ST.stock_unit_price
									, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
									, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									Group by ST.product_idx, product_option_idx, ST.stock_unit_price
								) as STT On STT.product_option_idx = Z.product_option_idx And STT.stock_unit_price = Z.stock_unit_price  	
								

";


$args['qry_where']			= " 1 = 1 ";
//if(count($qryWhereAry) > 0)
//{
//	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
//}
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