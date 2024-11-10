<?php
/**
 * 공통코드 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Code extends Dbconn
{
	/*
	 * 코드 중복 체크
	 * $code : 코드 값
	 * $code_idx : 상위 코드 IDX
	 * out : boolean (중복 시 false)
	 */
	public function checkDupCode($code, $code_idx)
	{
		if($code) {
			$qry = "
				Select count(*) From DY_CODE
				Where is_del = 'N'
				And code_idx = N'".$code_idx."' 
				And code = N'" . $code . "'
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
	 * 최상위 부모 코드 목록
	 * out : Array
	 */
	public function getParentCode()
	{
		$qry = "
			Select * From DY_CODE
				Where is_del = N'N'
					And code_idx = 0
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 코드 Insert
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertCode($args)
	{
		$code= "";
		$code_idx = "";
		$code_name = "";
		$is_use = "N";
		extract($args);

		parent::db_connect();

		//상위코드 정보 조회
		$qry = "Select code From DY_CODE Where idx = '".$code_idx."'";
		$parent_code = parent::execSqlOneCol($qry);

		$qry = "
			Insert Into DY_CODE
			(code_idx, code, code_name, parent_code, is_use, regip)
			VALUES 
			(
				N'$code_idx',
				N'$code',
				N'$code_name',
				N'$parent_code',
				N'".$is_use."',
				N'".$_SERVER["REMOTE_ADDR"]."'
			);
		";

		$rst = parent::execSqlInsert($qry);

		parent::db_close();
		return $rst;
	}

	/*
	 * 코드 Update
	 * $args
	 * out : boolean
	 */
	public function updateCode($args)
	{
		extract($args);

		parent::db_connect();

		$qry = "
			Update DY_CODE
			Set code_name = N'".$code_name."'
			, code_idx = N'".$code_idx."'
			, is_use = N'".$is_use."'
			, moddate = getdate()
			, modip = N'".$_SERVER["REMOTE_ADDR"]."'
			Where idx = '".$idx."'
		";
		$rst = parent::execSqlUpdate($qry);

		parent::db_close();
		return $rst;
	}

	/*
	 * 코드 정보
	 * $idx : DY_CODE 테이블 idx 값
	 * out : Array (ONE ROW)
	 */
	public function getCodeData($idx){
		$qry = "
			Select * 
			From  DY_CODE
			Where idx = N'".$idx."'
			And is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 개별 코드 정보 얻기
	 * @param $code : 코드 값
	 * @return array|false|null
	 */
	public function getCodeDataByCode($code){
		$qry = "
			Select * 
			From  DY_CODE
			Where code = N'".$code."'
			And is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 개별 코드 정보 얻기
	 * @param $parent_code:  부모코드값
	 * @param $code : 코드 값
	 * @return array|false|null
	 */
	public function getCodeDataByCodes($parent_code, $code){
		$qry = "
			Select * 
			From  DY_CODE
			Where code = N'".$code."'
			And  parent_code = N'".$parent_code."'
			And is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 부모 코드에 속한 코드 목록
	 * @param $parent_code : 부모 코드 값
	 * @return array
	 */
	public function getSubCodeList($parent_code){
		$qry = "
			Select code_idx, code_name, code
			From  DY_CODE
			Where 
			parent_code = N'".$parent_code."'
			And is_del = N'N'
			order by sort ASC, idx ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 택배사 코드/명 가져오기
	 * @return array
	 */
	public function getDeliveryList()
	{
		$qry = "
		SELECT delivery_code, delivery_name, min(sort_num)
		  FROM [DY_DELIVERY_CODE]
		  Where market_code = 'DY'
		  Group by delivery_code, delivery_name
			Order by min(sort_num) ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}
}
?>