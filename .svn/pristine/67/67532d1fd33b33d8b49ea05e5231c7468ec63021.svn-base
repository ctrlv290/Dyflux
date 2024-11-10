<?php
/**
 * 파일 업로드 관련 Class
 * User: woox
 * Date: 2018-11-10
 */

class Files extends DBConn
{
	/*
	 * 업로드 파일 정보 Insert
	 * $args
	 * out : Array
	 */
	public function insertFile($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$ref_table = "";
		$ref_table_idx = "";
		$ref_table_column = "";
		$user_filename = "";
		$save_path = "";
		$save_webpath = "";
		$save_filename = "";
		$extension = "";
		$mimetype = "";
		$file_size = "";
		$num = "";

		extract($args);
		$qry = "Insert Into DY_FILES 
				(ref_table, ref_table_idx, ref_table_column, user_filename, save_path, save_webpath, save_filename, extension, mimetype, file_size, num, file_regip, last_member_idx) 
				VALUES
				(
					N'" . $ref_table . "',
					N'" . $ref_table_idx . "',
					N'" . $ref_table_column . "',
					N'" . $user_filename . "',
					N'" . $save_path . "',
					N'" . $save_webpath . "',
					N'" . $save_filename . "',
					N'" . $extension . "',
					N'" . $mimetype . "',
					N'" . $file_size . "',
					N'" . $num . "',
					N'" . $modip . "',
					N'" . $last_member_idx . "'
				)  
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 업로드 파일 정보 Update
	 * $args
	 * out : Array
	 */
	public function updateFile($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$file_idx = "";
		$ref_table = "";
		$ref_table_idx = "";
		$user_filename = "";
		$save_path = "";
		$save_webpath = "";
		$save_filename = "";
		$extension = "";
		$mimetype = "";
		$file_size = "";
		$num = "";

		extract($args);
		$qry = "
				Update DY_FILES
				Set 
					user_filename = N'" . $user_filename . "',
					save_path = N'" . $save_path . "',
					save_webpath = N'" . $save_webpath . "',
					save_filename = N'" . $save_filename . "',
					extension = N'" . $extension . "',
					mimetype = N'" . $mimetype . "',
					file_size = N'" . $file_size . "',
					num = N'" . $num . "',
					file_moddate = NOW(),
					file_modip = N'" . $_SERVER["REMOTE_ADDR"] . "',
					last_member_idx = N'$last_member_idx'
				Where file_idx = N'". $file_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 업로드 파일 정보 Update
	 * $args
	 * out : Array
	 */
	public function updateFileActive($args)
	{
		$file_idx = "";
		$ref_table_idx = "";

		extract($args);
		$qry = "
				Update DY_FILES
				Set 
					ref_table_idx = N'" . $ref_table_idx . "',
					is_use = N'Y'
				Where file_idx = N'". $file_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 업로드 파일 사용 가능하도록  Update
	 * @param $file_idx
	 * @param $ref_table_idx
	 * @param $ref_table_column
	 * @return int
	 */
	public function updateFileIsUseY($file_idx, $ref_table_idx, $ref_table_column)
	{
		$qry = "
				Update DY_FILES
				Set 
					ref_table_idx = N'" . $ref_table_idx . "',
					ref_table_column = N'" . $ref_table_column . "',
					is_use = N'Y'
				Where file_idx = N'". $file_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 업로드 파일 정보 반환
	 * $file_idx
	 * out : Array (ONE ROW)
	 */
	public function getFileInfo($file_idx)
	{
		$qry = "
				Select * From DY_FILES
				Where is_del = N'N' And file_idx = N'". $file_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 다운로드를 위한 파일 정보 확인
	 * $file_idx, $save_filename
	 * out : Array (ONE ROW)
	 */
	public function checkFileInfo($file_idx, $save_filename)
	{
		$qry = "
				Select * From DY_FILES
				Where is_del = N'N' And file_idx = N'". $file_idx."' And save_filename = N'" . $save_filename . "'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * Ref Table 과 Ref Idx 으로 파일 목록 반환
	 * @param $ref_table
	 * @param $ref_table_idx
	 * @return array
	 */
	public function getFileListByRef($ref_table, $ref_table_idx)
	{
		$qry = "
				Select * From DY_FILES
				Where is_del = N'N' And ref_table = N'". $ref_table."' And ref_table_idx = N'" . $ref_table_idx . "'
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 파일 삭제 (사용안함 으로 변경)
	 * 물리적 파일은 삭제 하지 않음!
	 * @param $file_idx
	 * @return int
	 */
	public function deleteFile($file_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
				Update DY_FILES
				Set 
					file_moddate = NOW(),
					file_modip = N'$modip',
					is_use = N'N',
					is_del = N'Y',
				    last_member_idx = N'$last_member_idx'
				Where file_idx = N'". $file_idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

}
?>