<?php
/**
 * 메인화면관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */

class MainControl extends DBConn
{
	public function getMyMainControl($member_idx) {
		$qry = "
			Select * From DY_MY_MAIN Where member_idx = N'$member_idx'
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	public function saveMainControl($member_idx, $list)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$this->db_connect();

		foreach ($list as $k => $v) {
		    $idx = $this->execSqlOneCol("SELECT my_idx FROM DY_MY_MAIN WHERE member_idx = N'$member_idx' AND my_main_type = N'$k'");
		    if (!$idx) $idx = "NULL";

			$qry = "
				Insert Into DY_MY_MAIN
                (my_idx, member_idx, my_main_type, my_main_is_use, my_regip, last_member_idx)
                VALUES
                (N'$idx', N'$member_idx', N'$k', '$v', N'$modip', N'$last_member_idx')
                ON DUPLICATE KEY UPDATE
                my_main_is_use = '$v', my_moddate = now(), my_modip = N'$modip', last_member_idx = N'$last_member_idx'
			";

			$rst = $this->execSqlInsert($qry);
		}

		$this->db_close();

		return true;
	}
}
?>