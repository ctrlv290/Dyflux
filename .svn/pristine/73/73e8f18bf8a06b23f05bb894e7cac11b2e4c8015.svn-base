<?php
/**
 * 택배사관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Delivery extends DBConn
{
	/**
	 * 택배사 배송추적 정보 반환
	 * @param $delivery_idx
	 * @return array|false|null
	 */
	public function getDeliveryData($delivery_idx)
	{
		$qry = "
			Select T.*, D.delivery_name
			From DY_DELIVERY_TRACKING_URL T
			Left Outer Join (
			        SELECT delivery_code, delivery_name, min(sort_num) as sort_num
				    FROM DY_DELIVERY_CODE
					Group by delivery_code, delivery_name
				) D On T.delivery_code = D.delivery_code
			
			Where delivery_is_del = N'N' And delivery_idx = N'$delivery_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}



	/**
	 * 택배사 코드 목록 반환
	 * @return array
	 */
	public function getDeliveryCodeList(){
		$qry = "
		SELECT delivery_code, delivery_name, min(sort_num)
		  FROM DY_DELIVERY_CODE
		  Group by delivery_code, delivery_name
			Order by min(sort_num) ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 택배사 배송조회에 미등록된 택배사 코드 반환
	 * @return array
	 */
	public function getDeliveryCodeListExceptReg(){
		$qry = "
		SELECT delivery_code, delivery_name, min(sort_num)
		  FROM DY_DELIVERY_CODE
			Where delivery_code not in (Select delivery_code From DY_DELIVERY_TRACKING_URL Where delivery_is_del = N'N')
		  Group by delivery_code, delivery_name
			Order by min(sort_num) ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}


	/**
	 * 이미 등록된 택배사 코드 인지 확인
	 * true 면 등록 가능 false 면 이미 등록되어 있음
	 * @param $delivery_code
	 * @return bool
	 */
	public function checkDupDeliveryCode($delivery_code){
		$returnValue = false;
		$qry = "
			Select count(*) From DY_DELIVERY_TRACKING_URL
			Where delivery_is_del = N'N' And delivery_code = N'$delivery_code'
		";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return ($rst > 0) ? false : true;
	}

	/**
	 * 택배사 배송추적 등록
	 * @param $delivery_code
	 * @param $tracking_url
	 * @param $is_use
	 * @return int
	 */
	public function insertDeliveryTracking($delivery_code, $tracking_url, $is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_DELIVERY_TRACKING_URL
			(delivery_code, tracking_url, delivery_is_use, delivery_regip, last_member_idx)
			VALUES 
			(
			 N'$delivery_code'
			 , N'$tracking_url'
			 , N'$is_use'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 택배사 배송추적 수정
	 * @param $delivery_idx
	 * @param $tracking_url
	 * @param $is_use
	 * @return bool|resource
	 */
	public function updateDeliveryTracking($delivery_idx, $tracking_url, $is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_DELIVERY_TRACKING_URL
			Set tracking_url = N'$tracking_url'
				, delivery_is_use = N'$is_use'
				, delivery_moddate = NOW() 
				, delivery_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;

	}

	public function getDeliveryTrackingList($search_delivery_name = "")
	{
		$qrySearch = "";
		if(!empty($search_delivery_name)){
			$qrySearch = " And D.delivery_name like N'%".$search_delivery_name."%'";
		}

		$qry = "
			SELECT T.*, D.delivery_name, sort_num
			, CONVERT(T.delivery_regdate, CHAR(30)) AS delivery_regdate2
			From DY_DELIVERY_TRACKING_URL T
				Left Outer Join (
			        SELECT delivery_code, delivery_name, min(sort_num) as sort_num
				    FROM DY_DELIVERY_CODE
					Group by delivery_code, delivery_name
				) D On T.delivery_code = D.delivery_code
			Where T.delivery_is_del = N'N'
				$qrySearch
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}
}
?>