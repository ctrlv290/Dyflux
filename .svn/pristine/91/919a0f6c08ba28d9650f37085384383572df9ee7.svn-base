<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일자별 재고조회 리스트 JSON
 */
//Page Info
$pageMenuIdx = 113;
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
    "stock_status",
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
			}elseif(trim($col) == "stock_status"){
                $stock_status = "$val";
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

		//입고 확정 된 기간
		$_searchWhereStockProcDate = "	 
			And (
				stock_is_confirm_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And stock_is_confirm_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		//송장 입력 기간 직전 7일
		$tmpDate = strtotime($_search_paramAryList["date_start"]);
		$tmpDate1 = strtotime("-7 days", $tmpDate);
		$tmpDate2 = strtotime("-1 days", $tmpDate);
		$prevWeekStartDate = date('Y-m-d', $tmpDate1);
		$prevWeekEndDate = date('Y-m-d', $tmpDate2);
		$_searchWhereStockInvoiceDatePrevWeek = "	 
			And (
				stock_invoice_date >= '".$prevWeekStartDate." 00:00:00' 
				And stock_invoice_date <= '".$prevWeekEndDate." 23:59:59'
			) 
		";

		//송장 입력일 검색기간
		$_searchWhereStockInvoiceDate = "	 
			And (
				stock_invoice_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And stock_invoice_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		//배송일 검색기간
		$_searchWhereStockShippedDate = "	 
			And (
				stock_shipped_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And stock_shipped_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		//마지막 날짜 로 입고 확정 된 기간
		$_searchWhereStockProcDateLast = "	 
			And (
				stock_is_confirm_date >= '".$_search_paramAryList["date_end"]." 00:00:00' 
				And stock_is_confirm_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		//발주 기간
		$_searchWhereStockOrderDate = "	 
			And (
				stock_request_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And stock_request_date <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		//발주서 등록 기간
		$_searchWhereStockOrderRegDate = "	 
			And (
				stock_order_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And stock_order_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59'
			) 
		";

		$start_date = $_search_paramAryList["date_start"];
		$end_date = $_search_paramAryList["date_end"];

		$_search_date_ary = array();
		$_qry_date_ary = array();

		$start_date = strtotime($start_date);
		$end_date = strtotime($end_date);


		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $start_date);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $end_date);

	}

    $C_ListTable->db_connect();
// str_split
    $qry = "CALL str_split('".implode(",", $_search_date_ary)."',',');";
    $C_ListTable->multiQuery($qry);

    do {
        if ($rst = $C_ListTable->storeResult()) {
            $rst->free();
        } else {
            if ($C_ListTable->getErrorNo()) {
                echo "Store failed: (" . $C_ListTable->getErrorNo() . ") " . $C_ListTable->getError();
            }
        }
    } while ($C_ListTable->moreResults() && $C_ListTable->nextResult());

	$_search_date_count = count($_search_date_ary);

	//print_r2($_search_date_ary);
	//exit;

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
//	if(
//		$_search_paramAryList["stock_status"] != ""
//		&& array_key_exists($_search_paramAryList["stock_status"], $GL_StockStatusList)
//	){
//		$_tmp_filed = $_search_paramAryList["stock_status"];
//		$qryWhereAry[] = "STOCK.stock_amount_" . $_tmp_filed . " > 0";
//	}

	//작업 상태
	if(
		$_search_paramAryList["stock_kind"] == "IN"
		|| $_search_paramAryList["stock_kind"] == "OUT"
		|| $_search_paramAryList["stock_kind"] == "RETURN"
		|| $_search_paramAryList["stock_kind"] == "STOCKORDER"
	) {

		if($_search_paramAryList["stock_kind"] == "IN"){

			$_qry_stock_kind = "
				And (stock_kind = N'STOCK_ORDER' Or stock_kind = N'ORDER' OR stock_kind = N'BACK')
				And stock_type = 1
				And stock_status = N'$stock_status'
			";
		}elseif($_search_paramAryList["stock_kind"] == "OUT") {
			$_qry_stock_kind = "
				And stock_type = -1
				And stock_status = N'$stock_status'
				
			";
            //And stock_status IN ('SHIPPED', 'FAC_RETURN_EXCHNAGE', 'FAC_RETURN_BACK', 'BAD_OUT_EXCHANGE', 'BAD_OUT_RETURN', 'DISPOSAL_PERMANENT', 'LOSS')
		}elseif($_search_paramAryList["stock_kind"] == "RETURN") {
			$_qry_stock_kind = "
				And stock_type = 1
				And stock_kind = N'RETURN' 
			";
		}

//		$qryWhereAry[] = "stock_amount_" . $_search_paramAryList["stock_kind"] . " > 0";
	}

	//재고 상태 [stock_alert]
	if($_search_paramAryList["stock_alert"] == "stock_warning" || $_search_paramAryList["stock_alert"] == "stock_warning_danger") {
		$qryWhereAry[] = " product_option_warning_count > stock_amount_NORMAL ";
	}
	if($_search_paramAryList["stock_alert"] == "stock_danger" || $_search_paramAryList["stock_alert"] == "stock_warning_danger") {
		$qryWhereAry[] = " product_option_danger_count > stock_amount_NORMAL ";
	}

	//공급처
	if($_search_paramAryList["supplier_idx_hidden"]){
		//$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx_hidden"]) . ")";
		$qryWhereAry[] = "P.supplier_idx = N'" . $_search_paramAryList["supplier_idx_hidden"] . "'";
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
$args['qry_table_idx'] 	= "PO.product_option_idx";
$args['qry_get_colum'] 	= " 
							STOCK.*
";
foreach($_qry_date_ary as $dd) {
	$args['qry_get_colum'] .= ", STOCK_DAILY.".$dd["colName"];
}
$args['qry_get_colum'] 	.= "
							, STOCK3.stock_amount_IN, STOCK3.stock_amount_OUT
							, STOCK4.stock_amount_STOCKORDER, STOCK4.stock_amount_ORDER_STOCKIN
							, STOCK44.stock_order_amount
							, STOCK5.stock_amount_INVOICE
							, STOCK6.stock_amount_SHIPPED
							, STOCK7.stock_amount_INVOICE_prevweek
							, P.product_img_main
							, P.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_1) as product_img_filename_1
							, P.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_2) as product_img_filename_2
							, P.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_3) as product_img_filename_3
							, P.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_4) as product_img_filename_4
							, P.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_5) as product_img_filename_5
							, P.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_6) as product_img_filename_6
							, P.product_name
							, CONCAT(P.product_supplier_name, ' ', P.product_supplier_option) as product_supplier_name
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
							";

