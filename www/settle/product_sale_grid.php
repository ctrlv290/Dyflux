<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품별 매출 통계  리스트 JSON
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

$order_by = "seller_name ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
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
	$date_start = date('Y-m-d');
	$date_end = date('Y-m-d');
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$_searchWhereDate = "	 
			And (
				settle_date >= '".$_search_paramAryList["date_start"]."' 
				And settle_date <= '".$_search_paramAryList["date_end"]."'
			) 
		";

		$date_start = $_search_paramAryList["date_start"];
		$date_end = $_search_paramAryList["date_end"];
	}

	$_search_date_count = count($_search_date_ary);

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "T.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
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
$args['qry_table_idx'] 	= "T.product_idx";
$args['qry_get_colum'] 	= " 
							T.*, Convert(varchar(30), P.product_regdate, 120) as product_regdate2
							, P.product_name, P.product_supplier_name, P.product_regdate
							, PO.product_option_soldout , PO.product_option_name
							, STOCK.stock_amount_NORMAL, STOCK.stock_amount_BAD
							, S.seller_name, SUPPLIER.supplier_name
";
$args['qry_table_name'] 	= " 
								(
									Select 
									product_idx, product_option_idx, seller_idx
									, Sum(settle_sale_supply) as settle_sale_supply
									, Sum(product_option_cnt) as product_count
									From DY_SETTLE SP
									Where 
										settle_date between N'$date_start' And N'$date_end'
									Group by product_idx, product_option_idx, seller_idx
								) as T
								Inner Join DY_PRODUCT P On T.product_idx = P.product_idx
								Inner Join DY_PRODUCT_OPTION PO On T.product_option_idx = PO.product_option_idx
								Left Outer Join 
								(
								Select 
									ST.product_idx, ST.product_option_idx
									, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
									, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									Group by ST.product_idx, product_option_idx
								) as STOCK On STOCK.product_option_idx = T.product_option_idx  
								Left Outer Join DY_SELLER S On T.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = P.supplier_idx
";
$args['qry_where']			= " 
								1 = 1
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