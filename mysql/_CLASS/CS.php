<?php
/**
 * CS 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class CS extends Dbconn
{
	/**
	 * 주문IDX 생성 db connect 필요
	 * @return int|mixed
	 */
	private function makeOrderIdx()
	{
		//발주 IDX 생성
		$_new_order_idx = 0;
		$qry = "Select isnull(MAX(order_idx), 0) as max_order_idx From DY_ORDER";
		$max_order_idx = parent::execSqlOneCol($qry);
		$_new_order_idx = 0;
		if($max_order_idx < 100001){
			$_new_order_idx = 100001;
		}else{
			$_new_order_idx = $max_order_idx + 1;
		}

		return $_new_order_idx;
	}

	/**
	 * CS 창 탭 내용 가져오기
	 * 합포 IDX 로 관련 주문 내역 모두를 불러온다
	 * @param $order_pack_idx
	 * @return array
	 */
	public function getOrderDetailRelateOrderPackIdx($order_pack_idx, $sort_by = "O.order_idx", $sort_type = "asc")
	{

		$qry = "
			Select
			O.*, P.product_name, P.product_sale_type, PO.product_option_name
		     , M.order_matching_idx, M.product_idx, M.product_option_idx, M.product_option_cnt, M.product_option_sale_price
		     , M.order_matching_is_auto, M.matching_info_idx, M.is_gift, M.product_change_shipped
		     , Case When P.product_sale_type = 'SELF' Then convert(varchar(100), isNull(S.stock_amount_NORMAL, 0))
		            When P.product_sale_type = 'CONSIGNMENT' Then '위탁상품'
		            Else ''
              End as stock_amount_NORMAL
			, SELLER.seller_name, SP.supplier_name
			, PO.product_option_soldout, PO.product_option_soldout_temp
		    , C.code_name as order_progress_step_han
		    , M.order_cs_status
		    , (Select code_name From DY_CODE DCC WITH (NOLOCK) Where DCC.parent_code = N'ORDER_MATCHING_CS' And DCC.code = M.order_cs_status) as order_cs_status_han
			, ROW_NUMBER() OVER(PARTITION BY O.order_idx Order by M.order_matching_idx ASC) as inner_no
			From DY_ORDER O WITH (NOLOCK)
				Left Outer Join DY_ORDER_PRODUCT_MATCHING M WITH (NOLOCK) On O.order_idx = M.order_idx And M.order_matching_is_del = N'N' 
				Left Outer Join DY_PRODUCT_OPTION PO WITH (NOLOCK) On M.product_option_idx = PO.product_option_idx 
				Left Outer Join DY_PRODUCT P WITH (NOLOCK) On P.product_idx = PO.product_idx And P.product_is_del = N'N'
				Left Outer Join (
				  Select product_idx, product_option_idx
				  , Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
				  From DY_STOCK WITH (NOLOCK) 
				  Where stock_is_del = N'N' And stock_is_confirm = N'Y' And stock_status = N'NORMAL'
				  Group by product_idx, product_option_idx
				) As S On S.product_option_idx = PO.product_option_idx
				Left Outer Join DY_SELLER SELLER WITH (NOLOCK) On O.seller_idx = SELLER.seller_idx
				Left Outer Join DY_MEMBER_SUPPLIER SP WITH (NOLOCK) On SP.member_idx = P.supplier_idx
				Left Outer Join DY_CODE C WITH (NOLOCK) On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
			Where O.order_is_del = N'N'
				And O.order_pack_idx = N'$order_pack_idx'
				And 
			      (
			       O.order_progress_step = N'ORDER_COLLECT'
			       OR M.order_matching_is_del = N'N' 
			      )
			Order by $sort_by $sort_type, M.order_matching_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();


		return $_list;
	}

	/**
	 * 주문 상세 리스트
	 * 주문 IDX 로 관련 주문 내역 모두를 불러온다
	 * @param $order_pack_idx
	 * @return array
	 */
	public function getOrderDetailRelateOrderIdx($order_idx,$order_cs_status)
	{

		$qry = "
			Select
			O.*, P.product_name, P.product_sale_type, PO.product_option_name
		     , M.order_matching_idx, M.product_idx, M.product_option_idx, M.product_option_cnt, M.product_option_sale_price
		     , M.order_matching_is_auto, M.matching_info_idx
		     , Case When P.product_sale_type = 'SELF' Then convert(varchar(100), isNull(S.stock_amount_NORMAL, 0))
		            Else '위탁상품'
              End as stock_amount_NORMAL
			, SELLER.seller_name, SP.supplier_name
			, PO.product_option_soldout, PO.product_option_soldout_temp
		    , C.code_name as order_progress_step_han
		    , M.order_cs_status
		    , (Select code_name From DY_CODE DCC WITH (NOLOCK) Where DCC.parent_code = N'ORDER_MATCHING_CS' And DCC.code = M.order_cs_status) as order_cs_status_han
			, ROW_NUMBER() OVER(PARTITION BY O.order_idx Order by M.order_matching_idx ASC) as inner_no
			From DY_ORDER O WITH (NOLOCK)
				Inner Join DY_ORDER_PRODUCT_MATCHING M WITH (NOLOCK) On O.order_idx = M.order_idx
				Left Outer Join DY_PRODUCT_OPTION PO WITH (NOLOCK) On M.product_option_idx = PO.product_option_idx
				Left Outer Join DY_PRODUCT P WITH (NOLOCK) On P.product_idx = PO.product_idx
				Left Outer Join (
				  Select product_idx, product_option_idx
				  , Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
				  From DY_STOCK WITH (NOLOCK)
				  Where stock_is_del = N'N' And stock_is_confirm = N'Y' And stock_status = N'NORMAL'
				  Group by product_idx, product_option_idx
				) As S On S.product_option_idx = PO.product_option_idx
				Left Outer Join DY_SELLER SELLER WITH (NOLOCK) On O.seller_idx = SELLER.seller_idx
				Left Outer Join DY_MEMBER_SUPPLIER SP WITH (NOLOCK) On SP.member_idx = P.supplier_idx
				Left Outer Join DY_CODE C WITH (NOLOCK) On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
			Where O.order_is_del = N'N' And M.order_matching_is_del = N'N' 
				And PO.product_option_is_del = N'N' and P.product_is_del = N'N'
				And O.order_idx = N'$order_idx'
				And M.order_cs_status = N'$order_cs_status'

			Order by O.order_idx ASC, M.order_matching_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();


		return $_list;
	}

	/**
	 * CS 창 주문 상세 정보 반환 함수 1
	 * @param $order_idx
	 * @return array|false|null
	 */
	public function getOrderDetailView($order_idx)
	{
		$qry = "
			WITH CTE_DELIVERY_CODE AS (
				Select delivery_code, delivery_name
				From DY_DELIVERY_CODE
				Group by delivery_code, delivery_name
			)

			Select O.*
		    , SELLER.seller_name, SELLER.seller_type, SELLER.market_product_url
			, C.code_name as order_progress_step_han
			, CTE_D.delivery_name
			, T.tracking_url
			From DY_ORDER O 
			    Left Outer Join DY_SELLER SELLER On O.seller_idx = SELLER.seller_idx
				Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
				Left Outer Join CTE_DELIVERY_CODE CTE_D On CTE_D.delivery_code = O.delivery_code
				Left Outer Join DY_DELIVERY_TRACKING_URL T On CTE_D.delivery_code = T.delivery_code
			WHERE 
				O.order_is_del = N'N' And O.order_idx = N'$order_idx'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * CS 창 주문 상세 정보 반환 함수 2
	 * @param $order_idx
	 * @param $product_option_idx
	 * @return array|false|null
	 */
	public function getOrderDetailView2($order_idx, $product_option_idx)
	{
		$qry = "
			WITH CTE_DELIVERY_CODE AS (
				Select delivery_code, delivery_name
				From DY_DELIVERY_CODE
				Group by delivery_code, delivery_name
			)

			Select O.*
			, P.product_name, PO.product_option_name, M.product_idx, M.product_option_idx, M.product_option_cnt
		    , SELLER.seller_name, SELLER.seller_type
		    , M.order_matching_is_auto, M.product_change_shipped, M.product_option_sale_price
		    , PC.name as category_l_name
		    , PC_SUB.name as category_m_name
		    , SP.supplier_name
			, C.code_name as order_progress_step_han
			, CTE_D.delivery_name
			, CC.code_name as order_cs_status_han
			From DY_ORDER O 
			Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_PRODUCT_OPTION PO On M.product_option_idx = PO.product_option_idx
				Left Outer Join DY_PRODUCT P On P.product_idx = PO.product_idx
				Left Outer Join DY_CATEGORY PC On PC.category_idx = P.product_category_l_idx
				Left Outer Join DY_CATEGORY PC_SUB On PC_SUB.category_idx = P.product_category_m_idx
			    Left Outer Join DY_SELLER SELLER On O.seller_idx = SELLER.seller_idx
			    Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
				Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
				Left Outer Join CTE_DELIVERY_CODE CTE_D On CTE_D.delivery_code = O.delivery_code
				Left Outer Join DY_CODE CC On CC.parent_code = N'ORDER_MATCHING_CS' And CC.code = M.order_cs_status
			WHERE 
				O.order_is_del = N'N' And O.order_idx = N'$order_idx'
				And M.order_matching_is_del = N'N' And M.product_option_idx = N'$product_option_idx' 
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * CS 창 우선순위 목록
	 * @param $order_idx
	 * @param $product_option_idx
	 * @return array
	 */
	public function getOrderPriorityList($order_idx, $product_option_idx)
	{
		$qry = "
			Select 
				O.order_idx, O.invoice_priority
			    , isNull(convert(varchar(10), O.order_progress_step_accept_date, 120), '') as order_progress_step_accept_date
				, S.seller_name
				, O.receive_name
				, isNull(convert(varchar(10), O.invoice_priority_date, 120), '') as  invoice_priority_date
				, isNull(MEM.member_id, '') as member_id
			From DY_ORDER O
			Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
			Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			Left Outer Join DY_MEMBER MEM On MEM.idx = O.invoice_priority_member_idx
			Where 
			      O.order_is_del = N'N'
			      And O.order_progress_step = N'ORDER_ACCEPT'
				  And M.product_option_idx = N'$product_option_idx'
			Order by O.invoice_priority DESC, O.order_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();


		return $_list;
	}

	/**
	 * 상품 재고 얻기 - 옵션코드
	 * @param $product_option_idx
	 * @return array|false|null
	 */
	public function getStockCount($product_option_idx)
	{
		$qry = "
				WITH CTE_STOCK AS (
					Select 
						product_idx, product_option_idx
						, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
					From DY_STOCK 
					Where 
						stock_is_del = N'N' 
						And stock_is_confirm = N'Y' 
						And stock_status = N'NORMAL'
					Group by product_idx, product_option_idx
				)
				Select 
				P.product_idx, PO.product_option_idx
				, P.product_name, PO.product_option_name
				, isNull(S.stock_amount_NORMAL, 0) as stock_amount_NORMAL
				From 
					DY_PRODUCT P
					Inner Join DY_PRODUCT_OPTION PO On P.product_idx = PO.product_idx
					Left Outer Join CTE_STOCK S On S.product_option_idx = PO.product_option_idx
				Where
					P.product_is_del = N'N' And PO.product_option_is_del = N'N'
					And PO.product_option_idx = N'$product_option_idx'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * 택배사 코드 및 이름 목록 반환 함수
	 * @return array
	 */
	public function getDeliveryDistinctList()
	{
		$qry = "
			Select delivery_code, delivery_name, sort_num
				From DY_DELIVERY_CODE
				Group by delivery_code, delivery_name, sort_num
				Order by sort_num ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 주문 상세 정보 반환 - 상품정보 없음 //190904 상품정보 없어도 주문정보를 가져올 수 있도록
	 * @param $order_idx
	 * @return array|false|null
	 */
	public function getOrderDetail($order_idx)
	{
		$qry = "
			WITH CTE_DELIVERY_CODE AS (
				Select delivery_code, delivery_name
				From DY_DELIVERY_CODE
				Group by delivery_code, delivery_name
			)

			Select O.*
			, C.code_name as order_progress_step_han
			, CTE_D.delivery_name, SELLER.seller_type
			, SELLER.seller_name, M.order_matching_idx
			From DY_ORDER O 
				LEFT OUTER Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
			    Left Outer Join DY_SELLER SELLER On O.seller_idx = SELLER.seller_idx
				Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
				Left Outer Join CTE_DELIVERY_CODE CTE_D On CTE_D.delivery_code = O.delivery_code
			WHERE 
				O.order_is_del = N'N' And O.order_idx = N'$order_idx'
			Order by M.order_matching_idx ASC
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * 주문 상세 정보 반환 2
	 * @param $order_idx
	 * @return array|false|null
	 */
	public function getOrderDetail2($order_idx)
	{
		$qry = "
			WITH CTE_DELIVERY_CODE AS (
				Select delivery_code, delivery_name
				From DY_DELIVERY_CODE
				Group by delivery_code, delivery_name
			)

			Select O.*
			, M.order_cs_status
			, CC.code_name as order_cs_status_han 
			, C.code_name as order_progress_step_han
			, CTE_D.delivery_name, SELLER.seller_type
			, SELLER.seller_name
			From DY_ORDER O 
			Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
			    Left Outer Join DY_SELLER SELLER On O.seller_idx = SELLER.seller_idx
				Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = O.order_progress_step
				Left Outer Join CTE_DELIVERY_CODE CTE_D On CTE_D.delivery_code = O.delivery_code
				Left Outer Join DY_CODE CC On CC.parent_code = N'ORDER_MATCHING_CS' And CC.code = M.order_cs_status
			WHERE 
				O.order_is_del = N'N' And O.order_idx = N'$order_idx'
			Order by M.order_matching_idx ASC
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * 주문 상세 + 상품 정보
	 * @param $order_idx
	 * @param $order_matching_idx
	 * @return array|false|null
	 */
	public function getOrderProductDetail($order_idx, $order_matching_idx){
		$qry = "
			Select O.*,
			M.product_option_cnt,
			P.product_idx, P.product_name,
	        PO.product_option_idx, PO.product_option_name, M.product_option_sale_price, S.seller_type
			From DY_ORDER O 
			Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
			    Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
			    Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
				Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			WHERE 
				O.order_is_del = N'N' And O.order_idx = N'$order_idx' And M.order_matching_idx = N'$order_matching_idx'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();


		return $_view;
	}

	/**
	 * 주문 보류 상태 가져오기 (Y/N)
	 * @param $order_idx
	 * @return int|mixed
	 */
	public function getOrderHoldStatus($order_idx)
	{
		$qry = "
			Select order_is_hold
			From DY_ORDER
			Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 주문 보류 설정 하기 (합포 단위 - 하위 주문 모두 동일하게 설정 됨)
	 * @param $order_pack_idx       : 합포 IDX
	 * @param $cs_msg               : CS 메시지
	 * @param $set_alarm            : 알람 설졍 여부
	 * @param $set_alarm_datetime   : 알람 일시
	 * @return array
	 */
	public function setOrderHoldOn($order_pack_idx, $cs_msg, $set_alarm, $set_alarm_datetime)
	{
		$returnValue = array();
		$returnValue["result"] = false;
		$returnValue["msg"] = "";

		//현재 상태
		$qry = "
			Select order_is_hold, order_progress_step
			From DY_ORDER
			Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();

		//이미 보류 상태라면 return false
		if($_row["order_is_hold"] == "Y")
		{
			$returnValue["result"] = false;
			$returnValue["msg"] = "이미 보류 상태입니다.";
			return $returnValue;
		}else{
			//보류 상태가 아니라면

			//배송된 주문인지 체크
			if($_row["order_progress_step"] == "ORDER_SHIPPED"){
				$returnValue["result"] = false;
				$returnValue["msg"] = "이미 배송 되었습니다.";
				return $returnValue;
			}else {
				// 보류 설정
				$rst = $this->setOrderHold($order_pack_idx, "Y", $cs_msg, $set_alarm, $set_alarm_datetime);

				if($rst) {
					$returnValue["result"] = $rst;
				}
				return $returnValue;
			}
		}
	}

	/**
	 * 주문 보류 해제 설정 하기 (합포 단위 - 하위 주문 모두 동일하게 설정 됨)
	 * @param $order_pack_idx   : 합포 IDX
	 * @param $cs_msg           : CS 메시지
	 * @return bool
	 */
	public function setOrderHoldOff($order_pack_idx, $cs_msg)
	{
		$returnValue = false;

		//현재 상태
		$qry = "
			Select order_is_hold, order_progress_step
			From DY_ORDER
			Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();

		//이미 보류 해제 상태라면 return false
		if($_row["order_is_hold"] == "N")
		{
			$returnValue["result"] = false;
			$returnValue["msg"] = "이미 보류해제 상태입니다..";
			return $returnValue;
		}else{
			//보류 상태 라면

			// 보류 해제 설정
			$rst = $this->setOrderHold($order_pack_idx, "N", $cs_msg, null, null);

			if($rst) {
				$returnValue["result"] = $rst;
			}
			return $returnValue;
		}
	}

	/**
	 * 주문 보류 Update (합포 단위 - 하위 주문 모두 동일하게 설정 됨)
	 * @param $order_pack_idx       : 합포 IDX
	 * @param $hold_yn              : 보류 여부 (Y/N)
	 * @param $cs_msg               : CS 메세지
	 * @param $set_alarm            : 알람 설정 여부
	 * @param $set_alarm_datetime   : 알람 일시
	 * @return bool
	 */
	public function setOrderHold($order_pack_idx, $hold_yn, $cs_msg, $set_alarm = "N", $set_alarm_datetime = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//

		//주문 보류 설정 (합포 관련 주문 모두)
		$qry = "
			Update DY_ORDER
				Set order_is_hold = '$hold_yn', order_is_hold_date = getdate()
					, order_moddate = getdate(), order_modip = N'$modip', last_member_idx = N'$last_member_idx'
				Where order_pack_idx = N'$order_pack_idx'
		";
		$rst1 = parent::execSqlUpdate($qry);

		//CS 입력

		//CS 작업 상태 값 : 공통코드 (CS_JOB_TYPE) 참조
		$cs_task = ($hold_yn == "Y") ? "HOLD_ON" : "HOLD_OFF";

		$cs_idx = $this -> insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, $set_alarm, $set_alarm_datetime);

		if($cs_idx) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}
		parent::db_close();

		return $returnValue;

	}

	/**
	 * 주문 배송정보 Update 함수
	 * @param $order_pack_idx       : 합포 IDX
	 * @param $receive_name         : 수령자명
	 * @param $receive_tp_num       : 수령자 전화번호
	 * @param $receive_hp_num       : 수령자 핸드폰
	 * @param $receive_zipcode      : 수령자 우편번호
	 * @param $receive_addr1        : 수령자 주소
	 * @param $receive_memo         : 배송메시지
	 * @param $cs_msg               : CS 이력 입력 메시지
	 * @return bool
	 */
	public function updateOrderAddressIncludeRel($order_pack_idx, $receive_name, $receive_tp_num, $receive_hp_num, $receive_zipcode, $receive_addr1, $receive_memo, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//기존 정보 가져오기
		$_order = $this->getOrderDetail($order_pack_idx);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//변경 사항 정리
		$_diff_ary = array(
			"수령자" => array(
				"prev" => trim($_order["receive_name"]),
				"next" => trim($receive_name)
			),
			"전화번호" => array(
				"prev" => trim($_order["receive_tp_num"]),
				"next" => trim($receive_tp_num)
			),
			"핸드폰" => array(
				"prev" => trim($_order["receive_hp_num"]),
				"next" => trim($receive_hp_num)
			),
			"우편번호" => array(
				"prev" => trim($_order["receive_zipcode"]),
				"next" => trim($receive_zipcode)
			),
			"주소" => array(
				"prev" => trim($_order["receive_addr1"]),
				"next" => trim($receive_addr1)
			),
			"배송메모" => array(
				"prev" => trim($_order["receive_memo"]),
				"next" => trim($receive_memo)
			)
		);

		$cs_msg_diff = array();
		foreach($_diff_ary as $key => $df){
			if($df["prev"] != $df["next"]){
				$cs_msg_diff[] = $key . " 변경 : " . $df["prev"] . " -> " . $df["next"];
			}
		}

		$cs_msg_diff = "[" . implode(", ", $cs_msg_diff) . "] ";

		//변경 사항 CS message 에 추가
		$cs_msg = $cs_msg_diff . "\n" . $cs_msg;

		//Update
		$qry = "
			Update DY_ORDER
			Set 
				receive_name = N'$receive_name', 
				receive_tp_num = N'$receive_tp_num', 
				receive_hp_num = N'$receive_hp_num', 
				receive_zipcode = N'$receive_zipcode', 
				receive_addr1 = N'$receive_addr1',
				order_moddate = getdate(), order_modip = N'$modip', last_member_idx = N'$last_member_idx'
			Where order_pack_idx = N'$order_pack_idx'
		";
		$rst1 = parent::execSqlUpdate($qry);


		//CS 입력
		//CS 작업 상태 값 : 공통코드 (CS_JOB_TYPE) 참조
		$cs_task = "ADDRESS_CHANGE";
		$cs_idx = $this -> insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "");

		if($cs_idx) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}
		parent::db_close();

		return $returnValue;
	}

	/**
	 * 합포 금지 설정 변경 함수
	 * @param $order_pack_idx   : 합포IDX
	 * @param $order_is_lock    : 합포금지 설정 변수 (Y/N)
	 * @param $cs_msg           : CS 이력 입력 메시지
	 * @return bool
	 */
	public function updateOrderPackageLock($order_pack_idx, $order_is_lock, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//Update
		$qry = "
			Update DY_ORDER
			Set
				order_is_lock = N'$order_is_lock'
				, order_is_lock_date = getdate()
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
				order_pack_idx = '$order_pack_idx'
		";

		$rst1 = parent::execSqlUpdate($qry);

		//CS 입력
		//CS 작업 상태 값 : 공통코드 (CS_JOB_TYPE) 참조
		if($order_is_lock == "Y") {
			$cs_task = "PACKAGE_LOCK";
		}else{
			$cs_task = "PACKAGE_UNLOCK";
		}
		$cs_idx = $this -> insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "");

		if($cs_idx) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}
		parent::db_close();


		return $returnValue;
	}

	/**
	 * 주문 전체 취소
	 * 접수, 배송 주문만 취소 가능
	 * 송장 상태의 주문은 취소 불가
	 * @param $order_pack_idx    : 합포 IDX
	 * @param $cs_reason_code1   : CS 사유 코드1
	 * @param $cs_reason_code2   : CS 사유 코드2
	 * @param $cs_msg            : CS 이력 입력 메시지
	 * @return bool
	 */
	public function updateOrderCancelAll($order_pack_idx, $cs_reason_code1, $cs_reason_code2, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//취소되지 않은 주문 불러오기
		$qry = "
			Select O.order_idx, O.order_progress_step, OPM.order_matching_idx,  OPM.product_idx, OPM.product_option_idx, OPM.product_option_cnt, P.product_sale_type 
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
				Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
			Where 
				O.order_is_del = N'N' 
			    And OPM.order_cs_status != 'ORDER_CANCEL'
				And O.order_pack_idx = N'$order_pack_idx'
		";

		$_opm_list = parent::execSqlList($qry);

		//각 주문 취소
		foreach($_opm_list as $ord){

			$order_idx = $ord["order_idx"];
			$product_idx = $ord["product_idx"];
			$product_option_idx = $ord["product_option_idx"];
			$product_option_cnt = $ord["product_option_cnt"];
			$order_matching_idx = $ord["order_matching_idx"];
			$product_cancel_shipped = '';

			//배송 후 취소 인지 체크
			if($_pack_order_progress_step == "ORDER_SHIPPED"){
				$product_cancel_shipped = 'Y';
			}else{
				$product_cancel_shipped = 'N';
			}

			//주문 취소 Update (CS)
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
				Set 
				    order_cs_status = N'ORDER_CANCEL'
				    , product_cancel_shipped = N'$product_cancel_shipped'
				    , product_cancel_date = getdate()
				    , cs_reason_code1 = N'$cs_reason_code1'
				    , cs_reason_code2 = N'$cs_reason_code2'
					, order_matching_moddate = getdate()
					, order_matching_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where order_matching_idx = N'$order_matching_idx'
			";
			$tmp = parent::execSqlUpdate($qry);

			//정산테이블 입력
			$tmp = $this->insertSettleCancel($order_matching_idx, $cs_reason_code2);

			//CS 입력
			$cs_task = "ORDER_CANCEL_ALL";    //전체취소
			$this->insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, 'Y', '', $cs_task, $cs_msg, '', '', null, false, $cs_reason_code1, $cs_reason_code2);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 주문 개별 취소 (order_idx 로)
	 * 접수, 배송 주문만 취소 가능
	 * 송장 상태의 주문은 취소 불가
	 *
	 * 19.03.19 수정 - woox
	 * 판매처 취소도 이 함수를 통해 취소한다
	 * $is_seller_cancel : 판매처 취소 여부 (값이 "Y" 면 판매처 취소)
	 * $cancel_reason : 사유
	 * $cancel_date : 취소요청일 nvarchar(50)
	 * $return_invoice_no : 반품송장번호
	 *
	 * @param $order_idx    : order_idx
	 * @param $cs_reason_code1   : CS 사유 코드1
	 * @param $cs_reason_code2   : CS 사유 코드2
	 * @param $cs_msg            : CS 이력 입력 메시지
	 * @return bool
	 */
	public function updateOrderCancelOneByOrderIdx($order_idx, $cs_reason_code1, $cs_reason_code2, $cs_msg, $is_seller_cancel = "", $cancel_reason = "", $cancel_date = "", $return_invoice_no = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_pack_idx, order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();
		$_pack_order_progress_step = $_row["order_progress_step"];
		$order_pack_idx = $_row["order_pack_idx"];

		if(!$_row || $_pack_order_progress_step == "ORDER_INVOICE" || $_pack_order_progress_step == "ORDER_SHIPPED"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//취소되지 않은 주문 불러오기
		$qry = "
			Select O.order_idx, O.order_progress_step, OPM.order_matching_idx,  OPM.product_idx, OPM.product_option_idx, OPM.product_option_cnt, P.product_sale_type 
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
				Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
			Where 
				O.order_is_del = N'N' 
			    And OPM.order_cs_status != 'ORDER_CANCEL'
				And O.order_idx = N'$order_idx'
		";

		$_opm_list = parent::execSqlList($qry);

		//각 주문 취소 후 사입/자체 상품 일 경우 입고예정 처리
		foreach($_opm_list as $ord){

			$order_idx = $ord["order_idx"];
			$product_idx = $ord["product_idx"];
			$product_option_idx = $ord["product_option_idx"];
			$product_option_cnt = $ord["product_option_cnt"];
			$order_matching_idx = $ord["order_matching_idx"];
			$product_cancel_shipped = "";

			//배송 후 취소 인지 체크
			if($_pack_order_progress_step == "ORDER_SHIPPED"){
				$product_cancel_shipped = 'Y';
			}else{
				$product_cancel_shipped = 'N';
			}

			//주문 취소 Update (CS)
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
				Set 
				    order_cs_status = N'ORDER_CANCEL'
				    , product_cancel_shipped = N'$product_cancel_shipped'
				    , cs_reason_code1 = N'$cs_reason_code1'
				    , cs_reason_code2 = N'$cs_reason_code2'
					, order_matching_moddate = getdate()
					, order_matching_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where order_matching_idx = N'$order_matching_idx'
			";
			$tmp = parent::execSqlUpdate($qry);

			//정산 테이블 입력
			$tmp = $this->insertSettleCancel($order_matching_idx, $cs_reason_code2);

			//CS 입력
			$cs_task = "ORDER_CANCEL_ONE";    //개별취소
			$this->insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, 'Y', '', $cs_task, $cs_msg, '', '', null, false, $cs_reason_code1, $cs_reason_code2);
		}

		//판매처 취소 내용 Update
		if($is_seller_cancel == "Y")
		{
			$qry = "
				Update DY_ORDER
				Set 
				    order_is_seller_cancel = N'Y'
					, order_seller_cancel_date = getdate()
					, order_seller_cancel_confirm_member_idx = N'$last_member_idx'
					, order_seller_cancel_reqdate = N'$cancel_date'
					, order_seller_cancel_reason = N'$cancel_reason'
					, order_seller_cancel_return_invoice_no = N'$return_invoice_no'
				Where order_idx = N'$order_idx'
			";

			$tmp = parent::execSqlUpdate($qry);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * !!주문 전체 정상복귀
	 * 접수, 배송 주문만 정상복귀 가능
	 * 송장 상태의 주문은 정상복귀 불가
	 * @param $order_pack_idx
	 * @param $cs_msg
	 * @return bool
	 */
	public function updateOrderRestoreAll($order_pack_idx, $cs_msg){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//주문 불러오기
		//취소된 주문 만
		$qry = "
			Select O.order_idx, O.order_progress_step
			     , OPM.order_matching_idx,  OPM.product_idx, OPM.product_option_idx
			     , OPM.product_option_cnt, P.product_sale_type
				 , OPM.order_cs_status, OPM.product_cancel_shipped
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
				Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
			Where 
				O.order_is_del = N'N' 
			    And OPM.order_cs_status = 'ORDER_CANCEL'
				And O.order_pack_idx = N'$order_pack_idx'
		";

		$_opm_list = parent::execSqlList($qry);

		foreach($_opm_list as $ord) {

			$order_idx              = $ord["order_idx"];
			$product_idx            = $ord["product_idx"];
			$product_option_idx     = $ord["product_option_idx"];
			$product_option_cnt     = $ord["product_option_cnt"];
			$order_matching_idx     = $ord["order_matching_idx"];
			$order_cs_status        = $ord["order_cs_status"];
			$product_sale_type      = $ord["product_sale_type"];
			$product_cancel_shipped = $ord["product_cancel_shipped"];

			//주문 정상 복귀 ~ Update (CS)
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
				Set 
				    order_cs_status = N'NORMAL'
					, order_matching_moddate = getdate()
					, order_matching_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where order_matching_idx = N'$order_matching_idx'
			";
			$tmp = parent::execSqlUpdate($qry);


			//자체 상품일 경우 배송후 취소 일 경우에만 정산 입력
			//위탁 상품의 경우 무조건 정산 입력
			$isSettleIn = false;
			if($product_sale_type == "SELF"){
				if($product_cancel_shipped == "Y"){
					$isSettleIn = true;
				}
			}elseif($product_sale_type == "CONSIGNMENT"){
				$isSettleIn = true;
			}

			//정산 입력!!
			if($isSettleIn){
				//정산데이터 불러오기
				$qry = "
					Select
					O.*
			        , SELLER.seller_type, SELLER.vendor_use_charge
				    , Case When SELLER.seller_type = 'VENDOR_SELLER' THEN 
				        (Select vendor_grade From DY_MEMBER_VENDOR VENDOR Where VENDOR.member_idx = O.seller_idx)
				      Else '' End as vendor_grade
				    , M.order_matching_idx
					, M.product_option_cnt,  M.product_option_sale_price , M.product_cancel_shipped, M.product_change_shipped
				    , M.product_option_purchase_price, M.product_option_sale_price, M.order_cs_status, M.product_calculation_amt
					, isNull(S.stock_idx, 0) as stock_idx, isNull(S.stock_unit_price, 0) as stock_unit_price, S.stock_amount
				    , P.product_tax_type, P.product_delivery_fee_sale, P.product_delivery_fee_buy, P.product_sale_type
					, P.product_name, PO.product_option_name, P.product_idx, PO.product_option_idx
				    , P.product_category_l_idx, P.product_category_m_idx
					, PO.product_option_sale_price_A, PO.product_option_sale_price_B, PO.product_option_sale_price_C, PO.product_option_sale_price_D, PO.product_option_sale_price_E
					, SUPPLIER.member_idx as supplier_idx, SUPPLIER.supplier_use_prepay
					From DY_ORDER O
						Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
						Left Outer Join DY_STOCK S 
						  On S.order_idx = O.order_idx 
						       And S.product_option_idx = M.product_option_idx 
						       And S.stock_status = N'SHIPPED' 
						       And S.stock_type = 1 
						       And S.stock_amount > 0
					    Inner Join DY_SELLER SELLER On SELLER.seller_idx = O.seller_idx
						Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
					    Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = P.supplier_idx
						Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
					WHERE 
						O.order_is_del = N'N'
						And M.order_matching_is_del='N' 
					    And M.order_cs_status = N'NORMAL'
						And (
							S.order_idx is null
							Or (S.stock_is_del = N'N' And S.stock_is_confirm = N'Y')
						)
						And M.order_matching_idx = N'$order_matching_idx'
		
					Order by O.order_pack_idx ASC, O.order_idx ASC, M.order_matching_idx ASC
				";

				$_order_list = parent::execSqlList($qry);

				if($_order_list){
					$prev_order_idx = 0;
					$prev_order_pack_idx = 0;
					foreach ($_order_list as $ord){

						$settle_type = "SHIPPED";   //정산 타입 - 배송

						$order_idx                       = $ord["order_idx"];                               //관리번호
						$order_pack_idx                  = $ord["order_pack_idx"];                          //합포번호
						$order_cs_status                 = $ord["order_cs_status"];                         //CS상태
						$order_progress_step_accept_date = $ord["order_progress_step_accept_date"];         //발주일 [접수일]
						$settle_date                     = date('Y-m-d');                           //발주일 Y-m-d
						$seller_idx                      = $ord["seller_idx"];                              //판매처
						$seller_type                     = $ord["seller_type"];                             //판매처 타입
						$vendor_grade                    = $ord["vendor_grade"];                            //벤더사 등급
						$supplier_idx                    = $ord["supplier_idx"];                            //공급처
						$vendor_use_charge               = $ord["vendor_use_charge"];                       //벤더사 판매처 충전금 사용 여부 (Y/N)
						$supplier_use_prepay             = $ord["supplier_use_prepay"];                     //공급처 선급금 사용 여부 (Y/N)
						$market_order_no                 = $ord["market_order_no"];                         //마켓 주문번호
						$market_product_no               = $ord["market_product_no"];                       //마켓 상품 번호
						$market_product_name             = $ord["market_product_name"];                     //마켓 상품 명
						$market_product_option           = $ord["market_product_option"];                   //마켓 옵션 명
						$order_unit_price                = $ord["order_unit_price"];                        //판매단가
						$order_amt                       = $ord["order_amt"];                               //판매가
						$order_cnt                       = $ord["order_cnt"];                               //판매수량
						$delivery_fee                    = $ord["delivery_fee"];                            //배송비
						$delivery_type                   = $ord["delivery_type"];                           //배송비 정산구분 (선불/착불/선결제 등)
						$delivery_is_free                = $ord["delivery_is_free"];                        //배송비 정산구분 (선불:Y/착불:N)
						$product_idx                     = $ord["product_idx"];                             //상품코드
						$product_name                    = $ord["product_name"];                            //상품명
						$product_option_idx              = $ord["product_option_idx"];                      //옵션코드
						$product_option_name             = $ord["product_option_name"];                     //옵션명
						$product_option_cnt              = $ord["product_option_cnt"];                      //상품수량
						$product_tax_type                = $ord["product_tax_type"];                        //상품 세금 종류
						$product_tax_ratio               = ($product_tax_type == "TAXATION") ? 10 : 0;      //상품 대상세율 [과세 일 경우에만 10%]
						$product_sale_type               = $ord["product_sale_type"];                       //상품 판매 방식 (사입/위탁)
						$stock_idx                       = $ord["stock_idx"];                               //연결된 재고 코드
						$stock_unit_price                = $ord["stock_unit_price"];                        //연결된 재고 가격
						$stock_amount                    = $ord["stock_amount"];                            //연결된 재고 수량 [원가가 다른 재고가 한주문에 연결될 수 있음]
						$product_option_purchase_price   = $ord["product_option_purchase_price"];           //옵션 매입단가 - DY
						$product_option_sale_price       = $ord["product_option_sale_price"];               //판매단가 2 - DY 기준
						$product_delivery_fee_sale       = $ord["product_delivery_fee_sale"];               //상품 매입배송비
						$product_delivery_fee_buy        = $ord["product_delivery_fee_buy"];                //상품 매출배송비
						$product_calculation_amt         = $ord["product_calculation_amt"];                 //상품 정산예정금액

						$product_category_l_idx          = $ord["product_category_l_idx"];
						$product_category_m_idx          = $ord["product_category_m_idx"];

						$invoice_date                    = $ord["invoice_date"];
						$shipping_date                   = $ord["shipping_date"];
						$cancel_date                     = $ord["invoice_date"];

						$order_matching_idx              = $ord["order_matching_idx"];

						$settle_sale_supply                = 0;     //매출공급가액
						$settle_sale_supply_ex_vat         = 0;     //매출공급가액 부가세제외
						$settle_sale_commission_ex_vat     = 0;     //판매수수료 부가세 별도
						$settle_sale_commission_in_vat     = 0;     //판매수수료 부가세 포함
						$settle_delivery_in_vat            = 0;     //배송비 부가세 포함
						$settle_delivery_ex_vat            = 0;     //배송비 부가세 별도
						$settle_delivery_commission_ex_vat = 0;     //판매배송비 수수료 부가세 별도
						$settle_delivery_commission_in_vat = 0;     //판매배송비 수수료 부가세 포함
						$settle_purchase_supply            = 0;     //매입가(매출원가) 공급가액
						$settle_purchase_supply_ex_vat     = 0;     //매입가(매출원가) 공급가액 부가세 별도
						$settle_purchase_delivery_in_vat   = 0;     //매입 배송비 부가세 포함
						$settle_purchase_delivery_ex_vat   = 0;     //매입 배송비 부가세 별도
						$settle_sale_profit                = 0;     //매출 이익
						$settle_sale_amount                = 0;     //매출액
						$settle_sale_cost                  = 0;     //매출원가

						$settle_sale_sum                   = 0;     //매출합계
						$settle_purchase_sum               = 0;     //매입합계

						$commission = 0;            //수수료 (마켓)
						$delivery_commission = 0;   //배송비 수수료 (마켓)
						if($seller_type == "MARKET_SELLER" || $seller_type == "CUSTOM_SELLER"){
							$_market_commission = $this->getSettleCommission($seller_idx, $product_idx, $product_option_idx);
							$commission = $_market_commission["market_commission"];            //수수료 (마켓)
							$delivery_commission = $_market_commission["delivery_commission"];   //배송비 수수료 (마켓)
						}

						//벤더사 판매처라면 판매가 및 판매단가는 매칭 상품의 판매단가로 대체
						if($seller_type == "VENDOR_SELLER"){
							$order_unit_price = $product_option_sale_price;

							//변경!! 주문금액은 배송비를 포함하지 않음
							//$order_amt = ($order_unit_price * $product_option_cnt) + $delivery_fee;
							$order_amt = ($order_unit_price * $product_option_cnt);

							//벤더사 일 경우 매출배송비는 상품에 기록된 금액으로 대체
							$delivery_fee = $product_delivery_fee_sale;
						}else{

							//판매단가 = (판매금액 - 배송비) / 수량
							//소수점 버림
							//변경!! 주문금액은 배송비를 포함하지 않음
							//$order_unit_price = $product_option_sale_price = floor(($order_amt - $delivery_fee) / $product_option_cnt);
							//매칭 판매 단가는 매칭 시에 설정되므로 따로 수정하지 않는다.
							//$order_unit_price = $product_option_sale_price = floor($order_amt / $product_option_cnt);
							//$order_unit_price = floor($order_amt / $product_option_cnt);
							//매칭 시 주문 금액으로 개별 판매단가를 저장 했으므로 해당 금액을 불러옴 - 19.04.25
							$order_unit_price = $product_option_sale_price;

							//벤더사 판매처가 아니라면
							//두번째 매칭 상품 부터는 판매단가 및 판매가 공란
							//
							//같은 주문에 여러의 상품이 매칭된 경우
							//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
							if($prev_order_idx == $order_idx) {
								$order_unit_price = 0;
								$order_amt        = 0;
							}
						}

						//자체 상품이면 매입단가를 재고매입단가로 Update
						//재고 수량으로 대체
						if($product_sale_type == "SELF")
						{
							$product_option_purchase_price = $stock_unit_price;
							//취소or복귀 시에는 재고 수량을 따르지 않는다 - 19.04.25
							//$product_option_cnt = $stock_amount;
						}

						//관리번호와 합포번호가 다르면 - 합포된 주문
						if($prev_order_pack_idx == $order_pack_idx || $order_idx != $order_pack_idx){
							$delivery_fee = 0;  //배송비
							$delivery_purchase_fee = 0; //매입배송비
						}

						//같은 주문에 여러의 상품이 매칭된 경우
						//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
						//위 두 경우는 하나의 주문이라도 정산 테이블에 여러번 입력 된다.
						//두번째 입력 될 때부터는....
						if($prev_order_idx == $order_idx){
							$delivery_fee = 0;  //배송비
							$product_delivery_fee_buy = 0; //매입배송비
						}

						//매출공급가액 - 변경!! 주문금액은 배송비를 포함하지 않음
						//$settle_sale_supply = $order_amt - $delivery_fee;
						$settle_sale_supply = $order_amt;
						//매출공급가액 부가세 제외금액
						$settle_sale_supply_ex_vat = round($settle_sale_supply / (($product_tax_ratio/100) + 1));

						//!!!!19.04.25 정산예정금 관련 내용 추가!!!!
						//20.02.26 변경 수수료 ex 스왑
						//정산예정금액이 있으면 정산예정금액을 제외한 나머지 금액이 판매수수료가 됨
						//없으면 기존 대로 수수료관리에 등록된 내용으로 계산
						if($product_calculation_amt > 0){
							//정산예정금이 있다면
							//판매수수료 부가세포함 [매출공급가액 - 정산예정금(상품 정산예정금 * 상품 개수)]
							$settle_sale_commission_in_vat = $settle_sale_supply - ($product_calculation_amt * $product_option_cnt);
							//판매수수료 부가세별도 [판매수수료 부가세별도 / 1.1]
							$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
						}else {
							//수수료가 있다면
							//판매수수료 부가세포함 [매출공급가액 * 판매수수료]
							$settle_sale_commission_in_vat = round($settle_sale_supply * ($commission / 100));
							//판매수수료 부가세별도 [판매수수료 부가세별도 * 1.1]
							$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
						}

						//배송비 부가세포함
						$settle_delivery_in_vat = $delivery_fee;
						//배송비 부가세별도
						$settle_delivery_ex_vat = round($settle_delivery_in_vat / 1.1);
						//20.02.26 변경 판매배송비수수료 ex 스왑
						//판매배송비수수료 부가세포함 [배송비 부가세포함 - 배송비수수료]
						$settle_delivery_commission_in_vat = round($settle_delivery_in_vat * ($delivery_commission/100));
						//판매배송비수수료 부가세별도
						$settle_delivery_commission_ex_vat = round($settle_delivery_commission_in_vat / 1.1);
						//매입단가(매출원가) 공급가액
						$settle_purchase_unit_supply = $product_option_purchase_price;
						//매입단가(매출원가) 공급가액 부가세 별도 [매입단가(매출원가) 공급가액 / 1.1]
						$settle_purchase_unit_supply_ex_vat = round($product_option_purchase_price / (($product_tax_ratio/100) + 1));
						//매입가(매출원가) 공급가액
						$settle_purchase_supply = $product_option_purchase_price * $product_option_cnt;
						//매입가(매출원가) 공급가액 부가세 별도 [매입가(매출원가) 공급가액 - 상품 대상세금]
						$settle_purchase_supply_ex_vat = round($settle_purchase_supply / (($product_tax_ratio/100) + 1));
						//매입 배송비 부가세 포함
						$settle_purchase_delivery_in_vat = $product_delivery_fee_sale;
						//매입 배송비 부가세 별도 [매입 배송비 부가세 포함 / 1.1];
						$settle_purchase_delivery_ex_vat   = round($settle_purchase_delivery_in_vat / 1.1);

						//매출이익 = 매출공급가액[부X] - 수수료[판매수수료 부X] + 배송비[부X] - 배송비 수수료[판매배송비수수료 부X] - 매입가[부X] - 매입배송비[부X]
						$settle_sale_profit = $settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat - $settle_purchase_supply_ex_vat - $settle_purchase_delivery_ex_vat;

						//매출액 = 매출공급가액 + 배송비 - 판매수수료[부X] - 판매배송비수수료[부X]
						$settle_sale_amount = $settle_sale_supply + $settle_delivery_in_vat - $settle_sale_commission_ex_vat - $settle_delivery_commission_ex_vat;

						//매출원가 = 매입가(매출원가) 공급가액[부X] + 매입배송비[부X]
						$settle_sale_cost = $settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat;

						//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
						$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
						//매입합계 (매입가 + 매입배송비)
						$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

						$qry = "
							Insert Into DY_SETTLE
							(
								settle_date, settle_type, order_idx, order_pack_idx, order_cs_status, order_progress_step_accept_date
								, seller_idx, supplier_idx, vendor_grade, vendor_use_charge, supplier_use_prepay
								, market_order_no, market_product_no, market_product_name, market_product_option
								, order_unit_price, order_amt, order_cnt
								, commission, delivery_commisision
								, delivery_fee, delivery_type, delivery_is_free
								, order_matching_idx
								, product_idx, product_name, product_option_idx, product_option_name, product_option_cnt, product_sale_type, product_tax_type
								, product_option_sale_price, product_option_purchase_price, product_delivery_fee_sale, product_delivery_fee_buy
								, product_category_l_idx, product_category_m_idx
								, stock_idx
								, settle_sale_supply, settle_sale_supply_ex_vat, settle_sale_commission_ex_vat, settle_sale_commission_in_vat
								, settle_delivery_in_vat, settle_delivery_ex_vat, settle_delivery_commission_ex_vat, settle_delivery_commission_in_vat
								, settle_purchase_supply, settle_purchase_supply_ex_vat, settle_purchase_delivery_in_vat, settle_purchase_delivery_ex_vat
								, settle_sale_profit, settle_sale_amount, settle_sale_cost
								, settle_purchase_unit_supply, settle_purchase_unit_supply_ex_vat
								, settle_sale_sum, settle_purchase_sum
								, settle_regip, last_member_idx
							) 
							VALUES 
							(
							 N'$settle_date'
							 , N'$settle_type'
							 , N'$order_idx'
							 , N'$order_pack_idx'
							 , N'$order_cs_status'
							 , N'$order_progress_step_accept_date'
							 , N'$seller_idx'
							 , N'$supplier_idx'
							 , N'$vendor_grade'
							 , N'$vendor_use_charge'
							 , N'$supplier_use_prepay'
							 , N'$market_order_no'
							 , N'$market_product_no'
							 , N'$market_product_name'
							 , N'$market_product_option'
							 , N'$order_unit_price'
							 , N'$order_amt'
							 , N'$product_option_cnt'
							 , N'$commission'
							 , N'$delivery_commission'
							 , N'$delivery_fee'
							 , N'$delivery_type'
							 , N'$delivery_is_free'
							 , N'$order_matching_idx'
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
							 , N'$product_category_l_idx'
							 , N'$product_category_m_idx'
							 , N'$stock_idx'
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
							 , N'$modip'
							 , N'$last_member_idx'
							)
						";

						$inserted_idx = parent::execSqlInsert($qry);

						$prev_order_idx = $order_idx;
						$prev_order_pack_idx = $order_pack_idx;
					}
				}
			}

			//CS 입력
			$cs_task = "ORDER_RESTORE_ALL";    //전체정상복귀
			$this->insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, 'Y', '', $cs_task, $cs_msg, '', '', null, false, "", "");
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 개별 취소 처리
	 * @param $order_pack_idx
	 * @param $except_list      : 취소 리스트 (배열)
	 * @param $cs_reason_code1
	 * @param $cs_reason_code2
	 * @param $cs_msg
	 * @return bool
	 */
	public function updateOrderCancelOne($order_pack_idx, $except_list, $cs_reason_code1, $cs_reason_code2, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];


		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE"){
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		foreach($except_list as $except_one){

			$__order_matching_idx = $except_one["order_matching_idx"];
			$__order_pack_idx     = $except_one["order_pack_idx"];
			$__order_idx          = $except_one["order_idx"];
			$__product_option_idx = $except_one["product_option_idx"];
			$__product_option_cnt = $except_one["product_option_cnt"];

			//배송 후 취소 인지 체크
			if($_pack_order_progress_step == "ORDER_SHIPPED"){
				$product_cancel_shipped = 'Y';
			}else{
				$product_cancel_shipped = 'N';
			}

			//기존 주문에서 분리 가능 한 수량인지 체크
			$qry = "
				Select product_option_cnt as cnt
				From DY_ORDER D 
				    Inner Join DY_ORDER_PRODUCT_MATCHING PM On D.order_idx = PM.order_idx
				Where
					D.order_is_del = N'N'
					And PM.order_matching_is_del = N'N'
					And D.order_idx = N'$__order_idx'
					And PM.product_option_idx = N'$__product_option_idx'
					And PM.order_matching_idx = N'$__order_matching_idx'
			";
			$remain_cnt = parent::execSqlOneCol($qry);

			if($remain_cnt >= $__product_option_cnt) {

				//전체 수량 개별취소 라면 단순 값 변경
				if($remain_cnt == $__product_option_cnt){

					//전체 취소 처리
					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set 
						    order_cs_status = N'ORDER_CANCEL'
						    , product_cancel_shipped = N'$product_cancel_shipped'
						    , product_cancel_date = getdate()
						    , cs_reason_code1 = N'$cs_reason_code1'
						    , cs_reason_code2 = N'$cs_reason_code2'
							, order_matching_moddate = getdate()
							, order_matching_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp = parent::execSqlUpdate($qry);

					//정산테이블 입력
					$tmp = $this->insertSettleCancel($__order_matching_idx, $cs_reason_code2);

				}else{

					//부분 취소라면

					//부분 취소 입력
					$qry = "
						Insert Into DY_ORDER_PRODUCT_MATCHING
						(
						 order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt, order_matching_is_auto
						, product_option_sale_price, product_option_purchase_price, product_calculation_amt
						, order_cs_status, product_cancel_shipped
						, order_matching_regdate, order_matching_regip, last_member_idx
						)
						SELECT 
						order_idx, seller_idx, product_idx, product_option_idx, N'$__product_option_cnt', N'N'
					    , product_option_sale_price, product_option_purchase_price, product_calculation_amt
						, N'ORDER_CANCEL', N'$product_cancel_shipped'
						, getdate(), N'$modip', N'$last_member_idx'
						From DY_ORDER_PRODUCT_MATCHING
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp_insert = parent::execSqlInsert($qry);

					//재고 분리 200917 by kyu
					//재고 분리가 되지않으면 회수 요청이 정상적으로 진행되지 않음
					$qry = "SELECT * FROM DY_STOCK WHERE order_matching_idx = N'$__order_matching_idx'";

					$stock_data = $this->execSqlList($qry);
					if (count($stock_data) != 0) {
						foreach ($stock_data as $stock_datum) {
							// 기존 재고 데이터 업데이트
							$stock_datum["stock_amount"] = $remain_cnt - $__product_option_cnt;
							$this->insertFromArray($stock_datum, "DY_STOCK", "stock_idx", false);

							// 재고 추가
							unset($stock_datum["stock_idx"]);
							$stock_datum["order_matching_idx"] = $tmp_insert;
							$stock_datum["stock_amount"] = $__product_option_cnt;
							$this->insertFromArray($stock_datum, "DY_STOCK", "", false);
						}
					}

					//정산테이블 입력
					$tmp = $this->insertSettleCancel($tmp_insert, $cs_reason_code2);

					//기존 주문 수량 Update
					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set product_option_cnt = product_option_cnt - ".$__product_option_cnt."
							, order_matching_moddate = getdate()
							, order_matching_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp = parent::execSqlUpdate($qry);
				}

				//CS 입력
				$cs_task = "ORDER_CANCEL_ONE";    //전체취소처리
				$cs_msg_prefix = "상품옵션코드 : " . $__product_option_idx . "\n";
				$cs_msg_prefix .= "수량 : " . $__product_option_cnt . "\n";
				$this->insertCS($__order_idx, $order_pack_idx, $tmp_insert, 0, $__product_option_idx, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, $cs_reason_code1, $cs_reason_code2);

			}else{
				parent::sqlTransactionRollback();     //트랜잭션 롤백
				$returnValue = false;
				return $returnValue;
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 개별 정상복귀
	 * @param $order_pack_idx
	 * @param $except_list
	 * @param $cs_msg
	 * @return bool
	 */
	public function updateOrderRestoreOne($order_pack_idx, $except_list, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		foreach($except_list as $except_one){

			$__order_matching_idx = $except_one["order_matching_idx"];
			$__order_pack_idx     = $except_one["order_pack_idx"];
			$__order_idx          = $except_one["order_idx"];
			$__product_option_idx = $except_one["product_option_idx"];
			$__product_option_cnt = $except_one["product_option_cnt"];

			//기존 주문에서 분리 가능 한 수량인지 체크
			$qry = "
				Select product_option_cnt as cnt
				From DY_ORDER D 
				    Inner Join DY_ORDER_PRODUCT_MATCHING PM On D.order_idx = PM.order_idx
				Where
					D.order_is_del = N'N'
					And PM.order_matching_is_del = N'N'
					And D.order_idx = N'$__order_idx'
					And PM.product_option_idx = N'$__product_option_idx'
					And PM.order_matching_idx = N'$__order_matching_idx'
			";
			$remain_cnt = parent::execSqlOneCol($qry);

			if($remain_cnt >= $__product_option_cnt) {

				//전체 수량 개별정상복귀 라면 단순 값 변경
				if($remain_cnt == $__product_option_cnt){

					//전체 개별정상복귀 처리
					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set 
						    order_cs_status = N'NORMAL'
							, order_matching_moddate = getdate()
							, order_matching_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp = parent::execSqlUpdate($qry);

					//정산 입력을 위한 order_matching_idx
					$forSettle_order_matching_idx = $__order_matching_idx;

				}else{

					//부분 정상복귀라면

					//부분 정상복귀 입력
					$qry = "
						Insert Into DY_ORDER_PRODUCT_MATCHING
						(
						 order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt, order_matching_is_auto
						, order_cs_status
						, order_matching_regdate, order_matching_regip, last_member_idx
						)
						SELECT 
						order_idx, seller_idx, product_idx, product_option_idx, N'$__product_option_cnt', N'N'
						, N'NORMAL'
						, getdate(), N'$modip', N'$last_member_idx'
						From DY_ORDER_PRODUCT_MATCHING
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp_insert = parent::execSqlInsert($qry);

					//정산 입력을 위한 order_matching_idx
					$forSettle_order_matching_idx = $tmp_insert;

					//기존 취소 주문 수량 Update
					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set product_option_cnt = product_option_cnt - ".$__product_option_cnt."
							, order_matching_moddate = getdate()
							, order_matching_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where order_matching_idx = N'$__order_matching_idx'
					";
					$tmp = parent::execSqlUpdate($qry);
				}

				//정산데이터 불러오기
				$qry = "
					Select
					O.*
			        , SELLER.seller_type, SELLER.vendor_use_charge
				    , Case When SELLER.seller_type = 'VENDOR_SELLER' THEN 
				        (Select vendor_grade From DY_MEMBER_VENDOR VENDOR Where VENDOR.member_idx = O.seller_idx)
				      Else '' End as vendor_grade
				    , M.order_matching_idx
					, M.product_option_cnt,  M.product_option_sale_price , M.product_cancel_shipped, M.product_change_shipped
				    , M.product_option_purchase_price, M.product_option_sale_price, M.order_cs_status, M.product_calculation_amt
					, isNull(S.stock_idx, 0) as stock_idx, isNull(S.stock_unit_price, 0) as stock_unit_price, S.stock_amount
				    , P.product_tax_type, P.product_delivery_fee_sale, P.product_delivery_fee_buy, P.product_sale_type
					, P.product_name, PO.product_option_name, P.product_idx, PO.product_option_idx
				    , P.product_category_l_idx, P.product_category_m_idx
					, PO.product_option_sale_price_A, PO.product_option_sale_price_B, PO.product_option_sale_price_C, PO.product_option_sale_price_D, PO.product_option_sale_price_E
					, SUPPLIER.member_idx as supplier_idx, SUPPLIER.supplier_use_prepay
					From DY_ORDER O
						Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
						Left Outer Join DY_STOCK S 
						  On S.order_idx = O.order_idx 
						       And S.product_option_idx = M.product_option_idx 
						       And S.stock_status = N'SHIPPED' 
						       And S.stock_type = 1 
						       And S.stock_amount > 0
					    Inner Join DY_SELLER SELLER On SELLER.seller_idx = O.seller_idx
						Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
					    Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = P.supplier_idx
						Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
					WHERE 
						O.order_is_del = N'N'
						And M.order_matching_is_del='N' 
					    And M.order_cs_status = N'NORMAL'
						And (
							S.order_idx is null
							Or (S.stock_is_del = N'N' And S.stock_is_confirm = N'Y')
						)
						And M.order_matching_idx = N'$forSettle_order_matching_idx'
		
					Order by O.order_pack_idx ASC, O.order_idx ASC, M.order_matching_idx ASC
				";

				$_order_list = parent::execSqlList($qry);
				if($_order_list){
					$prev_order_idx = 0;
					$prev_order_pack_idx = 0;
					foreach ($_order_list as $ord){

                        $product_cancel_shipped         = $ord["product_cancel_shipped"];                   //주문취소의 배송 후/전 여부 (Y/N)

						$settle_type = "SHIPPED";   //정산 타입 - 배송

						$order_idx                       = $ord["order_idx"];                               //관리번호
						$order_pack_idx                  = $ord["order_pack_idx"];                          //합포번호
						$order_cs_status                 = $ord["order_cs_status"];                         //CS상태
						$order_progress_step_accept_date = $ord["order_progress_step_accept_date"];         //발주일 [접수일]
						$settle_date                     = date('Y-m-d');                           //발주일 Y-m-d
						$seller_idx                      = $ord["seller_idx"];                              //판매처
						$seller_type                     = $ord["seller_type"];                             //판매처 타입
						$vendor_grade                    = $ord["vendor_grade"];                            //벤더사 등급
						$supplier_idx                    = $ord["supplier_idx"];                            //공급처
						$vendor_use_charge               = $ord["vendor_use_charge"];                       //벤더사 판매처 충전금 사용 여부 (Y/N)
						$supplier_use_prepay             = $ord["supplier_use_prepay"];                     //공급처 선급금 사용 여부 (Y/N)
						$market_order_no                 = $ord["market_order_no"];                         //마켓 주문번호
						$market_product_no               = $ord["market_product_no"];                       //마켓 상품 번호
						$market_product_name             = $ord["market_product_name"];                     //마켓 상품 명
						$market_product_option           = $ord["market_product_option"];                   //마켓 옵션 명
						$order_unit_price                = $ord["order_unit_price"];                        //판매단가
						$order_amt                       = $ord["order_amt"];                               //판매가
						$order_cnt                       = $ord["order_cnt"];                               //판매수량
						$delivery_fee                    = $ord["delivery_fee"];                            //배송비
						$delivery_type                   = $ord["delivery_type"];                           //배송비 정산구분 (선불/착불/선결제 등)
						$delivery_is_free                = $ord["delivery_is_free"];                        //배송비 정산구분 (선불:Y/착불:N)
						$product_idx                     = $ord["product_idx"];                             //상품코드
						$product_name                    = $ord["product_name"];                            //상품명
						$product_option_idx              = $ord["product_option_idx"];                      //옵션코드
						$product_option_name             = $ord["product_option_name"];                     //옵션명
						$product_option_cnt              = $ord["product_option_cnt"];                      //상품수량
						$product_tax_type                = $ord["product_tax_type"];                        //상품 세금 종류
						$product_tax_ratio               = ($product_tax_type == "TAXATION") ? 10 : 0;      //상품 대상세율 [과세 일 경우에만 10%]
						$product_sale_type               = $ord["product_sale_type"];                       //상품 판매 방식 (사입/위탁)
						$stock_idx                       = $ord["stock_idx"];                               //연결된 재고 코드
						$stock_unit_price                = $ord["stock_unit_price"];                        //연결된 재고 가격
						$stock_amount                    = $ord["stock_amount"];                            //연결된 재고 수량 [원가가 다른 재고가 한주문에 연결될 수 있음]
						$product_option_purchase_price   = $ord["product_option_purchase_price"];           //옵션 매입단가 - DY
						$product_option_sale_price       = $ord["product_option_sale_price"];               //판매단가 2 - DY 기준
						$product_delivery_fee_sale       = $ord["product_delivery_fee_sale"];               //상품 매입배송비
						$product_delivery_fee_buy        = $ord["product_delivery_fee_buy"];                //상품 매출배송비
						$product_calculation_amt         = $ord["product_calculation_amt"];                 //상품 정산예정금액

						$product_category_l_idx          = $ord["product_category_l_idx"];
						$product_category_m_idx          = $ord["product_category_m_idx"];

						$invoice_date                    = $ord["invoice_date"];
						$shipping_date                   = $ord["shipping_date"];
						$cancel_date                     = $ord["invoice_date"];

						$order_matching_idx              = $ord["order_matching_idx"];

						$settle_sale_supply                = 0;     //매출공급가액
						$settle_sale_supply_ex_vat         = 0;     //매출공급가액 부가세제외
						$settle_sale_commission_ex_vat     = 0;     //판매수수료 부가세 별도
						$settle_sale_commission_in_vat     = 0;     //판매수수료 부가세 포함
						$settle_delivery_in_vat            = 0;     //배송비 부가세 포함
						$settle_delivery_ex_vat            = 0;     //배송비 부가세 별도
						$settle_delivery_commission_ex_vat = 0;     //판매배송비 수수료 부가세 별도
						$settle_delivery_commission_in_vat = 0;     //판매배송비 수수료 부가세 포함
						$settle_purchase_supply            = 0;     //매입가(매출원가) 공급가액
						$settle_purchase_supply_ex_vat     = 0;     //매입가(매출원가) 공급가액 부가세 별도
						$settle_purchase_delivery_in_vat   = 0;     //매입 배송비 부가세 포함
						$settle_purchase_delivery_ex_vat   = 0;     //매입 배송비 부가세 별도
						$settle_sale_profit                = 0;     //매출 이익
						$settle_sale_amount                = 0;     //매출액
						$settle_sale_cost                  = 0;     //매출원가

						$settle_sale_sum                   = 0;     //매출합계
						$settle_purchase_sum               = 0;     //매입합계

						$commission = 0;            //수수료 (마켓)
						$delivery_commission = 0;   //배송비 수수료 (마켓)
						if($seller_type == "MARKET_SELLER" || $seller_type == "CUSTOM_SELLER"){
							$_market_commission = $this->getSettleCommission($seller_idx, $product_idx, $product_option_idx);
							$commission = $_market_commission["market_commission"];            //수수료 (마켓)
							$delivery_commission = $_market_commission["delivery_commission"];   //배송비 수수료 (마켓)
						}

						//자체 상품일 경우 배송후 취소 일 경우에만 정산 입력
						//위탁 상품의 경우 무조건 정산 입력
						$isSettleIn = false;
						if($product_sale_type == "SELF"){
							if($product_cancel_shipped == "Y"){
								$isSettleIn = true;
							}
						}elseif($product_sale_type == "CONSIGNMENT"){
							$isSettleIn = true;
						}

						if(!$isSettleIn) continue;


						//벤더사 판매처라면 판매가 및 판매단가는 매칭 상품의 판매단가로 대체
						if($seller_type == "VENDOR_SELLER"){
							$order_unit_price = $product_option_sale_price;

							//변경!! 주문금액은 배송비를 포함하지 않음
							//$order_amt = ($order_unit_price * $product_option_cnt) + $delivery_fee;
							$order_amt = ($order_unit_price * $product_option_cnt);

							//벤더사 일 경우 매출배송비는 상품에 기록된 금액으로 대체
							$delivery_fee = $product_delivery_fee_sale;
						}else{

							//판매단가 = (판매금액 - 배송비) / 수량
							//소수점 버림
							//변경!! 주문금액은 배송비를 포함하지 않음
							//$order_unit_price = $product_option_sale_price = floor(($order_amt - $delivery_fee) / $product_option_cnt);
							//매칭 판매 단가는 매칭 시에 설정되므로 따로 수정하지 않는다.
							//$order_unit_price = $product_option_sale_price = floor($order_amt / $product_option_cnt);
							//$order_unit_price = floor($order_amt / $product_option_cnt);
							//매칭 시 주문 금액으로 개별 판매단가를 저장 했으므로 해당 금액을 불러옴 - 19.04.25
							$order_amt = $product_option_sale_price * $product_option_cnt;
							$order_unit_price = $product_option_sale_price;

							//벤더사 판매처가 아니라면
							//두번째 매칭 상품 부터는 판매단가 및 판매가 공란
							//
							//같은 주문에 여러의 상품이 매칭된 경우
							//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
							if($prev_order_idx == $order_idx) {
								$order_unit_price = 0;
								$order_amt        = 0;
							}
						}

						//자체 상품이면 매입단가를 재고매입단가로 Update
						//재고 수량으로 대체
						if($product_sale_type == "SELF")
						{
							$product_option_purchase_price = $stock_unit_price;
							//취소or복귀 시에는 재고 수량을 따르지 않는다 - 19.04.25
							//$product_option_cnt = $stock_amount;
						}

						//관리번호와 합포번호가 다르면 - 합포된 주문
						if($prev_order_pack_idx == $order_pack_idx || $order_idx != $order_pack_idx){
							$delivery_fee = 0;  //배송비
							$product_delivery_fee_buy = 0; //매입배송비
						}

						//같은 주문에 여러의 상품이 매칭된 경우
						//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
						//위 두 경우는 하나의 주문이라도 정산 테이블에 여러번 입력 된다.
						//두번째 입력 될 때부터는....
						if($prev_order_idx == $order_idx){
							$delivery_fee = 0;  //배송비
							$product_delivery_fee_buy = 0; //매입배송비
						}

						//매출공급가액 - 변경!! 주문금액은 배송비를 포함하지 않음
						//$settle_sale_supply = $order_amt - $delivery_fee;
						$settle_sale_supply = $order_amt;
						//매출공급가액 부가세 제외금액
						$settle_sale_supply_ex_vat = round($settle_sale_supply / (($product_tax_ratio/100) + 1));

						//!!!!19.04.25 정산예정금 관련 내용 추가!!!!
						//20.02.26 변경 수수료 ex 스왑
						//정산예정금액이 있으면 정산예정금액을 제외한 나머지 금액이 판매수수료가 됨
						//없으면 기존 대로 수수료관리에 등록된 내용으로 계산
						if($product_calculation_amt > 0){
							//정산예정금이 있다면
							//판매수수료 부가세포함 [매출공급가액 - 정산예정금(상품 정산예정금 * 상품 개수)]
							$settle_sale_commission_in_vat = $settle_sale_supply - ($product_calculation_amt * $product_option_cnt);
							//판매수수료 부가세별도 [판매수수료 부가세별도 / 1.1]
							$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
						}else {
							//수수료가 있다면
							//판매수수료 부가세포함 [매출공급가액 * 판매수수료]
							$settle_sale_commission_in_vat = round($settle_sale_supply * ($commission / 100));
							//판매수수료 부가세별도 [판매수수료 부가세별도 * 1.1]
							$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
						}

						//배송비 부가세포함
						$settle_delivery_in_vat = $delivery_fee;
						//배송비 부가세별도
						$settle_delivery_ex_vat = round($settle_delivery_in_vat / 1.1);
						//20.02.26 변경 판매배송비수수료 ex 스왑
						//판매배송비수수료 부가세포함 [배송비 부가세포함 - 배송비수수료]
						$settle_delivery_commission_in_vat = round($settle_delivery_in_vat * ($delivery_commission/100));
						//판매배송비수수료 부가세별도
						$settle_delivery_commission_ex_vat = round($settle_delivery_commission_in_vat / 1.1);
						//매입단가(매출원가) 공급가액
						$settle_purchase_unit_supply = $product_option_purchase_price;
						//매입단가(매출원가) 공급가액 부가세 별도 [매입단가(매출원가) 공급가액 / 1.1]
						$settle_purchase_unit_supply_ex_vat = round($product_option_purchase_price / (($product_tax_ratio/100) + 1));
						//매입가(매출원가) 공급가액
						$settle_purchase_supply = $product_option_purchase_price * $product_option_cnt;
						//매입가(매출원가) 공급가액 부가세 별도 [매입가(매출원가) 공급가액 - 상품 대상세금]
						$settle_purchase_supply_ex_vat = round($settle_purchase_supply / (($product_tax_ratio/100) + 1));
						//매입 배송비 부가세 포함
						$settle_purchase_delivery_in_vat = $product_delivery_fee_sale;
						//매입 배송비 부가세 별도 [매입 배송비 부가세 포함 / 1.1];
						$settle_purchase_delivery_ex_vat   = round($settle_purchase_delivery_in_vat / 1.1);

						//매출이익 = 매출공급가액[부X] - 수수료[판매수수료 부X] + 배송비[부X] - 배송비 수수료[판매배송비수수료 부X] - 매입가[부X] - 매입배송비[부X]
						$settle_sale_profit = $settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat - $settle_purchase_supply_ex_vat - $settle_purchase_delivery_ex_vat;

						//매출액 = 매출공급가액 + 배송비 - 판매수수료[부X] - 판매배송비수수료[부X]
						$settle_sale_amount = $settle_sale_supply + $settle_delivery_in_vat - $settle_sale_commission_ex_vat - $settle_delivery_commission_ex_vat;

						//매출원가 = 매입가(매출원가) 공급가액[부X] + 매입배송비[부X]
						$settle_sale_cost = $settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat;

						//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
						$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
						//매입합계 (매입가 + 매입배송비)
						$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

						$qry = "
							Insert Into DY_SETTLE
							(
								settle_date, settle_type, order_idx, order_pack_idx, order_cs_status, order_progress_step_accept_date
								, seller_idx, supplier_idx, vendor_grade, vendor_use_charge, supplier_use_prepay
								, market_order_no, market_product_no, market_product_name, market_product_option
								, order_unit_price, order_amt, order_cnt
								, commission, delivery_commisision
								, delivery_fee, delivery_type, delivery_is_free
								, order_matching_idx
								, product_idx, product_name, product_option_idx, product_option_name, product_option_cnt, product_sale_type, product_tax_type
								, product_option_sale_price, product_option_purchase_price, product_delivery_fee_sale, product_delivery_fee_buy
								, product_category_l_idx, product_category_m_idx
								, stock_idx
								, settle_sale_supply, settle_sale_supply_ex_vat, settle_sale_commission_ex_vat, settle_sale_commission_in_vat
								, settle_delivery_in_vat, settle_delivery_ex_vat, settle_delivery_commission_ex_vat, settle_delivery_commission_in_vat
								, settle_purchase_supply, settle_purchase_supply_ex_vat, settle_purchase_delivery_in_vat, settle_purchase_delivery_ex_vat
								, settle_sale_profit, settle_sale_amount, settle_sale_cost
								, settle_purchase_unit_supply, settle_purchase_unit_supply_ex_vat
								, settle_sale_sum, settle_purchase_sum
								, settle_regip, last_member_idx
							) 
							VALUES 
							(
							 N'$settle_date'
							 , N'$settle_type'
							 , N'$order_idx'
							 , N'$order_pack_idx'
							 , N'$order_cs_status'
							 , N'$order_progress_step_accept_date'
							 , N'$seller_idx'
							 , N'$supplier_idx'
							 , N'$vendor_grade'
							 , N'$vendor_use_charge'
							 , N'$supplier_use_prepay'
							 , N'$market_order_no'
							 , N'$market_product_no'
							 , N'$market_product_name'
							 , N'$market_product_option'
							 , N'$order_unit_price'
							 , N'$order_amt'
							 , N'$product_option_cnt'
							 , N'$commission'
							 , N'$delivery_commission'
							 , N'$delivery_fee'
							 , N'$delivery_type'
							 , N'$delivery_is_free'
							 , N'$order_matching_idx'
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
							 , N'$product_category_l_idx'
							 , N'$product_category_m_idx'
							 , N'$stock_idx'
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
							 , N'$modip'
							 , N'$last_member_idx'
							)
						";

						$inserted_idx = parent::execSqlInsert($qry);

						$prev_order_idx = $order_idx;
						$prev_order_pack_idx = $order_pack_idx;
					}
				}







				//CS 입력
				$cs_task = "ORDER_RESTORE_ONE";    //개별정상복귀
				$cs_msg_prefix = "상품옵션코드 : " . $__product_option_idx . "\n";
				$cs_msg_prefix .= "수량 : " . $__product_option_cnt . "\n";
				$this->insertCS($__order_idx, $order_pack_idx, $tmp_insert, 0, $__product_option_idx, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

			}else{
				parent::sqlTransactionRollback();     //트랜잭션 롤백
				$returnValue = false;
				return $returnValue;
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 회수 요청
	 * @param $order_idx
	 * @param $order_pack_idx
	 * @param $send_info
	 * @param $receive_info
	 * @param $request_info
	 * @param $is_auto_stock_order
	 * @param $product_list
	 * @param $cs_msg
	 * @return bool
	 */
	public function insertOrderReturn($order_idx, $order_pack_idx, $send_info, $receive_info, $request_info, $is_auto_stock_order, $product_list, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 배송 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step != "ORDER_SHIPPED"){
			$returnValue = false;
			return $returnValue;
		}


		$send_name       = $send_info["send_name"];
		$send_tel_num    = $send_info["send_tel_num"];
		$send_hp_num     = $send_info["send_hp_num"];
		$send_zipcode    = $send_info["send_zipcode"];
		$send_address    = $send_info["send_address"];
		$send_memo       = $send_info["send_memo"];

		$receive_name    = $receive_info["receive_name"];
		$receive_tel_num = $receive_info["receive_tel_num"];
		$receive_hp_num  = $receive_info["receive_hp_num"];
		$receive_zipcode = $receive_info["receive_zipcode"];
		$receive_address = $receive_info["receive_address"];

		$delivery_pay_type = $request_info["delivery_pay_type"];
		$delivery_return_type = $request_info["delivery_return_type"];
		$box_num = $request_info["box_num"];
		$product_price = $request_info["product_price"];
		$delivery_price = $request_info["delivery_price"];
		$pay_site = $request_info["pay_site"];
		$pay_pack = $request_info["pay_pack"];
		$pay_account = $request_info["pay_account"];


		//$FRT_DV_CD         = "";    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
		$FRT_DV_CD = "";
		if($delivery_pay_type == "선불") {
			$FRT_DV_CD = "01";
		}
		if($delivery_pay_type == "착불") {
			$FRT_DV_CD = "02";
		}
		if($delivery_pay_type == "선불") {
			$FRT_DV_CD = "03";
		}
		$API_CJ_Invoice = new API_CJ_Invoice();
		$_cj_info = array(
			'RCPT_DV' => "02",    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
			'SENDR_NM' => $send_name,    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
			'SENDR_TEL_NO' => ($send_hp_num != "" ? $send_hp_num : $send_tel_num),    // 송화인전화번호 : '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
			'SENDR_ADDR' => $send_address,    // 송화인주소 : 송화인 주소
			'RCVR_NM' => $receive_name,    // 수화인명 :  (ex : 홍길동)
			'RCVR_TEL_NO' => ($receive_tel_num != "" ? $receive_tel_num : $receive_hp_num),    // 수화인전화번호1 : 수화인전화번호 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
			'RCVR_ADDR' => $receive_address,    // 수화인주소 : 수화인 주소 (
			'INVC_NO' => "",    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다. Ex: 운송장번호 301100112233 의 경우, 3~11의 수(110011223) 을 MOD(7)한 결과가 3 이기에 적합한운송장번호이다. (ex : 301100112233)"
			'GDS_NM' => "반품",    // 상품명 :  (ex : 사과쥬스1박스)
			'FRT_DV_CD' => $FRT_DV_CD,    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
			'PRT_ST' => "03",    // 출력상태 : 01: 미출력,  02: 선출력,  03: 선발번 (반품은 선발번이 없음) (반품시 03)
		);
		//print_r2($_cj_info);
		$_ret = $API_CJ_Invoice->insertCJInvoice($_cj_info);
		if(!$_ret["result"]) {
			$returnValue = false;
			return $returnValue;
		}


		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//회수 요청 입력
		$qry = "
			Insert Into DY_ORDER_RETURN
			(
		      order_idx, order_pack_idx, delivery_status, delivery_code, accept_date, invoice_no, invoice_date, receive_date
		      , send_name, send_tel_num, send_hp_num, send_zipcode, send_address, send_memo
		      , receive_name, receive_tel_num, receive_hp_num, receive_zipcode, receive_address
		      , pay_type, return_type, box_num, product_price, delivery_price
		      , pay_site, pay_pack, pay_account
		      , return_regip, last_member_idx
	        )
	        VALUES
			(
			  N'$order_idx',N'$order_pack_idx', N'RETURN_REQUEST', N'CJGLS', null, N'', null, null
			  , N'$send_name', N'$send_tel_num', N'$send_hp_num', N'$send_zipcode', N'$send_address', N'$send_memo'
			  , N'$receive_name', N'$receive_tel_num', N'$receive_hp_num', N'$receive_zipcode', N'$receive_address'
			  , N'$delivery_pay_type', N'$delivery_return_type', N'$box_num', N'$product_price', N'$delivery_price'
			  , N'$pay_site', N'$pay_pack', N'$pay_account'
			  , N'$modip', N'$last_member_idx'
			)
		";

		$return_idx = parent::execSqlInsert($qry);

		if(!$return_idx){
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}else{

			//회수 상품 입력
			if($product_list){
				foreach($product_list as $prod){

					$order_idx = $prod["order_idx"];
					$order_matching_idx = $prod["order_matching_idx"];
					$product_idx = $prod["product_idx"];
					$product_option_idx = $prod["product_option_idx"];
					$product_option_cnt = $prod["return_cnt"];

					$qry = "
						Insert Into DY_ORDER_RETURN_PRODUCT
						(
						  return_idx, order_idx, order_pack_idx
						  , order_matching_idx, product_idx, product_option_idx, product_option_cnt
						  , return_product_regdate, return_product_regip, last_member_idx
					    )
					    VALUES 
						(
						 N'$return_idx', N'$order_idx', N'$order_pack_idx'
						 , N'$order_matching_idx', N'$product_idx', N'$product_option_idx', N'$product_option_cnt'
						 , getdate(), N'$modip', N'$last_member_idx'
						)
					";

					$_return_prod_idx = parent::execSqlInsert($qry);

					//자동 입고예정 등록 일 경우
					if($is_auto_stock_order == "Y"){

						//사입 자체 상품인지 확인
						$qry = "
							Select product_sale_type
							From DY_PRODUCT
							Where product_idx = N'$product_idx'
						";
						$_product_sale_type = parent::execSqlOneCol($qry);
						if($_product_sale_type == "SELF") {
							//배송처리된 재고 찾기
							$qry = "
								Select Top 1 stock_idx 
								From DY_STOCK S 
								Where 
								      stock_is_del = N'N'
								      And order_matching_idx = N'$order_matching_idx'
								      And product_idx = N'$product_idx'
								      And product_option_idx = N'$product_option_idx'
								      And order_idx = N'$order_idx'
								      And stock_status = N'SHIPPED'
								Order by stock_idx DESC
							";

							$_stock_idx = parent::execSqlOneCol($qry);

							if ($_stock_idx) {
								//입고예정 등록
								$qry = "
									Insert Into DY_STOCK
									(
									 stock_ref_idx, product_idx, product_option_idx
									 , stock_kind, order_idx, stock_order_idx, stock_order_is_ready, stock_order_msg
									 , stock_in_date, stock_due_date
									 , stock_type, stock_status
									 , stock_unit_price, stock_due_amount, stock_amount, stock_msg
									 , stock_file_idx, stock_request_date, stock_request_member_idx
									 , stock_regip, last_member_idx
									 )
									 SELECT 
										0, product_idx, product_option_idx
										, N'BACK', order_idx, 0, N'N', N''
										, N'', N''
										, N'0', N'$delivery_return_type'
										, stock_unit_price, N'$product_option_cnt', N'0', N''
										, N'0', getdate(), N'$last_member_idx'
										, N'$modip', N'$last_member_idx'
								     From DY_STOCK
									 Where stock_idx = N'$_stock_idx'
								";

								$in_stock_idx = parent::execSqlInsert($qry);

								$qry = "
									Update DY_STOCK
										Set stock_ref_idx = N'$in_stock_idx'
										Where stock_idx = N'$in_stock_idx'
								";
								$tmp = parent::execSqlUpdate($qry);


							}
						}

					}

				}
			}

			//CS 입력
			$cs_task = "ORDER_RETURN";    //회수요청
			$cs_msg_prefix = "";
			if($product_list) {
				foreach($product_list as $prod) {
					$cs_msg_prefix = "상품옵션코드 : " . $prod["product_option_idx"] . ", ";
					$cs_msg_prefix .= "수량 : " . $prod["return_cnt"] . "\n";
				}
			}
			$this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");
		}

		//원 주문 회수요청여부 Update
		$qry = "
			Update
				DY_ORDER
			Set order_return_request = N'Y'
			    , order_return_request_date = getdate()
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where order_idx = '$order_pack_idx'
		";
		parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;



		return $returnValue;
	}

	/**
	 * 회수 요청 수정 - 반품 수정
	 * @param $return_idx
	 * @param $order_pack_idx
	 * @param $send_info
	 * @param $receive_info
	 * @param $request_info
	 * @param $is_auto_stock_order
	 * @param $product_list
	 * @param $cs_msg
	 * @return bool
	 */
	public function updateOrderReturn($return_idx, $order_idx, $order_pack_idx, $send_info, $receive_info, $request_info, $is_auto_stock_order, $product_list, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 배송 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step != "ORDER_SHIPPED"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$send_name       = $send_info["send_name"];
		$send_tel_num    = $send_info["send_tel_num"];
		$send_hp_num     = $send_info["send_hp_num"];
		$send_zipcode    = $send_info["send_zipcode"];
		$send_address    = $send_info["send_address"];
		$send_memo       = $send_info["send_memo"];

		$receive_name    = $receive_info["receive_name"];
		$receive_tel_num = $receive_info["receive_tel_num"];
		$receive_hp_num  = $receive_info["receive_hp_num"];
		$receive_zipcode = $receive_info["receive_zipcode"];
		$receive_address = $receive_info["receive_address"];

		$delivery_pay_type = $request_info["delivery_pay_type"];
		$delivery_return_type = $request_info["delivery_return_type"];
		$box_num = $request_info["box_num"];
		$product_price = $request_info["product_price"];
		$delivery_price = $request_info["delivery_price"];
		$pay_site = $request_info["pay_site"];
		$pay_pack = $request_info["pay_pack"];
		$pay_account = $request_info["pay_account"];

		$qry = "
			Update DY_ORDER_RETURN
			Set
				send_name = N'$send_name', 
				send_tel_num = N'$send_tel_num', 
				send_hp_num = N'$send_hp_num', 
				send_zipcode = N'$send_zipcode', 
				send_address = N'$send_address', 
				send_memo = N'$send_memo', 
				receive_name = N'$receive_name', 
				receive_tel_num = N'$receive_tel_num', 
				receive_hp_num = N'$receive_hp_num', 
				receive_zipcode = N'$receive_zipcode', 
				receive_address = N'$receive_address', 
				pay_type = N'$delivery_pay_type', 
				return_type = N'$delivery_return_type', 
				box_num = N'$box_num', 
				product_price = N'$product_price', 
				delivery_price = N'$delivery_price', 
				pay_site = N'$pay_site', 
				pay_pack = N'$pay_pack', 
				pay_account = N'$pay_account',
			    return_moddate = getdate(),
			    return_modip = N'$modip',
			    last_member_idx = N'$last_member_idx'
			Where return_idx = N'$return_idx'
		";

		parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "ORDER_RETURN";    //회수요청
		$cs_msg_prefix = "[회수요청 수정]\n";
		$this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");


		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 회수 요청 삭제 - 반품삭제
	 * @param $return_idx
	 * @return bool
	 */
	public function deleteOrderReturn($return_idx, $order_pack_idx, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Update DY_ORDER_RETURN
			Set
				return_is_del = N'Y',
			    return_moddate = getdate(),
			    return_modip = N'$modip',
			    last_member_idx = N'$last_member_idx'
			Where return_idx = N'$return_idx'
		";

		parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "ORDER_RETURN";    //회수요청
		$cs_msg_prefix = "[회수요청 삭제]\n";
		$this->insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");



		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

    /**
     * 재고회수
     * @param $order_idx
     * @param $order_pack_idx
     * @param $product_list
     * @param $cs_msg
     * @return bool
     */
    public function insertStockReturn($order_idx, $order_pack_idx, $product_list, $cs_msg)
    {
        global $GL_Member;
        $modip = $_SERVER["REMOTE_ADDR"];
        $last_member_idx = $GL_Member["member_idx"];

        $returnValue = false;

        // 배송 상태 인지 체크
        $qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
        parent::db_connect();
        $_pack_order_progress_step = parent::execSqlOneCol($qry);
        parent::db_close();

        if($_pack_order_progress_step != "ORDER_SHIPPED"){
            $returnValue = false;
            return $returnValue;
        }

        parent::db_connect();
        parent::sqlTransactionBegin();  //트랜잭션 시작

        //회수 상품 입력
        if($product_list){
            foreach($product_list as $prod){

            $order_idx = $prod["order_idx"];
            $order_matching_idx = $prod["order_matching_idx"];
            $product_idx = $prod["product_idx"];
            $product_option_idx = $prod["product_option_idx"];
            $product_option_cnt = $prod["return_cnt"];

            //사입 자체 상품인지 확인
            $qry = "
                Select product_sale_type
                From DY_PRODUCT
                Where product_idx = N'$product_idx'
            ";
            $_product_sale_type = parent::execSqlOneCol($qry);
                if($_product_sale_type == "SELF") {
                    //배송처리된 재고 찾기
                    $qry = "
                        Select Top 1 stock_idx
                        From DY_STOCK S 
                        Where 
                              stock_is_del = N'N'
                              And order_matching_idx = N'$order_matching_idx'
                              And product_idx = N'$product_idx'
                              And product_option_idx = N'$product_option_idx'
                              And order_idx = N'$order_idx'
                              And stock_status = N'SHIPPED'
                        Order by stock_idx DESC
                    ";

                    $_stock_idx = parent::execSqlOneCol($qry);

                    if ($_stock_idx) {
                        //입고예정 등록
                        $qry = "
                            Insert Into DY_STOCK
                            (
                             stock_ref_idx, product_idx, product_option_idx
                             , stock_kind, order_idx, stock_order_idx, stock_order_is_ready, stock_order_msg
                             , stock_in_date, stock_due_date
                             , stock_type, stock_status
                             , stock_unit_price, stock_due_amount, stock_amount, stock_msg
                             , stock_file_idx, stock_request_date, stock_request_member_idx
                             , stock_regip, last_member_idx
                             )
                             SELECT 
                                0, product_idx, product_option_idx
                                , N'BACK', order_idx, 0, N'N', N''
                                , N'', N''
                                , N'0', N'RETURN'
                                , stock_unit_price, N'$product_option_cnt', N'0', N''
                                , N'0', getdate(), N'$last_member_idx'
                                , N'$modip', N'$last_member_idx'
                             From DY_STOCK
                             Where stock_idx = N'$_stock_idx'
                        ";

                        $in_stock_idx = parent::execSqlInsert($qry);

                        $qry = "
                            Update DY_STOCK
                                Set stock_ref_idx = N'$in_stock_idx'
                                Where stock_idx = N'$in_stock_idx'
                        ";
                        $tmp = parent::execSqlUpdate($qry);


                    }
                }
            }
        }
        //CS 입력
        $cs_task = "STOCK_RETURN";    //회수요청
        $cs_msg_prefix = "";
        if($product_list) {
            foreach($product_list as $prod) {
                $cs_msg_prefix = "상품옵션코드 : " . $prod["product_option_idx"] . ", ";
                $cs_msg_prefix .= "수량 : " . $prod["return_cnt"] . "\n";
            }
        }
        $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");


        //원 주문 재고회수여부 Update
        $qry = "
			Update
				DY_ORDER
			Set order_stock_return = N'Y'
			    , order_stock_return_date = getdate()
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where order_idx = '$order_idx'
		";
        parent::execSqlUpdate($qry);

        parent::sqlTransactionCommit();     //트랜잭션 커밋
        $returnValue = true;

        return $returnValue;
    }


	/**
	 * 상품 교환
	 * TODO : 상품 교환 시 정산 테이블 입력 여부 확인 필요!
	 * @param $order_pack_idx
	 * @param $order_idx
	 * @param $order_matching_idx
	 * @param $c_product_idx
	 * @param $c_product_option_idx
	 * @param $c_product_option_cnt
	 * @param $c_add_price
	 * @param $cs_reason_code1
	 * @param $cs_reason_code2
	 * @param $cs_msg
	 * @return bool
	 */
	public function changeOrderProduct($order_pack_idx, $order_idx, $order_matching_idx, $c_product_idx, $c_product_option_idx, $c_product_option_cnt, $c_product_sale_price, $c_add_price, $cs_reason_code1, $cs_reason_code2, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE" || $_pack_order_progress_step == "ORDER_COLLECT"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//현재 상품 가져오기
		$qry = "
			Select M.*, P.product_name, PO.product_option_name , PO.product_option_purchase_price, P.product_sale_type
			From DY_ORDER_PRODUCT_MATCHING M
				Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
				Left Outer Join DY_PRODUCT_OPTION PO On M.product_option_idx = PO.product_option_idx
			Where order_matching_idx = N'$order_matching_idx'
		";

		$_opm = parent::execSqlOneRow($qry);

		$product_idx                   = $_opm["product_idx"];
		$product_option_idx            = $_opm["product_option_idx"];
		$product_name                  = $_opm["product_name"];
		$product_option_name           = $_opm["product_option_name"];
		$product_option_cnt            = $_opm["product_option_cnt"];
		$seller_idx                    = $_opm["seller_idx"];
		$product_option_purchase_price = $_opm["product_option_purchase_price"];
		$product_sale_type             = $_opm["product_sale_type"];

		//교환될 상품 가져오기
		$qry = "
			Select P.product_name, PO.product_option_name , PO.product_option_purchase_price, P.product_sale_type
			From DY_PRODUCT P
				Left Outer Join DY_PRODUCT_OPTION PO On P.product_idx = PO.product_idx
			Where PO.product_option_idx = N'$c_product_option_idx'
		";

		$_opm_change = parent::execSqlOneRow($qry);
		$c_product_sale_type           = $_opm_change["product_sale_type"];

		//입력된 판매가를 기준으로 판매단가 구하기
		//소수점 버림
		$product_option_sale_price = floor(($c_product_sale_price+$c_add_price) / $c_product_option_cnt);

		//배송 전/후 분기
		if($_pack_order_progress_step == "ORDER_SHIPPED")
		{
			//배송 후 교환

			//교환 상품 정산 취소 입력
			$this->insertSettleCancel($order_matching_idx, $cs_reason_code2, false, true);

			//주문 상태 값만 변경
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
					Set 
					    order_cs_status = N'PRODUCT_CHANGE'
					    , product_change_shipped = N'Y'
					    , product_change_date = getdate()
					    , cs_reason_code1 = N'$cs_reason_code1'
					    , cs_reason_code2 = N'$cs_reason_code2'
						, order_matching_moddate = getdate()
						, order_matching_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where
						order_matching_idx = N'$order_matching_idx'
			";
			parent::execSqlUpdate($qry);

			//신규 주문 입력

			//발주 IDX 생성
			$_new_order_idx = $this->makeOrderIdx();

			//기존 주문 복사
			$qry = "
				Insert Into DY_ORDER
				(
				  order_idx, order_pack_idx, seller_idx, order_progress_step, order_progress_step_accept_temp_date, order_progress_step_accept_date, matching_type, 
				  order_pack_code, order_pay_date, market_order_no, market_order_no_is_auto, market_order_subno, market_product_no,market_product_no_is_auto,
				  market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
				  order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode, 
				  receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
				  delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, order_is_after_order, order_write_type, 
				  order_regdate, order_regip, last_member_idx
				)
				Select
					N'$_new_order_idx', N'$_new_order_idx', seller_idx, N'ORDER_ACCEPT', getdate(), getdate(), N'MANUAL',
			        order_pack_code, order_pay_date, 'C'+market_order_no, market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto,
			        market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
				    order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode, 
			        receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
			        delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, N'Y', N'CS_WRITE', 
					getdate(), N'$modip', N'$last_member_idx'
				From DY_ORDER
				Where order_idx = N'$order_idx'
			";

			$new_order = parent::execSqlInsert($qry);

			//주문 매칭 입력
			$qry = "
				Insert Into DY_ORDER_PRODUCT_MATCHING
				(
				    order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt, 
				    product_option_sale_price, product_option_purchase_price, 
				    order_matching_is_auto, order_matching_regdate, order_matching_regip, last_member_idx
			    )
			    VALUES 
				(
				    N'$_new_order_idx', N'$seller_idx', N'$c_product_idx', N'$c_product_option_idx', N'$c_product_option_cnt',
				    N'$product_option_sale_price', N'$product_option_purchase_price', 
				    N'N', getdate(), N'$modip', N'$last_member_idx'
				)
			";
			$_m_idx = parent::execSqlInsert($qry);

			//교환 후 상품 정산 입력
			//위탁상품일 경우에만 입력
			if($c_product_sale_type == "CONSIGNMENT") {
				$this -> insertSettleShipped($_new_order_idx, true, false, 0, true);
			}


		}else{
			//배송 전 교환

			//교환 전 상품 정산 취소 입력
			//위탁 상품 일 경우에만 입력 함
			if($product_sale_type == "CONSIGNMENT" && $_pack_order_progress_step == "ORDER_ACCEPT") {
				$this->insertSettleCancel($order_matching_idx, $cs_reason_code2, false, true);
			}

			//상품 교환
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
					Set 
					    order_cs_status = N'PRODUCT_CHANGE'
						, product_idx = N'$c_product_idx'
						, product_option_idx = N'$c_product_option_idx'
						, product_option_cnt = N'$c_product_option_cnt'
						, product_option_sale_price = N'$product_option_sale_price'
						, product_option_purchase_price = N'$product_option_purchase_price'
					    , product_change_shipped = N'N'
					    , product_change_date = getdate()
						, order_matching_moddate = getdate()
						, order_matching_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where
						order_matching_idx = N'$order_matching_idx'
			";

			parent::execSqlUpdate($qry);

			//교환 후 상품 정산 입력
			//위탁상품일 경우에만 입력
			if($c_product_sale_type == "CONSIGNMENT" && $_pack_order_progress_step == "ORDER_ACCEPT") {
				$this -> insertSettleShipped(0, true, false, $order_matching_idx, true);
			}
		}

		//CS 입력
		$cs_task = "PRODUCT_CHANGE";    //상품교환
		$cs_msg_prefix = "&lt;기존 상품 : (".$product_option_idx.") ".$product_name." - ".$product_option_name."&gt;";
		$cs_msg_prefix .= "&lt;수량 : ".$product_option_cnt."&gt;";
		$cs_msg_prefix .= "&lt;추가금액 : ".$c_add_price."&gt;\n";
		$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, $cs_reason_code1, $cs_reason_code2);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 상품 추가
	 * TODO : 상품 추가 시 원주문 판매금액 Update 됨! 확인 필요!!
	 * TODO : 상품 추가 시 위탁 상품 정산 테이블에 Insert 필요!!
	 * @param $order_pack_idx
	 * @param $order_idx
	 * @param $seller_idx
	 * @param $seller_type
	 * @param $product_idx_ary
	 * @param $product_option_idx_ary
	 * @param $product_option_cnt_ary
	 * @param $product_option_sale_price_ary
	 * @param $cs_msg
	 * @return bool
	 */
	public function addOrderProduct($order_pack_idx, $order_idx, $seller_idx, $seller_type, $product_idx_ary, $product_option_idx_ary, $product_option_cnt_ary, $product_option_sale_price_ary, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_pack_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pack_order_progress_step == "ORDER_INVOICE" || $_pack_order_progress_step == "ORDER_SHIPPED" || $_pack_order_progress_step == "ORDER_COLLECT"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$cs_msg_prefix = "";

		//상품 배열 확인
		if(count($product_idx_ary) > 0 && count($product_option_idx_ary) > 0) {
			foreach ($product_idx_ary as $key => $val) {

				$product_idx               = $product_idx_ary[$key];
				$product_option_idx        = $product_option_idx_ary[$key];
				$product_option_sale_price = str_replace(",", "", $product_option_sale_price_ary[$key]);
				$product_option_cnt        = str_replace(",", "", $product_option_cnt_ary[$key]);

				if(!$product_option_sale_price) $product_option_sale_price = 0;

				$product_option_sale_price_unit = 0;
				if($product_option_cnt) {
					$product_option_sale_price_unit = floor($product_option_sale_price / $product_option_cnt);
				}

				//상품명, 옵션명 Get
				$qry = "
					Select product_name, product_option_name, PO.product_option_purchase_price
					From DY_PRODUCT P Inner Join DY_PRODUCT_OPTION PO On P.product_idx = PO.product_idx
					Where P.product_idx = N'$product_idx' And PO.product_option_idx = N'$product_option_idx'
				";
				$_prod = parent::execSqlOneRow($qry);

				if($_prod) {

					$product_name                  = $_prod["product_name"];
					$product_option_name           = $_prod["product_option_name"];
					$product_option_purchase_price = $_prod["product_option_purchase_price"];

					$qry = "
						Insert Into DY_ORDER_PRODUCT_MATCHING
						(
						 order_idx, seller_idx, 
						 product_idx, product_option_idx, product_option_cnt, 
						 order_matching_is_auto, product_option_sale_price, product_option_purchase_price, order_cs_status, product_change_shipped, 
						 order_matching_regdate, order_matching_regip, last_member_idx
					    )
					    VALUES
						(
						 N'$order_idx', N'$seller_idx',
						 N'$product_idx', N'$product_option_idx', N'$product_option_cnt',
						 N'N', N'$product_option_sale_price_unit', N'$product_option_purchase_price', N'NORMAL', '',
						 getdate(), N'$modip', N'$last_member_idx'
						)
					";

					$inserted = parent::execSqlInsert($qry);

					if($inserted){

						//위탁상품일 경우
						//추가된 상품만 정산테이블에 입력. 접수일 경우
						if ($_pack_order_progress_step == "ORDER_ACCEPT") {
							$this->insertSettleShipped($order_pack_idx, true, false, $inserted, false);
						}

						//벤더사 판매처가 아닐 경우
						//주문 판매금액 Update
						if($seller_type != "VENDOR_SELLER") {
							$qry = "
								Update DY_ORDER
									Set order_amt = order_amt + " . $product_option_sale_price . "
									Where order_idx = N'$order_idx'
							";
							$tmp = parent::execSqlUpdate($qry);
						}

						$cs_msg_prefix .= "&lt;상품 : (".$product_option_idx.") ".$product_name." - ".$product_option_name."&gt;";
						$cs_msg_prefix .= "&lt;수량 : ".$product_option_cnt."&gt;";
						$cs_msg_prefix .= "&lt;가격 : ".$product_option_sale_price."&gt;\n";
					}
				}
			}

			//CS 입력
			$cs_task = "PRODUCT_ADD";    //상품교환
			$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;

		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}




		return $returnValue;
	}

	/**
	 * 주문 복사 상품 한 건
	 * @param $order_idx
	 * @param $copy_seller_idx
	 * @param $copy_product_idx
	 * @param $copy_product_option_idx
	 * @param $copy_product_option_cnt
	 * @param $copy_product_option_sale_price
	 * @param $cs_msg
	 * @return bool
	 */
	public function copyOrderOne($order_idx, $copy_seller_idx, $copy_product_idx, $copy_product_option_idx, $copy_product_option_cnt, $copy_product_option_sale_price, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$copy_product_option_cnt = trim(str_replace(",", "", $copy_product_option_cnt));
		$copy_product_option_sale_price = trim(str_replace(",", "", $copy_product_option_sale_price));

		/*******************************************************************************************************************/
		//판매처 타입 확인 [벤더사 판매처인지]
		//벤더사 판매처 등급 확인
		$qry = "
			Select S.seller_type, isNull(V.vendor_grade, '') as vendor_grade 
			From DY_SELLER S
			Left Outer Join DY_MEMBER_VENDOR V On S.seller_idx = V.member_idx 
			Where seller_idx = N'$copy_seller_idx'
		";
		parent::db_connect();
		$_seller_info = parent::execSqlOneRow($qry);
		parent::db_close();
		$seller_type = $_seller_info["seller_type"];
		$vendor_grade = $_seller_info["vendor_grade"];

		//상품 타입 가져오기 [자제/사입], [위탁]
		$product_sale_type = "";
		$qry = "
			Select product_sale_type From DY_PRODUCT Where product_idx = N'$copy_product_idx'
		";
		parent::db_connect();
		$product_sale_type = parent::execSqlOneCol($qry);
		parent::db_close();

		//판매가격, 매입가격 가져오기
		//벤더사 판매처가 아닐 경우 판매가격은 0 으로 설정됨.
		$C_Order = new Order();
		$_option_price_ary = $C_Order->getProductOptionPriceBySeller($copy_product_option_idx, $vendor_grade, true);
		$product_option_sale_price = $_option_price_ary["product_option_sale_price"];
		$product_option_purchase_price = $_option_price_ary["product_option_purchase_price"];
		/*******************************************************************************************************************/

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주 IDX 생성
		$_new_order_idx = $this->makeOrderIdx();

		//주문 상태 가져오기
		$qry = "SELECT order_progress_step From DY_ORDER Where order_idx = N'$order_idx'";
		$originOrderProgressStep = parent::execSqlOneCol($qry);

		//기존 주문 상태가 송장, 배송이라면 접수로 변경
		if ($originOrderProgressStep == "ORDER_INVOICE" || $originOrderProgressStep == "ORDER_SHIPPED") {
			$originOrderProgressStep = "ORDER_ACCEPT";
		}

		//기존 주문 복사
		$qry = "
			Insert Into DY_ORDER
			(
				order_idx, order_pack_idx, seller_idx,
				order_progress_step, order_progress_step_accept_temp_date, order_progress_step_accept_date, order_progress_step_accept_member_idx, matching_type,
				order_pack_code, order_pay_date, market_order_no, market_order_no_is_auto, market_order_subno, market_product_no,market_product_no_is_auto,
				market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
				order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode,
				receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
				delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, order_is_after_order, order_write_type,
				order_regdate, order_regip, last_member_idx
			)
			Select
				N'$_new_order_idx', N'$_new_order_idx', N'$copy_seller_idx',
				N'$originOrderProgressStep', order_progress_step_accept_temp_date, order_progress_step_accept_date, order_progress_step_accept_member_idx, N'MANUAL',
				order_pack_code, order_pay_date,
				market_order_no + '_' + convert(varchar(100), (Select count(*) + 1 From DY_ORDER Where market_order_no like O.market_order_no + '_%')),
				market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto,
				market_product_name, market_product_option, market_order_id, order_unit_price, N'$copy_product_option_sale_price', order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
				order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode,
				receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
				delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, N'Y', N'CS_WRITE',
				getdate(), N'$modip', N'$last_member_idx'
			From DY_ORDER O
			Where order_idx = N'$order_idx'
		";

		$new_order = parent::execSqlInsert($qry);

		//기본 매입가 가져오기
		$qry = "Select product_option_purchase_price From DY_PRODUCT_OPTION Where product_option_idx = N'$copy_product_option_idx'";
		$copy_product_option_purchase_price = parent::execSqlOneCol($qry);

		//주문 매칭 입력
		$qry = "
				Insert Into DY_ORDER_PRODUCT_MATCHING
				(
				    order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt, product_option_sale_price, product_option_purchase_price,
				    order_matching_is_auto, order_matching_regdate, order_matching_regip, last_member_idx
			    )
			    VALUES
	           (
	                N'$_new_order_idx', N'$copy_seller_idx', N'$copy_product_idx', N'$copy_product_option_idx', N'$copy_product_option_cnt', N'$copy_product_option_sale_price', N'$copy_product_option_purchase_price',
		            N'N', getdate(), N'$modip', N'$last_member_idx'
	           )
		";
		$_m_idx = parent::execSqlInsert($qry);

		/*******************************************************************************************************************/
		//벤더사 판매처가 아닐 경우
		//입력 받은 주문 금액을 제품 수량으로 나눠 입력
		if($seller_type != "VENDOR_SELLER") {
			$product_option_sale_price = floor($copy_product_option_sale_price / $copy_product_option_cnt);
		}

		//자제상품 일 경우 매입가를 0 으로 설정
		//송장단계에서 재고 매칭 시 재고원가를 입력 받는다
		if($product_sale_type == "SELF"){
			$product_option_purchase_price = 0;
		}

		//판매가 매입가 Update
		$qry = "
			Update 
				DY_ORDER_PRODUCT_MATCHING 
			Set 
			    product_option_sale_price = N'$product_option_sale_price'
			    , product_option_purchase_price = N'$product_option_purchase_price'
			Where order_matching_idx = N'$_m_idx'
		";
		$tmp = parent::execSqlUpdate($qry);
		/*******************************************************************************************************************/

		//위탁상품일 경우
		//추가된 상품만 정산테이블에 입력. 접수 상태일 때만
		if ($originOrderProgressStep == "ORDER_ACCEPT") {
			$this->insertSettleShipped($_new_order_idx, true, false, $_m_idx, false);
		}

		//CS 입력
		$cs_task = "ORDER_COPY_ONE";    //상품교환
		$cs_msg_prefix = "[주문복사] ";
		$cs_msg_prefix .= "&lt;이전 주문 관리번호 : ".$order_idx."&gt;\n";
		$inserted_cs_idx = $this->insertCS($_new_order_idx, $_new_order_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 주문 전체 복사
	 * @param $order_idx
	 * @param $copy_seller_idx
	 * @param $cs_msg
	 * @return bool
	 */
	public function copyOrderWhole($order_idx, $copy_seller_idx, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;


		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주 IDX 생성
		$_new_order_idx = $this->makeOrderIdx();

		//주문 상태 가져오기
		$qry = "SELECT order_progress_step From DY_ORDER Where order_idx = N'$order_idx'";
		$originOrderProgressStep = parent::execSqlOneCol($qry);

		//기존 주문 상태가 송장, 배송이라면 접수로 변경
		if ($originOrderProgressStep == "ORDER_INVOICE" || $originOrderProgressStep == "ORDER_SHIPPED") {
			$originOrderProgressStep = "ORDER_ACCEPT";
		}

		//기존 주문 복사
		$qry = "
			Insert Into DY_ORDER
			(
			  order_idx, order_pack_idx, seller_idx, 
			  order_progress_step, order_progress_step_accept_temp_date, order_progress_step_accept_date, order_progress_step_accept_member_idx, matching_type, 
			  order_pack_code, order_pay_date, market_order_no, market_order_no_is_auto, market_order_subno, market_product_no,market_product_no_is_auto,
			  market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
			  order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode, 
			  receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
			  delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, order_is_after_order, order_write_type, 
			  order_regdate, order_regip, last_member_idx
			)
			Select
				N'$_new_order_idx', N'$_new_order_idx', N'$copy_seller_idx', 
				N'$originOrderProgressStep', order_progress_step_accept_temp_date, order_progress_step_accept_date, order_progress_step_accept_member_idx, N'MANUAL',
				order_pack_code, order_pay_date
				, market_order_no + '_' + convert(varchar(100), (Select count(*) + 1 From DY_ORDER Where market_order_no like O.market_order_no + '_%'))
				, market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto,
				market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt, order_calculation_amt, order_cnt, delivery_fee,
				order_pay_type, order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode,
				receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo,
				delivery_type, delivery_is_free, order_is_auto, order_org_data1, order_org_data2, N'Y', N'CS_WRITE',
				getdate(), N'$modip', N'$last_member_idx'
			From DY_ORDER O
			Where order_idx = N'$order_idx'
		";

		$new_order = parent::execSqlInsert($qry);

		//주문 매칭 입력
		$qry = "
				Insert Into DY_ORDER_PRODUCT_MATCHING
				(
				    order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt, product_option_sale_price, product_option_purchase_price, product_calculation_amt, 
				    order_matching_is_auto, order_matching_regdate, order_matching_regip, last_member_idx
			    )
			    Select
		            N'$_new_order_idx', N'$copy_seller_idx', product_idx, product_option_idx, product_option_cnt, product_option_sale_price, product_option_purchase_price, product_calculation_amt, 
		            N'N', getdate(), N'$modip', N'$last_member_idx'
				From DY_ORDER_PRODUCT_MATCHING
				Where order_idx = N'$order_idx'
				Order by order_matching_idx ASC
		";
		$_m_idx = parent::execSqlInsert($qry);

		//위탁상품일 경우
		//추가된 상품만 정산테이블에 입력. 주문 상태가 접수일 때만
		if ($originOrderProgressStep == "ORDER_ACCEPT") {
			$this->insertSettleShipped($_new_order_idx, true, false, 0, false);	
		}

		//CS 입력
		$cs_task = "ORDER_COPY_WHOLE";    //상품교환
		$cs_msg_prefix = "[전체주문복사] ";
		$cs_msg_prefix .= "&lt;이전 주문 관리번호 : ".$order_idx."&gt;\n";
		$inserted_cs_idx = $this->insertCS($_new_order_idx, $_new_order_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 회수 요청 리스트
	 * @param $order_pack_idx
	 * @return array
	 */
	public function getOrderReturnList($order_idx){

		$qry = "
			Select 
		       R.*
			, (Select top 1 delivery_name From DY_DELIVERY_CODE D Where D.delivery_code = R.delivery_code) as delivery_name
			, C.code_name as delivery_status_han
			From DY_ORDER_RETURN R
			Left Outer Join DY_CODE C On C.parent_code = N'ORDER_RETURN_STATUS' And C.code = R.delivery_status
			
			Where return_is_del = N'N'
					And order_idx = N'$order_idx'
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 주문 삭제
	 * @param $order_idx
	 * @param $cs_msg
	 * @return bool
	 */
	public function deleteOrderOne($order_idx, $cs_msg){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장,배송 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_order_progress_step == "ORDER_INVOICE" || $_order_progress_step == "ORDER_SHIPPED"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//현재 주문이 합포 인지 체크
		//합포 주문일 경우 대표 주문을 삭제하는 거라면
		//합포를 분리!!!
		$qry = "
			Select count(*) From DY_ORDER
			Where order_pack_idx in (Select order_pack_idx From DY_ORDER Where order_idx = N'$order_idx')
		";
		$pack_count = parent::execSqlOneCol($qry);

		//합포 주문일 경우
		if($pack_count > 1){

			//대표 주문인지 체크
			$qry = "
				Select order_pack_idx From DY_ORDER Where order_idx = N'$order_idx' 
			";
			$order_pack_idx = parent::execSqlOneCol($qry);

			//대표 주문 이면 합포 분리
			if($order_idx == $order_idx){
				$qry = "
					Update DY_ORDER
						Set order_pack_idx = order_idx
						Where order_pack_idx = N'$order_pack_idx'
				";
				$tmp = parent::execSqlUpdate($qry);
			}
		}

		//주문 삭제 Update
		$qry = "
			Update DY_ORDER
			Set
				order_is_del = N'Y'
			    , order_is_cs_del = N'Y'
				, order_moddate = getdate()
				, order_modip = N'$modip'
			Where
				order_idx = N'$order_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		//주문 매칭 삭제
		$qry = "
			Update DY_ORDER_PRODUCT_MATCHING
			Set
				order_matching_is_del = N'Y'
			    , order_matching_is_cs_del = N'Y'
				, order_matching_moddate = getdate()
				, order_matching_modip = N'$modip'
			Where
				order_idx = N'$order_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "ORDER_DELETE";    //상품교환
		$cs_msg_prefix = "[주문삭제]\n";
		$inserted_cs_idx = $this->insertCS($order_idx, $order_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 주문 삭제 합포 포함 삭제
	 * @param $order_pack_idx
	 * @param $cs_msg
	 * @return bool
	 */
	public function deleteOrderAll($order_pack_idx, $cs_msg){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 송장,배송 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_pack_idx'
		";
		parent::db_connect();
		$_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_order_progress_step == "ORDER_INVOICE" || $_order_progress_step == "ORDER_SHIPPED"){
			$returnValue = false;
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//주문 삭제 Update
		$qry = "
			Update DY_ORDER
			Set
				order_is_del = N'Y'
			    , order_is_cs_del = N'Y'
				, order_moddate = getdate()
				, order_modip = N'$modip'
			Where
				order_pack_idx = N'$order_pack_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		//주문 매칭 삭제
		$qry = "
			Update DY_ORDER_PRODUCT_MATCHING
			Set
				order_matching_is_del = N'Y'
			    , order_matching_is_cs_del = N'Y'
				, order_matching_moddate = getdate()
				, order_matching_modip = N'$modip'
			Where
				order_idx in (Select order_idx From DY_ORDER Where order_pack_idx = N'$order_pack_idx')
		";
		$tmp = parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "ORDER_DELETE";    //상품교환
		$cs_msg_prefix = "[합포삭제]\n";
		$inserted_cs_idx = $this->insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg_prefix.$cs_msg, '', '', null, false, "", "");

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 주문 상품 매칭 정보 삭제
	 * @param $order_idx
	 * @param $order_pack_idx
	 * @param $matching_info_idx
	 * @param $cs_msg
	 * @return bool|resource
	 */
	public function deleteMatchingInfo($order_idx, $order_pack_idx, $matching_info_idx, $cs_msg){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$qry = "
			Update DY_PRODUCT_MATCHING_INFO
			Set 
		        matching_info_is_del = N'Y'
				, matching_info_moddate = getdate()
				, matching_info_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			WHERE matching_info_idx = N'$matching_info_idx'
		";

		parent::db_connect();

		$rst = parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "MATCHING_DELETE";    //매칭삭제
		$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg, '', '', null, false, "", "");

		parent::db_close();

		return $rst;
	}

	/**
	 * 반품예정 설정
	 * @param $order_idx
	 * @param $order_pack_idx
	 * @param $cs_msg
	 * @return bool
	 */
	public function setOrderReturnDueOn($order_idx, $order_pack_idx, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();

		//이미 설정 상태 인지 확인
		$qry = "
			Select order_is_return_due From DY_ORDER Where order_idx = N'$order_idx'
		";
		$order_is_return_due = parent::execSqlOneCol($qry);

		if($order_is_return_due == "Y"){
			$returnValue = false;
			return $returnValue;
		}


		//반품예정 설정!
		$qry = "
			Update DY_ORDER
			Set 
		        order_is_return_due = N'Y'
			    , order_is_return_due_date = getdate()
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			WHERE order_idx = N'$order_idx'
		";
		$rst = parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "RETURN_DUE_ON";    //반품예정 설정
		$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg, '', '', null, false, "", "");

		parent::db_close();

		$returnValue = true;
		return $returnValue;
	}

	/**
	 * 반품예정 해제
	 * @param $order_idx
	 * @param $order_pack_idx
	 * @param $cs_msg
	 * @return bool|resource
	 */
	public function setOrderReturnDueOff($order_idx, $order_pack_idx, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();

		//이미 해제된 상태 인지 확인
		$qry = "
			Select order_is_return_due From DY_ORDER Where order_idx = N'$order_idx'
		";
		$order_is_return_due = parent::execSqlOneCol($qry);

		if($order_is_return_due == "N"){
			$returnValue = false;
			return $returnValue;
		}


		//반품예정 설정!
		$qry = "
			Update DY_ORDER
			Set 
		        order_is_return_due = N'N'
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			WHERE order_idx = N'$order_idx'
		";
		$rst = parent::execSqlUpdate($qry);

		//CS 입력
		$cs_task = "RETURN_DUE_OFF";    //반품예정 설정
		$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg, '', '', null, false, "", "");

		parent::db_close();

		return $rst;
	}

	/**
	 * 우선순위 설정/해제
	 * @param $order_idx
	 * @param $order_pack_idx
	 * @param $product_option_idx
	 * @param $priority_type
	 * @param $position_number
	 * @param $cs_msg
	 * @return bool
	 */
	public function setInvoicePriority($order_idx, $order_pack_idx, $product_option_idx, $priority_type, $position_number, $cs_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		// 접수 상태 인지 체크
		$qry = "
			Select order_progress_step From DY_ORDER  Where order_idx = N'$order_idx'
		";
		parent::db_connect();
		$_order_progress_step = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_order_progress_step == "ORDER_COLLECT"){
			$returnValue = false;
			return $returnValue;
		}

		// 현재 우선순위를 확인하여
		// 설정 또는 해제
		$qry = "Select invoice_priority From DY_ORDER Where order_idx = N'$order_idx'";
		parent::db_connect();
		$invoice_priority = parent::execSqlOneCol($qry);
		parent::db_close();

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($invoice_priority == 0){
			//우선순위 설정
			$cs_task = "PRIORITY_ON";    //우선순위 설정

			$set_invoice_priority = 10;
			//우선순위 타입
			if($priority_type == "top"){
				//최상위

				$qry = "
						Select Max(invoice_priority) 
						From DY_ORDER O
						Where O.order_is_del = N'N' And O.order_progress_step = N'ORDER_ACCEPT'
				";
				$max_priority = parent::execSqlOneCol($qry);

				if($max_priority >= $set_invoice_priority){
					$set_invoice_priority = $max_priority + 1;
				}

			}elseif($priority_type == "bottom"){

				$set_invoice_priority = 1;

			}elseif($priority_type == "position"){

				if((int)$position_number > 1){
					$set_invoice_priority = (int)$position_number - 1;
				}else{
					$set_invoice_priority = 1;
				}
			}

			//합포 대상 모두 설정
			$qry = "
				Update DY_ORDER
				Set
					invoice_priority = N'$set_invoice_priority'
					, invoice_priority_date = getdate()
					, invoice_priority_member_idx = N'$last_member_idx'
					, order_moddate = getdate()
					, order_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where
					order_is_del = N'N'
					And order_pack_idx = N'$order_pack_idx'
			";
			$rst = parent::execSqlUpdate($qry);
		}else{
			//우선순위 해제
			$cs_task = "PRIORITY_OFF";    //우선순위 해제

			$qry = "
				Update DY_ORDER
				Set
					invoice_priority = 0
					, invoice_priority_date = null
					, invoice_priority_member_idx = 0
					, order_moddate = getdate()
					, order_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where
					order_is_del = N'N'
					And order_pack_idx = N'$order_pack_idx'
			";
			$rst = parent::execSqlUpdate($qry);

		}

		//CS 입력
		$inserted_cs_idx = $this->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg, '', '', null, false, "", "");

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * CS 내용 남기기
	 * 이 함수는 트랜잭션 내에서 호출을 위하여
	 * DB Connection Open 및 Close 를 하지 않음.
	 * 반드시 함수 호출 전 DB Connect 가 필요 함
	 * DB Open/Close 가 필요 할 경우 requireDBConnection 인자 값을 True 로 설정 할 것
	 * requireDBConnection 인자의 기본 값은 False
	 * @param $order_idx                : 주문 IDX
	 * @param $order_pack_idx           : 합포 IDX
	 * @param $order_matching_idx       : 주문 매칭 IDX
	 * @param $product_idx              : 상품 IDX
	 * @param $product_option_idx       : 옵션 IDX
	 * @param $cs_is_auto               : CS 자동 입력 여부 (Y/N)
	 * @param $cs_type                  : CS 타입 (CS 작업 상태 값 : 공통코드 (CS_TYPE) 참조)
	 * @param $cs_task                  : CS 작업 (CS 작업 상태 값 : 공통코드 (CS_TASK) 참조)
	 * @param $cs_msg                   : CS 내용
	 * @param $set_alarm                : 알람 설정 여부 (Y/N)
	 * @param $set_alarm_datetime       : 알람 일시 (YYYY-MM-DD hh:mm:ss)
	 * @param $cs_file_array            : 업로드 파일 IDX (Array)
	 * @param $requireDBConnection      : DB Connection 필요 여부 (T/F)
	 * @param $cs_reason_code1          : CS 사유 코드 (상위코드, 공통코드 참조)
	 * @param $cs_reason_code2          : CS 사유 코드 (하위코드, 공통코드 참조)
	 * @return int                      : 삽입된 cs_idx
	 */
	public function insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, $cs_is_auto, $cs_type, $cs_task, $cs_msg, $set_alarm, $set_alarm_datetime, $cs_file_array = array(), $requireDBConnection = false, $cs_reason_code1 = "", $cs_reason_code2 = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		list($cs_file_idx1, $cs_file_idx2, $cs_file_idx3, $cs_file_idx4, $cs_file_idx5) = $cs_file_array;

		if(!$cs_file_idx1) $cs_file_idx1 = 0;
		if(!$cs_file_idx2) $cs_file_idx2 = 0;
		if(!$cs_file_idx3) $cs_file_idx3 = 0;
		if(!$cs_file_idx4) $cs_file_idx4 = 0;
		if(!$cs_file_idx5) $cs_file_idx5 = 0;

		if($requireDBConnection) parent::db_connect();

		$qry = "
			Insert Into DY_ORDER_CS
			(
			 order_idx, order_pack_idx, order_matching_idx, product_idx, product_option_idx, cs_is_auto, 
			 cs_type, cs_task, cs_reason_code1, cs_reason_code2, cs_comment, 
			 cs_file_idx1, cs_file_idx2, cs_file_idx3, cs_file_idx4, cs_file_idx5, member_idx, cs_regip, last_member_idx
			) VALUES 
            (
		      N'$order_idx', 
		      N'$order_pack_idx',
              N'$order_matching_idx',
              N'$product_idx',
              N'$product_option_idx',
              N'$cs_is_auto',
              N'',
              N'$cs_task',
              N'$cs_reason_code1',
              N'$cs_reason_code2',
              N'$cs_msg',
              N'$cs_file_idx1',
              N'$cs_file_idx2',
              N'$cs_file_idx3',
              N'$cs_file_idx4',
              N'$cs_file_idx5',
              N'$last_member_idx',
              N'$modip',
              N'$last_member_idx'
			)
		";
		$cs_idx = parent::execSqlInsert($qry);

		if($cs_idx) {
			//첨부파일 Active
			$C_File = new Files();

			if ($cs_file_idx1) {
				$C_File->updateFileIsUseY($cs_file_idx1, $cs_idx, "cs_file_idx1");
			}
			if ($cs_file_idx2) {
				$C_File->updateFileIsUseY($cs_file_idx2, $cs_idx, "cs_file_idx2");
			}
			if ($cs_file_idx3) {
				$C_File->updateFileIsUseY($cs_file_idx3, $cs_idx, "cs_file_idx3");
			}
			if ($cs_file_idx4) {
				$C_File->updateFileIsUseY($cs_file_idx4, $cs_idx, "cs_file_idx4");
			}
			if ($cs_file_idx5) {
				$C_File->updateFileIsUseY($cs_file_idx5, $cs_idx, "cs_file_idx5");
			}


			//알람 설정
			if ($set_alarm && $set_alarm == "Y") {
				$qry       = "
					Insert Into DY_ORDER_CS_ALARM
					(cs_idx, cs_alarm_datetime, member_idx, cs_alarm_regip, last_member_idx) 
					VALUES 
					(
					 N'$cs_idx',
					 N'$set_alarm_datetime',
					 N'$last_member_idx',
					 N'$modip',
					 N'$last_member_idx'
					)
				";
				$alarm_idx = parent::execSqlInsert($qry);
			}
		}

		if($requireDBConnection) parent::db_close();

		return $cs_idx;
	}

	/**
	 * 외부에서 호출 하는 CS 남기기
	 * db_connection 을 호출 하고 insertCS 함수를 호출 함
	 * @param $order_idx                : 주문 IDX
	 * @param $order_pack_idx           : 합포 IDX
	 * @param $order_matching_idx       : 주문 매칭 IDX
	 * @param $product_idx              : 상품 IDX
	 * @param $product_option_idx       : 옵션 IDX
	 * @param $cs_is_auto               : CS 자동 입력 여부 (Y/N)
	 * @param $cs_type                  : CS 타입 (CS 작업 상태 값 : 공통코드 (CS_TYPE) 참조)
	 * @param $cs_task                  : CS 작업 (CS 작업 상태 값 : 공통코드 (CS_TASK) 참조)
	 * @param $cs_msg                   : CS 내용
	 * @param $set_alarm                : 알람 설정 여부 (Y/N)
	 * @param $set_alarm_datetime       : 알람 일시 (YYYY-MM-DD hh:mm:ss)
	 * @param $cs_file_array            : 업로드 파일 IDX (Array)
	 * @return bool
	 */
	public function insertCSOne($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, $cs_is_auto, $cs_type, $cs_task, $cs_msg, $set_alarm, $set_alarm_datetime, $cs_file_array = array())
	{
		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작
		$return = $this -> insertCS($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, $cs_is_auto, $cs_type, $cs_task, $cs_msg, $set_alarm, $set_alarm_datetime, $cs_file_array);
		if($return) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			$returnValue = false;
		}
		parent::db_close();
		return $returnValue;
	}

	/**
	 * CS 내역 가져오기
	 * @param $order_pack_idx
	 * @return array
	 */
	public function getCSList($order_pack_idx, $cs_task = "", $search_column = "", $search_keyword = "", $order_by = "CS.cs_idx desc")
	{

		$qry = "
			Select
			CS.*
			, C.code_name as cs_task_name
		    , isNull(C2.code_name, '') as cs_reason_text
			, M.member_id, U.name
			, Case When CS.cs_file_idx1 <> 0 THEN (Select user_filename + '|' + save_filename From DY_FILES WITH (NOLOCK) Where file_idx = CS.cs_file_idx1) Else '' End as filename1
			, Case When CS.cs_file_idx2 <> 0 THEN (Select user_filename + '|' + save_filename From DY_FILES WITH (NOLOCK) Where file_idx = CS.cs_file_idx2) Else '' End as filename2
			, Case When CS.cs_file_idx3 <> 0 THEN (Select user_filename + '|' + save_filename From DY_FILES WITH (NOLOCK) Where file_idx = CS.cs_file_idx3) Else '' End as filename3
			, Case When CS.cs_file_idx4 <> 0 THEN (Select user_filename + '|' + save_filename From DY_FILES WITH (NOLOCK) Where file_idx = CS.cs_file_idx4) Else '' End as filename4
			, Case When CS.cs_file_idx5 <> 0 THEN (Select user_filename + '|' + save_filename From DY_FILES WITH (NOLOCK) Where file_idx = CS.cs_file_idx5) Else '' End as filename5
			From DY_ORDER_CS CS WITH (NOLOCK)
			Left Outer Join DY_CODE C WITH (NOLOCK) On C.parent_code = 'CS_TASK' And CS.cs_task = C.code
			Left Outer Join DY_CODE C2 WITH (NOLOCK) On CS.cs_reason_code1 <> '' And CS.cs_reason_code2 <> '' And C2.parent_code = CS.cs_reason_code1 And C2.code = CS.cs_reason_code2
			Left Outer Join DY_MEMBER M WITH (NOLOCK) On M.idx = CS.last_member_idx
			Left Outer Join DY_MEMBER_USER U WITH (NOLOCK) On U.member_idx = M.idx
			Where
				CS.cs_is_del = N'N'
				And 
			      (
			        CS.order_pack_idx = N'$order_pack_idx'
			        Or
			        CS.order_idx = N'$order_pack_idx'
		          )
		";

		if($cs_task){
			$qry .= " And cs_task = N'$cs_task'";
		}

		if(trim($search_keyword) != ""){
			if($search_column == "member_id"){
				$qry .= " And member_id = N'$search_keyword'";
			}elseif($search_column == "cs_comment"){
				$qry .= " And cs_comment like N'%".$search_keyword."%'";
			}
		}

		$qry .= "
			Order by $order_by
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * CS 내역 가져오기
	 * @param $order_pack_idx
	 * @return array
	 */
	public function getCSListSimple($order_pack_idx)
	{

		$qry = "
			Select
			CS.*
			, C.code_name as cs_task_name
		    , isNull(C2.code_name, '') as cs_reason_text
			, M.member_id
			From DY_ORDER_CS CS
			Left Outer Join DY_CODE C On C.parent_code = 'CS_TASK' And CS.cs_task = C.code
			Left Outer Join DY_CODE C2 On CS.cs_reason_code1 <> '' And CS.cs_reason_code2 <> '' And C2.parent_code = CS.cs_reason_code1 And C2.code = CS.cs_reason_code2
			Left Outer Join DY_MEMBER M On M.idx = CS.last_member_idx
			Where
				CS.cs_is_del = N'N'
				And 
			      (
			        CS.order_pack_idx = N'$order_pack_idx'
			        Or
			        CS.order_idx = N'$order_pack_idx'
		          )
			Order by CS.cs_idx desc
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * CS 내역 확인 처리
	 * @param $cs_idx
	 * @return bool|resource
	 */
	public function setCSConfirm($cs_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_ORDER_CS
			Set cs_confirm = N'Y', cs_confirm_date = getdate()
			, cs_moddate = getdate(), cs_modip = N'$modip', last_member_idx = N'$last_member_idx'
			Where cs_idx = N'$cs_idx' And cs_confirm = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * CS 내역 확인 처리 - 주문 관련 전체 CS 내역
	 * @param $order_pack_idx       : 합포 IDX
	 * @param $include_auto         : 자동으로 생성된 CS 포함 여부
	 * @return bool|resource
	 */
	public function setCSConfirmByOrderPackIdx($order_pack_idx, $include_auto){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_ORDER_CS
			Set cs_confirm = N'Y', cs_confirm_date = getdate()
			, cs_moddate = getdate(), cs_modip = N'$modip', last_member_idx = N'$last_member_idx'
			Where order_pack_idx = N'$order_pack_idx' And cs_confirm = N'N'
		";

		//전체보기 상태가 아닐 경우
		if($include_auto != "Y"){
			$qry .= " And cs_is_auto = 'N'  ";
		}

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * CS 개별 삭제
	 * @param $cs_idx
	 * @return bool|resource
	 */
	public function deleteCS($cs_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		//파일 정보 얻기
		$qry = "
			Select cs_file_idx1, cs_file_idx2, cs_file_idx3, cs_file_idx4, cs_file_idx5
			From DY_ORDER_CS
			Where cs_idx = N'$cs_idx'
		";
		parent::db_connect();
		$_file_list = parent::execSqlOneRow($qry);
		parent::db_close();

		//파일 삭제
		$C_Files = new Files();
		foreach($_file_list as $f){
			$C_Files -> deleteFile($f);
		}
		$C_Files = null;

		//CS 삭제
		$qry = "
			Update DY_ORDER_CS
			Set cs_is_del = N'Y'
			, cs_moddate = getdate(), cs_modip = N'$modip', last_member_idx = N'$last_member_idx'
			Where cs_idx = N'$cs_idx' And cs_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * CS 알람 내역 가져오기
	 * @return array
	 */
	public function getMyAlarmList(){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$current_minute = Floor(date("i") / 10);

		$search_time_start = date("Y-m-d H:"). $current_minute . "0:00";


		$qry = "
			Select A.*
	        , CS.cs_comment
	        , CS.cs_type
	        , CS.cs_task
			, C.code_name as cs_task_name
			, M.member_id
			From DY_ORDER_CS_ALARM A
				Inner Join DY_ORDER_CS CS On A.cs_idx = CS.cs_idx
				Left Outer Join DY_CODE C On C.parent_code = 'CS_TASK' And CS.cs_task = C.code
				Left Outer Join DY_MEMBER M On M.idx = CS.last_member_idx
			Where 
		        A.cs_alarm_confirm = N'N' 
			    And CS.cs_is_del = N'N'
				And A.member_idx = N'$last_member_idx'
				And A.cs_alarm_datetime = convert(datetime, '$search_time_start');
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

	    return $_list;
	}

	/**
	 * CS 알람 확인 처리
	 * @param $cs_alarm_idx
	 */
	public function clearMyAlarm($cs_alarm_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_ORDER_CS_ALARM
			Set cs_alarm_confirm = N'Y'
				, cs_alarm_moddate = getdate(), cs_alarm_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where cs_alarm_idx = N'$cs_alarm_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
	}

	/**
	 * 회수 - 받는 분 주소록 목록
	 * @return array
	 */
	public function getAddressBookList()
	{
		$qry = "
			Select * From DY_ORDER_RETURN_ADDRESS_BOOK
			Where address_is_del = N'N'
			Order by address_idx asc
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 회수 - 주소록 추가
	 * @param $address_name
	 * @param $address_tel_num
	 * @param $address_hp_num
	 * @param $address_zipcode
	 * @param $address_address
	 * @return int
	 */
	public function addAddressBook($address_name, $address_tel_num, $address_hp_num, $address_zipcode, $address_address)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_ORDER_RETURN_ADDRESS_BOOK
			(
			  address_name, address_tel_num, address_hp_num, 
			  address_zipcode, address_address, 
			  address_regdate, address_regip, last_member_idx
		    )
			VALUES
			(
			 N'$address_name',
			 N'$address_tel_num',
			 N'$address_hp_num',
			 N'$address_zipcode',
			 N'$address_address',
			 getdate(), 
			 N'$modip',
			 N'$last_member_idx'
			)
		";

		parent::db_connect();
		$_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $_idx;
	}

	/**
	 * 회수 - 주소록 수정
	 * @param $address_idx
	 * @param $address_name
	 * @param $address_tel_num
	 * @param $address_hp_num
	 * @param $address_zipcode
	 * @param $address_address
	 * @return bool|resource
	 */
	public function updateAddressBook($address_idx, $address_name, $address_tel_num, $address_hp_num, $address_zipcode, $address_address)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update
				DY_ORDER_RETURN_ADDRESS_BOOK
			Set
			    address_name = N'$address_name', 
			    address_tel_num = N'$address_tel_num', 
			    address_hp_num = N'$address_hp_num', 
			    address_zipcode = N'$address_zipcode', 
			    address_address = N'$address_address', 
			    address_moddate = getdate(), 
			    address_modip = N'$modip', 
			    last_member_idx = N'$last_member_idx'
			WHERE address_idx = N'$address_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 회수 - 주소록 삭제
	 * @param $address_idx
	 * @return bool|resource
	 */
	public function deleteAddressBook($address_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update
				DY_ORDER_RETURN_ADDRESS_BOOK
			Set
			    address_is_del = N'Y',
			    address_moddate = getdate(), 
			    address_modip = N'$modip', 
			    last_member_idx = N'$last_member_idx'
			WHERE address_idx = N'$address_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 합포 개수 구하기
	 * @param $order_pack_idx
	 * @return int|mixed
	 */
	public function getOrderPackCount($order_pack_idx){
		$qry = "
			Select count(*) From DY_ORDER
			Where order_pack_idx = N'$order_pack_idx'
		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		return $cnt;
	}

	/**
	 * !! 정산 테이블 입력 함수 - For 주문 취소
	 * @param $order_matching_idx
	 * @param string $cs_reason_code2
	 * @param bool $requireDbConnection
	 * @param bool $isChange : 교환일 경우 true
     * @param bool $ShippedCancel : 배송취소일 경우 true
	 */
	public function insertSettleCancel($order_matching_idx, $cs_reason_code2 = "", $requireDbConnection = false, $isChange = false, $ShippedCancel = false){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		if($requireDbConnection){
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작
		}


		//정산데이터 불러오기
		//기존에는 배송 후 취소 일 경우에만 정산 테이블 입력 이었음.
		//And M.product_cancel_shipped = N'Y'
		//위탁 상품으로 인하여 해당 조건은 제외 됨.
		//우선 모두 불러온 뒤 자체 인지 위탁 인지에 따라 해당 조건과 병렬 조건화 하여 정산 테이블에 입력
		$qry = "
		
			Select
			O.*
	        , SELLER.seller_type, SELLER.vendor_use_charge
		    , Case When SELLER.seller_type = 'VENDOR_SELLER' THEN 
		        (Select vendor_grade From DY_MEMBER_VENDOR VENDOR Where VENDOR.member_idx = O.seller_idx)
		      Else '' End as vendor_grade
			, M.order_matching_idx
			, M.product_option_cnt,  M.product_option_sale_price , M.product_cancel_shipped, M.product_change_shipped
		    , M.product_option_purchase_price, M.product_option_sale_price, M.order_cs_status, M.product_calculation_amt
			, isNull(S.stock_idx, 0) as stock_idx, isNull(S.stock_unit_price, 0) as stock_unit_price, S.stock_amount
		    , P.product_tax_type, P.product_delivery_fee_sale, P.product_delivery_fee_buy, P.product_sale_type
			, P.product_name, PO.product_option_name, P.product_idx, PO.product_option_idx
		    , P.product_category_l_idx, P.product_category_m_idx
			, PO.product_option_sale_price_A, PO.product_option_sale_price_B, PO.product_option_sale_price_C, PO.product_option_sale_price_D, PO.product_option_sale_price_E
			, SUPPLIER.member_idx as supplier_idx, SUPPLIER.supplier_use_prepay
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_STOCK S 
			        On S.order_idx = O.order_idx 
		                And S.product_option_idx = M.product_option_idx 
			             And S.stock_status = N'SHIPPED' 
			             And S.stock_type = 1 
			             And S.stock_amount > 0
			             AND S.order_matching_idx = N'$order_matching_idx'
			    Inner Join DY_SELLER SELLER On SELLER.seller_idx = O.seller_idx
				Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
			    Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = P.supplier_idx
				Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
			WHERE 
				O.order_is_del = N'N'
				And M.order_matching_is_del='N' 
				And (
					S.order_idx is null
					Or (S.stock_is_del = N'N' And S.stock_is_confirm = N'Y')
				)
				And M.order_matching_idx = N'$order_matching_idx'

			Order by O.order_pack_idx ASC, O.order_idx ASC, M.order_matching_idx ASC
		";

		$_order_list = parent::execSqlList($qry);

		if($_order_list){
			$prev_order_idx = 0;
			$prev_order_pack_idx = 0;

			foreach ($_order_list as $ord){

				$product_cancel_shipped         = $ord["product_cancel_shipped"];

				$settle_type = "CANCEL";   //정산 타입 - 취소

				$order_idx                       = $ord["order_idx"];                               //관리번호
				$order_pack_idx                  = $ord["order_pack_idx"];                          //합포번호
				$order_cs_status                 = $ord["order_cs_status"];                         //CS상태
				$order_progress_step_accept_date = $ord["order_progress_step_accept_date"];         //발주일 [접수일]
				$settle_date                     = date('Y-m-d');                           //발주일 Y-m-d
				$seller_idx                      = $ord["seller_idx"];                              //판매처
				$seller_type                     = $ord["seller_type"];                             //판매처 타입
				$vendor_grade                    = $ord["vendor_grade"];                            //벤더사 등급
				$supplier_idx                    = $ord["supplier_idx"];                            //공급처
				$vendor_use_charge               = $ord["vendor_use_charge"];                       //벤더사 판매처 충전금 사용 여부 (Y/N)
				$supplier_use_prepay             = $ord["supplier_use_prepay"];                     //공급처 선급금 사용 여부 (Y/N)
				$market_order_no                 = $ord["market_order_no"];                         //마켓 주문번호
				$market_product_no               = $ord["market_product_no"];                       //마켓 상품 번호
				$market_product_name             = $ord["market_product_name"];                     //마켓 상품 명
				$market_product_option           = $ord["market_product_option"];                   //마켓 옵션 명
				$order_unit_price                = $ord["order_unit_price"];                        //판매단가
				$order_amt                       = $ord["order_amt"];                               //판매가
				$order_cnt                       = $ord["order_cnt"];                               //판매수량
				$delivery_fee                    = $ord["delivery_fee"];                            //배송비
				$delivery_type                   = $ord["delivery_type"];                           //배송비 정산구분 (선불/착불/선결제 등)
				$delivery_is_free                = $ord["delivery_is_free"];                        //배송비 정산구분 (선불:Y/착불:N)
				$product_idx                     = $ord["product_idx"];                             //상품코드
				$product_name                    = $ord["product_name"];                            //상품명
				$product_option_idx              = $ord["product_option_idx"];                      //옵션코드
				$product_option_name             = $ord["product_option_name"];                     //옵션명
				$product_option_cnt              = $ord["product_option_cnt"];                      //상품수량
				$product_tax_type                = $ord["product_tax_type"];                        //상품 세금 종류
				$product_tax_ratio               = ($product_tax_type == "TAXATION") ? 10 : 0;      //상품 대상세율 [과세 일 경우에만 10%]
				$product_sale_type               = $ord["product_sale_type"];                       //상품 판매 방식 (사입/위탁)
				$stock_idx                       = $ord["stock_idx"];                               //연결된 재고 코드
				$stock_unit_price                = $ord["stock_unit_price"];                        //연결된 재고 가격
				$stock_amount                    = $ord["stock_amount"];                            //연결된 재고 수량 [원가가 다른 재고가 한주문에 연결될 수 있음]
				$product_option_purchase_price   = $ord["product_option_purchase_price"];           //옵션 매입단가 - DY
				$product_option_sale_price       = $ord["product_option_sale_price"];               //판매단가 2 - DY 기준
				$product_delivery_fee_sale       = $ord["product_delivery_fee_sale"];               //상품 매출배송비
				$product_delivery_fee_buy        = $ord["product_delivery_fee_buy"];                //상품 매입배송비
				$product_calculation_amt         = $ord["product_calculation_amt"];                 //상품 정산예정금액

				$product_category_l_idx          = $ord["product_category_l_idx"];
				$product_category_m_idx          = $ord["product_category_m_idx"];

				$invoice_date                    = $ord["invoice_date"];
				$shipping_date                   = $ord["shipping_date"];
				$cancel_date                     = $ord["invoice_date"];

				$order_matching_idx              = $ord["order_matching_idx"];

				$settle_sale_supply                = 0;     //매출공급가액
				$settle_sale_supply_ex_vat         = 0;     //매출공급가액 부가세제외
				$settle_sale_commission_ex_vat     = 0;     //판매수수료 부가세 별도
				$settle_sale_commission_in_vat     = 0;     //판매수수료 부가세 포함
				$settle_delivery_in_vat            = 0;     //배송비 부가세 포함
				$settle_delivery_ex_vat            = 0;     //배송비 부가세 별도
				$settle_delivery_commission_ex_vat = 0;     //판매배송비 수수료 부가세 별도
				$settle_delivery_commission_in_vat = 0;     //판매배송비 수수료 부가세 포함
				$settle_purchase_supply            = 0;     //매입가(매출원가) 공급가액
				$settle_purchase_supply_ex_vat     = 0;     //매입가(매출원가) 공급가액 부가세 별도
				$settle_purchase_delivery_in_vat   = 0;     //매입 배송비 부가세 포함
				$settle_purchase_delivery_ex_vat   = 0;     //매입 배송비 부가세 별도
				$settle_sale_profit                = 0;     //매출 이익
				$settle_sale_amount                = 0;     //매출액
				$settle_sale_cost                  = 0;     //매출원가

				$settle_sale_sum                   = 0;     //매출합계
				$settle_purchase_sum               = 0;     //매입합계


				//자체/사입 상품 일 경우 배송 후 취소일 경우에만 정산 테이블 입력
				//위탁 상품 일 경우 배송 후 취소 여부와 상관 없이 정산 테이블 입력
                //배송취소일경우 자체/사입 제품일 경우에만 정산 테이블 입력
                if(!$ShippedCancel) {
                    if ($product_sale_type == "SELF" And $product_cancel_shipped == "N") {
                        continue;
                    }
                } else if ($ShippedCancel){
                    if ($product_sale_type != "SELF"){
                        continue;
                    }
                }

				//취소 이기 때문에 수량 * -1

				$order_amt = $order_amt * -1;
				$product_option_cnt = $product_option_cnt * -1;

				$commission = 0;            //수수료 (마켓)
				$delivery_commission = 0;   //배송비 수수료 (마켓)
				if($seller_type == "MARKET_SELLER" || $seller_type == "CUSTOM_SELLER"){
					$_market_commission = $this->getSettleCommission($seller_idx, $product_idx, $product_option_idx);
					$commission = $_market_commission["market_commission"];            //수수료 (마켓)
					$delivery_commission = $_market_commission["delivery_commission"];   //배송비 수수료 (마켓)
				}

				$pdt = new Product();
				$data = $pdt->getSpecialSaleData($ord["seller_idx"], $ord["product_option_idx"]);
				if($data) {
					$product_option_sale_price = $data["sale_unit_price"];
					$order_unit_price = $product_option_sale_price;
					$order_amt = $product_option_sale_price * $product_option_cnt;
				}

				//벤더사 판매처라면 판매가 및 판매단가는 매칭 상품의 판매단가로 대체
				if($seller_type == "VENDOR_SELLER"){
					$order_unit_price = $product_option_sale_price;

					//변경!! 주문금액은 배송비를 포함하지 않음
					//$order_amt = ($order_unit_price * $product_option_cnt) + $delivery_fee;
					$order_amt = $order_unit_price * $product_option_cnt;

				}else{

					//판매단가 = (판매금액 - 배송비) / 수량
					//소수점 버림
					//변경!! 주문금액은 배송비를 포함하지 않음
					//$order_unit_price = $product_option_sale_price = floor(($order_amt - $delivery_fee) / $product_option_cnt);
					//$order_unit_price = $product_option_sale_price = floor($order_amt / $product_option_cnt);
					//$order_unit_price = $product_option_sale_price = floor($product_option_sale_price / $product_option_cnt);
					//매칭 시 주문 금액으로 개별 판매단가를 저장 했으므로 해당 금액을 불러옴 - 19.04.25
					$order_amt = $product_option_sale_price * $product_option_cnt;
					$order_unit_price = $product_option_sale_price * -1;

					//벤더사 판매처가 아니라면
					//두번째 매칭 상품 부터는 판매단가 및 판매가 공란
					//
					//같은 주문에 여러의 상품이 매칭된 경우
					//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
					if($prev_order_idx == $order_idx) {
						$order_unit_price = 0;
						$order_amt        = 0;
					}
				}

				if($data) $delivery_fee = $data["sale_delivery_fee"];

				//자체 상품이면 매입단가를 재고매입단가로 Update
				//재고 수량으로 대체
				//취소이기 때문에 * -1
				//취소 일 때는 재고 수량으로 대체하지 않는다!!!
				//부분취소 일 경우 재고 수량 만큼 취소하게 되면 전체 취소가 되어 버림
				//아래 if 문 중 $product_option_cnt 부분 삭제(주석처리)
				if($product_sale_type == "SELF")
				{
					$product_option_purchase_price = $stock_unit_price;
					//취소or복귀 시에는 재고 수량을 따르지 않는다 - 19.04.25
					//$product_option_cnt = $stock_amount * -1;
				}

				//관리번호와 합포번호가 다르면 - 합포된 주문
				if($prev_order_pack_idx == $order_pack_idx || $order_idx != $order_pack_idx){
					$delivery_fee = 0;  //배송비
					$product_delivery_fee_buy = 0; //매입배송비
				}

				//같은 주문에 여러의 상품이 매칭된 경우
				//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
				//위 두 경우는 하나의 주문이라도 정산 테이블에 여러번 입력 된다.
				//두번째 입력 될 때부터는....
				if($prev_order_idx == $order_idx){
					$delivery_fee = 0;  //배송비
					$product_delivery_fee_buy = 0; //매입배송비
				}

				//취소 : 배송비, 매입배송비 x -1
				$delivery_fee = $delivery_fee * -1;
				$product_delivery_fee_buy = $product_delivery_fee_buy * -1;

				//매출공급가액 - 변경!! 주문금액은 배송비를 포함하지 않음
				//$settle_sale_supply = $order_amt - $delivery_fee;
				$settle_sale_supply = $order_amt;
				//매출공급가액 부가세 제외금액
				$settle_sale_supply_ex_vat = round($settle_sale_supply / (($product_tax_ratio/100) + 1));

				//!!!!19.04.25 정산예정금 관련 내용 추가!!!!
				//20.02.26 변경 수수료 ex 스왑
				//정산예정금액이 있으면 정산예정금액을 제외한 나머지 금액이 판매수수료가 됨
				//없으면 기존 대로 수수료관리에 등록된 내용으로 계산
				if($product_calculation_amt > 0){
					//정산예정금이 있다면
					//판매수수료 부가세포함 [매출공급가액 - 정산예정금(상품 정산예정금 * 상품 개수)]
					$settle_sale_commission_in_vat = $settle_sale_supply - ($product_calculation_amt * $product_option_cnt);
					//판매수수료 부가세별도 [판매수수료 부가세별도 / 1.1]
					$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
				}else {
					//수수료가 있다면
					//판매수수료 부가세포함 [매출공급가액 * 판매수수료]
					$settle_sale_commission_in_vat = round($settle_sale_supply * ($commission / 100));
					//판매수수료 부가세별도 [판매수수료 부가세별도 * 1.1]
					$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
				}

				//배송비 부가세포함
				$settle_delivery_in_vat = $delivery_fee;
				//배송비 부가세별도
				$settle_delivery_ex_vat = round($settle_delivery_in_vat / 1.1);
				//20.02.26 변경 판매배송비수수료 ex 스왑
				//판매배송비수수료 부가세포함 [배송비 부가세포함 - 배송비수수료]
				$settle_delivery_commission_in_vat = round($settle_delivery_in_vat * ($delivery_commission/100));
				//판매배송비수수료 부가세별도
				$settle_delivery_commission_ex_vat = round($settle_delivery_commission_in_vat / 1.1);
				//매입단가(매출원가) 공급가액
				$settle_purchase_unit_supply = $product_option_purchase_price;
				//매입단가(매출원가) 공급가액 부가세 별도 [매입단가(매출원가) 공급가액 / 1.1]
				$settle_purchase_unit_supply_ex_vat = round($product_option_purchase_price / (($product_tax_ratio/100) + 1));
				//매입가(매출원가) 공급가액
				$settle_purchase_supply = $product_option_purchase_price * $product_option_cnt;
				//매입가(매출원가) 공급가액 부가세 별도 [매입가(매출원가) 공급가액 - 상품 대상세금]
				$settle_purchase_supply_ex_vat = round($settle_purchase_supply / (($product_tax_ratio/100) + 1));
				//매입 배송비 부가세 포함
				$settle_purchase_delivery_in_vat = $product_delivery_fee_buy;
				//매입 배송비 부가세 별도 [매입 배송비 부가세 포함 / 1.1];
				$settle_purchase_delivery_ex_vat   = round($settle_purchase_delivery_in_vat / 1.1);

				//매출이익 = 매출공급가액[부X] - 수수료[판매수수료 부X] + 배송비[부X] - 배송비 수수료[판매배송비수수료 부X] - 매입가[부X] - 매입배송비[부X]
				$settle_sale_profit = $settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat - $settle_purchase_supply_ex_vat - $settle_purchase_delivery_ex_vat;

				//매출액 = 매출공급가액 + 배송비 - 판매수수료[부X] - 판매배송비수수료[부X]
				$settle_sale_amount = $settle_sale_supply + $settle_delivery_in_vat - $settle_sale_commission_ex_vat - $settle_delivery_commission_ex_vat;

				//매출원가 = 매입가(매출원가) 공급가액[부X] + 매입배송비[부X]
				$settle_sale_cost = $settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat;

				//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
				$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
				//매입합계 (매입가 + 매입배송비)
				$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

				$qry = "
					Insert Into DY_SETTLE
					(
						settle_date, settle_type, order_idx, order_pack_idx, order_cs_status, order_progress_step_accept_date
						, seller_idx, supplier_idx, vendor_grade, vendor_use_charge, supplier_use_prepay
						, cs_reason_cancel
						, market_order_no, market_product_no, market_product_name, market_product_option
						, order_unit_price, order_amt, order_cnt
						, commission, delivery_commisision
						, delivery_fee, delivery_type, delivery_is_free
						, order_matching_idx
						, product_idx, product_name, product_option_idx, product_option_name, product_option_cnt, product_sale_type, product_tax_type
						, product_option_sale_price, product_option_purchase_price, product_delivery_fee_sale, product_delivery_fee_buy
						, product_category_l_idx, product_category_m_idx
						, stock_idx
						, settle_sale_supply, settle_sale_supply_ex_vat, settle_sale_commission_ex_vat, settle_sale_commission_in_vat
						, settle_delivery_in_vat, settle_delivery_ex_vat, settle_delivery_commission_ex_vat, settle_delivery_commission_in_vat
						, settle_purchase_supply, settle_purchase_supply_ex_vat, settle_purchase_delivery_in_vat, settle_purchase_delivery_ex_vat
						, settle_sale_profit, settle_sale_amount, settle_sale_cost
						, settle_purchase_unit_supply, settle_purchase_unit_supply_ex_vat
						, settle_sale_sum, settle_purchase_sum
						, settle_regip, last_member_idx
					) 
					VALUES 
					(
					 N'$settle_date'
					 , N'$settle_type'
					 , N'$order_idx'
					 , N'$order_pack_idx'
					 , N'$order_cs_status'
					 , N'$order_progress_step_accept_date'
					 , N'$seller_idx'
					 , N'$supplier_idx'
					 , N'$vendor_grade'
					 , N'$vendor_use_charge'
					 , N'$supplier_use_prepay'
					 , N'$cs_reason_code2'
					 , N'$market_order_no'
					 , N'$market_product_no'
					 , N'$market_product_name'
					 , N'$market_product_option'
					 , N'$order_unit_price'
					 , N'$order_amt'
					 , N'$product_option_cnt'
					 , N'$commission'
					 , N'$delivery_commission'
					 , N'$delivery_fee'
					 , N'$delivery_type'
					 , N'$delivery_is_free'
					 , N'$order_matching_idx'
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
					 , N'$product_category_l_idx'
					 , N'$product_category_m_idx'
					 , N'$stock_idx'
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
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";

				$inserted_idx = parent::execSqlInsert($qry);

				$prev_order_idx = $order_idx;
				$prev_order_pack_idx = $order_pack_idx;
			}
		}

		if($requireDbConnection){
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}
	}

	/**
	 * !! 복귀 시 정산 테이블 입력 함수
	 * @param $order_pack_idx
	 * @param bool $forConsignmentProduct : 위탁 상품만 입력 할 경우 true
	 * @param bool $requireDbConnection
	 * @param string $order_matching_idx : 주문 단위가 아닌 상품 단위의 정산 입력할 경우에 매칭 IDX 를 입력한다.
	 * @param bool $isChange                : 교환 일 경우 true
	 */
	public function insertSettleShipped($order_pack_idx, $forConsignmentProduct = false ,$requireDbConnection = false, $order_matching_idx = "", $isChange = false)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		if($requireDbConnection){
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작
		}

		$keyWhereQry = " And O.order_pack_idx = N'$order_pack_idx' And M.order_cs_status = N'NORMAL' ";

		//위탁상품 전용 정산입력이라면
		if($forConsignmentProduct){
			$keyWhereQry = " And O.order_idx = N'$order_pack_idx' ";
			$forConsignmentQry = " And P.product_sale_type = N'CONSIGNMENT'";
		}

		//교환 등과 같이 상품 단위 정산 입력 이라면
		//주문번호가 아닌 매칭IDX를 가지고 정산입력 한다.
		//위탁상품 전용 입력 무시
		if($order_matching_idx){
			$keyWhereQry = " And M.order_matching_idx = N'$order_matching_idx' ";
		}

		//정산데이터 불러오기
		$qry = "
		
			Select
			O.*
	        , SELLER.seller_type, SELLER.vendor_use_charge
		    , Case When SELLER.seller_type = 'VENDOR_SELLER' THEN 
		        (Select vendor_grade From DY_MEMBER_VENDOR VENDOR Where VENDOR.member_idx = O.seller_idx)
		      Else '' End as vendor_grade
		    , M.order_matching_idx
			, M.product_option_cnt,  M.product_option_sale_price , M.product_cancel_shipped, M.product_change_shipped
		    , M.product_option_purchase_price, M.product_option_sale_price, M.order_cs_status, M.product_calculation_amt
			, isNull(S.stock_idx, 0) as stock_idx, isNull(S.stock_unit_price, 0) as stock_unit_price, S.stock_amount
		    , P.product_tax_type, P.product_delivery_fee_sale, P.product_delivery_fee_buy, P.product_sale_type
			, P.product_name, PO.product_option_name, P.product_idx, PO.product_option_idx
		    , P.product_category_l_idx, P.product_category_m_idx
			, PO.product_option_sale_price_A, PO.product_option_sale_price_B, PO.product_option_sale_price_C, PO.product_option_sale_price_D, PO.product_option_sale_price_E
			, SUPPLIER.member_idx as supplier_idx, SUPPLIER.supplier_use_prepay
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING M On O.order_idx = M.order_idx
				Left Outer Join DY_STOCK S 
				  On S.order_idx = O.order_idx 
				       And S.product_option_idx = M.product_option_idx 
				       And S.stock_status = N'SHIPPED' 
				       And S.stock_type = 1 
				       And S.stock_amount > 0
			    Inner Join DY_SELLER SELLER On SELLER.seller_idx = O.seller_idx
				Left Outer Join DY_PRODUCT P On P.product_idx = M.product_idx
			    Left Outer Join DY_MEMBER_SUPPLIER SUPPLIER On SUPPLIER.member_idx = P.supplier_idx
				Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = M.product_option_idx
			WHERE 
				O.order_is_del = N'N'
				And M.order_matching_is_del='N' 
			   
				And (
					S.order_idx is null
					Or (S.stock_is_del = N'N' And S.stock_is_confirm = N'Y')
				)
				$keyWhereQry
				$forConsignmentQry

			Order by O.order_pack_idx ASC, O.order_idx ASC, M.order_matching_idx ASC
		";

		$_order_list = parent::execSqlList($qry);

		if($_order_list){
			$prev_order_idx = 0;
			$prev_order_pack_idx = 0;
			foreach ($_order_list as $ord){

				$settle_type = "SHIPPED";   //정산 타입 - 배송
				if($isChange){
					$settle_type = "EXCHANGE";   //정산 타입 - 교환
				}

				$order_idx                       = $ord["order_idx"];                               //관리번호
				$order_pack_idx                  = $ord["order_pack_idx"];                          //합포번호
				$order_cs_status                 = $ord["order_cs_status"];                         //CS상태
				$order_progress_step_accept_date = $ord["order_progress_step_accept_date"];         //발주일 [접수일]
				$settle_date                     = date('Y-m-d');                           //발주일 Y-m-d
				$seller_idx                      = $ord["seller_idx"];                              //판매처
				$seller_type                     = $ord["seller_type"];                             //판매처 타입
				$vendor_grade                    = $ord["vendor_grade"];                            //벤더사 등급
				$supplier_idx                    = $ord["supplier_idx"];                            //공급처
				$vendor_use_charge               = $ord["vendor_use_charge"];                       //벤더사 판매처 충전금 사용 여부 (Y/N)
				$supplier_use_prepay             = $ord["supplier_use_prepay"];                     //공급처 선급금 사용 여부 (Y/N)
				$market_order_no                 = $ord["market_order_no"];                         //마켓 주문번호
				$market_product_no               = $ord["market_product_no"];                       //마켓 상품 번호
				$market_product_name             = $ord["market_product_name"];                     //마켓 상품 명
				$market_product_option           = $ord["market_product_option"];                   //마켓 옵션 명
				$order_unit_price                = $ord["order_unit_price"];                        //판매단가
				$order_amt                       = $ord["order_amt"];                               //판매가
				$order_cnt                       = $ord["order_cnt"];                               //판매수량
				$delivery_fee                    = $ord["delivery_fee"];                            //배송비
				$delivery_type                   = $ord["delivery_type"];                           //배송비 정산구분 (선불/착불/선결제 등)
				$delivery_is_free                = $ord["delivery_is_free"];                        //배송비 정산구분 (선불:Y/착불:N)
				$product_idx                     = $ord["product_idx"];                             //상품코드
				$product_name                    = $ord["product_name"];                            //상품명
				$product_option_idx              = $ord["product_option_idx"];                      //옵션코드
				$product_option_name             = $ord["product_option_name"];                     //옵션명
				$product_option_cnt              = $ord["product_option_cnt"];                      //상품수량
				$product_tax_type                = $ord["product_tax_type"];                        //상품 세금 종류
				$product_tax_ratio               = ($product_tax_type == "TAXATION") ? 10 : 0;      //상품 대상세율 [과세 일 경우에만 10%]
				$product_sale_type               = $ord["product_sale_type"];                       //상품 판매 방식 (사입/위탁)
				$stock_idx                       = $ord["stock_idx"];                               //연결된 재고 코드
				$stock_unit_price                = $ord["stock_unit_price"];                        //연결된 재고 가격
				$stock_amount                    = $ord["stock_amount"];                            //연결된 재고 수량 [원가가 다른 재고가 한주문에 연결될 수 있음]
				$product_option_purchase_price   = $ord["product_option_purchase_price"];           //옵션 매입단가 - DY
				$product_option_sale_price       = $ord["product_option_sale_price"];               //판매단가 2 - DY 기준
				$product_delivery_fee_sale       = $ord["product_delivery_fee_sale"];               //상품 매출배송비
				$product_delivery_fee_buy        = $ord["product_delivery_fee_buy"];                //상품 매입배송비
				$product_calculation_amt         = $ord["product_calculation_amt"];                 //상품 정산예정금액

				$product_category_l_idx          = $ord["product_category_l_idx"];
				$product_category_m_idx          = $ord["product_category_m_idx"];

				$invoice_date                    = $ord["invoice_date"];
				$shipping_date                   = $ord["shipping_date"];
				$cancel_date                     = $ord["invoice_date"];

				$order_matching_idx              = $ord["order_matching_idx"];

				$settle_sale_supply                = 0;     //매출공급가액
				$settle_sale_supply_ex_vat         = 0;     //매출공급가액 부가세제외
				$settle_sale_commission_ex_vat     = 0;     //판매수수료 부가세 별도
				$settle_sale_commission_in_vat     = 0;     //판매수수료 부가세 포함
				$settle_delivery_in_vat            = 0;     //배송비 부가세 포함
				$settle_delivery_ex_vat            = 0;     //배송비 부가세 별도
				$settle_delivery_commission_ex_vat = 0;     //판매배송비 수수료 부가세 별도
				$settle_delivery_commission_in_vat = 0;     //판매배송비 수수료 부가세 포함
				$settle_purchase_supply            = 0;     //매입가(매출원가) 공급가액
				$settle_purchase_supply_ex_vat     = 0;     //매입가(매출원가) 공급가액 부가세 별도
				$settle_purchase_delivery_in_vat   = 0;     //매입 배송비 부가세 포함
				$settle_purchase_delivery_ex_vat   = 0;     //매입 배송비 부가세 별도
				$settle_sale_profit                = 0;     //매출 이익
				$settle_sale_amount                = 0;     //매출액
				$settle_sale_cost                  = 0;     //매출원가

				$settle_sale_sum                   = 0;     //매출합계
				$settle_purchase_sum               = 0;     //매입합계

				$commission = 0;            //수수료 (마켓)
				$delivery_commission = 0;   //배송비 수수료 (마켓)
				if($seller_type == "MARKET_SELLER" || $seller_type == "CUSTOM_SELLER"){
					$_market_commission = $this->getSettleCommission($seller_idx, $product_idx, $product_option_idx);
					$commission = $_market_commission["market_commission"];            //수수료 (마켓)
					$delivery_commission = $_market_commission["delivery_commission"];   //배송비 수수료 (마켓)
				}

				//벤더사 판매처라면 판매가 및 판매단가는 매칭 상품의 판매단가로 대체
				if($seller_type == "VENDOR_SELLER"){
					$order_unit_price = $product_option_sale_price;

					//변경!! 주문금액은 배송비를 포함하지 않음
					//$order_amt = ($order_unit_price * $product_option_cnt) + $delivery_fee;
					$order_amt = ($order_unit_price * $product_option_cnt);

					//벤더사 일 경우 매출배송비는 상품에 기록된 금액으로 대체
					$delivery_fee = $product_delivery_fee_sale;
				}else{

					//판매단가 = (판매금액 - 배송비) / 수량
					//소수점 버림
					//변경!! 주문금액은 배송비를 포함하지 않음
					//$order_unit_price = $product_option_sale_price = floor(($order_amt - $delivery_fee) / $product_option_cnt);
					//매칭 판매 단가는 매칭 시에 설정되므로 따로 수정하지 않는다.
					//$order_unit_price = $product_option_sale_price = floor($order_amt / $product_option_cnt);
					//$order_unit_price = floor($order_amt / $product_option_cnt);
					//매칭 시 주문 금액으로 개별 판매단가를 저장 했으므로 해당 금액을 불러옴 - 19.04.25
					$order_amt = $product_option_sale_price * $product_option_cnt;
					$order_unit_price = $product_option_sale_price;


					//벤더사 판매처가 아니라면
					//두번째 매칭 상품 부터는 판매단가 및 판매가 공란
					//
					//같은 주문에 여러의 상품이 매칭된 경우
					//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
					if($prev_order_idx == $order_idx) {
						$order_unit_price = 0;
						$order_amt        = 0;
					}
				}

				//자체 상품이면 매입단가를 재고매입단가로 Update
				//재고 수량으로 대체
				if($product_sale_type == "SELF")
				{
					$product_option_purchase_price = $stock_unit_price;
					//취소or복귀 시에는 재고 수량을 따르지 않는다 - 19.04.25
					//$product_option_cnt = $stock_amount;
				}

				//관리번호와 합포번호가 다르면 - 합포된 주문
				if($prev_order_pack_idx == $order_pack_idx || $order_idx != $order_pack_idx){
					$delivery_fee = 0;  //배송비
					$product_delivery_fee_buy = 0; //매입배송비
				}

				//같은 주문에 여러의 상품이 매칭된 경우
				//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
				//위 두 경우는 하나의 주문이라도 정산 테이블에 여러번 입력 된다.
				//두번째 입력 될 때부터는....
				if($prev_order_idx == $order_idx){
					$delivery_fee = 0;  //배송비
					$product_delivery_fee_buy = 0; //매입배송비
				}

				//매출공급가액 - 변경!! 주문금액은 배송비를 포함하지 않음
				//$settle_sale_supply = $order_amt - $delivery_fee;
				$settle_sale_supply = $order_amt;
				//매출공급가액 부가세 제외금액
				$settle_sale_supply_ex_vat = round($settle_sale_supply / (($product_tax_ratio/100) + 1));

				//!!!!19.04.25 정산예정금 관련 내용 추가!!!!
				//20.02.26 변경 수수료 ex 스왑
				//정산예정금액이 있으면 정산예정금액을 제외한 나머지 금액이 판매수수료가 됨
				//없으면 기존 대로 수수료관리에 등록된 내용으로 계산
				if($product_calculation_amt > 0){
					//정산예정금이 있다면
					//판매수수료 부가세포함 [매출공급가액 - 정산예정금(상품 정산예정금 * 상품 개수)]
					$settle_sale_commission_in_vat = $settle_sale_supply - ($product_calculation_amt * $product_option_cnt);
					//판매수수료 부가세별도 [판매수수료 부가세별도 / 1.1]
					$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
				}else {
					//수수료가 있다면
					//판매수수료 부가세포함 [매출공급가액 * 판매수수료]
					$settle_sale_commission_in_vat = round($settle_sale_supply * ($commission / 100));
					//판매수수료 부가세별도 [판매수수료 부가세별도 * 1.1]
					$settle_sale_commission_ex_vat = round($settle_sale_commission_in_vat / 1.1);
				}

				//배송비 부가세포함
				$settle_delivery_in_vat = $delivery_fee;
				//배송비 부가세별도
				$settle_delivery_ex_vat = round($settle_delivery_in_vat / 1.1);
				//20.02.26 변경 판매배송비수수료 ex 스왑
				//판매배송비수수료 부가세포함 [배송비 부가세포함 - 배송비수수료]
				$settle_delivery_commission_in_vat = round($settle_delivery_in_vat * ($delivery_commission/100));
				//판매배송비수수료 부가세별도
				$settle_delivery_commission_ex_vat = round($settle_delivery_commission_in_vat / 1.1);
				//매입단가(매출원가) 공급가액
				$settle_purchase_unit_supply = $product_option_purchase_price;
				//매입단가(매출원가) 공급가액 부가세 별도 [매입단가(매출원가) 공급가액 / 1.1]
				$settle_purchase_unit_supply_ex_vat = round($product_option_purchase_price / (($product_tax_ratio/100) + 1));
				//매입가(매출원가) 공급가액
				$settle_purchase_supply = $product_option_purchase_price * $product_option_cnt;
				//매입가(매출원가) 공급가액 부가세 별도 [매입가(매출원가) 공급가액 - 상품 대상세금]
				$settle_purchase_supply_ex_vat = round($settle_purchase_supply / (($product_tax_ratio/100) + 1));
				//매입 배송비 부가세 포함
				$settle_purchase_delivery_in_vat = $product_delivery_fee_buy;
				//매입 배송비 부가세 별도 [매입 배송비 부가세 포함 / 1.1];
				$settle_purchase_delivery_ex_vat   = round($settle_purchase_delivery_in_vat / 1.1);

				//매출이익 = 매출공급가액[부X] - 수수료[판매수수료 부X] + 배송비[부X] - 배송비 수수료[판매배송비수수료 부X] - 매입가[부X] - 매입배송비[부X]
				$settle_sale_profit = $settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat - $settle_purchase_supply_ex_vat - $settle_purchase_delivery_ex_vat;

				//매출액 = 매출공급가액 + 배송비 - 판매수수료[부X] - 판매배송비수수료[부X]
				$settle_sale_amount = $settle_sale_supply + $settle_delivery_in_vat - $settle_sale_commission_ex_vat - $settle_delivery_commission_ex_vat;

				//매출원가 = 매입가(매출원가) 공급가액[부X] + 매입배송비[부X]
				$settle_sale_cost = $settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat;

				//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
				$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
				//매입합계 (매입가 + 매입배송비)
				$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

				$qry = "
					Insert Into DY_SETTLE
					(
						settle_date, settle_type, order_idx, order_pack_idx, order_cs_status, order_progress_step_accept_date
						, seller_idx, supplier_idx, vendor_grade, vendor_use_charge, supplier_use_prepay
						, market_order_no, market_product_no, market_product_name, market_product_option
						, order_unit_price, order_amt, order_cnt
						, commission, delivery_commisision
						, delivery_fee, delivery_type, delivery_is_free
						, order_matching_idx
						, product_idx, product_name, product_option_idx, product_option_name, product_option_cnt, product_sale_type, product_tax_type
						, product_option_sale_price, product_option_purchase_price, product_delivery_fee_sale, product_delivery_fee_buy
						, product_category_l_idx, product_category_m_idx
						, stock_idx
						, settle_sale_supply, settle_sale_supply_ex_vat, settle_sale_commission_ex_vat, settle_sale_commission_in_vat
						, settle_delivery_in_vat, settle_delivery_ex_vat, settle_delivery_commission_ex_vat, settle_delivery_commission_in_vat
						, settle_purchase_supply, settle_purchase_supply_ex_vat, settle_purchase_delivery_in_vat, settle_purchase_delivery_ex_vat
						, settle_sale_profit, settle_sale_amount, settle_sale_cost
						, settle_purchase_unit_supply, settle_purchase_unit_supply_ex_vat
						, settle_sale_sum, settle_purchase_sum
						, settle_regip, last_member_idx
					) 
					VALUES 
					(
					 N'$settle_date'
					 , N'$settle_type'
					 , N'$order_idx'
					 , N'$order_pack_idx'
					 , N'$order_cs_status'
					 , N'$order_progress_step_accept_date'
					 , N'$seller_idx'
					 , N'$supplier_idx'
					 , N'$vendor_grade'
					 , N'$vendor_use_charge'
					 , N'$supplier_use_prepay'
					 , N'$market_order_no'
					 , N'$market_product_no'
					 , N'$market_product_name'
					 , N'$market_product_option'
					 , N'$order_unit_price'
					 , N'$order_amt'
					 , N'$product_option_cnt'
					 , N'$commission'
					 , N'$delivery_commission'
					 , N'$delivery_fee'
					 , N'$delivery_type'
					 , N'$delivery_is_free'
					 , N'$order_matching_idx'
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
					 , N'$product_category_l_idx'
					 , N'$product_category_m_idx'
					 , N'$stock_idx'
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
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";

				$inserted_idx = parent::execSqlInsert($qry);

				$prev_order_idx = $order_idx;
				$prev_order_pack_idx = $order_pack_idx;
			}
		}

		if($requireDbConnection){
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}
	}


	/**
	 * 마켓 수수료 가져오기
	 * @param $seller_idx
	 * @param $product_idx
	 * @param $product_option_idx
	 * @param bool $requireDbConnection
	 * @return array
	 */
	public function getSettleCommission($seller_idx, $product_idx, $product_option_idx, $requireDbConnection = false){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = array();
		$returnValue["market_commission"] = 0;
		$returnValue["delivery_commission"] = 0;


		if($requireDbConnection){
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작
		}

		$qry = "
			Select top 1 C.market_commission, C.delivery_commission 
			From DY_MARKET_COMMISSION C
			Inner Join DY_MARKET_COMMISSION_PRODUCT P On C.comm_idx = P.comm_idx
			Where C.comm_is_del = N'N' And P.comm_product_is_del = N'N'
					And C.seller_idx = N'$seller_idx'
					And P.product_idx = N'$product_idx'
					And P.product_option_idx = N'$product_option_idx'
		";

		$_view = parent::execSqlOneRow($qry);

		if($_view){

			$returnValue["market_commission"] = $_view["market_commission"];
			$returnValue["delivery_commission"] = $_view["delivery_commission"];
		}

		if($requireDbConnection){
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}

		return$returnValue;
	}

	/**
	 * 반품 완료 처리
	 * @param $return_idx
	 * @param $paid_site
	 * @param $paid_pack
	 * @param $paid_account
	 * @param $unpaid_amount
	 * @return bool
	 */
	public function setReturnConfirm($return_idx, $paid_site, $paid_pack, $paid_account, $unpaid_amount)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$qry = "
			Update DY_ORDER_RETURN
			Set return_is_confirm = N'Y'
				, return_confirm_member_idx = N'$last_member_idx'
				, return_confirm_regdate = getdate()
			    , paid_site = N'$paid_site'
			    , paid_pack = N'$paid_pack'
			    , paid_account = N'$paid_account'
			    , unpaid_amount = N'$unpaid_amount'
				, return_moddate = getdate()
				, return_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where return_idx = $return_idx And return_is_confirm = N'N'
		";

		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		$returnValue = true;
		return $returnValue;

	}

	/**
	 * 반품 통계
	 * @return array
	 */
	public function getCSReturnStatistics(){

		$_date_ary = array();
		$query_start_date = date('Y-m-d', strtotime('-14 days'));
		$query_end_date = date('Y-m-d', time());
		$start_date = strtotime($query_start_date);
		$end_date = strtotime($query_end_date);

		$query_start =

		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $start_date);
			$_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
		}while ($new_date < $end_date);

		$qry = "
				Select
			        value as date
					, isNull(Sum(Case When cancel_reason = 'RETURN_REFUND' Then 1 Else 0 End), 0) as  RETURN_REFUND
					, isNull(Sum(Case When cancel_reason = 'RETURN_POOR' Then 1 Else 0 End), 0) as  RETURN_POOR
					, isNull(Sum(Case When cancel_reason = 'RETURN_DELIVERY_ERR' Then 1 Else 0 End), 0) as  RETURN_DELIVERY_ERR
					, isNull(Sum(Case When cancel_reason = 'CANCEL_LOSS' Then 1 Else 0 End), 0) as  CANCEL_LOSS
					, isNull(Sum(Case When cancel_reason = 'CANCEL_SOLDOUT' Then 1 Else 0 End), 0) as  CANCEL_SOLDOUT
					, isNull(Sum(Case When cancel_reason = 'CANCEL_DELIVERY_DELAY' Then 1 Else 0 End), 0) as  CANCEL_DELIVERY_DELAY
				     
					, isNull(Sum(Case When change_reason = 'EXCHANGE_NORMAL' Then 1 Else 0 End), 0) as  EXCHANGE_NORMAL
					, isNull(Sum(Case When change_reason = 'EXCHANGE_POOR' Then 1 Else 0 End), 0) as  EXCHANGE_POOR
					, isNull(Sum(Case When change_reason = 'EXCHANGE_DELIVERY_ERR' Then 1 Else 0 End), 0) as  EXCHANGE_DELIVERY_ERR
					, isNull(Sum(Case When change_reason = 'EXCHANGE_SOLDOUT' Then 1 Else 0 End), 0) as  EXCHANGE_SOLDOUT
					, isNull(Sum(Case When change_reason = 'EXCHANGE_PRODUCT_CHANGE' Then 1 Else 0 End), 0) as  EXCHANGE_PRODUCT_CHANGE
		        From
				(
					SELECT value FROM STRING_SPLIT('".implode(",", $_date_ary)."', ',')
				) as DateTable
				Left Outer Join 
				(
					Select cs_reason_code2 as cancel_reason, convert(varchar(10), product_cancel_date) as cancel_date
					From DY_ORDER_PRODUCT_MATCHING
					Where order_matching_is_del = N'N' And order_cs_status = N'ORDER_CANCEL'
						  And product_cancel_date >= '$query_start_date 00:00:00'
						  And product_cancel_date <= '$query_end_date 23:59:59'
				) CANCEL On CANCEL.cancel_date = DateTable.value
				Left Outer Join 
				(
					Select cs_reason_code2 as change_reason, convert(varchar(10), product_change_date) as cancel_date
					From DY_ORDER_PRODUCT_MATCHING
					Where order_matching_is_del = N'N' And order_cs_status = N'PRODUCT_CHANGE'
						  And product_change_date >= '$query_start_date 00:00:00'
						  And product_change_date <= '$query_end_date 23:59:59'
				) CHANGE On CANCEL.cancel_date = DateTable.value
				
				Group by DateTable.value
				Order by DateTable.value ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;

	}

	/**
	 * 판매처취소 포맷설정 가져오기
	 * @param $seller_idx
	 * @return array|false|null
	 */
	public function getSellerCancelFormat($seller_idx){
		$qry = "
			Select cancel_date, market_order_no, order_name, market_product_no, market_product_name, reason, order_idx, return_invoice_no
			From DY_ORDER_CANCEL_FORMAT
			Where seller_idx = N'$seller_idx'
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_row;
	}

	/**
	 * 판매처취소 포맷설정 Update
	 * @param $seller_idx
	 * @param $cancel_date
	 * @param $market_order_no
	 * @param $order_name
	 * @param $market_product_no
	 * @param $market_product_name
	 * @param $reason
	 * @param $order_idx
	 * @param $return_invoice_no
	 * @return bool|int|resource
	 */
	public function updateSellerCancelFormat($seller_idx, $cancel_date, $market_order_no, $order_name, $market_product_no, $market_product_name, $reason, $order_idx, $return_invoice_no){

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "Select count(*) From DY_ORDER_CANCEL_FORMAT Where seller_idx = N'$seller_idx'";
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($cnt == 0){
			$qry = "
				Insert Into DY_ORDER_CANCEL_FORMAT
				(seller_idx, cancel_date, market_order_no, order_name, market_product_no, market_product_name, reason, order_idx, return_invoice_no)
				VALUES 
				(N'$seller_idx', N'$cancel_date', N'$market_order_no', N'$order_name', N'$market_product_no', N'$market_product_name', N'$reason', N'$order_idx', N'$return_invoice_no')
			";
			parent::db_connect();
			$rst = parent::execSqlInsert($qry);
			parent::db_close();
		}else{
			$qry = "
				Update DY_ORDER_CANCEL_FORMAT
				Set 
					cancel_date = N'$cancel_date', 
					market_order_no = N'$market_order_no', 
					order_name = N'$order_name', 
					market_product_no = N'$market_product_no', 
					market_product_name = N'$market_product_name', 
					reason = N'$reason', 
					order_idx = N'$order_idx', 
					return_invoice_no = N'$return_invoice_no'
				Where seller_idx = N'$seller_idx'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return $rst;

	}

	/**
	 * 판매처취소 - 주문번호 확인
	 * @param $seller_idx
	 * @param $market_order_no
	 * @return array|false|null
	 */
	public function getSellerCancelOrderData($seller_idx, $market_order_no)
	{
		$qry = "
			Select O.order_idx, O.order_pack_idx, O.market_order_no, S.seller_name, O.order_name, O.market_product_no,  O.market_product_name, O.order_cnt, O.order_progress_step
			From DY_ORDER O 
				Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			Where O.order_is_del = N'N' And O.seller_idx = N'$seller_idx' And O.market_order_no = N'$market_order_no'
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_row;
	}

	/**
	 * 판매처취소 확인
	 * @param $order_idx
	 * @param $confirm_val
	 * @return bool|resource
	 */
	public function updateSellerCancelConfirm($order_idx, $confirm_val){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_ORDER
			Set order_seller_cancel_confirm = N'$confirm_val'
			, order_seller_cancel_confirm_date = getdate()
			, order_seller_cancel_confirm_member_idx = N'$last_member_idx'
			Where order_idx = N'$order_idx'
		";

		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		return $tmp;
	}

	/**
	 * 판매처취소 확인취소
	 * @param $order_idx
	 * @param $confirm_val
	 * @return bool|resource
	 */
	public function updateSellerCancelOffConfirm($order_idx, $confirm_val){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		if($confirm_val == "N") $last_member_idx = 0;

		$qry = "
			Update DY_ORDER
			Set order_seller_cancel_off_confirm = N'$confirm_val'
			, order_seller_cancel_off_confirm_date = getdate()
			, order_seller_cancel_off_confirm_member_idx = N'$last_member_idx'
			Where order_idx = N'$order_idx'
		";

		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		return $tmp;
	}

	/**
	 * 최근 입력한 배송정보 반환
	 * CS 에서 생성한 주문에 한함
	 * @return mixed
	 */
	public function getLatestShippingInfo(){
		$qry = "
			Select Top 5 receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode, receive_memo 
			From DY_ORDER 
			Where 
			      order_write_type = N'CS_WRITE'
				  And order_is_del = N'N'
				  And personal_data_destroy = N'N'
			Order by order_idx desc
		";
		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	public function getProductChangeReasonCode(bool $is_all=true, bool $is_shipped=true) {
		$code_con = new Code();
		$changeList = $code_con->getSubCodeList("CS_REASON_CHANGE");

		if(!$is_all) {
			$except_list = [];
			$except_code_list = [];

			if($is_shipped) {
				$except_code_list[] = "EXCHANGE_NORMAL";
			} else {
				$except_code_list[] = "EXCHANGE_POOR";
				$except_code_list[] = "EXCHANGE_DELIVERY_ERR";
			}

			foreach($changeList as $key => $change_code) {
				if(in_array($change_code["code"], $except_code_list))
					$except_list[] = $key;
			}

			foreach ($except_list as $key) {
				unset($changeList[$key]);
			}
		}

		return $changeList;
	}

	public function cancelOrder(string $order_idx, string $cs_reason_code1, string $cs_reason_code2) {
		$this->db_connect();
		$this->sqlTransactionBegin();

		$order_product_list = $this->execSqlList("
			SELECT 
				OPM.order_matching_idx, 
				CASE WHEN O.order_progress_step = 'ORDER_SHIPPED' THEN 'Y' ELSE 'N' END AS product_cancel_shipped
			FROM DY_ORDER O
				JOIN DY_ORDER_PRODUCT_MATCHING OPM ON O.order_idx = OPM.order_idx
			WHERE
				O.order_idx = $order_idx
				AND OPM.order_cs_status <> 'ORDER_CANCEL'
				AND OPM.order_matching_is_del <> 'Y'
		");

		$rst = true;

		foreach ($order_product_list as $opm) {
			if(!$this->cancelOrderProduct($opm["order_matching_idx"], $cs_reason_code1, $cs_reason_code2, $opm["product_cancel_shipped"])) $rst = false;
		}

		$this->db_close();

		return $rst;
	}

	public function cancelOrderProduct(
		string $opm_idx,
		string $cs_reason_code1 = "",
		string $cs_reason_code2 = "",
		string $product_cancel_shipped = "",
		int $cancel_cnt = -1
	) {
		if ($cancel_cnt == 0) return false;

		global $GL_Member;

		$opm_data = [];
		$opm_data["order_matching_idx"] = $opm_idx;
		$opm_data["order_cs_status"] = "ORDER_CANCEL";
		$opm_data["product_cancel_shipped"] = $product_cancel_shipped;
		$opm_data["cs_reason_code1"] = $cs_reason_code1;
		$opm_data["cs_reason_code2"] = $cs_reason_code2;
		$opm_data["order_matching_moddate"] = "NOW()";
		$opm_data["order_matching_modip"] = $_SERVER["REMOTE_ADDR"];
		$opm_data["last_member_idx"] = $GL_Member["member_idx"];

		//주문 취소 Update (CS)
		$rst = $this->insertFromArray($opm_data, "DY_ORDER_PRODUCT_MATCHING", "order_matching_idx");

		return $rst;
	}
}
?>