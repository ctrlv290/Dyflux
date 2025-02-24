<?php
/**
 * User: ssawoona
 * Date: 2019
 * Desc: 배송장 출력을 위한 리스트 조회
 */
//Init
include_once "../_init_.php";
set_time_limit(300);

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$C_ListTable = new ListTable();



//******************************* 리스트 기본 설정 ******************************//
$_search_param    = $_GET["param"];
$print_date       = $_GET["print_date"];
$print_date_count = $_GET["print_date_count"];
$sort_field       = $_GET["srt"];

$chk_order_progress_step = "";
//검색 가능한 컬럼 지정
$available_col = array(
	"search_column",
	"order_progress_step",
	"delivery_code",
	"order_pack_idx",
	"receive_name",
);
$qryWhereAry = array();
//region *** param 으로 넘어 오는 검색 파라미터를 파싱 ***
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "order_progress_step") {
				$val_ary = explode(",", $val);
				$val_ary_quote = array_map(function($val){
					return "'" . $val . "'";
				}, $val_ary);
				$val_join = implode(", ", $val_ary_quote);
				$chk_order_progress_step = $val;
				$qryWhereAry[] = " order_progress_step IN (N" . $val_join . ")";
			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}elseif(trim($col) == "delivery_code"){
				$qryWhereAry[] = "A.".$col . " = N'" . $val . "'";
			}elseif(trim($col) == "order_pack_idx"){
				$qryWhereAry[] = "A.".$col . " = N'" . $val . "'";
			}else{
				$qryWhereAry[] = "A.". $col . " like N'%" . $val . "%'";
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

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx = '" . $_search_paramAryList["supplier_idx"] . "' ";
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx = '" . $_search_paramAryList["seller_idx"] . "' ";
	}
}
//endregion

$qry_where = "";
if(count($qryWhereAry) > 0)
{
	$qry_where .= " And " . join(" And ", $qryWhereAry);
}


$send_field = "";

// 송장 출력 보내는분정보 가져오기
$C_SiteInfo = new SiteInfo();
$_view = $C_SiteInfo->getSiteInfo();
if($_view)
{
	$send_field = "
		, '".$_view["invoice_name"]."' AS send_name
		, '".$_view["invoice_tel"]."' AS send_tel
		, '".$_view["invoice_addr"]."' AS send_addr
	";
}

$qry_invoice_join = " Left Outer Join ";
if($chk_order_progress_step == "ORDER_INVOICE" || $chk_order_progress_step == "ORDER_SHIPPED")
{
	/// 송장, 배송 상태에서는 DY-Invoice 를 통해 정상적으로 출력된 리스트만 나와야 함
	$qry_invoice_join = " Join ";
}
$qry_print_date = "		
		$qry_invoice_join CTE_TOP_DY_INVOICE_PRINT_LOG IV On A.order_pack_idx = IV.order_pack_idx 
			AND IV.order_by_num = 1
	";
if($print_date != "" && $print_date_count != "") {
	/// 차수로 검색할 경우
	$qry_print_date = "		
		Join CTE_TOP_DY_INVOICE_PRINT_LOG IV On A.order_pack_idx = IV.order_pack_idx 
			AND IV.print_date = '".$print_date."' AND IV.print_date_count = '".$print_date_count."'
	";
	$send_field = "
		, MAX(IV.send_name) AS send_name
		, MAX(IV.send_phone1) AS send_tel
		, MAX(IV.send_add) AS send_addr
	";
}
//echo $qey_print_date."<br />";
$query = "
	WITH CTE_TOP_DY_INVOICE_PRINT_LOG as (
		SELECT ROW_NUMBER() OVER(PARTITION BY order_pack_idx ORDER BY print_log_idx DESC) as order_by_num, *
		FROM DY_INVOICE_PRINT_LOG WHERE print_is_del = N'N' AND print_type = N'F'
	)
