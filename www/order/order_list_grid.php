<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 리스트 JSON
 */
//Page Info
$pageMenuIdx = 73;
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

$order_by = "A.seller_name ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
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
			if(trim($col) == "search_column" && $val == "seller_name"){
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
$args['qry_table_idx'] 	= "A.seller_idx";
$args['qry_get_colum'] 	= " A.seller_idx, A.seller_name
							, (Select top 1 collect_sdate From DY_ORDER_COLLECT C Where C.seller_idx = A.seller_idx Order by order_collect_idx desc)  as last_order_datetime
							, (Select top 1 collect_count From DY_ORDER_COLLECT C Where C.seller_idx = A.seller_idx Order by order_collect_idx desc)  as last_order_count
							, (Select top 1 collect_order_count From DY_ORDER_COLLECT C Where C.seller_idx = A.seller_idx Order by order_collect_idx desc)  as last_new_order_count
							, (
								Select count(*) From DY_ORDER O 
								Where 
								O.seller_idx = A.seller_idx 
								And O.order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING')
								And order_is_del = N'N') as available_order_count
							,
							(
								Select top 1 
									concat(
										member_id
										, (Case When collect_type = 'AUTO' Then ' (자동)' Else '' End)
									) as member_id
								From DY_ORDER_COLLECT C
									Left Outer Join DY_MEMBER M
										On C.last_member_idx = M.idx
								Where C.seller_idx = A.seller_idx  
								Order by order_collect_idx desc
							) as member_id
							
                            ";

$args['qry_table_name'] 	= " DY_SELLER A ";
$args['qry_where']			= " A.seller_is_use = N'Y' And A.seller_is_del = N'N'";
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
$WholeGetListResult['searchForm'];
$WholeGetListResult['sortLink'][];
*/
//******************************* 리스트 기본 설정 끝 ******************************//
//print_r($WholeGetListResult);

//총합 구하기


$userdata = array();
$userdata["sum_last_order_count"] = 0;
$userdata["sum_last_new_order_count"] = 0;
$userdata["sum_available_order_count"] = 0;

$C_Order = new Order();

$seller_idx = "";
//벤더사 로그인일 경우
if(!isDYLogin()){
	$seller_idx = $GL_Member["member_idx"];
}

$_sum = $C_Order->getOrderUploadSum($seller_idx);


$userdata["sum_last_order_count"]      = $_sum["sum_last_order_count"];
$userdata["sum_last_new_order_count"]  = $_sum["sum_last_new_order_count"];
$userdata["sum_available_order_count"] = $_sum["sum_available_order_count"];

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>