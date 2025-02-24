<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입매출현황 [판매일보]  리스트 JSON
 */
//Page Info
$pageMenuIdx = 122;
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

$order_by = "T.settle_date ASC, T.order_idx ASC, T.settle_idx ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_name",
	"product_option_name",
	"search_column",
	"product_tax_type",
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
				|| $col == "product_tax_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}


	$date_search_col = "settle_date";
	$search_date_col_name = " settle_date as search_date ";
	$date_search_col_name_han = "정산일";
	if($_search_paramAryList["period_type"] == "settle_date"){
		$date_search_col = "settle_date";
		$search_date_col_name = " CASE WHEN T.settle_date != DATE_FORMAT(T.settle_regdate,'%Y-%m-%d')
		                               Then T.settle_date
                                       ELSE T.settle_regdate End AS search_date ";
		$date_search_col_name_han = "정산일";

		if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
			$qryWhereAry[] = "	 
			(
				$date_search_col >= '".$_search_paramAryList["date_start"]."' 
				And $date_search_col <= '".$_search_paramAryList["date_end"]."'
			) 
		";
		}

	}elseif($_search_paramAryList["period_type"] == "order_accept_regdate"){
        $date_search_col = "O.order_progress_step_accept_date";
        $search_date_col_name = " IFNULL(DATE_FORMAT(O.order_progress_step_accept_date, '%Y-%m-%d %H:%i:%s'), 
                                    Case when T.settle_date != DATE_FORMAT(T.settle_regdate,'%Y-%m-%d') 
                                    THEN T.settle_date
                                    ELSE T.settle_regdate END
			                      ) AS search_date ";
        $date_search_col_name_han = "접수일";

        if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
            $qryWhereAry[] = "	 
			(
				IFNULL($date_search_col , T.settle_date) >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And IFNULL($date_search_col ,T.settle_date) <= '".$_search_paramAryList["date_end"]." 23:59:59.998'
			) 
		";
        }
    }elseif($_search_paramAryList["period_type"] == "order_progress_step_accept_temp_date"){
		$date_search_col = "O.order_progress_step_accept_temp_date";
		$search_date_col_name = " IFNULL(DATE_FORMAT(O.order_progress_step_accept_temp_date, '%Y-%m-%d %H:%i:%s'), 
			                        Case when T.settle_date != DATE_FORMAT(T.settle_regdate,'%Y-%m-%d') 
			                        THEN IFNULL(DATE_FORMAT(O.order_progress_step_accept_date, '%Y-%m-%d %H:%i:%s'),T.settle_date)
		                        	ELSE IFNULL(DATE_FORMAT(O.order_progress_step_accept_date, '%Y-%m-%d %H:%i:%s'),DATE_FORMAT(T.settle_regdate,'%Y-%m-%d %H:%i:%s')) End
		                          ) AS search_date ";
        $date_search_col_name_han = "발주일";

		if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
			$qryWhereAry[] = "	 
			(
				IFNULL($date_search_col ,IFNULL(O.order_progress_step_accept_date,T.settle_date)) >= '".$_search_paramAryList["date_start"]." 00:00:00' 
				And IFNULL($date_search_col ,IFNULL(O.order_progress_step_accept_date,T.settle_date)) <= '".$_search_paramAryList["date_end"]." 23:59:59.998'
				
			) 
		";
		}
	}else{

		if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
			$qryWhereAry[] = "	 
			(
				$date_search_col >= '".$_search_paramAryList["date_start"]."' 
				And $date_search_col <= '".$_search_paramAryList["date_end"]."'
			) 
		";
		}
	}


	$_search_date_count = count($_search_date_ary);

    if ($_search_paramAryList["search_mode"] == "search"){
        if($_search_paramAryList["seller_idx"]) {
            $qryWhereAry[] = "T.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
//            $qryWhereAry[] = "T.seller_idx = N'" . $_search_paramAryList["seller_idx"] . "'";
        }
        if($_search_paramAryList["supplier_idx"]) {
            $qryWhereAry[] = "supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
//            $qryWhereAry[] = "T.supplier_idx = N'" . $_search_paramAryList["supplier_idx"] . "'";
        }
    } elseif ($_search_paramAryList["search_mode"] == "filter"){
        if($_search_paramAryList["filter_seller_idx"]) {
            $qryWhereAry[] = "T.seller_idx = N'" . $_search_paramAryList["filter_seller_idx"] . "'";
        }
        if($_search_paramAryList["filter_supplier_idx"]){
            $qryWhereAry[] = "T.supplier_idx = N'" . $_search_paramAryList["filter_supplier_idx"] . "'";
        }
    }
}