";
$query = $query . "
	SELECT TOP 300
	A.order_pack_idx
	,STUFF((
			SELECT
				'\n' + ST_P.product_name + '[' + ST_PO.product_option_name + '] - ' + CONVERT(NVARCHAR(100),  ST_OPM.product_option_cnt) + '개'
			FROM DY_ORDER ST_O 
				Inner Join DY_ORDER_PRODUCT_MATCHING ST_OPM On ST_O.order_idx = ST_OPM.order_idx AND ST_OPM.order_cs_status != N'ORDER_CANCEL'
				Left Outer Join DY_PRODUCT ST_P On ST_P.product_idx = ST_OPM.product_idx
				Left Outer Join DY_PRODUCT_OPTION ST_PO On ST_PO.product_option_idx = ST_OPM.product_option_idx
			WHERE ST_O.order_is_del = N'N' And ST_OPM.order_matching_is_del = N'N' And ST_O.order_pack_idx = A.order_pack_idx
		FOR XML PATH('') ),1,1,'') AS product_option_names
	,STUFF((
			SELECT
				',' + CONVERT(NVARCHAR(100), ST_PO.product_option_idx) + ':' + CONVERT(NVARCHAR(100), ST_OPM.product_option_cnt)
			FROM DY_ORDER ST_O 
				Inner Join DY_ORDER_PRODUCT_MATCHING ST_OPM On ST_O.order_idx = ST_OPM.order_idx AND ST_OPM.order_cs_status != N'ORDER_CANCEL'
				Left Outer Join DY_PRODUCT ST_P On ST_P.product_idx = ST_OPM.product_idx
				Left Outer Join DY_PRODUCT_OPTION ST_PO On ST_PO.product_option_idx = ST_OPM.product_option_idx
			WHERE ST_O.order_is_del = N'N' And ST_OPM.order_matching_is_del = N'N' And ST_O.order_pack_idx = A.order_pack_idx
		FOR XML PATH('') ),1,1,'') AS product_option_count
	, MAX(A.order_regdate) AS order_regdate
	, MAX(A.order_pay_date) AS order_pay_date
	, MAX(A.receive_name) AS receive_name
	, MAX(A.receive_zipcode) AS receive_zipcode
	, MAX(A.receive_addr1) + ' ' + MAX(A.receive_addr2) AS receive_addr
	, MAX(A.receive_memo) AS receive_memo
	, MAX(A.receive_hp_num) AS receive_hp_num
	, MAX(A.receive_tp_num) AS receive_tp_num
	, MAX(A.delivery_code) AS delivery_code
	, MAX(A.delivery_fee) AS delivery_fee	
	, MAX(A.order_progress_step_accept_date) AS order_progress_step_accept_date
	, MAX(A.order_progress_step) AS order_progress_step
	, MAX(DC.delivery_name) AS delivery_name	
	, MAX(S.supplier_name) AS supplier_name
	, MAX(SL.seller_name) AS seller_name
	, MAX(C.code_name) AS order_progress_step_han
	, MAX(A.invoice_no) AS invoice_no
	, MAX(IV.p_clsfcd) AS p_clsfcd
	, MAX(IV.p_subclsfcd) AS p_subclsfcd
	, MAX(IV.p_clsfaddr) AS p_clsfaddr
	, MAX(IV.p_clldlcbranshortnm) AS p_clldlcbranshortnm
	, MAX(IV.p_clldlvempnm) AS p_clldlvempnm
	, MAX(IV.p_clldlvempnicknm) AS p_clldlvempnicknm
	, MAX(IV.p_prngdivcd) AS p_prngdivcd
	, MAX(IV.p_farediv) AS p_farediv
	, MAX(IV.p_boxtyp) AS p_boxtyp
	, CASE WHEN MAX(A.order_progress_step) = 'ORDER_ACCEPT' THEN 'false' ELSE 'true' END AS stock_state	
	, '-' AS stock_state_han
	, CASE WHEN MAX(A.order_progress_step) = 'ORDER_ACCEPT' THEN 'false' ELSE 'true' END AS cj_reAddr_state
	, '-' AS cj_reAddr_state_han
	, '-' AS cj_reAddr_msg
	, '-' AS cj_reAddr_addr		
	$send_field
	FROM DY_ORDER A 
		Inner Join DY_ORDER_PRODUCT_MATCHING OPM On A.order_idx = OPM.order_idx AND OPM.order_cs_status != N'ORDER_CANCEL'
		Join DY_PRODUCT P On P.product_idx = OPM.product_idx AND P.product_sale_type = N'SELF'
		Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = OPM.product_option_idx
		Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = P.supplier_idx
		Join DY_SELLER SL On SL.seller_idx = A.seller_idx 
		Left Outer Join DY_DELIVERY_CODE DC On DC.market_code = N'DY' AND A.delivery_code = DC.delivery_code
		Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
		$qry_print_date
	WHERE  A.order_is_del = N'N' And  order_is_hold = 'N' And OPM.order_matching_is_del = N'N' $qry_where
	GROUP BY A.order_pack_idx
	ORDER BY A.order_pack_idx ASC
