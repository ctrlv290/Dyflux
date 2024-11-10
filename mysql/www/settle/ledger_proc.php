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

if($mode == "adjust_add") {

	$ledger_type = $_POST["ledger_type"];
	$ledger_add_type = $_POST["ledger_add_type"];

	$target_idx_list           = $_POST["target_idx"];
	$ledger_date_list          = $_POST["ledger_date"];
	$ledger_title_list         = $_POST["ledger_title"];
	$ledger_amount_list        = $_POST["ledger_amount"];
	$ledger_memo_list          = $_POST["ledger_memo"];


	foreach ($target_idx_list as $key => $val)
	{
		$target_idx           = $val;
		$ledger_date          = $ledger_date_list[$key];
		$ledger_title         = $ledger_title_list[$key];
		$ledger_amount        = str_replace(",", "", $ledger_amount_list[$key]);
		$ledger_memo          = $ledger_memo_list[$key];

		$ledger_adjust_amount = (empty($ledger_adjust_amount)) ? 0 : $ledger_adjust_amount;

		if($val != "" && $ledger_date != "" && $ledger_title != "" && ($ledger_amount > 0 || $ledger_amount < 0) ){
			$ledger_adjust_amount = 0;
			$ledger_tran_amount = 0;
			$ledger_refund_amount = 0;

			if($ledger_add_type == "ADJUST"){
				$ledger_adjust_amount = $ledger_amount;
			}elseif($ledger_add_type == "TRAN"){
				$ledger_tran_amount = $ledger_amount;
			}elseif($ledger_add_type == "REFUND"){
				$ledger_refund_amount = $ledger_amount;
			}

			//insertLedgerDetail($ledger_type, $ledger_add_type, $target_idx, $ledger_date, $ledger_title, $ledger_adjust_amount, $ledger_tran_amount, $ledger_refund_amount, $ledger_memo)
			$tmp = $C_SETTLE->insertLedgerDetail($ledger_type, $ledger_add_type, $target_idx, $ledger_date, $ledger_title, $ledger_adjust_amount, $ledger_tran_amount, $ledger_refund_amount, $ledger_memo);
		}
	}

	if($ledger_type == "LEDGER_PURCHASE") {
		$script = "
			try{
				opener.SettleLedge.PurchaseLedgeSearch();
			}catch(e){}
		";
	}elseif($ledger_type == "LEDGER_SALE"){
		$script = "
			try{
				opener.SettleLedge.SaleLedgeSearch();
			}catch(e){}
		";
	}
	put_msg_and_exec_script_and_close("등록되었습니다.", $script);

	//
}
?>