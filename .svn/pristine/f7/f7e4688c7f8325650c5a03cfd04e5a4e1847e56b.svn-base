<?php

/**
 * 항목 설정 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class ColumnModel extends Dbconn
{
	/**
	 * @param $target
	 * @param $saveData
	 * @return int
	 */
	public function saveColumnModel($target, $saveData)
	{
		global $GL_Member;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//기존 데이터 제거
		$qry = "
			Delete From DY_COLUMN_USER
			Where member_idx = N'" . $GL_Member["member_idx"] . "'
				AND col_target = N'$target'
		";

		parent::execSqlUpdate($qry);

		$saveCnt = 0;
		if (count($saveData) > 0) {
			foreach ($saveData as $col) {
				$qry = "
					Insert Into DY_COLUMN_USER
					(col_target, col_field_name, col_user_visible_name, col_user_sort, col_user_is_use, member_idx, col_user_regip) 
					VALUES 
					(
						N'" . $target . "'
						, N'" . $col["column_name"] . "'
						, N'" . $col["visible_name"] . "'
						, N'" . $col["sort"] . "'
						, N'" . (($col["is_use"] == "true") ? "Y" : "N") . "'
						, N'" . $GL_Member["member_idx"] . "'
						, N'" . $_SERVER["REMOTE_ADDR"] . "'
					)
				";

				$idx = parent::execSqlInsert($qry);
				$saveCnt++;
			}
		}

		if ($saveCnt) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
		} else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();
		return $saveCnt;
	}

	/**
	 * @param $target
	 * @param $member_idx
	 * @return array
	 */
	public function getUserColumn($target, $member_idx)
	{
		$qry = "
			Select * 
			From DY_COLUMN_USER
			Where col_target = N'$target' And member_idx = N'$member_idx'
			Order by col_user_sort ASC
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * @param $target
	 * @param $saveData
	 * @return int
	 */
	public function saveColumnModelXls($target, $saveData)
	{
		global $GL_Member;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//기존 데이터 제거
		$qry = "
			Delete From DY_COLUMN_USER_XLS
			Where member_idx = N'" . $GL_Member["member_idx"] . "'
				AND col_target = N'$target'
		";

		parent::execSqlUpdate($qry);

		$saveCnt = 0;
		if (count($saveData) > 0) {
			foreach ($saveData as $col) {
				$qry = "
					Insert Into DY_COLUMN_USER_XLS
					(col_target, col_field_name, col_user_visible_name, col_user_sort, col_user_is_use, member_idx, col_user_regip) 
					VALUES 
					(
						N'" . $target . "'
						, N'" . $col["column_name"] . "'
						, N'" . $col["visible_name"] . "'
						, N'" . $col["sort"] . "'
						, N'" . (($col["is_use"] == "true") ? "Y" : "N") . "'
						, N'" . $GL_Member["member_idx"] . "'
						, N'" . $_SERVER["REMOTE_ADDR"] . "'
					)
				";

				$idx = parent::execSqlInsert($qry);
				$saveCnt++;
			}
		}

		if ($saveCnt) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
		} else {
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();
		return $saveCnt;
	}

	/**
	 * @param $target
	 * @param $member_idx
	 * @return array
	 */
	public function getUserColumnXls($target, $member_idx)
	{
		$qry = "
			Select * 
			From DY_COLUMN_USER_XLS
			Where col_target = N'$target' And member_idx = N'$member_idx'
			Order by col_user_sort ASC
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}
}