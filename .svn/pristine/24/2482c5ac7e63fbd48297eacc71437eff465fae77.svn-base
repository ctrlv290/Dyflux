<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자금일보 관련 Process
 */

//Page Info
$pageMenuIdx = 240;
//Init
include_once "../_init_.php";


$mode = $_POST["mode"];

$C_Vendor = new Vendor();

if($mode == "add") {

	$member_idx_list    = $_POST["member_idx"];
	$charge_date_list   = $_POST["charge_date"];
	$charge_amount_list = $_POST["charge_amount"];
	$charge_memo_list   = $_POST["charge_memo"];

	foreach($member_idx_list as $key => $member_idx)
	{
		$charge_date   = $charge_date_list[$key];
		$charge_amount = str_replace(",", "", $charge_amount_list[$key]);
		$charge_memo   = $charge_memo_list[$key];

		if($member_idx && validateDate($charge_date, 'Y-m-d') && $charge_amount)
		{
			$rst = $C_Vendor->insertVendorCharge($member_idx, $charge_date, $charge_amount, $charge_memo);
		}
	}


	$script = "
			try{
				opener.SettleCharge.VendorChargeGridReload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("저장되었습니다.", $script);

}
?>