<?php
/**
 * 권한그룹 관련 Class
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
class MemberGroup extends Dbconn
{
	/*
	 * 그룹명 중복 체크
	 * $member_group_name : 그룹명
	 * out : boolean (중복 시 false)
	 */
	public function checkDupName($member_group_name)
	{
		if($member_group_name) {
			$qry = "
				Select count(*) From DY_MEMBER_GROUP
				Where member_group_is_del = 'N' And member_group_is_hidden = N'N' And member_group_name = N'" . $member_group_name . "'
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
	 * 가장 큰 그룹 일련번호 반환
	 * 권한그룹 IDX 범위 : 110000 ~ 119999
	 * out : int
	 */
	public function getMaxMemberGroupIdx()
	{
		$qry = "Select Max(member_group_idx) From DY_MEMBER_GROUP Where member_group_idx < 120000 ";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 그룹 Insert
	 * DY_MEMBER_GROUP, DY_MEMBER_GROUP_USER 두 테이블 Insert
	 * Transaction 처리
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertMemberGroup($args)
	{
		global $GL_Member;
		$member_group_name = "";
		$member_group_is_use = "";
		$member_idx_list = array();

		extract($args);

		$maxIDX = $this->getMaxMemberGroupIdx();
		if(!$maxIDX) $maxIDX = 110000;
		$maxIDX = $maxIDX + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Insert Into DY_MEMBER_GROUP
			(member_group_idx, member_group_name, member_group_regip, member_group_is_use, last_member_idx)
			VALUES 
			(
				N'$maxIDX',
				N'$member_group_name',
				N'".$_SERVER["REMOTE_ADDR"]."',
				N'".$member_group_is_use."',
				N'".$GL_Member["member_idx"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		if($maxIDX)
		{
			if($member_idx_list) {
				foreach($member_idx_list as $user_idx) {

					$qry = "
						Insert Into DY_MEMBER_GROUP_USER
						(member_group_idx, member_idx, member_group_user_regip, member_group_user_is_use, last_member_idx)
						VALUES 
						(
							'" . $maxIDX . "',
							N'" . $user_idx . "',
							N'" . $_SERVER["REMOTE_ADDR"] . "',
							N'Y',
							N'" . $GL_Member["member_idx"] . "'
						)
					";

					$rst2 = parent::execSqlInsert($qry);
				}
			}
			parent::sqlTransactionCommit();     //트랜잭션 커밋
		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();
		return $maxIDX;
	}

	/*
	 * 권한그룹 Update
	 * DY_MEMBER, DY_MEMBER_USER 두 테이블 Update
	 * Transaction 처리
	 * $args
	 * out : boolean
	 */
	public function updateMemberGroup($args)
	{
		global $GL_Member;
		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Update DY_MEMBER_GROUP
			Set 
				member_group_name = N'" . $member_group_name . "'
				, member_group_etc = N'" . $member_group_etc . "'
				, member_group_is_use = N'" . $member_group_is_use . "'
				, member_group_moddate = getdate()
				, member_group_modip = N'".$_SERVER["REMOTE_ADDR"]."'
				, last_member_idx = N'".$GL_Member["member_idx"]."'
			Where member_group_idx = '".$member_group_idx."'
		";
		$rst = parent::execSqlUpdate($qry);

		//기존 사용자 Truncate
		$qry = "
			Delete From DY_MEMBER_GROUP_USER
			Where member_group_idx = N'".$member_group_idx."'
		";
		$rst2 = parent::execSqlUpdate($qry);

		//그룹 멤버 Insert
		if($member_idx_list) {
			foreach($member_idx_list as $user_idx) {

				$qry = "
						Insert Into DY_MEMBER_GROUP_USER
						(member_group_idx, member_idx, member_group_user_regip, member_group_user_is_use, last_member_idx)
						VALUES 
						(
							'" . $member_group_idx . "',
							N'" . $user_idx . "',
							N'" . $_SERVER["REMOTE_ADDR"] . "',
							N'Y',
							N'" . $GL_Member["member_idx"] . "'
						)
					";

				$rst3 = parent::execSqlInsert($qry);
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋

		parent::db_close();
		return $rst;
	}

	/*
	 * 권한그룹 정보 반환
	 * $idx : 권한그룹 IDX
	 * out : Array (ONE ROW)
	 */
	public function getMemberGroupData($idx){
		$qry = "
			Select M.* 
			From  DY_MEMBER_GROUP M
			Where M.member_group_idx = N'".$idx."' And M.member_group_is_del= N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 권한그룹 멤버 리스트 반환
	 * $idx : 권한그룹 IDX
	 * out : Array
	 */
	public function getMemberGroupUserList($idx){
		$qry = "
			Select U.*, MU.member_id, MU.name
			From  DY_MEMBER_GROUP_USER U
				Left Outer Join DY_MEMBER M On U.member_idx = M.idx
				Left Outer Join DY_MEMBER_USER MU On M.idx = MU.member_idx
			Where 
				U.member_group_idx = N'".$idx."' 
				And U.member_group_user_is_del = N'N'
				And M.is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}
}
?>