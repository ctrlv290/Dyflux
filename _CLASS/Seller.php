<?php
/**
 * 판매처 관련 Class
 * User: woox
 * Date: 2018-11-10
 * 사용자, 자사 판매처, 벤더사 판매처(벤더사), 공급처 의 코드(IDX)는 unique
 * 사용자 IDX 범위 : 10000 ~ 19999
 * 자사 판매처 IDX 범위 : 90000 ~ 99999
 * 벤더사 판매처 IDX 범위 : 20000 ~ 39999
 * 공급처 IDX 범위 : 40000 ~ 59999
 * 권한그룹 IDX 범위 : 110000 ~ 119999
 *      기본 권한그룹 (수정/삭제 불가) : 벤더사와 공급처 기본 권한 부여를 위해 사용
 *      벤더사 권한그룹 IDX : 120000 (고정)
 *      공급처 권한그룹 IDX : 140000 (고정)
 */
class Seller extends Dbconn
{
	/**
	 * 판매처코드 산출 (가장 큰 판매처 IDX 값을 반환한다)
	 * seller_type = 'MARKET_SELLER'
	 * 판매처 타입이 자사 판매처(마켓) 인 것만
	 * out : int
	 */
	public function getMaxIdx()
	{
		$qry = "Select Max(seller_idx) From DY_SELLER Where seller_type IN('MARKET_SELLER', 'CUSTOM_SELLER')  ";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 판매처 Insert
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertSeller($args)
	{
		global $GL_Member;
		extract($args);

		$maxIDX = $this->getMaxIdx();
		if(!$maxIDX) $maxIDX = 70000;
		$maxIDX = $maxIDX + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Insert Into DY_SELLER
			(seller_idx, seller_type, market_code, 
			seller_name, manage_group_idx, market_login_id, 
			market_login_pw, market_auth_code, market_auth_code2, 
			market_admin_url, market_mall_url, market_product_url, 
			seller_auto_order, seller_invoice_product, seller_invoice_option, seller_use_api, 
			seller_is_use, seller_regip, last_member_idx)
			VALUES 
			(
				N'$maxIDX',
				N'$market_type',
				N'$market_code',
				N'$seller_name',
				N'$manage_group_idx',
				N'$market_login_id',
				N'$market_login_pw',
				N'$market_auth_code',
				N'$market_auth_code2',
				N'$market_admin_url',
				N'$market_mall_url',
				N'$market_product_url',
				N'$seller_auto_order',
				N'$seller_invoice_product',
				N'$seller_invoice_option',
				N'".$seller_use_api."',
				N'".$seller_is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."',
				N'".$GL_Member["member_idx"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋

		// 기본판매처(마켓) 경우 정해진 발주서 포멧으로 셋팅
		// DY_ORDER_FORMAT_SELLER 에 DY_CODE.idx 를 seller_idx 로 최초 Insert 가 필요함!!
		if($market_type == "MARKET_SELLER") {
			$qry = "
				INSERT INTO DY_ORDER_FORMAT_SELLER 
				           ([seller_idx]
				           ,[order_format_default_idx]
				           ,[order_format_seller_header_name]
				           ,[order_format_seller_regip]
				           ,[order_format_seller_is_use]
				           ,[order_format_seller_is_del]
				           ,[last_member_idx])
				SELECT '$maxIDX'
				           ,[order_format_default_idx]
				           ,[order_format_seller_header_name]
				           ,N'".$_SERVER["REMOTE_ADDR"]."'
				           ,N'Y'
				           ,N'N'
				           ,N'".$GL_Member["member_idx"]."'
				FROM DY_ORDER_FORMAT_SELLER
				WHERE seller_idx = (SELECT idx FROM DY_CODE WHERE parent_code = N'$market_type' AND code = N'$market_code')		
			";
			parent::execSqlInsert($qry);
		}

		//커밋 후 히스토리 테이블 MEMBER_IDX Update
		$qry = "
			Update 
				
		";

		parent::db_close();
		return $maxIDX;
	}

	/**
	 * 판매처 Update
	 * 일괄 수정 에서도 사용되기 때문에
	 * 필수 필드를 제외한 나머지 필드들은 값이 없으면 업데이트 하지 않음
	 * $args
	 * out : boolean
	 */
	public function updateSeller($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		$qry = "
			Update DY_SELLER
				Set 
				seller_type = N'".$market_type."',
				market_code = N'".$market_code."',
				seller_name = N'".$seller_name."',
				market_login_id = N'".$market_login_id."',
				market_login_pw = N'".$market_login_pw."',
				market_auth_code = N'".$market_auth_code."',
				market_auth_code2 = N'".$market_auth_code2."',
				market_admin_url = N'".$market_admin_url."',
				market_mall_url = N'".$market_mall_url."',
				market_product_url = N'".$market_product_url."',
		";

		if($manage_group_idx != null) {
			$qry .= "
				manage_group_idx = N'" . $manage_group_idx . "',
			";
		}

		if($seller_auto_order != null) {
			$qry .= "
				seller_auto_order = N'" . $seller_auto_order . "',
			";
		}
		if($seller_invoice_product != null) {
			$qry .= "
				seller_invoice_product = N'" . $seller_invoice_product . "',
			";
		}
		if($seller_invoice_option != null) {
			$qry .= "
				seller_invoice_option = N'" . $seller_invoice_option . "',
			";
		}
		if($seller_use_api != null) {
			$qry .= "
				seller_use_api = N'" . $seller_use_api . "',
			";
		}
		if($seller_is_use != null) {
			$qry .= "
				seller_is_use = N'" . $seller_is_use . "',
			";
		}

		$qry .= "
				seller_moddate = getdate(), 
				seller_modip = N'".$_SERVER["REMOTE_ADDR"]."',
				last_member_idx = N'".$GL_Member["member_idx"]."'
			Where seller_idx = '".$seller_idx."'
		";

		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매처 market_auth_code Update
	 * Cafe24 API 등 API Key 등이 계속 변동 되어야 할 경우 사용
	 * @param $args
	 * @return bool|resource
	 */
	public function updateSeller_AuthCodes($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		$qry = " Update DY_SELLER Set ";
		if($market_auth_code != null) {
			$qry .= " market_auth_code = N'" . $market_auth_code . "', ";
		}
		if($market_auth_code2 != null) {
			$qry .= " market_auth_code2 = N'" . $market_auth_code2 . "', ";
		}
		if($market_auth_code3 != null) {
			$qry .= " market_auth_code3 = N'" . $market_auth_code3 . "', ";
		}
		if($market_auth_code4 != null) {
			$qry .= " market_auth_code4 = N'" . $market_auth_code4 . "', ";
		}
		if($market_auth_code5 != null) {
			$qry .= " market_auth_code5 = N'" . $market_auth_code5 . "', ";
		}
		if($market_auth_code6 != null) {
			$qry .= " market_auth_code6 = N'" . $market_auth_code6 . "', ";
		}
		if($market_auth_code7 != null) {
			$qry .= " market_auth_code7 = N'" . $market_auth_code7 . "', ";
		}
		$qry .= "
				seller_moddate = getdate(), 
				seller_modip = N'".$_SERVER["REMOTE_ADDR"]."',
				last_member_idx = N'".$GL_Member["member_idx"]."'
			Where seller_idx = '".$seller_idx."'
		";

		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매처 정보 반환
	 * seller_type = 'MARKET_SELLER'
	 * 판매처 타입이 자사 판매처(마켓) 인 것만
	 * $idx : 판매처 IDX
	 * out : Array (ONE ROW)
	 */
	public function getSellerData($idx){
		$qry = "
			Select * 
			From  DY_SELLER 
			Where seller_idx = N'".$idx."' 
				And 
				(seller_type = 'MARKET_SELLER' Or seller_type = 'CUSTOM_SELLER')
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매처 정보 반환2
	 * $idx : 판매처 IDX
	 * out : Array (ONE ROW)
	 */
	public function getAllSellerData($idx){
		$qry = "
			Select * 
			From  DY_SELLER 
			Where seller_idx = N'".$idx."' 
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매 마켓 목록 반환
	 * $market_type : 판매처 타입 (MARKET_SELLER : 공통코드 참조, CUSTOM_SELLER : 사용자정의)
	 * out : Array
	 */
	public function getMarketList($market_type)
	{
		if($market_type == "MARKET_SELLER"){
			//기본판매처
			$qry = "
				Select code as 'SEL_VALUE', code_name as 'SEL_TEXT'
				From DY_CODE
				Where is_del = N'N' And is_use = N'Y'
					And code_idx in (
						Select idx From DY_CODE Where code = N'MARKET_SELLER'
					)
				Order by code_name ASC
			";
		}elseif($market_type == "CUSTOM_SELLER") {
			//사용자정의판매처
			$qry = "
				Select code as 'SEL_VALUE', code_name as 'SEL_TEXT'
				From DY_CODE
				Where is_del = N'N' And is_use = N'Y'
					And code_idx in (
						Select idx From DY_CODE Where code = N'CUSTOM_SELLER'
					)
				Order by code_name ASC
			";
		}
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 벤더사 판매처 Insert
	 * 벤더사 승인 시 입력
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertVendorSeller($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작
		$qry = "
			Insert Into DY_SELLER
			(seller_idx, seller_type, market_code, 
			seller_name, manage_group_idx, market_login_id, 
			market_login_pw, market_auth_code, market_auth_code2, 
			market_admin_url, market_mall_url, market_product_url, 
			seller_auto_order, seller_invoice_product, seller_invoice_option, 
			seller_is_use, seller_regip, last_member_idx)
			VALUES 
			(
				N'$vendor_idx',
				N'VENDOR_SELLER',
				0,
				N'$vendor_name',
				0,
				N'',
				N'',
				N'',
				N'',
				N'',
				N'',
				N'',
				N'',
				N'',
				N'',
				N'".$vendor_is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."',
				N'".$GL_MEMBER["member_idx"]."'
			);
		";
		$rst = parent::execSqlInsert($qry);
		parent::sqlTransactionCommit();     //트랜잭션 커밋

		parent::db_close();
		return $vendor_idx;
	}

	/**
	 * 벤더사 판매처 Update
	 * $args
	 * out : boolean
	 */
	public function updateVendorSeller($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		$qry = "
			Update DY_SELLER
				Set 
				seller_name = N'',
				seller_is_use = N'".$vendor_is_use."',
				seller_moddate = getdate(), 
				seller_modip = N'".$_SERVER["REMOTE_ADDR"]."',
				last_member_idx = N'".$GL_Member["member_idx"]."'
			Where seller_idx = '".$vendor_idx."'
		";

		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매처 코드(IDX) 의 유효성 반환
	 * 존재하지 않는 판매처 또는 벤더사 판매처 또는 삭제된 판매처 일 경우 false
	 * $seller_idx : 판매처 테이블 IDX
	 * out : boolean (존재하지 않는 판매처 또는 벤더사 판매처 또는 삭제된 판매처 일 경우 false)
	 */
	public function isValidSeller($seller_idx)
	{
		$qry = "
			Select count(*) From DY_SELLER
			WHERE 
				seller_is_del = N'N'
				And seller_type = N'MARKET_SELLER'
				And seller_idx = N'".$seller_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return ($rst == 1) ? true : false;
	}

	/**
	 * 판매처 정보 반환
	 * 사용가능한 판매처만 반환
	 * @param $seller_idx : 판매처 IDX
	 * @return array|false|null
	 */
	public function getUseSellerAllData($seller_idx)
	{
		$qry = "
			Select * 
				From DY_SELLER
				Where seller_is_use = N'Y' And seller_is_del = N'N'
						And seller_idx = N'$seller_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 판매처 정보 반환 - 판매처명으로 검색
	 * 사용가능한 판매처만 반환
	 * @param $seller_name : 판매처 IDX
	 * @return array|false|null
	 */
	public function getUseSellerAllDataByName($seller_name)
	{
		$qry = "
			Select * 
				From DY_SELLER
				Where seller_is_use = N'Y' And seller_is_del = N'N'
						And seller_name = N'$seller_name'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 판매 목록 반환
	 * out : Array
	 */
	public function getSellerList()
	{
		$qry = "
			Select seller_idx, seller_name
			From DY_SELLER
			Where seller_is_del = N'N' And seller_is_use = N'Y'
			Order by seller_name asc
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매 목록 반환 2 (보다 많은 정보를 반환)
	 * out : Array
	 */
	public function getSellerListDetail()
	{
		$qry = "
			Select *
			From DY_SELLER
			Where seller_is_del = N'N'
			Order by seller_name asc
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	public function getSellerInvoiceFormat($seller_idx)
	{
		$qry = "
			Select * From DY_SELLER_INVOICE_FORMAT
			Where seller_idx = N'$seller_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	public function saveSellerInvoiceFormat($seller_idx, $header_print, $margin_top, $column_setting_array)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Select f_idx From DY_SELLER_INVOICE_FORMAT Where seller_idx = N'$seller_idx'
		";
		parent::db_connect();
		$f_idx = parent::execSqlOneCol($qry);
		parent::db_close();

		if(!$f_idx){
			$qry = "
				Insert Into DY_SELLER_INVOICE_FORMAT
				(seller_idx, header_print, margin_top, f_regip, f_regidx, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, AA, AB, AC, AD, AE, AF, AG, AH, AI, AJ, AK, AL, AM, AN, AO, AP, AQ, AR, [AS], AT, AU, AV, AW, AX, AY, AZ)
				VALUES
				(
				 N'$seller_idx'
				 , N'$header_print'
				 , N'$margin_top'
				 , N'$modip'
				 , N'$last_member_idx'
			";
			foreach($column_setting_array as $key => $val) {
				$qry .= " , N'".$val."'";
			}
			$qry .= ")";

			parent::db_connect();
			$inserted_idx = parent::execSqlInsert($qry);
			parent::db_close();
		}else{

			$qry = "
				Update DY_SELLER_INVOICE_FORMAT
				Set
				header_print = N'$header_print'
				, margin_top = N'$margin_top'
				, f_moddate = getdate()
				, f_modip = N'$modip'
				, f_modidx = N'$last_member_idx'
			";
			foreach($column_setting_array as $key => $val) {
				$qry .= " , [".$key."] = N'".$val."'";
			}
			$qry .= " Where f_idx = N'$f_idx'";


			parent::db_connect();
			$inserted_idx = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return $inserted_idx;

	}
}
?>