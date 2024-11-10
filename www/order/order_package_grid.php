<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 합포 리스트 JSON
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

$order_by = "A.order_idx asc";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
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
			if(trim($col) == "search_column" && in_array($val, $available_val)){
				if($val == "market_product_name_no")
				{
					$qryWhereAry[] = " 
						( 
							market_product_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
							Or
							market_product_no like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
						)
					";
				}else{
					if(trim($_search_paramAryList["search_keyword"]) != "") {
						$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
					}
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
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= "
							A.order_idx, S.seller_name, A.order_pack_idx,
							A.market_product_no, A.market_product_name, A.market_product_option,
							A.order_cnt, 
							A.receive_name, A.receive_addr1, A.receive_tp_num, A.receive_hp_num,
							ROW_NUMBER() OVER(PARTITION BY A.seller_idx, receive_name, receive_addr1, receive_tp_num, receive_hp_num  ORDER BY order_idx) as inner_no,
							Min(order_idx) OVER(PARTITION BY A.seller_idx, receive_name, receive_addr1, receive_tp_num, receive_hp_num  ORDER BY order_idx) as min_order_idx
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx 
								";
$args['qry_where']			= "  A.order_is_del = N'N' And exists (
									Select 1
									From DY_ORDER O2
									Where O2.order_is_del = N'N' 
										And O2.order_progress_step  in (N'ORDER_PRODUCT_MATCHING')
										And A.seller_idx = O2.seller_idx
										And A.receive_name = O2.receive_name
										And A.receive_addr1 = O2.receive_addr1
										And A.receive_tp_num = O2.receive_tp_num
										And A.receive_hp_num = O2.receive_hp_num
										And O2.order_is_lock = N'N'
									Group by O2.receive_name, O2.receive_addr1, O2.receive_tp_num, O2.receive_hp_num
									Having count(receive_name) > 1
								) And A.order_progress_step  in (N'ORDER_PRODUCT_MATCHING') And A.order_is_lock = N'N'
								";
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