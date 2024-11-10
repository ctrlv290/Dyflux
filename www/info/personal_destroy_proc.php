<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 개인정보파기 관련 Process
 */
//Page Info
$pageMenuIdx = 59;
//Init
include "../_init_.php";
//print_r($_POST);

$C_SiteInfo = new SiteInfo();

$mode                    = $_POST["mode"];

if($mode == "save") {

	$accept  = ($_POST["accept"] == "Y") ? "Y" : "N";
	$invoice = ($_POST["invoice"] == "Y") ? "Y" : "N";
	$shipped = ($_POST["shipped"] == "Y") ? "Y" : "N";

	$tmp = $C_SiteInfo->setPersonalDataDestroySetting($accept, $invoice, $shipped);

	put_msg_and_go("저장되었습니다.", "personal_destroy.php");

}elseif($mode == "sell_save"){
	foreach($_POST as $key => $val)
	{
		if($key != "mode")
		{
			$key_ary = explode("_", $key);
			$seller_idx = $key_ary[1];

			$tmp = $C_SiteInfo->saveSellerPersonalDataUse($seller_idx, $val);
		}
	}

	put_msg_and_go("저장되었습니다.", "personal_destroy.php");
}

?>