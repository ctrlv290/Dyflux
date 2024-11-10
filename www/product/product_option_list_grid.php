<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 리스트 JSON
 */
//Page Info
$pageMenuIdx = 35;
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

$order_by = "A.product_option_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"product_idx",
	"product_option_soldout_type",
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

		if(trim($val) && in_array($col, $avaliable_col)) {
			if(trim($col) == "product_idx") {
				$qryWhereAry[] = " A.product_idx = N'" . $val . "'";
			}elseif(trim($col) == "product_option_soldout_type"){

				if($val == "both"){
					$qryWhereAry[] = " (A.product_option_soldout = N'Y' Or A.product_option_soldout_temp = N'Y')";
				}elseif($val == "product_option_soldout"){
					$qryWhereAry[] = " A.product_option_soldout = N'Y' ";
				}elseif($val == "product_option_soldout_temp"){
					$qryWhereAry[] = " A.product_option_soldout_temp = N'Y' ";
				}elseif($val == "available"){
					$qryWhereAry[] = " (A.product_option_soldout = N'N' And A.product_option_soldout_temp = N'N')";
				}
			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
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

// make select query
$args['qry_table_idx'] 	= "A.product_option_idx";
$args['qry_get_colum'] 	= " A.product_option_idx, A.product_idx
							, A.product_option_soldout
							, A.product_option_soldout_temp
							, A.product_option_name
							, A.product_option_sale_price_A
							, A.product_option_sale_price_B
							, A.product_option_sale_price_C
							, A.product_option_sale_price_D
							, A.product_option_sale_price_E
							, A.product_option_regdate
							, A.product_option_warning_count
							, A.product_option_danger_count
							, A.product_option_purchase_price
							, P.product_sale_type
							, ISNULL(STOCK.stock_amount_NORMAL, 0) as stock_amount_NORMAL
							, ISNULL(A.product_option_soldout_memo, N'') AS product_option_soldout_memo
							, ISNULL(A.product_option_barcode_GTIN, N'') AS product_option_barcode_GTIN
                            ";

$args['qry_table_name'] 	= " DY_PRODUCT_OPTION A
									Left Outer Join DY_PRODUCT P On A.product_idx = P.product_idx
									Left Outer Join
									(
										Select 
										product_option_idx
										, isNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL 
										From DY_STOCK S
										Where S.stock_is_del = N'N' And S.stock_is_confirm = N'Y'
										Group by product_option_idx
									) as STOCK On STOCK.product_option_idx = A.product_option_idx
";
$args['qry_where']			= " A.product_option_is_del = 'N' And A.product_option_is_use = 'Y'";
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