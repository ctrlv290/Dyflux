<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 기간별 재고조회 리스트 JSON
 */
//Page Info
$pageMenuIdx = 112;
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

$order_by = "STOCK.product_option_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_sale_type",
	"without_soldout",
	"soldout_status",
	"soldout_temp_status",
	"supplier_idx",
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

		if(trim($val) && in_array($col, $available_col)) {
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
				|| $col == "product_sale_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "without_soldout"){
				$qryWhereAry[] = " stock_amount_NORMAL > 0 ";
			}elseif(trim($col) == "soldout_status"){
				//품절상태 조건
				if($val == "except_soldout"){
					//품절제외
					$qryWhereAry[] = " product_option_soldout = N'N' ";
				}elseif($val == "soldout"){
					//품절
					$qryWhereAry[] = " product_option_soldout = N'Y' ";
				}
			}elseif(trim($col) == "soldout_temp_status"){
				//일시품절상태 조건
				if($val == "except_soldout_temp"){
					//일시품절제외
					$qryWhereAry[] = " product_option_soldout_temp = N'N' ";
				}elseif($val == "soldout_temp"){
					//일시품절
					$qryWhereAry[] = " product_option_soldout_temp = N'Y' ";
				}
			}elseif(trim($col) == "search_column"){
                if(trim($_search_paramAryList["search_keyword"]) != "") {
                    if($val == "product_name_option_name"){
                        $qryWhereAry[] = "
							P.product_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
							Or 
							PO.product_option_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
						";
                    }else {
                        $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
                    }
                }
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){

	}

	if($_search_paramAryList["date_start2"] != "" && $_search_paramAryList["date_end2"] != ""){

		//입고 확정 된 기간
		$_searchWhereStockProcDate = "	 
			And (
				stock_is_confirm_date >= '".$_search_paramAryList["date_start2"]." 00:00:00' 
				And stock_is_confirm_date <= '".$_search_paramAryList["date_end2"]." 23:59:59'
			) 
		";

		//마지막 날짜 로 입고 확정 된 기간
		$_searchWhereStockProcDateLast = "	 
			And stock_is_confirm_date >= '".$_search_paramAryList["date_end2"]." 00:00:00'
			And stock_is_confirm_date <= '".$_search_paramAryList["date_end2"]." 23:59:59.998'
		";
		$_last_date = $_search_paramAryList["date_end2"];

		//발주 기간
		$_searchWhereStockOrderDate = "	 
			And (
				stock_request_date >= '".$_search_paramAryList["date_start2"]." 00:00:00' 
				And stock_request_date <= '".$_search_paramAryList["date_end2"]." 23:59:59'
			) 
		";
	}

	//상태 수량 검색
	if(
		$_search_paramAryList["stock_status_for_amount"] != ""
		&& array_key_exists($_search_paramAryList["stock_status_for_amount"], $GL_StockStatusList)
		&& ($_search_paramAryList["stock_amount_start"] != "" || $_search_paramAryList["stock_amount_end"] != "")){

		$_tmp_filed = $_search_paramAryList["stock_status_for_amount"];

		if($_search_paramAryList["stock_amount_start"] != ""){
			$_tmp_val_start = $_search_paramAryList["stock_amount_start"];
			if(validateNumber($_tmp_val_start))
			{
				$qryWhereAry[] = "STOCK.stock_amount_" . $_tmp_filed . " >= " . $_tmp_val_start;
			}
		}

		if($_search_paramAryList["stock_amount_end"] != ""){
			$_tmp_val_end = $_search_paramAryList["stock_amount_end"];
			if(validateNumber($_tmp_val_end)){
				$qryWhereAry[] = "STOCK.stock_amount_" . $_tmp_filed . " <= " . $_tmp_val_end;
			}
		}
	}

	//상태 검색
	if(
		$_search_paramAryList["stock_status"] != ""
		&& array_key_exists($_search_paramAryList["stock_status"], $GL_StockStatusList)
	){
		$_tmp_filed = $_search_paramAryList["stock_status"];
		$qryWhereAry[] = "STOCK.stock_amount_" . $_tmp_filed . " > 0";
	}

	//작업 상태
	if(
		$_search_paramAryList["stock_kind"] == "IN"
		|| $_search_paramAryList["stock_kind"] == "OUT"
		|| $_search_paramAryList["stock_kind"] == "RETURN"
		|| $_search_paramAryList["stock_kind"] == "SHIPPED"
		|| $_search_paramAryList["stock_kind"] == "BAD_last"
	) {

		if($_search_paramAryList["stock_kind_amount_start"] != ""){
			$_tmp_val_start = $_search_paramAryList["stock_kind_amount_start"];
			if(validateNumber($_tmp_val_start))
			{
				$qryWhereAry[] = "stock_amount_" . $_search_paramAryList["stock_kind"] . " >= " . $_tmp_val_start;
			}
		}

		if($_search_paramAryList["stock_kind_amount_end"] != ""){
			$_tmp_val_end = $_search_paramAryList["stock_kind_amount_end"];
			if(validateNumber($_tmp_val_end)){
				$qryWhereAry[] = "stock_amount_" . $_search_paramAryList["stock_kind"] . " <= " . $_tmp_val_end;
			}
		}

		$qryWhereAry[] = "stock_amount_" . $_search_paramAryList["stock_kind"] . " > 0";
	}
	
	//재고 상태 [stock_alert]
	if($_search_paramAryList["stock_alert"] == "stock_warning" || $_search_paramAryList["stock_alert"] == "stock_warning_danger") {
		$qryWhereAry[] = " product_option_warning_count > stock_amount_NORMAL ";
	}
	if($_search_paramAryList["stock_alert"] == "stock_danger" || $_search_paramAryList["stock_alert"] == "stock_warning_danger") {
		$qryWhereAry[] = " product_option_danger_count > stock_amount_NORMAL ";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
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
$args['qry_table_idx'] 	= "PO.product_option_idx";
$args['qry_get_colum'] 	= " 
							STOCK.*
							, STOCK2.stock_amount_NORMAL_last, STOCK2.stock_amount_BAD_last
							, STOCK3.stock_amount_IN, STOCK3.stock_amount_OUT, STOCK3.stock_amount_RETURN, STOCK3.stock_amount_INVOICE, STOCK3.stock_amount_SHIPPED
							, STOCK4.stock_amount_STOCKORDER
							, P.product_img_main
							, P.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_1) as product_img_filename_1
							, P.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_2) as product_img_filename_2
							, P.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_3) as product_img_filename_3
							, P.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_4) as product_img_filename_4
							, P.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_5) as product_img_filename_5
							, P.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_6) as product_img_filename_6
							, P.product_name
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_l_idx), '') as category_l_name, P.product_category_l_idx 
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_m_idx), '') as category_m_name, P.product_category_m_idx
							, PO.product_option_name
							, PO.product_option_sale_price
							, PO.product_option_warning_count
							, PO.product_option_danger_count
							, PO.product_option_soldout
							, PO.product_option_soldout_temp
							, P.product_regdate
							, S.supplier_name
                            ";


