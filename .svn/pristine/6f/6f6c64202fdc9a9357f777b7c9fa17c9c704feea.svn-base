<?php
/**
 * 로그인 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Login extends DBConn
{
	/*
	 * 로그인을 위한 ID 체크
	 * $member_id
	 * out : Array
	 */
	public function checkLoginID($member_id){
		$qry = "
			Select idx, member_type, member_pw, lastlogin_date, can_mobile_login
			From DY_MEMBER 
			Where is_use = N'Y' And is_del = N'N'
			And member_id = N'".$member_id."' 
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
 * 사용자 IDX 로 사용자 정보 반환
 * @param $member_idx
 * @return array|false|null
 */
	public function checkLoginIDByIdx($member_idx){
		$qry = "
			Select idx, member_type, member_id, member_pw, lastlogin_date 
			From DY_MEMBER 
			Where is_use = N'Y' And is_del = N'N'
			And idx = N'".$member_idx."' 
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * CS 로그인 체크 후 정보 반화
	 * @param $member_id
	 * @param $cs_login_token
	 * @return array|false|null
	 */
	public function checkLoginTokenID($member_id, $cs_login_token){
		$cs_login_ip = $_SERVER["REMOTE_ADDR"];
		$qry = "
			Select idx, member_type, member_pw, lastlogin_date 
			From DY_MEMBER 
			Where is_use = N'Y' And is_del = N'N' 
			And member_type IN (N'USER', N'ADMIN')
			And member_id = N'".$member_id."' 
			And cs_login_token = N'".$cs_login_token."' 
			And cs_login_ip = N'".$cs_login_ip."'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 벤더사 승인 상태 리턴
	 * @param $member_idx
	 * @return int|mixed
	 */
	public function checkVendorStatus($member_idx) {
		$qry = "
			Select vendor_status From DY_MEMBER_VENDOR where member_idx = '".$member_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 회원 정보 리턴
	 * $member_idx
	 * out : Array
	 */
	public function getMemberUserData($member_idx){
		$qry = "
			Select * From DY_MEMBER_USER where member_idx = '".$member_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 벤더사 정보 리턴
	 * $member_idx
	 * out : Array
	 */
	public function getMemberVendorData($member_idx){
		$qry = "
			Select * From DY_MEMBER_VENDOR where member_idx = '".$member_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 공급처 정보 리턴
	 * $member_idx
	 * out : Array
	 */
	public function getMemberSupplierData($member_idx){
		$qry = "
			Select * From DY_MEMBER_SUPPLIER where member_idx = '".$member_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 마지막 로그인 일시 Update
	 * @param $member_idx
	 * @param $cs_login_token : CS로그인일 경우 발행된 토큰 코드
	 * @param $cs_login_ip : CS로그인일 경우 해당 IP
	 * @return bool|int|resource
	 */
	public function updateLastLoginInfo($member_idx, $cs_login_token = "", $cs_login_ip = ""){
		// 토큰이 있을 경우만 Update => 웹에서 로그인해도 토큰은 남아 있어야 CS 로그인 유지가 가능함
		$token_qry = "";
		if($cs_login_token != "" && $cs_login_ip != "") {
			$token_qry = ", cs_login_token = N'".$cs_login_token."', cs_login_ip = N'".$cs_login_ip."' ";
		}
		$qry = "
			Update DY_MEMBER
			Set
				lastlogin_date = NOW()
				, lastlogin_ip = N'".$_SERVER["REMOTE_ADDR"]."'
				".$token_qry."
			Where 
				idx = N'".$member_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		$qry = "
			Insert Into DY_MEMBER_LOGIN_LOG
			(member_idx, member_login_regdate, member_login_regip, member_login_agent) 
			VALUES 
			(
				N'".$member_idx."'
				, NOW()
				, N'".$_SERVER["REMOTE_ADDR"]."'
				, N'".$_SERVER["HTTP_USER_AGENT"]."'
			)
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 자동 로그인 토큰 업데이트
	 * @param $token
	 * @param $idx
	 * @return bool|resource
	 */
	public function updateToken($token, $idx)
	{
		$qry = "
			Update DY_MEMBER
				Set auto_login_token = N'$token'
			Where idx = N'$idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 자동 로그인 토큰 업데이트 (모바일용)
	 * @param $token
	 * @param $idx
	 * @return bool|resource
	 */
	public function updateMToken($token, $idx)
	{
		$qry = "
			Update DY_MEMBER
				Set auto_m_login_token = N'$token'
			Where idx = N'$idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 로그인 상테를 점검
	 * 사용자/벤더사/공급처 별로 조건 을 체크
	 * out : boolean (T/F 로그인을 유지해도 되는 지 여부)
	 */
	public function checkLoginData()
	{
		global $GL_Member;
		$returnValue = false;
		if($GL_Member)
		{
			if($GL_Member["member_type"] == "ADMIN" || $GL_Member["member_type"] == "USER")
			{
				$qry = "
					Select count(*) From DY_MEMBER M 
						Where 
						M.is_del = N'N' 
						And M.is_use = N'Y'
						And M.member_type in ('ADMIN', 'USER')
						And M.idx = '".$GL_Member["member_idx"]."'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if($rst == 1){
					$returnValue = true;
				}
			}elseif($GL_Member["member_type"] == "VENDOR"){
				$qry = "
					Select count(*) From DY_MEMBER M
						Left Outer Join DY_MEMBER_VENDOR V On M.idx = V.member_idx 
						Where 
							M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'VENDOR'
							And V.vendor_status = N'VENDOR_APPLY'
						And M.idx = '".$GL_Member["member_idx"]."'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if($rst == 1){
					$returnValue = true;
				}
			}elseif($GL_Member["member_type"] == "SUPPLIER") {
				$qry = "
					Select count(*) From DY_MEMBER M
						Left Outer Join DY_MEMBER_SUPPLIER S On M.idx = S.member_idx 
						Where 
							M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'SUPPLIER'
						And M.idx = '".$GL_Member["member_idx"]."'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if($rst == 1){
					$returnValue = true;
				}
			}

		}
		return $returnValue;
	}

	/*
	 * 로그인 상테를 점검 (모바일용!!)
	 * 사용자만 로그인 가능
	 * 특정 사용자만 로그인 가능 ($GL_Available_MobileLogin_member_idx 참조) - 사용안함
	 * 아래와 같이 변경
	 * DY_MEMBER 테이블 can_mobile_login (Y/N) 필드 확인
	 *
	 * out : boolean (T/F 로그인을 유지해도 되는 지 여부)
	 */
	public function checkMLoginData()
	{
		global $GL_Member_M;
		$returnValue = false;

		if ($GL_Member_M) {
			if ($GL_Member_M["member_type"] == "ADMIN" || $GL_Member_M["member_type"] == "USER") {
				$qry = "
					Select count(*) From DY_MEMBER M 
						Where 
						M.is_del = N'N' 
						And M.is_use = N'Y'
						And M.can_mobile_login = N'Y'
						And M.member_type in ('ADMIN', 'USER')
						And M.idx = '" . $GL_Member_M["member_idx"] . "'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if ($rst == 1) {
					$returnValue = true;
				}
			}
		}
		return $returnValue;
	}

	/**
	 * 로그인 사용자 세션 설정
	 * @param $member_idx
	 * @return bool
	 */
	public function setLoginSession($member_idx)
	{
		global $GL_Member;

		$returnValue = false;

		$_memAry = $this->checkLoginIDByIdx($member_idx);

		if($_memAry) {
			$member_id      = $_memAry["member_id"];
			$member_type    = $_memAry["member_type"];
			$lastlogin_date = $_memAry["lastlogin_date"];

			if ($member_type == "ADMIN" || $member_type == "USER") {

				$member_data = $this->getMemberUserData($member_idx);

				$GL_Member["member_idx"]     = $member_idx;
				$GL_Member["member_id"]      = $member_id;
				$GL_Member["member_name"]    = $member_data["name"];
				$GL_Member["member_email"]   = $member_data["email"];
				$GL_Member["lastlogin_date"] = $lastlogin_date;
				$GL_Member["member_type"]    = $member_type;

			} elseif ($member_type == "VENDOR") {

				$member_data = $this->getMemberVendorData($member_idx);

				$GL_Member["member_idx"]     = $member_idx;
				$GL_Member["member_id"]      = $member_id;
				$GL_Member["member_name"]    = $member_data["vendor_name"];
				$GL_Member["member_email"]   = $member_data["vendor_officer1_email"];
				$GL_Member["lastlogin_date"] = $lastlogin_date;
				$GL_Member["member_type"]    = $member_type;
				$GL_Member["vendor_use_charge"] = $member_data["vendor_use_charge"];
				$GL_Member["vendor_grade"] = $member_data["vendor_grade"];

			} elseif ($member_type == "SUPPLIER") {

				$member_data = $this->getMemberSupplierData($member_idx);

				$GL_Member["member_idx"]     = $member_idx;
				$GL_Member["member_id"]      = $member_id;
				$GL_Member["member_name"]    = $member_data["supplier_name"];
				$GL_Member["member_email"]   = $member_data["supplier_officer1_email"];
				$GL_Member["lastlogin_date"] = $lastlogin_date;
				$GL_Member["member_type"]    = $member_type;

			}

			set_session("dy_member", $GL_Member);
			$returnValue = true;
		}


		return $returnValue;
	}

	/**
	 * 모바일 페이지 로그인 정보 세션 저장
	 * @param $member_idx
	 * @return bool
	 */
	public function setMobileLoginSession($member_idx)
	{
		global $GL_Member_M;

		$returnValue = false;

		$_memAry = $this->checkLoginIDByIdx($member_idx);

		if ($_memAry) {
			$member_id      = $_memAry["member_id"];
			$member_type    = $_memAry["member_type"];
			$lastlogin_date = $_memAry["lastlogin_date"];

			if ($member_type == "ADMIN" || $member_type == "USER") {

				$member_data = $this->getMemberUserData($member_idx);

				$GL_Member_M["member_idx"]     = $member_idx;
				$GL_Member_M["member_id"]      = $member_id;
				$GL_Member_M["member_name"]    = $member_data["name"];
				$GL_Member_M["member_email"]   = $member_data["email"];
				$GL_Member_M["lastlogin_date"] = $lastlogin_date;
				$GL_Member_M["member_type"]    = $member_type;

			}

			set_session("dy_member_mobile", $GL_Member_M);
			$returnValue = true;
		}else{
			$returnValue = false;
		}

		return $returnValue;
	}

	/**
	 * 쿠키에 저장되어 있던 토큰을 이용하여 자동 로그인 체크
	 * @param $token
	 */
	public function checkAutoLogin($token) {
		$qry = "
			Select idx From DY_MEMBER Where auto_login_token = N'$token'
		";

		parent::db_connect();
		$_member_idx = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_member_idx){
			$this -> setLoginSession($_member_idx);
		}
	}


	/**
	 * 쿠키에 저장되어 있던 토큰을 이용하여 자동 로그인 체크 (모바일용!!)
	 * @param $token
	 */
	public function checkAutoMLogin($token) {
		$qry = "
			Select idx, can_mobile_login From DY_MEMBER Where auto_m_login_token = N'$token'
		";

		parent::db_connect();
		$_row = parent::execSqlOneRow($qry);
		parent::db_close();
		$_member_idx = $_row["idx"];
		$_can_mobile_login = $_row["can_mobile_login"];

		if($_row && $_can_mobile_login == "Y"){
			$this -> setMobileLoginSession($_member_idx);
		}
	}


	/**
	 * CS프로그램 로그인을 위해 토큰으로 로그인 시키기!!
	 */
	public function setLoginSessionByToken() {
		$cs_m_id    = trim($_POST["cs_m_id"]);
		$cs_m_token = trim($_POST["cs_m_token"]);
		if($cs_m_id != "" && $cs_m_token != "") {
			$_cs_memAry = $this-> checkLoginTokenID($cs_m_id, $cs_m_token);
			if ($_cs_memAry) {
				$_cs_db_member_idx = $_cs_memAry["idx"];
				$this -> setLoginSession($_cs_db_member_idx);
			}
		}
	}

	/**
	 * CS 프로그램 로그인을 위한 토큰발행(랜덤코드)
	 * @return string
	 */
	function getCSloginToken() {
		$len = 20;
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}


	public function checkMobileLogin($id, $pw, $available_member_idx)
	{
		$qry = "";
	}
}
?>