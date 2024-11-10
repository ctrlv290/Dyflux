<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 로그 조회 리스트 JSON
 */
//Page Info
$pageMenuIdx = 200;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();
$C_Stock = new Stock();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "is_date DESC";
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
			$qryWhereAry[] = $col . " = N'" . $val . "'";
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryStockMove = "	 
			And stock_move_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_move_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
        $qryStockConfirm = "	 
			And stock_is_confirm_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_is_confirm_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}
    if($_search_paramAryList["product_option_idx"]){
        $qryStockMove .= "And product_option_idx = N'" . $_search_paramAryList["product_option_idx"] . "'";
        $qryStockConfirm .= "And product_option_idx = N'" . $_search_paramAryList["product_option_idx"] . "'";
    }
    if($_search_paramAryList["stock_unit_price"] != "all"){
        $qryStockMove .= "And stock_unit_price = N'" . $_search_paramAryList["stock_unit_price"] . "'";
        $qryStockConfirm .= "And stock_unit_price = N'" . $_search_paramAryList["stock_unit_price"] . "'";
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
$args['qry_table_idx'] 	= "product_option_idx";
$args['qry_get_colum'] 	= "*";
$args['qry_table_name'] 	= "(
                                    Select 
                                         A.product_option_idx, A.stock_move_amount, A.stock_unit_price, A.stock_move_regdate AS is_date, A.stock_move_msg AS is_msg, '1' as stock_type
                                            , (Select member_id From DY_MEMBER M Where A.last_member_idx = M.idx) as member_id
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = A.stock_status_prev) as stock_status_prev_han
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = A.stock_status_next) as stock_status_next_han
                                    From  DY_STOCK_MOVE_LOG A
                                    where stock_move_is_del = N'N' 
                                    $qryStockMove
                                    UNION ALL
                                    Select 
                                        S.product_option_idx, S.stock_amount, S.stock_unit_price,S.stock_is_confirm_date, S.stock_msg AS is_msg, S.stock_type
                                            , (Select member_id From DY_MEMBER M Where S.last_member_idx = M.idx) as member_id
                                            , '입고' as stock_status_prev_han
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = S.stock_status) as stock_status_next_han
                                    From DY_STOCK S
                                    WHERE 
                                        S.stock_is_del = N'N'
                                        And S.stock_kind = N'STOCK_ORDER'
                                        And S.stock_type = 1
                                        AND S.stock_amount != 0
                                        $qryStockConfirm                                      
                                    UNION ALL
                                    Select 
                                        S.product_option_idx, S.stock_amount, S.stock_unit_price, S.stock_is_confirm_date, S.stock_msg AS is_msg, S.stock_type
                                            , (Select member_id From DY_MEMBER M Where S.last_member_idx = M.idx) as member_id
                                            , '출고' as stock_status_prev_han
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = S.stock_status) as stock_status_next_han
                                    From DY_STOCK S
                                    WHERE 
                                        S.stock_is_del = N'N'
                                        And stock_kind = N'ORDER'
                                        And stock_type = 1
                                        AND S.stock_amount != 0
                                        $qryStockConfirm                                       
                                    UNION ALL
                                    Select 
                                        S.product_option_idx, S.stock_amount, S.stock_unit_price, S.stock_is_confirm_date, S.stock_msg AS is_msg, S.stock_type
                                            , (Select member_id From DY_MEMBER M Where S.last_member_idx = M.idx) as member_id
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = S.stock_status) as stock_status_prev_han
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_KIND' And C1.code = S.stock_kind) as stock_status_next_han
                                    From DY_STOCK S
                                    WHERE 
                                        S.stock_is_del = N'N'
                                        And stock_kind = N'ETC'
                                        AND S.stock_amount != 0
                                        $qryStockConfirm                                  
                                        UNION ALL
                                    Select 
                                        S.product_option_idx, S.stock_amount, S.stock_unit_price, S.stock_is_confirm_date, S.stock_msg AS is_msg, S.stock_type
                                            , (Select member_id From DY_MEMBER M Where S.last_member_idx = M.idx) as member_id
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_KIND' And C1.code = S.stock_kind) as stock_status_prev_han
                                            , (Select code_name From DY_CODE C1 Where C1.parent_code = N'STOCK_STATUS' And C1.code = S.stock_status) as stock_status_next_han
                                    From DY_STOCK S
                                    WHERE 
                                        S.stock_is_del = N'N'
                                        And (stock_kind = N'BACK' and stock_status = N'NORMAL')
                                        AND S.stock_amount != 0
                                        $qryStockConfirm                              
                                    ) a";

$args['qry_where']			= " 1 = 1 ";
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