<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 신규 발주 시 상품 추가 팝업 페이지 Grid List
 */
//Page Info
$pageMenuIdx = 188;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();


//******************************* 리스트 기본 설정 ******************************//
// get info set
$page               = (!$page) ? '1' : $page;
$args['page']       = (!$page) ? '1' : $page;
$args['searchVar']  = $searchVar;
$args['searchWord'] = $searchWord;
$args['sortBy']     = $sortBy;
$args['sortType']   = $sortType;
$args['pagename']   = $GL_page_nm;

$order_by = "A.stock_order_idx ASC ";

//발주 코드
$stock_order_idx = $_GET["stock_order_idx"];

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "S.stock_idx";
$args['qry_get_colum'] 	= " A.stock_order_idx, S.stock_idx
							, S.product_idx, S.product_option_idx
							, PO.product_option_name
							, P.product_name
							, S.stock_unit_price
							, S.stock_due_amount
							, S.stock_amount
							, IFNULL(S.stock_order_msg, '') as stock_order_msg
							, (S.stock_unit_price * S.stock_amount)  as stock_cal_price
                            ";

$args['qry_table_name'] 	= " DY_STOCK_ORDER A
									Left Outer Join DY_STOCK S On A.stock_order_idx = S.stock_order_idx
									Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = S.product_option_idx
									Left Outer Join DY_PRODUCT P On PO.product_idx = P.product_idx
";
$args['qry_where']			= " A.stock_order_is_del = 'N' And A.stock_order_idx = N'$stock_order_idx' And S.stock_is_del = N'N' And S.stock_order_is_ready = N'Y'";
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

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>