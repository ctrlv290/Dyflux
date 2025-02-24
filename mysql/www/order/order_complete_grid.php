<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 완료 페이지 재고 부족 상품 리스트 JSON
 */
//Page Info
$pageMenuIdx = 183;
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

$order_by = "OPM.product_option_idx asc";
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

$tmp_randno = "";
if($_search_paramAryList["tmp_randno"]){
	$tmp_randno = $_search_paramAryList["tmp_randno"];
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "     A.order_idx
                                , sum(product_option_cnt) AS sum_product_option_cnt
                                , Case When STOCK.stock_amount_NORMAL is null Then 0
                                    Else
                                        stock_amount_NORMAL
                                    End as stock_amount_NORMAL";
$args['qry_get_colum'] 	= "
								A.order_idx
								, OPM.product_option_idx
								, P.product_name
								, PO.product_option_name
								, S.supplier_name
								, sum(product_option_cnt) AS sum_product_option_cnt
								, Case When STOCK.stock_amount_NORMAL is null Then 0
									Else
										stock_amount_NORMAL
									End as stock_amount_NORMAL
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Left Outer Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = OPM.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
								Left Outer Join (
								Select 
									ST.product_idx, ST.product_option_idx
									, IFNULL(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									Group by ST.product_idx, product_option_idx
								) as STOCK On STOCK.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								";
$args['qry_where']			= " 
								A.order_is_del = N'N'
								And A.tmp_randno = N'$tmp_randno'
								And P.product_sale_type = N'SELF' 		
";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}

//벤더사 로그인일 경우
if(!isDYLogin()){
	$args['qry_where'] .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
}

$args['qry_groupby']		= "OPM.product_option_idx HAVING sum_product_option_cnt > stock_amount_NORMAL		";
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
$userdata["shortage_product"] = $listRst_cnt;
$grid_response["userdata"] = $userdata;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>