<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 내역 조회 페이지
 */
//Page Info
$pageMenuIdx = 96;
//Init
include_once "../_init_.php";

$last_member_idx = $GL_Member["member_idx"];

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

$order_by = "CS.cs_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"member_idx",
	"order_progress_step",
	"order_cs_status",
	"cs_confirm",
	"cs_task",
	"cs_cancel_type",
	"cs_change_type",
	"cs_type",
	"cs_alarm",
	"cs_sms",
	"market_product_name",
	"market_product_option",
	"search_column",
);

$available_search_col = array(
	"receive_name",
	"receive_tp_num",
	"receive_hp_num",
	"receive_addr1",
	"name_all",
	"order_name",
	"order_name",
	"order_tp_num",
	"order_hp_num",
	"CS.order_idx",
);

$qryWhereAry = array();
$qryOrderWhereAry = array();
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
			if(
				$col == "member_id"
				|| $col == "order_progress_step"
				|| $col == "cs_confirm"
				|| $col == "cs_task"
				|| $col == "cs_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "order_cs_status"){
				$qryOrderWhereAry[] = $col . " = N'" . $val . "'";

			}elseif(trim($col) == "cs_alarm"){
				if($val == "M") {
					$qryWhereAry[] = "
						CS.cs_idx in (
							Select cs_idx From DY_ORDER_CS_ALARM
							Where 
							member_idx = N'$last_member_idx'
						)
					";
				}elseif($val == "Y"){
					$qryWhereAry[] = "
						CS.cs_idx in (
							Select cs_idx From DY_ORDER_CS_ALARM
						)
					";
				}
			}elseif(
				trim($col) == "market_product_name"
				|| trim($col) == "market_product_option"
			){

				$qryWhereAry[] = $col . " like N'%" . $val . "%'";

			}elseif(trim($col) == "cs_cancel_type"){
				$qryWhereAry[] = "
						CS.cs_idx in (
							Select cs_idx From DY_ORDER_CS
							Where cs_reason_code1 = N'CS_REASON_CANCEL'
								And cs_reason_code2 = N'".$val."'
						)
					";
			}elseif(trim($col) == "cs_change_type"){
				$qryWhereAry[] = "
						CS.cs_idx in (
							Select cs_idx From DY_ORDER_CS
							Where cs_reason_code1 = N'CS_REASON_CHANGE'
								And cs_reason_code2 = N'".$val."'
						)
					";
			}elseif(trim($col) == "search_column"){
				if(
					in_array($val, $available_search_col)
					&& trim($_search_paramAryList["search_keyword"]) != ""
				) {
					$qryOrderWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	$date_start = date('Y-m-d');
	$date_end = date('Y-m-d');
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){

		if($_search_paramAryList["period_type"] == "cs_regdate"){
			$qryWhereAry[] = "
				cs_regdate >= '".$_search_paramAryList["date_start"]." ".$_search_paramAryList["time_start"]."' 
				And cs_regdate <= '".$_search_paramAryList["date_end"]." ".$_search_paramAryList["time_end"]."'
			";
		}elseif($_search_paramAryList["period_type"] == "order_accept"){
			$qryOrderWhereAry[] = "
				order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." ".$_search_paramAryList["time_start"]."' 
				And order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." ".$_search_paramAryList["time_end"]."'
			";
		}elseif($_search_paramAryList["period_type"] == "order_shipped"){
			$qryOrderWhereAry[] = "
				shipping_date >= '".$_search_paramAryList["date_start"]." ".$_search_paramAryList["time_start"]."' 
				And shipping_date <= '".$_search_paramAryList["date_end"]." ".$_search_paramAryList["time_end"]."'
			";
		}
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryOrderWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

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
$args['qry_table_idx'] 	= "CS.cs_idx";
$args['qry_get_colum'] 	= " 
							CS.*
							, O.order_progress_step
							, O.market_order_no
							, O.receive_name
							, O.market_product_name
							, O.market_product_option
							, S.seller_name
							, M.member_id
							, C.code_name as order_progress_step_han
							, CS_C.code_name as cs_task_han
							, Convert(varchar(30), CS.cs_regdate, 120) as cs_regdate2
							
";
$args['qry_table_name'] 	= " 
								DY_ORDER_CS CS
								Left Outer Join DY_ORDER O On CS.order_idx = O.order_idx 
								Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER M On M.idx = CS.member_idx
								Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
								Left Outer Join DY_CODE CS_C On CS_C.parent_code = N'CS_TASK' And CS_C.code = CS.cs_task
";
$args['qry_where'] = " CS.cs_is_del = N'N' ";
if(count($qryOrderWhereAry) > 0) {
	$args['qry_where'] .= " 
								And CS.order_idx in (
									Select OO.order_idx
									From DY_ORDER OO
										Inner Join DY_ORDER_PRODUCT_MATCHING OM On OO.order_idx = OM.order_idx
										Left Outer Join DY_PRODUCT P On OM.product_idx = P.product_idx
										Left Outer Join DY_PRODUCT_OPTION PO On OM.product_option_idx = PO.product_option_idx 
									Where 1 = 1
";

	$args['qry_where'] .= " And " . join(" And ", $qryOrderWhereAry);
	$args['qry_where'] .= " ) ";
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

$userdata = array();

$userdata["date_start"] = $date_start;
$userdata["date_end"] = $date_end;

$_list = $WholeGetListResult['listRst'];
//$_new_list = array();
//$C_CS = new CS();
//foreach($_list as $row)
//{
//	$_cs_list = $C_CS->getCSListSimple($row["order_pack_idx"]);
//	$cs_full = array();
//	if($_cs_list)
//	{
//		foreach($_cs_list as $cs) {
//			$cs_text = "";
//			$dt = date("Y-m-d H:i:s", strtotime($cs["cs_regdate"]));
//			$confirm = ($cs["cs_confirm"] == "Y") ? "처리" : "미처리";
//			$cs_text .= $dt . "/" . $cs["cs_task_name"] . " " . $cs["cs_reason_text"] . "/" . $confirm . "\n";
//			$cs_text .= $cs["cs_comment"];
//
//			$cs_full[] = $cs_text;
//		}
//	}
//
//	$cs_full_text = implode("\n============================================\n", $cs_full);
//	$row["cs_full_text"] = $cs_full_text;
//	$_new_list[] = $row;
//}


//합계
$qry = "Select
	O.order_progress_step, count(O.order_progress_step) as cnt
";

$qry .= " From ";
$qry .= $args['qry_table_name'];
$qry .= " Where ";
$qry .= $args['qry_where'];
$qry .= "
	And O.order_progress_step is not null
	Group by O.order_progress_step
";

$C_ListTable->db_connect();
$add_result = $C_ListTable->execSqlList($qry);
$C_ListTable->db_close();

//합계 초기화
$userdata["ORDER_PRODUCT_MATCHING"] = 0;
$userdata["ORDER_ACCEPT"] = 0;
$userdata["ORDER_INVOICE"] = 0;
$userdata["ORDER_SHIPPED"] = 0;

foreach($add_result as $add){
	$userdata[$add["order_progress_step"]] = $add["cnt"];
}


$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;
$grid_response["rows"] = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>