//if($_GET["sidx"] && $_GET["sord"])
//{
//	if($_GET["sidx"] == "search_date"){
		if($_search_paramAryList["period_type"] == "settle_date"){
//			$order_by = " T.settle_date " . $_GET["sord"];
            $order_by = " A.search_date " . $_GET["sord"];
		}elseif($_search_paramAryList["period_type"] == "order_progress_step_accept_temp_date") {
			$order_by = " A.search_date " . $_GET["sord"];
		}elseif($_search_paramAryList["period_type"] == "order_accept_regdate") {
            $order_by = " A.search_date " . $_GET["sord"];
        }
//	}else {
//
//		$order_by = $_GET["sidx"] . " " . $_GET["sord"];
//	}
//}


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
$args['qry_table_idx'] 	= "A.settle_idx";

//벤더사 로그인 여부 분기
if(isDYLogin()) {
	$args['qry_get_colum'] = " A.* ";
}else{
	$args['qry_get_colum'] 	= "
			A.settle_idx, A.settle_date, A.settle_closing, A.settle_type, A.order_idx, A.order_pack_idx, A.order_cs_status, A.order_progress_step_accept_date
			, A.cs_reason_cancel, A.market_order_no, A.product_idx, A.product_name, A.product_option_idx, A.product_option_name, A.product_option_cnt
			, A.product_sale_type, A.product_tax_type, A.product_option_sale_price, A.order_unit_price
			, A.settle_sale_supply, A.settle_sale_supply_ex_vat, A.settle_sale_commission_in_vat, A.settle_sale_commission_ex_vat, A.settle_delivery_in_vat, A.settle_delivery_ex_vat
			, A.settle_delivery_commission_in_vat, A.settle_delivery_commission_ex_vat
	 ";
}
$args['qry_get_colum'] 	.= " 
";

$qry_where			= " 
								where T.settle_is_del = N'N'
								";

if(count($qryWhereAry) > 0)
{
    $qry_where .= " And " . join(" And ", $qryWhereAry);
}

$args['qry_table_name'] 	= " 
								(SELECT T.* , S.seller_name, SUPPLIER.supplier_name
                                , O.order_name, O.order_tp_num, O.order_hp_num
                                , O.receive_name, O.receive_tp_num, O.receive_hp_num
                                , IFNULL(O.receive_addr1, '') as receive_addr1
                                , IFNULL(O.receive_addr2, '') as receive_addr2
                                , IFNULL(O.receive_zipcode, '') as receive_zipcode
                                , O.receive_memo, O.order_progress_step_accept_temp_date
                                , Case When T.order_cs_status = 'ORDER_CANCEL' Then
								(Select code_name From DY_CODE C_I Where C_I.parent_code = N'CS_REASON_CANCEL' And C_I.code = T.cs_reason_cancel)
							    End as cs_reason_cancel_text
							    , G.manage_group_name
							    , $search_date_col_name
								FROM DY_SETTLE T
                                LEFT JOIN DY_ORDER O On T.order_idx = O.order_idx
                                LEFT JOIN DY_SELLER S ON T.seller_idx = S.seller_idx
                                LEFT JOIN DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = T.supplier_idx
                                LEFT JOIN DY_MANAGE_GROUP G ON G.manage_group_idx = S.manage_group_idx
                                $qry_where 
                                UNION ALL
                                SELECT T.* , S.seller_name, SUPPLIER.supplier_name
                                , O.order_name, O.order_tp_num, O.order_hp_num
                                , O.receive_name, O.receive_tp_num, O.receive_hp_num
                                , IFNULL(O.receive_addr1, '') as receive_addr1
                                , IFNULL(O.receive_addr2, '') as receive_addr2
                                , IFNULL(O.receive_zipcode, '') as receive_zipcode
                                , O.receive_memo, O.order_progress_step_accept_temp_date
                                , Case When T.order_cs_status = 'ORDER_CANCEL' Then
								(Select code_name From DY_CODE C_I Where C_I.parent_code = N'CS_REASON_CANCEL' And C_I.code = T.cs_reason_cancel)
                                End as cs_reason_cancel_text
                                , G.manage_group_name
                                , $search_date_col_name
                                FROM DY_SETTLE_UPLOAD T
                                LEFT JOIN DY_ORDER O On T.order_idx = O.order_idx
                                LEFT JOIN DY_SELLER S ON T.seller_idx = S.seller_idx
                                LEFT JOIN DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = T.supplier_idx
                                LEFT JOIN DY_MANAGE_GROUP G ON G.manage_group_idx = S.manage_group_idx
                                $qry_where
                                )as A
