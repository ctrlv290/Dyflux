<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 반품관리 페이지
 */
//Page Info
$pageMenuIdx = 97;
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

$order_by = "R.return_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
//$available_col = array(
//	"member_idx",
//	"order_progress_step",
//	"order_cs_status",
//	"cs_confirm",
//	"cs_task",
//	"cs_cancel_type",
//	"cs_change_type",
//	"cs_type",
//	"cs_alarm",
//	"cs_sms",
//	"market_product_name",
//	"market_product_option",
//	"search_column",
//);

$available_col = array(
	"member_idx",
    "search_column",
);

$available_search_col = array(
	"R.receive_name",
	"R.receive_tp_num",
	"R.receive_hp_num",
	"R.receive_addr1",
	"name_all",
	"order_name",
	"order_tp_num",
	"order_hp_num",
	"R.order_idx",
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
			if($col == "member_id") {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "search_column"){
				if($val == "name_all" && trim($_search_paramAryList["search_keyword"]) != ""){

                    $tmp_keyword = trim($_search_paramAryList["search_keyword"]);
                    $tmp_keyword_ary = explode(" ", $tmp_keyword);
                    $tmp_qry = array();

                    foreach ($tmp_keyword_ary as $kw) {
                        if(trim($kw)) {
                            $tmp_qry[] = " ( R.receive_name like N'%" . trim($kw) . "%' Or order_name like N'%" . trim($kw) . "%' )";
                        }
                    }

                    $qryWhereAry[]  = "(" . implode(" OR ", $tmp_qry) . ")";

                    //$qryWhereAry[] = " ( receive_name = N'" . trim($_search_paramAryList["search_keyword"]) . "' Or order_name = N'" . trim($_search_paramAryList["search_keyword"]) . "' )";
                }elseif(
                    in_array($val, $available_search_col)
                    && trim($_search_paramAryList["search_keyword"]) != ""
                ) {
                    $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
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

		if($_search_paramAryList["period_type"] == "return_regdate"){
			$qryWhereAry[] = "
				return_regdate >= '".$_search_paramAryList["date_start"]." ".$_search_paramAryList["time_start"]."' 
				And return_regdate <= '".$_search_paramAryList["date_end"]." ".$_search_paramAryList["time_end"]."'
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
$args['qry_table_idx'] 	= "R.return_idx";
$args['qry_get_colum'] 	= " 
							R.*
							, O.order_name
							, P.product_name
							, PO.product_option_name
							, C.code_name as return_type_han
							, C2.code_name as delivery_status_han
							, DATE_FORMAT(O.shipping_date, '%Y-%m-%d %H:%i:%s') as shipping_date
							, DATE_FORMAT(O.order_progress_step_accept_date, '%Y-%m-%d %H:%i:%s') as order_progress_step_accept_date
";
$args['qry_table_name'] 	= " 
								DY_ORDER_RETURN R
								Left Outer Join DY_ORDER_RETURN_PRODUCT RP On R.return_idx = RP.return_idx
								Left Outer Join DY_ORDER O On R.order_pack_idx = O.order_idx
								Left Outer Join DY_PRODUCT P On RP.product_idx = P.product_idx 
								Left Outer Join DY_PRODUCT_OPTION PO On RP.product_option_idx = PO.product_option_idx 
								Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
								Left Outer Join DY_MEMBER M On M.idx = R.last_member_idx
								Left Outer Join DY_CODE C On C.parent_code = N'ORDER_RETURN_DELIVERY_TYPE' And C.code = R.return_type
								Left Outer Join DY_CODE C2 On C2.parent_code = N'ORDER_RETURN_STATUS' And C2.code = R.delivery_status
";
$args['qry_where'] = " R.return_is_del = N'N' ";
//if(count($qryOrderWhereAry) > 0) {
//	$args['qry_where'] .= "
//								And R.order_idx in (
//									Select OO.order_idx
//									From DY_ORDER OO
//										Inner Join DY_ORDER_PRODUCT_MATCHING OM On OO.order_idx = OM.order_idx
//										Left Outer Join DY_PRODUCT P On OM.product_idx = P.product_idx
//										Left Outer Join DY_PRODUCT_OPTION PO On OM.product_option_idx = PO.product_option_idx
//									Where 1 = 1
//";
//
//	$args['qry_where'] .= " And " . join(" And ", $qryOrderWhereAry);
//	$args['qry_where'] .= " ) ";
//}
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