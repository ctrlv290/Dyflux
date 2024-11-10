<?php
/**
 * 사이트 정보관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */

class SiteInfo extends Dbconn
{
	public function getSiteInfo()
	{
		$qry = "
			Select * From DY_SITE_INFO
			Where idx = 1
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	public function getOfficerList()
	{
		$returnValue = array();
		$qry = "
			Select
				officer1_name, officer1_tel, officer1_mobile, officer1_email
				, officer2_name, officer2_tel, officer2_mobile, officer2_email
				, officer3_name, officer3_tel, officer3_mobile, officer3_email
				, officer4_name, officer4_tel, officer4_mobile, officer4_email
			From DY_SITE_INFO
			Where idx = 1
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		if($rst)
		{

			if(trim($rst["officer1_name"]) != ""){
				$returnValue[] = array(
					"no" => 1,
					"name"   => $rst["officer1_name"],
					"tel"    => $rst["officer1_tel"],
					"mobile" => $rst["officer1_mobile"],
					"email"  => $rst["officer1_email"]
				);
			}
			if(trim($rst["officer2_name"]) != ""){
				$returnValue[] = array(
					"no" => 2,
					"name"   => $rst["officer2_name"],
					"tel"    => $rst["officer2_tel"],
					"mobile" => $rst["officer2_mobile"],
					"email"  => $rst["officer2_email"]
				);
			}
			if(trim($rst["officer3_name"]) != ""){
				$returnValue[] = array(
					"no" => 3,
					"name"   => $rst["officer3_name"],
					"tel"    => $rst["officer3_tel"],
					"mobile" => $rst["officer3_mobile"],
					"email"  => $rst["officer3_email"]
				);
			}
			if(trim($rst["officer4_name"]) != ""){
				$returnValue[] = array(
					"no" => 4,
					"name"   => $rst["officer4_name"],
					"tel"    => $rst["officer4_tel"],
					"mobile" => $rst["officer4_mobile"],
					"email"  => $rst["officer4_email"]
				);
			}
		}

		return $returnValue;
	}

	function updateSiteInfo($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$site_name       = "";
		$ceo_name        = "";
		$license_no      = "";
		$zipcode         = "";
		$addr1           = "";
		$addr2           = "";
		$fax             = "";
		$email_default   = "";
		$email_account   = "";
		$email_order     = "";
		$invoice_name    = "";
		$invoice_tel     = "";
		$invoice_addr    = "";
		$officer1_name   = "";
		$officer1_tel    = "";
		$officer1_mobile = "";
		$officer1_email  = "";
		$officer2_name   = "";
		$officer2_tel    = "";
		$officer2_mobile = "";
		$officer2_email  = "";
		$officer3_name   = "";
		$officer3_tel    = "";
		$officer3_mobile = "";
		$officer3_email  = "";
		$officer4_name   = "";
		$officer4_tel    = "";
		$officer4_mobile = "";
		$officer4_email  = "";
		$officer5_name   = "";
		$officer5_tel    = "";
		$officer5_mobile = "";
		$officer5_email  = "";
		$md              = "";
		$etc             = "";

		extract($args);
		$qry = "
			UPDATE DY_SITE_INFO SET 
			site_name = '$site_name' 
			, ceo_name = '$ceo_name' 
			, license_no = '$license_no' 
			, zipcode = '$zipcode' 
			, addr1 = '$addr1' 
			, addr2 = '$addr2' 
			, fax = '$fax' 
			, email_default = '$email_default' 
			, email_account = '$email_account' 
			, email_order = '$email_order' 			
			, invoice_name = '$invoice_name'
			, invoice_tel = '$invoice_tel'
			, invoice_addr = '$invoice_addr'			
			, officer1_name = '$officer1_name' 
			, officer1_tel = '$officer1_tel' 
			, officer1_mobile = '$officer1_mobile' 
			, officer1_email = '$officer1_email' 
			, officer2_name = '$officer2_name' 
			, officer2_tel = '$officer2_tel' 
			, officer2_mobile = '$officer2_mobile' 
			, officer2_email = '$officer2_email' 
			, officer3_name = '$officer3_name' 
			, officer3_tel = '$officer3_tel' 
			, officer3_mobile = '$officer3_mobile' 
			, officer3_email = '$officer3_email' 
			, officer4_name = '$officer4_name' 
			, officer4_tel = '$officer4_tel' 
			, officer4_mobile = '$officer4_mobile' 
			, officer4_email = '$officer4_email' 
			, officer5_name = '$officer5_name' 
			, officer5_tel = '$officer5_tel' 
			, officer5_mobile = '$officer5_mobile' 
			, officer5_email = '$officer5_email' 
			, md = '$md' 
			, etc = '$etc' 
			, moddate = getdate()
			, modip = '$modip' 
			, last_member_idx = '$last_member_idx' 
			WHERE  idx = 1";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 개인정보 파기 설정 불러오기
	 * @return array|false|null
	 */
	public function getPersonalDataDestroySetting()
	{
		$qry = "
			Select * From DY_PERSONAL_DATA_DESTROY Where idx = 1
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 개인정보 파기 설정 저장
	 * @param $accept
	 * @param $invoice
	 * @param $shipped
	 * @return bool|resource
	 */
	public function setPersonalDataDestroySetting($accept, $invoice, $shipped)
	{
		$qry = "
			Update DY_PERSONAL_DATA_DESTROY
				Set accept = N'$accept', invoice = N'$invoice', shipped = N'$shipped'
				Where idx = 1
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 개인정보 파기 로그 반환
	 * @return array
	 */
	public function getPersonalDataDestroyLog()
	{
		$qry = "
			Select Top 5 * From DY_PERSONAL_DATA_DESTROY_LOG
			Order by idx DESC
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 판매처 개인정보 파기 대상 여부 변경
	 * @param $seller_idx
	 * @param $val
	 * @return bool|resource
	 */
	public function saveSellerPersonalDataUse($seller_idx, $val)
	{
		$qry = "
			Update DY_SELLER
			Set seller_use_personal_destroy = N'$val'
			Where seller_idx = $seller_idx
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}
}
?>