";
//벤더사 로그인일 경우
if(!isDYLogin()){
	$args['qry_where'] .= " T.seller_idx = N'".$GL_Member["member_idx"]."'";
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


//합계
$qry = "Select
	IFNULL(Sum(CASE WHEN settle_type != 'UPLOAD' THEN settle_sale_supply End)
	- Sum(CASE WHEN settle_type != 'UPLOAD' THEN settle_sale_commission_in_vat End)
	+ Sum(CASE WHEN settle_type != 'UPLOAD' THEN settle_delivery_in_vat End)
	- Sum(settle_delivery_commission_in_vat), 0) as sale_sum
	, IFNULL(Sum(CASE WHEN settle_type = 'UPLOAD' THEN settle_sale_sum END),0) AS sale_sum_upload
	, IFNULL(Sum(CASE WHEN settle_type != 'UPLOAD' THEN settle_purchase_supply End) 
	+ Sum(CASE WHEN settle_type != 'UPLOAD' THEN settle_purchase_delivery_in_vat End), 0) as purchase_sum
	, IFNULL(Sum(CASE WHEN settle_type = 'UPLOAD' THEN settle_purchase_sum END),0) AS purchase_sum_upload 
	, IFNULL(Sum(settle_sale_profit), 0) as profit_sum
";

$qry .= " From ";
$qry .= $args['qry_table_name'];
//$qry .= " Where ";
//$qry .= $args['qry_where'];

$C_ListTable->db_connect();
$add_result = $C_ListTable->execSqlOneRow($qry);
$C_ListTable->db_close();

$userdata["sale_sum"] = $add_result["sale_sum"] + $add_result["sale_sum_upload"] ;
$userdata["purchase_sum"] = $add_result["purchase_sum"] + $add_result["purchase_sum_upload"];
$userdata["profit_sum"] = $add_result["profit_sum"];

//검색 결과 내 판매처
$qry = "SELECT seller.seller_idx,seller.seller_name,seller.manage_group_idx,manage.manage_group_name From ";
$qry .= $args['qry_table_name'];
$qry .= "LEFT outer JOIN dy_seller seller ON A.seller_idx = seller.seller_idx
        LEFT outer JOIN DY_MANAGE_GROUP manage ON seller.manage_group_idx = manage.manage_group_idx
        group BY seller.seller_idx,seller.seller_name,seller.manage_group_idx,manage.manage_group_name
        ORDER BY seller.seller_name ASC
        ";
$C_ListTable->db_connect();
$seller_result = $C_ListTable->execSqlList($qry);
$C_ListTable->db_close();
$userdata["seller_result"] = $seller_result;

//검색 결과 내 공급처
$qry = "SELECT supplier.member_idx,supplier.supplier_name,supplier.manage_group_idx,manage.manage_group_name From ";
$qry .= $args['qry_table_name'];
$qry .= "LEFT outer JOIN DY_MEMBER_SUPPLIER supplier ON A.supplier_idx = supplier.member_idx
        LEFT outer JOIN DY_MANAGE_GROUP manage ON supplier.manage_group_idx = manage.manage_group_idx
        group BY supplier.member_idx,supplier.supplier_name,supplier.manage_group_idx,manage.manage_group_name
        ORDER BY supplier.supplier_name ASC
        ";
$C_ListTable->db_connect();
$supplier_result = $C_ListTable->execSqlList($qry);
$C_ListTable->db_close();
$userdata["supplier_result"] = $supplier_result;

$userdata["search_mode"] = $_search_paramAryList["search_mode"];

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
$grid_response["userdata"] = $userdata;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
