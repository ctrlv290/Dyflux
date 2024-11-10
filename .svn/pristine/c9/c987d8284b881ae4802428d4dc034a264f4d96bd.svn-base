<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 리스트 JSON
 */
//Page Info
$pageMenuIdx = 212;
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

//$order_by = "C.comm_idx DESC";
$order_by = "C.seller_name DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}
$order_by .= ", comm_type ASC, market_commission ASC, delivery_commission ASC";

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"seller_idx",
	"comm_type",
	"search_column",
);
$available_search_col = array(
	"market_product_no",
	"P.product_name",
	"O.product_option_name",
	"CP.product_idx",
	"CP.product_option_idx",
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
				$col == "seller_idx"
                || $col == "comm_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "search_column" && in_array($val, $available_search_col)){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			C.comm_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And C.comm_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "C.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
$args['qry_table_idx'] 	= "C.comm_idx";
$args['qry_get_colum'] 	= " C.*
							, S.seller_name
							, P.product_idx, P.product_name, O.product_option_idx, O.product_option_name 
							, (Select member_id From DY_MEMBER M Where C.last_member_idx = M.idx) as member_id
                            ";

$args['qry_table_name'] 	= " DY_MARKET_COMMISSION C
								Inner Join DY_MARKET_COMMISSION_PRODUCT CP On C.comm_idx = CP.comm_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = CP.product_idx
								Left Outer Join DY_PRODUCT_OPTION O On O.product_option_idx = CP.product_option_idx
								Left Outer Join DY_SELLER S On C.seller_idx = S.seller_idx
";
$args['qry_where']			= " C.comm_is_del = N'N' And CP.comm_product_is_del = N'N' And P.product_is_del = N'N' And O.product_option_is_del = N'N' ";
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