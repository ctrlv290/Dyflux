<?php
/**
 * 권한 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Permission extends DBConn
{
	/*
	 * 권한 Insert
	 * 기존 권한을 모두 삭제 후
	 * 넘겨 받은 menu_idx 배열을 이용하여 새로운 권한을 Insert
	 * $member_idx : 사용자 IDX
	 * $menu_idx_array : DY_MENU 테이블 IDX
	 * $permissionChangeAdd : 추가된 권한 DY_MENU IDX (for 변경이력)
	 * $permissionChangeDelete : 삭제된 권한 DY_MENU IDX (for 변경이력)
	 * $history_dml_flag : 변경 이력  DML Flag
	 * out : boolean
	 */
	public function insertPermission($member_idx, $menu_idx_array, $permissionChangeAdd, $permissionChangeDelete, $history_dml_flag)
	{
		global $GL_Member;


		parent::db_connect();
		parent::sqlTransactionBegin();              //트랜잭션 시작


		//기존 권한 모두 삭제
		$qry = "
			Delete
			From  DY_PERMISSION 
			Where member_idx = N'".$member_idx."'
		";
		$rst = parent::execSqlUpdate($qry);


		//menu idx array 를 권한 Insert
		foreach($menu_idx_array as $menu) {
			$qry = "
				Insert Into DY_PERMISSION
				(menu_idx, member_idx, permission_regip, last_member_idx)
				VALUES 
				(
					N'".$menu["idx"]."',
					N'$member_idx',
					N'" . $_SERVER["REMOTE_ADDR"] . "',
					N'" . $GL_Member["member_idx"] . "'
				);
			";
			$rst = parent::execSqlInsert($qry);
		}

		//삭제된 권한 History Insert
		if($permissionChangeDelete) {
			$qry = "
				Insert Into DY_MEMBER_USER_HISTORY
				(table_nm, table_idx1, table_idx2, table_idx3, column_mn, before_data, after_data, member_idx, dml_flag, memo, regip) 
				VALUES 
				(
					N'DY_MEMBER_USER_PERMISSION'
					, N'" . $member_idx . "'
					, N'0'
					, N'0'
					, N'권한 삭제'
					, N''
					, N'" . $permissionChangeDelete . "'
					, N'" . $GL_Member["member_idx"] . "'
					, N'" . $history_dml_flag . "'
					, N'권한 삭제'
					, N'" . $_SERVER["REMOTE_ADDR"] . "'
				)
			";
			$rst = parent::execSqlInsert($qry);
		}

		//추가된 권한 History Insert
		if($permissionChangeAdd) {
			$qry = "
				Insert Into DY_MEMBER_USER_HISTORY
				(table_nm, table_idx1, table_idx2, table_idx3, column_mn, before_data, after_data, member_idx, dml_flag, memo, regip) 
				VALUES 
				(
					N'DY_MEMBER_USER_PERMISSION'
					, N'".$member_idx."'
					, N'0'
					, N'0'
					, N'권한 추가'
					, N''
					, N'".$permissionChangeAdd."'
					, N'" . $GL_Member["member_idx"] . "'
					, N'" . $history_dml_flag . "'
					, N'권한 추가'
					, N'" . $_SERVER["REMOTE_ADDR"] . "'
				)
			";
			$rst = parent::execSqlInsert($qry);
		}

		parent::sqlTransactionCommit();             //트랜잭션 커밋
		parent::db_close();
		return true;
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
			Set is_use = N'".$is_use."'
			, moddate = NOW()
			, modip = N'".$_SERVER["REMOTE_ADDR"]."'
			, last_member_idx = N'".$GL_Member["member_idx"]."'
		";
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
	 * 사용자 권한 모두 삭제
	 * $member_idx : 사용자 IDX
	 * out : Array
	 */
	public function deletePermissionByMemberIdx($member_idx){
		$qry = "
			Delete
			From  DY_PERMISSION 
			Where member_idx = N'".$member_idx."'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}
}
?>