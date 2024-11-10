<?php
/**
 * 메인화면관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */

class MainControl extends Dbconn
{
	public function getMyMainControl($member_idx)
	{
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

		foreach ($list as $k => $v)
		{
			$qry = "
				IF exists (select * From DY_MY_MAIN Where member_idx = N'$member_idx' And my_main_type = N'$k')
				Begin
					Update DY_MY_MAIN
						Set 
							my_main_is_use = N'$v'
							, my_moddate = getdate()
							, my_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where
							member_idx = N'$member_idx' And my_main_type = N'$k'
				End
				ELSE
				Begin
					Insert Into DY_MY_MAIN
					(member_idx, my_main_type, my_main_is_use, my_regip, last_member_idx)
					VALUES
					(
					N'$member_idx'
					, N'$k'
					, N'$v'
					, N'$modip'
					, N'$last_member_idx'
					)
				End
			";

			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();
		}

		return true;
	}
}
?>