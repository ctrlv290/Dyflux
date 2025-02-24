<?php
/**
 * 홈 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Home extends Dbconn
{
	/**
	 * 당일매출현황
	 * @param $date
	 * @return array
	 */
	public function getTodaySalesSummary($date)
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}


		$qry = "
				Select
				Count(distinct order_idx) as cnt
				, Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat) as amt
				From DY_SETTLE
				Where
					settle_is_del = N'N'
					And settle_date = N'$date'
					$addQry
		";

		parent::db_connect();
		$ship_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
				Select
				Count(distinct order_idx) as cnt
				, Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat) as amt
				From DY_SETTLE
				Where
					settle_is_del = N'N'
					And settle_date = N'$date'
					And settle_type in (N'CANCEL')
					$addQry
		";
		parent::db_connect();
		$cancel_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
			Select 
				count(*) as cnt
			From DY_ORDER
			Where order_is_del = N'N' 
				And invoice_date between N'$date 00:00:00' And N'$date 23:59:59.997'
				$addQry
		";

		parent::db_connect();
		$invoice_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
			Select 
				count(*) as cnt
			From DY_ORDER
			Where order_is_del = N'N' 
				And shipping_date between N'$date 00:00:00' And N'$date 23:59:59.997'
				$addQry
		";

		parent::db_connect();
		$shipping_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$returnValue = array();
		$returnValue["sales_cnt"] = $ship_row["cnt"];
		$returnValue["sales_amt"] = $ship_row["amt"];
		$returnValue["cancel_cnt"] = $cancel_row["cnt"];
		$returnValue["cancel_amt"] = $cancel_row["amt"];
		$returnValue["invoice_cnt"] = $invoice_row["cnt"];
		$returnValue["shipped_cnt"] = $shipping_row["cnt"];

		return $returnValue;
	}

	/**
	 * 배송지연현황
	 * @return array
	 */
	public function getShippingDelay()
	{
		global $GL_Member;

		$addQry = " and order_idx in (Select order_idx From DY_ORDER_PRODUCT_MATCHING Where order_cs_status in (N'NORMAL', N'PRODUCT_CHANGE') ) ";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		//이번달 첫째날
		$date = strtotime(date("Y-m-01"));

		//3개월전 첫째날
		$date_3 =date("Y-m-d", strtotime("-3 month", $date));

		//x일전
		$date_6days = date("Y-m-d", strtotime("-6 days"));
		$date_5days = date("Y-m-d", strtotime("-5 days"));
		$date_4days = date("Y-m-d", strtotime("-4 days"));
		$date_3days = date("Y-m-d", strtotime("-3 days"));
		$date_2days = date("Y-m-d", strtotime("-2 days"));
		$date_1days = date("Y-m-d", strtotime("-1 days"));
		$date_0days = date("Y-m-d");


		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date < N'$date_3 00:00:00' 
						$addQry
		";

		parent::db_connect();
		$delay_3month_before = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_3 00:00:00'
						And order_progress_step_accept_date <= N'$date_6days 23:59:59.998'
						$addQry
						
		";

		parent::db_connect();
		$delay_3month_after = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_5days 00:00:00'
						And order_progress_step_accept_date <= N'$date_5days 23:59:59.998'
						$addQry
						
		";

		parent::db_connect();
		$delay_5day = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_4days 00:00:00'
						And order_progress_step_accept_date <= N'$date_4days 23:59:59.998'
						$addQry
						
		";

		parent::db_connect();
		$delay_4day = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_3days 00:00:00'
						And order_progress_step_accept_date <= N'$date_3days 23:59:59.998'
						$addQry
						
		";

		parent::db_connect();
		$delay_3day = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_2days 00:00:00'
						And order_progress_step_accept_date <= N'$date_2days 23:59:59.998'
						$addQry
						
		";

		parent::db_connect();
		$delay_2day = parent::execSqlOneCol($qry);
		parent::db_close();

		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_1days 00:00:00'
						And order_progress_step_accept_date <= N'$date_1days 23:59:59.998'
						$addQry
		";

		parent::db_connect();
		$delay_1day = parent::execSqlOneCol($qry);
		parent::db_close();


		$qry = "
			Select 
				Count(*)
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_progress_step_accept_date >= N'$date_0days 00:00:00'
						And order_progress_step_accept_date <= N'$date_0days 23:59:59.998'
						$addQry
		";

		parent::db_connect();
		$delay_0day = parent::execSqlOneCol($qry);
		parent::db_close();


		$returnValue = array();
		$returnValue["3month_before"] = $delay_3month_before;
		$returnValue["3month_after"] = $delay_3month_after;
		$returnValue["5day"] = $delay_5day;
		$returnValue["4day"] = $delay_4day;
		$returnValue["3day"] = $delay_3day;
		$returnValue["2day"] = $delay_2day;
		$returnValue["1day"] = $delay_1day;
		$returnValue["0day"] = $delay_0day;

		/*기간*/
		$returnValue["3month_before_start"] = $date_3;
		$returnValue["3month_after_start"] = $date_3;
		$returnValue["5day_start"] = $date_5days;
		$returnValue["4day_start"] = $date_4days;
		$returnValue["3day_start"] = $date_3days;
		$returnValue["2day_start"] = $date_2days;
		$returnValue["1day_start"] = $date_1days;
		$returnValue["0day_start"] = $date_0days;

		$returnValue["3month_before_end"] = $date_3;
		$returnValue["3month_after_end"] = $date_6days;
		$returnValue["5day_end"] = $date_5days;
		$returnValue["4day_end"] = $date_4days;
		$returnValue["3day_end"] = $date_3days;
		$returnValue["2day_end"] = $date_2days;
		$returnValue["1day_end"] = $date_1days;
		$returnValue["0day_end"] = $date_0days;

		$returnValue["sum"] = $delay_0day + $delay_1day + $delay_2day + $delay_3day + $delay_4day + $delay_5day;

		return $returnValue;

	}

	/**
	 * 재고현황
	 * @return array
	 */
	public function getStock()
	{
		global $GL_Stock_InWhereHouse, $GL_Stock_OutWhereHouse;

		//총재고
		$qry = "
			Select
			Sum(stock_amount * stock_type) as cnt
			From DY_STOCK
			Where stock_is_del = N'N' And stock_is_confirm = N'Y'
				And stock_status IN ($GL_Stock_InWhereHouse)
		";

		parent::db_connect();
		$sum = parent::execSqlOneCol($qry);
		parent::db_close();

		$today = date("Y-m-d");

		//오늘 입고
		$qry = "
			Select
			Sum(stock_amount * stock_type) as cnt
			From DY_STOCK
			Where stock_is_del = N'N' And stock_is_confirm = N'Y' 
			    And stock_is_confirm_date >= N'$today 00:00:00'
			    And stock_is_confirm_date <= N'$today 23:59:59.998'
				And stock_status IN ($GL_Stock_InWhereHouse)
				And stock_kind in (N'STOCK_ORDER', N'BACK')
		";

		parent::db_connect();
		$in = parent::execSqlOneCol($qry);
		parent::db_close();

		//오늘 출고
		$qry = "
			Select
			Sum(stock_amount * stock_type) as cnt
			From DY_STOCK
			Where stock_is_del = N'N' And stock_is_confirm = N'Y' 
			    And stock_is_confirm_date >= N'$today 00:00:00'
			    And stock_is_confirm_date <= N'$today 23:59:59.998'
				And stock_status IN ($GL_Stock_OutWhereHouse)
		";

		parent::db_connect();
		$out = parent::execSqlOneCol($qry);
		parent::db_close();

		//오늘 배송
		$qry = "
			Select
			Sum(stock_amount * stock_type) as cnt
			From DY_STOCK
			Where stock_is_del = N'N' And stock_is_confirm = N'Y' 
			    And stock_is_confirm_date >= N'$today 00:00:00'
			    And stock_is_confirm_date <= N'$today 23:59:59.998'
				And stock_status = N'ORDER_SHIPPED'
		";
		parent::db_connect();
		$shipped = parent::execSqlOneCol($qry);
		parent::db_close();

		//오늘 불량
		$qry = "
			Select
			Sum(stock_amount * stock_type) as cnt
			From DY_STOCK
			Where stock_is_del = N'N' And stock_is_confirm = N'Y' 
			    And stock_is_confirm_date >= N'$today 00:00:00'
			    And stock_is_confirm_date <= N'$today 23:59:59.998'
				And stock_status = N'BAD'
		";
		parent::db_connect();
		$bad = parent::execSqlOneCol($qry);
		parent::db_close();

		//재고 경고
		$qry = "
			Select
				count(*)
			From 
				 (
					Select
			        product_idx, product_option_idx
					, isNull(Sum(stock_amount * stock_type), 0) as stock_cnt
					From DY_STOCK
					Where stock_is_del = N'N' And stock_is_confirm = N'Y'
						And stock_status = N'NORMAL'
			        Group by product_idx, product_option_idx
				 ) S
			     Inner Join DY_PRODUCT P On S.product_idx = P.product_idx
			     Inner Join DY_PRODUCT_OPTION PO On S.product_option_idx = PO.product_option_idx
			Where 
			      P.product_is_del = N'N'
			      And P.product_is_trash = N'N' 
			      And product_option_is_del = N'N'
			      And P.product_sale_type = N'SELF' 
				  And (
				    PO.product_option_warning_count > S.stock_cnt
				  )
		";
		//					Or isNull(S.stock_cnt, 0) = 0
		parent::db_connect();
		$warning = parent::execSqlOneCol($qry);
		parent::db_close();


		$returnValue = array();
		$returnValue["sum"] = $sum;
		$returnValue["in"] = $in;
		$returnValue["out"] = $out;
		$returnValue["bad"] = $bad;
		$returnValue["shipped"] = $shipped;
		$returnValue["warning"] = $warning;

		return $returnValue;
	}

	/**
	 * 재고현황 - 벤더사용
	 * @return array
	 */
	public function getStockForVendor()
	{
		global $GL_Member, $GL_Stock_InWhereHouse, $GL_Stock_OutWhereHouse;

		//현재고
		$qry = "
			Select
			isNull(Sum(stock_amount_NORMAL), 0) as cnt
			From 
		     (
				Select 
					ST.product_idx, ST.product_option_idx
					, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
				From DY_STOCK ST
					Where stock_is_del = N'N' And stock_is_confirm = N'Y'
					Group by ST.product_idx, product_option_idx
			 ) as STOCK
			Left Outer Join DY_PRODUCT P On P.product_idx = STOCK.product_idx 
			Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = STOCK.product_option_idx 
			Where P.product_sale_type = N'SELF' 
				And P.product_is_del = N'N' 
				And P.product_is_trash = N'N' 
				And P.product_is_use = N'Y'
				And 
					(
						P.product_vendor_show = N'ALL'
						Or
						(
							P.product_vendor_show = N'SELECTED'
							And P.product_idx in (
								Select product_idx
								From DY_PRODUCT_VENDOR_SHOW
								Where vendor_idx = N'".$GL_Member["member_idx"]."'
									And product_vendor_show_is_use = N'Y'
									And product_vendor_show_is_del = N'N'
							)
						)
					)
				And PO.product_option_is_use = N'Y'
		";

		parent::db_connect();
		$sum = parent::execSqlOneCol($qry);
		parent::db_close();

		$today = date("Y-m-d");


		$returnValue = array();
		$returnValue["sum"] = $sum;

		return $returnValue;
	}

	/**
	 * 미처리현황
	 * @return array
	 */
	public function getYet()
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and O.seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		//출력예정 - 일반
		$qry = "
			Select 
			count(*) as cnt
			From DY_ORDER O 
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
			Where O.order_is_del = N'N' And M.order_matching_is_del = 'N' And P.product_sale_type = N'SELF'
					And O.order_progress_step = N'ORDER_ACCEPT' And O.order_is_pack = N'N'
					$addQry
		";
		parent::db_connect();
		$print = parent::execSqlOneCol($qry);
		parent::db_close();

		//출력예정 - 합포
		$qry = "
			Select 
			count(distinct O.order_pack_idx) as cnt
			From DY_ORDER O 
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
			Where O.order_is_del = N'N' And M.order_matching_is_del = 'N' And P.product_sale_type = N'SELF'
					And O.order_progress_step = N'ORDER_ACCEPT' And O.order_is_pack = N'Y'
					$addQry
		";
		parent::db_connect();
		$print_pack = parent::execSqlOneCol($qry);
		parent::db_close();

		//송장전송대기
		$qry = "
			Select 
			count(distinct O.order_pack_idx) as cnt
			From DY_ORDER O 
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
			Where O.order_is_del = N'N' And M.order_matching_is_del = 'N' And P.product_sale_type = N'SELF'
					And O.order_progress_step = N'ORDER_INVOICE'
			        And O.market_invoice_regdate is not null
			        And O.market_invoice_state = N'N'
					$addQry
		";
		parent::db_connect();
		$send_invoice_ready = parent::execSqlOneCol($qry);
		parent::db_close();

		$returnValue = array();
		$returnValue["print"] = $print;
		$returnValue["print_pack"] = $print_pack;
		$returnValue["send"] = $send_invoice_ready;

		return $returnValue;
	}

	/**
	 * 반품현황
	 * @return array
	 */
	public function getReturn()
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and O.seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		$date = strtotime("-3 months");
		$date_ymd = date("Y-m-d", $date);
		$cancel_date = date("m/d", $date);

		//배송후 취소
		$qry = "
			Select 
			Count(*)
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
			Where O.order_is_del = 'N' And M.order_matching_is_del = 'N'
			        And M.order_cs_status = N'ORDER_CANCEL'
					And M.product_cancel_shipped = N'Y'
					And M.product_cancel_date >= N'$date_ymd 00:00:00'
					$addQry
		";
		parent::db_connect();
		$cancel_cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		$returnValue = array();
		$returnValue["cancel_cnt"] = $cancel_cnt;
		$returnValue["cancel_date_ymd"] = $date_ymd;
		$returnValue["cancel_date"] = $cancel_date;

		//장기지연
		//등록 된지 2주이상
		$date = strtotime("-2 weeks");
		$date_ymd = date("Y-m-d", $date);
		$delay_date = date("Y-m-d", strtotime("-1 day", $date));
		$delay_date_forsearch = date("m/d", strtotime("-1 day", $date));

		$date_start = strtotime("-2 months");
		$date_start_ymd = date("Y-m-d", $date_start);
		$delay_date_start = date("Y-m-d", strtotime("-1 day", $date_start));
		$delay_date_forsearch_start = date("m/d", strtotime("-1 day", $date_start));

		$qry = "
			Select 
			count(*)
			From DY_ORDER_RETURN R
			Where return_is_del = N'N' 
			  And return_regdate >= '$date_start_ymd 00:00:00' 
			  And return_regdate < '$date_ymd 00:00:00' 
			  And return_is_confirm = N'N'
		";

		parent::db_connect();
		$delay_cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		$returnValue["delay_cnt"] = $delay_cnt;
		$returnValue["delay_date_end"] = $delay_date;
		$returnValue["delay_date_forsearch_end"] = $delay_date_forsearch;
		$returnValue["delay_date_start"] = $delay_date_start;
		$returnValue["delay_date_forsearch_start"] = $delay_date_forsearch_start;

		return $returnValue;
	}


	/**
	 * 신규 제품 목록
	 * @return array
	 */
	public function getLastProduct()
	{
		global $GL_Member;
		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                And seller_idx = N'".$GL_Member["member_idx"]."'
					";
			}
		}

		$qry = "
			Select Top 5 product_idx, product_name
			From DY_PRODUCT
			Where product_is_del = N'N'
			$addQry
			Order by product_regdate DESC
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 공지사항
	 * @return array
	 */
	public function getHomeNotice()
	{
		global $GL_Member;
		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and ( bbs_target = N'ALL' Or bbs_target = N'SELLER' )
					";
			}elseif ($GL_Member["member_type"] == "SUPPLIER") {
				$addQry .= "
		                and ( bbs_target = N'ALL' Or bbs_target = N'SUPPLIER' )
					";
			}
		}

		$qry = "
			Select top 5 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'notice' And bbs_is_del = N'N'
			$addQry
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 디자인 게시판
	 * @return array
	 */
	public function getHomeDesign()
	{
		global $GL_Member;
		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and bbs_target_vendor_" .$GL_Member["vendor_grade"]. " = N'Y'
					";
			}
		}

		$qry = "
			Select top 5 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'design' And bbs_is_del = N'N'
			$addQry
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 업체 게시판
	 * @return array
	 */
	public function getHomeBiz()
	{
		global $GL_Member;
		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$view_member_idx = $GL_Member["member_idx"];
				$addQry = " And bbs_ref in (Select bbs_idx From DY_BBS Where member_idx = N'$view_member_idx') ";
			}
		}

		$qry = "
			Select top 5 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'biz' And bbs_is_del = N'N'
			$addQry
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	public function getLastestSalesAmount()
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		$qry = "
			Select 
			    Top 10
			    settle_date
				, Sum(settle_sale_supply) as settle_sale_supply
			From DY_SETTLE
			Where settle_is_del = N'N' And settle_type in (N'SHIPPED')
			$addQry
			Group by settle_date
			Order by settle_date desc
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		foreach($_list as $key => $row){
			$dt = date("m-d", strtotime($row["settle_date"]));
			$_list[$key]["dt"] = $dt;

			$settle_sale_supply = round($row["settle_sale_supply"] / 10000);
			$_list[$key]["val"] = $settle_sale_supply;
		}

		return $_list;
	}

	public function getLastestSalesCnt()
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		$qry = "
			Select 
			    Top 10
			    settle_date
				, count(settle_sale_supply) as settle_sale_supply
			From DY_SETTLE
			Where settle_is_del = N'N' And settle_type in (N'SHIPPED')
			$addQry
			Group by settle_date
			Order by settle_date desc
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		foreach($_list as $key => $row){
			$dt = date("m-d", strtotime($row["settle_date"]));
			$_list[$key]["dt"] = $dt;

			$_list[$key]["val"] = $row["settle_sale_supply"];
		}

		return $_list;
	}

	public function getLastestCancelCnt()
	{
		global $GL_Member;

		$addQry = "";
		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}

		$qry = "
			Select 
			    Top 10
			    settle_date
				, count(settle_sale_supply) as settle_sale_supply
			From DY_SETTLE
			Where settle_is_del = N'N' And settle_type in (N'CANCEL')
			$addQry
			Group by settle_date
			Order by settle_date desc
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		foreach($_list as $key => $row){
			$dt = date("m-d", strtotime($row["settle_date"]));
			$_list[$key]["dt"] = $dt;

			$_list[$key]["val"] = $row["settle_sale_supply"];
		}

		return $_list;
	}

	public function getFavList()
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Select fav_idx, menu_idx From DY_MEMBER_FAV Where member_idx = N'$last_member_idx' Order by fav_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		$C_SiteMenu = new SiteMenu();

		$_return = array();

		$i=0;
		foreach($_list as $row) {
			$MenuInfoAry = array();
			$fav_idx = $row["fav_idx"];
			$menuLoopIdx = $row["menu_idx"];
			while (true) {
				$i++;
				$tmp = "";
				$tmp = $C_SiteMenu->getMenuInfo($menuLoopIdx);
				array_unshift($MenuInfoAry, $tmp);
				$menuLoopIdx = $tmp["parent_idx"];
				if ($tmp["parent_idx"] == 0 || $i == 5) {
					break;
				}
			}
			$_return[$fav_idx] = $MenuInfoAry;
		}


		return $_return;
	}

	/**
	 * 당일매출현황
	 * @param $period
	 * @return array
	 */
	public function getTodaySalesSummaryMobile($period)
	{
		global $GL_Member;

		if($period == "today"){
			$date = date('Y-m-d');
			$settleQry = " And settle_date = N'$date' ";
			$orderQry = " And order_progress_step_accept_date between N'$date 00:00:00' And N'$date 23:59:59.997' ";
			$invoiceQry = " And invoice_date between N'$date 00:00:00' And N'$date 23:59:59.997' ";
			$shippedQry = " And shipping_date between N'$date 00:00:00' And N'$date 23:59:59.997' ";
		}elseif($period == "week"){
			$date_prev = date('Y-m-d', strtotime("-6 day"));
			$date = date('Y-m-d');
			$settleQry = " And settle_date between N'$date_prev' and N'$date' ";
			$orderQry = " And order_progress_step_accept_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";
			$invoiceQry = " And invoice_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";
			$shippedQry = " And shipping_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";

		}elseif($period == "month"){
			$date_prev = date('Y-m-01');
			$date = date('Y-m-t');
			$settleQry = " And settle_date between N'$date_prev' and N'$date' ";
			$orderQry = " And order_progress_step_accept_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";
			$invoiceQry = " And invoice_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";
			$shippedQry = " And shipping_date between N'$date_prev 00:00:00' And N'$date 23:59:59.997' ";
		}


		$qry = "
				Select
				Count(distinct order_idx) as cnt
				, Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat) as amt
				, Sum(settle_purchase_supply + settle_purchase_delivery_in_vat) as amt_purchase
				From DY_SETTLE
				Where
					settle_is_del = N'N'
					$settleQry
		";


		parent::db_connect();
		$settle_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
			Select 
				count(*) as cnt
			From DY_ORDER
			Where order_is_del = N'N' 
				And order_progress_step IN (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
				$orderQry
		";

		parent::db_connect();
		$order_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
			Select 
				count(distinct order_idx) as cnt
			From DY_ORDER
			Where order_is_del = N'N' 
				$invoiceQry
		";

		parent::db_connect();
		$invoice_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$qry = "
			Select 
				count(distinct order_idx) as cnt
			From DY_ORDER O
			Where order_is_del = N'N'
				$shippedQry
		";

		parent::db_connect();
		$shipping_row = parent::execSqlOneRow($qry);
		parent::db_close();


		$returnValue = array();
		$returnValue["sales_cnt"] = $settle_row["cnt"];
		$returnValue["sales_amt"] = $settle_row["amt"];
		$returnValue["purchase_amt"] = $settle_row["amt_purchase"];
		$returnValue["order_cnt"] = $order_row["cnt"];
		$returnValue["invoice_cnt"] = $invoice_row["cnt"];
		$returnValue["shipped_cnt"] = $shipping_row["cnt"];

		return $returnValue;
	}

	/**
	 * 충전금 부족 업체 리스트
	 * @return array
	 */
	public function getNotEnoughChargeVendorList()
	{
		$qry = "
			Select *
			    FROM 
	            (
					Select V.member_idx, V.vendor_name 
						, (Select isNull(Sum(charge_amount * charge_inout), 0)	 From DY_MEMBER_VENDOR_CHARGE C Where C.member_idx = M.idx And charge_is_del = N'N' And charge_inout = 1)
						 - (Select isNull(Sum(settle_sale_sum), 0) From DY_SETTLE S Where S.seller_idx = M.idx And S.settle_is_del = N'N')
						 + (Select isNull(Sum(ledger_tran_amount), 0) From DY_LEDGER L Where L.target_idx = M.idx And L.ledger_is_del = N'N' And L.charge_idx = 0) 
						 as remain_amount
					From 
					     DY_MEMBER M 
					         Inner Join DY_MEMBER_VENDOR V On M.idx = V.member_idx
					Where M.is_del = N'N' 
					  And M.is_use = N'Y'
					  And V.vendor_status = N'VENDOR_APPLY'
					  And V.vendor_use_charge = N'Y'
				) as A
			Where remain_amount < 0
			Order By vendor_name ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}
}
?>