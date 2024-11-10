<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 하부주문관리 리스트 JSON
 */
//Page Info
$pageMenuIdx = 81;
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

$order_by = "A.order_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"order_progress_step",
	"order_matching_cs",
	"A.order_idx",
	"market_order_no",
	"M.product_idx",
	"M.product_option_idx",
	"product_name",
	"product_option_name",
	"receive_name",
	"receive_hp_num",
	"invoice_no",
	"receive_addr",
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

		$col = trim($col);
		$val = trim($val);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_progress_step") {
				$val_ary = explode(",", $val);
				$val_ary_quote = array_map(function($val){
					return "'" . $val . "'";
				}, $val_ary);
				$val_join = implode(", ", $val_ary_quote);
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif(trim($col) == "order_matching_cs"){
				if($val == "NORMAL" || $val == "ORDER_CANCEL" || $val == "PRODUCT_CHANGE"){
					$qryWhereAry[] = " M.order_cs_status = N'".$val."'";
				}elseif($val == "ORDER_CANCEL_N"){
					$qryWhereAry[] = " M.order_cs_status = N'ORDER_CANCEL' And M.product_cancel_shipped = N'N'";
				}elseif($val == "ORDER_CANCEL_Y"){
					$qryWhereAry[] = " M.order_cs_status = N'ORDER_CANCEL' And M.product_cancel_shipped = N'Y'";
				}elseif($val == "PRODUCT_CHANGE"){
					$qryWhereAry[] = " M.order_cs_status = N'PRODUCT_CHANGE' And M.product_change_shipped = N'N'";
				}elseif($val == "PRODUCT_CHANGE"){
					$qryWhereAry[] = " M.order_cs_status = N'PRODUCT_CHANGE' And M.product_change_shipped = N'Y'";
				}elseif($val == "HOLD"){
					$qryWhereAry[] = " O.order_is_hold = N'Y'";
				}
			}elseif(trim($col) == "receive_addr"){
				$qryWhereAry[] = " O.receive_addr1 like N'%".$val."%' Or O.receive_addr2 like N'%".$val."%'";
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["period_type"] == "order_regdate"){
		$qryWhereAry[] = "	 
			A.order_regdate >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.order_regdate <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "'
		";
	}elseif($_search_paramAryList["period_type"] == "order_accept_regdate"){
		$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." " .  $_search_paramAryList["time_start"] . "'
			And A.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." " .  $_search_paramAryList["time_end"] . "' 
		";
	}
}

if($GL_Member["member_type"] == "SUPPLIER"){
	$qryWhereAry[] = "P.supplier_idx = N'".$GL_Member["member_idx"]."'";
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

//엑셀다운로드 시 표시 행수 무한
if($gridPrintForExcelDownload) {
	// paging set
	$args['show_row'] 	= 9999;
	$args['show_page'] 	= 9999;
}

// make select query
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= " 
							A.*
							, M.product_option_cnt
							, M.order_cs_status
							, P.product_idx, P.product_name
							, PO.product_option_idx, PO.product_option_name
							, S.supplier_name
							, SL.seller_name
							, C.code_name as order_progress_step_han
							, C2.code_name as order_cs_status_han
							, D.delivery_name
                            ";

$args['qry_table_name'] 	= " DY_ORDER A 
								Inner Join DY_ORDER_PRODUCT_MATCHING M On A.order_idx = M.order_idx
								Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
								Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
								Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
								Left Outer Join DY_CODE C2 On C2.parent_code = N'ORDER_MATCHING_CS' And C2.code = M.order_cs_status
								Left Outer Join (
									Select distinct delivery_code, delivery_name
									From DY_DELIVERY_CODE
									Where market_code = 'DY'
								) D On D.delivery_code = A.delivery_code
";
$args['qry_where']			= " A.order_is_del = N'N' And M.order_matching_is_del = N'N' And A.order_progress_step not in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING') ";
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

//판매금액 합산
$C_Dbconn = new DBConn();
$qry = "
	Select 
	Sum(order_amt) as order_amt_sum
	, Sum(order_calculation_amt) as order_calculation_amt_sum
";
$qry .= " From ";
$qry .= $args['qry_table_name'];
$qry .= " Where ";
$qry .= $args['qry_where'];

$C_Dbconn->db_connect();
$summary_result = $C_Dbconn->execSqlOneRow($qry);
$C_Dbconn->db_close();

$userdata["order_amt_sum"] = $summary_result["order_amt_sum"];
$userdata["order_calculation_amt_sum"] = $summary_result["order_calculation_amt_sum"];

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
$grid_response["rows"]     = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>