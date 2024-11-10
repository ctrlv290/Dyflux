<?php
/**
 * 발주 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Order extends Dbconn
{
	/**
	 * 발주서 포맷 설정 - 판매처별 포맷 불러오기
	 * @param $seller_idx : 판매처 코드
	 * @return array
	 */
	public function getOrderFormatDefaultWithSeller($seller_idx)
	{
		$qry = "
			Select
				D.order_format_default_idx
				, D.order_format_default_header_code
				, D.order_format_default_header_name
			    , D.order_format_default_is_req
			    , D.order_format_default_data_type
				, isNull(S.order_format_seller_idx, 0) as order_format_seller_idx 
				, isNull(S.order_format_seller_header_name, '') as order_format_seller_header_name
			From DY_ORDER_FORMAT_DEFAULT D
				Left Outer Join DY_ORDER_FORMAT_SELLER S 
					On D.order_format_default_idx = S.order_format_default_idx
					    And S.seller_idx = N'$seller_idx'
				        And S.order_format_seller_is_use = N'Y'
						And S.order_format_seller_is_del = N'N'
			Where 1=1
			Order by D.order_format_default_sort ASC
		";

		parent::db_connect();
		$list = parent::execSqlList($qry);
		parent::db_close();

		return $list;
	}

	/**
	 * 판매저 발주서 포맷 저장 함수
	 * @param $seller_idx : 판매처 IDX
	 * @param $order_format_default_idx : 발주서 기본 헤더 IDX
	 * @param $order_format_seller_idx : 발주서 판매처 헤더 IDX
	 * @param $order_format_seller_header_name : 발주서 판매처 헤더명
	 * @return bool|int|resource
	 */
	public function saveOrderFormatSeller($seller_idx, $order_format_default_idx, $order_format_seller_idx, $order_format_seller_header_name)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		if(trim($order_format_seller_header_name) == "")
		{
			//빈값이면 삭제
			if($order_format_seller_idx) {
				$qry = "
					Delete From DY_ORDER_FORMAT_SELLER
					Where order_format_seller_idx = N'$order_format_seller_idx'
				";
				parent::db_connect();
				$rst = parent::execSqlUpdate($qry);
				parent::db_close();

				return $rst;
			}else{
				return false;
			}
		}else{
			//값이 있으면 추가 or 수정
			$qry = "
				Select count(*) as cnt From DY_ORDER_FORMAT_SELLER Where order_format_seller_idx = N'$order_format_seller_idx'
			";
			parent::db_connect();
			$exists = parent::execSqlOneCol($qry);
			parent::db_close();

			if($exists) {
				//존재하면 Update
				$qry = "
					Update DY_ORDER_FORMAT_SELLER
					Set
						order_format_seller_header_name = N'$order_format_seller_header_name'
					WHERE order_format_seller_idx = N'$order_format_seller_idx'
				";

				parent::db_connect();
				$rst = parent::execSqlUpdate($qry);
				parent::db_close();

				return $rst;

			}else{
				//존재하지 않으면 Insert
				$qry = "
					Insert Into DY_ORDER_FORMAT_SELLER
					(
						seller_idx, order_format_default_idx, order_format_seller_header_name
						, order_format_seller_regip, last_member_idx
					) 
					VALUES 
					(
						N'$seller_idx',
						N'$order_format_default_idx',
						N'$order_format_seller_header_name',
						N'$modip',
						N'$last_member_idx'
					)
				";

				parent::db_connect();
				$rst = parent::execSqlInsert($qry);
				parent::db_close();

				return $rst;
			}
		}
	}

	/**
	 * 판매처 기본 발주 양식 입력
	 * @param $seller_idx
	 * @return bool|resource
	 */
	public function copyOrderFormatDefault($seller_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_ORDER_FORMAT_SELLER
			(
			  seller_idx, order_format_default_idx, order_format_seller_header_name
			  , order_format_seller_regdate, order_format_seller_regip
			  , order_format_seller_moddate, order_format_seller_modip
			  , last_member_idx
		    ) VALUES 
			SELECT 
				N'$seller_idx', order_format_default_idx, order_format_seller_header_name
				, getdate(), N'$modip'
				, getdate(), N'$modip'
				, N'$last_member_idx' 
			From DY_ORDER_FORMAT_SELLER Where seller_idx = 0 Order by order_format_default_idx ASC
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 쇼핑몰 주문 번호 자동 생성 함수
	 * ord 접두사 사용
	 * 10000 부터 시작
	 * market_order_no_is_auto 가 'Y' 인 것들 중에 가장 큰 값을 기준으로 +1
	 * @return string
	 */
	public function getAutoOrderNo(){
		$qry = "
			Select MAX(market_order_no) as market_order_no_max
			From DY_ORDER
			Where market_order_no_is_auto = N'Y'
		";

		parent::db_connect();
		$max_order_no = parent::execSqlOneCol($qry);
		parent::db_close();

		$max_order_no_new = "";
		if(!$max_order_no){
			$max_order_no_new = "ord100001";
		}else {
			$max_order_no = intval(str_replace("ord", "", $max_order_no));
			$max_order_no_new = "ord".($max_order_no + 1);
		}

		return $max_order_no_new;
	}

	/**
	 * 쇼핑몰 상품 코드 자동 생성 함수
	 * pdt 접두사 사용
	 * 10000 부터 시작
	 * market_product_no_is_auto 가 'Y' 인 것들 중에 가장 큰 값을 기준으로 +1
	 * @return string
	 */
	public function getAutoProductCode(){
		$qry = "
			Select MAX(market_product_no) as market_product_no_max
			From DY_ORDER
			Where market_product_no_is_auto = N'Y'
		";

		parent::db_connect();
		$max_product_no = parent::execSqlOneCol($qry);
		parent::db_close();

		$max_order_no_new = "";
		if(!$max_product_no){
			$max_product_no_new = "pdt100001";
		}else {
			$max_product_no = intval(str_replace("ord", "", $max_product_no));
			$max_product_no_new = "ord".($max_product_no + 1);
		}

		return $max_product_no_new;
	}

	/**
	 * 수동 발주 입력 함수
	 * 업로드된 엑셀을 판매처별 발주서 포맷을 적용하여 구성한 데이터를 발주 테이블에 입력
	 * @param $seller_idx : 판매처 IDX
	 * @param $order_data : DY_ORDER_FORMAT_DEFAULT 테이블의 [order_format_default_header_name] 가 Key 로 구성된 배열
	 * @return int : 최종 발주 수량
	 */
	public function insertOrder($seller_idx, $order_data)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = array();
		$inserted = 0;
		$dup = 0;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//벤더사 판매처 여부 확인
		$qry = "Select seller_type From DY_SELLER Where seller_idx = N'$seller_idx'";
		$seller_type = parent::execSqlOneCol($qry);
		$is_vendor_seller = false;
		if($seller_type == "VENDOR_SELLER"){
			$is_vendor_seller = true;
		}

		foreach($order_data as $order) {

			$order = array_map_deep(DY_ESCAPE_FUNCTION,  $order);

			//발주 IDX 생성
			$qry = "Select isnull(MAX(order_idx), 0) as max_order_idx From DY_ORDER";
			$max_order_idx = parent::execSqlOneCol($qry);
			$order_idx = 0;
			if($max_order_idx < 100001){
				$order_idx = 100001;
			}else{
				$order_idx = $max_order_idx + 1;
			}


			//region //변수 초기화 및 바인딩
			$market_order_no = $order["주문번호"];
			$market_order_no_is_auto = "N";
			if($order["주문번호"] == "auto_order_no"){
				$qry = "
					Select MAX(market_order_no) as market_order_no_max
					From DY_ORDER
					Where market_order_no_is_auto = N'Y'
				";

				$max_order_no = parent::execSqlOneCol($qry);

				$max_order_no_new = "";
				if(!$max_order_no){
					$max_order_no_new = "ord100001";
				}else {
					$max_order_no = intval(str_replace("ord", "", $max_order_no));
					$max_order_no_new = "ord".($max_order_no + 1);
				}
				$market_order_no = $max_order_no_new;
				$market_order_no_is_auto = "Y";
			}

			$market_product_no = $order["상품코드"];
			$market_product_no_is_auto = "N";
			if($order["상품코드"] == "auto_product_code"){
				$qry = "
					Select MAX(market_product_no) as market_product_no_max
					From DY_ORDER
					Where market_product_no_is_auto = N'Y'
				";

				$max_product_no = parent::execSqlOneCol($qry);

				$max_product_no_new = "";
				if(!$max_product_no){
					$max_product_no_new = "pdt100001";
				}else {
					$max_product_no = intval(str_replace("pdt", "", $max_product_no));
					$max_product_no_new = "pdt".($max_product_no + 1);
				}

				$market_product_no = $max_product_no_new;
				$market_product_no_is_auto = "Y";
			}

			$order_pack_idx            = $order_idx;
			$order_collect_idx         = $order["order_collect_idx"];
			$matching_type             = "";
			$order_state               = "";
			$order_pack_code           = "";

			//$order_pay_date            = ($order["주문일"] && $order["주문시간"]) ? $order["주문일"] . ' ' . $order["주문시간"] : "";
			// 대부분 주문일만 있음 -> ssawoona
			if ($order["주문일"] && $order["주문시간"]) {
				$order_pay_date        = mssqlDateTimeStringConvert($order["주문일"] . ' ' . $order["주문시간"]);
			} else if ($order["주문일"]) {
				$order_pay_date        = mssqlDateTimeStringConvert($order["주문일"]);
			}

			$order_confirm_date        = "";    //발주일시는 현재간
			$invoice_date              = "";
			$market_order_subno        = $order["주문상세번호"];
			$market_product_name       = $order["상품명"];
			$market_product_option     = $order["옵션"];
			$market_order_id           = $order["구매자ID"];
			$order_unit_price          = $order["판매단가"];
			$order_amt                 = $order["판매금액"];			
			$order_pay_amt             = "";
			$order_calculation_amt     = $order["정산금액"];
			$order_cnt                 = $order["수량"];
			$delivery_fee              = $order["배송비금액"];
			$order_pay_type            = $order["결제수단"];
			$order_name                = $order["주문자명"];
			$order_tp_num              = $order["주문자전화번호"];
			$order_hp_num              = $order["주문자핸드폰"];
			$order_addr1               = $order["주문자주소"];
			$order_addr2               = "";
			$order_zipcode             = $order["주문자우편번호"];
			$receive_name              = $order["수령자명"];
			$receive_tp_num            = $order["수령자전화번호"];
			$receive_hp_num            = $order["수령자핸드폰"];
			$receive_addr1             = $order["수령자주소"];
			$receive_addr2             = "";
			$receive_zipcode           = $order["수령자우편번호"];
			$receive_memo              = $order["메모"];
			$delivery_code             = "";
			$invoice_no                = "";
			$delivery_type             = $order["배송비구분"];
			$order_is_auto             = "";
			$order_org_data1           = "";
			$order_org_data2           = "";
			$order_write_type          = ($order["주문생성타입"]) ? $order["주문생성타입"] : "COLLECT";

			$order_commission          = $order["수수료(원)"];
			$order_commission_per      = $order["수수료(%)"];

			$order_discount_amt        = $order["할인금액(단가)"]; //단가 계산에 사용되는 할인 금액

			$array_delivery_is_free = array(
				'' => 'Y',
				'선불' => 'Y',
				'선결제' => 'Y',
				'신용' => 'Y',
				'FREE' => 'Y',
				'PREPAYED' => 'Y',
				'무료' => 'Y',
				'유료' => 'Y',
				'착불' => 'N'
			);
			$delivery_is_free          = $array_delivery_is_free[$delivery_type];
			if($delivery_is_free == "")
				$delivery_is_free = "Y";

			//단가와 할인금액(단가)이 있을 경우, 단가는 할인 금액을 제외하고 계산한다. TODO 카페 24뿐만 아니라 다른 곳도 같은 방식인지 확인 할 필요 있음
			if ($order_unit_price > 0 && $order_discount_amt) {
				$order_unit_price = $order_unit_price - round($order_discount_amt / $order_cnt);
			}

			// 판매합계가 없고 판매 단가만 있는 아이들은 판매합계 금액을 계산해서 넣는다
			if($order_amt <= 0 && $order_unit_price > 0) {
				$order_amt             = $order_unit_price * $order_cnt;
			}

			// 수수료 2019-08-02 kyu
			if ($order_commission || $order_commission_per) {
				$temp_comm = 0;

				if ($order_commission_per) {
					$temp_comm = round(($order_amt / 100) * $order_commission_per);
				}

				if ($order_commission) {
					if ($temp_comm) {
						if ($temp_comm != $order_commission) {
							//수수료(%) 와 수수료(원) 이 있지만, 계산 시, 금액이 다름
						}
					}

					$temp_comm = $order_commission;
				}

				$order_calculation_amt = $order_amt - $temp_comm;
			}

			// 정산예정금액 부가세 미포함 20200312 kyu
			if ($order["정산금액(부가세미포함)"]) {
				if (! $order["정산금액"]) {
					$order_calculation_amt = round($order["정산금액(부가세미포함)"] * 1.1);
				}
			}

			// 수령자 주소가 상세랑 각각필드로 오는 마켓의 경우 앞단에서 | 로 붙이고 치환 시킨다.
			$receive_addr1 = str_replace("|", " ", $receive_addr1);

			//endregion

			//벤더 판매처의 경우 판매금액, 판매단가  0 으로 설정
			if($is_vendor_seller) {
				$order_calculation_amt = 0;     //정산금액
				$order_unit_price = 0;          //판매단가
				$order_amt = 0;                 //픈매금액
			}

			/**
			 * 이미 존재하는 발주 인지 확인
			 * * 중복 발주 조건
			 *
			 * 쇼핑몰 주문 번호가 자동생성이 아닌 경우 쇼핑몰 주문번호로 중복 검사
			 * market_order_no_is_auto = 'N'
			 *
			 * 쇼핑몰 주문번호 자동생성인 경우
			 * 수령자명 && 수령자핸드폰 && 수령자주소 && 메모 && 상품명 && 옵션 && 수량 && 판매금액
			 *
			 * 매칭완료가 안된 것들 대상
			 */
			if($market_order_no_is_auto == "N")
			{
				$qry = "
					Select count(*) From DY_ORDER
					WHERE  order_is_del = N'N' And seller_idx = N'$seller_idx'
							And market_order_no = N'$market_order_no'
							And market_order_subno = N'$market_order_subno'
				";
			}else {

				//조건 추가 - 19.06.17
				//당일 주문에 한해서만 중복 체크
				$today_datetime = date("Y-m-d") . " 00:00:00";

				$qry = "
					Select count(*) From DY_ORDER WITH (NOLOCK)
					WHERE  order_is_del = N'N' And seller_idx = N'$seller_idx'
							And receive_name = N'$receive_name'
							And receive_hp_num = N'$receive_hp_num'
							And receive_addr1 = N'$receive_addr1'
							And receive_memo = N'$receive_memo'
							And market_product_name = N'$market_product_name'
							And market_product_option = N'$market_product_option'
							And order_cnt = N'$order_cnt'
							And order_amt = N'$order_amt'
							And order_regdate >= N'$today_datetime'
				";
			}

			$dupCount = parent::execSqlOneCol($qry);

			if($dupCount == 0) {

				$qry = "
					Insert Into DY_ORDER
					(
					 order_idx, order_pack_idx, seller_idx, order_collect_idx, matching_type
					 , order_state, order_pack_code, order_pay_date, order_confirm_date
					 , market_order_no, market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto
					 , market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt
					 , order_calculation_amt, order_cnt, delivery_fee, order_pay_type
					 , order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode
					 , receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode
					 , receive_memo, delivery_code, invoice_no, delivery_type, delivery_is_free
					 , order_is_auto, order_org_data1, order_org_data2
					 , order_regip, last_member_idx
					)
					 VALUES 
					(
						N'$order_idx',                
						N'$order_pack_idx',                
						N'$seller_idx',   
						N'$order_collect_idx',                
						N'$matching_type',
						N'$order_state',
						N'$order_pack_code',
						N'$order_pay_date',
						NULL,
						N'$market_order_no',
						N'$market_order_no_is_auto',
						N'$market_order_subno',
						N'$market_product_no',
						N'$market_product_no_is_auto',
						N'$market_product_name',
						N'$market_product_option',
						N'$market_order_id',
						N'$order_unit_price',
						N'$order_amt',
						N'$order_pay_amt',
						N'$order_calculation_amt',
						N'$order_cnt',
						N'$delivery_fee',
						N'$order_pay_type',
						N'$order_name',
						N'$order_tp_num',
						N'$order_hp_num',
						N'$order_addr1',
						N'$order_addr2',
						N'$order_zipcode',
						N'$receive_name',
						N'$receive_tp_num',
						N'$receive_hp_num',
						N'$receive_addr1',
						N'$receive_addr2',
						N'$receive_zipcode',
						N'$receive_memo',
						N'$delivery_code',
						N'$invoice_no',
						N'$delivery_type',
						N'$delivery_is_free',
						N'$order_is_auto',
						N'$order_org_data1',
						N'$order_org_data2',
						N'$modip',
						N'$last_member_idx'
					)
				";
				$rst = parent::execSqlInsert($qry);
				$inserted++;
			}else{
				$dup++;
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		$returnValue["inserted"] = $inserted;
		$returnValue["dup"] = $dup;

		return $returnValue;
	}

	/**
	 * 쿠팡 배송비 조정
	 * 쿠팡의 경우 배송비가 옵션마다 똑같이 들어와서 한개 옵션에만 배송비가 붙고 나머지는 0 으로 UPDATE
	 * 서버 부하 때문에 오늘꺼만 UPDATE
	 * 엑셀 업로드 할때 한번씩 실행!!!
	 * order_idx not in (
			Select
			distinct Min(order_idx) OVER(PARTITION BY market_order_no) as min_order_idx
			From DY_ORDER A  INNER JOIN [DY_SELLER] B ON A.seller_idx = B.seller_idx
			WHERE B.market_code = 'COUPANG'
		)
	 */
	public function updateOrderDeliveryFee_Coupang() {
		$qry = "
			UPDATE [DY_ORDER] SET delivery_fee = 0 
			FROM [DY_ORDER] U INNER JOIN 
			(
				SELECT AA.order_idx
				FROM [DY_ORDER] AA INNER JOIN 
				(
					SELECT MIN(order_idx) order_idx, market_order_no, COUNT(market_order_no) order_cnt, MAX(B.seller_idx) seller_idx
					FROM [DY_ORDER] A INNER JOIN [DY_SELLER] B ON A.seller_idx = B.seller_idx
					WHERE B.market_code = 'COUPANG' AND A.order_is_del = 'N' 
					AND A.order_regdate BETWEEN '".date("Y-m-d")." 00:00:00.000' AND '".date("Y-m-d")." 23:59:59.999'
					GROUP BY market_order_no
				) BB
				ON AA.order_idx <> BB.order_idx AND AA.market_order_no = BB.market_order_no AND AA.seller_idx = BB.seller_idx
				WHERE AA.order_is_del = 'N' 
				AND AA.order_regdate BETWEEN '".date("Y-m-d")." 00:00:00.000' AND '".date("Y-m-d")." 23:59:59.999'
			) S ON U.order_idx = S.order_idx
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}
	/**
	 * 수동 발주 로그 입력
	 * @param $seller_idx : 판매처 IDX
	 * @param $order_date : 발주일
	 * @param $upload_cnt : 업로드 개수
	 * @param $order_cnt : 발주성공 개수
	 * @param $filename : 업로드 파일명
	 * @return int : inserted idx
	 */
	public function insertOrderUpload($args)
	{
		//DY_ORDER_COLLECT
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];


		//region //변수초기화
		$collect_num           = 0;
		$seller_idx            = 0;
		$collect_type          = "XLS";
		$collect_sdate         = "";
		$collect_edate         = "";
		$collect_count         = 0;
		$collect_order_count   = 0;
		$collect_state         = "ㄹ";
		$collect_message       = "";
		$collect_filename      = "";
		//endregion

		extract($args);

		$qry = "
			Insert Into DY_ORDER_COLLECT
			(
			  collect_num, seller_idx, collect_type, collect_sdate, collect_edate, 
			  collect_count, collect_order_count, collect_state, collect_message, collect_filename, 
			  order_collect_regip, last_member_idx
			)
			VALUES 
			(
              N'$collect_num', 
              N'$seller_idx', 
              N'$collect_type', 
              N'$collect_sdate', 
              N'$collect_edate', 
              N'$collect_count', 
              N'$collect_order_count', 
              N'$collect_state', 
              N'$collect_message', 
              N'$collect_filename', 
              N'$modip', 
              N'$last_member_idx'
			)	 
		";
		$this->updateOrderDeliveryFee_Coupang();
		parent::db_connect();
		$order_collect_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $order_collect_idx;
	}
	public function insertOrderUpload_old($seller_idx, $order_date, $upload_cnt, $order_cnt, $filename)
	{
		//DY_ORDER_COLLECT
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		//region //변수초기화
		$collect_num           = 0;
		$collect_sdate         = $order_date;
		$collect_edate         = $order_date;
		$collect_type          = "XLS";
		$collect_count         = $upload_cnt;
		$collect_order_count   = $order_cnt;
		$collect_state         = "S";
		$collect_message       = "";
		$collect_filename      = $filename;
		//endregion

		$qry = "
			Insert Into DY_ORDER_COLLECT
			(
			  collect_num, seller_idx, collect_type, collect_sdate, collect_edate, 
			  collect_count, collect_order_count, collect_state, collect_message, collect_filename, 
			  order_collect_regip, last_member_idx
			)
			VALUES 
			(
              N'$collect_num', 
              N'$seller_idx', 
              N'$collect_type', 
              N'$collect_sdate', 
              N'$collect_edate', 
              N'$collect_count', 
              N'$collect_order_count', 
              N'$collect_state', 
              N'$collect_message', 
              N'$collect_filename', 
              N'$modip', 
              N'$last_member_idx'
			)	 
		";

		parent::db_connect();
		$order_collect_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $order_collect_idx;
	}

	/**
	 * 발주서에 수동 발주 로그 IDX Update
	 * 발주서 입력 시에 임시로 발급한 번호를 실제 로그 IDX 로 Update
	 * @param $prev_order_collect_idx : 임시로 발급했던 번호
	 * @param $new_order_collect_idx : 실제 로그 IDX
	 * @return bool|resource
	 */
	public function updateOrderCollectIDX($prev_order_collect_idx, $new_order_collect_idx)
	{
		$qry = "
			Update DY_ORDER
			Set order_collect_idx = N'$new_order_collect_idx'
			Where order_collect_idx = N'$prev_order_collect_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 일괄접수 처리!! (임시 접수 대상)
	 * TODO : 매칭 시에 판매가 및 매입가를 가져오는 것으로 확인 됨. 일괄 접수 처리시 동일한 내용을 반복하는지 확인 필요
	 * UPDATE : 선택 받아 사용하도록 변경 20190703 kyu
	 * @return int|mixed
	 */
	public function updateOrderAcceptWholeConfirm($order_accept_temp_list){

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$str_order_accept_temp_list = implode(",", $order_accept_temp_list);

		//개수 카운트
		$qry = "
			Select O.*, S.seller_type
			From DY_ORDER O
				Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			Where 
			      order_is_del = N'N'
				  And order_progress_step = N'ORDER_ACCEPT_TEMP'
				  AND order_pack_idx in ($str_order_accept_temp_list)
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		$cnt = count($_list);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//일괄접수(발주) 시 상품 판매 가격 Update
		foreach ($_list as $ord)
		{
			$order_pack_idx = $ord["order_pack_idx"];
			$order_idx = $ord["order_idx"];
			$seller_idx = $ord["seller_idx"];
			$order_matching_idx = 0;
			$product_option_sale_price = 0;

			//벤더사 판매처 소진 금액
			$vendor_charge_sum = 0;


			//벤더사 판매처가 아니면 주문금액을 첫번째 매칭 상품 가격에 Update
			//아래 로직 삭제 - 19.04.25
			//상품 매칭시 판매가 Update 함
			/*
			if($ord["seller_type"] != "VENDOR_SELLER"){

				$product_option_sale_price = $ord["order_amt"];

				//첫번째 매칭 상품 가져오기
				$qry = "
					Select Min(order_matching_idx) as  order_matching_idx
					From DY_ORDER_PRODUCT_MATCHING 
					Where order_idx = N'$order_idx'
				";

				$order_matching_idx = parent::execSqlOneCol($qry);

				if($order_matching_idx)
				{
					//상품 가격 Update
					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set 
							product_option_sale_price = N'$product_option_sale_price'
						Where 
							order_matching_idx = N'$order_matching_idx'
					";

					$tmp = parent::execSqlUpdate($qry);

				}
			}else{

				//벤더사 판매처 일 경우 등급에 맞는 가격을 가져온다

				//등급 가져오기
				$qry = "Select vendor_grade From DY_MEMBER_VENDOR Where member_idx = N'$seller_idx'";

				$vendor_grade = parent::execSqlOneCol($qry);

				//매칭 상품 리스트 - 상품옵션 판매 금액을 포함
				//						, product_option_sale_price_".$vendor_grade." as product_option_sale_price
				$qry = "
					Select order_matching_idx
					, product_option_sale_price_".$vendor_grade." as product_option_sale_price
					From DY_ORDER_PRODUCT_MATCHING M
						Left Outer Join DY_PRODUCT_OPTION O On M.product_option_idx = O.product_option_idx
					Where order_idx = N'$order_idx'
				";

				$_product_list = parent::execSqlList($qry);


				//상품옵션 판매 금액 Update
				foreach ($_product_list as $prod) {

					$__order_matching_idx = $prod["order_matching_idx"];
					$__product_option_sale_price = $prod["product_option_sale_price"];
					$qry = "
						Update 
							DY_ORDER_PRODUCT_MATCHING 
						Set product_option_sale_price = N'$__product_option_sale_price'
						Where order_matching_idx = N'$__order_matching_idx'
					";

					$tmp = parent::execSqlUpdate($qry);
				}

			}
			*/

			//사은품 검색 및 추가
			//하나라도 매치되면 Pass
			$gift_match = false;
			$gift_match_ary = array();
			$nowDT = date("Y-m-d H:i:s");

			//매칭된 상품 불러오기
			$qry = "
				Select M.*, S.member_idx, PO.product_option_purchase_price
				From DY_ORDER_PRODUCT_MATCHING M
				Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
				Left Outer Join DY_PRODUCT_OPTION PO On M.product_option_idx = PO.product_option_idx
				Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
				Where order_matching_is_del = N'N' And P.product_is_del = N'N' And PO.product_option_is_del = N'N' And order_idx = N'$order_idx'
			";

			$_match_list = parent::execSqlList($qry);

			foreach ($_match_list as $m) {

				$m_supplier_idx                  = $m["member_idx"];
				$m_product_idx                   = $m["product_idx"];
				$m_product_option_idx            = $m["product_option_idx"];
				$m_product_option_cnt            = $m["product_option_cnt"];
				$m_product_option_purchase_price = $m["product_option_purchase_price"];

				$ord_order_amt         = $ord["order_amt"];
				$ord_order_pay_type    = $ord["order_pay_type"];
				$ord_market_product_no = $ord["market_product_no"];

				$qry = "
					Select G.*, PO.product_option_purchase_price 
					From DY_ORDER_GIFT G
						Inner Join DY_PRODUCT P On P.product_idx = G.gift_product_idx
						Inner Join DY_PRODUCT_OPTION PO On PO.product_option_idx = G.gift_product_option_idx
					Where gift_is_del = N'N'
					And P.product_is_del = N'N' And PO.product_option_is_del = N'N'
					And gift_status = N'Y'
					And gift_date_start <= N'$nowDT' And gift_date_end >= '$nowDT'
				    And 
					      (
					        (G.supplier_idx = 0  And product_option_idx_list = N'')
					          Or G.supplier_idx = N'$m_supplier_idx' 
					          Or (G.supplier_idx = 0 And product_option_idx_list like N'%$m_product_option_idx%')
					      ) 
					And 
					      (G.seller_idx = 0 Or G.seller_idx = N'$seller_idx')
					And  
					      (
					        market_product_no_list = N'' 
					         Or 
					        ( market_product_no_list like N'%$ord_market_product_no%' And '$ord_market_product_no' <> '' )
				          )
					And 
				          (gift_match_pay = N'N' Or (gift_match_pay = N'Y' And gift_match_pay_text = N'$ord_order_pay_type') )
					And 
				          (
				            gift_match_product = N'N' 
				              Or (gift_match_product = N'Y' And gift_match_product_cnt_s <= N'$m_product_option_cnt' And gift_match_product_cnt_e >= N'$m_product_option_cnt')
				            )
					And 
				          (
				            gift_match_order_amount = N'N' 
				             Or (gift_match_order_amount = N'Y' And '$ord_order_amt' <> '' And '$ord_order_amt' <> '0' 
				                   And gift_match_order_amount_s <= N'$ord_order_amt' And gift_match_order_amount_e >= N'$ord_order_amt')
			              )
					Order by gift_regdate DESC
				";

				$gift_m_list = parent::execSqlList($qry);

				if($gift_m_list){
					foreach($gift_m_list as $g) {

						//1:N 매칭일 경우 사은품이 여러번 매칭 될 수 있다
						//이미 입력된 사은품인지 체크
						//이미 입력된 사은품이면 Pass
						if(in_array($g["gift_idx"], $gift_match_ary)){
							continue;
						}

						//이미 입력된 사은품이 있고 중복 사은품일 경우 Pass
						if(count($gift_match_ary) > 0){
							if($g["gift_is_only"] == "Y"){
								continue;
							}
						}

						$gift_match_ary[] = $g["gift_idx"];

						$gift_idx = $g["gift_idx"];
						$gift_name = $g["gift_name"];
						$gift_product_idx = $g["gift_product_idx"];
						$gift_product_option_idx = $g["gift_product_option_idx"];
						$gift_product_option_purchase_price = $g["product_option_purchase_price"];
						$gift_cnt_type = $g["gift_cnt_type"];
						$gift_cnt_type_cnt = $g["gift_cnt_type_cnt"];
						$gift_cnt = $g["gift_cnt"];

						$gift_product_option_cnt = 0;

						if($gift_cnt_type == "O"){              //주문번호당
							$gift_product_option_cnt = 1;
						}elseif($gift_cnt_type == "C"){         //수량만큼
							$gift_product_option_cnt = $m_product_option_cnt;
						}elseif($gift_cnt_type == "N"){         // n개당 1개
							$gift_product_option_cnt = floor($m_product_option_cnt / $gift_cnt_type_cnt);
						}

						//사은품 소진 수량 가져오기
						$qry = "Select isNull(Sum(product_option_cnt), 0) From DY_ORDER_PRODUCT_MATCHING M Where M.gift_idx = N'$gift_idx' And M.order_matching_is_del = N'N'";
						$use_cnt = parent::execSqlOneCol($qry);

						//남은수량 계산
						$remain_cnt = $gift_cnt - $use_cnt;

						if($gift_product_option_cnt > 0 && $remain_cnt >= $gift_product_option_cnt) {
							//매칭된 상품 주문매칭에 입력
							$qry = "
								Insert Into DY_ORDER_PRODUCT_MATCHING
								(
								 order_idx, seller_idx,
								 product_idx, product_option_idx, product_option_cnt,
								 order_matching_is_auto, product_option_sale_price, product_option_purchase_price,
								 is_gift, gift_idx, gift_title,
								 order_matching_regip, last_member_idx
								)
								VALUES
								(
									N'$order_idx', N'$seller_idx', 
									N'$gift_product_idx', N'$gift_product_option_idx', N'$gift_product_option_cnt',
								    N'Y', 0, N'$gift_product_option_purchase_price',
								    N'Y', N'$gift_idx', N'$gift_name',
							        N'$modip', N'$last_member_idx'
								)
							";

							$gift_inserted_idx = parent::execSqlInsert($qry);

							$C_CS = new CS();
							$cs_task = "NORMAL";    //일반
							$cs_msg = "[사은품 매칭] \n 대상 관리번호 : ".$order_idx."\n사은품 이름 : ".$gift_name."\n사은품 상품옵션코드 : " . $gift_product_option_idx; //사은품
							$cs_idx = $C_CS -> insertCS($order_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);


							//소진 후 남은 수량이 0 이면 사은품 종료
							if(($remain_cnt - $gift_product_option_cnt) == 0){
								$qry = "Update DY_ORDER_GIFT Set gift_status = N'X', gift_moddate = getdate(), gift_modip = N'$modip', gift_modidx = N'$last_member_idx' Where gift_idx = N'$gift_idx'";
								parent::execSqlUpdate($qry);
							}

						}

						//입력한 사은품이 중복불가 사은품 일 경우 종료
						if($g["gift_is_only"] == "Y"){
							break;
						}
					}
				}
			}

			//접수 처리 Update - 정상테이블 입력을 위해서
			$qry = "
				Update DY_ORDER
					Set order_progress_step = N'ORDER_ACCEPT'
						, order_progress_step_accept_date = getdate()
						, order_progress_step_accept_member_idx = N'$last_member_idx'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
				Where 
					  order_idx = N'$order_idx'
					  And order_progress_step = N'ORDER_ACCEPT_TEMP'
			";

			$tmp = parent::execSqlUpdate($qry);

			//정상테이블 입력 - 위탁상품만 입력
			$this->insertSettleShipped($order_idx, true, false);
		}

		//Commit
		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		/*
		 * !!IMPORTANT!! - 19.01.31
		 * - 배송(출고) 전 까지는 재고 테이블에 재고 관련 사항 입력 하지 않음
		 * - 기존에는 접수 시 부터 접수된 재고 수량을 파악하기 위해 필요 했지만
		 * - DY_ORDER_PRODUCT_MATCHING 테이블을 통해 접수 및 송장 상태인 재고 수량 Count
		 */
		//재고 접수 처리
		/*
		$qry = "
			Update DY_STOCK
				Set stock_status = N'ACCEPT'
				, stock_moddate = getdate()
				, stock_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
				stock_is_del = N'N'
				And stock_status = N'ACCEPT_TEMP'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		*/

		return $cnt;
	}

	/**
	 * 발주서 모두 삭제
	 * 발주 진행 상태값이 : 주문정보수집단계(ORDER_COLLECT) 인 것 만
	 * @return bool|resource
	 */
	public function deleteOrderAll()
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//매칭 상품 내역 삭제(Update)
		$qry = "
			Update DY_ORDER_PRODUCT_MATCHING
			Set order_matching_moddate = getdate(), order_matching_modip = N'$modip', order_matching_is_del = N'Y'
			Where order_idx in (Select order_idx From DY_ORDER Where order_is_del = N'N' And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING'))
		";
		$rst = parent::execSqlUpdate($qry);

		//주문 삭제(Update)
		$qry = "
			Update DY_ORDER
			Set order_moddate = getdate(), order_modip = N'$modip', order_is_del = N'Y'
			Where order_is_del = N'N' And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING')
		";

		$rst = parent::execSqlUpdate($qry);


		//Commit
		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $rst;
	}

	/**
	 * 판매처 발주 삭제
	 * 발주 진행 상태값이 : 주문정보수집단계(ORDER_COLLECT) 인 것 만
	 * @param $seller_idx : 판매처 IDX
	 * @return bool|resource
	 */
	public function deleteOrderOne($seller_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//매칭 상품 내역 삭제(Update)
		$qry = "
			Update DY_ORDER_PRODUCT_MATCHING
			Set order_matching_moddate = getdate(), order_matching_modip = N'$modip', order_matching_is_del = N'Y'
			Where order_idx in (
		      Select order_idx 
		      From DY_ORDER 
		      Where order_is_del = N'N' And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING' ) 
		        and seller_idx = N'$seller_idx'
		    )
		";
		$rst = parent::execSqlUpdate($qry);

		//주문 삭제(Update)
		$qry = "
			Update DY_ORDER
			Set order_moddate = getdate(), order_modip = N'$modip', order_is_del = N'Y'
			Where order_is_del = N'N' And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING') 
			  and seller_idx = N'$seller_idx'
		";
		$rst2 = parent::execSqlUpdate($qry);

		//Commit
		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();
		return $rst2;
	}

	/**
	 * 수동 상품 매칭을 위한 발주 정보 반환
	 * 발주 진행 상태값이 : 주문정보수집단계(ORDER_COLLECT) 인 것 만
	 * @param $order_idx : 발주 IDX
	 * @return array|false|null
	 */
	public function getOrderDataForMatching($order_idx)
	{
		$qry = "
			Select O.*, S.seller_name
			From DY_ORDER O
				Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			Where order_is_del = N'N' And order_progress_step = N'ORDER_COLLECT'And order_idx = N'$order_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	public function getOrderDataForMatchingConfirm($seller_idx){

		if($seller_idx) {
			$sellerQry = " And O.seller_idx = N'$seller_idx' ";
		}

		$qry = "
			Select O.order_idx, O.market_product_no, O.market_product_option, S.seller_name
			, STUFF((
				Select '[;;]' + product_name + ' ' + PO.product_option_name + ' / 수량:' +  Convert(varchar(10), PM.product_option_cnt)
				From DY_ORDER_PRODUCT_MATCHING PM
				Left Outer Join DY_PRODUCT P On PM.product_idx = P.product_idx
				Left Outer Join DY_PRODUCT_OPTION PO On PM.product_option_idx = PO.product_option_idx
				Where PM.order_matching_is_del = N'N' And PM.order_idx = O.order_idx
				FOR XML PATH('')), 1, 4, '') as matching_info
			From DY_ORDER O
			    Left Outer Join DY_SELLER S On O.seller_idx = S.seller_idx
			Where 
		        order_is_del = N'N' 
			    And order_progress_step = N'ORDER_PRODUCT_MATCHING'
			    $sellerQry
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * [수동 매칭] 상품 매칭 저장
	 * @param $order_idx : 발주 IDX
	 * @param $order_cnt : 주문수량
	 * @param $seller_idx : 판매처 IDX
	 * @param $product_list : 매칭에 선택된 상품 목록 (array) [{product_idx, product_option_idx, product_option_cnt}]
	 * @param $saveYn : 자동 여부
	 * @return int : 저장된 개수
	 */
	public function saveOrderMatching($order_idx, $order_cnt, $seller_idx, $product_list, $saveYn)
	{
		global $GL_Member;
		$modip           = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$inserted = 0;
		$insertedArray = array();
		$isExists = 0;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//주문 정보 가져오기
		$qry = "
			Select order_amt, order_calculation_amt From DY_ORDER Where order_idx = N'$order_idx'
		";
		$_order = parent::execSqlOneRow($qry);
		$order_amt = $_order["order_amt"];
		$order_calculation_amt = $_order["order_calculation_amt"];


		//발주 IDX 로 매칭 정보가 있는지 확인
		$qry      = "Select count(*) as cnt From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_idx = N'$order_idx'";
		$isExists = parent::execSqlOneCol($qry);

		//판매처 타입 확인 [벤더사 판매처인지]
		//벤더사 판매처 등급 확인
		$qry = "
			Select S.seller_type, isNull(V.vendor_grade, '') as vendor_grade 
			From DY_SELLER S
			Left Outer Join DY_MEMBER_VENDOR V On S.seller_idx = V.member_idx 
			Where seller_idx = N'$seller_idx'
		";
		$_seller_info = parent::execSqlOneRow($qry);
		$seller_type = $_seller_info["seller_type"];
		$vendor_grade = $_seller_info["vendor_grade"];


		//이미 매칭 정보가 있다면 Pass
		if ($isExists === 0) {
			if (count($product_list) > 0) {

				foreach ($product_list as $key => $prd) {
					$product_idx        = $prd["product_idx"];
					$product_option_idx = $prd["product_option_idx"];
					//$product_option_cnt = $prd["product_option_cnt"]; //매칭수량  - 기존
					$product_option_cnt = $prd["product_delivery_cnt"]; //배송수량  - 19.07.17
					$product_option_sale_price = 0;         //판매가격
					$product_option_purchase_price = 0;     //매입가격
					$product_calculation_amt = 0;           //정산예정금액(개당 단가)

					//$insert_product_option_cnt = $product_option_cnt * $order_cnt;    //매칭수량 * 주문수량 - 기존
					$insert_product_option_cnt = $product_option_cnt;                   //배송수량 으로 변경 - 19.07.17

					//상품 타입 가져오기 [자제/사입], [위탁]
					$product_sale_type = "";
					$qry = "
						Select product_sale_type From DY_PRODUCT Where product_idx = N'$product_idx'
					";
					$product_sale_type = parent::execSqlOneCol($qry);

					/**
					 * ------------------------------------------------------
					 * |     판매처    | 상품 타입 | 판매가 |    매입가   |
					 * ------------------------------------------------------
					 * |  마켓 판매처  |    자체   |   X    | 송장시 입력 |
					 * |  마켓 판매처  |    위탁   |   X    |      O      |
					 * |  벤더 판매처  |    자체   |   O    | 송장시 입력 |
					 * |  벤더 판매처  |    위탁   |   O    |      O      |
					 * * ------------------------------------------------------
					 */

					//판매가격, 매입가격 가져오기
					//벤더사 판매처가 아닐 경우 판매가격은 0 으로 설정됨.
					$_option_price_ary = $this->getProductOptionPriceBySeller($product_option_idx, $vendor_grade);
					$product_option_sale_price = $_option_price_ary["product_option_sale_price"];
					$product_option_purchase_price = $_option_price_ary["product_option_purchase_price"];


					//마켓 판매처 일 경우
					//주문금액을 수량으로 나눈다 (소수점 버림)
					//첫번째 상품만 입력 나머지는 0
					if($seller_type != "VENDOR_SELLER" && $key == 0) {
						$product_option_sale_price = floor($order_amt / $insert_product_option_cnt);

						//주문에 정산예정금액이 있으면
						///첫번째 상품에 1/n 하여 정산예정금액 저장
						if($order_calculation_amt > 0){
							$product_calculation_amt = floor($order_calculation_amt / $insert_product_option_cnt);
						}
					}

					//자체상품 일 경우 매입가를 0 으로 설정
					//송장단계에서 재고 매칭 시 재고원가를 입력 받는다
					if($product_sale_type == "SELF"){
						$product_option_purchase_price = 0;
					}

					//상품코드, 상품옵션코드, 상품수량 숫자형인지 확인
					if (is_numeric($product_idx)
						&& is_numeric($product_option_idx)
						&& is_numeric($insert_product_option_cnt)
					) {

						$qry = "
							Insert Into DY_ORDER_PRODUCT_MATCHING
							(
							 order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt
							 , order_matching_is_auto
							 , product_option_sale_price, product_option_purchase_price, product_calculation_amt
							 , order_matching_regip, last_member_idx
							)
							VALUES
							(
							 N'$order_idx',
							 N'$seller_idx',
							 N'$product_idx',
							 N'$product_option_idx',
							 N'$insert_product_option_cnt',
							 N'$saveYn',
							 N'$product_option_sale_price',
							 N'$product_option_purchase_price',
							 N'$product_calculation_amt',
							 N'$modip',
							 N'$last_member_idx'
							)
						";

						$rst = parent::execSqlInsert($qry);
						if ($rst) {
							$inserted++;
							$insertedArray[] = $rst;
						}
					}
				}

			}

			//전달 받은 상품목록 개수와 저장된 개수가 같으면
			//Commit
			if (count($product_list) == count($insertedArray)) {

				$matchingType = "MANUAL";

				if ($saveYn  == "Y") {
					$matchingType = "AUTO";
				}

				//발주의 발주진행 상태를 상품매칭[ORDER_PRODUCT_MATCHING] 으로 변경
				//매칭 타입을 수동[MANUAL] 로 변경
				//190830 자동 저장을 눌렀을 경우, [AUTO] 로
				$qry = "
					Update DY_ORDER
					Set order_progress_step = N'ORDER_PRODUCT_MATCHING'
					    , matching_type = N'$matchingType'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where order_idx = N'$order_idx'
				";
				$rst = parent::execSqlUpdate($qry);

				//Commit
				parent::sqlTransactionCommit();     //트랜잭션 커밋

			} else {
				//Rollback
				parent::sqlTransactionRollback();     //트랜잭션 롤백
			}
		}else{
			//Rollback
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}
		parent::db_close();

		return $insertedArray;
	}

	public function updateMatchingIdxForManual($matchedList, $matchingInfoIdx) {
		parent::db_connect();

		foreach ($matchedList as $match) {
			$qry = "
				UPDATE DY_ORDER_PRODUCT_MATCHING
				SET matching_info_idx = N'$matchingInfoIdx'
				WHERE order_matching_idx = N'$match'
			";

			parent::execSqlUpdate($qry);
		}

		parent::db_close();
	}

	/**
	 * 자동 상품 매칭 작업을 위한 수집단계의 발주 목록 반환
	 * 발주 진행 상태값이 : 주문정보수집단계(ORDER_COLLECT) 인 것 만
	 * @return array
	 */
	public function getOrderListForAutoMatching()
	{
		global $GL_Member;
		$qry = "
			Select order_idx, seller_idx, market_product_no, market_product_name, market_product_option
			From DY_ORDER
			Where order_is_del = N'N' And order_progress_step = N'ORDER_COLLECT'
		";

		//벤더사 로그인일 경우
		if(!isDYLogin()){
			$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		$qry .= " Order by order_idx ASC ";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * [자동 매칭] 발주 IDX 를 가지고 상품 매칭 정보를 검색 하여 매칭 정보 입력
	 * @param $order_idx : 발주 IDX
	 * @return bool
	 */
	public function execOrderMatching($order_idx){

		global $GL_Member;
		$modip           = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;
		$isExists = 0;
		$isMatchingCount = 0;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주 IDX 로 매칭 정보가 있는지 확인
		$qry      = "Select count(*) as cnt From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_idx = N'$order_idx'";
		$isExists = parent::execSqlOneCol($qry);

		//이미 매칭 정보가 있다면 Pass
		if ($isExists === 0) {

			//판매처, 주문금액 가져오기
			$qry = "Select seller_idx, order_amt, order_calculation_amt FROM DY_ORDER Where order_idx = N'$order_idx'";
			$_order = parent::execSqlOneRow($qry);
			$seller_idx = $_order["seller_idx"];
			$order_amt = $_order["order_amt"];
			$order_calculation_amt = $_order["order_calculation_amt"];

			//판매처 타입 확인 [벤더사 판매처인지]
			//벤더사 판매처 등급 확인
			$qry = "
				Select S.seller_type, V.vendor_grade 
				From DY_SELLER S
				Left Outer Join DY_MEMBER_VENDOR V On S.seller_idx = V.member_idx 
				Where seller_idx = N'$seller_idx'
			";
			$_seller_info = parent::execSqlOneRow($qry);
			$seller_type = $_seller_info["seller_type"];
			$vendor_grade = $_seller_info["vendor_grade"];

			//매칭 정보 확인 및 저장
			$qry = "
				Insert Into DY_ORDER_PRODUCT_MATCHING
				(
				  order_idx, seller_idx
				  , product_idx, product_option_idx, product_option_cnt
				  , order_matching_is_auto, matching_info_idx, order_matching_regip, last_member_idx
				)
				( 
				Select
			       '$order_idx', ORD.seller_idx
					, ML.product_idx, ML.product_option_idx, (ML.product_option_cnt * ORD.order_cnt)
					, N'Y', MI.matching_info_idx, '$modip', '$last_member_idx'
				  From DY_ORDER ORD
				    Left Outer Join DY_PRODUCT_MATCHING_INFO MI
			            On ORD.seller_idx = MI.seller_idx
			              And (Case When ORD.market_product_no_is_auto = 'N' 
			                Then ORD.market_product_no Else '' End) = MI.market_product_no
				          And ORD.market_product_name = MI.market_product_name
				          And ORD.market_product_option = MI.market_product_option
					Left Outer Join DY_PRODUCT_MATCHING_LIST ML
						On MI.matching_info_idx = ML.matching_info_idx
				  Where ORD.order_is_del = N'N' And ORD.order_idx = N'$order_idx' And ML.product_idx is not null And MI.matching_info_is_del = N'N'
				)
			";

			$rst = parent::execSqlUpdate($qry);


			//자동 매칭 되었는지 확인
			//매칭 테이블과 상품테이블을 조인하여 판매가 및 매입가 가져오기
			if($vendor_grade) {
				$price_col = "O.product_option_sale_price_" . $vendor_grade;
			}else{
				$price_col = 0;
			}
			$qry = "
					Select 
						M.order_matching_idx, M.product_option_cnt
						, P.product_idx, O.product_option_idx
						, P.product_sale_type, " . $price_col . " as product_option_sale_price
						, O.product_option_purchase_price
					From DY_ORDER_PRODUCT_MATCHING M
						Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
						Left Outer Join DY_PRODUCT_OPTION O On M.product_option_idx = O.product_option_idx
					Where 
					      M.order_matching_is_del = N'N' And M.order_idx = N'$order_idx'
				    Order By M.order_matching_idx ASC
						
			";

			//parent::log_write($qry);

			//$isMatchingCount = parent::execSqlOneCol($qry);
			$matching_list = parent::execSqlList($qry);

			//매칭된 내역이 있다면
			if($matching_list) {

				//매칭된 내역들의 판매가격, 매입가격 을 Update
				foreach($matching_list as $key => $prd) {
					$order_matching_idx            = $prd["order_matching_idx"];
					$product_option_idx            = $prd["product_option_idx"];
					$product_option_cnt            = $prd["product_option_cnt"];
					$product_option_sale_price     = $prd["product_option_sale_price"];         //판매가격
					$product_option_purchase_price = $prd["product_option_purchase_price"];     //매입가격
					$product_calculation_amt       = 0;                                         //정산예정금액
					$product_sale_type             = $prd["product_sale_type"];

					/**
					 * ------------------------------------------------------
					 * |     판매처    | 상품 타입 | 판매가 |    매입가   |
					 * ------------------------------------------------------
					 * |  마켓 판매처  |    자체   |   X    | 송장시 입력 |
					 * |  마켓 판매처  |    위탁   |   X    |      O      |
					 * |  벤더 판매처  |    자체   |   O    | 송장시 입력 |
					 * |  벤더 판매처  |    위탁   |   O    |      O      |
					 * * ------------------------------------------------------
					 */

					//판매가격, 매입가격 가져오기
					//벤더사 판매처가 아닐 경우 판매가격은 0 으로 설정됨.
					$_option_price_ary = $this->getProductOptionPriceBySeller($product_option_idx, $vendor_grade);
					$product_option_sale_price = $_option_price_ary["product_option_sale_price"];
					$product_option_purchase_price = $_option_price_ary["product_option_purchase_price"];

					//마켓 판매처 일 경우
					//주문금액을 수량으로 나눈다 (소수점 버림)
					//첫번째 상품만 입력 나머지는 0
					if($seller_type != "VENDOR_SELLER" && $key == 0) {
						$product_option_sale_price = floor($order_amt / $product_option_cnt);

						//주문에 정산예정금액이 있으면
						///첫번째 상품에 1/n 하여 정산예정금액 저장
						if($order_calculation_amt > 0){
							$product_calculation_amt = floor($order_calculation_amt / $product_option_cnt);
						}
					}

					if ($product_option_sale_price == INF || $product_calculation_amt == INF)
						continue;

					//자체상품 일 경우 매입가를 0 으로 설정
					//송장단계에서 재고 매칭 시 재고원가를 입력 받는다
					if ($product_sale_type == "SELF") {
						$product_option_purchase_price = 0;
					}

					$qry = "
						Update DY_ORDER_PRODUCT_MATCHING
						Set
							product_option_sale_price = N'$product_option_sale_price'
							, product_option_purchase_price = N'$product_option_purchase_price'
							, product_calculation_amt = N'$product_calculation_amt'
						Where
							order_matching_idx = N'$order_matching_idx'		
					";
					$tmp = parent::execSqlUpdate($qry);
				}

				//발주의 발주진행 상태를 상품매칭[ORDER_PRODUCT_MATCHING] 으로 변경
				//매칭 타입을 자동[AUTO] 으로 변경
				$qry = "
					Update DY_ORDER
					Set order_progress_step = N'ORDER_PRODUCT_MATCHING'
					    , matching_type = N'AUTO'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where order_idx = N'$order_idx'
				";
				$rst = parent::execSqlUpdate($qry);

				//Commit
				parent::sqlTransactionCommit();     //트랜잭션 커밋

				$returnValue = true;
			}else{

				//Rollback
				parent::sqlTransactionRollback();     //트랜잭션 롤백
			}


		}

		parent::db_close();

		return $returnValue;
	}

	/**
	 * 매칭 내역 삭제
	 * @param $order_idx
	 * @return bool
	 */
	public function cancelOrderMatching($order_idx){
		global $GL_Member;
		$modip           = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주 IDX 로 매칭 정보가 있는지 확인
		$qry      = "Select count(*) as cnt From DY_ORDER_PRODUCT_MATCHING Where order_matching_is_del = N'N' And order_idx = N'$order_idx'";
		$isExists = parent::execSqlOneCol($qry);

		//이미 매칭 정보가 있다면
		if ($isExists > 0) {

			//매칭 내역 삭제
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
				Set order_matching_is_del = N'Y', order_matching_moddate = getdate(), order_matching_modip = N'$modip', last_member_idx = N'$last_member_idx'
				Where order_idx = N'$order_idx'
			";
			parent::execSqlUpdate($qry);

			//주문 상태 되돌리기
			$qry = "
				Update DY_ORDER
				Set order_progress_step = N'ORDER_COLLECT'
					, order_is_auto = N'N'
					, order_moddate = getdate()
					, order_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where order_idx = N'$order_idx'
			";
			parent::execSqlUpdate($qry);

			//Commit
			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}

		parent::db_close();

		return $returnValue;
	}

	/**
	 * 합포 가능한 주문인지 여부
	 * @param $order_idx
	 * @return array
	 */
	public function isCanOrderPackageAdd($order_idx, $order_pack_idx){

		$returnValue = array();
		$returnValue["result"] = false;
		$returnValue["msg"] = "";

		$qry = "
			Select 
			       O.*
				   , (Select count(*) From DY_ORDER OO Where OO.order_pack_idx = O.order_idx And O.order_is_del = N'N') as pack_count
			From DY_ORDER O
			Where O.order_idx = N'$order_idx' And O.order_is_del = N'N'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		if($_view) {

			$qry = "
				Select seller_idx From DY_ORDER Where order_idx = N'$order_pack_idx'
			";
			parent::db_connect();
			$_pack_view = parent::execSqlOneRow($qry);
			parent::db_close();

			if ($_view["order_idx"] != $_view["order_pack_idx"] || $_view["pack_count"] > 1) {
				$returnValue["result"] = false;
				$returnValue["msg"]    = "이미 합포된 주문입니다.";
			} elseif ($_view["order_is_lock"] == "Y") {
				$returnValue["result"] = false;
				$returnValue["msg"]    = "합포 금지된 주문입니다.";
			} elseif ($_view["seller_idx"] != $_pack_view["seller_idx"]) {
				$returnValue["result"] = false;
				$returnValue["msg"]    = "판매처가 서로 다른 주문은 합포할 수 없습니다.";
			} elseif ($_view["order_progress_step"] != "ORDER_ACCEPT" && $_view["order_progress_step"] != "ORDER_ACCEPT_TEMP") {
                $returnValue["result"] = false;
                $returnValue["msg"]    = "접수 상태의 주문만 합포추가가 가능합니다.";
            } else {
				$returnValue["result"] = true;
			}
			$returnValue["o_seller_idx"] = $_view["seller_idx"];
			$returnValue["p_seller_idx"] = $_pack_view["seller_idx"];

		}else{
			$returnValue["result"] = false;
			$returnValue["msg"]    = "잘못된 주문번호입니다.";
		}



		return $returnValue;

	}

	/**
	 * 합포 실행!
	 * @param $current_order_idx : 합포될 발주 IDX
	 * @param $parent_order_idx : 합포 대상 IDX
	 * @param $cs_msg : CS 이력 추가 메시지
	 * @param $is_order_package_page : 발주 단에서 합포 일 경우
	 * @return bool|resource
	 */
	public function execOrderPackage($current_order_idx, $parent_order_idx, $cs_msg = "", $is_order_package_page = false){

		$addQry = "";

		if($is_order_package_page){
			$addQry = ", order_progress_step = N'ORDER_PACKING' ";
		}

		//현 주문 합포로 변경
		$qry = "
			Update DY_ORDER
				Set order_pack_idx = N'$parent_order_idx'
				Where order_idx = N'$current_order_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();


		//합포된 주문들 Flag 변경
		$qry = "
			Update DY_ORDER
				Set order_is_pack = N'Y'
				$addQry
				Where order_pack_idx = N'$parent_order_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		$C_CS = new CS();
		$cs_task = "PACKAGE_ADD";    //합포추가
		$cs_idx = $C_CS -> insertCS($current_order_idx, $parent_order_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);

		return $rst;
	}

	/**
	 * 합포가능한 주문이 있는지 확인
	 * 리턴 값 : 합포 가능한 주문 수
	 * @return int
	 */
	public function checkOrderPackageAble(){

		global $GL_Member;

		$returnValue = 0;
		$addQry = "";

		//벤더사 로그인일 경우
		if(!isDYLogin()){
			$addQry .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		$qry = "
			WITH CTE_TEST as (
				SELECT 
				Min(order_idx) OVER(PARTITION BY seller_idx, receive_name, receive_addr1, receive_tp_num, receive_hp_num  ORDER BY order_idx) as min_order_idx
				, *
				FROM DY_ORDER A
				Where A.order_is_del = N'N'
					AND EXISTS (
						SELECT 1
						FROM DY_ORDER O2
						WHERE O2.order_is_del = N'N'
							AND O2.order_progress_step = N'ORDER_PRODUCT_MATCHING'
							AND A.seller_idx = O2.seller_idx
							AND A.receive_name = O2.receive_name
							AND A.receive_addr1 = O2.receive_addr1
							AND A.receive_tp_num = O2.receive_tp_num
							AND A.receive_hp_num = O2.receive_hp_num
							And O2.order_is_lock = N'N'
						GROUP BY O2.receive_name
							,O2.receive_addr1
							,O2.receive_tp_num
							,O2.receive_hp_num
						HAVING count(receive_name) > 1
						)
					AND A.order_progress_step = N'ORDER_PRODUCT_MATCHING' And A.order_is_lock = N'N'
					$addQry
			)
			
			Select min_order_idx, count(min_order_idx) as cnt
			,     STUFF((SELECT ',' + convert(nvarchar(20), order_idx)
			           FROM CTE_TEST b
			           WHERE b.min_order_idx = a.min_order_idx
			          FOR XML PATH('')), 1, 1, '') as order_idx_list
			From CTE_TEST a
			Group by min_order_idx
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		if($rst){
			$returnValue = $rst;
		}

		return $returnValue;
	}


	/**
	 * 자동으로 합포가능한 주문을 합포!
	 * 리턴 값 : 합포된 주문 수
	 * @return int
	 */
	public function autoOrderPackageExec(){

		global $GL_Member;

		$returnValue = 0;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			SELECT 
			Min(order_idx) OVER(PARTITION BY seller_idx, receive_name, receive_addr1, receive_tp_num, receive_hp_num  ORDER BY order_idx) as min_order_idx
			, *
			FROM DY_ORDER A
			Where A.order_is_del = N'N'
				AND EXISTS (
					SELECT 1
					FROM DY_ORDER O2
					WHERE O2.order_is_del = N'N'
						AND O2.order_progress_step = N'ORDER_PRODUCT_MATCHING'
						AND A.seller_idx = O2.seller_idx
						AND A.receive_name = O2.receive_name
						AND A.receive_addr1 = O2.receive_addr1
						AND A.receive_tp_num = O2.receive_tp_num
						AND A.receive_hp_num = O2.receive_hp_num
						AND O2.order_is_lock = N'N'
					GROUP BY O2.receive_name
						,O2.receive_addr1
						,O2.receive_tp_num
						,O2.receive_hp_num
					HAVING count(receive_name) > 1
					)
				AND A.order_progress_step = N'ORDER_PRODUCT_MATCHING' AND A.order_is_lock = N'N'
		";

		//벤더사 로그인일 경우
		if(!isDYLogin()){
			$qry .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		$rst = parent::execSqlList($qry);

		if($rst){
			foreach($rst as $o){
				$order_idx = $o["order_idx"];
				$order_pack_idx = $o["min_order_idx"];

				$qry = "
					Update DY_ORDER
					Set order_pack_idx = N'$order_pack_idx', order_progress_step = N'ORDER_PACKING', order_is_pack = N'Y'
					Where order_idx = N'$order_idx'
				";

				parent::execSqlUpdate($qry);
			}

			$returnValue = count($rst);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		$C_CS    = new CS();
		foreach($rst as $o) {
			$order_idx      = $o["order_idx"];
			$order_pack_idx = $o["min_order_idx"];

			if($order_idx != $order_pack_idx) {
				$cs_task = "PACKAGE_ADD";    //합포추가
				$cs_msg = "자동합포";
				$cs_idx  = $C_CS->insertCS($order_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);
			}
		}


		return $returnValue;
	}

	/**
	 * 발주 완료 대기 목록 반환
	 * 합포까지 모두 완료된 상태의 주문건 불러오기
	 * (재고부족 처리 되어 접수 되지 않은 주문건 포함)
	 * @return array
	 */
	public function getOrderReadyToComplete(){
		global $GL_Member;

		$qry = "
			Select * 
			From
				DY_ORDER
			Where 
				order_progress_step in (N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_SHORTAGE')
				And order_is_del = N'N'
		";

		//벤더사 로그인일 경우
		if(!isDYLogin()){
			$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		$qry .= " Order by order_idx ASC ";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
	    return $rst;
	}

	/**
	 * 주문 발주 완료 - 접수처리
	 * 가접수 상태로 접수
	 * 정상 재고를 카운팅 하여, 재고 부족 일 시 접수 하지 않음
	 * @return array : ["주문 건수", "접수된 주문 건수", "재고부족 주문 건수"]
	 */
	public function updateOrderToAcceptTemp($tmp_randno){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = array();
		$returnValue["order_cnt"] = 0;      //주문 건수
		$returnValue["accept_cnt"] = 0;     //접수된 주문 건수
		$returnValue["shortage_cnt"] = 0;   //재고부족 주문 건수

		/**
		 * 합포까지 모두 완료된 상태의 주문건 불러오기
		 * (재고부족 처리 되어 접수 되지 않은 주문건 포함)
		 */
		$qry = "
			Select * 
			From
				DY_ORDER
			Where 
				order_progress_step in (N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_SHORTAGE')
				And order_is_del = N'N'
			Order by order_idx ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//접수 대기 중인 주문이 있는지 확인
		if($rst)
		{
			//접수 대기 중[ORDER_PRODUCT_MATCHING] 주문 ForEach
			foreach($rst as $ord){

				//주문건 수 Count Up
				$returnValue["order_cnt"]++;

				//주문번호
				$_order_idx = $ord["order_idx"];

				//발급된 임시 키를 Update
				$qry = "Update DY_ORDER
							Set tmp_randno = N'$tmp_randno'
							Where order_idx = N'$_order_idx'
				";
				$tmp = parent::execSqlUpdate($qry);

				//매칭 된 상품 리스트
				$qry = "
					Select M.order_matching_idx, M.order_idx, M.product_idx, M.product_option_idx, M.product_option_cnt
							, P.product_sale_type
					From
						DY_ORDER_PRODUCT_MATCHING M 
						Left Outer Join DY_PRODUCT P On M.product_idx = P.product_idx
					Where 
						order_idx = N'$_order_idx'
						And order_matching_is_del = N'N'
					Order by order_matching_idx ASC
				";
				$_list_m_prod = parent::execSqlList($qry);

				if($_list_m_prod)
				{
					//재고 여부 Flag
					$_TF_able_stock = true;
					/**
					 * 재고 확인 중 한 상품이라도 재고 부족 일 시
					 * 해당 주문은 미접수 처리
					 * (미접수 처리 및 재고 부족 Flag 설정)
					 * 변경!!! 재고 여유와 상관 없이 접수로 입력
					 */
					foreach ($_list_m_prod as $prod){

						$_order_matching_idx = $prod["order_matching_idx"];
						$_product_idx        = $prod["product_idx"];
						$_product_option_idx = $prod["product_option_idx"];
						$_product_option_cnt = $prod["product_option_cnt"];
						$_product_sale_type  = $prod["product_sale_type"];

						//자체 상품일경우에만 재고 확인
						if($_product_sale_type == "SELF") {
							//재고 확인
							//정상재고 수 가져오기
                            //TODO: 재고 수량 가져올때 is_proc 을 사용하지 않도록 변경해야하는 것이 아닌지
							$qry = "
								Select 
									Sum(
								    Case 
									  When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0
									End) as normal_cnt 
								From DY_STOCK
								WHERE 
									stock_is_del = N'N' And stock_is_proc = N'Y' And stock_is_confirm = N'Y'
									And product_option_idx = N'$_product_option_idx'
								Group by product_idx, product_option_idx
							";

							$normal_cnt = parent::execSqlOneCol($qry);

							//주문 수량 보다 정상 재고가 작으면 "재고부족"
							if ($_product_option_cnt > $normal_cnt) {
								//재고 부족
								$_TF_able_stock = false;

								//재고 부족 수량
								$order_matching_is_shortage_cnt = $_product_option_cnt - $normal_cnt;

								//매칭 상품 테이블에 재고부족 Flag 설정
								$qry = "
									Update DY_ORDER_PRODUCT_MATCHING
									Set order_matching_is_shortage = N'Y'
									, order_matching_is_shortage_cnt = N'$order_matching_is_shortage_cnt'
									Where order_matching_idx = N'$_order_matching_idx'
								";
								parent::execSqlUpdate($qry);
							}
						}
					}

					//재고 여유 일 경우
					if(!$_TF_able_stock){
						$returnValue["shortage_cnt"]++;
					}
					//재고 여유와 상관 없이 접수로 입력
					//if($_TF_able_stock){

						//상품 재고 테이블에 접수 Insert
						/**
						 * 단가 0원 으로 입력
						 * 주문에 의한 재고 stock_kind = ORDER
						 * 가접수 상태 stock_status = ACCEPT_TEMP
						 *
						 * !!IMPORTANT!! - 19.01.31
						 * - 배송(출고) 전 까지는 재고 테이블에 재고 관련 사항 입력 하지 않음
						 * - 기존에는 접수 시 부터 접수된 재고 수량을 파악하기 위해 필요 했지만
						 * - DY_ORDER_PRODUCT_MATCHING 테이블을 통해 접수 및 송장 상태인 재고 수량 Count
						 */
						/*
						$qry = "
							Insert Into DY_STOCK
							(
								product_idx, product_option_idx,
								stock_kind, order_idx,
							    stock_type, stock_status,
							    stock_unit_price, stock_amount,
							    stock_request_date, stock_request_member_idx,
							    stock_modip, last_member_idx
							)
							VALUES
							(
							 N'$_product_idx',
							 N'$_product_option_idx',
							 N'ORDER',
							 N'$_order_idx',
							 0,
							 N'ACCEPT_TEMP',
							 0,
							 N'$_product_option_cnt',
							 getdate(),
							 N'$last_member_idx',
							 N'$modip',
							 N'$last_member_idx'
							)
						";


						$inserted_idx = parent::execSqlInsert($qry);
						*/

						//재고 ref_idx Update
						/*
						$qry = "
							Update DY_STOCK
								Set stock_ref_idx = N'$inserted_idx'
								Where stock_idx = N'$inserted_idx'
						";
						$tmp = parent::execSqlUpdate($qry);
						*/

						//주문 State 가접수 상태로 변경 [ORDER_ACCEPT_TEMP]
						//가접수 date 도 Update
						$qry = "
							Update DY_ORDER
								Set order_progress_step = N'ORDER_ACCEPT_TEMP'
									, order_progress_step_accept_temp_date = getdate()
							Where order_idx = N'$_order_idx'
						";
						$tmp = parent::execSqlUpdate($qry);

						//접수된 주문 건수 Count Up
						$returnValue["accept_cnt"]++;
					//}else{

					/*
						//재고 부족일 경우
						//주문 State 재고부족으로 변경 [ORDER_SHORTAGE]
						$qry = "
							Update DY_ORDER
								Set order_progress_step = N'ORDER_SHORTAGE'
							Where order_idx = N'$_order_idx'
						";
						$tmp = parent::execSqlUpdate($qry);

						//재고부족 주문 건수 Count Up
						$returnValue["shortage_cnt"]++;
					*/
					//}

				}

			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		return $returnValue;
	}

	/**
	 * 주문일괄삭제
	 * 발주에 의한 주문 (C/S 가 아닌) 모두 삭제 (order_is_after_order = N'N')
	 * 아래 단계에 있는 주문들을 모두 삭제 한다. (order_progress_step in .....)
	 * 단계 : 수집, 매칭, 합포, 가접수, 접수, 송장
	 * 검색 조건인 발주일시 는 주문생성일시 (order_regdate)
	 * 삭제는 Delete 가 아닌 is_order_del 필드 업데이트(Y)
	 * @param $seller_idx : 판매처 IDX
	 * @param $date : 발주일 Y-m-d
	 * @param $time_start : 발주시간 시작 h:i:s
	 * @param $time_end : 발주시간 끝 h:i:s
	 * @return bool|int|mixed
	 */
	public function deleteOrderBatchDelete($seller_idx, $date, $time_start, $time_end)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$search_start = $date . " " . $time_start;
		$search_end = $date . " " . $time_end;

		$time_full = $time_start . " ~ " . $time_end;

		if(validateDate($search_start) && validateDate($search_end)) {

			$qry = "
				Select count(*) as cnt From DY_ORDER
					Where order_is_del = N'N'
						And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_ACCEPT_TEMP', N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_is_after_order = N'N'
					    
						And order_regdate >= N'$search_start' 
						And order_regdate <= N'$search_end' 
			";

			//벤더사 로그인일 경우
			if(!isDYLogin()){
				$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
			}else{
				$qry .= " And seller_idx = N'$seller_idx'";
			}


			parent::db_connect();
			$returnValue = parent::execSqlOneCol($qry);
			parent::db_close();


			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작

			//매칭 상품 내역 삭제(Update)
			$qry = "
				Update DY_ORDER_PRODUCT_MATCHING
				Set order_matching_moddate = getdate(), order_matching_modip = N'$modip', order_matching_is_del = N'Y'
				Where order_idx in (
			      Select order_idx 
			      From DY_ORDER 
			      Where order_is_del = N'N'
						And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_ACCEPT_TEMP', N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_is_after_order = N'N'
						And order_regdate >= N'$search_start' 
						And order_regdate <= N'$search_end' 
			    )
			";

			//벤더사 로그인일 경우
			if(!isDYLogin()){
				$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
			}else{
				$qry .= " And seller_idx = N'$seller_idx'";
			}
			$rst = parent::execSqlUpdate($qry);

			//관련 재고 삭제
			$qry = "
				Update DY_STOCK
					Set stock_is_del = N'N'
						, stock_moddate = getdate()
						, stock_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where stock_is_del = N'N' And order_idx in (
				      Select order_idx 
				      From DY_ORDER 
				      Where order_is_del = N'N'
							And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_ACCEPT_TEMP', N'ORDER_ACCEPT', N'ORDER_INVOICE')
							And order_is_after_order = N'N'
							And order_regdate >= N'$search_start' 
							And order_regdate <= N'$search_end' 
			";
			//벤더사 로그인일 경우
			if(!isDYLogin()){
				$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
			}else{
				$qry .= " And seller_idx = N'$seller_idx'";
			}

			$qry .= "
				    )
			";
			$tmp = parent::execSqlUpdate($qry);

			//삭제 업데이트
			$qry = "
				Update DY_ORDER
					Set order_is_del = N'Y'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where order_is_del = N'N'
						And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING', N'ORDER_ACCEPT_TEMP', N'ORDER_ACCEPT', N'ORDER_INVOICE')
						And order_is_after_order = N'N'
						And order_regdate >= N'$search_start' 
						And order_regdate <= N'$search_end' 
			";
			if(!isDYLogin()){
				$qry .= " And seller_idx = N'".$GL_Member["member_idx"]."'";
			}else{
				$qry .= " And seller_idx = N'$seller_idx'";
			}
			$tmp = parent::execSqlUpdate($qry);

			if(!isDYLogin()){
				$seller_idx = $GL_Member["member_idx"];
			}

			//로그 삽입
			$qry = "
				Insert Into DY_ORDER_DELETE_LOG
				(seller_idx, order_date, order_time, order_delete_log_count, order_delete_log_regip, last_member_idx)
				VALUES
				(
				 N'$seller_idx',
				 N'$date',
				 N'$time_full',
				 N'$returnValue',
				 N'$modip',
				 N'$last_member_idx'
				)
			";
			$inserted_idx = parent::execSqlInsert($qry);

			if($inserted_idx){
				parent::sqlTransactionCommit();     //트랜잭션 커밋
			}else{
				parent::sqlTransactionRollback();     //트랜잭션 롤백
			}

			parent::db_close();
		}

		return $returnValue;
	}

	/**
	 * 현재 발주 수량 목록 (그래프)
	 */
	public function getOrderAcceptListBySeller()
	{
		global $GL_Member;
		$qry = "
			Select
	            A.seller_idx, A.seller_name
				, (
					Select count(*) From DY_ORDER O 
					Where 
					O.seller_idx = A.seller_idx 
					And O.order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING')
					And order_is_del = N'N'
				) as cnt
			From DY_SELLER A
			Where A.seller_is_use = N'Y' And A.seller_is_del = N'N'
		";

		if(!isDYLogin()){
			$qry .= " And A.seller_idx = N'".$GL_Member["member_idx"]."'";
		}

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 주문 정보 반환 - 송장입력 엑셀 업로드 시 검증 사용
	 * @param $order_idx
	 * @param bool $isInTransaction         : 트랜잭션이 필요 할 경우 db_connect, db_close 를 하지 않는다.
	 * @return array|false|null
	 */
	public function getOrderDataForInvoiceUpload($order_idx, $isInTransaction = false){
		$qry = "
			Select
				A.*
				, C.code_name as order_progress_step_han
				, DC.delivery_name
				, (Select count(*) From DY_ORDER OO WITH (NOLOCK) Where OO.order_pack_idx = A.order_pack_idx And OO.order_is_del = N'N') as pack_cnt 
			From DY_ORDER A WITH (NOLOCK)
				Left Outer Join DY_CODE C WITH (NOLOCK) On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
				Left Outer Join DY_SELLER S WITH (NOLOCK) On S.seller_idx = A.seller_idx
				Left Outer Join
			    (
			      Select delivery_code, delivery_name
		            From DY_DELIVERY_CODE WITH (NOLOCK)
		            Group by delivery_code, delivery_name
			    )
			    as DC 
				  On DC.delivery_code = A.delivery_code
			Where
				order_is_del = N'N'
				And order_idx = N'$order_idx'
		";

		if(!$isInTransaction) parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		if(!$isInTransaction) parent::db_close();

		return $rst;
	}

	/**
	 * 택배사명으로 택배사 코드 가져오기
	 * @param $delivery_name
	 * @return bool|int|mixed
	 */
	public function getDeliveryCodeByName($delivery_name)
	{
		$returnValue = false;

		$qry = "
			Select delivery_code
	            From DY_DELIVERY_CODE
				Where delivery_name = N'$delivery_name'
	            Group by delivery_code, delivery_name
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		if($rst){
			$returnValue = $rst;
		}

		return $returnValue;
	}

	/**
	 * 택배사명으로 택배사 코드 가져오기
	 * @param $delivery_code
	 * @return bool|int|mixed
	 */
	public function getDeliveryNameByCode($delivery_code)
	{
		$returnValue = false;

		$qry = "
			Select delivery_name
	            From DY_DELIVERY_CODE
				Where delivery_code = N'$delivery_code'
	            Group by delivery_code, delivery_name
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		if($rst){
			$returnValue = $rst;
		}

		return $returnValue;
	}

	/**
	 * 재고 확인
	 * order_idx 로 관련(합포) 주문을 모두 포함하여
	 * 상품들의 재고 여부를 확인한다.
	 * 관련 주문에 재고가 부족한 상품이 있을 경우 False 반환
	 * @param $order_idx
	 * @return bool
	 */
	public function checkOrderProductStock($order_idx, $isInTransaction = false)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//주문건 확인
		$_view = $this->getOrderDataForInvoiceUpload($order_idx, $isInTransaction);

		if($_view) {
			//접수, 송장 상태 일 때만 송장입력 가능
			if ($_view["order_progress_step"] == "ORDER_ACCEPT" || $_view["order_progress_step"] == "ORDER_INVOICE") {
				/*
				 * 재고 조사 시작
				 */

				//재고가 모자른 상품 확인
				//사입/자체 상품만 해당
				//취소 상품 제외
				$_search_order_pack_idx = $_view["order_pack_idx"];
				$qry = "
					Select 
				       count(*)
					From DY_ORDER_PRODUCT_MATCHING M WITH (NOLOCK)
					    Left Outer Join DY_PRODUCT_OPTION PO WITH (NOLOCK) On M.product_option_idx = PO.product_option_idx
					    Left Outer Join DY_PRODUCT P WITH (NOLOCK) On P.product_idx = PO.product_idx
						Left Outer Join 
					    (
					        Select
				              product_option_idx
			                  , IsNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL
				              From DY_STOCK STOCK WITH (NOLOCK)
					          Where STOCK.stock_is_del = N'N'
				                And STOCK.stock_is_confirm = N'Y'
					          Group by STOCK.product_option_idx
				        ) as S On M.product_option_idx = S.product_option_idx
					Where 
					      M.order_matching_is_del = N'N'
					      And M.order_idx in (Select order_idx From DY_ORDER O WITH (NOLOCK) Where O.order_pack_idx = N'$_search_order_pack_idx')
					      And P.product_sale_type = N'SELF'
					      And M.product_option_cnt > isNull(S.stock_amount_NORMAL, 0)
						  And M.order_cs_status <> N'ORDER_CANCEL'
				";


				if(!$isInTransaction) parent::db_connect();
				$shortage_cnt = parent::execSqlOneCol($qry);
				if(!$isInTransaction) parent::db_close();

				//재고 부족 상품이 없을 경우에 True
				if($shortage_cnt == 0){
					$returnValue = true;
				}
			}
		}

		return $returnValue;
	}

	/**
	 * 송장번호 사용여부 확인 함수
	 * 이미 다른 주문에 입력된 송장번호인지 체크
	 * 리턴값이 true 일 사용이 불가능한 송장번호
	 * 리턴값이 false 일 사용가능한 송장번호
	 * @param $invoice_no       : 송장번호
	 * @return bool             : T/F
	 */
	public function isUsedInvoiceNo($invoice_no)
	{
		$qry = "
			Select count(*) From DY_ORDER
			Where
				invoice_no = N'$invoice_no'
				And order_is_del = N'N'
		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($cnt){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 송장 입력 함수
	 * 송장입력 엑셀 업로드를 통해 입력 됨
	 * 송장입력이 가능한 상태는 접수[ORDER_ACCEPT], 송장[ORDER_INVOICE] 만 가능
	 * TODO: 재고 조사, 재고 처리
	 * 송장 입력 시 합포된 주문은 모두 동일한 송장번호로 업데이트 되므로
	 * 합포 된 모든 주문의 모든 상품에 대하여 재고 조사 및 재고 차감을 한다.
	 * 따라서 합포된 주문건들이 각각 송장 입력될 때를 대비하여 $isAlreadyStockCheck 인자를 추가함.
	 * $isAlreadyStockCheck 는 기본 값이 False 이며 False 일 경우 재고조사 및 재고차감 실행
	 * True 일 경우 이미 재고 조사 및 재고차감을 한것으로 간주 하고 송장 번호만 Update 한다.
	 * $isAlreadyStockCheck 값과 상관 없이 이미 송장[ORDER_INVOICE] 상태인 주문의 경우 재고조사 및 재고차감을 하지 않는다!!
	 * @param $order_idx                    : 주문 IDX
	 * @param $invoice_no                   : 반영송장번호
	 * @param string $delivery_code         :
	 * @param bool $isAlreadyStockCheck     : 이미 재고 조사를 한 경우 T/F (True 일 경우 재고 조사 및 재고차감 없이 송장 Update)
	 * @param string $cs_msg                : CS 이력 입력 시 메시지
	 * @param string $invoice_reg_type      : 송장 등록 형식 (CS, AUTO, XLS)
	 * @return array
	 */
	public function updateOrderStepToInvoice($order_idx, $invoice_no, $delivery_code = "ETC", $isAlreadyStockCheck = false, $cs_msg = "", $invoice_reg_type = "CS")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue           = array();
		$returnValue["result"] = false;
		$returnValue["msg"]    = "";

		//주문건 확인
		$_view = $this->getOrderDataForInvoiceUpload($order_idx);

		//송장번호 사용가능 여부 확인
		$_invoice_chk = $this->isUsedInvoiceNo($invoice_no);

		//true 일 경우 이미 사용된 송장번호
		if($_invoice_chk){
			$returnValue["result"] = false;
			$returnValue["msg"]    = "이미 사용중인 송장번호 입니다.";
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($_view){
			//접수, 송장 상태 일 때만 송장입력 가능
			//보류 중인 주문 제외 - 19.06.18
			if(
				($_view["order_progress_step"] == "ORDER_ACCEPT" || $_view["order_progress_step"] == "ORDER_INVOICE")
				&& $_view["order_is_hold"] == "N"
			) {

				//재고 확인 Flag
				$_IsStockOK = true;

				//재고조사가 필요 할 경우
				if(!$isAlreadyStockCheck && $_view["order_progress_step"] == "ORDER_ACCEPT") {

					//재고 확인
					$chkStock = $this->checkOrderProductStock($_view["order_idx"], true);

					//재고 부족시 return
					if (!$chkStock) {
						$returnValue["msg"]    = "재고가 부족합니다.";
						return $returnValue;
					}

					//송장 상태로 입력 될 재고 리스트 배열
					$_StockOutAry = array();

					//관련 상품들의 재고 입력
					//INVOICE -1 상태로 입력 한다

					$_search_order_pack_idx = $_view["order_pack_idx"];

					//관련 상품들 중 자체 / 사입 상품만 Load
					//취소된 상품 제외
					$qry = "
						Select 
					       M.*, P.product_idx
						From DY_ORDER_PRODUCT_MATCHING M WITH (NOLOCK) 
						    Left Outer Join DY_PRODUCT_OPTION PO WITH (NOLOCK) On M.product_option_idx = PO.product_option_idx
						    Left Outer Join DY_PRODUCT P WITH (NOLOCK) On P.product_idx = PO.product_idx
						Where 
						      M.order_matching_is_del = N'N'
						      And M.order_idx in (Select order_idx From DY_ORDER O WITH (NOLOCK) Where O.order_pack_idx = N'$_search_order_pack_idx')
						      And P.product_sale_type = N'SELF'
							  And M.order_cs_status <> 'ORDER_CANCEL'
					";

					$_prod_list = parent::execSqlList($qry);

					//관련 상품들 ForEach
					foreach ($_prod_list as $prod) {
						$_order_matching_idx = $prod["order_matching_idx"];
						$_product_option_idx = $prod["product_option_idx"];

						//주문 수량
						$order_product_option_cnt = intval($prod["product_option_cnt"]);


						/*
						 * 가용재고 구하기
						 * 먼저 등록 된 금액의 재고가 먼저 소진 됨
						 * TODO : 선입선출 관련 내용 확인 필요
						 * TODO : 100원 1개 등록, 110원 10개 등록, 100원 1개 등록 순으로 등록되어 있다고 가정 시
						 * TODO : 2개 차감 할 경우
						 * TODO : 100원짜리 1개가 선 등록 되어 있기 때문에 100원 2개가 먼저 차감 됨
						 * TODO : 100원짜리 재고가 모두 소진된 후 110원 짜리 재고 소진됨
						 * TODO : 110원 짜리 재고 소진 중 100원 짜리 재고가 등록 될 경우 다시 100원짜리 재고가 소진됨
						 */
						$qry = "WITH StockALL As (
								Select
								product_idx, product_option_idx, stock_unit_price
								, IsNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL
								, Min(stock_regdate) as min_date
								From DY_STOCK S WITH (NOLOCK)
								Where
									S.stock_is_del = N'N'
									And product_option_idx = N'$_product_option_idx'
									And stock_status = N'NORMAL'
									And stock_is_confirm = N'Y' 
														
								Group by product_idx, product_option_idx, stock_unit_price
							)
							Select * From StockAll Where stock_amount_NORMAL > 0 Order by min_date ASC
						";

						$_stock_list = parent::execSqlList($qry);

						//가용재고 리스트
						if ($_stock_list) {
							foreach ($_stock_list as $stock) {

								//남은 수량이 없으면 Pass
								if($order_product_option_cnt == 0){
									break;
								}

								//현 금액의 재고 수량
								$_currentStockPriceAmount = intval($stock["stock_amount_NORMAL"]);


								if ($order_product_option_cnt > $_currentStockPriceAmount) {
									//현재 금액의 재고가 부족일 경우
									//현재 금액의 재고만큼만 차감 후 Next

									$_StockOutAry[] = array(
										"order_idx" => $prod["order_idx"],
										"order_matching_idx" => $prod["order_matching_idx"],
										"product_idx" => $stock["product_idx"],
										"product_option_idx" => $stock["product_option_idx"],
										"stock_unit_price" => $stock["stock_unit_price"],
										"invoice_cnt" => $_currentStockPriceAmount
									);

									$order_product_option_cnt = $order_product_option_cnt - $_currentStockPriceAmount;

								} else {
									//재고가 충분 할 경우
									//주문 수량 만큼 차감

									$_StockOutAry[] = array(
										"order_idx" => $prod["order_idx"],
										"order_matching_idx" => $prod["order_matching_idx"],
										"product_idx" => $stock["product_idx"],
										"product_option_idx" => $stock["product_option_idx"],
										"stock_unit_price" => $stock["stock_unit_price"],
										"invoice_cnt" => $order_product_option_cnt
									);

									$order_product_option_cnt = 0;
								}
							}

							//재고 차감을 모두 하였음에도 주문 수량에 못 미칠 경우
							if ($order_product_option_cnt > 0) {
								$_IsStockOK = false;
								break;
							}

						} else {
							$_IsStockOK = false;
							break;
						}
					}
				}


				if(!$_IsStockOK){
					//재고가 부족함!!
					$returnValue["result"] = false;
					$returnValue["msg"]    = "재고가 부족합니다.";
					parent::sqlTransactionRollback();     //트랜잭션 롤백
				}else {
					//재고가 가 있다면...

					//재고 조사 및 차감이 필요 할 때만
					if(!$isAlreadyStockCheck && $_view["order_progress_step"] == "ORDER_ACCEPT") {
						//재고 차감 등록 시작
						foreach ($_StockOutAry as $out) {
							$this->insertStockOut($out["product_idx"], $out["product_option_idx"], $out["stock_unit_price"], $out["invoice_cnt"], "INVOICE", "ORDER", "송장입력", $out["order_idx"], $out["order_matching_idx"]);
						}
					}

					//주문서 상태 변경
					$qry = "
						Update DY_ORDER
							Set order_progress_step = N'ORDER_INVOICE'
							    , invoice_date = getdate()
							    , delivery_code = N'$delivery_code'
								, invoice_no = N'$invoice_no'
							    , invoice_reg_type = N'$invoice_reg_type'
								, order_moddate = getdate()
								, order_modip = N'$modip'
								, last_member_idx = N'$last_member_idx'
							--Where order_pack_idx = N'$order_idx'
							Where order_pack_idx in (Select order_pack_idx From DY_ORDER WITH (NOLOCK) Where order_idx = N'$order_idx')
					";
					$rst = parent::execSqlUpdate($qry);

					//order_pack_idx 구하기
					$qry = "Select top 1 order_pack_idx From DY_ORDER WITH (NOLOCK) Where invoice_no = N'$invoice_no' And order_is_del = 'N' ";
					$order_pack_idx = parent::execSqlOneCol($qry);

					//택배사명 구하기
					$qry = "SELECT delivery_name FROM DY_DELIVERY_CODE WITH (NOLOCK) WHERE market_code = N'DY' AND delivery_code = N'$delivery_code'";
					$delivery_name = parent::execSqlOneCol($qry);
					$cs_msg = "[택배사 : ".$delivery_name.", 송장번호 : ".$invoice_no."]\n".$cs_msg;

					$C_CS = new CS();
					$cs_task = "INVOICE_INSERT";    //송장입력
					$cs_idx = $C_CS -> insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);

					$returnValue["result"] = true;
					parent::sqlTransactionCommit();     //트랜잭션 커밋
					//parent::sqlTransactionRollback();     //트랜잭션 롤백
				}

			}else{
				parent::sqlTransactionRollback();  //트랜잭션 롤백
			}

		}else{
			parent::sqlTransactionRollback();  //트랜잭션 롤백
		}

		parent::db_close();

		return $returnValue;
	}

	/**
	 * 재고 차감 입력 함수
	 * NORMAL -1 , 넘겨받은 STATUS +1
	 * 트랜잭션을 고려하여 db_connect, db_close 를 하지 않는 함수
	 * 호출 시 db_connect 필요
	 * @param $product_idx
	 * @param $product_option_idx
	 * @param $stock_unit_price     : 재고 금액
	 * @param $stock_amount         : 재고 차감 수량
	 * @param $stock_status         : 재고 상태 (송장 :INVOICE, 배송: SHIPPED)
	 * @param $stock_kind           : 재고 구분 (주문에 의한 재고 : ORDER)
	 * @param $stock_msg
	 * @param $order_idx
	 * @param $order_matching_idx
	 * @return int
	 */
	public function insertStockOut($product_idx, $product_option_idx, $stock_unit_price, $stock_amount, $stock_status, $stock_kind, $stock_msg, $order_idx, $order_matching_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_STOCK
			(
			 stock_ref_idx, product_idx, product_option_idx, stock_kind, order_idx, order_matching_idx, stock_order_idx,
			 stock_order_is_ready, stock_order_msg, stock_in_date, stock_due_date, 
			 stock_type, stock_status, stock_unit_price, stock_due_amount, stock_amount, stock_msg, 
			 stock_file_idx, stock_request_date, stock_request_member_idx, stock_is_proc, stock_is_proc_date, stock_is_proc_member_idx, 
			 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx, 
			 stock_regip, last_member_idx
		    ) 
		    VALUES 
			(
			 0, N'$product_idx', N'$product_option_idx', N'$stock_kind', N'$order_idx', N'$order_matching_idx', 0,
			 N'N', N'$stock_msg', null, null,
			 -1, N'NORMAL', N'$stock_unit_price', 0, N'$stock_amount', N'$stock_msg',
			 0, getdate(), N'$last_member_idx', N'Y', getdate(), N'$last_member_idx',
			 N'Y', getdate(), N'$last_member_idx',
			 N'$modip', N'$last_member_idx'
			)
		";

		$inserted_idx1 = parent::execSqlInsert($qry);

		$qry = "
			Update DY_STOCK
				Set stock_ref_idx = N'$inserted_idx1'
				Where stock_idx = N'$inserted_idx1'
		";
		$tmp = parent::execSqlUpdate($qry);

		$qry = "
			Insert Into DY_STOCK
			(
			 stock_ref_idx, product_idx, product_option_idx, stock_kind, order_idx, order_matching_idx, stock_order_idx,
			 stock_order_is_ready, stock_order_msg, stock_in_date, stock_due_date, 
			 stock_type, stock_status, stock_unit_price, stock_due_amount, stock_amount, stock_msg, 
			 stock_file_idx, stock_request_date, stock_request_member_idx, stock_is_proc, stock_is_proc_date, stock_is_proc_member_idx, 
			 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx,
			 stock_invoice_date, 
			 stock_regip, last_member_idx
		    ) 
		    VALUES 
			(
			 0, N'$product_idx', N'$product_option_idx', N'$stock_kind', N'$order_idx', N'$order_matching_idx', 0,
			 N'N', N'$stock_msg', null, null,
			 1, N'$stock_status', N'$stock_unit_price', 0, N'$stock_amount', N'$stock_msg',
			 0, getdate(), N'$last_member_idx', N'Y', getdate(), N'$last_member_idx',
			 N'Y', getdate(), N'$last_member_idx',
			 getdate(), 
			 N'$modip', N'$last_member_idx'
			)
		";

		$inserted_idx2 = parent::execSqlInsert($qry);

		$qry = "
			Update DY_STOCK
				Set stock_ref_idx = N'$inserted_idx2'
				Where stock_idx = N'$inserted_idx2'
		";
		$tmp = parent::execSqlUpdate($qry);

		$qry = "
			Update DY_ORDER_PRODUCT_MATCHING 
				Set product_option_purchase_price = N'$stock_unit_price'
				Where order_matching_idx = N'$order_matching_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		return $inserted_idx2;
	}

	/**
	 * 송장입력 업로드 이력 저장
	 * 업로드된 임시 파일을 저장폴더(DY_ORDER_INVOICE_PATH) 로 이동 후
	 * 로그 Insert
	 * @param $xls_filename : 임시 저장된 엑셀 파일명
	 * @param $user_filename : 사용자가 업로드한 엑셀 파일명
	 * @param $apply_count : 송장 입력 수
	 * @return bool|int
	 */
	public function insertOrderInvoiceUploadLog($xls_filename, $user_filename, $apply_count)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;
		$dest_filename_fullpath = DY_ORDER_INVOICE_PATH . "/" . $xls_filename;
		if(file_exists($xls_filename_fullpath))
		{
			if(rename($xls_filename_fullpath, $dest_filename_fullpath)){

				$qry = "
					Insert Into DY_ORDER_INOVICE_UPLOAD_LOG
					(
					 order_invoice_upload_log_apply_count, order_invoice_upload_log_savefilename
					 , order_invoice_upload_log_userfilename, order_invoice_upload_log_regip, last_member_idx
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

	/**
	 * 송장입력 업로드 이력 - 사용자 엑셀 파일 명 반환
	 * @param $order_invoice_upload_log_idx : 업로드 이력 IDX
	 * @param $save_filename : 실제저장된 파일명
	 * @return int|mixed
	 */
	public function getOrderInvoiceUploadLogFileInfo($order_invoice_upload_log_idx, $save_filename)
	{
		$qry = "
			Select order_invoice_upload_log_userfilename
			From DY_ORDER_INOVICE_UPLOAD_LOG
			WHERE 
				order_invoice_upload_log_idx_is_del = N'N'
				And order_invoice_upload_log_idx = N'$order_invoice_upload_log_idx'
				And order_invoice_upload_log_savefilename = N'$save_filename'
		";

		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 송장일괄삭제 시 송장번호 체크 함수
	 * 삭제 가능한 송장번호 인지 체크
	 * 배송주문 포함하여 반환
	 * TODO : 배송주문 포함/미포함 분리하여 결과를 리턴 해야 할지 결정 필요
	 * @param $invoice_no
	 * @return int|mixed
	 */
	public function getOrderInvoiceIsUse($invoice_no)
	{
		$qry = "
			Select count(*)
			From DY_ORDER
			Where order_is_del = N'N' And invoice_no = N'$invoice_no'
		";

		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 송장일괄삭제(개별) 함수
	 * 입력한 송장번호를 사용하는 주문건의 송장번호를 빈값으로 수정하고
	 * 해당 주문의 STEP 을 접수로 변경한다
	 * - 19.02.01 : 송장일괄삭제 시, 재고차감된 내역도 복원 한다. (stock_is_del = 'Y')
	 * TODO : 배송주문 포함 삭제 시 배송주문도 접수로 변경해야 하는지 여부
	 * @param $invoice_no           : 송장번호
	 * @param $is_include_shipped   : 배송주문도 포함하여 삭제 할지 여부 (T/F)
	 * @param string $cs_msg        : CS 이력 입력 시 메시지
	 * @return bool|resource
	 */
	public function deleteOrderInvoiceByInvoiceNo($invoice_no, $is_include_shipped, $cs_msg = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$target_step = array();
		$target_step[] = "'ORDER_INVOICE'";

		if($is_include_shipped)
		{
			$target_step[] = "'ORDER_SHIPPED'";
		}

		$target_step_join = implode(",", $target_step);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//order_pack_idx 구하기
		$qry = "Select top 1 order_pack_idx From DY_ORDER Where invoice_no = N'$invoice_no' And order_is_del = 'N' ";
		$order_pack_idx = parent::execSqlOneCol($qry);

		//차감된 재고 복구
		$qry = "
			Update DY_STOCK
			Set 
				stock_is_del = N'Y'
				, stock_moddate = getdate()
				, stock_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			From DY_STOCK S
			Inner Join DY_ORDER O On O.order_idx = S.order_idx
			Where
		        O.invoice_no = N'$invoice_no'
			    And S.stock_is_del = N'N'
				And O.order_progress_step in ($target_step_join)
		";
		$rst = parent::execSqlUpdate($qry);

		$qry = "
			Update DY_ORDER
			Set
				invoice_no = N'', invoice_date = null
			    , order_progress_step = N'ORDER_ACCEPT'
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
				invoice_no = N'$invoice_no'
				And order_progress_step in ($target_step_join)
		";
		$rst = parent::execSqlUpdate($qry);

		if($order_pack_idx) {
			$C_CS    = new CS();
			$cs_task = "INVOICE_DELETE";    //송장입력
			$cs_idx  = $C_CS->insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);
		}
		parent::sqlTransactionCommit();
		parent::db_close();

		return $rst;
	}

	/**
	 * 송장일괄삭제(일괄) 함수
	 * 범위로 넘어온 일시를 기준으로 검색된 주문건의
	 * 송장번호를 빈값으로 수정하고 STEP을 접수로 변경한다.
	 * - 19.02.01 : 송장일괄삭제 시, 재고차감된 내역도 복원 한다.
	 * TODO : 배송주문 포함 삭제 시 배송주문도 접수로 변경해야 하는지 여부
	 * @param $date                 : 송장입력일 (Y-m-d)
	 * @param $time_start           : 송장입력일시 시작 (H:i:s)
	 * @param $time_end             : 송장입력일시 종료 (H:i:s)
	 * @param $is_include_shipped   : 배송주문 포함 여부 (T/F)
	 * @return bool|int|mixed
	 */
	public function deleteOrderInvoiceAll($date, $time_start, $time_end, $is_include_shipped)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$search_start = $date . " " . $time_start;
		$search_end = $date . " " . $time_end;

		//대상 주문 STEP
		$target_step = array();
		$target_step[] = "'ORDER_INVOICE'"; //송장

		//배송주문 포함 일 경우
		if($is_include_shipped)
		{
			$target_step[] = "'ORDER_SHIPPED'"; //배송
		}

		$target_step_join = implode(",", $target_step);

		if(validateDate($search_start) && validateDate($search_end)) {

			$qry = "
				Select order_idx From DY_ORDER
					Where order_is_del = N'N'
						And order_progress_step in ($target_step_join)
						And invoice_date >= N'$search_start' 
						And invoice_date <= N'$search_end' 
			";

			parent::db_connect();
			$_order_list = parent::execSqlList($qry);
			parent::db_close();

			$returnValue = count($_order_list);

			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작

			//차감된 재고 복구
			foreach ($_order_list as $ord) {
				$_order_idx = $ord["order_idx"];
				if($_order_idx) {
					$qry = "
						Update DY_STOCK
							Set stock_is_del = N'Y'
								, stock_moddate = getdate(), stock_modip = N'$last_member_idx'
							Where stock_is_del = N'N' And order_idx = N'$_order_idx'
					";

					parent::execSqlUpdate($qry);
				}
			}

			//삭제 업데이트
			$qry = "
				Update DY_ORDER
					Set invoice_no = N''
					    , invoice_date = null
					    , order_progress_step = N'ORDER_ACCEPT'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where order_is_del = N'N'
						And order_progress_step in ($target_step_join)
						And invoice_date >= N'$search_start' 
						And invoice_date <= N'$search_end' 
			";
			$tmp = parent::execSqlUpdate($qry);

			parent::sqlTransactionCommit();
			parent::db_close();
		}

		return $returnValue;
	}

	/**
	 * 동일한 송장번호를 가지고 있는 주문 반환 함수
	 * 상태가 송장[ORDER_INVOICE] 인 것 만
	 * @param $invoice_no   : 송장번호
	 * @return array
	 */
	public function getOrderListByInvoiceNo($invoice_no)
	{
		$qry = "
			Select * , C.code_name as order_progress_step_han
			From DY_ORDER A
				Left Outer Join DY_CODE C On C.parent_code = N'ORDER_PROGRESS_STEP' And C.code = A.order_progress_step
			Where order_is_del = N'N'
			And invoice_no = N'$invoice_no'
			And order_progress_step = N'ORDER_INVOICE'
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 배송처리 함수
	 * 주문 IDX 로 해당 배송처리 (STEP 을 ORDER_SHIPPED 로 변경)
	 * 현재 상태가 ORDER_INVOICE 이고 보류중이 아닌 주문만 가능
	 * TODO : ORDER_INVOICE 상태의 주문만 배송처리할 것인지 검토 필요
	 * @param $order_idx
	 * @param $cs_msg       : CS 이력 입력 메시지
	 * @return bool|resource
	 */
	public function updateOrderStepToShipped($order_idx, $cs_msg = ""){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//주문건 확인
		$_view = $this->getOrderDataForInvoiceUpload($order_idx);

		if($_view){
			//송장상태 일 때만 배송처리 가능
			//보류 상태일 때 불가
			if($_view["order_progress_step"] == "ORDER_INVOICE" && $_view["order_is_hold"] == "N") {

				parent::db_connect();
				parent::sqlTransactionBegin();  //트랜잭션 시작

				//재고 배송상태 변경
				$qry = "
					Update DY_STOCK
						Set stock_status = N'SHIPPED'
							, stock_moddate = getdate(), stock_modip = N'$modip', last_member_idx = N'$last_member_idx'
						Where order_idx = N'$order_idx'
				";
				$tmp = parent::execSqlUpdate($qry);

				//주문 배송상태 변경
				$qry = "
					Update DY_ORDER
						Set order_progress_step = N'ORDER_SHIPPED'
						    , shipping_date = getdate()
						    , shipping_member_idx = N'$last_member_idx'
							, order_moddate = getdate()
							, order_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where order_idx = N'$order_idx'
				";


				$returnValue = parent::execSqlUpdate($qry);

				parent::sqlTransactionCommit();     //트랜잭션 커밋
				parent::db_close();
			}
		}

		return $returnValue;
	}

	/**
	 * 배송처리 일괄 함수
	 * 송장번호로 해당 주문 모두 배송처리 (STEP 을 ORDER_SHIPPED 로 변경)
	 * 현재 상태가 ORDER_INVOICE 이고 보류중이 아닌 주문만 가능
	 * - 19.02.01 : 송장으로 입력된 재고 차감 건도 배송상태로 변경
	 * TODO : ORDER_INVOICE 상태의 주문만 배송처리할 것인지 검토 필요
	 * @param $invoice_no
	 * @param $cs_msg           : CS 이력 입력 메시지
	 * @return bool|resource
	 */
	public function updateOrderStepToShippedByInvoiceNo($invoice_no, $cs_msg = ""){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//order_pack_idx 구하기
		$qry = "Select top 1 order_pack_idx From DY_ORDER Where invoice_no = N'$invoice_no' And order_is_del = 'N' ";
		$order_pack_idx = parent::execSqlOneCol($qry);

		//재고 배송상태 변경
		$qry = "
			Update DY_STOCK
			Set stock_status = N'SHIPPED', stock_shipped_date = getdate()
			  , stock_moddate = getdate(), stock_modip = N'$last_member_idx'
			From DY_STOCK S
			Inner Join DY_ORDER O On O.order_idx = S.order_idx
			Where
		        O.invoice_no = N'$invoice_no' And O.order_is_hold = N'N' And S.stock_is_del = N'N' And S.stock_status = N'INVOICE'
		";
		$rst = parent::execSqlUpdate($qry);

		//주문 상태 변경
		$qry = "
			Update DY_ORDER
				Set order_progress_step = N'ORDER_SHIPPED'
				    , shipping_date = getdate()
				    , shipping_member_idx = N'$last_member_idx'
					, order_moddate = getdate()
					, order_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where invoice_no = N'$invoice_no' And order_is_hold = N'N' And order_is_del = N'N'
		";
		$returnValue = parent::execSqlUpdate($qry);

		//정산 테이블 Insert

		if($order_pack_idx) {
			$C_CS    = new CS();
			$cs_task = "SHIPPED_CONFIRM";    //배송확인
			$cs_idx  = $C_CS->insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);
		}

		//정상테이블 입력
		$this->insertSettleShipped($order_pack_idx, false, false);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $returnValue;
	}

	/**
	 * !! 배송 처리 시 정산 테이블 입력 함수
	 * 위탁 상품의 경우 일괄접수처리 시 정산 테이블에 입력된다.
	 * $forConsignmentProduct 인자 값이 true 일 경우 자체/사입 상품은 건너뛰고 위탁상품만 쿼리에서 불러온다.
	 * @param $order_pack_idx
	 * @param bool $forConsignmentProduct
	 * @param bool $requireDbConnection
	 */
	public function insertSettleShipped($order_pack_idx, $forConsignmentProduct = false ,$requireDbConnection = false)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		if($requireDbConnection){
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작
		}

		$keyWhereQry = " And O.order_pack_idx = N'$order_pack_idx' ";

		//위탁상품 전용 정산입력이라면
		if($forConsignmentProduct){
			$keyWhereQry = " And O.order_idx = N'$order_pack_idx' ";
			$forConsignmentQry = " And P.product_sale_type = N'CONSIGNMENT'";
		}else{
			$keyWhereQry = " And O.order_pack_idx = N'$order_pack_idx' And P.product_sale_type <> N'CONSIGNMENT' ";
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
			    And M.order_cs_status in (N'NORMAL', N'PRODUCT_CHANGE')
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

				//자체 상품이면 매입단가를 재고매입단가로 Update
				//재고 수량으로 대체
				if($product_sale_type == "SELF")
				{
					$product_option_purchase_price = $stock_unit_price;
					$product_option_cnt = $stock_amount;
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
					$order_amt        = $order_unit_price * $product_option_cnt;

					//벤더사 판매처가 아니라면
					//두번째 매칭 상품 부터는 판매단가 및 판매가 공란
					//
					//같은 주문에 여러의 상품이 매칭된 경우
					//한 상품에 여러개(원가가 다른)의 재고가 연결 된 경우
					if($prev_order_idx == $order_idx) {
						$order_unit_price = ($product_option_sale_price == 0) ? 0 : $product_option_sale_price;
						$order_amt        = $order_unit_price * $product_option_cnt;
					}
				}



				//같은 합포 상품이라면
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
	 * 배송일괄취소(파일) 시 송장번호 체크 함수
	 * 배송 취소 가능한 송장번호 인지 체크
	 * STEP 이 배송[ODER_SHIPPED] 인 주문만 체크
	 * @param $invoice_no
	 * @return int|mixed
	 */
	public function getOrderShippedInvoiceIsUse($invoice_no)
	{
		$qry = "
			Select count(*)
			From DY_ORDER
			Where order_is_del = N'N' And invoice_no = N'$invoice_no' And order_progress_step = N'ORDER_SHIPPED'
		";

		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 배송일괄취소(개별) 함수
	 * 입력한 송장번호를 사용하는 주문건의 STEP 을 송장[ORDER_INVOICE] 로 변경한다.
	 * 상태가 배송[ORDER_SHIPPED] 인 주문만 해당
	 * - 19.02.01 : 배송상태인 재고차감 건 송장상태로 변경
	 * @param $invoice_no       : 송장번호
	 * @param $cs_msg           : CS 이력 입력 메시지
	 * @return bool|resource
	 */
	public function updateOrderShippedToCancelByInvoiceNo($invoice_no, $cs_msg = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//order_pack_idx 구하기
		$qry = "Select top 1 order_pack_idx From DY_ORDER Where invoice_no = N'$invoice_no' And order_is_del = 'N' ";
		$order_pack_idx = parent::execSqlOneCol($qry);

        if($order_pack_idx) {
			$C_CS    = new CS();

            //order_matching_idx 구하기
            $qry = "
			Select OPM.order_matching_idx
			From DY_ORDER O
				Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
			Where 
				O.order_is_del = N'N' 
				And OPM.order_cs_status != 'ORDER_CANCEL'
				And O.order_pack_idx = N'$order_pack_idx'
		    ";
            $_opm_list = parent::execSqlList($qry);

            //각 주문 정산 입력
            foreach($_opm_list as $ord) {
                 $order_matching_idx = $ord["order_matching_idx"];
                 $C_CS->insertSettleCancel($order_matching_idx,'',true,false, true);
            }

            $cs_task = "SHIPPED_CANCEL";    //배송취소
            $cs_idx  = $C_CS->insertCS($order_pack_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);
        }


		//재고 배송상태 변경
		$qry = "
			Update DY_STOCK
			Set stock_status = N'INVOICE', stock_moddate = getdate(), stock_modip = N'$last_member_idx'
			From DY_STOCK S
			Inner Join DY_ORDER O On O.order_idx = S.order_idx
			Where
		        O.invoice_no = N'$invoice_no' And O.order_is_hold = N'N' And S.stock_is_del = N'N' And S.stock_status = N'SHIPPED'
		";
        $rst = parent::execSqlUpdate($qry);


        //주문 배송 취소 -> 송장 상태로 변경
        $qry = "
			Update DY_ORDER
			Set
				shipping_date = null
			    , order_progress_step = N'ORDER_INVOICE'
				, order_moddate = getdate()
				, order_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
				invoice_no = N'$invoice_no'
				And order_progress_step = N'ORDER_SHIPPED'
		";
        $rst = parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $rst;
	}

	/**
	 * 배송취소(일괄) 함수
	 * 범위로 넘어온 일시를 기준으로 검색된 주문건의
	 * STEP을 송장[ORDER_INVOICE]로 변경한다.
	 * - 19.02.01 송장상태로 입력된 재고 차감 건을 배송상태로 변경
	 * @param $date                 : 송장입력일 (Y-m-d)
	 * @param $time_start           : 송장입력일시 시작 (H:i:s)
	 * @param $time_end             : 송장입력일시 종료 (H:i:s)
	 * @return bool|int|mixed
	 */
	public function updateOrderShippedCancelAll($date, $time_start, $time_end)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$search_start = $date . " " . $time_start;
		$search_end = $date . " " . $time_end;

		if(validateDate($search_start) && validateDate($search_end)) {

			$qry = "
				Select order_idx From DY_ORDER
					Where order_is_del = N'N'
						And order_progress_step = N'ORDER_SHIPPED'
						And shipping_date >= N'$search_start' 
						And shipping_date <= N'$search_end' 
			";

			parent::db_connect();
			$_order_list = parent::execSqlList($qry);
			parent::db_close();

			$returnValue = count($_order_list);

			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작

            $C_CS    = new CS();
			foreach ($_order_list as $ord) {
				$_order_idx = $ord["order_idx"];
				if($_order_idx) {
                    //정산 취소 입력
                    $qry = "
                    Select OPM.order_matching_idx
                    From DY_ORDER O
                        Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
                    Where 
                        O.order_is_del = N'N' 
                        And OPM.order_cs_status != 'ORDER_CANCEL'
                        And O.order_idx = N'$_order_idx'
                    ";
                    $order_matching_idx = parent::execSqlOneCol($qry);
                    $C_CS->insertSettleCancel($order_matching_idx,'',true,false, true);

                    //차감된 배송상태의 재고 -> 송장상태 로 변경
                    $qry = "
						Update DY_STOCK
							Set stock_status = N'INVOICE'
								, stock_moddate = getdate(), stock_modip = N'$last_member_idx'
							Where stock_is_del = N'N' And order_idx = N'$_order_idx' And stock_status = N'SHIPPED'
					";
					parent::execSqlUpdate($qry);
				}
			}


			//송장상태로 업데이트
			$qry = "
				Update DY_ORDER
					Set shipping_date = null
					    , order_progress_step = N'ORDER_INVOICE'
						, order_moddate = getdate()
						, order_modip = N'$modip'
						, last_member_idx = N'$last_member_idx'
					Where order_is_del = N'N'
						And order_progress_step = N'ORDER_SHIPPED'
						And shipping_date >= N'$search_start' 
						And shipping_date <= N'$search_end' 
			";
			$tmp = parent::execSqlUpdate($qry);

			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}

		return $returnValue;
	}

	/**
	 * 주문 상품 수량 가져오기
	 * @param $order_idx
	 * @param $product_option_idx
	 * @return bool|int|mixed
	 */
	public function getOrderMatchingProductOptionCnt($order_idx, $product_option_idx)
	{
		$returnValue = false;

		$qry = "
			Select product_option_cnt as cnt
			From DY_ORDER D 
			    Inner Join DY_ORDER_PRODUCT_MATCHING PM On D.order_idx = PM.order_idx
			Where
				D.order_is_del = N'N'
				And PM.order_matching_is_del = N'N'
				And D.order_idx = N'$order_idx'
				And PM.product_option_idx = N'$product_option_idx'
		";

		parent::db_connect();
		$returnValue = parent::execSqlOneCol($qry);
		parent::db_close();

		return $returnValue;
	}

	/**
	 * 합포제외 실행 함수
	 * 주문에서 합포제외한 목록을 받아
	 * 새로이 주문을 생성하여 목록의 상품들을 합포 함
	 * TODO: 전체 상품 수량이 모두 0 일 경우 주문 삭제 처리 필요! (상품이 여러개 매칭되어 있을 경우도 있음!)
	 * @param $except : 제외목록
	 * @param $cs_msg : CS 이력 추가 메시지
	 * @return bool
	 */
	public function separateOrderExceptOne($except, $cs_msg = "")
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = true;

		$new_order_pack_idx = "";

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		foreach($except as $except_one){

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

			if($remain_cnt >= $__product_option_cnt){

				//발주 IDX 생성
				$qry = "Select isnull(MAX(order_idx), 0) as max_order_idx From DY_ORDER";
				$max_order_idx = parent::execSqlOneCol($qry);
				$order_idx = 0;
				if($max_order_idx < 100001){
					$order_idx = 100001;
				}else{
					$order_idx = $max_order_idx + 1;
				}

				//함께 합포해야 할 상품이 있다면 합포IDX 받아오기
				//없으면 발주IDX 입력 후 다음 상품을 위하여 합포IDX 저장
				if($new_order_pack_idx != ""){
					$order_pack_idx = $new_order_pack_idx;
				}else{
					$order_pack_idx = $order_idx;
					$new_order_pack_idx = $order_idx;
				}

				//주문 입력
				//입력 시 합포 제외된 주문이므로
				//송장상태가 아닌 접수 상태로 입력 됨
				$qry = "

					Insert Into DY_ORDER
					(
					 order_idx, order_pack_idx, seller_idx, order_collect_idx, matching_type
					 , order_progress_step, order_progress_step_accept_temp_date, order_progress_step_accept_date, order_progress_step_accept_member_idx
					 , order_state, order_pack_code, order_pay_date, order_confirm_date
					 , market_order_no, market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto
					 , market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt
					 , order_calculation_amt, order_cnt, delivery_fee, order_pay_type
					 , order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode
					 , receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode
					 , receive_memo, delivery_code, invoice_no, delivery_type, delivery_is_free
					 , order_is_auto, order_org_data1, order_org_data2
					 , order_regip, last_member_idx
					)
					(
					Select 
						'$order_idx', '$order_pack_idx', D.seller_idx, D.order_collect_idx, D.matching_type
					    , N'ORDER_ACCEPT', D.order_progress_step_accept_temp_date, D.order_progress_step_accept_date, D.order_progress_step_accept_member_idx
						, D.order_state, D.order_pack_code, D.order_pay_date, D.order_confirm_date
						, D.market_order_no, D.market_order_no_is_auto, D.market_order_subno, D.market_product_no, D.market_product_no_is_auto
						, D.market_product_name, D.market_product_option, D.market_order_id, D.order_unit_price, D.order_amt, D.order_pay_amt
						, D.order_calculation_amt, D.order_cnt, D.delivery_fee, D.order_pay_type
					    , D.order_name, D.order_tp_num, D.order_hp_num, D.order_addr1, D.order_addr2, D.order_zipcode
						, D.receive_name, D.receive_tp_num, D.receive_hp_num, D.receive_addr1, D.receive_addr2, D.receive_zipcode
						, D.receive_memo, '', '', D.delivery_type, D.delivery_is_free
						, N'N', D.order_org_data1, D.order_org_data2
						, N'$modip', N'$last_member_idx'
					From DY_ORDER D 
					Where
						D.order_is_del = N'N'
						And D.order_idx = N'$__order_idx'
					)
				";

				$tmp = parent::execSqlInsert($qry);

				//주문 상품 입력
				$qry = "
					Insert Into DY_ORDER_PRODUCT_MATCHING
					(
					 order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt
					 , product_option_sale_price, product_option_purchase_price
					 , order_matching_regip, last_member_idx
					)
					(
					  Select
					    N'$order_idx', seller_idx, product_idx, product_option_idx, N'$__product_option_cnt'
				        , product_option_sale_price, product_option_purchase_price
					    , N'$modip', N'$last_member_idx'
					  From DY_ORDER_PRODUCT_MATCHING
					  Where order_matching_is_del = N'N'
					        And order_idx = N'$__order_idx' 
					        And product_option_idx = N'$__product_option_idx'
					        And order_matching_idx = N'$__order_matching_idx'
					) 
				";
				 $tmp2 = parent::execSqlInsert($qry);

				 //제외하는 원주문 합포 여부 확인
				$qry = "Select count(*) From DY_ORDER Where order_pack_idx = N'".$__order_pack_idx."' And order_is_del = N'N' ";
				$ori_pack_cnt = parent::execSqlOneCol($qry);
				if($ori_pack_cnt == 1){
					//주문개수가 1개 일 경우 합포여부 변경
					$qry = "Update DY_ORDER Set order_is_pack = N'N' Where order_pack_idx = N'".$__order_pack_idx."'";
				}

				//제외되어 신규 생성된 주문 합포 여부 확인
				$qry = "Select count(*) From DY_ORDER Where order_pack_idx = N'".$order_pack_idx."' And order_is_del = N'N' ";
				$ori_pack_cnt = parent::execSqlOneCol($qry);
				if($ori_pack_cnt > 1){
					//주문개수가 1개이상이 경우 합포여부 변경
					$qry = "Update DY_ORDER Set order_is_pack = N'Y' Where order_pack_idx = N'".$order_pack_idx."'";
				}

				 //제외하는 원 주문 CS
				$C_CS = new CS();
				$cs_task = "PACKAGE_EXCEPT";    //합포제외
				$cs_msg_add = $__order_idx . " 에서 " . $order_idx . " 합포 제외\n" . $cs_msg;
				$cs_idx = $C_CS -> insertCS($__order_idx, $__order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg_add, "", "", null, true);

				//제외되어 신규 생성 된 주문 CS
				$C_CS = new CS();
				$cs_task = "PACKAGE_EXCEPT";    //합포제외
				$cs_msg_add = $__order_pack_idx . " 에서 제외됨\n" . $cs_msg;
				$cs_idx = $C_CS -> insertCS($order_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg_add, "", "", null, true);


				 //기존 주문 상품 수 Update (minus)
				$qry = "
					Update DY_ORDER_PRODUCT_MATCHING
					Set product_option_cnt = product_option_cnt - " . $__product_option_cnt . "
					Where 
						order_idx = N'$__order_idx' 
						And product_option_idx = N'$__product_option_idx'
						And order_matching_idx = N'$__order_matching_idx'
				";
				$tmp3 = parent::execSqlUpdate($qry);

				//기존 주문 상품 수가 0 일 으로 Update 된 경우 삭제 처리
				$qry = "
						IF EXISTS (
							Select *
							From DY_ORDER_PRODUCT_MATCHING
							Where 
							order_idx = N'$__order_idx' 
							And product_option_idx = N'$__product_option_idx'
							And order_matching_idx = N'$__order_matching_idx'
							And product_option_cnt > 0
						)
							BEGIN
								SELECT 'HAVE' -- 상품수가 남아 있음
							END
						ELSE
							BEGIN
								Update DY_ORDER_PRODUCT_MATCHING
								Set order_matching_is_del = N'Y'
								Where 
									order_idx = N'$__order_idx' 
									And product_option_idx = N'$__product_option_idx'
									And order_matching_idx = N'$__order_matching_idx'
							END
				";

				$tmp3 = parent::execSqlUpdate($qry);

				//기존 주문에 남아 있는 상품이 없을 경우 삭제 처리
//				$qry = "
//					IF EXISTS (
//							Select *
//							From DY_ORDER_PRODUCT_MATCHING
//							Where
//							order_idx = N'$__order_idx'
//							And order_matching_is_del = N'N'
//						)
//							BEGIN
//								SELECT 'HAVE' -- 상품이 남아 있음
//							END
//						ELSE
//							BEGIN
//								Update DY_ORDER
//								Set order_is_del = N'Y'
//									, order_moddate = getdate()
//									, order_modip = N'$modip'
//									, last_member_idx = N'$last_member_idx'
//								Where
//									order_idx = N'$__order_idx'
//							END
//				";
//				$tmp4 = parent::execSqlUpdate($qry);



			}

		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		return $returnValue;
	}

	/**
	 * 주문 생성 함수
	 * 생성된 주문은 접수 상태로 입력됨
	 * @param $args
	 * @return bool
	 */
	public function insertNewAcceptOrder($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//변수 초기화
		$product_idx         = "";
		$product_option_idx  = "";
		$seller_idx          = "";
		$product_option_cnt  = "";
		$delivery_is_free    = "";
		$order_amt           = "";
		$market_order_no     = "";
		$order_name          = "";
		$order_tp_num        = "";
		$order_hp_num        = "";
		$order_zipcode       = "";
		$order_addr1         = "";
		$order_addr2         = "";
		$receive_name        = "";
		$receive_tp_num      = "";
		$receive_hp_num      = "";
		$receive_zipcode     = "";
		$receive_addr1       = "";
		$receive_addr2       = "";
        $receive_memo        = "";
		$product_name        = "";
		$product_option_name = "";
		$cs_msg              = "";
		$order_write_type    = "CS_WRITE";
		extract($args);

		//상품 확인
		$qry = "
			Select count(*) 
			From DY_PRODUCT P 
			  Inner Join DY_PRODUCT_OPTION O On O.product_idx = P.product_idx
			Where
				P.product_is_del = N'N' And O.product_option_is_del = N'N'
				And O.product_idx = N'$product_idx' And O.product_option_idx = N'$product_option_idx'
		";
		parent::db_connect();
		$_pChk = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_pChk != 1)
		{
			return $returnValue;
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주 IDX 생성
		$qry = "Select isnull(MAX(order_idx), 0) as max_order_idx From DY_ORDER";
		$max_order_idx = parent::execSqlOneCol($qry);
		$order_idx = 0;
		if($max_order_idx < 100001){
			$order_idx = 100001;
		}else{
			$order_idx = $max_order_idx + 1;
		}

		//합포IDX
		$order_pack_idx = $order_idx;

		//일괄 발주 일련번호
		$order_collect_idx = 0;

		//매칭타입
		$matching_type = "MANUAL";

		//발주 진행 상태 (접수)
		$order_progress_step = "ORDER_ACCEPT";

		//사용?
		$order_state = "";

		//쇼핑몰 주문 번호 자동 생성 여부
		$market_order_no_is_auto = "N";
		if($market_order_no == ""){
			$qry = "
					Select MAX(market_order_no) as market_order_no_max
					From DY_ORDER
					Where market_order_no_is_auto = N'Y'
				";

			$max_order_no = parent::execSqlOneCol($qry);

			$max_order_no_new = "";
			if(!$max_order_no){
				$max_order_no_new = "ord100001";
			}else {
				$max_order_no = intval(str_replace("ord", "", $max_order_no));
				$max_order_no_new = "ord".($max_order_no + 1);
			}
			$market_order_no = $max_order_no_new;
			$market_order_no_is_auto = "Y";
		}

		//배송비 정산 구분
		$delivery_type = "";
		if($delivery_is_free == "Y"){
			$delivery_type = "선불";
		}else{
			$delivery_type = "착불";
		}

		//주문 입력
		$qry = "
			Insert Into DY_ORDER
			(
			 order_idx, order_pack_idx, seller_idx, order_collect_idx
			 , order_progress_step, order_progress_step_accept_temp_date
			 , order_progress_step_accept_date, order_progress_step_accept_member_idx
			 , matching_type
			 , order_state, order_pack_code, order_pay_date, order_confirm_date
			 , market_order_no, market_order_no_is_auto, market_order_subno, market_product_no, market_product_no_is_auto
			 , market_product_name, market_product_option, market_order_id, order_unit_price, order_amt, order_pay_amt
			 , order_calculation_amt, order_cnt, delivery_fee, order_pay_type
			 , order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode
			 , receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode
			 , receive_memo, delivery_code, invoice_no, delivery_type, delivery_is_free
			 , order_is_auto, order_org_data1, order_org_data2, order_write_type
			 , order_regip, last_member_idx
			)
			 VALUES 
			(
				N'$order_idx',                
				N'$order_pack_idx',                
				N'$seller_idx',   
				N'$order_collect_idx',                
				N'$order_progress_step',                
				getdate(),                
				getdate(),                
				N'$last_member_idx',
				N'$matching_type',
				N'$order_state',
				N'',
				N'',
				getdate(),
				N'$market_order_no',
				N'$market_order_no_is_auto',
				N'',
				N'',
				N'N',
				N'$product_name',
				N'$product_option_name',
				N'',
				N'0',
				N'$order_amt',
				N'0',
				N'0',
				N'$product_option_cnt',
				N'0',
				N'',
				N'$order_name',
				N'$order_tp_num',
				N'$order_hp_num',
				N'$order_addr1',
				N'$order_addr2',
				N'$order_zipcode',
				N'$receive_name',
				N'$receive_tp_num',
				N'$receive_hp_num',
				N'$receive_addr1',
				N'$receive_addr2',
				N'$receive_zipcode',
				N'$receive_memo',
				N'',
				N'',
				N'$delivery_type',
				N'$delivery_is_free',
				N'N',
				N'',
				N'',
				N'$order_write_type',
				N'$modip',
				N'$last_member_idx'
			)
		";
		$rst = parent::execSqlInsert($qry);

		//주문 매칭 상품 입력
		$qry = "
			Insert Into DY_ORDER_PRODUCT_MATCHING
			(
			 order_idx, seller_idx, product_idx, product_option_idx, product_option_cnt
			, order_matching_is_auto, order_matching_regip, last_member_idx
			) VALUES 
			(
			 N'$order_idx',
			 N'$seller_idx',
			 N'$product_idx',
			 N'$product_option_idx',
			 N'$product_option_cnt',
			 N'N',
			 N'$modip',
			 N'$last_member_idx'
			)
		";
		$__order_matching_idx = parent::execSqlInsert($qry);

		//판매처 타입 확인 [벤더사 판매처인지]
		//벤더사 판매처 등급 확인
		$qry = "
			Select S.seller_type, isNull(V.vendor_grade, '') as vendor_grade 
			From DY_SELLER S
			Left Outer Join DY_MEMBER_VENDOR V On S.seller_idx = V.member_idx 
			Where seller_idx = N'$seller_idx'
		";
		$_seller_info = parent::execSqlOneRow($qry);
		$seller_type = $_seller_info["seller_type"];
		$vendor_grade = $_seller_info["vendor_grade"];

		//상품 타입 가져오기 [자제/사입], [위탁]
		$product_sale_type = "";
		$qry = "
			Select product_sale_type From DY_PRODUCT Where product_idx = N'$product_idx'
		";
		$product_sale_type = parent::execSqlOneCol($qry);

		//판매가격, 매입가격 가져오기
		//벤더사 판매처가 아닐 경우 판매가격은 0 으로 설정됨.
		$_option_price_ary = $this->getProductOptionPriceBySeller($product_option_idx, $vendor_grade);
		$product_option_sale_price = $_option_price_ary["product_option_sale_price"];
		$product_option_purchase_price = $_option_price_ary["product_option_purchase_price"];

		//벤더사 판매처가 아닐 경우
		//입력 받은 주문 금액을 제품 수량으로 나눠 입력
		if($seller_type != "VENDOR_SELLER") {
			$product_option_sale_price = floor($order_amt / $product_option_cnt);
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
			Where order_matching_idx = N'$__order_matching_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		//정상테이블 입력
		$this->insertSettleShipped($order_idx, true, false);

		if($__order_matching_idx) {

			$C_CS = new CS();
			$cs_task = "ORDER_WRITE";    //주문생성
			$cs_idx = $C_CS -> insertCS($order_idx, $order_pack_idx, 0, 0, 0, "Y", "", $cs_task, $cs_msg, "", "", null, true);

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
	 * 마켓 주문번호로 내부 ORDER 정보 조회
	 * 중복이 있을 경우 맨 마지막 주문의 order 정보로..
	 * @param $seller_idx
	 * @param $market_order_no
	 * @param $market_order_subno
	 * @return array|false|null: DY_ORDER OneRow
	 */
	public function getOrderByMarketOrderNo($seller_idx, $market_order_no, $market_order_subno)
	{
		$qry = "
			Select TOP 1 O.order_idx, O.order_pack_idx, O.order_progress_step, O.invoice_no, O.delivery_code, O.invoice_date, DC.market_delivery_code, DC.delivery_name
			FROM DY_ORDER O 
				INNER JOIN DY_SELLER S ON O.seller_idx = S.seller_idx 
				LEFT OUTER JOIN DY_DELIVERY_CODE DC ON O.delivery_code = DC.delivery_code AND S.market_code = DC.market_code
			Where O.seller_idx = N'$seller_idx' And O.market_order_no = N'$market_order_no' And O.market_order_subno = N'$market_order_subno'
			And O.order_is_del = N'N'
			ORDER BY O.order_idx DESC
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();
		return $_row;
	}
	/**
	 * 서브코드가 없어 Auto 로 들어간 마켓용 ORDER 정보 조회
	 * @param $seller_idx
	 * @param $market_order_no
	 * @return array|false|null
	 */
	public function getOrderByMarketOrderNo_autoSubNo($seller_idx, $market_order_no)
	{
		$qry = "
			Select TOP 1 O.order_idx, O.order_pack_idx, O.order_progress_step, O.invoice_no, O.delivery_code
			, O.invoice_date, DC.market_delivery_code, O.market_order_no, O.market_order_subno, DC.delivery_name 
			FROM DY_ORDER O 
				INNER JOIN DY_SELLER S ON O.seller_idx = S.seller_idx 
				LEFT OUTER JOIN DY_DELIVERY_CODE DC ON O.delivery_code = DC.delivery_code AND S.market_code = DC.market_code
			Where O.seller_idx = N'$seller_idx' And O.market_order_no = N'$market_order_no' 
			And O.order_is_del = N'N'
			ORDER BY O.order_idx DESC
		";
		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();
		return $_row;
	}


	/**
	 * 마켓 송장 등록 상태 Update
	 * @param $seller_idx
	 * @param $market_order_no
	 * @param $market_order_subno
	 * @param $market_invoice_state
	 * @param $market_invoice_msg
	 * @return bool|resource
	 */
	public function updateMarketInvoiceState($seller_idx, $market_order_no, $market_order_subno, $market_invoice_state, $market_invoice_msg)
	{
		$qry = "
			UPDATE DY_ORDER SET
				market_invoice_regdate = GETDATE() 
				,market_invoice_state  = N'$market_invoice_state'
				,market_invoice_msg    = N'$market_invoice_msg'
			WHERE seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no' And market_order_subno = N'$market_order_subno' And market_invoice_state != 'S'
		";
		parent::db_connect();
		$_ret = parent::execSqlUpdate($qry);
		parent::db_close();
		return $_ret;
	}

	/**
	 * 마켓 송장 등록 상태 Update (서브코드가 없어 Auto 로 들어간 마켓용)
	 * @param $seller_idx
	 * @param $market_order_no
	 * @param $market_order_subno
	 * @param $market_invoice_state
	 * @param $market_invoice_msg
	 * @return bool|resource
	 */
	public function updateMarketInvoiceState_autoSubNo($seller_idx, $market_order_no, $market_invoice_state, $market_invoice_msg)
	{
		$qry = "
			UPDATE DY_ORDER SET
				market_invoice_regdate = GETDATE() 
				,market_invoice_state  = N'$market_invoice_state'
				,market_invoice_msg    = N'$market_invoice_msg'
			WHERE seller_idx = N'$seller_idx' And market_order_no = N'$market_order_no' And market_invoice_state != 'S'
		";
		parent::db_connect();
		$_ret = parent::execSqlUpdate($qry);
		parent::db_close();
		return $_ret;
	}


	/**
	 * 벤더사 등급의 상품 옵션 판매가격, 매입가격 가져오기
	 * $vendor_grade 가 없거나 0 이면 판매가격은 0으로 반환
	 * @param $product_option_idx
	 * @param $vendor_grade
	 * @param bool $require_DB_Connection
	 * @return array
	 */
	public function getProductOptionPriceBySeller($product_option_idx, $vendor_grade, $require_DB_Connection = false)
	{
		if($require_DB_Connection){
			parent::db_connect();
		}

		$returnValue = array();

		$returnValue["product_option_purchase_price"] = 0;  //매입가
		$returnValue["product_option_sale_price"] = 0;      //판매가

		if(!empty(trim($vendor_grade))) {
			$col = "product_option_sale_price_" . $vendor_grade;
		}else{
			$col = 0;
		}
		$qry = "
			Select product_option_purchase_price, $col as product_option_sale_price
			From DY_PRODUCT_OPTION Where product_option_idx = N'$product_option_idx'
		";

		$_row = parent::execSqlOneRow($qry);

		$product_option_purchase_price = $_row["product_option_purchase_price"];
		$product_option_sale_price = $_row["product_option_sale_price"];

		$returnValue["product_option_purchase_price"] = $product_option_purchase_price;  //매입가

		if($product_option_sale_price) {
			$returnValue["product_option_sale_price"] = $product_option_sale_price;      //판매가
		}

		if($require_DB_Connection){
			parent::db_close();
		}

		return $returnValue;
	}

	/**
	 * 주문다운로드 파일 다운로드 로그 입력
	 * @param $save_filename
	 * @param $target_supplier_idx
	 * @param $delivery_type
	 * @param $order_progress_step
	 * @param $supplier_idx
	 * @param $seller_idx
	 * @param $date_start
	 * @param $date_end
	 * @param $receive_name
	 * @return int
	 */
	public function insertOrderDownloadFileLog($save_filename, $target_supplier_idx, $delivery_type, $order_progress_step, $supplier_idx, $seller_idx, $date_start, $date_end, $receive_name)
	{

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_ORDER_DOWNLOAD_FILE
			(
			  supplier_idx
			  , sch_delivery_type, sch_order_progress_step
			  , sch_supplier_idx, sch_seller_idx
			  , sch_date_start, sch_date_end
			  , sch_receive_name
			  , order_download_file_name, order_download_file_regip, last_member_idx
		    )
		    VALUES 
			(
			 N'$target_supplier_idx'
			 , N'$delivery_type'
			 , N'$order_progress_step'
			 , N'$supplier_idx'
			 , N'$seller_idx'
			 , N'$date_start'
			 , N'$date_end'
			 , N'$receive_name'
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
	 * 주문다운로드 파일 생성 로그 정보 반환
	 * @param $order_download_file_idx
	 * @return array|false|null
	 */
	public function getOrderDownloadFileLog($order_download_file_idx){
		$qry = "
			Select * From DY_ORDER_DOWNLOAD_FILE
			Where order_download_file_idx = N'$order_download_file_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 주문다운로드 파일 생성 로그 정보 반환
	 * @param $order_download_file_idx
	 * @return array|false|null
	 */
	public function getOrderDownloadFileLogDetail($order_download_file_idx){
		$qry = "
			Select F.*, S.supplier_email_order, S.supplier_name 
			From DY_ORDER_DOWNLOAD_FILE F
			Left Outer Join DY_MEMBER_SUPPLIER S On F.supplier_idx = S.member_idx
			Where order_download_file_idx = N'$order_download_file_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 주문다운로드 생성 파일 얻기
	 * $order_download_file_idx 가 없으면 최신 파일
	 * @param $supplier_idx
	 * @param string $order_download_file_idx
	 * @return array|false|null
	 */
	public function getLastOrderDownloadFile($supplier_idx, $order_download_file_idx = "")
	{
		$qry = "
			Select Top 1 *
			From DY_ORDER_DOWNLOAD_FILE
			Where supplier_idx = N'$supplier_idx'
		";

		if($order_download_file_idx){
			$qry .= "
				And order_download_file_idx = N'$order_download_file_idx'
			";
		}

		$qry .= "
			Order by order_download_file_idx DESC
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 주문다운로드 이메일 발송 로그 Insert
	 * 삭제 상태로 입력 됨
	 * 메일 발송 후 stock_order_email_is_del = 'N' 업데이트 필요
	 * @param $order_download_file_idx             : 파일 IDX
	 * @param $supplier_idx                     : 공급처 IDX
	 * @param $email_receiver       : 수신 Email
	 * @param $email_title          : 메일 제목
	 * @param $email_msg            : 메일 내용
	 * @param $email_receiver_cc    : 함께 받은 발송자 Email
	 * @return int
	 */
	public function insertOrderDownloadEmailSendLog($order_download_file_idx, $supplier_idx, $email_receiver, $email_title, $email_msg, $email_receiver_cc)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_ORDER_DOWNLOAD_EMAIL
			(
				order_download_file_idx, supplier_idx
				, order_download_email_receiver, order_download_email_title
				, order_download_email_msg, order_download_email_receiver_cc
				, order_download_email_regip, last_member_idx, order_download_email_is_del
			)
			VALUES 
			(
			 N'$order_download_file_idx',
			 N'$supplier_idx',
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
	 * 주문다운로드 이메일 발송 로그 삭제 상태 변경 => 'N'
	 * @param $order_download_email_idx
	 * @return bool|resource
	 */
	public function updateOrderDownloadEmailSendLogIsDel($order_download_email_idx)
	{
		$qry = "
			Update DY_ORDER_DOWNLOAD_EMAIL
			Set order_download_email_is_del = N'N'
			Where order_download_email_idx = N'$order_download_email_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 주문다운로드 다운로드 로그 Insert
	 * 이메일 발송을 통한 발주서 다운로드 시 로그 Insert
	 * @param $supplier_idx
	 * @param $order_download_file_idx
	 * @param $order_download_email_idx
	 * @return string
	 */
	public function insertOrderDownloadDocumentDownLog($supplier_idx, $order_download_file_idx, $order_download_email_idx){
		$modip   = $_SERVER["REMOTE_ADDR"];
		$referer = $_SERVER["HTTP_REFERER"];
		$agent   = $_SERVER["HTTP_USER_AGENT"];

		$returnValue = "";

		$qry = "
			Insert Into DY_ORDER_DOWNLOAD_FILE_DOWN_LOG
			(
			 supplier_idx, order_download_file_idx, order_download_email_idx, HTTP_REFERER, USER_AGENT, order_download_file_down_regip
			) 
			VALUES 
			(
			 N'$supplier_idx',
			 N'$order_download_file_idx',
			 N'$order_download_email_idx',
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

	public function insertGift($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$gift_name                 = "";
		$gift_date_start           = "";
		$gift_date_end             = "";
		$supplier_idx              = "";
		$product_option_idx_list   = "";
		$seller_idx                = "";
		$market_product_no_list    = "";
		$gift_match_pay            = "";
		$gift_match_pay_text       = "";
		$gift_match_product        = "";
		$gift_match_product_cnt_s  = "";
		$gift_match_product_cnt_e  = "";
		$gift_match_order_amount   = "";
		$gift_match_order_amount_s = "";
		$gift_match_order_amount_e = "";
		$gift_delivery_free        = "";
		$gift_memo                 = "";
		$gift_product_full_name    = "";
		$gift_product_idx          = "";
		$gift_product_option_idx   = "";
		$gift_cnt                  = "";
		$gift_is_only              = "";
		$gift_cnt_type             = "";
		$gift_cnt_type_cnt         = "";
		$gift_status               = "";

		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Insert Into DY_ORDER_GIFT
			(
			 gift_name, gift_date_start, gift_date_end
			 , supplier_idx, product_option_idx_list
			 , seller_idx, market_product_no_list
			 , gift_match_pay, gift_match_pay_text, gift_match_product, gift_match_product_cnt_s, gift_match_product_cnt_e
			 , gift_match_order_amount, gift_match_order_amount_s, gift_match_order_amount_e
			 , gift_delivery_free, gift_memo, gift_product_idx, gift_product_option_idx
			 , gift_cnt, gift_is_only, gift_cnt_type, gift_cnt_type_cnt, gift_status
			 , gift_regip, gift_regidx, last_member_idx
			 )
			 VALUES
			(
			 N'$gift_name'
			 , N'$gift_date_start'
			 , N'$gift_date_end'
			 , N'$supplier_idx'
			 , N'$product_option_idx_list'
			 , N'$seller_idx'
			 , N'$market_product_no_list'
			 , N'$gift_match_pay'
			 , N'$gift_match_pay_text'
			 , N'$gift_match_product'
			 , N'$gift_match_product_cnt_s'
			 , N'$gift_match_product_cnt_e'
			 , N'$gift_match_order_amount'
			 , N'$gift_match_order_amount_s'
			 , N'$gift_match_order_amount_e'
			 , N'$gift_delivery_free'
			 , N'$gift_memo'
			 , N'$gift_product_idx'
			 , N'$gift_product_option_idx'
			 , N'$gift_cnt'
			 , N'$gift_is_only'
			 , N'$gift_cnt_type'
			 , N'$gift_cnt_type_cnt'
			 , N'$gift_status'
			 , N'$modip'
			 , N'$last_member_idx'
			 , N'$last_member_idx'
			)
		";

		$inserted_idx = parent::execSqlInsert($qry);

		//관리자 상품 Insert
		if(!$supplier_idx){
			$product_option_idx_list_ary = explode(",", $product_option_idx_list);
			foreach($product_option_idx_list_ary as $prod){
				$prod = trim($prod);
				if(is_numeric($prod)){
					$qry = "
						Insert Into DY_ORDER_GIFT_PRODUCT
						(gift_idx, product_option_idx) VALUES
						(N'$inserted_idx', N'$prod')
					";
					$tmp = parent::execSqlInsert($qry);
				}
			}
		}

		//판매처 상품 Insert
		$market_product_no_list_ary = explode(",", $market_product_no_list);
		foreach ($market_product_no_list_ary as $mk){
			$mk = trim($mk);
			if($mk != ""){
				$qry = "
					Insert Into DY_ORDER_GIFT_MARKET
					(gift_idx, market_product_no) VALUES
					(N'$inserted_idx', N'$mk')
				";
				$tmp = parent::execSqlInsert($qry);
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		return $inserted_idx;
	}

	public function updateGift($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$gift_idx                  = "";
		$gift_name                 = "";
		$gift_date_start           = "";
		$gift_date_end             = "";
		$supplier_idx              = "";
		$product_option_idx_list   = "";
		$seller_idx                = "";
		$market_product_no_list    = "";
		$gift_match_pay            = "";
		$gift_match_pay_text       = "";
		$gift_match_product        = "";
		$gift_match_product_cnt_s  = "";
		$gift_match_product_cnt_e  = "";
		$gift_match_order_amount   = "";
		$gift_match_order_amount_s = "";
		$gift_match_order_amount_e = "";
		$gift_delivery_free        = "";
		$gift_memo                 = "";
		$gift_product_full_name    = "";
		$gift_product_idx          = "";
		$gift_product_option_idx   = "";
		$gift_cnt                  = "";
		$gift_is_only              = "";
		$gift_cnt_type             = "";
		$gift_cnt_type_cnt         = "";
		$gift_status               = "";

		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Update DY_ORDER_GIFT
			Set
				gift_name = N'$gift_name'
			  , gift_date_start = N'$gift_date_start'
			  , gift_date_end = N'$gift_date_end'
			  , supplier_idx = N'$supplier_idx'
			  , product_option_idx_list = N'$product_option_idx_list'
			  , seller_idx = N'$seller_idx'
			  , market_product_no_list = N'$market_product_no_list'
			  , gift_match_pay = N'$gift_match_pay'
			  , gift_match_pay_text = N'$gift_match_pay_text'
			  , gift_match_product = N'$gift_match_product'
			  , gift_match_product_cnt_s = N'$gift_match_product_cnt_s'
			  , gift_match_product_cnt_e = N'$gift_match_product_cnt_e'
			  , gift_match_order_amount = N'$gift_match_order_amount'
			  , gift_match_order_amount_s = N'$gift_match_order_amount_s'
			  , gift_match_order_amount_e = N'$gift_match_order_amount_e'
			  , gift_delivery_free = N'$gift_delivery_free'
			  , gift_memo = N'$gift_memo'
			  , gift_product_idx = N'$gift_product_idx'
			  , gift_product_option_idx = N'$gift_product_option_idx'
			  , gift_cnt = N'$gift_cnt'
			  , gift_is_only = N'$gift_is_only'
			  , gift_cnt_type = N'$gift_cnt_type'
			  , gift_cnt_type_cnt = N'$gift_cnt_type_cnt'
			  , gift_status = N'$gift_status'
			  , gift_moddate = getdate()
			  , gift_modip = N'$modip'
			  , gift_modidx = N'$last_member_idx'
			  , last_member_idx = N'$last_member_idx'
			Where gift_idx = N'$gift_idx'
		";

		$rst = parent::execSqlInsert($qry);

		//기존 등록된 관리자 상품 삭제
		$qry = "Delete From DY_ORDER_GIFT_PRODUCT Where gift_idx = N'$gift_idx'";
		$tmp = parent::execSqlInsert($qry);

		//관리자 상품 Insert
		if(!$supplier_idx){
			$product_option_idx_list_ary = explode(",", $product_option_idx_list);
			foreach($product_option_idx_list_ary as $prod){
				$prod = trim($prod);
				if(is_numeric($prod)){
					$qry = "
						Insert Into DY_ORDER_GIFT_PRODUCT
						(gift_idx, product_option_idx) VALUES
						(N'$gift_idx', N'$prod')
					";
					$tmp = parent::execSqlInsert($qry);
				}
			}
		}

		//기존 등록된 판매처 상품 삭제
		$qry = "Delete From DY_ORDER_GIFT_MARKET Where gift_idx = N'$gift_idx'";
		$tmp = parent::execSqlInsert($qry);

		//판매처 상품 Insert
		$market_product_no_list_ary = explode(",", $market_product_no_list);
		foreach ($market_product_no_list_ary as $mk){
			$mk = trim($mk);
			if($mk != ""){
				$qry = "
					Insert Into DY_ORDER_GIFT_MARKET
					(gift_idx, market_product_no) VALUES
					(N'$gift_idx', N'$mk')
				";
				$tmp = parent::execSqlInsert($qry);
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		//parent::sqlTransactionRollback();     //트랜잭션 롤백
		parent::db_close();

		return $rst;
	}

	public function getGift($gift_idx){
		$qry = "
			Select 
			       G.*
					, P.product_name, PO.product_option_name
			From DY_ORDER_GIFT G
			Left Outer Join DY_PRODUCT P On P.product_idx = G.gift_product_idx
			Left Outer Join DY_PRODUCT_OPTION PO On PO.product_option_idx = G.gift_product_option_idx
			Where gift_is_del = N'N' And gift_idx = N'$gift_idx'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}


	/**
	 * 최근 및 현재 발주 현황 합산 구하기
	 * @param $seller_idx
	 * @return array
	 */
	public function getOrderUploadSum($seller_idx)
	{
		global $GL_Member;

		$returnValue                              = array();
		$returnValue["sum_last_order_count"]      = 0;
		$returnValue["sum_last_new_order_count"]  = 0;
		$returnValue["sum_available_order_count"] = 0;

		$addQry = "";
		if($seller_idx){
			$addQry = " And seller_idx = N'$seller_idx'";
		}

		$qry = "
			WITH CTE_COLLECT as (
				Select
					seller_idx, 
					collect_sdate, 
					collect_count, 
					collect_order_count, 
					ROW_NUMBER() OVER(PARTITION BY seller_idx Order by order_collect_idx DESC) as rn
				From DY_ORDER_COLLECT
				Where 
					order_collect_is_del = N'N' 
					And collect_state = N'S' 
					And seller_idx <> 0
					$addQry
			)
			, CTE_ORDER as (
				Select 
					seller_idx
					, count(seller_idx) as order_cnt
				From DY_ORDER O 
				Where order_is_del = N'N'
					And order_progress_step in (N'ORDER_COLLECT', N'ORDER_PRODUCT_MATCHING', N'ORDER_PACKING')
					$addQry
				Group by seller_idx
			)
			
		";

		$qry .= "
			Select 
				Sum(collect_count) as sum_last_order_count , 
				Sum(collect_order_count) as sum_last_new_order_count, 
				(Select Sum(order_cnt) From CTE_ORDER) as sum_available_order_count
			From CTE_COLLECT Where rn = 1
		";

		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();

		$returnValue["sum_last_order_count"]      = $_row["sum_last_order_count"];
		$returnValue["sum_last_new_order_count"]  = $_row["sum_last_new_order_count"];
		$returnValue["sum_available_order_count"] = $_row["sum_available_order_count"];

		return $returnValue;
	}
}
?>