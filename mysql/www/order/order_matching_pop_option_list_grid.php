<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 매칭 팝업에서 사용되는 상품 검색 Grid List
 */
//Page Info
$pageMenuIdx = 73;
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

$order_by = "A.product_option_idx ASC ";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"product_name",
	"product_option_name",
	"search_column",
);
$available_col_val = array(
	"product_option_name",
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
			if(trim($col) == "search_column" && trim($val) == "product_option_name"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$keyword = trim($_search_paramAryList["search_keyword"]);

					$keyword_ary = explode(" ", $keyword);
					$qryWhereSubAry = array();
					foreach($keyword_ary as $vv){
						$qryWhereSubAry[] = $val . " like N'%" . $vv . "%'";
					}

					if(count($qryWhereSubAry) > 0) {
						$qryWhereAry[] = " (" . implode(" AND ", $qryWhereSubAry) . ") ";
					}
				}
			}else{
				//if(strpos($val, ' ') !== false){}
				$val_ary = explode(" ", $val);
				$qryWhereSubAry = array();
				foreach($val_ary as $vv){
					$qryWhereSubAry[] = $col . " like N'%" . $vv . "%'";
				}

				if(count($qryWhereSubAry) > 0) {
					$qryWhereAry[] = " (" . implode(" AND ", $qryWhereSubAry) . ") ";
				}
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
if(isDYLogin()) {
	$args['qry_get_colum'] = " A.product_option_idx, A.product_idx
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
							, P.product_name
							, S.supplier_name
							, C.code_name
							, 1 as matching_cnt
                            ";
}else{
	$args['qry_get_colum'] = " A.product_option_idx, A.product_idx
							, A.product_option_soldout
							, A.product_option_soldout_temp
							, A.product_option_name
							, A.product_option_regdate
							, A.product_option_warning_count
							, A.product_option_danger_count
							, P.product_sale_type
							, P.product_name
							, 1 as matching_cnt
                            ";
}
$args['qry_table_name'] 	= " DY_PRODUCT_OPTION A
									Left Outer Join DY_PRODUCT P On A.product_idx = P.product_idx
									Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
									LEFT OUTER JOIN DY_CODE AS C ON P.product_sale_type = C.code AND C.parent_code = N'PRODUCT_SALE_TYPE'
";
$args['qry_where']			= " P.product_is_del = N'N' And P.product_is_trash = N'N' And A.product_option_is_del = 'N' And A.product_option_is_use = 'Y' And P.product_idx is not null";
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
$grid_response["is_vendor"] = ! isDYLogin();
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>