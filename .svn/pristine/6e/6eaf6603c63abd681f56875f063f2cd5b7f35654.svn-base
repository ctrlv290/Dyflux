<?php
/**
 * 벤더사 등급 관련 Class
 * User: woox
 * Date: 2018-11-10
 */

class VendorGrade extends DBConn
{
	/*
	 * 벤더사 등급 목록
	 * out : Array
	 */
	public function getVendorGradeList()
	{
		$qry = "
			Select * From DY_VENDOR_GRADE
			Order By vendor_grade_idx ASC
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/*
	 * 벤더사 등급 저장
	 * $args
	 * out : boolean
	 */
	public function saveVendorGrade($args)
	{
		$vendor_grade_idx = "";
		$vendor_grade_name = "";
		$vendor_grade_discount = "";
		$vendor_grade_etc = "";
		extract($args);

		$qry = "
			Update 
				DY_VENDOR_GRADE
			Set 
				vendor_grade_name = N'$vendor_grade_name'
				, vendor_grade_discount = N'$vendor_grade_discount'
				, vendor_grade_etc = N'$vendor_grade_etc'
			WHERE
				vendor_grade_idx = N'$vendor_grade_idx' 
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}
}
?>