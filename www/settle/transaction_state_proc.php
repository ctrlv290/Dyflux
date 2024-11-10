<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 관련 Process
 */

//Page Info
$pageMenuIdx = 134;
//Init
include_once "../_init_.php";

$mode = $_POST["mode"];

$C_SETTLE = new Settle();

if($mode == "etc_add") {

	//print_r2($_POST);

	$tran_type          = $_POST["tran_type"];
	$target_name_list   = $_POST["target_name"];
	$tran_date_list     = $_POST["tran_date"];
	$prev_amount_list   = $_POST["prev_amount"];
	$today_amount_list  = $_POST["today_amount"];
	$tran_amount_list   = $_POST["tran_amount"];
	$remain_amount_list = $_POST["remain_amount"];
	$tran_memo_list     = $_POST["tran_memo"];


	foreach ($target_name_list as $key => $val)
	{
		$target_name   = $val;
		$tran_date     = $tran_date_list[$key];
		$prev_amount   = $prev_amount_list[$key];
		$today_amount  = $today_amount_list[$key];
		$tran_amount   = $tran_amount_list[$key];
		$remain_amount = $remain_amount_list[$key];
		$tran_memo     = $tran_memo_list[$key];

		$prev_amount = (empty($prev_amount)) ? 0 : $prev_amount;
		$today_amount = (empty($today_amount)) ? 0 : $today_amount;
		$tran_amount = (empty($tran_amount)) ? 0 : $tran_amount;
		$remain_amount = (empty($remain_amount)) ? 0 : $remain_amount;
		$tran_memo = (empty($tran_memo)) ? "" : $tran_memo;

		if($val != "" && $tran_date != ""){

//			echo $target_name . "<br>";
//			echo $tran_date . "<br>";
//			echo $prev_amount . "<br>";
//			echo $today_amount . "<br>";
//			echo $tran_amount . "<br>";
//			echo $remain_amount . "<br>";
//			echo $tran_memo . "<br>";

			$tmp = $C_SETTLE->insertTransactionEtc($tran_type, $tran_date, $target_name, $prev_amount, $today_amount, $tran_amount, $remain_amount, $tran_memo);
		}
	}

	if($tran_type == "SALE_ETC") {
		$script = "
			try{
				opener.SettlePurchase.TransactionStateGetTableData('.sale-etc', 'get_sale_etc', 'SALE_ETC');
			}catch(e){}
		";
	}elseif($tran_type == "PURCHASE_ETC"){
		$script = "
			try{
				opener.SettlePurchase.TransactionStateGetTableData('.purchase-etc', 'get_purchase_etc', 'PURCHASE_ETC');
			}catch(e){}
		";
	}
	put_msg_and_exec_script_and_close("등록되었습니다.", $script);

	//
}
?>