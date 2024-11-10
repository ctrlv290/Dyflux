<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일주문일괄삭제 리스트 JSON
 */
//Page Info
$pageMenuIdx = 76;
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

$order_by = "S.seller_idx ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
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

	if($_search_paramAryList["date"] && $_search_paramAryList["time_start"] && $_search_paramAryList["time_end"]){
		$qryWhereAry[] = " 
				order_progress_step_accept_temp_date >= '".$_search_paramAryList["date"]." ".$_search_paramAryList["time_start"]."'
				And order_progress_step_accept_temp_date <= '".$_search_paramAryList["date"]." ".$_search_paramAryList["time_end"]."'
		";
	}
}

$order_date = $_search_paramAryList["date"];

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.seller_idx";
$args['qry_get_colum'] 	= " A.*, '".$order_date."' as order_date
							, S.seller_name
                            ";

$args['qry_table_name'] 	= " 
								(
									Select
									seller_idx
									, count(seller_idx) as order_cnt
									, Sum(Case When order_progress_step = 'ORDER_INVOICE' Then 1 Else 0 End) as invoice_cnt
									, Sum(Case When order_progress_step = 'ORDER_SHIPPED' Then 1 Else 0 End) as shipped_cnt
									From DY_ORDER
									Where order_is_del = N'N' And order_is_after_order = N'N'
								";
								if(count($qryWhereAry) > 0)
								{
									$args['qry_table_name']  .= " And " . join(" And ", $qryWhereAry);
								}
$args['qry_table_name'] .= "
									Group by seller_idx
								) as A
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
";
$args['qry_where']			= " 1=1 ";

//벤더사 로그인일 경우
if(!isDYLogin()){
	$args['qry_where'] .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
}

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