";
//echo $query;
$C_Dbconn = new Dbconn();
$C_Dbconn->db_connect();
$WholeGetListResult = $C_Dbconn->execSqlList($query);
$C_Dbconn->db_close();
//print_r2($grid_response);



//region *** 조회된 product_option_idx 의 재고 조회 ***
$ret_stock_list = array();
if($WholeGetListResult) {
	$arry_po_list = array();
	$str_po_list = "";
	foreach ($WholeGetListResult as $row) {
		//echo $row["product_option_count"] ."<br />";
		$tmp = explode(",", $row["product_option_count"]);
		foreach ($tmp as $val) {
			$tmp_po_idx = explode(":", $val)[0];
			$arry_po_list[] = $tmp_po_idx;
		}
	}
	$arry_po_list = array_unique($arry_po_list);
	$str_po_list = implode(",", $arry_po_list);
	//echo $str_po_list;
	if($str_po_list) {
		$query = "
			SELECT 
				STOCK.product_option_idx,
				Sum(Case When STOCK.stock_status = 'NORMAL' Then STOCK.stock_amount * STOCK.stock_type Else 0 End) AS stock_amount
			FROM DY_STOCK STOCK
			Inner Join DY_PRODUCT P On STOCK.product_idx = P.product_idx 
				AND P.product_sale_type = N'SELF' And P.product_is_del = N'N' 
				And P.product_is_trash = N'N' And P.product_is_use = N'Y'
			Inner Join DY_PRODUCT_OPTION PO On STOCK.product_option_idx = PO.product_option_idx
				And PO.product_option_is_use = N'Y'
			WHERE STOCK.stock_is_del = N'N' And STOCK.stock_is_confirm = N'Y'  
			AND STOCK.stock_status = 'NORMAL'
			AND STOCK.product_option_idx IN ($str_po_list)
			Group by STOCK.product_option_idx		
		";
		$C_Dbconn->db_connect();
		$ret_stock_list = $C_Dbconn->execSqlList($query);
		$C_Dbconn->db_close();

		//print_r2($ret_po_list);
	}
}
//endregion
//print_r2($ret_stock_list);

