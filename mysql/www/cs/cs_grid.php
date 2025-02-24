<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 주문 리스트 JSON
 */
//Page Info
$pageMenuIdx = 205;
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
	"search_column",
	"order_progress_step",
	"order_idx",
	"market_order_no",
	"chk_hold",
	"chk_soldout",
	"chk_souldout_temp",
	"chk_cs_not_confirm",
	"chk_pack",
	"chk_gift",
	"order_cs_status",
	"delivery_is_free",
	"cs_task",
);
//검색 가능한 셀렉트박스 값 지정
$available_val_for_search_column = array(
	"receive_name",
	"receive_tp_num",
	"receive_hp_num",
	"receive_addr1",
	"name_all",
	"order_name",
	"order_tp_num",
	"order_hp_num",
	"A.order_idx",
	"invoice_no",
	"product_name",
	"market_product_name",
	"market_product_option",
	"market_product_ALL",
	"market_product_no",
	"order_idx",
	"A.invoice_no",
	"A.market_order_no"
);

$qryWhereAry = array();
$qryWhereOrderSearch = array();
$qryWherePackOrderSearch = array();
$qryWhereCSSearch = array();
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
				if($val == "ACCEPT_TEMP_BEFORE"){
					$qryWhereAry[] = " order_progress_step IN (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING') ";
				}elseif($val == "ACCEPT_TEMP"){
					$qryWhereAry[] = " order_progress_step = N'ORDER_ACCEPT_TEMP' ";
				}elseif($val == "ACCEPT"){
					$qryWhereAry[] = " order_progress_step = N'ORDER_ACCEPT' ";
				}elseif($val == "INVOICE"){
					$qryWhereAry[] = " order_progress_step = N'ORDER_INVOICE' ";
				}elseif($val == "SHIPPED"){
					$qryWhereAry[] = " order_progress_step = N'ORDER_SHIPPED' ";
				}elseif($val == "ACCEPT_INVOICE"){
					$qryWhereAry[] = " order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE') ";
				}elseif($val == "ACCEPT_SHIPPED"){
					$qryWhereAry[] = " order_progress_step in (N'ORDER_ACCEPT', N'ORDER_SHIPPED') ";
				}elseif($val == "INVOICE_SHIPPED"){
					$qryWhereAry[] = " order_progress_step in (N'ORDER_INVOICE', N'ORDER_SHIPPED') ";
				}
			}elseif(trim($col) == "order_idx"){
				$qryWhereAry[] = " order_idx = N'$val' ";
			}elseif(trim($col) == "chk_hold" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "N" : "Y";
				$qryWhereAry[] = " order_is_hold = N'$chk' ";
			}elseif(trim($col) == "chk_soldout" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "N" : "Y";
				$qryWhereOrderSearch[] = "pPO.product_option_soldout = N'$chk' ";
			}elseif(trim($col) == "chk_souldout_temp" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "N" : "Y";
				$qryWhereOrderSearch[] = "  pPO.product_option_soldout_temp = N'$chk' ";
			}elseif(trim($col) == "chk_cs_not_confirm" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "Y" : "N";
				$qryWhereCSSearch[] = " CS.cs_confirm = N'$chk' ";
			}elseif(trim($col) == "chk_pack" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "N" : "Y";
				$qryWherePackOrderSearch[] = "
					A.order_idx in (
						Select order_pack_idx
						From DY_ORDER O3
						Where O3.order_is_del = 'N'
						Group by order_pack_idx
						Having count(order_pack_idx) > 1
					)
				";
			}elseif(trim($col) == "chk_gift" && $val == "Y"){
				$chk = ($_search_paramAryList["chk_except"] == "Y") ? "N" : "Y";
				$qryWherePackOrderSearch[] = "
					A.order_idx in (
						Select O3.order_pack_idx
						From DY_ORDER O3
							Inner Join DY_ORDER_PRODUCT_MATCHING OPM3 On O3.order_idx = OPM3.order_idx
						Where O3.order_is_del = 'N'
							  And OPM3.order_matching_is_del = N'N'
							  And OPM3.is_gift = N'$chk'
						Group by O3.order_pack_idx
						Having count(O3.order_pack_idx) > 1
					)
				";
			}elseif(trim($col) == "order_cs_status" && $val != ""){
				if($val == "NORMAL"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status = N'NORMAL')";
				}elseif($val == "NORMAL_CHANGE"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status in (N'NORMAL', N'PRODUCT_CHANGE'))";
				}elseif($val == "CANCEL"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status = N'ORDER_CANCEL')";
				}elseif($val == "CHANGE"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status = N'PRODUCT_CHANGE')";
				}elseif($val == "CHANGE_SHIPPED"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status = N'PRODUCT_CHANGE' And product_change_shipped = N'Y' )";
				}elseif($val == "CHANGE_SHIPPED_NORMAL"){
					$qryWhereAry[] = " A.order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_cs_status in (N'NORMAL', N'PRODUCT_CHANGE') And product_change_shipped = N'Y' )";
				}
			}elseif(trim($col) == "delivery_is_free" && $val != ""){
				$qryWhereAry[] = " delivery_is_free = N'$val' ";
			}elseif(trim($col) == "cs_task" && $val != ""){
				$qryWhereAry[] = " A.order_pack_idx in (Select order_pack_idx From DY_ORDER_CS Where cs_is_del = N'N' And cs_task = N'$val')";
			}elseif(trim($col) == "search_column" && in_array($val, $available_val_for_search_column)){
				if($val == "A.order_idx" && trim($_search_paramAryList["search_keyword"]) != "")
				{
					$qryWhereAry[] = $val . " = N'" . trim($_search_paramAryList["search_keyword"]) . "'";
				}elseif($val == "name_all" && trim($_search_paramAryList["search_keyword"]) != ""){

					$tmp_keyword = trim($_search_paramAryList["search_keyword"]);
					$tmp_keyword_ary = explode(" ", $tmp_keyword);
					$tmp_qry = array();

					foreach ($tmp_keyword_ary as $kw) {
						if(trim($kw)) {
							$tmp_qry[] = " ( receive_name like N'%" . trim($kw) . "%' Or order_name like N'%" . trim($kw) . "%' )";
						}
					}

					$qryWhereAry[]  = "(" . implode(" OR ", $tmp_qry) . ")";

					//$qryWhereAry[] = " ( receive_name = N'" . trim($_search_paramAryList["search_keyword"]) . "' Or order_name = N'" . trim($_search_paramAryList["search_keyword"]) . "' )";
				}elseif($val == "product_name" && trim($_search_paramAryList["search_keyword"]) != ""){

					$tmp_keyword = trim($_search_paramAryList["search_keyword"]);
					$tmp_keyword_ary = explode(" ", $tmp_keyword);
					$tmp_qry = array();

					foreach ($tmp_keyword_ary as $kw) {
						if(trim($kw)) {
							$tmp_qry[] = " pP.product_name like '%" . trim($kw) . "%'";
						}
					}

					$qryWhereAry[]  = "(" . implode(" OR ", $tmp_qry) . ")";

					//$qryWhereOrderSearch[] = " pP.product_name like '%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}else{
					if(trim($_search_paramAryList["search_keyword"]) != "") {

						$tmp_keyword = trim($_search_paramAryList["search_keyword"]);
						$tmp_keyword_ary = explode(" ", $tmp_keyword);
						$tmp_qry = array();

						foreach ($tmp_keyword_ary as $kw) {
							if(trim($kw)) {
								$tmp_qry[] = $val . " like '%" . trim($kw) . "%'";
							}
						}

						$qryWhereAry[]  = "(" . implode(" OR ", $tmp_qry) . ")";


						//$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
					}
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$sub_query_order_where = "";
	$sub_query_cs_where = "";

	if($_search_paramAryList["period_type"] == "order_invoice_date"){
		$qryWhereAry[] = "	 
			A.invoice_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			And A.invoice_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
		";

		$sub_query_order_where = "	 
			AND OO.invoice_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			AND OO.invoice_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
		";
	}elseif($_search_paramAryList["period_type"] == "order_accept_regdate"){
		$qryWhereAry[] = "	 
			A.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			And A.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";

		$sub_query_order_where = "	 
			AND OO.order_progress_step_accept_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			AND OO.order_progress_step_accept_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}elseif($_search_paramAryList["period_type"] == "order_regdate"){
		$qryWhereAry[] = "	 
			A.order_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00'
			And A.order_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";

		$sub_query_order_where = "	 
			AND OO.order_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00'
			AND OO.order_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";

		$sub_query_cs_where = "AND CCC.cs_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00'";
	}elseif($_search_paramAryList["period_type"] == "order_shipping_date"){
		$qryWhereAry[] = "	 
			A.shipping_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			And A.shipping_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";

		$sub_query_order_where = "	 
			AND OO.shipping_date >= '".$_search_paramAryList["date_start"]." 00:00:00'
			AND OO.shipping_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
							A.*
							, (CASE
							    WHEN A.receive_addr2 = '' THEN A.receive_addr1
							    ELSE CONCAT_WS(',', A.receive_addr1, A.receive_addr2)
							END) as receive_addr 
							, OPM.product_option_cnt_total
							, OPM.order_cs_status_list
							, SL.seller_name
							, C.code_name as order_progress_step_han
							, CS.cs_total_cnt
							, CS.cs_manual_cnt
                            ";

$args['qry_table_name'] 	= " DY_ORDER A
								Left Outer Join (
									Select 
										order_pack_idx
										, Sum(product_option_cnt) as product_option_cnt_total
										,(
                                            SELECT GROUP_CONCAT(DISTINCT C2.code_name)
                                            FROM DY_ORDER AS O2
                                                Left Outer Join DY_ORDER_PRODUCT_MATCHING AS OPM2 ON O2.order_idx = OPM2.order_idx And OPM2.order_matching_is_del = N'N'
                                                Left Outer Join DY_CODE AS C2 On C2.parent_code = N'ORDER_MATCHING_CS' And C2.code = OPM2.order_cs_status
                                            WHERE O2.order_pack_idx = OO.order_pack_idx
                                        ) AS order_cs_status_list
									From DY_ORDER OO
									Left Outer Join DY_ORDER_PRODUCT_MATCHING OOPM On OO.order_idx = OOPM.order_idx And OOPM.order_matching_is_del = N'N'
									Where 
										OO.order_is_del = N'N'
										$sub_query_order_where
									Group by order_pack_idx
								) as OPM On OPM.order_pack_idx = A.order_idx
								Left Outer Join DY_SELLER SL On SL.seller_idx = A.seller_idx
								Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
								Left Outer Join (
									Select 
										order_pack_idx
										, count(order_pack_idx) as cs_total_cnt
									    , Sum(IFNULL((Case When cs_is_auto = 'N' Then 1 Else 0 End), 0)) as cs_manual_cnt
								    From DY_ORDER_CS CCC
								    Where cs_is_del = 'N'
								    $sub_query_cs_where
								    Group by order_pack_idx
								) as CS On A.order_pack_idx = CS.order_pack_idx
";
$args['qry_where']			= " 
								A.order_idx > 0
								AND A.order_is_del = N'N' 
								AND A.order_idx = A.order_pack_idx 
								 
							";
//And A.order_progress_step not in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_ACCEPT_TEMP')
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}

if(count($qryWhereOrderSearch) > 0)
{
	$args["qry_where"] .= "	And A.order_idx in (
						Select order_pack_idx 
							From DY_ORDER pO
								Left Outer Join DY_ORDER_PRODUCT_MATCHING pM On pO.order_idx = pM.order_idx And pM.order_matching_is_del = N'N' 
								Left Outer Join DY_PRODUCT_OPTION pPO On pM.product_option_idx = pPO.product_option_idx And pPO.product_option_is_del = N'N'
								Left Outer Join DY_PRODUCT pP On pP.product_idx = pPO.product_idx And pP.product_is_del = N'N'
							Where pO.order_is_del = N'N' 
									 
	";
	$args['qry_where'] .= " And " . join(" And ", $qryWhereOrderSearch);

	$args["qry_where"] .= " ) ";
}

if(count($qryWhereCSSearch) > 0)
{
	$args["qry_where"] .= "	And A.order_idx in (
						Select order_pack_idx 
							From DY_ORDER_CS CS
							Where CS.cs_is_del = N'N'  
	";
	$args['qry_where'] .= " And " . join(" And ", $qryWhereCSSearch);

	$args["qry_where"] .= " ) ";
}

if(count($qryWherePackOrderSearch) > 0) {
	$args['qry_where'] .= " And " . join(" And ", $qryWherePackOrderSearch);
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

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"]     = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>