<?php
/**
 * 사용자 관련 Class
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
class Users extends DBConn
{
	/*
	 * 로그인 아이디 중복 체크
	 * $login_id : 로그인 아이디
	 * out : boolean (중복 시 false)
	 */
	public function checkDupID($login_id)
	{
		if($login_id) {
			$qry = "
				Select count(*) From DY_MEMBER
				Where is_del = 'N' And member_id = N'" . $login_id . "'
			";

			parent::db_connect();
			$rst = parent::execSqlOneCol($qry);
			parent::db_close();

			return ($rst > 0) ? false : true;
		}else{
			return false;
		}

	}

	/*
	 * 가장 큰 사용자 일련번호 반환
	 * 사용자 IDX 범위 : 10000 ~ 19999
	 * out : int
	 */
	public function getMaxUserIdx()
	{
		$qry = "Select Max(idx) From DY_MEMBER Where idx < 20000 ";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 사용자 Insert
	 * DY_MEMBER, DY_MEMBER_USER 두 테이블 Insert
	 * Transaction 처리
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertUser($args)
	{
		global $GL_Member;
		extract($args);

		$maxIDX = $this->getMaxUserIdx();
		if(!$maxIDX) $maxIDX = 10000;
		$maxIDX = $maxIDX + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Insert Into DY_MEMBER
			(idx, member_id, member_pw, member_type, is_use, regip, last_member_idx)
			VALUES 
			(
				N'$maxIDX',
				N'$login_id',
				N'$login_pw',
				N'USER',
				N'".$is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."',
				N'".$GL_Member["member_idx"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		if($maxIDX)
		{
			$qry = "
				Insert Into DY_MEMBER_USER
				(member_idx, member_id, name, tel, mobile, email, etc, regip, last_member_idx)
				VALUES 
				(
					'".$maxIDX."',
					N'".$login_id."',
					N'".$name."',
					N'".$tel."',
					N'".$mobile."',
					N'".$email."',
					N'".$etc."',
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
	 * 사용자 Update
	 * DY_MEMBER, DY_MEMBER_USER 두 테이블 Update
	 * Transaction 처리
	 * $args
	 * out : boolean
	 */
	public function updateUser($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Update DY_MEMBER
			Set 
			moddate = NOW()
			, modip = N'".$_SERVER["REMOTE_ADDR"]."'
			, last_member_idx = N'".$GL_Member["member_idx"]."'
		";

		if($is_use)
		{
			$qry .= ", is_use = N'".$is_use."'";
		}

		if($login_pw) {
			$qry .= "
				, member_pw = N'" . $login_pw . "'
			";
		}
		$qry .= "
			Where idx = '".$idx."'
		";
		$rst = parent::execSqlUpdate($qry);

		$qry = "
			Update DY_MEMBER_USER
				Set name = N'".$name."',
				tel = N'".$tel."',
				mobile = N'".$mobile."',
				email = N'".$email."',
				etc = N'".$etc."',
				moddate = NOW(), 
				modip = N'".$_SERVER["REMOTE_ADDR"]."',
				last_member_idx = N'".$GL_Member["member_idx"]."'
			Where member_idx = '".$idx."'
		";

		$rst2 = parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋

		parent::db_close();
		return $rst;
	}

	/*
	 * 사용자 정보 반환
	 * $idx : 사용자 IDX
	 * out : Array (ONE ROW)
	 */
	public function getUserData($idx){
		$qry = "
			Select M.*, U.name, U.tel, U.mobile, U.email, U.etc 
			From  DY_MEMBER M Left Outer Join DY_MEMBER_USER U 
				On M.idx = U.member_idx
			Where M.idx = N'".$idx."' And M.is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	public function getUserType($idx){
		$qry = "
			Select member_type
			From DY_MEMBER
			Where idx = N'".$idx."' And is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 일반 사용자 리스트
	 */
	public function getUserList(){
		$qry = "
			Select M.idx, M.member_id, U.name
			From DY_MEMBER M 
			Inner Join DY_MEMBER_USER U On M.idx = U.member_idx
			Where M.is_del = N'N' 
			  And (M.member_type = N'USER' OR M.member_type = N'ADMN')
			Order By M.member_id ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}
}
?>