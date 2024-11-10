<?php
/**
 * 공통 그룹 관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class ManageGroup extends DBConn
{
	/**
	 * 그룹 목록
	 * @param $manage_group_type :  그룹 타입 (공통코드 참조 : MANAGE_GROUP)
	 * @return array
	 */
	public function getManageGroupList($manage_group_type)
	{
		global $GL_Member;
		$qry = "
			Select * From DY_MANAGE_GROUP
			Where manage_group_is_del = N'N'
		";

		if($manage_group_type == "SELLER_ALL_GROUP") {
			$qry .= "
				And manage_group_type in ('SELLER_GROUP', 'VENDOR_GROUP')
			";
		}else{
			$qry .= "
		        And manage_group_type = N'$manage_group_type'
			";
		}

		if(!isDYLogin()) {
			if ($GL_Member["member_type"] == "VENDOR") {
				$qry .= "
		            And manage_group_idx in (Select manage_group_idx From DY_SELLER Where seller_idx = N'".$GL_Member["member_idx"]."')
				";
			}
		}

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
     * 그룹 Insert
	 * $manage_group_type : 그룹 타입 [참조코드 : MANAGEE_GROUP]
	 * $manage_group_name : 그룹명
	 * out : int (Insert IDENTITY)
     */
	public function insertManageGroup($manage_group_type, $manage_group_name){
		$qry = "
			Insert Into DY_MANAGE_GROUP
			(manage_group_type, manage_group_name, manage_group_regip)
			VALUES 
			(
				N'".$manage_group_type."',
				N'".$manage_group_name."',
				N'".$_SERVER["REMOTE_ADDR"]."'
			)
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 그룹 Update
	 * $args :
	 * out : boolean
     */
	public function updateManageGroup($args)
	{
		$manage_group_idx = "";
		$manage_group_name = "";
		extract($args);
		$qry = "
			Update DY_MANAGE_GROUP
			Set
				manage_group_name = N'$manage_group_name'
				, manage_group_moddate = NOW()
				, manage_group_modip = N'".$_SERVER["REMOTE_ADDR"]."'
			Where 
				manage_group_idx = N'$manage_group_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 그룹 Delete
	 * $manage_group_idx : 그룹 테이블 IDX
	 * out : boolean
	 */
	public function deleteManageGroup($manage_group_idx)
	{
		$qry = "
			Delete From DY_MANAGE_GROUP
			Where 
				manage_group_idx = N'$manage_group_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 그룹 멤버 반환 (사용가능한 멤버만 is_use = Y )
	 * $manage_group_type : 그룹 타입 (공통코드 참조 : MANAGE_GROUP)
	 * $manage_group_idx : 그룹 IDX (그룹 IDX 가 없으면 전체 반환)
	 * out : Array
	 */
	public function getManageGroupMemberList($manage_group_type, $manage_group_idx)
	{
		global $GL_Member;
		$qry = "";
		if($manage_group_type == "SELLER_GROUP" || $manage_group_type == "SELLER_ALL_GROUP"){
			$qry = "
				Select S.seller_idx as idx, S.seller_name as name
				From DY_SELLER S
				Where S.seller_is_use = N'Y' And S.seller_is_del = N'N'
			";

			if(!isDYLogin()) {
				if ($GL_Member["member_type"] == "VENDOR") {
					$qry .= "
		                And seller_idx = N'".$GL_Member["member_idx"]."'
					";
				}
			}

		}elseif($manage_group_type == "VENDOR_GROUP") {
			$qry = "
				Select M.idx as idx, S.vendor_name as name
				From DY_MEMBER M
					Left Outer Join DY_MEMBER_VENDOR S On M.idx = S.member_idx
				Where M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'VENDOR'
			";

			if(!isDYLogin()) {
				if ($GL_Member["member_type"] == "VENDOR") {
					$qry .= "
		                And member_idx = N'".$GL_Member["member_idx"]."'
					";
				}
			}

		}elseif($manage_group_type == "VENDOR_CHARGE_GROUP") {
			$qry = "
				Select M.idx as idx, S.vendor_name as name
				From DY_MEMBER M
					Left Outer Join DY_MEMBER_VENDOR S On M.idx = S.member_idx
				Where M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'VENDOR' And S.vendor_status = N'VENDOR_APPLY' And S.vendor_use_charge = N'Y'
			";

			if(!isDYLogin()) {
				if ($GL_Member["member_type"] == "VENDOR") {
					$qry .= "
		                And member_idx = N'".$GL_Member["member_idx"]."'
					";
				}
			}

		}elseif($manage_group_type == "SUPPLIER_GROUP") {
			$qry = "
				Select M.idx as idx, S.supplier_name as name
				From DY_MEMBER M
					Left Outer Join DY_MEMBER_SUPPLIER S On M.idx = S.member_idx
				Where M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'SUPPLIER'
			";
		}

		if($manage_group_idx != "0" && $manage_group_idx != "")
		{
			$qry .= " And S.manage_group_idx = N'$manage_group_idx' ";
		}

		$qry .= " Order by name ASC ";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}
}
?>