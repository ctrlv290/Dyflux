<?php
/**
 * 벤더사 관련 Class
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

class Vendor extends Dbconn
{
	/*
	 * 벤더사 Insert
	 * 대상 테이블 DY_MEMBER, DY_MEMBER_VENDOR, DY_SELLER
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertVendor($args)
	{
		global $GL_Member;
		extract($args);

		$maxIDX = $this->getMaxVendorIdx();
		if(!$maxIDX) $maxIDX = 20000;
		$maxIDX = $maxIDX + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Insert Into DY_MEMBER
			(idx, member_id, member_pw, member_type, is_use, regip)
			VALUES 
			(
				N'$maxIDX',
				N'$login_id',
				N'$login_pw',
				N'VENDOR',
				N'".$is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		if($maxIDX)
		{
			$qry = "
				Insert Into DY_MEMBER_VENDOR
				(member_idx, manage_group_idx, vendor_name, 
				vendor_grade, vendor_ceo_name, vendor_license_number, 
				vendor_zipcode, vendor_addr1, vendor_addr2, 
				vendor_fax, vendor_startdate, vendor_enddate, 
				vendor_license_file, vendor_bank_account_number, vendor_bank_name, 
				vendor_bank_book_copy_file, vendor_bank_holder_name, 
				vendor_email_default, vendor_email_account, vendor_email_order, vendor_use_charge,
				vendor_officer1_name, vendor_officer1_tel, vendor_officer1_mobile, vendor_officer1_email, 
				vendor_officer2_name, vendor_officer2_tel, vendor_officer2_mobile, vendor_officer2_email, 
				vendor_officer3_name, vendor_officer3_tel, vendor_officer3_mobile, vendor_officer3_email, 
				vendor_officer4_name, vendor_officer4_tel, vendor_officer4_mobile, vendor_officer4_email, 
				vendor_md, vendor_etc, vendor_status, vendor_regip, last_member_idx)
				VALUES 
				(
					'".$maxIDX."',
					N'".$manage_group_idx."',
					N'".$vendor_name."',
					N'".$vendor_grade."',
					N'".$vendor_ceo_name."',
					N'".$vendor_license_number."',
					N'".$vendor_zipcode."',
					N'".$vendor_addr1."',
					N'".$vendor_addr2."',
					N'".$vendor_fax."',
					N'".$vendor_startdate."',
					N'".$vendor_enddate."',
					N'".$vendor_license_file."',
					N'".$vendor_bank_account_number."',
					N'".$vendor_bank_name."',
					N'".$vendor_bank_book_copy_file."',
					N'".$vendor_bank_holder_name."',
					N'".$vendor_email_default."',
					N'".$vendor_email_account."',
					N'".$vendor_email_order."',
					N'".$vendor_use_charge."',
					N'".$vendor_officer1_name."',
					N'".$vendor_officer1_tel."',
					N'".$vendor_officer1_mobile."',
					N'".$vendor_officer1_email."',
					N'".$vendor_officer2_name."',
					N'".$vendor_officer2_tel."',
					N'".$vendor_officer2_mobile."',
					N'".$vendor_officer2_email."',
					N'".$vendor_officer3_name."',
					N'".$vendor_officer3_tel."',
					N'".$vendor_officer3_mobile."',
					N'".$vendor_officer3_email."',
					N'".$vendor_officer4_name."',
					N'".$vendor_officer4_tel."',
					N'".$vendor_officer4_mobile."',
					N'".$vendor_officer4_email."',
					N'".$vendor_md."',
					N'".$vendor_etc."',
					N'VENDOR_PENDDING',
					N'".$_SERVER["REMOTE_ADDR"]."',
					N'".$GL_Member["member_idx"]."'
				)
			";

			$rst2 = parent::execSqlInsert($qry);

			parent::sqlTransactionCommit();     //트랜잭션 커밋
		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();
		return $maxIDX;
	}

	/*
	 * 가장 큰 벤더사 일련번호 반환
	 * 벤더사 IDX 범위 : 20000 ~ 39999
	 * out : int
	 */
	public function getMaxVendorIdx()
	{
		$qry = "Select Max(idx) From DY_MEMBER Where idx > 20000 And idx < 40000  ";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}


	/*
	 * 벤더사 정보 리턴
	 * member_type = 'VENDOR' 만 조회
	 * $vendor_idx : DY_MEMBER 테이블 IDX
	 * out : int
	 */
	public function getVendorData($vendor_idx)
	{
		$qry = "
			Select M.member_id, M.is_use, V.*
			From DY_MEMBER M Left Outer Join DY_MEMBER_VENDOR V On M.idx = V.member_idx
			Where M.is_del = N'N' And M.member_type = 'VENDOR' And M.idx = N'".$vendor_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 벤더사 정보 Update
	 * $args
	 * out : boolean
	 */
	public function updateVendor($args)
	{
		global $GL_Member;
		extract($args);

		//기존 벤더사 가져오기
		$vendorInfo = $this->getVendorData($idx);


		if($vendorInfo) {
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작


			$qry = "
				Update DY_MEMBER
				Set moddate = getdate()
				, modip = N'" . $_SERVER["REMOTE_ADDR"] . "'
				, last_member_idx = N'".$GL_Member["member_idx"]."'
			";
			if ($login_pw) {
				$qry .= "
					, member_pw = N'" . $login_pw . "'
				";
			}
			if ($is_use) {
				$qry .= "
					, is_use = N'" . $is_use . "'
				";
			}
			$qry .= "
				Where idx = '" . $idx . "'
			";
			$rst = parent::execSqlUpdate($qry);



			$qry = "
				Update DY_MEMBER_VENDOR
					Set 
					vendor_moddate = getdate(), 
					vendor_modip = N'" . $_SERVER["REMOTE_ADDR"] . "', 
					last_member_idx = N'".$GL_Member["member_idx"]."',
			";

			if($vendor_name) {
				$qry .= "
					vendor_name =                N'" . $vendor_name . "',
				";
			}

			if($manage_group_idx != null) {
				$qry .= "
					manage_group_idx =           N'" . $manage_group_idx . "',
				";
			}
			if($vendor_grade) {
				$qry .= "
					vendor_grade =               N'" . $vendor_grade . "',
				";
			}
			$qry .= "
					vendor_ceo_name =            N'" . $vendor_ceo_name . "',
					vendor_license_number =      N'" . $vendor_license_number . "',
					vendor_zipcode =             N'" . $vendor_zipcode . "',
					vendor_addr1 =               N'" . $vendor_addr1 . "',
					vendor_addr2 =               N'" . $vendor_addr2 . "',
			";
			if($manage_group_idx != null) {
				$qry .= "
					vendor_fax =                 N'" . $vendor_fax . "',
				";
			}
			if($vendor_startdate != null) {
				$qry .= "
					vendor_startdate =           N'" . $vendor_startdate . "',
				";
			}
			if($vendor_enddate != null) {
				$qry .= "
					vendor_enddate =             N'" . $vendor_enddate . "',
				";
			}
			if($vendor_license_file != null) {
				$qry .= "
					vendor_license_file =        N'" . $vendor_license_file . "',
				";
			}

			$qry .= "
					vendor_bank_account_number = N'" . $vendor_bank_account_number . "',
					vendor_bank_name =           N'" . $vendor_bank_name . "',
					vendor_bank_holder_name =    N'" . $vendor_bank_holder_name . "',
			";

			if($vendor_bank_book_copy_file != null) {
				$qry .= "
					vendor_bank_book_copy_file = N'" . $vendor_bank_book_copy_file . "',
				";
			}

			if($vendor_email_default != null) {
				$qry .= "
					vendor_email_default =       N'" . $vendor_email_default . "',
				";
			}

			if($vendor_email_account != null) {
				$qry .= "
					vendor_email_account =       N'" . $vendor_email_account . "',
				";
			}

			if($vendor_email_order != null) {
				$qry .= "
					vendor_email_order =         N'" . $vendor_email_order . "',
				";
			}

			if ($vendor_use_charge) {
				$qry .= "
					vendor_use_charge = N'" . $vendor_use_charge . "', 
				";
			}

			$qry .= "
					vendor_officer1_name =       N'" . $vendor_officer1_name . "',
					vendor_officer1_tel =        N'" . $vendor_officer1_tel . "',
					vendor_officer1_mobile =     N'" . $vendor_officer1_mobile . "',
					vendor_officer1_email =      N'" . $vendor_officer1_email . "',
			";

			if($vendor_officer2_name != null) {
				$qry .= "
					vendor_officer2_name =       N'" . $vendor_officer2_name . "',
					vendor_officer2_tel =        N'" . $vendor_officer2_tel . "',
					vendor_officer2_mobile =     N'" . $vendor_officer2_mobile . "',
					vendor_officer2_email =      N'" . $vendor_officer2_email . "',
				";
			}
			if($vendor_officer3_name != null) {
				$qry .= "
					vendor_officer3_name =       N'" . $vendor_officer3_name . "',
					vendor_officer3_tel =        N'" . $vendor_officer3_tel . "',
					vendor_officer3_mobile =     N'" . $vendor_officer3_mobile . "',
					vendor_officer3_email =      N'" . $vendor_officer3_email . "',
				";
			}
			if($vendor_officer4_name != null) {
				$qry .= "
					vendor_officer4_name =       N'" . $vendor_officer4_name . "',
					vendor_officer4_tel =        N'" . $vendor_officer4_tel . "',
					vendor_officer4_mobile =     N'" . $vendor_officer4_mobile . "',
					vendor_officer4_email =      N'" . $vendor_officer4_email . "',
				";
			}
			$qry .= "
					vendor_md =                  N'" . $vendor_md . "',
					vendor_etc =                 N'" . $vendor_etc . "'
				Where member_idx = '" . $idx . "'
			";

			$rst2 = parent::execSqlUpdate($qry);


			//현재 벤더사 상태가 승인 일 경우
			//벤더사 명, 등급, 사용 여부 등을 판매처 테이블(DY_SEELER)에도 Update

			//현재 벤더사 승인 상태 (공통코드 참조 : VENDOR_STATUS)
			//VENDOR_APPLY
			$_current_vendor_status = $vendorInfo["vendor_status"];
			if($_current_vendor_status == "VENDOR_APPLY")
			{
				$qry = "
					Update DY_SELLER
					SET
						seller_moddate   = getdate(), 
						seller_modip     = N'" . $_SERVER["REMOTE_ADDR"] . "',
						last_member_idx = N'".$GL_Member["member_idx"]."'
				";

				if($is_use) {
					$qry .= "
						, seller_is_use =     N'" . $is_use . "'
					";
				}
				if($vendor_name) {
					$qry .= "
						, seller_name =     N'" . $vendor_name . "'
					";
				}
				if($vendor_grade) {
					$qry .= "
						, vendor_grade =     N'" . $vendor_grade . "'
					";
				}
				if($manage_group_idx != null) {
					$qry .= "
						, manage_group_idx =     N'" . $manage_group_idx . "'
					";
				}

				if ($vendor_use_charge) {
					$qry .= "
						, vendor_use_charge = N'" . $vendor_use_charge . "'
					";
				}
				$qry .="
					Where seller_idx = '" . $idx . "'
				";

				$rst3 = parent::execSqlUpdate($qry);
			}

			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}


		return $rst;
	}

	/*
	 * 벤더사 승인 여부 변경 프로세스
	 * 승인 시 판매처 테이블(DY_SELLER) 에 벤더사 판매처 입력(Insert)
	 * $args
	 * out : boolean
	 */
	public function changeVendorStatus($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];
		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작
		//승인 일 경우 판매처 테이블(DY_SELLER) 에 벤더사 판매처 입력(Insert)
		if($vendor_status == "VENDOR_APPLY")
		{
			//기존 벤더사 이름 , 등급, 그룹 가져오기
			$qry = "Select vendor_name, vendor_grade, manage_group_idx, vendor_use_charge From DY_MEMBER_VENDOR Where member_idx = N'".$vendor_idx."'";
			$vendorInfo = parent::execSqlOneRow($qry);

			if($vendorInfo)
			$qry = "
				Insert Into DY_SELLER
				(seller_idx, seller_type, market_code, seller_name, vendor_grade, vendor_use_charge, manage_group_idx, seller_regip, last_member_idx)
				VALUES 
				(
					N'".$vendor_idx."',
					N'VENDOR_SELLER',
					N'',
					N'".$vendorInfo["vendor_name"]."',
					N'".$vendorInfo["vendor_grade"]."',
					N'".$vendorInfo["vendor_use_charge"]."',
					N'".$vendorInfo["manage_group_idx"]."',
					N'".$_SERVER["REMOTE_ADDR"]."',
					N'".$GL_Member["member_idx"]."'
				)
			";

			$tmp_idx = parent::execSqlInsert($qry);

			//기본 발주서포맷 입력
			$qry = "
				Insert Into DY_ORDER_FORMAT_SELLER
				(
				  seller_idx, order_format_default_idx, order_format_seller_header_name
				  , order_format_seller_regdate, order_format_seller_regip
				  , order_format_seller_moddate, order_format_seller_modip
				  , last_member_idx
			    )  
				SELECT 
					N'$vendor_idx', order_format_default_idx, order_format_seller_header_name
					, getdate(), N'$modip'
					, getdate(), N'$modip'
					, N'$last_member_idx' 
				From DY_ORDER_FORMAT_SELLER Where seller_idx = 0 Order by order_format_default_idx ASC
			";
			$tmp = parent::execSqlUpdate($qry);
		}

		//승인/반려 Update
		$qry = "
			Update DY_MEMBER_VENDOR
			Set
				vendor_status = N'".$vendor_status."',
				vendor_status_msg = N'".$vendor_status_msg."',
				last_member_idx = N'".$GL_Member["member_idx"]."'
			Where
				member_idx = N'".$vendor_idx."'
		";
		$rst = parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();
	}

	/*
	 * 벤더사 코드(IDX) 의 유효성 반환
	 * 존재하지 않는 벤더사 또는 삭제된 벤더사 일 경우 false
	 * $vendor_idx : 벤더사 테이블 IDX
	 * out : boolean (존재하지 않는 벤더사 또는 삭제된 벤더사 일 경우 false)
	 */
	public function isValidVendor($vendor_idx)
	{
		$qry = "
			Select count(*) 
			From DY_MEMBER M Left Outer Join DY_MEMBER_VENDOR V
				On M.idx = V.member_idx
			WHERE 
				M.is_del = N'N'
				And M.member_type = 'VENDOR'
				And M.idx = N'".$vendor_idx."'
				And isnull(V.member_idx, 0) != 0
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return ($rst == 1) ? true : false;
	}

	/*
	 * 벤더사 리스트 반환
	 * 승인된 벤더사 만
	 * is_use = 'Y"
	 * out : int
	 */
	public function getVendorUseAbleList()
	{
		$qry = "
			Select M.member_id, M.idx, V.vendor_name
			From DY_MEMBER M Left Outer Join DY_MEMBER_VENDOR V On M.idx = V.member_idx
			Where M.is_del = N'N' And M.member_type = 'VENDOR' And V.vendor_status = N'VENDOR_APPLY' And M.is_use = N'Y'
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 벤더사 충전금 입력
	 * @param $member_idx
	 * @param $charge_date
	 * @param $charge_amount
	 * @param $charge_memo
	 * @return int|boolean
	 */
	public function insertVendorCharge($member_idx, $charge_date, $charge_amount, $charge_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$_view = $this->getVendorData($member_idx);

		if($_view) {

			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작

			$vendor_grade = $_view["vendor_grade"];
			$qry = "
				Insert Into DY_MEMBER_VENDOR_CHARGE
				(charge_date, member_idx, charge_inout, charge_amount, vendor_grade, charge_memo, charge_regip, charge_regidx)
				VALUES
				(
				 N'$charge_date'
				 , N'$member_idx'
				 , 1
				 , N'$charge_amount'
				 , N'$vendor_grade'
				 , N'$charge_memo'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";

			$inserted_idx = parent::execSqlInsert($qry);

			//자금일보에 입력
			//선급금 : 91
			//선수금 : 93 으로 변경
			//$tran_type = BANK_CUSTOMER_IN
			$account_idx = 93;
			$tran_type = "BANK_CUSTOMER_IN";
			$tran_inout = "IN";
			$target_idx = $member_idx;
			$tran_memo = $charge_memo;
			$tran_amount = $charge_amount;

			$qry = "
				Insert Into DY_SETTLE_REPORT
				(
				tran_date, tran_type, account_idx, tran_inout, target_idx, tran_memo, tran_amount
				, tran_user, tran_card_no, tran_purpose, tran_fixed, charge_idx
				, tran_regip, tran_regidx)
				VALUES
				(
				   N'$charge_date'
				 , N'$tran_type'
				 , N'$account_idx'
				 , N'$tran_inout'
				 , N'$target_idx'
				 , N'$tran_memo'
				 , N'$tran_amount'
				 , N''
				 , N''
				 , N''
				 , N'Y'
				 , N'$inserted_idx'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";

			$inserted_idx2 = parent::execSqlInsert($qry);

			//매출거래처별원장에 입력

			$ledger_type = "LEDGER_SALE";
			$ledger_add_type = "TRAN";
			$ledger_date = $charge_date;
			$ledger_title = "충전금";
			$ledger_adjust_amount = 0;
			$ledger_tran_amount = $charge_amount;
			$ledger_refund_amount = 0;
			$ledger_memo = $charge_memo;

			$qry = "
				Insert Into DY_LEDGER
				(
				 target_idx, ledger_type, ledger_add_type, ledger_date, ledger_title
				 , ledger_adjust_amount, ledger_tran_amount, ledger_refund_amount, ledger_memo
				 , charge_idx
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
				 , N'$inserted_idx'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";
			$inserted_idx3 = parent::execSqlInsert($qry);


			parent::sqlTransactionCommit();     //트랜잭션 커밋

		parent::db_close();

		}else{
			$inserted_idx = false;
		}

		return $inserted_idx;
	}

	public function getVendorRemainChargeAmount($vendor_idx)
	{
		//isNull(Sum(charge_amount * charge_inout), 0) as charge_sum
		$qry = "
			Select
			Sum(charge_amount * charge_inout) 
			  - (Select isNull(Sum(settle_sale_sum), 0) From DY_SETTLE S Where S.seller_idx = N'$vendor_idx' And S.settle_is_del = N'N') 
			  + (Select isNull(Sum(ledger_tran_amount), 0) From DY_LEDGER L Where L.target_idx = N'$vendor_idx' And L.ledger_is_del = N'N' And L.charge_idx = 0)
			  as charge_sum
			From DY_MEMBER_VENDOR_CHARGE V
			Where charge_is_del = N'N'
			  And charge_inout = 1
			  And member_idx = N'$vendor_idx' 
		";

		parent::db_connect();
		$sum = parent::execSqlOneCol($qry);
		parent::db_close();
//
//		$qry = "
//			Select
//			isNull(Sum(Case When settle_type = 'SHIPPED' Or settle_type = 'CANCEL' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as closing_settle_amount
//			, isNull(Sum(Case When settle_type = 'ADJUST_SALE' Or settle_type = 'ADJUST_PURCHASE' Then settle_sale_supply - settle_sale_commission_in_vat + settle_delivery_in_vat - settle_delivery_commission_in_vat Else 0 End), 0) as adjust_settle_amount
//			From DY_SETTLE
//			Where settle_is_del = N'N'
//		";

		return $sum;
	}
}
?>