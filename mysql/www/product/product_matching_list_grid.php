<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭정보 리스트 JSON
 */
//Page Info
$pageMenuIdx = 66;
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

$order_by = "A.matching_info_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}
$order_by .= ", L.matching_list_idx ASC";

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_sale_type",
	"supplier_idx",
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
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
				|| $col == "product_sale_type"
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
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			A.matching_info_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.matching_info_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
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
$args['qry_table_idx'] 	= "A.matching_info_idx";
$args['qry_get_colum'] 	= " A.matching_info_idx
							, A.seller_idx, S.seller_name
							, A.market_product_no, A.market_product_name, A.market_product_option, A.matching_info_regdate
							, L.product_option_cnt
							, P.supplier_idx
							, P.product_idx, P.product_name, O.product_option_idx, O.product_option_name 
							, A.member_idx
							, (Select member_id From DY_MEMBER M Where A.member_idx = M.idx) as member_id
							, L.matching_list_idx
                            ";
if(isDYLogin()){
	$args['qry_get_colum'] 	.= " 
							, SP.supplier_name
							
                            ";
}
$args['qry_table_name'] 	= " DY_PRODUCT_MATCHING_INFO A 
								Left Outer Join DY_PRODUCT_MATCHING_LIST L On A.matching_info_idx = L.matching_info_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = L.product_idx
								Left Outer Join DY_PRODUCT_OPTION O On O.product_option_idx = L.product_option_idx
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
";
$args['qry_where']			= " A.matching_info_is_del = N'N' ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}

//벤더사 로그인일 경우
if(!isDYLogin()){
	$args['qry_where'] .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
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