//region *** 재고체크해서 송장상태로 변경이 가능한지 체크 + CJ주소정제 가능 여부 체크 ***
$C_API_CJ_Invoice = new API_CJ_Invoice();
if($WholeGetListResult) {
	$check_stock_list = $ret_stock_list;
	foreach ($WholeGetListResult as $key => $row) {
		if($row["order_progress_step"] != "ORDER_ACCEPT") {
			break;
		}

		$stock_state = false;
		$tmp = explode(",", $row["product_option_count"]);
		$tmp_stock_list = $check_stock_list;
		//echo $row["order_pack_idx"] . "[".$row["product_option_names"] ."]->".$row["product_option_count"]."<Br />";
		foreach ($tmp as $val) {
			$tmp_po_info = explode(":", $val);
			$tmp_idx = -1;
			foreach ($check_stock_list as $idx => $stk) {
				if($stk["product_option_idx"] == $tmp_po_info[0]) {
					//echo "&nbsp;&nbsp;&nbsp;".$idx.".".$stk["product_option_idx"]." : " .$stk["stock_amount"]."==".$tmp_po_info[1]."<Br />";
					if((int)$stk["stock_amount"] >= (int)$tmp_po_info[1]) { // 재고가 주문 수량 보다 많으면..
						//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=>재고있다<Br />";
						$tmp_idx = $idx;
					}
				}
			}
			if($tmp_idx >= 0) {
				$stock_state = true;
				$check_stock_list[$tmp_idx]["stock_amount"] =
					(int)$check_stock_list[$tmp_idx]["stock_amount"] - (int)$tmp_po_info[1];
			} else {
				// 재고가 한개라도 부족하면 재고 부족처리하고 다음 주문 확인을 위해 롤백
				$stock_state = false;
				$check_stock_list = $tmp_stock_list;
				break;
			}
		}
		$WholeGetListResult[$key]["stock_state"]     = ($stock_state) ? 'true' : 'false';
		$WholeGetListResult[$key]["stock_state_han"] = ($stock_state) ? '송장가능' : '재고부족';

		/// *** CJ 주소정제 에러 유무 체크 ***
		$ret_cj_invoice = $C_API_CJ_Invoice->repCJAddress(array(
				order_pack_idx => $row["product_option_count"],
				receive_addr => $row["receive_addr"],
		));
		$WholeGetListResult[$key]["cj_reAddr_state"]     = ($ret_cj_invoice["p_errorcd"] == '0') ? 'true' : 'false';
		$WholeGetListResult[$key]["cj_reAddr_state_han"] = ($ret_cj_invoice["p_errorcd"] == '0') ? '성공' : '실패';
		$WholeGetListResult[$key]["cj_reAddr_msg"]       = $ret_cj_invoice["p_errormsg"];
		$WholeGetListResult[$key]["cj_reAddr_addr"]      = $ret_cj_invoice["receive_addr"];

		/// *** CJ 배송을 위해 전화번호 필수 체크 ***
		if($ret_cj_invoice["p_errorcd"] == '0') {
			$tel_check = false;
			$arry_tels = explode("-", $row["receive_hp_num"]);
			if (count($arry_tels) == 3) {
				$tel_check = true;
				if(substr($row["receive_hp_num"],0,1) != "0") {
					$tel_check = false;
				}
			}
			if(!$tel_check) {
				$arry_tels = explode("-", $row["receive_tp_num"]);
				if (count($arry_tels) == 3) {
					$tel_check = true;
					if(substr($row["receive_hp_num"],0,1) != "0") {
						$tel_check = false;
					}
				}
			} else {
				if(strlen($row["receive_hp_num"]) < 10) {
					$tel_check = false;
				}
			}



			$WholeGetListResult[$key]["tel_check"] = ($tel_check) ? 'true' : 'false';
			$WholeGetListResult[$key]["cj_phone_info"] = count($arry_tels);

			$WholeGetListResult[$key]["cj_reAddr_state"]     = ($tel_check) ? 'true' : 'false';
			$WholeGetListResult[$key]["cj_reAddr_state_han"] = ($tel_check) ? '성공' : '실패';
			$WholeGetListResult[$key]["cj_reAddr_msg"]       = ($tel_check) ? "" : "핸드폰번호가 올바르지 않습니다.";

		}


	}
}
//endregion



if($sort_field == "product") {
	//************ 정렬 1
	//foreach ((array) $WholeGetListResult as $key => $value) {
	//	$sort[$key] = $value['product_option_names'];
	//}
	//array_multisort($sort, SORT_ASC, $WholeGetListResult);
	//************
	//************ 정렬 2
	function cmp_asc($a, $b)
	{
		return strcmp($a["product_option_names"], $b["product_option_names"]);
	}
	function cmp_desc($a, $b)
	{
		return strcmp($b["product_option_names"], $a["product_option_names"]);
	}
	usort($WholeGetListResult, "cmp_asc");
	//************
}

//print_r2($WholeGetListResult);
$grid_response               = array();
$grid_response["rows"]       = $WholeGetListResult;
$grid_response["stock_list"] = $ret_stock_list;
//print_r2($grid_response);
echo json_encode($grid_response, true);


?>