foreach($_qry_date_ary as $dd) {
	$args['qry_table_name'] 	.= ", Sum(Case When confirm_date = '".$dd["date"]."' Then stock_amount_daily Else 0 End) as '".$dd["colName"]."'";
}


$args['qry_table_name'] 	.= " 
									From
									(
										SELECT val FROM str_split_temp
									) as DateTable
									Left Outer Join
									(
									Select 
										product_idx, product_option_idx, stock_unit_price
										, Sum(stock_amount * stock_type) as stock_amount_daily
										, confirm_date
									From 
										(
											Select *, convert(stock_is_confirm_date, DATE) as confirm_date From DY_STOCK 
											Where 
											stock_is_del = N'N' 
											And stock_is_confirm = N'Y'
											".$_qry_stock_kind."
										) ST
										Group by product_idx, product_option_idx, stock_unit_price, confirm_date
									) as STOCK_IN
									On DateTable.val = STOCK_IN.confirm_date
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK_DAILY 
								
								Left Outer Join (
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
								) as STOCK On STOCK.product_idx = STOCK_DAILY.product_idx And STOCK.product_option_idx = STOCK_DAILY.product_option_idx And STOCK.stock_unit_price = STOCK_DAILY.stock_unit_price
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
											OR (stock_status = 'MOVE') 
											Then stock_amount * stock_type 
											Else 0 
										End
									) as stock_amount_OUT
								From DY_STOCK ST3
									Where stock_is_del = N'N' And stock_is_confirm = N'Y' 
									$_searchWhereStockProcDate
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK3 On STOCK_DAILY.product_idx = STOCK3.product_idx And STOCK_DAILY.product_option_idx = STOCK3.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK3.stock_unit_price
								Left Outer Join (
								Select 
									product_idx, product_option_idx, stock_unit_price
									, Sum(
										Case When stock_kind = 'STOCK_ORDER' 
											And (stock_status = 'STOCK_ORDER_ADD' Or stock_status = 'STOCK_ORDER_READY')
											Then stock_due_amount
										 Else 0 End
									 ) as stock_amount_STOCKORDER
									, Sum(
										Case When (stock_kind = 'ORDER' Or stock_kind = 'RETURN' Or stock_kind = 'EXCHANGE') 
											And (stock_status = 'ORDER_RETURN') Then stock_due_amount
										 Else 0 End
									 ) as stock_amount_ORDER_STOCKIN
								From DY_STOCK ST4
									Where stock_is_del = N'N'
									$_searchWhereStockOrderDate
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK4 On STOCK_DAILY.product_idx = STOCK4.product_idx And STOCK_DAILY.product_option_idx = STOCK4.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK4.stock_unit_price
								
								Left Outer Join (
								Select 
									product_idx, product_option_idx, stock_unit_price
									, Sum(stock_due_amount) as stock_order_amount
								From DY_STOCK ST44 Inner Join DY_STOCK_ORDER O44 On ST44.stock_order_idx = O44.stock_order_idx
									Where stock_is_del = N'N' And O44.stock_order_is_order in ('Y', 'T')
									$_searchWhereStockOrderRegDate
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK44 On STOCK_DAILY.product_idx = STOCK44.product_idx And STOCK_DAILY.product_option_idx = STOCK44.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK44.stock_unit_price
								
								Left Outer Join (
								Select 
									product_idx, product_option_idx, stock_unit_price
									, Sum(stock_amount * stock_type) as stock_amount_INVOICE
								From DY_STOCK ST5
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									$_searchWhereStockInvoiceDate
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK5 On STOCK_DAILY.product_idx = STOCK5.product_idx And STOCK_DAILY.product_option_idx = STOCK5.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK5.stock_unit_price
								
								Left Outer Join (
								Select 
									product_idx, product_option_idx, stock_unit_price
									, Sum(stock_amount * stock_type) as stock_amount_SHIPPED
								From DY_STOCK ST6
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									$_searchWhereStockShippedDate
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK6 On STOCK_DAILY.product_idx = STOCK6.product_idx And STOCK_DAILY.product_option_idx = STOCK6.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK6.stock_unit_price
								
								Left Outer Join (
								Select 
									product_idx, product_option_idx, stock_unit_price
									, Sum(stock_amount * stock_type) as stock_amount_INVOICE_prevweek
								From DY_STOCK ST7
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									$_searchWhereStockInvoiceDatePrevWeek
									Group by product_idx, product_option_idx, stock_unit_price
								) as STOCK7 On STOCK_DAILY.product_idx = STOCK7.product_idx And STOCK_DAILY.product_option_idx = STOCK7.product_option_idx And STOCK_DAILY.stock_unit_price = STOCK7.stock_unit_price
								
								Inner Join DY_PRODUCT P On STOCK_DAILY.product_idx = P.product_idx 
								Inner Join DY_PRODUCT_OPTION PO On STOCK_DAILY.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join (
									Select product_option_idx, Sum(product_option_cnt)  as stock_amount_ACCEPT
										From DY_ORDER_PRODUCT_MATCHING OPM
											Inner Join DY_ORDER DO On OPM.order_idx = DO.order_idx
										Where 
											OPM.order_matching_is_del = N'N'
											And DO.order_is_del = N'N'
											And DO.order_progress_step = N'ORDER_ACCEPT'
										Group by product_option_idx 
								) Matching On PO.product_option_idx = Matching.product_option_idx
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


// 일자별 재고 합계가 0인 날짜 추출
// 엑셀다운로드 용 컬럼 날짜 배열 저장
$delArr = array();
$_excelDateArr = Array();
foreach ($_qry_date_ary as $ss) {
    $sumStock_cnt = "0";
    for($i=0; $i<=$listRst_cnt-1; $i++) {
        if($listRst[$i][$ss["colName"]] != 0)
            $sumStock_cnt ++;
        }
    if ($sumStock_cnt == 0) {
        array_push($delArr,$ss["colName"]);
    }else{
        $_excelDateArr[] = array(
            "date" => $ss["date"],
            "colName" => $ss["colName"]
        );
    }
}

// 일자별 재고 합계가 0인 데이터 삭제
foreach ($delArr as $dd) {
    for ($i = 0; $i <= $listRst_cnt - 1; $i++) {
            unset($WholeGetListResult['listRst'][$i][$dd]);
    }
}

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;
$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));

$userdata = array();
$userdata["period"] = date('m/d', $start_date) . " ~ " . date('m/d', $end_date);
$userdata["date_count"] = $_search_date_count;
$userdata["hide_date"] = $delArr;

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
$grid_response["userdata"] = array();
$grid_response["userdata"] = $userdata;

if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>