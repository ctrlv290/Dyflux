<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 충전금관리 리스트 Grid
 */
//Page Info
$pageMenuIdx = 138;
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

$order_by = "C.last_charge_date DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
);

$available_search_col = array(
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
				$col == ""
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = " C.member_idx = N'" . $_search_paramAryList["seller_idx"] . "'";
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			C.last_charge_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And C.last_charge_date <= '".$_search_paramAryList["date_end"]."' 
		";
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
$args['qry_table_idx'] 	= "C.member_idx";
$args['qry_get_colum'] 	= " 
							C.*
							, V.vendor_name
							, V.vendor_grade
							, (Select top 1 charge_memo From DY_MEMBER_VENDOR_CHARGE Z Where Z.settle_idx = 0 And Z.member_idx = C.member_idx And Z.charge_date = C.last_charge_date Order by Z.charge_regdate desc) as last_memo
";
$args['qry_table_name'] 	= " 
								(
								Select
									member_idx
									, Sum(charge_amount * charge_inout) as remain_amount
									, Max(charge_date) as last_charge_date
									, Max(charge_idx) as last_charge_idx
									, Sum(charge_amount * charge_inout)
									 - (Select isNull(Sum(settle_sale_sum), 0) From DY_SETTLE S Where S.seller_idx = MV.member_idx And S.settle_is_del = N'N')
									 + (Select isNull(Sum(ledger_tran_amount), 0) - isNull(Sum(ledger_adjust_amount), 0) From DY_LEDGER L Where L.target_idx = MV.member_idx And L.ledger_is_del = N'N' And L.charge_idx = 0)  
									 as remain_amount2 
								From DY_MEMBER_VENDOR_CHARGE MV
								Where charge_is_del = N'N' And charge_inout = 1
								Group by member_idx
								) as C
								Left Outer Join DY_MEMBER_VENDOR V On C.member_idx = V.member_idx
";
$args['qry_where'] = " 1 = 1 ";
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