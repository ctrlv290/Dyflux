<?php
/**
 * 상품정보제공고시 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class ProductNotice extends Dbconn
{
	/*
	 * 상품정보제공고시 목록
	 * out : Array
	 */
	public function getProductNoticeList()
	{
		$qry = "
			Select * From DY_PRODUCT_NOTICE
			Where product_notice_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
     * 상품정보제공고시 Insert
	 * $args
	 * out : int (Insert IDENTITY)
     */
	public function insertProductNotice($args){

		global $GL_Member;

		$product_notice_title = "";
		$product_notice_1_use = "";
		$product_notice_1_title = "";
		$product_notice_2_use = "";
		$product_notice_2_title = "";
		$product_notice_3_use = "";
		$product_notice_3_title = "";
		$product_notice_4_use = "";
		$product_notice_4_title = "";
		$product_notice_5_use = "";
		$product_notice_5_title = "";
		$product_notice_6_use = "";
		$product_notice_6_title = "";
		$product_notice_7_use = "";
		$product_notice_7_title = "";
		$product_notice_8_use = "";
		$product_notice_8_title = "";
		$product_notice_9_use = "";
		$product_notice_9_title = "";
		$product_notice_10_use = "";
		$product_notice_10_title = "";
		$product_notice_11_use = "";
		$product_notice_11_title = "";
		$product_notice_12_use = "";
		$product_notice_12_title = "";
		$product_notice_13_use = "";
		$product_notice_13_title = "";
		$product_notice_14_use = "";
		$product_notice_14_title = "";
		$product_notice_15_use = "";
		$product_notice_15_title = "";
		$product_notice_16_use = "";
		$product_notice_16_title = "";
		$product_notice_17_use = "";
		$product_notice_17_title = "";
		$product_notice_18_use = "";
		$product_notice_18_title = "";
		$product_notice_19_use = "";
		$product_notice_19_title = "";
		$product_notice_20_use = "";
		$product_notice_20_title = "";

		extract($args);
		$qry = "
			Insert Into DY_PRODUCT_NOTICE
			(
				product_notice_title
				, product_notice_1_use
				, product_notice_1_title
				, product_notice_2_use
				, product_notice_2_title
				, product_notice_3_use
				, product_notice_3_title
				, product_notice_4_use
				, product_notice_4_title
				, product_notice_5_use
				, product_notice_5_title
				, product_notice_6_use
				, product_notice_6_title
				, product_notice_7_use
				, product_notice_7_title
				, product_notice_8_use
				, product_notice_8_title
				, product_notice_9_use
				, product_notice_9_title
				, product_notice_10_use
				, product_notice_10_title
				, product_notice_11_use
				, product_notice_11_title
				, product_notice_12_use
				, product_notice_12_title
				, product_notice_13_use
				, product_notice_13_title
				, product_notice_14_use
				, product_notice_14_title
				, product_notice_15_use
				, product_notice_15_title
				, product_notice_16_use
				, product_notice_16_title
				, product_notice_17_use
				, product_notice_17_title
				, product_notice_18_use
				, product_notice_18_title
				, product_notice_19_use
				, product_notice_19_title
				, product_notice_20_use
				, product_notice_20_title
				, product_notice_regip
				, last_member_idx
			)
			VALUES 
			(
				N'$product_notice_title',
				N'$product_notice_1_use',
				N'$product_notice_1_title',
				N'$product_notice_2_use',
				N'$product_notice_2_title',
				N'$product_notice_3_use',
				N'$product_notice_3_title',
				N'$product_notice_4_use',
				N'$product_notice_4_title',
				N'$product_notice_5_use',
				N'$product_notice_5_title',
				N'$product_notice_6_use',
				N'$product_notice_6_title',
				N'$product_notice_7_use',
				N'$product_notice_7_title',
				N'$product_notice_8_use',
				N'$product_notice_8_title',
				N'$product_notice_9_use',
				N'$product_notice_9_title',
				N'$product_notice_10_use',
				N'$product_notice_10_title',
				N'$product_notice_11_use',
				N'$product_notice_11_title',
				N'$product_notice_12_use',
				N'$product_notice_12_title',
				N'$product_notice_13_use',
				N'$product_notice_13_title',
				N'$product_notice_14_use',
				N'$product_notice_14_title',
				N'$product_notice_15_use',
				N'$product_notice_15_title',
				N'$product_notice_16_use',
				N'$product_notice_16_title',
				N'$product_notice_17_use',
				N'$product_notice_17_title',
				N'$product_notice_18_use',
				N'$product_notice_18_title',
				N'$product_notice_19_use',
				N'$product_notice_19_title',
				N'$product_notice_20_use',
				N'$product_notice_20_title',
				N'".$_SERVER["REMOTE_ADDR"]."',
				N'".$GL_Member["member_idx"]."'
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
	public function updateProductNotice($args)
	{

		global $GL_Member;
		$product_notice_idx = "";
		$product_notice_title = "";
		$product_notice_1_use = "";
		$product_notice_1_title = "";
		$product_notice_2_use = "";
		$product_notice_2_title = "";
		$product_notice_3_use = "";
		$product_notice_3_title = "";
		$product_notice_4_use = "";
		$product_notice_4_title = "";
		$product_notice_5_use = "";
		$product_notice_5_title = "";
		$product_notice_6_use = "";
		$product_notice_6_title = "";
		$product_notice_7_use = "";
		$product_notice_7_title = "";
		$product_notice_8_use = "";
		$product_notice_8_title = "";
		$product_notice_9_use = "";
		$product_notice_9_title = "";
		$product_notice_10_use = "";
		$product_notice_10_title = "";
		$product_notice_11_use = "";
		$product_notice_11_title = "";
		$product_notice_12_use = "";
		$product_notice_12_title = "";
		$product_notice_13_use = "";
		$product_notice_13_title = "";
		$product_notice_14_use = "";
		$product_notice_14_title = "";
		$product_notice_15_use = "";
		$product_notice_15_title = "";
		$product_notice_16_use = "";
		$product_notice_16_title = "";
		$product_notice_17_use = "";
		$product_notice_17_title = "";
		$product_notice_18_use = "";
		$product_notice_18_title = "";
		$product_notice_19_use = "";
		$product_notice_19_title = "";
		$product_notice_20_use = "";
		$product_notice_20_title = "";

		extract($args);
		$qry = "
			Update DY_PRODUCT_NOTICE
			SET
				product_notice_title = N'$product_notice_title'
				, product_notice_1_use = N'$product_notice_1_use'
				, product_notice_1_title = N'$product_notice_1_title'
				, product_notice_2_use = N'$product_notice_2_use'
				, product_notice_2_title = N'$product_notice_2_title'
				, product_notice_3_use = N'$product_notice_3_use'
				, product_notice_3_title = N'$product_notice_3_title'
				, product_notice_4_use = N'$product_notice_4_use'
				, product_notice_4_title = N'$product_notice_4_title'
				, product_notice_5_use = N'$product_notice_5_use'
				, product_notice_5_title = N'$product_notice_5_title'
				, product_notice_6_use = N'$product_notice_6_use'
				, product_notice_6_title = N'$product_notice_6_title'
				, product_notice_7_use = N'$product_notice_7_use'
				, product_notice_7_title = N'$product_notice_7_title'
				, product_notice_8_use = N'$product_notice_8_use'
				, product_notice_8_title = N'$product_notice_8_title'
				, product_notice_9_use = N'$product_notice_9_use'
				, product_notice_9_title = N'$product_notice_9_title'
				, product_notice_10_use = N'$product_notice_10_use'
				, product_notice_10_title = N'$product_notice_10_title'
				, product_notice_11_use = N'$product_notice_11_use'
				, product_notice_11_title = N'$product_notice_11_title'
				, product_notice_12_use = N'$product_notice_12_use'
				, product_notice_12_title = N'$product_notice_12_title'
				, product_notice_13_use = N'$product_notice_13_use'
				, product_notice_13_title = N'$product_notice_13_title'
				, product_notice_14_use = N'$product_notice_14_use'
				, product_notice_14_title = N'$product_notice_14_title'
				, product_notice_15_use = N'$product_notice_15_use'
				, product_notice_15_title = N'$product_notice_15_title'
				, product_notice_16_use = N'$product_notice_16_use'
				, product_notice_16_title = N'$product_notice_16_title'
				, product_notice_17_use = N'$product_notice_17_use'
				, product_notice_17_title = N'$product_notice_17_title'
				, product_notice_18_use = N'$product_notice_18_use'
				, product_notice_18_title = N'$product_notice_18_title'
				, product_notice_19_use = N'$product_notice_19_use'
				, product_notice_19_title = N'$product_notice_19_title'
				, product_notice_20_use = N'$product_notice_20_use'
				, product_notice_20_title = N'$product_notice_20_title'
				, product_notice_moddate = getdate()
				, product_notice_modip= N'{$_SERVER["REMOTE_ADDR"]}'
				, last_member_idx = N'{$GL_Member["member_idx"]}'
			Where product_notice_idx = N'$product_notice_idx'
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
	public function deleteProductNotice($manage_group_idx)
	{
		$qry = "
			Delete From DY_PRODUCT_NOTICE
			Where 
				manage_group_idx = N'$manage_group_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 상품정보제공고시 정보 반환
	 * $product_notice_idx : DY_PRODUCT_NOTICE 테이블 IDX
	 * out : array (ONE ROW)
	 */
	public function getProductNoticeData($product_notice_idx)
	{
		$qry = "
			Select * 
			From  DY_PRODUCT_NOTICE 
			Where product_notice_idx = N'$product_notice_idx' 
				And product_notice_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 상품정보제공고시 리스트 반환
	 * out : array (ONE ROW)
	 */
	public function getProductNoticeTitleList()
	{
		$qry = "
			Select product_notice_idx, product_notice_title
			From  DY_PRODUCT_NOTICE 
			Where product_notice_is_del = N'N'
			Order by product_notice_idx asc
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}
}
?>