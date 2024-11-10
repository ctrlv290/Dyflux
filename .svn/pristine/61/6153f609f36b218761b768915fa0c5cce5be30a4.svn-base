<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 리스트 JSON
 *       벤더사 판매처 [seller_type : VENDOR_SELLER] 는 제외한 목록
 */
//Page Info
$pageMenuIdx = 53;
//Permission IDX
$pagePermissionIdx = 43;
//Init
include_once "../_init_.php";
$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$C_ListTable = new ListTable();

$is_view_collect = $_GET["isvc"];   // collect 정보를 같이 표시할 것인가? - ssawoona


//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.seller_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"seller_is_use",
	"manage_group_idx",
	"seller_name",
	"seller_nameidx",
	"seller_type",
	"seller_auto_order",
);
$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $avaliable_col)) {
			if($col == "seller_nameidx") {
				$qryWhereAry[] = " (seller_name like N'%" . $val . "%' Or Convert(varchar, seller_idx) = N'" . $val . "')";
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

$coll_colum = "";
if($is_view_collect == "true") {
	$coll_colum = ", OC.* ";
}

// make select query
$args['qry_table_idx'] 	= "A.seller_idx";
$args['qry_get_colum'] 	= " A.* ".$coll_colum."
							, Case When seller_type = 'MARKET_SELLER' Then
									(Select code_name From DY_CODE C Where C.parent_code = 'MARKET_SELLER' And C.code = A.market_code)
								When seller_type = 'CUSTOM_SELLER' Then
									(Select code_name From DY_CODE C Where C.parent_code = 'CUSTOM_SELLER' And C.code = A.market_code)
								End as market_name
							, (Select manage_group_name From DY_MANAGE_GROUP B Where A.manage_group_idx = B.manage_group_idx) as manage_group_name
                            ";

$args['qry_table_name'] 	= " DY_SELLER A ";
if($is_view_collect == "true") {
	$args['qry_table_name'] 	= " DY_SELLER A
		LEFT OUTER JOIN 
		(
		SELECT seller_idx  AS oc_seller_idx, collect_sdate, collect_edate, collect_order_count, collect_state, collect_message FROM (
		SELECT ROW_NUMBER() OVER(PARTITION BY seller_idx ORDER BY order_collect_idx DESC) as order_by_num, *
		FROM DY_ORDER_COLLECT WHERE order_collect_is_del = N'N' And collect_type = N'AUTO') TMP 
		WHERE TMP.order_by_num = 1
		) OC ON A.seller_idx = OC.oc_seller_idx
	";
}

$args['qry_where']			= " A.seller_is_del = 'N'";

if($_GET["is_seller_search_pop"] != "Y") {
	$args['qry_where'] .= " And A.seller_type != 'VENDOR_SELLER' ";
}
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