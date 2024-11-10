<?php
/**
 * 정산통계 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Settle extends Dbconn
{
	/**
	 * 매입매출현황 - Inline 수정
	 * @param $settle_idx
	 * @param $update_ary
	 */
	public function updateTransactionRow($settle_idx, $update_ary)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_SETTLE 
			Set
				settle_moddate = getdate()
				, settle_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
				, settle_was_edited = N'Y'

		";
		foreach($update_ary as $col => $val){

			$qry .= ", ".$col." = N'".$val."'";

		}
		$qry .= " Where settle_idx = N'$settle_idx'";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
	}

	/**
	 * 매입/매출 보정
	 * @param $args
	 */
	public function insertTransaction($args, $transaction_upload = false)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

        $insert_table = 'DY_SETTLE';
        if($transaction_upload){
            $insert_table = 'DY_SETTLE_UPLOAD';
        }

		$returnValue = false;

		$settle_date                        = "";
		$settle_type                        = "";
        $settle_closing                     = "";
		$seller_idx                         = "";
		$product_idx                        = "";
		$product_option_idx                 = "";
		$supplier_idx                       = "";
		$vendor_grade                       = "";
		$product_option_cnt                 = "";
		$purchase_amt                       = "";
		$order_unit_price                   = "";
		$order_unit_amt                     = "";
		$product_option_purchase_price      = "";
		$product_option_cnt                 = "";
		$settle_sale_supply                 = "";
		$settle_sale_supply_ex_vat          = "";
		$settle_sale_commission_ex_vat      = "";
		$settle_sale_commission_in_vat      = "";
		$settle_delivery_in_vat             = "";
		$settle_delivery_ex_vat             = "";
		$settle_delivery_commission_ex_vat  = "";
		$settle_delivery_commission_in_vat  = "";
		$settle_purchase_supply             = "";
		$settle_purchase_supply_ex_vat      = "";
		$settle_purchase_delivery_in_vat    = "";
		$settle_purchase_delivery_ex_vat    = "";
		$settle_sale_profit                 = "";
		$settle_sale_amount                 = "";
		$settle_sale_cost                   = "";
		$settle_memo                        = "";
		$settle_purchase_unit_supply        = "";
		$settle_purchase_unit_supply_ex_vat = "";
		$settle_settle_amt                  = "";
		$settle_ad_amt                      = "";

		$settle_sale_sum                   = 0;     //매출합계
		$settle_purchase_sum               = 0;     //매입합계
		extract($args);

		$settle_sale_supply                = str_replace(",", "", $settle_sale_supply);
		$settle_sale_commission_in_vat     = str_replace(",", "", $settle_sale_commission_in_vat);
		$settle_delivery_in_vat            = str_replace(",", "", $settle_delivery_in_vat);
		$settle_delivery_commission_in_vat = str_replace(",", "", $settle_delivery_commission_in_vat);
		$settle_purchase_supply            = str_replace(",", "", $settle_purchase_supply);
		$settle_purchase_delivery_in_vat   = str_replace(",", "", $settle_purchase_delivery_in_vat);

		if(!is_numeric($settle_sale_supply)) $settle_sale_supply = 0;
		if(!is_numeric($settle_sale_commission_in_vat)) $settle_sale_commission_in_vat = 0;
		if(!is_numeric($settle_delivery_in_vat)) $settle_delivery_in_vat = 0;
		if(!is_numeric($settle_delivery_commission_in_vat)) $settle_delivery_commission_in_vat = 0;
		if(!is_numeric($settle_purchase_supply)) $settle_purchase_supply = 0;
		if(!is_numeric($settle_purchase_delivery_in_vat)) $settle_purchase_delivery_in_vat = 0;

		//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
        if(!$transaction_upload){
		$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
		//매입합계 (매입가 + 매입배송비)
		$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;
        }
        if(!$transaction_upload) {
            parent::db_connect();
            parent::sqlTransactionBegin();  //트랜잭션 시작
        }
		/**
		 * 벤더사 충전금 사용 여부
		 */
		$vendor_use_charge = "N";
		$qry = "Select vendor_use_charge, vendor_grade From DY_SELLER Where seller_idx = N'$seller_idx'";
		$_row = parent::execSqlOneRow($qry);
		$vendor_use_charge = $_row["vendor_use_charge"];
		$vendor_grade = $_row["vendor_grade"];

		/**
		 * 공급처 선급금 사용여부
		 */
		$supplier_use_prepay = "N";
		$qry = "Select supplier_use_prepay From DY_MEMBER_SUPPLIER Where member_idx = N'$supplier_idx'";
		$supplier_use_prepay = parent::execSqlOneCol($qry);

		/*
		 * 상품관련 필요 사항
		 * 상품명, 상품옵션명, 상품판매타입, 상품세금종류, 상품매입배송비, 상품매출배송비
		 */
		$qry = "
			Select P.product_name, P.product_sale_type, P.product_tax_type, P.product_delivery_fee_buy, P.product_delivery_fee_sale
			, PO.product_option_name, PO.product_option_purchase_price
			From DY_PRODUCT P
			Inner Join DY_PRODUCT_OPTION PO On P.product_idx = PO.product_idx
			Where P.product_idx = N'$product_idx' And PO.product_option_idx = N'$product_option_idx'
		";

		$_product = parent::execSqlOneRow($qry);

		$product_name                  = $_product["product_name"];
		$product_option_name           = $_product["product_option_name"];
		$product_sale_type             = $_product["product_sale_type"];
		$product_tax_type              = $_product["product_tax_type"];
		$product_delivery_fee_buy      = $_product["product_delivery_fee_buy"];
		$product_delivery_fee_sale     = $_product["product_delivery_fee_sale"];
		$product_option_purchase_price = $_product["product_option_purchase_price"];

		$order_idx = 0;
		$order_pack_idx = 0;
		$order_cs_status = "";
		$order_progress_step_accept_date = "";
		$product_option_sale_price = $order_unit_price;
		$qry = "
			Insert Into $insert_table
			(
				settle_date, settle_type, settle_closing, order_idx, order_pack_idx, order_cs_status, order_progress_step_accept_date
				, seller_idx, supplier_idx, vendor_grade, vendor_use_charge, supplier_use_prepay
				, market_product_no, market_product_name, market_product_option
				, order_unit_price, order_amt, order_cnt
				, commission, delivery_commisision
				, delivery_fee, delivery_type, delivery_is_free
				, product_idx, product_name, product_option_idx, product_option_name, product_option_cnt, product_sale_type, product_tax_type
				, product_option_sale_price, product_option_purchase_price, product_delivery_fee_sale, product_delivery_fee_buy, stock_idx
				, settle_sale_supply, settle_sale_supply_ex_vat, settle_sale_commission_ex_vat, settle_sale_commission_in_vat
				, settle_delivery_in_vat, settle_delivery_ex_vat, settle_delivery_commission_ex_vat, settle_delivery_commission_in_vat
				, settle_purchase_supply, settle_purchase_supply_ex_vat, settle_purchase_delivery_in_vat, settle_purchase_delivery_ex_vat
				, settle_sale_profit, settle_sale_amount, settle_sale_cost
				, settle_purchase_unit_supply, settle_purchase_unit_supply_ex_vat
				, settle_sale_sum, settle_purchase_sum
				, settle_settle_amt, settle_ad_amt, settle_memo
				, settle_regip, last_member_idx
			) 
			VALUES 
			(
			 N'$settle_date'
			 , N'$settle_type'
			 , N'$settle_closing'
			 , N'$order_idx'
			 , N'$order_pack_idx'
			 , N'$order_cs_status'
			 , N'$order_progress_step_accept_date'
			 , N'$seller_idx'
			 , N'$supplier_idx'
			 , N'$vendor_grade'
			 , N'$vendor_use_charge'
			 , N'$supplier_use_prepay'
			 , N''
			 , N''
			 , N''
			 , N'$order_unit_price'
			 , N'0'
			 , N'$product_option_cnt'
			 , N'0'
			 , N'0'
			 , N'0'
			 , N''
			 , N''
			 , N'$product_idx'
			 , N'$product_name'
			 , N'$product_option_idx'
			 , N'$product_option_name'
			 , N'$product_option_cnt'
			 , N'$product_sale_type'
			 , N'$product_tax_type'
			 , N'$product_option_sale_price'
			 , N'$product_option_purchase_price'
			 , N'$product_delivery_fee_sale'
			 , N'$product_delivery_fee_buy'
			 , N'0'
			 , N'$settle_sale_supply'
			 , N'$settle_sale_supply_ex_vat'
			 , N'$settle_sale_commission_ex_vat'
			 , N'$settle_sale_commission_in_vat'
			 , N'$settle_delivery_in_vat'
			 , N'$settle_delivery_ex_vat'
			 , N'$settle_delivery_commission_ex_vat'
			 , N'$settle_delivery_commission_in_vat'
			 , N'$settle_purchase_supply'
			 , N'$settle_purchase_supply_ex_vat'
			 , N'$settle_purchase_delivery_in_vat'
			 , N'$settle_purchase_delivery_ex_vat'
			 , N'$settle_sale_profit'
			 , N'$settle_sale_amount'
			 , N'$settle_sale_cost'
			 , N'$settle_purchase_unit_supply'
			 , N'$settle_purchase_unit_supply_ex_vat'
			 , N'$settle_sale_sum'
			 , N'$settle_purchase_sum'
			 , N'$settle_settle_amt'
			 , N'$settle_ad_amt'
			 , N'$settle_memo'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		$inserted_idx = parent::execSqlInsert($qry);
		if(!$transaction_upload) {
		    parent::sqlTransactionCommit();     //트랜잭션 커밋
            parent::db_close();
		}
	}

	/**
	 * 판매일보 마감!
	 */
	public function transactionClose(){

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		//마감되지 않은 정산 테이블의 날짜를 가져온다
		$qry = "
			Select distinct settle_date
			From DY_SETTLE
			Where settle_closing = N'N' And settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL', N'ADJUST_SALE', N'ADJUST_PURCHASE')
		";
		parent::db_connect();
		$_date_list = parent::execSqlList($qry);
		parent::db_close();

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작
		if($_date_list) {
			foreach($_date_list as $_settle_date) {


				$settle_date       = $_settle_date["settle_date"];
				$settle_date_time  = strtotime($settle_date);
				$settle_date_year  = date('Y');
				$settle_date_month = date('m');
				$settle_date_day   = date('d');

				//해당 날짜의 상품별 합계 구하기
				$qry = "
					Select
					seller_idx, product_idx, product_option_idx
			        , count(*) as product_count
					, SUM(order_unit_price) as order_unit_price
					, SUM(settle_sale_supply) as settle_sale_supply
					, SUM(settle_sale_supply_ex_vat) as settle_sale_supply_ex_vat
					, SUM(settle_sale_commission_ex_vat) as settle_sale_commission_ex_vat
					, SUM(settle_sale_commission_in_vat) as settle_sale_commission_in_vat
					, SUM(settle_purchase_supply) as settle_purchase_supply
					, SUM(settle_purchase_supply_ex_vat) as settle_purchase_supply_ex_vat
					, SUM(settle_sale_profit) as settle_sale_profit
					, SUM(settle_purchase_unit_supply) as settle_purchase_unit_supply
					, SUM(settle_purchase_unit_supply_ex_vat) as settle_purchase_unit_supply_ex_vat
					From DY_SETTLE
					Where settle_is_del = N'N'
							And settle_date = N'$settle_date'
					Group by seller_idx, product_idx, product_option_idx
				";

				$_prod_sum_list = parent::execSqlList($qry);

				if ($_prod_sum_list) {
					foreach ($_prod_sum_list as $prod) {
						$seller_idx                         = $prod["seller_idx"];
						$product_idx                        = $prod["product_idx"];
						$product_option_idx                 = $prod["product_option_idx"];
						$product_count                      = $prod["product_count"];
						$order_unit_price                   = $prod["order_unit_price"];
						$settle_sale_supply                 = $prod["settle_sale_supply"];
						$settle_sale_supply_ex_vat          = $prod["settle_sale_supply_ex_vat"];
						$settle_sale_commission_ex_vat      = $prod["settle_sale_commission_ex_vat"];
						$settle_sale_commission_in_vat      = $prod["settle_sale_commission_in_vat"];
						$settle_purchase_supply             = $prod["settle_purchase_supply"];
						$settle_purchase_supply_ex_vat      = $prod["settle_purchase_supply_ex_vat"];
						$settle_sale_profit                 = $prod["settle_sale_profit"];
						$settle_purchase_unit_supply        = $prod["settle_purchase_unit_supply"];
						$settle_purchase_unit_supply_ex_vat = $prod["settle_purchase_unit_supply_ex_vat"];

						//이미 있는지 확인
						$qry = "
								Select count(*) From DY_SETTLE_PRODUCT 
								Where settle_date = N'$settle_date'
							        And seller_idx = N'$seller_idx' 
								    And product_idx = N'$product_idx' 
								    And product_option_idx = N'$product_option_idx'
						";
						$dup = parent::execSqlOneCol($qry);

						//없으면 Insert
						if ($dup == 0) {
							$qry = "
								Insert Into DY_SETTLE_PRODUCT 
	                              (
	                               settle_date, settle_date_year, settle_date_month, settle_date_day
	                               , seller_idx, product_idx, product_option_idx
	                              ) 
	                              VALUES
								  (
								   N'$settle_date', N'$settle_date_year', N'$settle_date_month', N'$settle_date_day'
								   , N'$seller_idx', N'$product_idx', N'$product_option_idx'
								  )							
							";
							$tmp = parent::execSqlInsert($qry);
						}

						//실제 Update
						$qry = "
							Update DY_SETTLE_PRODUCT
							Set
								product_count = N'$product_count'
								, order_unit_price = N'$order_unit_price'
								, settle_sale_supply = N'$settle_sale_supply'
								, settle_sale_supply_ex_vat = N'$settle_sale_supply_ex_vat'
								, settle_sale_commission_ex_vat = N'$settle_sale_commission_ex_vat'
								, settle_sale_commission_in_vat = N'$settle_sale_commission_in_vat'
								, settle_purchase_supply = N'$settle_purchase_supply'
								, settle_purchase_supply_ex_vat = N'$settle_purchase_supply_ex_vat'
								, settle_sale_profit = N'$settle_sale_profit'
								, settle_purchase_unit_supply = N'$settle_purchase_unit_supply'
								, settle_purchase_unit_supply_ex_vat = N'$settle_purchase_unit_supply_ex_vat'
							Where 
								settle_date = N'$settle_date'
								And seller_idx = N'$seller_idx' 
							    And product_idx = N'$product_idx' 
							    And product_option_idx = N'$product_option_idx'
						";
						$tmp = parent::execSqlUpdate($qry);
					}
				}
			}


			//벤더사 충전금 증가/차감 처리
			//마감되지 않은 정산들 중에
			//판매처가 벤더사 이고 충전금을 사용하는 벤더사 라면 모두 입력
			//19.05.03 변경!!!!!!!!!!!
			//충전금 소진을 입력하지 않고
			//판매일보 금액을 실시간으로 계산하여 소진금액으로 사용한다
			/*
			$qry = "
				Select
				S.settle_idx, S.settle_date, S.settle_sale_sum, S.order_idx
				, V.vendor_grade, V.member_idx
				From DY_SETTLE S 
				Left Outer Join DY_MEMBER_VENDOR V On S.seller_idx = V.member_idx
				Where V.member_idx is not null And V.vendor_use_charge = N'Y'
				And S.settle_closing = N'N' And S.settle_is_del = N'N'
			";

			$_vendor_sale_list = parent::execSqlList($qry);

			foreach($_vendor_sale_list as $v){

				$_settle_idx    = $v["settle_idx"];
				$_settle_date   = $v["settle_date"];
				$_member_idx    = $v["member_idx"];
				$_charge_inout  = 0;
				$_charge_amount = (int) $v["settle_sale_sum"];
				$_order_idx     = $v["order_idx"];
				$_vendor_grade  = $v["vendor_grade"];

//				if($_charge_amount > 0){
//					$_charge_inout = -1;
//				}elseif($_charge_amount < 0){
//					$_charge_inout = 1;
//				}
				//마감 금액은 무조건 charge_inout 이 -1
				//취소 금액은 금액 자체를 마이너스로 입력
				$_charge_inout = -1;

				$qry = "
					Insert Into DY_MEMBER_VENDOR_CHARGE
					(
					 charge_date, member_idx
					 , charge_inout, charge_amount
					 , settle_idx, order_idx
					 , vendor_grade, charge_memo
					 , charge_regip, charge_regidx
					 )
					VALUES
					(
					 N'$_settle_date'
					 , N'$_member_idx'
					 , N'$_charge_inout'
					 , N'$_charge_amount'
					 , N'$_settle_idx'
					 , N'$_order_idx'
					 , N'$_vendor_grade'
					 , N'발주마감'
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";

				$tmp_idx = parent::execSqlInsert($qry);
			}
			*/

			//마감 처리
			$qry = "
				Update DY_SETTLE
					Set settle_closing = N'Y', settle_closing_date = getdate(), settle_closing_member_idx = N'$last_member_idx'
				Where settle_closing = N'N' And settle_is_del = N'N'
			";
			$tmp = parent::execSqlUpdate($qry);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();
	}

	/**
	 * 마감 예정 정보 반환
	 * @return array
	 */
	public function getClosingInfo()
	{
		$returnValue = array();

		$qry = "
			Select count(*) as cnt, SUM(order_unit_price) as order_unit_price 
			From DY_SETTLE
			Where settle_closing = N'N' And settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL', N'ADJUST_SALE', N'ADJUST_PURCHASE')
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		$order_cnt = $rst["cnt"];
		$order_unit_price = $rst["order_unit_price"];

		$returnValue["order_cnt"] = $order_cnt;
		$returnValue["order_unit_price"] = $order_unit_price;

		return $returnValue;
	}

	/**
	 * 상품별 판매처별 통계
	 * @param $product_option_idx
	 * @param $date_start
	 * @param $date_end
	 * @return array
	 */
	public function getSettleProductEachSeller($product_option_idx, $date_start, $date_end)
	{
		$qry = "
			Select 
			product_idx, product_option_idx, SP.seller_idx, S.seller_name
			, Sum(settle_sale_supply) as settle_sale_supply
			, Sum(product_option_cnt) as product_count
			From DY_SETTLE SP
				Left Outer Join DY_SELLER S On SP.seller_idx = S.seller_idx
			Where 
		        product_option_idx = N'$product_option_idx'  
				And settle_date between N'$date_start' And N'$date_end'
			Group by product_idx, product_option_idx, SP.seller_idx, S.seller_name
			Order by product_option_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 당일판매요약표 - 판매처
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummarySeller($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " seller_idx = N'$seller_idx'";
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " product_name = N'$search_keyword'";
			}
		}

		if($except_cancel_order == "Y"){
			$qryWhereAry[] = " settle_type <> N'CANCEL'";
		}

		$qry = "
			WITH CTE_SUM_SELLER as (
				Select
					Sum(settle_sale_supply) - Sum(settle_sale_commission_in_vat) + Sum(settle_delivery_in_vat) - Sum(settle_delivery_commission_in_vat) as sum_settle_sale_supply 
					, Sum(order_cnt) as sum_order_cnt
					, seller_idx
				From DY_SETTLE 
				Where settle_is_del = N'N'
						And settle_date between N'$date_start' and N'$date_end'
		
		";

		if(count($qryWhereAry) > 0){
			$qry .= " And " . implode("AND ", $qryWhereAry);
		}

		$qry .= "
				Group by seller_idx
			)
		";

		$qry .= "
			Select 
				CTE.sum_settle_sale_supply 
				, CTE.sum_order_cnt 
				, CTE.seller_idx
				, S.seller_name
			From CTE_SUM_SELLER CTE
				Left Outer Join DY_SELLER S On S.seller_idx = CTE.seller_idx
		";


		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - 카테고리
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryCategory($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " seller_idx = N'$seller_idx'";
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " product_name = N'$search_keyword'";
			}
		}

		if($except_cancel_order == "Y"){
			$qryWhereAry[] = " settle_type <> N'CANCEL'";
		}

		$qry = "
			WITH CTE_SUM_SELLER as (
				Select
					Sum(settle_sale_supply) - Sum(settle_sale_commission_in_vat) + Sum(settle_delivery_in_vat) - Sum(settle_delivery_commission_in_vat) as sum_settle_sale_supply
					, Sum(settle_purchase_supply) + Sum(settle_purchase_delivery_in_vat) as sum_settle_purchase_supply
					, Sum(product_option_cnt) as sum_product_option_cnt
					, product_category_l_idx, product_category_m_idx
				From DY_SETTLE 
				Where settle_is_del = N'N'
						And settle_date between N'$date_start' and N'$date_end'
			
		
		";

		if(count($qryWhereAry) > 0){
			$qry .= " And " . implode("AND ", $qryWhereAry);
		}

		$qry .= "
				Group by product_category_l_idx, product_category_m_idx
			)
		";

		$qry .= "
			Select 
				CTE.sum_settle_sale_supply 
				, CTE.sum_settle_purchase_supply 
				, CTE.sum_product_option_cnt
				, CTE.product_category_l_idx
				, CTE.product_category_m_idx
				, C1.name as category_l_name
				, C2.name as category_m_name
			From CTE_SUM_SELLER CTE
			Left Outer Join DY_CATEGORY C1 On C1.parent_category_idx = 0 And C1.category_idx = CTE.product_category_l_idx
			Left Outer Join DY_CATEGORY C2 On C2.parent_category_idx = CTE.product_category_l_idx And C2.category_idx = CTE.product_category_m_idx
				
		";


		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - 주문
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryOrder($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " seller_idx = N'$seller_idx'";
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " 
					order_idx in (
						Select order_idx
						From DY_ORDER_PRODUCT_MATCHING OPM
						Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
						Where OPM.order_matching_is_del = N'N'
								And P.product_name = N'$search_keyword'
					)
				";
			}
		}

		/*
		if($except_cancel_order != "Y"){
			$qryWhereAry[] = "
				And order_idx not in (
							Select order_idx
							From DY_ORDER_PRODUCT_MATCHING OPM
							Where OPM.order_matching_is_del = N'N'
									OPM.order_cs_status <> N'ORDER_CANCEL'
						)
			";
		}
		*/

		$qry = "
			WITH CTE_SUM_ORDER as (
				Select
					order_idx, order_pack_idx, order_cnt
				From DY_ORDER
				Where order_is_del = N'N'
						And order_progress_step_accept_date >= N'$date_start 00:00:00' 
						And order_progress_step_accept_date <= N'$date_end 23:59:59.998'
		";

		if(count($qryWhereAry) > 0){
			$qry .= " And " . implode("AND ", $qryWhereAry);
		}

		$qry .= "
			)
		";

		$qry .= "
			Select 
				(Select isNull(Sum(order_cnt), 0) as sum_order_cnt From CTE_SUM_ORDER) as sum_order_cnt
				, (
				    Select isNull(Sum(order_cnt), 0) as sum_order_pack_cnt 
				    From CTE_SUM_ORDER
				    Where order_pack_idx in (
				      Select order_pack_idx From CTE_SUM_ORDER
				      Group by order_pack_idx
				      Having count(order_pack_idx) > 1
				    )
				  ) as sum_order_pack_cnt
				, (Select count(distinct order_idx) From CTE_SUM_ORDER) as order_distinct_cnt
		";


		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - 송장
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryInvoice($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " seller_idx = N'$seller_idx'";
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " 
					order_idx in (
						Select order_idx
						From DY_ORDER_PRODUCT_MATCHING OPM
						Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
						Where OPM.order_matching_is_del = N'N'
								And P.product_name = N'$search_keyword'
					)
				";
			}
		}

		/*
		if($except_cancel_order != "Y"){
			$qryWhereAry[] = "
				And order_idx not in (
							Select order_idx
							From DY_ORDER_PRODUCT_MATCHING OPM
							Where OPM.order_matching_is_del = N'N'
									OPM.order_cs_status <> N'ORDER_CANCEL'
						)
			";
		}
		*/

		$qry = "
			WITH CTE_SUM_ORDER as (
				Select
					order_idx, order_pack_idx, invoice_no, delivery_is_free
				From DY_ORDER
				Where order_is_del = N'N'
						And order_progress_step in (N'ORDER_INVOICE', N'ORDER_SHIPPED')
						And order_progress_step_accept_date >= N'$date_start 00:00:00' 
						And order_progress_step_accept_date <= N'$date_end 23:59:59.998'
						
		";

		if(count($qryWhereAry) > 0){
			$qry .= " And " . implode("AND ", $qryWhereAry);
		}

		$qry .= "
			)
		";

		$qry .= "
			Select 
				(Select count(distinct invoice_no) as sum_order_cnt From CTE_SUM_ORDER) as sum_invoice_cnt
				, (
				    Select count(distinct invoice_no) as sum_pack_invoice_cnt 
				    From CTE_SUM_ORDER
				    Where order_pack_idx in (
				      Select order_pack_idx From CTE_SUM_ORDER
				      Group by order_pack_idx
				      Having count(order_pack_idx) > 1
				    )
				  ) as sum_pack_invoice_cnt
				, (
				    Select count(distinct invoice_no) as sum_free_invoice_cnt 
				    From CTE_SUM_ORDER
				    Where delivery_is_free = N'Y'
				  ) as sum_free_invoice_cnt
		";


		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - 배송
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryShipped($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " seller_idx = N'$seller_idx'";
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " 
					order_idx in (
						Select order_idx
						From DY_ORDER_PRODUCT_MATCHING OPM
						Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
						Where OPM.order_matching_is_del = N'N'
								And P.product_name = N'$search_keyword'
					)
				";
			}
		}

		/*
		if($except_cancel_order != "Y"){
			$qryWhereAry[] = "
				And order_idx not in (
							Select order_idx
							From DY_ORDER_PRODUCT_MATCHING OPM
							Where OPM.order_matching_is_del = N'N'
									OPM.order_cs_status <> N'ORDER_CANCEL'
						)
			";
		}
		*/

		$qry = "
			WITH CTE_SUM_ORDER as (
				Select
					order_idx, order_pack_idx, order_progress_step_accept_date, invoice_no, invoice_date, shipping_date
					, (Select Sum(product_option_cnt) From DY_ORDER_PRODUCT_MATCHING M Where M.order_idx = O.order_idx) as sum_product_cnt 
				From DY_ORDER O
				Where order_is_del = N'N'
						And order_progress_step in (N'ORDER_INVOICE', N'ORDER_SHIPPED')
						And order_progress_step_accept_date >= N'$date_start 00:00:00' 
						And order_progress_step_accept_date <= N'$date_end 23:59:59.998'
						
		";

		if(count($qryWhereAry) > 0){
			$qry .= " And " . implode("AND ", $qryWhereAry);
		}

		$qry .= "
			)
		";

		$qry .= "
			Select 
				(
				  Select count(distinct invoice_no) as cnt_accept_invoice_shipped 
					From CTE_SUM_ORDER
				    Where convert(varchar(10), order_progress_step_accept_date, 120) = convert(varchar(10), invoice_date, 120)
				          And convert(varchar(10), invoice_date, 120) = convert(varchar(10), shipping_date, 120)
			    ) as cnt_accept_invoice_shipped
				, (
				  Select count(distinct invoice_no) as cnt_invoice_shipped 
					From CTE_SUM_ORDER
				    Where convert(varchar(10), invoice_date, 120) = convert(varchar(10), shipping_date, 120)
			    ) as cnt_invoice_shipped
				, (
				  Select isNull(sum(sum_product_cnt), 0) as sum_accept_shipped 
					From CTE_SUM_ORDER
				    Where convert(varchar(10), order_progress_step_accept_date, 120) = convert(varchar(10), shipping_date, 120)
			    ) as sum_accept_shipped
				, (
				  Select count(distinct order_idx) as sum_accept_shipped 
					From CTE_SUM_ORDER
				    Where convert(varchar(10), order_progress_step_accept_date, 120) = convert(varchar(10), shipping_date, 120)
			    ) as cnt_accept_shipped
		";


		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - 반품
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryReturn($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qry = "
				Select
					isNull(Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' Then 1 Else 0 End), 0) as cnt_cancel_after_shipped
					, isNull(Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' Then 1 Else 0 End), 0) as cnt_change_after_shipped
				From DY_ORDER_PRODUCT_MATCHING 
				Where order_matching_is_del = N'N'
						And ( 
								(
								cs_reason_code1 = N'CS_REASON_CANCEL'
								And product_cancel_shipped = N'Y'
								And product_cancel_date >= N'$date_start 00:00:00' 
								And product_cancel_date <= N'$date_end 23:59:59.998'
								)
								Or
								(
								cs_reason_code1 = N'CS_REASON_CHANGE'
								And product_change_shipped = N'Y'
								And product_change_date >= N'$date_start 00:00:00' 
								And product_change_date <= N'$date_end 23:59:59.998'
								)	
						)							
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 당일판매요약표 - CS이력
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $except_cancel_order
	 * @return array
	 */
	public function getTodaySummaryCS($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $except_cancel_order)
	{

		$qry = "
				WITH CTE_CS as (
					Select cs_task, cs_reason_code1, cs_reason_code2 From DY_ORDER_CS
					Where cs_is_del = N'N'
					And cs_regdate >= N'$date_start 00:00:00' 
					And cs_regdate <= N'$date_end 23:59:59.998'
				)
	
				Select
					isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'RETURN_REFUND' Then 1 Else 0 End), 0
					) as C_RETURN_REFUND
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'RETURN_POOR' Then 1 Else 0 End), 0
					) as C_RETURN_POOR
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'RETURN_DELIVERY_ERR' Then 1 Else 0 End), 0
					) as C_RETURN_DELIVERY_ERR
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'CANCEL_LOSS' Then 1 Else 0 End), 0
					) as C_CANCEL_LOSS
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'CANCEL_SOLDOUT' Then 1 Else 0 End), 0
					) as C_CANCEL_SOLDOUT
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CANCEL' And cs_reason_code2 = 'CANCEL_DELIVERY_DELAY' Then 1 Else 0 End), 0
					) as C_CANCEL_DELIVERY_DELAY
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' And cs_reason_code2 = 'EXCHANGE_NORMAL' Then 1 Else 0 End), 0
					) as X_EXCHANGE_NORMAL
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' And cs_reason_code2 = 'EXCHANGE_POOR' Then 1 Else 0 End), 0
					) as X_EXCHANGE_POOR
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' And cs_reason_code2 = 'EXCHANGE_DELIVERY_ERR' Then 1 Else 0 End), 0
					) as X_EXCHANGE_DELIVERY_ERR
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' And cs_reason_code2 = 'EXCHANGE_SOLDOUT' Then 1 Else 0 End), 0
					) as X_EXCHANGE_SOLDOUT
					, isNull(
						Sum(Case When cs_reason_code1 = 'CS_REASON_CHANGE' And cs_reason_code2 = 'EXCHANGE_PRODUCT_CHANGE' Then 1 Else 0 End), 0
					) as X_EXCHANGE_PRODUCT_CHANGE
					, isNull(Sum(Case When cs_task = 'NORMAL' Then 1 Else 0 End), 0) as NORMAL
				From CTE_CS						
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 판매처별통계 - 전체 합계
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @return array
	 */
	public function getSellerSaleStatisticsSum($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword)
	{
		$returnValue = array();
		$qryWhereAry = array();

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " 
					product_name like '%$search_keyword%'
				";
			}
		}

		$addWhereQuery = "";
		if(count($qryWhereAry) > 0){
			$addWhereQuery = " And " . implode("AND ", $qryWhereAry);
		}

		$qry_CTE = "
		
			WITH
			CTE_ORDER as (
				Select 
				    count(distinct S.order_idx) as order_count
				    , isNull(Sum(M.product_option_cnt), 0) as sum_product_option_cnt
					, isNull(Sum(Case When M.order_cs_status = 'ORDER_CANCEL' Then 1 Else 0 End), 0) as order_cancel_count
					, isNull(Sum(Case When M.order_cs_status = 'ORDER_CANCEL' Then M.product_option_cnt Else 0 End), 0) as sum_cancel_product_cnt
					, isNull(Sum(Case When M.order_cs_status = 'PRODUCT_CHANGE' Then M.product_option_cnt Else 0 End), 0) as sum_cancel_change_cnt
				From DY_ORDER S
				Inner Join DY_ORDER_PRODUCT_MATCHING M On S.order_idx = M.order_idx
				Where S.order_is_del = N'N' And M.order_matching_is_del = N'N'
						And S.order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
						And S.order_progress_step_accept_date >= N'$date_start 00:00:00'
						And S.order_progress_step_accept_date <= N'$date_end 23:59:59.998'
						$addWhereQuery
			), 
			CTE_SETTLE_ORDER as (
				Select
					count(distinct Case When settle_type = 'SHIPPED' Then order_idx End) as order_count
					, isNull(Sum(Case When settle_type = 'SHIPPED' Then product_option_cnt Else 0 End), 0) as sum_product_option_cnt
					, count(distinct Case When settle_type = 'CANCEL' Then order_idx End) as order_cancel_count
					, isNull(Sum(Case When settle_type = 'CANCEL' Then product_option_cnt Else 0 End), 0) * -1 as sum_cancel_product_cnt
					, isNull(Sum(Case When settle_type = 'EXCHANGE' Then product_option_cnt Else 0 End), 0) as sum_cancel_change_cnt
				From DY_SETTLE S
				Where settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL')
					And settle_date between N'$date_start' And N'$date_end'
				$addWhereQuery
			),
			
			CTE_SETTLE as (
				Select
					isNull(Sum(Case When settle_type = 'SHIPPED' Or settle_type = 'EXCHANGE' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as sum_settle_sale_supply
					, isNull(Sum(Case When settle_type = 'CANCEL' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) * -1 as sum_settle_sale_supply_cancel
				From DY_SETTLE S
				Where settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL', N'EXCHANGE')
					And settle_date between N'$date_start' And N'$date_end'
				$addWhereQuery
			)
		";

		$qry =  $qry_CTE . "
			Select
				A.* 
			From
			CTE_SETTLE_ORDER A
		";

		parent::db_connect();
		$_settle_order = parent::execSqlOneRow($qry);
		parent::db_close();

		$returnValue["order_count"]            = $_settle_order["order_count"];
		$returnValue["sum_product_option_cnt"] = $_settle_order["sum_product_option_cnt"];
		$returnValue["order_cancel_count"]     = $_settle_order["order_cancel_count"];
		$returnValue["sum_cancel_product_cnt"] = $_settle_order["sum_cancel_product_cnt"];
		$returnValue["sum_cancel_change_cnt"]  = $_settle_order["sum_cancel_change_cnt"];

		$qry =  $qry_CTE . "
			Select
				A.* 
			From
			CTE_SETTLE A
		";

		parent::db_connect();
		$_settle = parent::execSqlOneRow($qry);
		parent::db_close();

		$returnValue["sum_settle_sale_supply"] = $_settle["sum_settle_sale_supply"];
		$returnValue["sum_settle_sale_supply_cancel"] = $_settle["sum_settle_sale_supply_cancel"];

		return $returnValue;
	}

	/**
	 * 판매처별통계
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $search_column
	 * @param $search_keyword
	 * @param $order_by
	 * @return array
	 */
	public function getSellerSaleStatistics($period_type, $date_start, $date_end, $seller_group, $seller_idx, $search_column, $search_keyword, $order_by = "S.seller_name ASC")
	{
		parent::db_connect();

		$qryWhereAry = array();

		if($seller_idx){
			$qryWhereAry[] = " S.seller_idx = N'$seller_idx'";
		} else {
			if ($seller_group) {
				$qry = "SELECT seller_idx FROM DY_SELLER WHERE manage_group_idx = N'$seller_group'";

				$sellerList = parent::execSqlList($qry);

				if (count($sellerList)) {
					$sellerListForStr = array();

					foreach ($sellerList as $seller) {
						$sellerListForStr[] = "N'".$seller["seller_idx"]."'";
					}

					$strSellerList = implode(",", $sellerListForStr);

					$qryWhereAry[] = " S.seller_idx IN ($strSellerList)";
				}
			}
		}

		if($search_column && $search_keyword){
			if($search_column == "product_name") {
				$qryWhereAry[] = " 
					product_name like '%$search_keyword%'
				";
			}
		}

		$addWhereQuery = "";
		if(count($qryWhereAry) > 0){
			$addWhereQuery = " And " . implode("AND ", $qryWhereAry);
		}

		$qry = "
		
			WITH
			CTE_ORDER as (
				Select 
				    S.seller_idx
				    , count(distinct S.order_idx) as order_count
				    , isNull(Sum(M.product_option_cnt), 0) as sum_product_option_cnt
					, isNull(Sum(Case When M.order_cs_status = 'ORDER_CANCEL' Then 1 Else 0 End), 0) as order_cancel_count
					, isNull(Sum(Case When M.order_cs_status = 'ORDER_CANCEL' Then M.product_option_cnt Else 0 End), 0) as sum_cancel_product_cnt
					, isNull(Sum(Case When M.order_cs_status = 'PRODUCT_CHANGE' Then M.product_option_cnt Else 0 End), 0) as sum_cancel_change_cnt
				From DY_ORDER S
				Inner Join DY_ORDER_PRODUCT_MATCHING M On S.order_idx = M.order_idx
				Where S.order_is_del = N'N' And M.order_matching_is_del = N'N'
						And S.order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
						And S.order_progress_step_accept_date >= N'$date_start 00:00:00'
						And S.order_progress_step_accept_date <= N'$date_end 23:59:59.998'
						$addWhereQuery
				Group by S.seller_idx
			), 
			CTE_SETTLE_ORDER as (
				Select
					S.seller_idx
					, count(distinct Case When settle_type = 'SHIPPED' Then order_idx End) as order_count
					, isNull(Sum(Case When settle_type = 'SHIPPED' Then product_option_cnt Else 0 End), 0) as sum_product_option_cnt
					, count(distinct Case When settle_type = 'CANCEL' Then order_idx End) as order_cancel_count
					, isNull(Sum(Case When settle_type = 'CANCEL' Then product_option_cnt Else 0 End), 0) * -1 as sum_cancel_product_cnt
					, isNull(Sum(Case When settle_type = 'EXCHANGE' Then product_option_cnt Else 0 End), 0) as sum_cancel_change_cnt
				From DY_SETTLE S
				Where settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL')
					And settle_date between N'$date_start' And N'$date_end'
				$addWhereQuery
				Group by S.seller_idx
			),
			
			CTE_SETTLE as (
				Select
					S.seller_idx
					, isNull(Sum(Case When settle_type = 'SHIPPED' Or settle_type = 'EXCHANGE' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as sum_settle_sale_supply
					, isNull(Sum(Case When settle_type = 'CANCEL' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) * -1 as sum_settle_sale_supply_cancel 
				From DY_SETTLE S
				Where settle_is_del = N'N'
					And settle_type in (N'SHIPPED', N'CANCEL', N'EXCHANGE')
					And settle_date between N'$date_start' And N'$date_end'
				$addWhereQuery
				Group by S.seller_idx
			)
		";

		$qry .= "
			Select
				A.* 
				, T.sum_settle_sale_supply
				, T.sum_settle_sale_supply_cancel
				, S.seller_name
			From
			CTE_SETTLE_ORDER A
			Full Outer Join CTE_SETTLE T On T.seller_idx = A.seller_idx
			Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx Or T.seller_idx = S.seller_idx
			Order by $order_by
		";

		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 월별 판매처 통계 - 매출액
	 * @param $date_year
	 * @param $seller_idx
	 * @return array
	 */
	public function getSellerMonthlyStatistics($date_year, $seller_idx)
	{
		$qryWhere = "";
		$date_year_prev = (int) $date_year - 1;

		$date_start = $date_year_prev . "-01-01";
		$date_end = $date_year . "-12-31";

		$qryWhere = " And settle_date between N'$date_start' And N'$date_end' ";

		$qryWhere2 = "";

		if($seller_idx){
			$qryWhere .= " And seller_idx = N'$seller_idx'";
			$qryWhere2 .= " And seller_idx = N'$seller_idx'";
		}

		$pivot_string = "";
		$pivot_column = "";
		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year_prev."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year_prev."-".$i."], 0) as [".$date_year_prev."-".$i."]";
		}

		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year."-".$i."], 0) as [".$date_year."-".$i."]";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
					seller_idx as settle_seller_idx
					, CAST(YEAR(settle_date) AS VARCHAR(4)) + '-' + CAST(MONTH(settle_date) AS VARCHAR(2)) as s_date
					, isNull(Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat), 0) as sum_settle_sale_supply
				From DY_SETTLE
				Where settle_is_del = N'N' And settle_closing = N'Y'
				$qryWhere
				Group by seller_idx, CAST(YEAR(settle_date) AS VARCHAR(4)) + '-' + CAST(MONTH(settle_date) AS VARCHAR(2))
				
				Union All
				
				Select
					seller_idx as settle_seller_idx
					, CAST(YEAR(settle_date) AS VARCHAR(4)) + '-0' as s_date
					, isNull(Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat), 0) as sum_settle_sale_supply
				From DY_SETTLE
				Where settle_is_del = N'N' And settle_closing = N'Y'
				$qryWhere
				Group by seller_idx, CAST(YEAR(settle_date) AS VARCHAR(4))
			)
		";

		$qry .= "
				Select 
				      $pivot_column
				      , S.seller_name, S.seller_idx
				From CTE_SETTLE
				PIVOT
				(
					Sum([sum_settle_sale_supply]) 
					For s_date in ($pivot_string)
				) as V
				Right Outer Join DY_SELLER S ON V.settle_seller_idx = S.seller_idx
				Where S.seller_is_del = N'N' And S.seller_is_use = N'Y'
				$qryWhere2
				Order by S.seller_name ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 월별 판매처 통계 - 주문수량
	 * @param $date_year
	 * @return array
	 */
	public function getSellerMonthlyOrderCntStatistics($date_year, $seller_idx)
	{
		$qryWhere = "";
		$date_year_prev = (int) $date_year - 1;

		$date_start = $date_year_prev . "-01-01";
		$date_end = $date_year . "-12-31";

		$qryWhere = " And order_progress_step_accept_date between N'$date_start' And N'$date_end' ";

		$qryWhere2 = "";

		if($seller_idx){
			$qryWhere .= " And seller_idx = N'$seller_idx'";
			$qryWhere2 .= " And seller_idx = N'$seller_idx'";
		}

		$pivot_string = "";
		$pivot_column = "";
		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year_prev."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year_prev."-".$i."], 0) as [".$date_year_prev."-".$i."]";
		}

		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year."-".$i."], 0) as [".$date_year."-".$i."]";
		}

		$qry = "
			WITH CTE_ORDER as (
				Select
					seller_idx as order_seller_idx
					, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-' + CAST(MONTH(order_progress_step_accept_date) AS VARCHAR(2)) as s_date
					, count(distinct order_idx) as order_count
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
				$qryWhere
				Group by seller_idx, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-' + CAST(MONTH(order_progress_step_accept_date) AS VARCHAR(2))
				
				Union All
				
				Select
					seller_idx as order_seller_idx
					, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-0' as s_date
					, count(distinct order_idx) as order_count
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
				$qryWhere
				Group by seller_idx, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4))
			)
		";

		$qry .= "
				Select 
				      $pivot_column
				      , S.seller_name, S.seller_idx
				From CTE_ORDER
				PIVOT
				(
					Sum([order_count]) 
					For s_date in ($pivot_string)
				) as V
				Right Outer Join DY_SELLER S ON V.order_seller_idx = S.seller_idx
				Where S.seller_is_del = N'N' And S.seller_is_use = N'Y'
				$qryWhere2
				Order by S.seller_name ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 월별 판매처 통계 - 주문수량
	 * @param $date_year
	 * @return array
	 */
	public function getSellerMonthlyOrderCntStatistics2($date_year)
	{
		$qryWhere = "";
		$date_year_prev = (int) $date_year - 1;

		$date_start = $date_year_prev . "-01-01";
		$date_end = $date_year . "-12-31";

		$qryWhere = " And order_progress_step_accept_date between N'$date_start' And N'$date_end' ";

		$pivot_string = "";
		$pivot_column = "";
		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year_prev."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year_prev."-".$i."], 0) as [".$date_year_prev."-".$i."]";
		}

		for($i=0;$i<13;$i++){
			if($pivot_string != "") $pivot_string .= ", ";
			$pivot_string .= "[".$date_year."-".$i."]";

			if($pivot_column != "") $pivot_column .= ", ";
			$pivot_column .= "isNull(V.[".$date_year."-".$i."], 0) as [".$date_year."-".$i."]";
		}

		$qry = "
			WITH CTE_ORDER as (
				Select
					seller_idx as order_seller_idx
					, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-' + CAST(MONTH(order_progress_step_accept_date) AS VARCHAR(2)) as s_date
					, count(distinct order_idx) as order_count
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
				$qryWhere
				Group by seller_idx, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-' + CAST(MONTH(order_progress_step_accept_date) AS VARCHAR(2))
				
				Union All
				
				Select
					seller_idx as order_seller_idx
					, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4)) + '-0' as s_date
					, count(distinct order_idx) as order_count
				From DY_ORDER
				Where order_is_del = N'N' And order_progress_step in (N'ORDER_ACCEPT', N'ORDER_INVOICE', N'ORDER_SHIPPED')
				$qryWhere
				Group by seller_idx, CAST(YEAR(order_progress_step_accept_date) AS VARCHAR(4))
			)
		";

		$qry .= "
				Select 
				      $pivot_column
				      , S.seller_name, S.seller_idx
				From CTE_ORDER
				PIVOT
				(
					Sum([order_count]) 
					For s_date in ($pivot_string)
				) as V
				Right Outer Join DY_SELLER S ON V.order_seller_idx = S.seller_idx
				Where S.seller_is_del = N'N' And S.seller_is_use = N'Y'
				Order by S.seller_name ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 공급처별정산(재고) 목록
	 * @param $supplier_idx
	 * @param $date_start
	 * @param $date_end
	 * @return array
	 */
	public function getSupplierStockList($supplier_idx, $date_start, $date_end)
	{
		$qry = "
			WITH CTE_STOCK as (
				Select
					S.product_idx, S.product_option_idx
					, isNull(Sum(stock_unit_price * stock_amount), 0) as stock_in_sum
					, isNull(Sum(stock_amount), 0) as stock_in_amount
					, stock_unit_price
				From DY_STOCK S
				Left Outer Join DY_PRODUCT P On P.product_idx = S.product_idx
				Where S.stock_is_del = N'N' And stock_is_confirm = N'Y'
					And S.stock_kind in (N'STOCK_ORDER', N'BACK')
					And S.stock_status in (N'NORMAL', N'ABNORMAL', N'HOLD')
				    And P.supplier_idx = N'$supplier_idx'
					And S.stock_is_confirm_date >= '".$date_start." 00:00:00'
					And S.stock_is_confirm_date <= '".$date_end." 23:59:59'
				Group by S.product_idx, S.product_option_idx, S.stock_unit_price WITH ROLLUP
			)
		";

		$qry .= "
			Select
				 S.*
			     , P.product_name, P.product_supplier_name, P.product_supplier_option
			     , PO.product_option_name
			From CTE_STOCK S
			Left Outer Join DY_PRODUCT P On P.product_idx = S.product_idx
			Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = S.product_option_idx
			Where 
			      (S.product_idx is null And S.product_option_idx is null And S.stock_unit_price is null)
					OR
			      (S.product_idx is not null And S.product_option_idx is not null And S.stock_unit_price is not null)
			Order by isNull(S.product_option_idx, 99999) ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 거래현황 - 거래처 IDX & 타입 & 날짜 기준 데이터 반환
	 * @param $tran_type
	 * @param $target_idx
	 * @param $date
	 * @return array|false|null
	 */
	public function getTransactionStateData($tran_type, $target_idx, $date)
	{
		$qry = "
			Select * From DY_TRANSACTION_STATE Where tran_is_del = N'N' And tran_type = N'$tran_type' And target_idx = N'$target_idx' And tran_date = convert(date, '$date')
		";
		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 거래현황 - 거래처 IDX & 타입 & 날짜 기준 데이터 반환
	 * @param $tran_type
	 * @param $target_idx
	 * @param $date
	 * @return array|false|null
	 */
	public function getTransactionStateDataByTranIdx($tran_idx)
	{
		$qry = "
			Select * From DY_TRANSACTION_STATE Where tran_is_del = N'N' And tran_idx = N'$tran_idx'
		";
		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 거래현황 - 매출현황(외상매출금) 목록
	 * 정산 데이터는 마감 데이터가 아니더라도 불러온다
	 * @param $period_type
	 * @param $date
	 * @return array
	 */
	public function getTransactionStateSaleCredit($period_type, $date, $vendor_use_charge = "N")
	{
		$tran_type = "";
		$addQry = "";
		if($vendor_use_charge == "N"){
			$tran_type = "SALE_CREDIT_IN_AMOUNT";
			$sum_col = " , isNull(A.prev_tran_amount, 0) as prev_tran_amount, isNull(A.today_tran_amount, 0) as today_tran_amount ";
			$addQry = " And SELLER.vendor_use_charge = N'N' ";
		}else{
			$tran_type = "SALE_PREPAY_IN_AMOUNT";
			$sum_col = " , isNull(V.prev_tran_amount, 0) as prev_tran_amount, isNull(V.today_tran_amount, 0) as today_tran_amount ";
			$addQry = " And SELLER.vendor_use_charge = N'Y' ";
		}

		if($period_type == "day") {
			// And vendor_use_charge = N'$vendor_use_charge'
			$qry = "
				Select 
				     S.seller_idx as customer_idx
			         , S.tran_date
				     , S.prev_settle_amount
				     , S.today_settle_amount
				     $sum_col
				     , SELLER.seller_name as customer_name
					 , isNull(T2.tran_memo, '') as today_tran_memo
					From 
					(
					Select
					    seller_idx
					    , '$date' as tran_date
				        , isNull(Sum(Case WHEN settle_date < Convert(date, '$date') Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as prev_settle_amount
				        , isNull(Sum(Case WHEN settle_date = Convert(date, '$date') Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as today_settle_amount
					From DY_SETTLE
					Where settle_is_del = 'N'
					Group by seller_idx
					) S
					/*
					Left Outer Join (
					  Select 
					      target_idx
					      , isNull(Sum(Case WHEN tran_date < Convert(date, '$date') Then tran_amount Else 0 End), 0) as prev_tran_amount
					      , isNull(Sum(Case WHEN tran_date = Convert(date, '$date') Then tran_amount Else 0 End), 0) as today_tran_amount
					  From DY_TRANSACTION_STATE
					  Where tran_is_del = N'N' And tran_date <= Convert(date, '$date')
					        And tran_type = N'SALE_CREDIT_IN_AMOUNT'
					  Group by target_idx
					) T On S.seller_idx = T.target_idx
					*/
				    Full Outer Join (
				      Select 
				        target_idx
				        , isNull(Sum(Case WHEN ledger_date < Convert(date, '$date') Then ledger_tran_amount Else 0 End), 0) as prev_tran_amount
					    , isNull(Sum(Case WHEN ledger_date = Convert(date, '$date') Then ledger_tran_amount Else 0 End), 0) as today_tran_amount
				      From DY_LEDGER
				      Where ledger_is_del = N'N' And ledger_date <= Convert(date, '$date')
				          And ledger_type = N'LEDGER_SALE' And ledger_add_type = N'TRAN'
				      Group by target_idx
				    ) A On A.target_idx = S.seller_idx  
					  
				    Full Outer Join (
				      Select 
				        member_idx
				        , isNull(Sum(Case WHEN charge_date < Convert(date, '$date') Then (charge_inout * charge_amount) Else 0 End), 0) as prev_tran_amount
					      , isNull(Sum(Case WHEN charge_date = Convert(date, '$date') Then (charge_inout * charge_amount) Else 0 End), 0) as today_tran_amount
				      From DY_MEMBER_VENDOR_CHARGE 
				      Where charge_is_del = N'N' And settle_idx = 0 And charge_date <= Convert(date, '$date')
				      Group by member_idx
				    ) V On S.seller_idx = V.member_idx Or A.target_idx = V.member_idx
					  
				    Left Outer Join (
				      SELECT 
			            target_idx as target_idx2
				        , tran_memo
				        , tran_date
				      From DY_TRANSACTION_STATE
				      Where tran_is_del = N'N' And tran_date = Convert(date, '$date')
				            And tran_type = N'SALE_CREDIT_IN_AMOUNT'
				    ) T2 On S.seller_idx = T2.target_idx2 And S.tran_date = T2.tran_date
					  
					Left Outer Join DY_SELLER SELLER 
					  On SELLER.seller_idx = S.seller_idx 
					       Or SELLER.seller_idx = A.target_idx
					       Or SELLER.seller_idx = V.member_idx
				Where 1 = 1 $addQry
				Order by SELLER.seller_name ASC
			";
		}elseif($period_type == "week" || $period_type == "month"){

			if($period_type == "week") {
				$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
			}elseif($period_type == "month"){
				$prev_date = date('Y', strtotime($date)) . "-" . date('m', strtotime($date)) . "-01";
			}

			$qry = "
				Select 
				     S.seller_idx as customer_idx
			         , S.tran_date
				     , S.prev_settle_amount
				     , S.today_settle_amount
				     $sum_col
				     , SELLER.seller_name as customer_name
					 , isNull(T2.tran_memo, '') as today_tran_memo
					From 
					(
					Select
					    seller_idx
					    , '$date' as tran_date
				        , isNull(Sum(Case WHEN settle_date < Convert(date, '$prev_date') Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as prev_settle_amount
				        , isNull(Sum(Case WHEN settle_date >= Convert(date, '$prev_date') And settle_date <= Convert(date, '$date') Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as today_settle_amount
					From DY_SETTLE
					Where settle_is_del = 'N' And vendor_use_charge = N'$vendor_use_charge'
					Group by seller_idx
					) S
				    
				    /*
					Left Outer Join (
					  Select 
					      target_idx
					      , isNull(Sum(Case WHEN tran_date < Convert(date, '$prev_date') Then tran_amount Else 0 End), 0) as prev_tran_amount
					      , isNull(Sum(Case WHEN tran_date >= Convert(date, '$prev_date') And tran_date <= Convert(date, '$date') Then tran_amount Else 0 End), 0) as today_tran_amount
					  From DY_TRANSACTION_STATE
					  Where tran_is_del = N'N' And tran_date <= Convert(date, '$date')
					        And tran_type = N'SALE_CREDIT_IN_AMOUNT'
					  Group by target_idx
					) T On S.seller_idx = T.target_idx
					*/
				    Full Outer Join (
				      Select 
				        target_idx
				        , isNull(Sum(Case WHEN ledger_date < Convert(date, '$prev_date') Then ledger_tran_amount Else 0 End), 0) as prev_tran_amount
					    , isNull(Sum(Case WHEN ledger_date >= Convert(date, '$prev_date') And ledger_date <= Convert(date, '$date') Then ledger_tran_amount Else 0 End), 0) as today_tran_amount
				      From DY_LEDGER
				      Where ledger_is_del = N'N' And ledger_date <= Convert(date, '$date')
				          And ledger_type = N'LEDGER_SALE' And ledger_add_type = N'TRAN'
				      Group by target_idx
				    ) A On A.target_idx = S.seller_idx  
					  
				    Full Outer Join (
				      Select 
				        member_idx
				        , isNull(Sum(Case WHEN charge_date < Convert(date, '$prev_date') Then (charge_inout * charge_amount) Else 0 End), 0) as prev_tran_amount
					      , isNull(Sum(Case WHEN charge_date >= Convert(date, '$prev_date') And charge_date <= Convert(date, '$date')  Then (charge_inout * charge_amount) Else 0 End), 0) as today_tran_amount
				      From DY_MEMBER_VENDOR_CHARGE 
				      Where charge_is_del = N'N' And settle_idx = 0 And charge_date <= Convert(date, '$date')
				      Group by member_idx
				    ) V On S.seller_idx = V.member_idx Or A.target_idx = V.member_idx
					  
				    Left Outer Join (
				      SELECT 
			            target_idx as target_idx2
				        , tran_memo
				        , tran_date
				      From DY_TRANSACTION_STATE
				      Where tran_is_del = N'N' And tran_date = Convert(date, '$date')
				            And tran_type = N'SALE_CREDIT_IN_AMOUNT'
				    ) T2 On S.seller_idx = T2.target_idx2 And S.tran_date = T2.tran_date
					  
					Left Outer Join DY_SELLER SELLER 
					  On SELLER.seller_idx = S.seller_idx 
					       Or SELLER.seller_idx = A.target_idx
					       Or SELLER.seller_idx = V.member_idx
				Where 1 = 1 $addQry
				Order by SELLER.seller_name ASC
			";
		}

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 거래현황 - 매입현황(외상매입금) 목록
	 * @param $period_type
	 * @param $date
	 * @return array
	 */
	public function getTransactionStatePurchaseCredit($period_type, $date, $supplier_use_prepay = "N")
	{
		$tran_type = "";
		if($supplier_use_prepay == "N"){
			$tran_type = "PURCHASE_CREDIT_IN_AMOUNT";
			$addQry = " And SUPPLIER.supplier_use_prepay = N'N' ";
		}else{
			$tran_type = "PURCHASE_PREPAY_IN_AMOUNT";
			$addQry = " And SUPPLIER.supplier_use_prepay = N'Y' ";
		}

		if($period_type == "day") {
			// And supplier_use_prepay = N'$supplier_use_prepay'
			$qry = "
				Select 
				     CASE
						WHEN S.supplier_idx IS NOT NULL THEN S.supplier_idx
						WHEN ST.supplier_idx IS NOT NULL THEN ST.supplier_idx
						ELSE A.target_idx
					END AS customer_idx
					, CASE
						WHEN S.tran_date IS NOT NULL THEN S.tran_date
						WHEN ST.tran_date IS NOT NULL THEN ST.tran_date
						ELSE A.tran_date
					END AS tran_date
					, ISNULL(S.prev_settle_amount, 0) + ISNULL(ST.prev_stock_amount, 0) + ISNULL(A.prev_adjust_amount, 0) + ISNULL(A.prev_refund_amount, 0) AS prev_settle_amount
					, ISNULL(S.today_settle_amount, 0) + ISNULL(ST.today_stock_amount, 0) + ISNULL(A.today_adjust_amount, 0) + ISNULL(A.today_refund_amount, 0) AS today_settle_amount
					, isNull(A.prev_tran_amount, 0) as prev_tran_amount
					, isNull(A.today_tran_amount, 0) as today_tran_amount
					, SUPPLIER.supplier_name as customer_name
					, SUPPLIER.supplier_use_prepay
					, isNull(T2.tran_memo, '') as today_tran_memo
					From 
					(
					Select
					    supplier_idx
					    , '$date' as tran_date
				        , isNull(Sum(Case WHEN settle_date < Convert(date, '$date') Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as prev_settle_amount
				        , isNull(Sum(Case WHEN settle_date = Convert(date, '$date') Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as today_settle_amount
					From DY_SETTLE
					Where settle_is_del = 'N' And product_sale_type = N'CONSIGNMENT'
					Group by supplier_idx
					) S
					FULL OUTER JOIN (
						Select
							P.supplier_idx
							, '$date' as tran_date
							, isNull(Sum(Case WHEN stock_is_confirm_date < Convert(date, '$date') Then CONVERT(BIGINT, ST1.stock_unit_price) * CONVERT(BIGINT, ST1.stock_amount) Else 0 End), 0) as prev_stock_amount
							, isNull(Sum(Case WHEN stock_is_confirm_date = Convert(date, '$date') Then CONVERT(BIGINT, ST1.stock_unit_price) * CONVERT(BIGINT, ST1.stock_amount) Else 0 End), 0) as today_stock_amount
						From DY_STOCK AS ST1
							JOIN DY_PRODUCT AS P ON P.product_idx = ST1.product_idx
						WHERE ST1.stock_kind = N'STOCK_ORDER' AND ST1.stock_is_confirm = N'Y' AND ST1.stock_is_cancel = N'N' AND ST1.stock_is_del = N'N' AND ST1.stock_status != N'SHORTAGE'
						Group by P.supplier_idx
					) ST ON S.supplier_idx = ST.supplier_idx
				    Full Outer Join (
						Select
							target_idx
							, '$date' as tran_date
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$date') Then ledger_tran_amount Else 0 End), 0) as prev_tran_amount
							, isNull(Sum(Case WHEN ledger_date = Convert(date, '$date') Then ledger_tran_amount Else 0 End), 0) as today_tran_amount
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$date') Then ledger_adjust_amount Else 0 End), 0) as prev_adjust_amount
							, isNull(Sum(Case WHEN ledger_date = Convert(date, '$date') Then ledger_adjust_amount Else 0 End), 0) as today_adjust_amount
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$date') Then ledger_refund_amount Else 0 End), 0) as prev_refund_amount
							, isNull(Sum(Case WHEN ledger_date = Convert(date, '$date') Then ledger_refund_amount Else 0 End), 0) as today_refund_amount
						From DY_LEDGER
						Where ledger_is_del = N'N' And ledger_date <= Convert(date, '$date')
							And ledger_type = N'LEDGER_PURCHASE'
						Group by target_idx
					) A On A.target_idx = S.supplier_idx OR A.target_idx = ST.supplier_idx
				    Left Outer Join (
						SELECT 
							target_idx as target_idx2
							, tran_memo
							, tran_date
						From DY_TRANSACTION_STATE
						Where tran_is_del = N'N' And tran_date = Convert(date, '$date') And tran_type = N'$tran_type'
					) T2 On (T2.target_idx2 = S.supplier_idx AND T2.tran_date = S.tran_date)
							OR (T2.target_idx2 = ST.supplier_idx AND T2.tran_date = ST.tran_date)
							OR (T2.target_idx2 = A.target_idx AND T2.tran_date = A.tran_date)
					Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER 
						On SUPPLIER.member_idx = S.supplier_idx
							OR SUPPLIER.member_idx = ST.supplier_idx
							Or SUPPLIER.member_idx = A.target_idx
				Where 1 = 1 $addQry
				Order by SUPPLIER.supplier_name ASC
			";
		}elseif($period_type == "week" || $period_type == "month"){

			if($period_type == "week") {
				$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
			}elseif($period_type == "month"){
				$prev_date = date('Y', strtotime($date)) . "-" . date('m', strtotime($date)) . "-01";
			}

			$qry = "
				Select 
				     CASE
						WHEN S.supplier_idx IS NOT NULL THEN S.supplier_idx
						WHEN ST.supplier_idx IS NOT NULL THEN ST.supplier_idx
						ELSE A.target_idx
					END AS customer_idx
					, CASE
						WHEN S.tran_date IS NOT NULL THEN S.tran_date
						WHEN ST.tran_date IS NOT NULL THEN ST.tran_date
						ELSE A.tran_date
					END AS tran_date
					, ISNULL(S.prev_settle_amount, 0) + ISNULL(ST.prev_stock_amount, 0) + ISNULL(A.prev_adjust_amount, 0) + ISNULL(A.prev_refund_amount, 0) AS prev_settle_amount
					, ISNULL(S.today_settle_amount, 0) + ISNULL(ST.today_stock_amount, 0) + ISNULL(A.today_adjust_amount, 0) + ISNULL(A.today_refund_amount, 0) AS today_settle_amount
					, isNull(A.prev_tran_amount, 0) as prev_tran_amount
					, isNull(A.today_tran_amount, 0) as today_tran_amount
					, SUPPLIER.supplier_name as customer_name
					, isNull(T2.tran_memo, '') as today_tran_memo
					From 
					(
					Select
					    supplier_idx
					    , '$date' as tran_date
				        , isNull(Sum(Case WHEN settle_date < Convert(date, '$prev_date') Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as prev_settle_amount
				        , isNull(Sum(Case WHEN settle_date >= Convert(date, '$prev_date') And settle_date <= Convert(date, '$date') Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as today_settle_amount
					From DY_SETTLE
					Where settle_is_del = 'N' And product_sale_type = N'CONSIGNMENT'
					Group by supplier_idx
					) S
				    FULL OUTER JOIN (
						Select
							P.supplier_idx
							, '$date' as tran_date
							, isNull(Sum(Case WHEN stock_is_confirm_date < Convert(date, '$prev_date') Then CONVERT(BIGINT, ST1.stock_unit_price) * CONVERT(BIGINT,ST1.stock_amount) Else 0 End), 0) as prev_stock_amount
							, isNull(Sum(Case WHEN stock_is_confirm_date >= Convert(date, '$prev_date') And stock_is_confirm_date <= Convert(date, '$date') Then CONVERT(BIGINT, ST1.stock_unit_price) * CONVERT(BIGINT, ST1.stock_amount) Else 0 End), 0) as today_stock_amount
						From DY_STOCK AS ST1
							JOIN DY_PRODUCT AS P ON P.product_idx = ST1.product_idx
						WHERE ST1.stock_kind = N'STOCK_ORDER' AND ST1.stock_is_confirm = N'Y' AND ST1.stock_is_cancel = N'N' AND ST1.stock_is_del = N'N' AND ST1.stock_status != N'SHORTAGE'
						Group by P.supplier_idx
					) ST ON S.supplier_idx = ST.supplier_idx
					Full Outer Join (
						Select 
							target_idx
							, '$date' as tran_date
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$prev_date') Then ledger_tran_amount Else 0 End), 0) as prev_tran_amount
							, isNull(Sum(Case WHEN ledger_date >= Convert(date, '$prev_date') And ledger_date <= Convert(date, '$date')  Then ledger_tran_amount Else 0 End), 0) as today_tran_amount
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$prev_date') Then ledger_adjust_amount Else 0 End), 0) as prev_adjust_amount
							, isNull(Sum(Case WHEN ledger_date >= Convert(date, '$prev_date') And ledger_date <= Convert(date, '$date')  Then ledger_adjust_amount Else 0 End), 0) as today_adjust_amount
							, isNull(Sum(Case WHEN ledger_date < Convert(date, '$prev_date') Then ledger_refund_amount Else 0 End), 0) as prev_refund_amount
							, isNull(Sum(Case WHEN ledger_date >= Convert(date, '$prev_date') And ledger_date <= Convert(date, '$date')  Then ledger_refund_amount Else 0 End), 0) as today_refund_amount
						From DY_LEDGER
						Where ledger_is_del = N'N' And ledger_date <= Convert(date, '$date')
							And ledger_type = N'LEDGER_PURCHASE'
						Group by target_idx
					) A On A.target_idx = S.supplier_idx  
				    Left Outer Join (
				      SELECT 
			            target_idx as target_idx2
				        , tran_memo
				        , tran_date
				      From DY_TRANSACTION_STATE
				      Where tran_is_del = N'N' And tran_date = Convert(date, '$date')
				            And tran_type = N'$tran_type'
				    ) T2 On (T2.target_idx2 = S.supplier_idx AND T2.tran_date = S.tran_date)
							OR (T2.target_idx2 = ST.supplier_idx AND T2.tran_date = ST.tran_date)
							OR (T2.target_idx2 = A.target_idx AND T2.tran_date = A.tran_date)
					Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER
						On SUPPLIER.member_idx = S.supplier_idx
							OR SUPPLIER.member_idx = ST.supplier_idx
							Or SUPPLIER.member_idx = A.target_idx
				Where 1 = 1 $addQry
				Order by SUPPLIER.supplier_name ASC
			";
		}

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 거래현황 - 데이터 수정
	 * @param $tran_type
	 * @param $date
	 * @param $target_idx
	 * @param $tran_amount
	 * @return bool|int|resource
	 */
	public function saveTransactionModify($tran_type, $date, $target_idx, $tran_amount)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Select tran_idx From DY_TRANSACTION_STATE Where tran_is_del = N'N' And tran_type = N'$tran_type' And target_idx = N'$target_idx' And tran_date = convert(date, '$date')
		";
		parent::db_connect();
		$tran_idx = parent::execSqlOneCol($qry);
		parent::db_close();

		if(!$tran_idx){
			//Insert
			$qry = "
				Insert Into DY_TRANSACTION_STATE
				(tran_date, tran_type, target_idx, tran_amount, tran_memo, tran_regip, tran_regidx)
				VALUES
				(
				 N'$date'
				 , N'$tran_type'
				 , N'$target_idx'
				 , N'$tran_amount'
				 , N''
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";
			parent::db_connect();
			$rst = parent::execSqlInsert($qry);
			parent::db_close();
		}else{
			$qry = "
				Update DY_TRANSACTION_STATE
				Set 
					tran_amount = N'$tran_amount'
					, tran_moddate = getdate()
					, tran_modip = N'$modip'
					, tran_modidx = N'$last_member_idx'
				Where tran_idx = N'$tran_idx'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return $rst;
	}

	/**
	 * 거래현황 - 메모 수정
	 * @param $tran_type
	 * @param $date
	 * @param $target_idx
	 * @param $tran_memo
	 * @return bool|int|resource
	 */
	public function saveTransactionMemoModify($tran_type, $date, $target_idx, $tran_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Select tran_idx From DY_TRANSACTION_STATE Where tran_is_del = N'N' And tran_type = N'$tran_type' And target_idx = N'$target_idx' And tran_date = convert(date, '$date')
		";
		parent::db_connect();
		$tran_idx = parent::execSqlOneCol($qry);
		parent::db_close();

		if(!$tran_idx){
			//Insert
			$qry = "
				Insert Into DY_TRANSACTION_STATE
				(tran_date, tran_type, target_idx, tran_amount, tran_memo, tran_regip, tran_regidx)
				VALUES
				(
				 N'$date'
				 , N'$tran_type'
				 , N'$target_idx'
				 , 0
				 , N'$tran_memo'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";
			parent::db_connect();
			$rst = parent::execSqlInsert($qry);
			parent::db_close();
		}else{
			$qry = "
				Update DY_TRANSACTION_STATE
				Set 
					tran_memo = N'$tran_memo'
					, tran_moddate = getdate()
					, tran_modip = N'$modip'
					, tran_modidx = N'$last_member_idx'
				Where tran_idx = N'$tran_idx'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return $rst;
	}

	/**
	 * 거래현황 - 기타 등록
	 * @param $tran_type
	 * @param $date
	 * @param $target_name
	 * @param $prev_amount
	 * @param $today_amount
	 * @param $tran_amount
	 * @param $remain_amount
	 * @param $tran_memo
	 * @return int
	 */
	public function insertTransactionEtc($tran_type, $date, $target_name, $prev_amount, $today_amount, $tran_amount, $remain_amount, $tran_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_TRANSACTION_STATE
			(
			 tran_date, tran_type, target_idx, target_name, tran_amount, tran_memo
			 , tran_prev_amount, tran_today_amount, tran_remain_amount
			 , tran_regip, tran_regidx
			 )
			VALUES
			(
			 N'$date'
			 , N'$tran_type'
			 , 0
			 , N'$target_name'
			 , N'$tran_amount'
			 , N'$tran_memo'
			 , N'$prev_amount'
			 , N'$today_amount'
			 , N'$remain_amount'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;
	}

	/**
	 * 거래현황 - 기타 수정
	 * @param $tran_type
	 * @param $date
	 * @param $tran_idx
	 * @param $prev_amount
	 * @param $today_amount
	 * @param $tran_amount
	 * @param $remain_amount
	 * @param $tran_memo
	 * @return bool|resource
	 */
	public function saveTransactionEtcModify($tran_idx, $prev_amount, $today_amount, $tran_amount, $remain_amount, $tran_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];


		$qry = "
				Update DY_TRANSACTION_STATE
				Set 
					tran_amount = N'$tran_amount'
					, tran_prev_amount = N'$prev_amount'
					, tran_today_amount = N'$today_amount'
					, tran_remain_amount = N'$remain_amount'
					, tran_memo = N'$tran_memo'
					, tran_moddate = getdate()
					, tran_modip = N'$modip'
					, tran_modidx = N'$last_member_idx'
				Where tran_idx = N'$tran_idx'
			";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 거래현황 - 기타 목록 반환
	 * @param $tran_type
	 * @param $date
	 * @return array
	 */
	public function getTransactionEtc($tran_type, $date)
	{
		$qry = "
			Select
		     tran_idx
			, target_name as customer_name
			, tran_prev_amount as prev_settle_amount
			, tran_today_amount as today_settle_amount
			, tran_amount as today_tran_amount
			, tran_memo as today_tran_memo
			, tran_remain_amount
			From DY_TRANSACTION_STATE
			Where tran_is_del = N'N' And tran_type = N'$tran_type' And tran_date = Convert(date, '$date')
			Order by tran_regdate ASC
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 매입거래처별원장 전월이월 불러오기
	 * 정산 데이터는 마감 데이터가 아니더라도 불러온다
	 * @param $supplier_idx
	 * @param $year_month
	 * @return array|false|null
	 */
	public function getPurchaseLedgerPrevSum($supplier_idx, $year_month)
	{
		$date_start = strtotime($year_month."-01");

		$settleSearch = "";
		$settleSearch .= " And settle_date < N'".date('Y-m-d', $date_start)."' ";
		$ledgeSearch = " And ledger_date < N'".date('Y-m-d', $date_start)."' ";
		$stockSearchDate = " WHERE stock_confirm_date < N'".date('Y-m-d', $date_start)."' ";
		$stockSearchSupp = "";

		if($supplier_idx){
			$settleSearch .= " And supplier_idx = N'$supplier_idx'";
			$ledgeSearch .= " And target_idx = N'$supplier_idx'";
			$stockSearchSupp = " AND P.supplier_idx = N'$supplier_idx' ";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
					N'$year_month' as dt
					, isNull(Sum(settle_purchase_supply + settle_purchase_delivery_in_vat), 0) as sum_settle_amount
				From DY_SETTLE
				Where settle_is_del = N'N' And product_sale_type = N'CONSIGNMENT'
					$settleSearch
			)
			, CTE_LEDGE as (
				Select
					N'$year_month' as dt
					, isNull(Sum(ledger_adjust_amount), 0) as sum_ledger_adjust_amount
					, isNull(Sum(ledger_tran_amount), 0) as sum_ledger_tran_amount
					, isNull(Sum(ledger_refund_amount), 0) as sum_ledger_refund_amount
				From DY_LEDGER
				Where ledger_is_del = N'N'
				$ledgeSearch
			)
			, CTE_STOCK AS (
				SELECT N'$year_month' as dt, SUM(A.stock_total_price) AS sum_stock_amount
				FROM (
					SELECT CONVERT(CHAR(10), S.stock_is_confirm_date, 23) AS stock_confirm_date, S.stock_unit_price * S.stock_amount AS stock_total_price
					FROM DY_STOCK AS S
						LEFT OUTER JOIN DY_PRODUCT AS P ON P.product_idx = S.product_idx
					WHERE S.stock_kind = N'STOCK_ORDER' 
						AND S.stock_is_confirm = N'Y' 
						AND S.stock_is_cancel = N'N' 
						AND S.stock_is_del = N'N'
						AND S.stock_status != N'SHORTAGE'
						$stockSearchSupp
				) AS A
				$stockSearchDate
			)
		";

		$qry .= "
			Select
			isNull(S.sum_settle_amount, 0) as sum_settle_amount
			, isNull(L.sum_ledger_adjust_amount, 0) as sum_ledger_adjust_amount
			, isNull(L.sum_ledger_tran_amount, 0) as sum_ledger_tran_amount
			, isNull(L.sum_ledger_refund_amount, 0) as sum_ledger_refund_amount
			, ISNULL(ST.sum_stock_amount, 0) AS sum_stock_amount
			From 
			CTE_SETTLE S 
			Inner Join CTE_LEDGE L On S.dt = L.dt
			INNER JOIN CTE_STOCK ST ON S.dt = ST.dt
		";

		parent::db_connect();
		$_list = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 매입거래처별원장 일별로 불러오기
	 * 정산 데이터는 마감 데이터가 아니더라도 불러온다
	 * @param $supplier_idx
	 * @param $year_month
	 * @return array
	 */
	public function getPurchaseLedgerList($supplier_idx, $year_month)
	{
		$date_start = strtotime($year_month."-01");
		$date_end = strtotime($year_month . "-" . date('t', $date_start));

		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $date_start);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $date_end);

		$settleSearch = "";
		$settleSearch .= " And settle_date between N'".date('Y-m-d', $date_start)."' And N'".date('Y-m-d', $date_end)."' ";
		$ledgeSearch = " And ledger_date between N'".date('Y-m-d', $date_start)."' And N'".date('Y-m-d', $date_end)."' ";
		$stockSearchDate = " WHERE A.stock_confirm_date between N'".date('Y-m-d', $date_start)."' And N'".date('Y-m-d', $date_end)."' ";
		$stockSearchSupp = "";

		if($supplier_idx){
			$settleSearch .= " And supplier_idx = N'$supplier_idx'";
			$ledgeSearch .= " And target_idx = N'$supplier_idx'";
			$stockSearchSupp = " AND P.supplier_idx = N'$supplier_idx' ";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
					settle_date
					, isNull(Sum(Case When settle_type = 'SHIPPED' Or settle_type = 'CANCEL' Or settle_type = 'EXCHANGE' Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as closing_settle_amount
					, isNull(Sum(Case When settle_type = 'ADJUST_SALE' Or settle_type = 'ADJUST_PURCHASE' Then settle_purchase_supply + settle_purchase_delivery_in_vat Else 0 End), 0) as adjust_settle_amount
				From DY_SETTLE
				Where settle_is_del = N'N' And product_sale_type = N'CONSIGNMENT'
					$settleSearch
				Group by settle_date
			)
			, CTE_LEDGE as (
				Select
					ledger_date
					, isNull(Sum(ledger_adjust_amount), 0) as sum_ledger_adjust_amount
					, isNull(Sum(ledger_tran_amount), 0) as sum_ledger_tran_amount
					, isNull(Sum(ledger_refund_amount), 0) as sum_ledger_refund_amount
				From DY_LEDGER
				Where ledger_is_del = N'N' And ledger_type = N'LEDGER_PURCHASE' And target_idx = N'$supplier_idx'
				$ledgeSearch
				Group by ledger_date
			)
			, CTE_LEDGER_MEMO as (
				Select
					ledger_date
					, ledger_memo_idx
					, ledger_memo
				From DY_LEDGER_MEMO
				Where ledger_memo_is_del = N'N' And ledger_type = N'LEDGER_PURCHASE' And target_idx = N'$supplier_idx'
				$ledgeSearch
			)
			, CTE_STOCK AS (
				SELECT A.stock_confirm_date, SUM(A.stock_total_price) AS stock_total_price
				FROM (
					SELECT CONVERT(CHAR(10), S.stock_is_confirm_date, 23) AS stock_confirm_date, S.stock_unit_price * S.stock_amount AS stock_total_price
					FROM DY_STOCK AS S
						LEFT OUTER JOIN DY_PRODUCT AS P ON P.product_idx = S.product_idx
					WHERE S.stock_kind = N'STOCK_ORDER' 
						AND S.stock_is_confirm = N'Y' 
						AND S.stock_is_cancel = N'N' 
						AND S.stock_is_del = N'N'
						$stockSearchSupp
				) AS A
				$stockSearchDate
				GROUP BY A.stock_confirm_date
			)
		";

		$qry .= "
			SELECT
				CASE 
					WHEN S.settle_date IS NOT NULL THEN S.settle_date
					WHEN L.ledger_date IS NOT NULL THEN L.ledger_date
					ELSE ST.stock_confirm_date
				END AS dt
				, (ISNULL(S.closing_settle_amount, 0) + ISNULL(ST.stock_total_price, 0)) AS closing_settle_amount
				, ISNULL(S.adjust_settle_amount, 0) AS adjust_settle_amount
				, ISNULL(L.sum_ledger_adjust_amount, 0) AS sum_ledger_adjust_amount
				, ISNULL(L.sum_ledger_tran_amount, 0) AS sum_ledger_tran_amount
				, ISNULL(L.sum_ledger_refund_amount, 0) AS sum_ledger_refund_amount
				, LM.ledger_memo_idx
				, LM.ledger_memo
				, ( ISNULL(S.adjust_settle_amount, 0) + ISNULL(L.sum_ledger_adjust_amount, 0) ) AS sum_adjust_amount
				, ( ISNULL(S.closing_settle_amount, 0) + ISNULL(ST.stock_total_price, 0) + ISNULL(S.adjust_settle_amount, 0) + ISNULL(L.sum_ledger_adjust_amount, 0) ) AS sum_settle_amount
				, (
					ISNULL(S.closing_settle_amount, 0) + ISNULL(ST.stock_total_price, 0) + ISNULL(S.adjust_settle_amount, 0) + ISNULL(L.sum_ledger_adjust_amount, 0)
					- ISNULL(L.sum_ledger_tran_amount, 0) + ISNULL(L.sum_ledger_refund_amount, 0)
				) AS sum_remain_amount
			FROM CTE_SETTLE S
			FULL OUTER JOIN CTE_LEDGE L ON S.settle_date = L.ledger_date
			FULL OUTER JOIN CTE_STOCK ST ON L.ledger_date = ST.stock_confirm_date
			LEFT OUTER JOIN CTE_LEDGER_MEMO LM ON S.settle_date = LM.ledger_date OR L.ledger_date = LM.ledger_date OR ST.stock_confirm_date = LM.ledger_date
					
			ORDER BY dt
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 매입거래처별원장 전월이월 불러오기
	 * 정산 데이터는 마감 데이터가 아니더라도 불러온다
	 * @param $seller_idx
	 * @param $year_month
	 * @return array|false|null
	 */
	public function getSaleLedgerPrevSum($seller_idx, $year_month)
	{
		$date_start = strtotime($year_month."-01");

		$settleSearch = "";
		$settleSearch .= " And settle_date < N'".date('Y-m-d', $date_start)."' ";
		$ledgeSearch = " And ledger_date < N'".date('Y-m-d', $date_start)."' ";

		if($seller_idx){
			$settleSearch .= " And seller_idx = N'$seller_idx'";
			$ledgeSearch .= " And target_idx = N'$seller_idx'";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
					N'$year_month' as dt
					, isNull(Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat), 0) as sum_settle_amount
				From DY_SETTLE
				Where settle_is_del = N'N'
					$settleSearch
			)
			, CTE_LEDGE as (
				Select
					N'$year_month' as dt
					, isNull(Sum(ledger_adjust_amount), 0) as sum_ledger_adjust_amount
					, isNull(Sum(ledger_tran_amount), 0) as sum_ledger_tran_amount
					, isNull(Sum(ledger_refund_amount), 0) as sum_ledger_refund_amount
				From DY_LEDGER
				Where ledger_is_del = N'N' And ledger_type = N'LEDGER_SALE'
				$ledgeSearch
			)
		";

		$qry .= "
			Select
			isNull(S.sum_settle_amount, 0) as sum_settle_amount
			, isNull(L.sum_ledger_adjust_amount, 0) as sum_ledger_adjust_amount
			, isNull(L.sum_ledger_tran_amount, 0) as sum_ledger_tran_amount
			, isNull(L.sum_ledger_refund_amount, 0) as sum_ledger_refund_amount
			From 
			CTE_SETTLE S 
			Inner Join CTE_LEDGE L On S.dt = L.dt
		
		";

		parent::db_connect();
		$_list = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 매입거래처별원장 일별로 불러오기
	 * 정산 데이터는 마감 데이터가 아니더라도 불러온다
	 * @param $seller_idx
	 * @param $year_month
	 * @return array
	 */
	public function getSaleLedgerList($seller_idx, $year_month)
	{
		global $GL_Member;

		$date_start = strtotime($year_month."-01");
		$date_end = strtotime($year_month . "-" . date('t', $date_start));

		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $date_start);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $date_end);

		$settleSearch = "";
		$settleSearch .= " And settle_date between N'".date('Y-m-d', $date_start)."' And N'".date('Y-m-d', $date_end)."' ";
		$ledgeSearch = " And ledger_date between N'".date('Y-m-d', $date_start)."' And N'".date('Y-m-d', $date_end)."' ";

		if($seller_idx){
			$settleSearch .= " And seller_idx = N'$seller_idx'";
			$ledgeSearch .= " And target_idx = N'$seller_idx'";
		}

		if(!isDYLogin()){
			$settleSearch .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
					settle_date
					, isNull(Sum(Case When settle_type = 'SHIPPED' Or settle_type = 'CANCEL' Or settle_type = 'EXCHANGE' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as closing_settle_amount
					, isNull(Sum(Case When settle_type = 'ADJUST_SALE' Or settle_type = 'ADJUST_PURCHASE' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as adjust_settle_amount
				From DY_SETTLE
				Where settle_is_del = N'N'
					$settleSearch
				Group by settle_date
			)
			, CTE_LEDGE as (
				Select
					ledger_date
					, isNull(Sum(ledger_adjust_amount), 0) as sum_ledger_adjust_amount
					, isNull(Sum(ledger_tran_amount), 0) as sum_ledger_tran_amount
					, isNull(Sum(ledger_refund_amount), 0) as sum_ledger_refund_amount
				From DY_LEDGER
				Where ledger_is_del = N'N' And ledger_type = N'LEDGER_SALE' And target_idx = N'$seller_idx'
				$ledgeSearch
				Group by ledger_date
			)
			, CTE_LEDGER_MEMO as (
				Select
					ledger_date
					, ledger_memo_idx
					, ledger_memo
				From DY_LEDGER_MEMO
				Where ledger_memo_is_del = N'N' And ledger_type = N'LEDGER_SALE' And target_idx = N'$seller_idx'
				$ledgeSearch
			)
		";

		$qry .= "
			Select
			Case When S.settle_date is not null Then S.settle_date Else L.ledger_date End  as dt
			, isNull(S.closing_settle_amount, 0) as closing_settle_amount
			, isNull(S.adjust_settle_amount, 0) as adjust_settle_amount
			, isNull(L.sum_ledger_adjust_amount, 0) as sum_ledger_adjust_amount
			, isNull(L.sum_ledger_tran_amount, 0) as sum_ledger_tran_amount
			, isNull(L.sum_ledger_refund_amount, 0) as sum_ledger_refund_amount
			, LM.ledger_memo_idx
			, LM.ledger_memo
			, ( isNull(S.adjust_settle_amount, 0) + isNull(L.sum_ledger_adjust_amount, 0) ) as sum_adjust_amount
			, ( isNull(S.closing_settle_amount, 0) + isNull(S.adjust_settle_amount, 0) + isNull(L.sum_ledger_adjust_amount, 0) ) as sum_settle_amount
			, (
			   isNull(S.closing_settle_amount, 0) + isNull(S.adjust_settle_amount, 0) + isNull(L.sum_ledger_adjust_amount, 0)
			  - isNull(L.sum_ledger_tran_amount, 0) + isNull(L.sum_ledger_refund_amount, 0)
			) as sum_remain_amount
			From 
			CTE_SETTLE S
			Full Outer Join CTE_LEDGE L On S.settle_date = L.ledger_date
			Left Outer Join CTE_LEDGER_MEMO LM On S.settle_date = LM.ledger_date Or L.ledger_date = LM.ledger_date
		
			Order by Case When S.settle_date is not null Then S.settle_date Else L.ledger_date End 
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 매입거래처별원장 일별 세부내역 불러오기
	 * @param $target_idx
	 * @param $date
	 * @param $ledger_type
	 * @return array
	 */
	public function getLedgerDetail($target_idx, $date, $ledger_type)
	{
		$qry = "
			Select 
			*
			, isNull(ledger_adjust_amount, 0) as sum_adjust_amount
			, isNull(ledger_tran_amount, 0) as sum_ledger_tran_amount
			, isNull(ledger_refund_amount, 0) as sum_ledger_refund_amount
			From DY_LEDGER
			Where ledger_is_del = N'N' And target_idx = N'$target_idx' And ledger_date = Convert(date, N'$date') And ledger_type = N'$ledger_type'
			Order by ledger_regdate ASC
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	public function getStockOrderAmountForDate($supplier_idx, $date) {
		$qry = "
			SELECT ISNULL(SUM(stock_unit_price * stock_amount), 0) AS total
			FROM DY_STOCK AS S
				LEFT JOIN DY_PRODUCT AS P ON S.product_idx = P.product_idx
			WHERE stock_kind = 'STOCK_ORDER'
				AND stock_is_confirm = 'Y'
				AND stock_status != 'SHORTAGE'
				AND P.supplier_idx = '$supplier_idx'
		";

		$qry .= "
				AND stock_is_confirm_date >= '".$date." 00:00:00'
				AND stock_is_confirm_date <= '".$date." 23:59:59'
		";

		parent::db_connect();
		$total = parent::execSqlOneCol($qry);
		parent::db_close();

		return $total;
	}

	/**
	 * 거래처별원장 항목 입력
	 * @param $ledger_type
	 * @param $ledger_add_type
	 * @param $target_idx
	 * @param $ledger_date
	 * @param $ledger_title
	 * @param $ledger_adjust_amount
	 * @param $ledger_tran_amount
	 * @param $ledger_refund_amount
	 * @param $ledger_memo
	 * @return int
	 */
	public function insertLedgerDetail($ledger_type, $ledger_add_type, $target_idx, $ledger_date, $ledger_title, $ledger_adjust_amount, $ledger_tran_amount, $ledger_refund_amount, $ledger_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_LEDGER
			(
			 target_idx, ledger_type, ledger_add_type, ledger_date, ledger_title
			 , ledger_adjust_amount, ledger_tran_amount, ledger_refund_amount, ledger_memo
			 , ledger_regip, ledger_regidx
		    )
		    VALUES 
			(
			 N'$target_idx'
			 , N'$ledger_type'
			 , N'$ledger_add_type'
			 , N'$ledger_date'
			 , N'$ledger_title'
			 , N'$ledger_adjust_amount'
			 , N'$ledger_tran_amount'
			 , N'$ledger_refund_amount'
			 , N'$ledger_memo'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;
	}

	/**
	 * 거래처별원장 일별 메모 내용 반환
	 * @param $ledger_memo_idx
	 * @return array|false|null
	 */
	public function getLedgerMemo($ledger_memo_idx)
	{
		$qry = "
			Select ledger_memo From DY_LEDGER_MEMO Where ledger_memo_idx = N'$ledger_memo_idx'
		";

		parent::db_connect();
		$ledger_memo = parent::execSqlOneCol($qry);
		parent::db_close();

		return $ledger_memo;
	}

	/**
	 * 거래처별원장 일별 메모 내용 반환
	 * @param $ledger_idx
	 * @return array|false|null
	 */
	public function getLedgerMemo2($ledger_idx)
	{
		$qry = "
			Select ledger_memo From DY_LEDGER Where ledger_idx = N'$ledger_idx'
		";

		parent::db_connect();
		$ledger_memo = parent::execSqlOneCol($qry);
		parent::db_close();

		return $ledger_memo;
	}

	/**
	 * 거래처별원장 내용가져오기
	 * @param $ledger_idx
	 * @return array|false|null
	 */
	public function getLedgerContents($ledger_idx)
	{
		$qry = "
			Select * From DY_LEDGER Where ledger_idx = N'$ledger_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 거래처별원장 일별 메모 수정
	 * @param $ledger_memo_idx
	 * @param $ledger_type
	 * @param $target_idx
	 * @param $ledger_date
	 * @param $ledger_memo
	 * @return bool|int|resource
	 */
	public function updateLedgerMemo($ledger_memo_idx, $ledger_type, $target_idx, $ledger_date, $ledger_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "Select count(*) From DY_LEDGER_MEMO Where ledger_memo_is_del = N'N' And ledger_memo_idx = N'$ledger_memo_idx'";
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		//다시한번 있는지 체크
		if($cnt == 0){
			$qry = "Select ledger_memo_idx From DY_LEDGER_MEMO Where ledger_memo_is_del = N'N' And ledger_type = N'$ledger_type' And target_idx = N'$target_idx' And ledger_date = N'$ledger_date'";
			parent::db_connect();
			$cnt = parent::execSqlOneCol($qry);
			parent::db_close();
		}

		if(!$cnt){
			$qry = "
				Insert Into DY_LEDGER_MEMO
				(
				 target_idx, ledger_type, ledger_date, ledger_memo
				, ledger_memo_regip, ledger_memo_regidx
				)
				VALUES
				(
					N'$target_idx'
					, N'$ledger_type'
					, N'$ledger_date'
					, N'$ledger_memo'
					, N'$modip'
					, N'$last_member_idx' 
				)
			";

			parent::db_connect();
			$inserted_idx = parent::execSqlInsert($qry);
			parent::db_close();
		}else{
			$qry = "
				Update DY_LEDGER_MEMO
				Set
					ledger_memo = N'$ledger_memo'
				Where ledger_memo_idx = N'$ledger_memo_idx'
			";

			parent::db_connect();
			$inserted_idx = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return $inserted_idx;
	}

	/**
	 * 거래원장에서 등록한 거래내역만 삭제
	 * @param $ledger_idx
	 * @return bool|resource
	 */
	public function deleteLedgerDetail($ledger_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_LEDGER
			Set ledger_is_del = N'Y', ledger_moddate = getdate(), ledger_modip = N'$modip', ledger_modidx = N'$last_member_idx'
			Where ledger_is_del = N'N' And charge_idx = 0 And tran_idx = 0 And ledger_idx = N'$ledger_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 거래처별원장 일별 메모 수정
	 * @param $ledger_idx
	 * @param $ledger_memo
	 * @return bool|int|resource
	 */
	public function updateLedgerMemo2($ledger_idx, $ledger_memo, $ledger_title, $amount_name, $amount)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_LEDGER
			Set
			    ledger_title = N'$ledger_title', 
			    $amount_name = N'$amount', 
				ledger_memo = N'$ledger_memo'
			Where ledger_idx = N'$ledger_idx'
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlUpdate($qry);
		parent::db_close();

		return $inserted_idx;
	}


	/**
	 * 거래처별원장 파일생성 로그 삽입
	 * @param $save_filename
	 * @param $target_idx
	 * @param $ledger_type
	 * @param $user_filename
	 * @param $ledger_period
	 * @param $ledger_is_shrink
	 * @return int
	 */
	public function insertLedgerFileLog($save_filename, $target_idx, $ledger_type, $user_filename, $ledger_period, $ledger_is_shrink)
	{

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_LEDGER_FILE
			(
			  target_idx, ledger_type, ledger_period, ledger_is_shrink
			  , user_file_name, file_name, file_regip, last_member_idx
		    )
		    VALUES 
			(
			 N'$target_idx'
			 , N'$ledger_type'
			 , N'$ledger_period'
			 , N'$ledger_is_shrink'
			 , N'$user_filename'
			 , N'$save_filename'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";
		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;

	}

	/**
	 * 거래처별원장 파일 생성 로그 정보 반환
	 * @param $file_idx
	 * @return array|false|null
	 */
	public function getLedgerDownloadFileLog($file_idx){
		$qry = "
			Select * From DY_LEDGER_FILE
			Where file_idx = N'$file_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 거래처별원장 이메일 발송 로그 Insert
	 * 삭제 상태로 입력 됨
	 * 메일 발송 후 stock_order_email_is_del = 'N' 업데이트 필요
	 * @param $file_idx             : 파일 IDX
	 * @param $target_idx                     : 공급처 IDX
	 * @param $email_receiver       : 수신 Email
	 * @param $email_title          : 메일 제목
	 * @param $email_msg            : 메일 내용
	 * @param $email_receiver_cc    : 함께 받은 발송자 Email
	 * @return int
	 */
	public function insertLedgerEmailSendLog($file_idx, $target_idx, $email_receiver, $email_title, $email_msg, $email_receiver_cc)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_LEDGER_EMAIL
			(
				file_idx, target_idx
				, email_receiver, email_title
				, email_msg, email_receiver_cc
				, email_regip, last_member_idx, email_is_del
			)
			VALUES 
			(
			 N'$file_idx',
			 N'$target_idx',
			 N'$email_receiver',
			 N'$email_title',
			 N'$email_msg',
			 N'$email_receiver_cc',
			 N'$modip',
			 N'$last_member_idx',
			 N'Y'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $rst;
	}

	/**
	 * 거래처별원장 다운로드 로그 Insert
	 * 이메일 발송을 통한 거래처별원장 다운로드 시 로그 Insert
	 * @param $target_idx
	 * @param $file_idx
	 * @param $email_idx
	 * @return string
	 */
	public function insertLedgerDocumentDownLog($target_idx, $file_idx, $email_idx){
		$modip   = $_SERVER["REMOTE_ADDR"];
		$referer = $_SERVER["HTTP_REFERER"];
		$agent   = $_SERVER["HTTP_USER_AGENT"];

		$returnValue = "";

		$qry = "
			Insert Into DY_LEDGER_FILE_DOWN_LOG
			(
			 target_idx, file_idx, email_idx, HTTP_REFERER, USER_AGENT, file_down_regip
			) 
			VALUES 
			(
			 N'$target_idx',
			 N'$file_idx',
			 N'$email_idx',
			 N'$referer',
			 N'$agent',
			 N'$modip'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $returnValue;
	}

	/**
	 * 거래처별원장 이메일 발송 로그 삭제 상태 변경 => 'N'
	 * @param $email_idx
	 * @return bool|resource
	 */
	public function updateLedgerEmailSendLogIsDel($email_idx)
	{
		$qry = "
			Update DY_LEDGER_EMAIL
			Set email_is_del = N'N'
			Where email_idx = N'$email_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}


	/**
	 * 일별 매출 차트 - 정산 관련
	 * @param $data_type
	 * @param $date
	 * @param $seller_idx
	 * @param $period_days
	 * @param $isMobile
	 * @return array
	 */
	public function getLast30DaysSettleData($data_type, $date, $seller_idx, $period_days = 29, $isMobile = false)
	{
		global $GL_Member;
		$end_date = strtotime($date);
		$start_date = strtotime("-".$period_days."days", $end_date);

		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $start_date);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $end_date);

		if($seller_idx){
			$settleSearch = " And seller_idx = N'$seller_idx'";
			echo $settleSearch . "<br>";
		}

		if(!isDYLogin() && !$isMobile){
			$settleSearch .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		if($data_type == "settle_sale_supply")
		{
			//매출액

			$qry = "
				WITH CTE_SETTLE as (
					Select
						settle_date
						, isNull(Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat), 0) as sum_settle_sale_supply
					From DY_SETTLE
					Where settle_is_del = N'N'
						$settleSearch
					Group by settle_date
				)
			";

			$qry .= "
			
				Select
				DateTable.value as dt
				, isNull(S.sum_settle_sale_supply, 0) as sum_settle_sale_supply
				From 
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
				) as DateTable
				Left Outer Join CTE_SETTLE S On DateTable.value = S.settle_date
			
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			return $_list;
		}elseif($data_type == "settle_product_cnt"){
			//수량

			$qry = "
				WITH CTE_SETTLE as (
					Select
						settle_date
						, isNull(Sum(product_option_cnt), 0) as sum_product_option_cnt
					From DY_SETTLE
					Where settle_is_del = N'N'
						$settleSearch
					Group by settle_date
				)
			";

			$qry .= "
			
				Select
				DateTable.value as dt
				, isNull(S.sum_product_option_cnt, 0) as sum_product_option_cnt
				From 
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
				) as DateTable
				Left Outer Join CTE_SETTLE S On DateTable.value = S.settle_date
			
			";


			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			return $_list;
		}

	}

	/**
	 * 일별 매출 차트 - 주문 관련
	 * @param $data_type
	 * @param $date
	 * @param $seller_idx
	 * @param $period_days
	 * @param $isMobile
	 * @return array
	 */
	public function getLast30DaysOrder($data_type, $date, $seller_idx, $period_days = 29, $isMobile = false)
	{

		global $GL_Member;

		$end_date = strtotime($date);
		$start_date = strtotime("-".$period_days."days", $end_date);

		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $start_date);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $end_date);

		if($seller_idx){
			$qrySearch = " And seller_idx = N'$seller_idx'";
		}

		if(!isDYLogin() && !$isMobile){
			$qrySearch .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		if($data_type == "order")
		{
			//주문수

			$qry = "
				WITH CTE_ORDER as (
					Select
						convert(date, order_progress_step_accept_date) as order_date
						, count(*) as order_cnt
					From DY_ORDER
					Where order_is_del = N'N'
						$qrySearch
					Group by convert(DATE, order_progress_step_accept_date)
				)
			";

			$qry .= "
			
				Select
				DateTable.value as dt
				, isNull(S.order_cnt, 0) as order_cnt
				From 
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
				) as DateTable
				Left Outer Join CTE_ORDER S On DateTable.value = S.order_date
			
			";


			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			return $_list;
		}elseif($data_type == "invoice"){
			$qry = "
				WITH CTE_ORDER as (
					Select
						convert(date, invoice_date) as order_date
						, count(*) as order_cnt
					From DY_ORDER
					Where order_is_del = N'N'
						$qrySearch
					Group by convert(DATE, invoice_date)
				)
			";

			$qry .= "
			
				Select
				DateTable.value as dt
				, isNull(S.order_cnt, 0) as order_cnt
				From 
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
				) as DateTable
				Left Outer Join CTE_ORDER S On DateTable.value = S.order_date
			
			";


			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			return $_list;
		}
	}

	/**
	 * 매출 캘린더용 - 해당월의 정산 데이터 반환
	 * !!마감 되지 않은 데이터 포함!!
	 * @param $data_type
	 * @param $date
	 * @param $seller_idx
	 * @param $supplier_idx
	 * @param bool $isMobile
	 * @return array|bool
	 */
	public function getThisMonthsSettleData($data_type, $date, $seller_idx, $supplier_idx, $isMobile = false)
	{
		global $GL_Member;

		$addQry = "";
		//벤더사 로그인일 경우
		if(!isDYLogin() && !$isMobile) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$addQry .= "
		                and seller_idx = N'" .$GL_Member["member_idx"]. "'
					";
			}
		}


		$dt = strtotime($date);
		$start_date = date('Y-m-01', $dt);
		$end_date = date('Y-m-t', strtotime($start_date));

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

		if($seller_idx){
			$settleSearch = " And seller_idx = N'$seller_idx'";
		}

		if($supplier_idx){
			$settleSearch .= " And supplier_idx = N'$supplier_idx'";
		}

		if($data_type == "settle_sale_supply")
		{
			//매출액

			$qry = "
				WITH CTE_SETTLE as (
					Select
						settle_date
						, isNull(Sum(settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat), 0) as sum_settle_sale_supply
						, isNull(Sum(product_option_cnt), 0) as sum_product_option_cnt
					From DY_SETTLE
					Where settle_is_del = N'N' And settle_closing = N'Y'
						$settleSearch
						$addQry
					Group by settle_date
				)
			";

			$qry .= "
			
				Select
				DateTable.value as dt
				, isNull(S.sum_settle_sale_supply, 0) as sum_settle_sale_supply
				, isNull(S.sum_product_option_cnt, 0) as sum_product_option_cnt
				From 
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
				) as DateTable
				Left Outer Join CTE_SETTLE S On DateTable.value = S.settle_date
			
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			return $_list;
		}else{
			return false;
		}

	}


	/**
	 * 광고비관리 - 광고비 입력
	 * @param $seller_idx
	 * @param $ad_date
	 * @param $ad_inout
	 * @param $ad_amount
	 * @param $ad_product_name
	 * @param $ad_memo
	 * @return int
	 */
	public function insertAdCost($seller_idx, $ad_date, $ad_inout, $ad_amount, $ad_product_name, $ad_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//광고비 테이블 입력
		$qry = "
			Insert Into DY_SETTLE_AD_COST
			(ad_date, seller_idx, ad_inout, ad_amount, ad_product_name, ad_keyword, ad_memo, ad_regip, ad_regidx)
			VALUES
			(
			 N'$ad_date'
			 , N'$seller_idx'
			 , N'$ad_inout'
			 , N'$ad_amount'
			 , N'$ad_product_name'
			 , N''
			 , N'$ad_memo'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";
		$inserted_idx = parent::execSqlInsert($qry);

		//충전일 경우 정산테이블 입력
		if($ad_inout == 1) {

			$vendor_use_charge = "";
			$vendor_grade = "";

			$qry = "
				Select vendor_grade, vendor_use_charge From DY_SELLER Where seller_idx = N'$seller_idx'
			";
			$_seller_view = parent::execSqlOneRow($qry);

			$vendor_grade = $_seller_view["vendor_grade"];
			$vendor_use_charge = $_seller_view["vendor_use_charge"];

			$qry = "
				Insert Into DY_SETTLE
				(
				 settle_date, settle_type, seller_idx, vendor_use_charge, vendor_grade, settle_ad_amt, settle_memo, settle_regip, last_member_idx
			    ) VALUES
				(
				 N'$ad_date'
				 , N'AD_COST_CHARGE'
				 , N'$seller_idx'
				 , N'$vendor_use_charge'
				 , N'$vendor_grade'
				 , N'$ad_amount'
				 , N'$ad_memo'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";

			$inserted_idx2 = parent::execSqlInsert($qry);
		}


		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $inserted_idx;
	}


	/**
	 * 배송 통계
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $delivery_code
	 * @param $seller_idx
	 * @param $supplier_idx
	 * @return array
	 */
	public function getDeliveryStatistics($period_type, $date_start, $date_end, $delivery_code, $seller_idx, $supplier_idx)
	{
		global $GL_Member;

		if($period_type == "order_accept_regdate"){
			$gruopByCol = " Convert(varchar(10), order_progress_step_accept_date, 120) ";
			$dateQry = " And order_progress_step_accept_date >= N'$date_start' And order_progress_step_accept_date <= N'$date_end' ";
		}elseif($period_type == "invoice_date"){
			$gruopByCol = " Convert(varchar(10), invoice_date, 120) ";
			$dateQry = " And invoice_date >= N'$date_start' And invoice_date <= N'$date_end' ";
		}elseif($period_type == "shipping_date"){
			$gruopByCol = " Convert(varchar(10), shipping_date, 120) ";
			$dateQry = " And shipping_date >= N'$date_start' And shipping_date <= N'$date_end' ";
		}

		if($seller_idx){
			$sellerQry = " And seller_idx = N'$seller_idx'";
		}

		if($supplier_idx){
			$supplierQry = " And order_idx in (
				Select S_O.order_idx 
				From DY_ORDER S_O
					Inner Join DY_ORDER_PRODUCT_MATCHING S_M On S_O.order_idx = S_M.order_idx
					Left Outer Join DY_PRODUCT S_P On S_P.product_idx = S_M.product_idx
				Where 
					S_O.order_is_del = N'N' And S_O.order_progress_step = N'ORDER_SHIPPED'
					And order_matching_is_del = N'N'
					And S_P.supplier_idx = N'$supplier_idx'
			) 
			";
		}

		//벤더사 로그인일 경우
		if(!isDYLogin()){
			$vendorQry = " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}


		$qry = "
			Select
			$gruopByCol as date
			
			, Sum(Case When delivery_is_free = 'Y' Then 1 Else 0 End) as prepay_cnt
			, Sum(Case When delivery_is_free = 'N' Then 1 Else 0 End) as afterpay_cnt
			, Sum(Case When order_is_pack = 'Y' Then 1 Else 0 End) as pack_cnt
			, Sum(Case When order_is_pack = 'N' Then 1 Else 0 End) as single_cnt
			
			From DY_ORDER
			Where order_is_del = N'N' And order_progress_step = N'ORDER_SHIPPED'
				And delivery_code = N'$delivery_code' 
			$dateQry
			$sellerQry
			$supplierQry
			$vendorQry
			Group by $gruopByCol
			Order by $gruopByCol DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 송장번호 이력 조회
	 * @param $period_type
	 * @param $date_start
	 * @param $date_end
	 * @param $delivery_code
	 * @param $seller_idx
	 * @param $supplier_idx
	 * @return array
	 */
	public function getInvoiceHistory($period_type, $date_start, $date_end, $delivery_code, $seller_idx, $supplier_idx){
		if($period_type == "order_accept_regdate"){
			$gruopByCol = " Convert(varchar(10), order_progress_step_accept_date, 120) ";
			$dateQry = " And order_progress_step_accept_date >= N'$date_start' And order_progress_step_accept_date <= N'$date_end' ";
		}elseif($period_type == "invoice_date"){
			$gruopByCol = " Convert(varchar(10), invoice_date, 120) ";
			$dateQry = " And cs_regdate >= N'$date_start' And cs_regdate <= N'$date_end' ";
		}elseif($period_type == "shipping_date"){
			$gruopByCol = " Convert(varchar(10), shipping_date, 120) ";
			$dateQry = " And shipping_date >= N'$date_start' And shipping_date <= N'$date_end' ";
		}

		if($seller_idx){
			$sellerQry = " And seller_idx = N'$seller_idx'";
		}

		if($supplier_idx){
			$supplierQry = " And order_idx in (
				Select S_O.order_idx 
				From DY_ORDER S_O
					Inner Join DY_ORDER_PRODUCT_MATCHING S_M On S_O.order_idx = S_M.order_idx
					Left Outer Join DY_PRODUCT S_P On S_P.product_idx = S_M.product_idx
				Where 
					S_O.order_is_del = N'N' And S_O.order_progress_step = N'ORDER_SHIPPED'
					And order_matching_is_del = N'N'
					And S_P.supplier_idx = N'$supplier_idx'
			) 
			";
		}


		$qry = "
			Select
				 O.order_idx
			     , invoice_date
			     , invoice_no
			     , invoice_reg_type
			     , O.delivery_code
			     , D.delivery_name
				 , C.cs_regdate, Convert(varchar(30), C.cs_regdate, 120) as cs_regdate2
				 , M.member_id
			From DY_ORDER O 
				Inner Join DY_ORDER_CS C On O.order_idx = C.order_idx And C.cs_task = N'INVOICE_INSERT' And C.cs_is_del = N'N'
				Left Outer Join DY_MEMBER M On C.last_member_idx = M.idx
				Left Outer Join (
				  Select delivery_code, delivery_name From DY_DELIVERY_CODE Group by delivery_code, delivery_name
				) D On O.delivery_code = D.delivery_code
			Where O.order_is_del = N'N' And invoice_no <> '' And invoice_no is not null
				And O.delivery_code = N'$delivery_code' 
			$dateQry
			$sellerQry
			$supplierQry
			Order by C.cs_regdate DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 기간별 매출 이익
	 * @param $date_year
	 * @param $date_month
	 * @param $seller_idx
	 * @return array
	 */
	public function getSalesProfitByPeriod($date_year, $date_month, $seller_idx)
	{

		$time = mktime(0, 0, 0, $date_month, 1, $date_year);

		$date_start = date('Y-m-d', $time);
		$date_end = date('Y-m-t', $time);

		$searchWhere = "";
		if($seller_idx) {
			$searchWhere = " And T.seller_idx = N'$seller_idx'";
		}

		$qry = "
		
			WITH CTE_SETTLE as (
			
				Select
				settle_date, T.seller_idx, seller_name
				, Sum(settle_sale_supply) - Sum(settle_sale_commission_in_vat) + Sum(settle_delivery_in_vat) - Sum(settle_delivery_commission_in_vat) as settle_sale_supply
				, Sum(settle_sale_supply_ex_vat) - Sum(settle_sale_commission_ex_vat) + Sum(settle_delivery_ex_vat) - Sum(settle_delivery_commission_ex_vat) as settle_sale_supply_ex_vat
				, Sum(settle_purchase_supply) + Sum(settle_purchase_delivery_in_vat) as settle_purchase_supply
				, Sum(settle_purchase_supply_ex_vat) + Sum(settle_purchase_delivery_ex_vat) as settle_purchase_supply_ex_vat
				, Sum(settle_sale_profit) as settle_sale_profit
				, GROUPING(settle_date) as date_grp
				, GROUPING(T.seller_idx) as idx_grp
				, GROUPING(seller_name) as name_grp
				From DY_SETTLE T
				Left Outer Join DY_SELLER S On S.seller_idx = T.seller_idx
				Where settle_is_del = N'N' And settle_type in (N'SHIPPED', N'ADJUST_SALE', N'ADJUST_PURCHASE', N'CANCEL')
				
				And settle_date between convert(date, N'$date_start') And convert(date, N'$date_end')
				$searchWhere
				Group by 
					settle_date, T.seller_idx, seller_name WITH RollUp
			
			)
			
			
			Select 
				*
			From CTE_SETTLE
			Where 
				(date_grp = 0 And idx_grp = 0 And name_grp = 0)
				Or 
				(date_grp = 0 And idx_grp = 1 And name_grp = 1)
				Or 
				(date_grp = 1 And idx_grp = 1 And name_grp = 1)
			Order by isNull(settle_date, '2100-12-31')
			, Case When seller_name is Null Then 1 Else 0 End, seller_name
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 상품별 매출 이익
	 * @param $date_start
	 * @param $date_end
	 * @param $seller_idx
	 * @param $product_idx
	 * @return array
	 */
	public function getSalesProfitByProduct($date_start, $date_end, $seller_idx, $product_idx)
	{

		$searchWhere = "";
		if($seller_idx) {
			$searchWhere = " And T.seller_idx = N'$seller_idx'";
		}
		if($product_idx) {
			$searchWhere = " And T.product_idx = N'$product_idx'";
		}

		$qry = "
		
			WITH CTE_SETTLE as (
				Select
				settle_date, product_idx, product_name, product_option_idx, product_option_name
				, Sum(settle_sale_supply) as settle_sale_supply
				, Sum(settle_sale_supply_ex_vat) as settle_sale_supply_ex_vat
				, Sum(settle_purchase_supply) as settle_purchase_supply
				, Sum(settle_purchase_supply_ex_vat) as settle_purchase_supply_ex_vat
				, Sum(settle_sale_profit) as settle_sale_profit
				, Sum(product_option_cnt) as product_option_cnt
				, GROUPING(settle_date) as date_grp
				, GROUPING(T.product_idx) as idx_grp
				, GROUPING(product_name) as name_grp
				, GROUPING(T.product_option_idx) as idx2_grp
				, GROUPING(product_option_name) as name2_grp
				From DY_SETTLE T
				Where settle_is_del = N'N' And settle_type in (N'SHIPPED', N'ADJUST_SALE', N'ADJUST_PURCHASE', N'CANCEL')
				
				And settle_date between convert(date, N'$date_start') And convert(date, N'$date_end')
				$searchWhere
				Group by 
					settle_date, product_idx, product_name, product_option_idx, product_option_name WITH RollUp
			
			)
			
			
			Select 
				*
			From CTE_SETTLE
			Where 
				(date_grp = 0 And idx_grp = 0 And name_grp = 0 And idx2_grp = 0 And name2_grp = 0)
				Or 
				(date_grp = 0 And idx_grp = 1 And name_grp = 1 And idx2_grp = 1 And name2_grp = 1)
				Or 
				(date_grp = 1 And idx_grp = 1 And name_grp = 1 And idx2_grp = 1 And name2_grp = 1)
			Order by isNull(settle_date, '2100-12-31')
			, Case When product_name is Null Then 1 Else 0 End, product_name
			, Case When product_option_name is Null Then 1 Else 0 End, product_option_name
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 정산 - 매출/매입
	 * 판매처 판매일보 금액 및 세금계산서 발행 내역을 반환
	 * @param $date_ym
	 * @param $tax_type
	 * @param $target_idx
	 * @return array
	 */
	public function getTransactionSumByMonth($date_ym, $tax_type, $target_idx = ""){

		$time = strtotime($date_ym . "-01");

		$date_start = date('Y-m-d', $time);
		$date_end = date('Y-m-t', $time);

		$settle_amt_col = "settle_sale_supply";

		if($tax_type == "SALE"){

			$settle_amt_col = "settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat";

			$target_table = "DY_SELLER";
			$target_idx_col = "S.seller_idx";
			$target_name_col = "S.seller_name";

			$settle_groupby_col = "seller_idx";
			$settle_target_idx = "C.seller_idx";

		}elseif($tax_type == "PURCHASE"){

			$settle_amt_col = "settle_purchase_supply + settle_purchase_delivery_in_vat";

			$target_table = "DY_MEMBER_SUPPLIER";
			$target_idx_col = "S.member_idx";
			$target_name_col = "S.supplier_name";

			$settle_groupby_col = "supplier_idx";
			$settle_target_idx = "C.supplier_idx";
		}

		if($target_idx){
			$onlyTargetQry = "  And $target_idx_col = N'$target_idx' ";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
				       $settle_groupby_col
				       , Convert(nvarchar(7), settle_date, 120) as [Month]
					   , isNull(Sum($settle_amt_col), 0) as sum_amt
					   , isNull(Sum(Case When product_tax_type = 'TAXATION' Then $settle_amt_col Else 0 End), 0) as taxation_amt
					   , isNull(Sum(Case When product_tax_type = 'FREE' Then $settle_amt_col Else 0 End), 0) as free_amt
					   , isNull(Sum(Case When product_tax_type = 'SMALL' Then $settle_amt_col Else 0 End), 0) as small_amt
				From DY_SETTLE
				Where settle_is_del = N'N'
						And settle_type in (N'SHIPPED', N'ADJUST_SALE', N'ADJUST_PURCHASE', N'CANCEL')
						And settle_date between Convert(date, N'$date_start') And Convert(date, N'$date_end') 
				Group by $settle_groupby_col, Convert(nvarchar(7), settle_date, 120)
			)
			
			Select
				$target_name_col as target_name
				, $target_idx_col as target_idx
				, C.sum_amt, C.taxation_amt, C.free_amt, C.small_amt
				, T.taxation_amount
				, T.taxation_confirm
				, T.taxation_date
				, T.taxation_memo
				
				, T.free_amount
				, T.free_confirm
				, T.free_date
				, T.free_memo
				
				, T.small_amount
				, T.small_confirm
				, T.small_date
				, T.small_memo
				
				, (isNull(T.taxation_amount, 0) + isNull(T.free_amount, 0) + isNull(T.small_amount, 0)) as sum_amount
			From 
				$target_table S
				Left Outer Join CTE_SETTLE C
					On $target_idx_col = $settle_target_idx
				Left Outer Join DY_SETTLE_TAX T
					On $target_idx_col = T.target_idx
						And C.Month = T.tax_ym
						And T.tax_type = N'$tax_type'
						And T.tax_is_del = N'N'
			Where 1 = 1
			$onlyTargetQry
			Order by $target_name_col COLLATE Korean_Wansung_BIN ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 정산 - 계산서 값 Update
	 * @param $tax_type
	 * @param $date_ym
	 * @param $target_idx
	 * @param $taxation_amount
	 * @param $taxation_memo
	 * @param $free_amount
	 * @param $free_memo
	 * @param $small_amount
	 * @param $small_memo
	 * @return bool|resource
	 */
	public function updateTaxInfo($tax_type, $date_ym, $target_idx, $taxation_amount, $taxation_memo, $free_amount, $free_memo, $small_amount, $small_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		if(!is_numeric($taxation_amount)) $taxation_amount = 0;
		if(!is_numeric($free_amount)) $free_amount = 0;
		if(!is_numeric($small_amount)) $small_amount = 0;

		$qry = "
			IF exists (
				select * 
				From DY_SETTLE_TAX 
				Where 
					tax_is_del = N'N' 
					And tax_ym = N'$date_ym' 
					And tax_type = N'$tax_type'
					And target_idx = N'$target_idx'
			)
			Begin
				Update DY_SETTLE_TAX
					Set 
						taxation_amount = N'$taxation_amount'
						, taxation_memo = N'$taxation_memo'
						, free_amount = N'$free_amount'
						, free_memo = N'$free_memo'
						, small_amount = N'$small_amount'
						, small_memo = N'$small_memo'
						, tax_moddate = getdate()
						, tax_modip = N'$modip'
						, tax_modidx = N'$last_member_idx'
					Where
						tax_is_del = N'N' 
						And tax_ym = N'$date_ym' 
						And tax_type = N'$tax_type'
						And target_idx = N'$target_idx'
			End
			ELSE
			Begin
				Insert Into DY_SETTLE_TAX
				(tax_type, target_idx, tax_ym, taxation_amount, taxation_memo, free_amount, free_memo, small_amount, small_memo, tax_regip, tax_regidx)
				VALUES
				(
				N'$tax_type'
				, N'$target_idx'
				, N'$date_ym'
				, N'$taxation_amount'
				, N'$taxation_memo'
				, N'$free_amount'
				, N'$free_memo'
				, N'$small_amount'
				, N'$small_memo'
				, N'$modip'
				, N'$last_member_idx'
				)
			End
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 정산 - 계산서 내용 확인
	 * @param $tax_type
	 * @param $date_ym
	 * @param $target_idx
	 * @param $col_name
	 * @return bool|resource
	 */
	public function updateTaxConfirm($tax_type, $date_ym, $target_idx, $col_name)
	{

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$confirm_col = $col_name . "_confirm";
		$confirm_date_col = $col_name . "_date";
		$confirm_idx_col = $col_name . "_member_idx";

		$qry = "
			Update DY_SETTLE_TAX
			Set
				$confirm_col = N'Y'
				,  $confirm_date_col = getdate()
				,  $confirm_idx_col = N'$last_member_idx'
			Where 
				tax_is_del = N'N' 
				And tax_ym = N'$date_ym' 
				And tax_type = N'$tax_type'
				And target_idx = N'$target_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 정산 - 일별 팝업 내용
	 * @param $date_ym
	 * @param $tran_type
	 * @param $target_idx
	 * @return array
	 */
	public function getSettleDailySum($date_ym, $tran_type, $target_idx)
	{

		$time = strtotime($date_ym . "-01");

		$date_start = date('Y-m-d', $time);
		$date_end = date('Y-m-t', $time);

		$settle_amt_col = "settle_sale_supply";

		if($tran_type == "SALE"){

			$settle_amt_col = "settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat";

			$target_table = "DY_SELLER";
			$target_idx_col = "S.seller_idx";
			$target_name_col = "S.seller_name";

			$settle_groupby_col = "seller_idx";
			$settle_target_idx = "C.seller_idx";

		}elseif($tran_type == "PURCHASE"){

			$settle_amt_col = "settle_purchase_supply + settle_purchase_delivery_in_vat";

			$target_table = "DY_MEMBER_SUPPLIER";
			$target_idx_col = "S.member_idx";
			$target_name_col = "S.supplier_name";

			$settle_groupby_col = "supplier_idx";
			$settle_target_idx = "C.supplier_idx";
		}

		if($target_idx){
			$onlyTargetQry = "  And $target_idx_col = N'$target_idx' ";
		}

		$qry = "
			WITH CTE_SETTLE as (
				Select
				       $settle_groupby_col
				       , settle_date
					   , isNull(Sum($settle_amt_col), 0) as sum_amt
					   , isNull(Sum(Case When product_tax_type = 'TAXATION' Then $settle_amt_col Else 0 End), 0) as taxation_amt
					   , isNull(Sum(Case When product_tax_type = 'FREE' Then $settle_amt_col Else 0 End), 0) as free_amt
					   , isNull(Sum(Case When product_tax_type = 'SMALL' Then $settle_amt_col Else 0 End), 0) as small_amt
				From DY_SETTLE
				Where settle_is_del = N'N'
						And settle_type in (N'SHIPPED', N'ADJUST_SALE', N'ADJUST_PURCHASE', N'CANCEL')
						And settle_date between Convert(date, N'$date_start') And Convert(date, N'$date_end') 
				Group by $settle_groupby_col, settle_date
			)
			
			Select C.*,  $target_name_col as target_name
			From
				CTE_SETTLE C
				Left Outer Join $target_table S On $target_idx_col = $settle_target_idx 
			Where 1 = 1
			$onlyTargetQry
			Order by settle_date ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}



	/**
	 * 계산서 파일생성 로그 삽입
	 * @param $save_filename
	 * @param $target_idx
	 * @param $tax_type
	 * @param $user_filename
	 * @param $ledger_period
	 * @return int
	 */
	public function insertTaxFileLog($save_filename, $target_idx, $tax_type, $user_filename, $ledger_period)
	{

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_SETTLE_TAX_FILE
			(
			  target_idx, tax_type, tax_period
			  , user_file_name, file_name, file_regip, last_member_idx
		    )
		    VALUES 
			(
			 N'$target_idx'
			 , N'$tax_type'
			 , N'$ledger_period'
			 , N'$user_filename'
			 , N'$save_filename'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";
		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;

	}

	/**
	 * 계산서 파일 생성 로그 정보 반환
	 * @param $file_idx
	 * @return array|false|null
	 */
	public function getTaxDownloadFileLog($file_idx){
		$qry = "
			Select * From DY_SETTLE_TAX_FILE
			Where file_idx = N'$file_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 계산서 이메일 발송 로그 Insert
	 * 삭제 상태로 입력 됨
	 * 메일 발송 후 email_is_del = 'N' 업데이트 필요
	 * @param $file_idx             : 파일 IDX
	 * @param $target_idx                     : 공급처 IDX
	 * @param $email_receiver       : 수신 Email
	 * @param $email_title          : 메일 제목
	 * @param $email_msg            : 메일 내용
	 * @param $email_receiver_cc    : 함께 받은 발송자 Email
	 * @return int
	 */
	public function insertTaxEmailSendLog($file_idx, $target_idx, $email_receiver, $email_title, $email_msg, $email_receiver_cc)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_SETTLE_TAX_EMAIL
			(
				file_idx, target_idx
				, email_receiver, email_title
				, email_msg, email_receiver_cc
				, email_regip, last_member_idx, email_is_del
			)
			VALUES 
			(
			 N'$file_idx',
			 N'$target_idx',
			 N'$email_receiver',
			 N'$email_title',
			 N'$email_msg',
			 N'$email_receiver_cc',
			 N'$modip',
			 N'$last_member_idx',
			 N'Y'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $rst;
	}

	/**
	 * 계산서 다운로드 로그 Insert
	 * 이메일 발송을 통한 거래처별원장 다운로드 시 로그 Insert
	 * @param $target_idx
	 * @param $file_idx
	 * @param $email_idx
	 * @return string
	 */
	public function insertTaxDocumentDownLog($target_idx, $file_idx, $email_idx){
		$modip   = $_SERVER["REMOTE_ADDR"];
		$referer = $_SERVER["HTTP_REFERER"];
		$agent   = $_SERVER["HTTP_USER_AGENT"];

		$returnValue = "";

		$qry = "
			Insert Into DY_SETTLE_TAX_FILE_DOWN_LOG
			(
			 target_idx, file_idx, email_idx, HTTP_REFERER, USER_AGENT, file_down_regip
			) 
			VALUES 
			(
			 N'$target_idx',
			 N'$file_idx',
			 N'$email_idx',
			 N'$referer',
			 N'$agent',
			 N'$modip'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $returnValue;
	}

	/**
	 * 계산서 이메일 발송 로그 삭제 상태 변경 => 'N'
	 * @param $email_idx
	 * @return bool|resource
	 */
	public function updateTaxEmailSendLogIsDel($email_idx)
	{
		$qry = "
			Update DY_SETTLE_TAX_EMAIL
			Set email_is_del = N'N'
			Where email_idx = N'$email_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}


	/**
	 * 정산 테이블에 쇼핑몰 주문번호가 있는지 확인하여 개수 리턴
	 * @param $seller_idx
	 * @param $market_order_no
	 * @return int|mixed
	 */
	public function existsMarketOrderNoInSettle($seller_idx, $market_order_no)
	{
		$qry = "
			Select count(*) From DY_SETTLE
			Where seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no'
		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		return $cnt;
	}

	/**
	 * 정산예정금 데이터가 존재하는지 확인하여 개수 리턴
	 * @param $seller_idx
	 * @param $market_order_no
	 * @return int|mixed
	 */
	public function existsMarketOrderNoInLoss($seller_idx, $market_order_no)
	{
		$qry = "
			Select count(*) From DY_SETTLE_LOSS
			Where seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no'
		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		return $cnt;
	}

	public function insertLossXls($seller_idx, $market_order_no, $order_name, $market_product_name, $order_cnt, $order_amt, $commission, $commission_etc, $delivery_fee, $delivery_commission, $settle_amount, $loss_date)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			IF exists (select * From DY_SETTLE_LOSS Where loss_is_del = N'N' And seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no')
			Begin
				Update DY_SETTLE_LOSS
				Set
					order_name = N'$order_name'
					, market_product_name = N'$market_product_name'
					, order_cnt = N'$order_cnt'
					, order_amt = N'$order_amt'
					, commission = N'$commission'
					, commission_etc = N'$commission_etc'
					, delivery_fee = N'$delivery_fee'
					, delivery_commission = N'$delivery_commission'
					, settle_amount = N'$settle_amount'
					, loss_confirm = N'N'
					, loss_moddate = getdate()
					, loss_modip = N'$modip'
					, loss_modidx = N'$last_member_idx'
				Where 
				seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no'
			End
			Else
			Begin
				Insert Into DY_SETTLE_LOSS
				(
				 loss_date, seller_idx, market_order_no, order_name, market_product_name 
				 , order_cnt, order_amt, commission, commission_etc, delivery_fee, delivery_commission, settle_amount
				 , loss_regip, loss_regidx
				 )
				 VALUES 
				(
				 N'$loss_date'
				 , N'$seller_idx'
				 , N'$market_order_no'
				 , N'$order_name'
				 , N'$market_product_name'
				 , N'$order_cnt'
				 , N'$order_amt'
				 , N'$commission'
				 , N'$commission_etc'
				 , N'$delivery_fee'
				 , N'$delivery_commission'
				 , N'$settle_amount'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			End
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlUpdate($qry);
		parent::db_close();

		return $inserted_idx;

	}


	/**
	 * 정산예정금 업로드 이력 저장
	 * 업로드된 임시 파일을 저장폴더(DY_ORDER_INVOICE_PATH) 로 이동 후
	 * 로그 Insert
	 * @param $xls_filename : 임시 저장된 엑셀 파일명
	 * @param $user_filename : 사용자가 업로드한 엑셀 파일명
	 * @param $apply_count : 입력 수
	 * @return bool|int
	 */
	public function insertLossUploadLog($xls_filename, $user_filename, $apply_count)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;
		$dest_filename_fullpath = DY_LOSS_XLS_PATH . "/" . $xls_filename;
		if(file_exists($xls_filename_fullpath))
		{
			if(rename($xls_filename_fullpath, $dest_filename_fullpath)){

				$qry = "
					Insert Into DY_SETTLE_LOSS_UPLOAD_LOG
					(
					 upload_log_apply_count, upload_log_savefilename
					 , upload_log_userfilename, upload_log_regip, last_member_idx
					 )
					 VALUES 
					(
					 N'$apply_count',
					 N'$xls_filename',
					 N'$user_filename',
					 N'$modip',
					 N'$last_member_idx'
					)
				";

				parent::db_connect();
				$returnValue = parent::execSqlInsert($qry);
				parent::db_close();
			}
		}

		return $returnValue;
	}

	public function updateLossConfirm($loss_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_SETTLE_LOSS
			Set loss_confirm = N'Y'
				, loss_confirm_date = getdate()
				, loss_confirm_idx = N'$last_member_idx'
			Where loss_idx = N'$loss_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	public function getLossWidthBankCustomerIn($target_idx, $date_start, $date_end)
	{

		$qry = "
			Select tran_idx, tran_date, Sum(tran_amount) as tran_amount, Max(tran_memo) as tran_memo, Max(A.account_name) as account_name
			, GROUPING(tran_idx) as grp1
			, GROUPING(tran_date) as grp2
			From DY_SETTLE_REPORT R
				Left Outer Join DY_ACCOUNT_CODE A On R.account_idx = A.account_idx
			Where R.tran_type = N'BANK_CUSTOMER_IN' And R.tran_inout = N'IN'
					And R.target_idx = N'$target_idx'
					And R.tran_date between N'$date_start' And N'$date_end'
			Group by tran_idx, tran_date WITH ROLLUP
			Having 
				(GROUPING(tran_idx) = 0 And GROUPING(tran_date) = 0)
				Or 
				(GROUPING(tran_idx) = 1 And GROUPING(tran_date) = 1)

			Order by isNull(tran_date, '2999-01-01') asc
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	public function getLossWithLedgerRefund($target_idx, $date_start, $date_end)
	{

		$qry = "
			Select ledger_idx, ledger_date, Sum(ledger_refund_amount) as tran_amount, Max(ledger_memo) as tran_memo, Max(ledger_title) as ledger_title
			, GROUPING(ledger_idx) as grp1
			, GROUPING(ledger_date) as grp2
			From DY_LEDGER L
			Where L.ledger_type = N'LEDGER_SALE' And L.ledger_add_type = N'REFUND' And L.ledger_is_del = N'N'
					And L.target_idx = N'$target_idx'
					And L.ledger_date between N'$date_start' And N'$date_end'
			Group by ledger_idx, ledger_date WITH ROLLUP
			Having 
				(GROUPING(ledger_idx) = 0 And GROUPING(ledger_date) = 0)
				Or 
				(GROUPING(ledger_idx) = 1 And GROUPING(ledger_date) = 1)

			Order by isNull(ledger_date, '2999-01-01') asc
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	public function getLossStatistics($seller_idx, $date_start, $date_end)
	{

		$qry = "
			WITH CTE_SETTLE As (
				Select 
					settle_date
					, Sum((settle_sale_supply + settle_delivery_in_vat) - (settle_sale_commission_in_vat + settle_delivery_commission_in_vat)) as settle_sum
				From DY_SETTLE
				Where 
					settle_is_del = N'N'
					And seller_idx = N'$seller_idx'
					And settle_date between N'$date_start' And N'$date_end'
				Group by settle_date
			)
			, CTE_LOSS As (
				Select 
					loss_date
					, Sum(order_amt + delivery_fee - commission - delivery_commission) as site_sum
					, Sum(commission_etc) as commission_etc
				From DY_SETTLE_LOSS
				Where 
					loss_is_del = N'N'
					And seller_idx = N'$seller_idx'
					And loss_date between N'$date_start' And N'$date_end'
				Group by loss_date
			)
			, CTE_REPORT As (
				Select 
					tran_date, Sum(tran_amount) as tran_amount
				From DY_SETTLE_REPORT
				Where tran_type = N'BANK_CUSTOMER_IN' And tran_inout = N'IN'
				And target_idx = N'$seller_idx'
				And tran_date between N'$date_start' And N'$date_end'
				Group by tran_date
			)
		
		
			Select
				Case When settle_date is not null Then settle_date
				When loss_date is not null Then loss_date
				When tran_date is not null Then tran_date 
				End as all_date
				, *
			From CTE_SETTLE S
				Full Outer Join CTE_LOSS L On S.settle_date = L.loss_date
				Full Outer Join CTE_REPORT R On R.tran_date = S.settle_date Or R.tran_date = L.loss_date  
			Order by all_date ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 일별상품별통계 차트 데이터
	 * @param $date_start
	 * @param $date_end
	 * @param $product_option_idx
	 * @param $product_option_purchase_price
	 * @param $seller_idx
	 * @return array|bool
	 */
	public function getProductChartData($date_start, $date_end, $product_option_idx, $product_option_purchase_price, $seller_idx)
	{
		$seller_qry = "";
		if($seller_idx)
		{
			$seller_qry = " And seller_idx = N'$seller_idx'";
		}


		$qry = "
			Select
				A.seller_idx, S.seller_name
			From
		     (
				Select seller_idx 
				From DY_SETTLE 
				Where settle_is_del = N'N' And settle_date between Convert(date, '$date_start') And Convert(date, '$date_end')
		        And product_option_idx = N'$product_option_idx' And product_option_purchase_price = N'$product_option_purchase_price' $seller_qry
		        Group by seller_idx
   			 ) as A
			Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
			Order by S.seller_name ASC
		";
		parent::db_connect();
		$seller_list = parent::execSqlList($qry);
		parent::db_close();

		if(!$seller_list) return false;

		$seller_col_ary = array();
		foreach($seller_list as $ss)
		{
			$seller_col_ary[] = " isNull(Sum(CASE WHEN seller_idx = ". $ss["seller_idx"] ." THEN settle_sale_supply Else 0 End), 0) as '".$ss["seller_name"]."' ";
		}
		$seller_col = implode(", ", $seller_col_ary);

		$qry = "
			Select
				settle_date as date
				, $seller_col
			From DY_SETTLE
		    WHERE settle_is_del = N'N'
			And settle_date between Convert(date, '$date_start') And Convert(date, '$date_end')
			And product_option_idx = N'$product_option_idx' And product_option_purchase_price = N'$product_option_purchase_price' $seller_qry
		    Group by settle_date
			Order by settle_date ASC
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 일별상품별통계 차트 데이터
	 * @param $date_start
	 * @param $date_end
	 * @param $product_option_idx
	 * @param $product_option_purchase_price
	 * @param $seller_idx
	 * @return array|bool
	 */
	public function getProductChartMonthlyData($date_start, $date_end, $product_option_idx, $product_option_purchase_price, $seller_idx)
	{
		$seller_qry = "";
		if($seller_idx)
		{
			$seller_qry = " And seller_idx = N'$seller_idx'";
		}


		$qry = "
			Select
				A.seller_idx, S.seller_name
			From
		     (
				Select seller_idx 
				From DY_SETTLE 
				Where settle_is_del = N'N' And settle_date between Convert(date, '$date_start') And Convert(date, '$date_end')
		        And product_option_idx = N'$product_option_idx' And product_option_purchase_price = N'$product_option_purchase_price' $seller_qry
		        Group by seller_idx
   			 ) as A
			Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
			Order by S.seller_name ASC
		";
		parent::db_connect();
		$seller_list = parent::execSqlList($qry);
		parent::db_close();

		if(!$seller_list) return false;

		$seller_col_ary = array();
		foreach($seller_list as $ss)
		{
			$seller_col_ary[] = " isNull(Sum(CASE WHEN seller_idx = ". $ss["seller_idx"] ." THEN settle_sale_supply Else 0 End), 0) as '".$ss["seller_name"]."' ";
		}
		$seller_col = implode(", ", $seller_col_ary);

		$qry = "
			Select
				left(settle_date, 7) as date
				, $seller_col
			From DY_SETTLE
		    WHERE settle_is_del = N'N'
			And settle_date between Convert(date, '$date_start') And Convert(date, '$date_end')
			And product_option_idx = N'$product_option_idx' And product_option_purchase_price = N'$product_option_purchase_price' $seller_qry
		    Group by left(settle_date, 7)
			Order by left(settle_date, 7) ASC
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}
}
?>