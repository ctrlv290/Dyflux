<?php
/**
 * 공급사 관련 Class
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

class Supplier extends DBConn
{
	/**
	 * 공급처 Insert
	 * 대상 테이블 DY_MEMBER, DY_MEMBER_SUPPLIER
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertSupplier($args)
	{
		global $GL_Member;
		extract($args);

		$maxIDX = $this->getMaxSupplierIdx();
		if(!$maxIDX) $maxIDX = 40000;
		$maxIDX = $maxIDX + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$supplier_use_prepay = "N";

		$qry = "
			Insert Into DY_MEMBER
			(idx, member_id, member_pw, member_type, is_use, regip)
			VALUES 
			(
				N'$maxIDX',
				N'$login_id',
				N'$login_pw',
				N'SUPPLIER',
				N'".$is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		if($maxIDX)
		{
			$qry = "
				Insert Into DY_MEMBER_SUPPLIER
				(member_idx, manage_group_idx, supplier_name, 
				supplier_ceo_name, supplier_license_number, 
				supplier_zipcode, supplier_addr1, supplier_addr2, 
				supplier_fax, supplier_startdate, supplier_enddate, 
				supplier_license_file, supplier_bank_account_number, supplier_bank_name, 
				supplier_bank_book_copy_file, supplier_bank_holder_name, 
				supplier_email_default, supplier_email_account, supplier_email_order, supplier_use_prepay, supplier_payment_type,
				supplier_officer1_name, supplier_officer1_tel, supplier_officer1_mobile, supplier_officer1_email, 
				supplier_officer2_name, supplier_officer2_tel, supplier_officer2_mobile, supplier_officer2_email, 
				supplier_officer3_name, supplier_officer3_tel, supplier_officer3_mobile, supplier_officer3_email, 
				supplier_officer4_name, supplier_officer4_tel, supplier_officer4_mobile, supplier_officer4_email, 
				supplier_md, supplier_etc, supplier_regip, last_member_idx)
				VALUES 
				(
					'".$maxIDX."',
					N'".$manage_group_idx."',
					N'".$supplier_name."',
					N'".$supplier_ceo_name."',
					N'".$supplier_license_number."',
					N'".$supplier_zipcode."',
					N'".$supplier_addr1."',
					N'".$supplier_addr2."',
					N'".$supplier_fax."',
					N'".$supplier_startdate."',
					N'".$supplier_enddate."',
					N'".$supplier_license_file."',
					N'".$supplier_bank_account_number."',
					N'".$supplier_bank_name."',
					N'".$supplier_bank_book_copy_file."',
					N'".$supplier_bank_holder_name."',
					N'".$supplier_email_default."',
					N'".$supplier_email_account."',
					N'".$supplier_email_order."',
					N'".$supplier_use_prepay."',
					N'".$supplier_payment_type."',
					N'".$supplier_officer1_name."',
					N'".$supplier_officer1_tel."',
					N'".$supplier_officer1_mobile."',
					N'".$supplier_officer1_email."',
					N'".$supplier_officer2_name."',
					N'".$supplier_officer2_tel."',
					N'".$supplier_officer2_mobile."',
					N'".$supplier_officer2_email."',
					N'".$supplier_officer3_name."',
					N'".$supplier_officer3_tel."',
					N'".$supplier_officer3_mobile."',
					N'".$supplier_officer3_email."',
					N'".$supplier_officer4_name."',
					N'".$supplier_officer4_tel."',
					N'".$supplier_officer4_mobile."',
					N'".$supplier_officer4_email."',
					N'".$supplier_md."',
					N'".$supplier_etc."',
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

	/**
	 * 가장 큰 공급처 일련번호 반환
	 * 공급처 IDX 범위 : 40000 ~ 59999
	 * out : int
	 */
	public function getMaxSupplierIdx()
	{
		$qry = "Select Max(idx) From DY_MEMBER Where idx > 40000 And idx < 60000  ";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 공급처 정보 리턴
	 * member_type = 'SUPPLIER' 만 조회
	 * $supplier_idx : DY_MEMBER 테이블 IDX
	 * out : int
	 */
	public function getSupplierData($supplier_idx)
	{
		$qry = "
			Select M.member_id, M.is_use, V.*
			From DY_MEMBER M Left Outer Join DY_MEMBER_SUPPLIER V On M.idx = V.member_idx
			Where M.is_del = N'N' And M.member_type = 'SUPPLIER' And M.idx = N'".$supplier_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 공급처 정보 Update
	 * $args
	 * out : boolean
	 */
	public function updateSupplier($args)
	{
		global $GL_Member;
		extract($args);

		//기존 공급처 가져오기
		$supplierInfo = $this->getSupplierData($idx);


		if($supplierInfo) {
			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작


			$qry = "
				Update DY_MEMBER
				Set moddate = NOW()
				, modip = N'" . $_SERVER["REMOTE_ADDR"] . "'
				, last_member_idx = N'".$GL_Member["member_idx"]."'
			";
			if ($login_pw) {
				$qry .= "
					, member_pw = N'" . $login_pw . "'
				";
			}
			if($is_use) {
				$qry .= "
					, is_use = N'" . $is_use . "'
				";
			}
			$qry .= "
				Where idx = '" . $idx . "'
			";
			$rst = parent::execSqlUpdate($qry);



			$qry = "
				Update DY_MEMBER_SUPPLIER
					Set 
					supplier_moddate = NOW(), 
					supplier_modip = N'" . $_SERVER["REMOTE_ADDR"] . "', 
					last_member_idx = N'".$GL_Member["member_idx"]."',
			";
			if($supplier_name) {
				$qry .= "
					supplier_name =             N'" . $supplier_name . "',
				";
			}
			if($manage_group_idx != null) {
				$qry .= "
					manage_group_idx =           N'" . $manage_group_idx . "',
				";
			}
			$qry .= "
					supplier_ceo_name =            N'" . $supplier_ceo_name . "',
					supplier_license_number =      N'" . $supplier_license_number . "',
					supplier_zipcode =             N'" . $supplier_zipcode . "',
					supplier_addr1 =               N'" . $supplier_addr1 . "',
					supplier_addr2 =               N'" . $supplier_addr2 . "',
			";
			if($manage_group_idx != null) {
				$qry .= "
					supplier_fax =                 N'" . $supplier_fax . "',
				";
			}
			if($supplier_startdate != null) {
				$qry .= "
					supplier_startdate =           N'" . $supplier_startdate . "',
				";
			}
			if($supplier_enddate != null) {
				$qry .= "
					supplier_enddate =             N'" . $supplier_enddate . "',
				";
			}
			if($supplier_license_file != null) {
				$qry .= "
					supplier_license_file =        N'" . $supplier_license_file . "',
				";
			}

			$qry .= "
					supplier_bank_account_number = N'" . $supplier_bank_account_number . "',
					supplier_bank_name =           N'" . $supplier_bank_name . "',
					supplier_bank_holder_name =    N'" . $supplier_bank_holder_name . "',
			";

			if($supplier_bank_book_copy_file != null) {
				$qry .= "
					supplier_bank_book_copy_file = N'" . $supplier_bank_book_copy_file . "',
				";
			}

			if($supplier_email_default != null) {
				$qry .= "
					supplier_email_default =       N'" . $supplier_email_default . "',
				";
			}

			if($supplier_email_account != null) {
				$qry .= "
					supplier_email_account =       N'" . $supplier_email_account . "',
				";
			}

			if($supplier_email_order != null) {
				$qry .= "
					supplier_email_order =         N'" . $supplier_email_order . "',
				";
			}

			if($supplier_use_prepay != null) {
				$qry .= "
					supplier_use_prepay =         N'" . $supplier_use_prepay . "',
				";
			}

            if($supplier_payment_type != null) {
                $qry .= "
					supplier_payment_type =         N'" . $supplier_payment_type . "',
				";
            }

			$qry .= "
					supplier_officer1_name =       N'" . $supplier_officer1_name . "',
					supplier_officer1_tel =        N'" . $supplier_officer1_tel . "',
					supplier_officer1_mobile =     N'" . $supplier_officer1_mobile . "',
					supplier_officer1_email =      N'" . $supplier_officer1_email . "',
			";

			if($supplier_officer2_name != null) {
				$qry .= "
					supplier_officer2_name =       N'" . $supplier_officer2_name . "',
					supplier_officer2_tel =        N'" . $supplier_officer2_tel . "',
					supplier_officer2_mobile =     N'" . $supplier_officer2_mobile . "',
					supplier_officer2_email =      N'" . $supplier_officer2_email . "',
				";
			}
			if($supplier_officer3_name != null) {
				$qry .= "
					supplier_officer3_name =       N'" . $supplier_officer3_name . "',
					supplier_officer3_tel =        N'" . $supplier_officer3_tel . "',
					supplier_officer3_mobile =     N'" . $supplier_officer3_mobile . "',
					supplier_officer3_email =      N'" . $supplier_officer3_email . "',
				";
			}
			if($supplier_officer4_name != null) {
				$qry .= "
					supplier_officer4_name =       N'" . $supplier_officer4_name . "',
					supplier_officer4_tel =        N'" . $supplier_officer4_tel . "',
					supplier_officer4_mobile =     N'" . $supplier_officer4_mobile . "',
					supplier_officer4_email =      N'" . $supplier_officer4_email . "',
				";
			}
			$qry .= "
					supplier_md =                  N'" . $supplier_md . "',
					supplier_etc =                 N'" . $supplier_etc . "'
				Where member_idx = '" . $idx . "'
			";

			$rst2 = parent::execSqlUpdate($qry);

			parent::sqlTransactionCommit();     //트랜잭션 커밋
			parent::db_close();
		}


		return $rst;
	}

	/**
	 * 공급처 코드(IDX) 의 유효성 반환
	 * 존재하지 않는 벤더사 또는 삭제된 벤더사 일 경우 false
	 * $supplier_idx : 벤더사 테이블 IDX
	 * out : boolean (존재하지 않는 벤더사 또는 삭제된 벤더사 일 경우 false)
	 */
	public function isValidSupplier($supplier_idx)
	{
		$qry = "
			Select count(*) 
			From DY_MEMBER M Left Outer Join DY_MEMBER_SUPPLIER V
				On M.idx = V.member_idx
			WHERE 
				M.is_del = N'N'
				And M.member_type = 'SUPPLIER'
				And M.idx = N'".$supplier_idx."'
				And IFNULL(V.member_idx, 0) != 0
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return ($rst == 1) ? true : false;
	}

	/**
	 * 사용중인 공급처 목록 반환
	 * @return array
	 */
	public function getUseSupplierList(){

		$qry = "
			Select 
				S.*
			From DY_MEMBER_SUPPLIER S 
				Left Outer Join DY_MEMBER M On S.member_idx = M.idx
			Where M.is_del = N'N' And M.is_use = N'Y'
			Order By S.supplier_name ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 사용중인 공급처 정보 리턴
	 * member_type = 'SUPPLIER' 만 조회
	 * $supplier_idx : DY_MEMBER 테이블 IDX
	 * out : int
	 */
	public function getUseSupplierData($supplier_idx)
	{
		$qry = "
			Select V.*
			From DY_MEMBER M Left Outer Join DY_MEMBER_SUPPLIER V On M.idx = V.member_idx
			Where M.is_del = N'N' And M.is_use = N'Y' And M.member_type = 'SUPPLIER' And M.idx = N'".$supplier_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}
}
?>