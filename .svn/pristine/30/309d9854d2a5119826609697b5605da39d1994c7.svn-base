<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 개인정보파기
 * 스크립트가 아닌 DB Stored Procedure 로 변경 SQL Agent 로 자동실행
 */

include_once "../_init_.php";

$C_SiteInfo = new SiteInfo();

$_info = $C_SiteInfo->getPersonalDataDestroySetting();

$accept = false;
$invoice = true;
$shipped = true;


if($_info["accept"] == "N" && $_info["invoice"] == "N" && $_info["shipped"] == "N") {

}else{

	$accept = ($_info["accept"] == "Y") ? true : false;
	$invoice = ($_info["invoice"] == "Y") ? true : false;
	$shipped = ($_info["shipped"] == "Y") ? true : false;


	//파기 기준일은 등록일시 기준
	$dt = date("Y-m-d 00:00:00", strtotime("-3 Month"));
	$qry = "
		Select
			top 10 
            order_idx, 
			order_name, order_tp_num, order_hp_num, order_addr1, order_addr2, order_zipcode, 
			receive_name, receive_tp_num, receive_hp_num, receive_addr1, receive_addr2, receive_zipcode
		From DY_ORDER
		Where order_regdate < '$dt' And personal_data_destroy = N'N'
	";

	$C_SiteInfo->db_connect();
	$_list = $C_SiteInfo->execSqlList($qry);
	$C_SiteInfo->db_close();

	foreach ($_list as $o)
	{
		$order_idx = $o["order_idx"];

		$order_name = mytory_asterisk($o["order_name"]);
		$order_tp_num = add_hyphen_and_asterisk($o["order_tp_num"]);
		$order_hp_num = add_hyphen_and_asterisk($o["order_hp_num"]);
		$order_addr1 = address_asterisk($o["order_addr1"]);
		$order_addr2 = str_repeat("*", mb_strlen($o["order_addr2"], 'utf-8'));

		$receive_name = mytory_asterisk($o["receive_name"]);
		$receive_tp_num = add_hyphen_and_asterisk($o["receive_tp_num"]);
		$receive_hp_num = add_hyphen_and_asterisk($o["receive_hp_num"]);
		$receive_addr1 = address_asterisk($o["receive_addr1"]);
		$receive_addr2 = str_repeat("*", mb_strlen($o["receive_addr2"], 'utf-8'));

		$qry = "
			Update DY_ORDER
			Set personal_data_destroy = N'Y', personal_data_destroy_date = getdate()
				, order_name = N'$order_name'
				, order_tp_num = N'$order_tp_num'
				, order_hp_num = N'$order_hp_num'
				, order_addr1 = N'$order_addr1'
				, order_addr2 = N'$order_addr2'
				, receive_name = N'$receive_name'
				, receive_tp_num = N'$receive_tp_num'
				, receive_hp_num = N'$receive_hp_num'
				, receive_addr1 = N'$receive_addr1'
				, receive_addr2 = N'$receive_addr2'
			Where order_idx = N'$order_idx'
		";

		//echo $qry . "<br>";
	}
}

?>