<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자산현황 리스트 Grid
 */
//Page Info
$pageMenuIdx = 257;
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

$order_by = "STOCK.product_option_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"P.product_category_l_idx",
	"P.product_category_m_idx",
	"search_column",
);

$available_search_col = array(
	"P.product_name",
	"P.product_option_name",
);

$qryWhereAry = array();
$qryStockWhereAry = array();
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
				$col == "P.product_category_l_idx"
				|| $col == "P.product_category_m_idx"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif($col == "search_column") {
				if(in_array($val, $available_search_col) && trim($_search_paramAryList["search_keyword"]) != ""){
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else {
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

	$date_search_col = "";
	if($_search_paramAryList["date"] != ""){
		$qryStockWhereAry[] = "	 
			ST.stock_is_confirm_date <= '".$_search_paramAryList["date"]." 23:59:59.997' 
		";
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
$args['qry_table_idx'] 	= "PO.product_option_idx";
$args['qry_get_colum'] 	= " 
							STOCK.*
							, P.product_name
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_l_idx), '') as category_l_name, P.product_category_l_idx 
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_m_idx), '') as category_m_name, P.product_category_m_idx
							, PO.product_option_name
							, S.supplier_name
							, P.supplier_idx
							, (STOCK.stock_amount_NORMAL + STOCK.stock_amount_ABNORMAL + STOCK.stock_amount_BAD + STOCK.stock_amount_HOLD + STOCK.stock_amount_DISPOSAL) as stock_assets_amount
							, ((STOCK.stock_amount_NORMAL + STOCK.stock_amount_ABNORMAL + STOCK.stock_amount_BAD + STOCK.stock_amount_HOLD + STOCK.stock_amount_DISPOSAL) * STOCK.stock_unit_price) as stock_assets_price
";
$args['qry_table_name'] 	= " 
								(
								Select 
									ST.product_idx, ST.product_option_idx, ST.stock_unit_price
									, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
									, Sum(Case When stock_status = 'ABNORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_ABNORMAL
									, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
									, Sum(Case When stock_status = 'HOLD' Then stock_amount * stock_type Else 0 End) as stock_amount_HOLD
									, Sum(Case When stock_status = 'DISPOSAL' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
";
if(count($qryStockWhereAry) > 0)
{
	$args['qry_table_name'] .= " And " . join(" And ", $qryStockWhereAry);
}
$args['qry_table_name'] 	.= " 
									Group by ST.product_idx, product_option_idx, stock_unit_price
								) as STOCK 
								Inner Join DY_PRODUCT P On STOCK.product_idx = P.product_idx 
								Inner Join DY_PRODUCT_OPTION PO On STOCK.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
";

$args['qry_where'] = "
								P.product_sale_type = N'SELF' 
								And P.product_is_del = N'N' 
								And P.product_is_trash = N'N'  
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

$userdata = array();

$userdata["date_start"] = $date_start;
$userdata["date_end"] = $date_end;

$_list = $WholeGetListResult['listRst'];

//합계
$qry = "Select
	Sum(stock_amount_NORMAL) as stock_amount_NORMAL
	, Sum(stock_amount_ABNORMAL) as stock_amount_ABNORMAL
	, Sum(stock_amount_BAD) as stock_amount_BAD
	, Sum(stock_amount_HOLD) as stock_amount_HOLD
	, Sum(stock_amount_DISPOSAL) as stock_amount_DISPOSAL
	, Sum(STOCK.stock_amount_NORMAL + STOCK.stock_amount_ABNORMAL + STOCK.stock_amount_BAD + STOCK.stock_amount_HOLD + STOCK.stock_amount_DISPOSAL) as stock_assets_amount
	, ( 
		Sum(STOCK.stock_amount_NORMAL * STOCK.stock_unit_price)
		+ Sum(STOCK.stock_amount_ABNORMAL * STOCK.stock_unit_price)
		+ Sum(STOCK.stock_amount_BAD * STOCK.stock_unit_price)
		+ Sum(STOCK.stock_amount_HOLD * STOCK.stock_unit_price) 
		+ Sum(STOCK.stock_amount_DISPOSAL * STOCK.stock_unit_price)
	) as stock_assets_price
";

$qry .= " From ";
$qry .= $args['qry_table_name'];
$qry .= " Where ";
$qry .= $args['qry_where'];

$C_ListTable->db_connect();
$add_result = $C_ListTable->execSqlOneRow($qry);
$C_ListTable->db_close();

$userdata["stock_amount_NORMAL"] = $add_result["stock_amount_NORMAL"];
$userdata["stock_amount_ABNORMAL"] = $add_result["stock_amount_ABNORMAL"];
$userdata["stock_amount_BAD"] = $add_result["stock_amount_BAD"];
$userdata["stock_amount_HOLD"] = $add_result["stock_amount_HOLD"];
$userdata["stock_amount_DISPOSAL"] = $add_result["stock_amount_DISPOSAL"];
$userdata["stock_assets_amount"] = $add_result["stock_assets_amount"];
$userdata["stock_assets_price"] = $add_result["stock_assets_price"];


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