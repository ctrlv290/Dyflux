<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 택배사관리 관련 Process
 */
//Page Info
$pageMenuIdx = 53;
include "../_init_.php";

$C_Delivery = new Delivery();
//print_r($_POST);

$mode            = $_POST["mode"];
$delivery_idx    = $_POST["delivery_idx"];
$delivery_code   = $_POST["delivery_code"];
$tracking_url    = $_POST["tracking_url"];
$delivery_is_use = $_POST["delivery_is_use"];


if($mode == "add")
{
	//Check Dup
	if(!$C_Delivery->checkDupDeliveryCode($delivery_code))
	{
		put_msg_and_back("이미 등록된 택배사 코드입니다.");
		exit;
	}else{
		$C_Delivery->insertDeliveryTracking($delivery_code, $tracking_url, $delivery_is_use);

		$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
		put_msg_and_exec_script_and_close("등록되었습니다.", $script);
	}
}elseif($mode == "mod"){

	$C_Delivery->updateDeliveryTracking($delivery_idx, $tracking_url, $delivery_is_use);

	$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("수정되었습니다.", $script);
}elseif($mode == "lst"){

}

?>