$args['qry_table_name'] 	= " 
								(
									Select 
										product_idx, product_option_idx, stock_unit_price
										, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
										, Sum(Case When stock_status = 'ABNORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_ABNORMAL
										, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
										, Sum(Case When stock_status = 'BAD_OUT_EXCHANGE' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_EXCHANGE
										, Sum(Case When stock_status = 'BAD_OUT_RETURN' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_RETURN
										, Sum(Case When stock_status = 'HOLD' Then stock_amount * stock_type Else 0 End) as stock_amount_HOLD
										, Sum(Case When stock_status = 'FAC_RETURN_EXCHNAGE' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_EXCHNAGE
										, Sum(Case When stock_status = 'FAC_RETURN_BACK' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_BACK
										, Sum(Case When stock_status = 'LOSS' Then stock_amount * stock_type Else 0 End) as stock_amount_LOSS
										, Sum(Case When stock_status = 'DISPOSAL' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL
										, Sum(Case When stock_status = 'DISPOSAL_PERMANENT' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL_PERMANENT
									From DY_STOCK ST
										Where stock_is_del = N'N' And stock_is_confirm = N'Y'
										Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK
								Left Outer Join (
									Select 
										product_idx, product_option_idx, stock_unit_price
										, IFNULL(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL_last
										, IFNULL(Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_BAD_last
									From DY_STOCK ST2
										Where stock_is_del = N'N' And stock_is_confirm = N'Y'
										$_searchWhereStockProcDateLast
										Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK2 On STOCK.product_idx = STOCK2.product_idx And STOCK.product_option_idx = STOCK2.product_option_idx And STOCK.stock_unit_price = STOCK2.stock_unit_price
								Left Outer Join (
									Select 
										product_idx, product_option_idx, stock_unit_price
										, Sum(Case When stock_kind = 'STOCK_ORDER' Then stock_amount * stock_type Else 0 End) as stock_amount_IN
										, Sum(
											Case When 
												(stock_status = 'SHIPPED') 
												OR (stock_status = 'FAC_RETURN_EXCHNAGE') 
												OR (stock_status = 'FAC_RETURN_BACK') 
												OR (stock_status = 'BAD_OUT_EXCHANGE') 
												OR (stock_status = 'BAD_OUT_RETURN') 
												OR (stock_status = 'DISPOSAL_PERMANENT') 
												OR (stock_status = 'LOSS') 
												Then stock_amount * stock_type 
												Else 0 
											End
										) as stock_amount_OUT
										, Sum(
											Case When stock_status = 'INVOICE'
												Then stock_amount * stock_type 
												Else 0 
											End
										) as stock_amount_INVOICE
										, Sum(
											Case When stock_status = 'SHIPPED'
												Then stock_amount * stock_type 
												Else 0 
											End
										) as stock_amount_SHIPPED
										, Sum(Case When stock_kind = 'RETURN' Then stock_amount * stock_type Else 0 End) as stock_amount_RETURN
									From DY_STOCK ST3
										Where stock_is_del = N'N'
										$_searchWhereStockProcDate
										Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK3 On STOCK.product_idx = STOCK3.product_idx And STOCK.product_option_idx = STOCK3.product_option_idx And STOCK.stock_unit_price = STOCK3.stock_unit_price
								Left Outer Join (
									Select 
										product_idx, product_option_idx, stock_unit_price
										, Sum(Case When stock_kind = 'STOCK_ORDER' And (stock_status = 'STOCK_ORDER_ADD' Or stock_status = 'STOCK_ORDER_READY') Then stock_due_amount Else 0 End) as stock_amount_STOCKORDER
									From DY_STOCK ST4
										Where stock_is_del = N'N'
										$_searchWhereStockOrderDate
										Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK4 On STOCK.product_idx = STOCK4.product_idx And STOCK.product_option_idx = STOCK4.product_option_idx And STOCK.stock_unit_price = STOCK4.stock_unit_price
								
								Inner Join DY_PRODUCT P On STOCK.product_idx = P.product_idx 
								Inner Join DY_PRODUCT_OPTION PO On STOCK.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
";
$args['qry_where']			= " 
								P.product_sale_type = N'SELF' 
								And P.product_is_del = N'N' 
								And P.product_is_trash = N'N' 
								And P.product_is_use = N'Y'
								And PO.product_option_is_use = N'Y'
								";
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
$userdata["last_date"] = $_last_date;

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