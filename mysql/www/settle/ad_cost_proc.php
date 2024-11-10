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

$C_Settle = new Settle();

if($mode == "add_charge" || $mode == "add_use") {

	$ad_inout = 1;
	if($mode == "add_use"){
		$ad_inout = -1;
	}

	$seller_idx_list = $_POST["seller_idx"];
	$ad_date_list   = $_POST["ad_date"];
	$ad_amount_list = $_POST["ad_amount"];
	$ad_product_name_list = $_POST["ad_product_name"];
	$ad_memo_list   = $_POST["ad_memo"];

	foreach($seller_idx_list as $key => $seller_idx)
	{
		$ad_date         = $ad_date_list[$key];
		$ad_amount       = str_replace(",", "", $ad_amount_list[$key]);
		$ad_product_name = $ad_product_name_list[$key];
		$ad_memo         = $ad_memo_list[$key];

		if($seller_idx && validateDate($ad_date, 'Y-m-d') && $ad_amount)
		{
			$rst = $C_Settle->insertAdCost($seller_idx, $ad_date, $ad_inout, $ad_amount, $ad_product_name, $ad_memo);
		}
	}


	$script = "
			try{
				opener.SettleCharge.AdCostGridReload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("저장되었습니다.", $script);

}
?>