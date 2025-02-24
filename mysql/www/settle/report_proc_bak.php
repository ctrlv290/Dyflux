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

$C_Bank = new Bank();
$C_Loan = new Loan();
$C_Report = new Report();

if($mode == "save_bank_transaction") {

	$tran_date      = $_POST["tran_date"];
	$bank_idx_list  = $_POST["bank_idx"];
	$tran_in_list   = $_POST["tran_in"];
	$tran_out_list  = $_POST["tran_out"];
	$tran_memo_list = $_POST["tran_memo"];

	foreach ($bank_idx_list as $key => $bank_idx) {
		$tran_in   = str_replace(",", "", $tran_in_list[$key]);
		$tran_out  = str_replace(",", "", $tran_out_list[$key]);
		$tran_memo = $tran_memo_list[$key];

		$rst = $C_Bank->saveTodayBankTransaction($tran_date, $bank_idx, $tran_in, $tran_out, $tran_memo);
	}


	$script = "
			try{
				opener.SettleReport.ReportBankReload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("저장되었습니다.", $script);

}elseif($mode == "save_loan_transaction") {

	$tran_date      = $_POST["tran_date"];
	$loan_idx_list  = $_POST["loan_idx"];
	$tran_in_list   = $_POST["tran_in"];
	$tran_out_list  = $_POST["tran_out"];
	$tran_memo_list = $_POST["tran_memo"];

	foreach($loan_idx_list as $key => $loan_idx)
	{
		$tran_in   = str_replace(",", "", $tran_in_list[$key]);
		$tran_out  = str_replace(",", "", $tran_out_list[$key]);
		$tran_memo = $tran_memo_list[$key];

		$rst = $C_Loan->saveTodayLoanTransaction($tran_date, $loan_idx, $tran_in, $tran_out, $tran_memo);
	}


	$script = "
			try{
				opener.SettleReport.ReportBankReload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("저장되었습니다.", $script);

}elseif($mode == "add" || $mode == "update"){

	$tran_date_list    = $_POST["tran_date"];
	$tran_type         = $_POST["tran_type"];
	$tran_inout        = $_POST["tran_inout"];
	$account_idx_list  = $_POST["account_idx"];
	$target_idx_list   = $_POST["target_idx"];
    $target_cal_list   = $_POST["target_cal"];
	$tran_memo_list    = $_POST["tran_memo"];
	$tran_amount_list  = $_POST["tran_amount"];
	$tran_idx_list     = $_POST["tran_idx"];
	$tran_user_list    = $_POST["tran_user"];
	$tran_card_no_list = $_POST["tran_card_no"];
	$tran_purpose_list = $_POST["tran_purpose"];
	$tran_is_sync      = $_POST["tran_is_sync"] == "Y" ? true : false; //계좌간이체 지출에도 등록

	foreach($account_idx_list as $key => $account_idx){

		$tran_memo = $tran_memo_list[$key];
        $target_cal = str_replace(",", "", $target_cal_list[$key]);
		$tran_amount = str_replace(",", "", $tran_amount_list[$key]);

		$tran_date = str_replace(",", "", $tran_date_list[$key]);

		if(!$account_idx || $tran_amount === "") continue;
		if($target_cal) {
            $tran_amount = $target_cal * $tran_amount;
        }
		$target_idx = "";
		if($tran_type == "BANK_CUSTOMER_IN" || $tran_type == "BANK_CUSTOMER_OUT"){
			$target_idx = $target_idx_list[$key];
			if(!$target_idx) continue;
		}

		$tran_user = "";
		$tran_card_no = "";
		$tran_purpose = "";
		if($tran_type == "CARD_OUT"){
			$tran_user = $tran_user_list[$key];
			$tran_card_no = $tran_card_no_list[$key];
			$tran_purpose = $tran_purpose_list[$key];

			if(!$tran_user || !$tran_card_no || !$tran_purpose) continue;
		}

		$tran_idx = "";
		if($mode == "update"){
			$tran_idx = $tran_idx_list[$key];
		}

		$rst_save = $C_Report->saveReportData($tran_idx, $tran_date, $tran_type, $tran_inout, $account_idx, $tran_memo, $tran_amount, $target_idx, $tran_user, $tran_card_no, $tran_purpose, $tran_is_sync);

		if ($tran_is_sync && $rst_save) {
			$sync_enabled = true;
			if ($mode == "update") {
				$sync_idx = $C_Report->getReportSynchronizedIdx($tran_idx);
				if (! $sync_idx) {
					$sync_enabled = false;
				} else {
					$tran_idx = $sync_idx;
				}
			}

			if ($sync_enabled) {
				if ($tran_type == "TRANSFER_IN") {
					$rst_sec = $C_Report->saveReportData($tran_idx, $tran_date, "TRANSFER_OUT", "OUT", "7", $tran_memo, $tran_amount, $target_idx, $tran_user, $tran_card_no, $tran_purpose, $tran_is_sync , $rst_save);
				} elseif ($tran_type == "TRANSFER_OUT") {
					$rst_sec = $C_Report->saveReportData($tran_idx, $tran_date, "TRANSFER_IN", "IN", "75", $tran_memo, $tran_amount, $target_idx, $tran_user, $tran_card_no, $tran_purpose, $tran_is_sync, $rst_save);
				}
			}
		}
	}

	$script = "
			try{
				opener.SettleReport.ReportListReload('$tran_type', '$tran_inout');
			}catch(e){}
		";

	if($tran_type == "BANK_CUSTOMER_OUT") {
		$script .= "
			try{
				opener.SettleLedge.PurchaseLedgeSearch();
			}catch(e){}
		";
	}elseif($tran_type == "BANK_CUSTOMER_IN"){
		$script .= "
			try{
				opener.SettleLedge.SaleLedgeSearch();
			}catch(e){}
		";
	}elseif($tran_type == "TRANSFER_IN" && $tran_is_sync){
		$script .= "
			try{
				opener.SettleReport.ReportListReload('TRANSFER_OUT', 'OUT');
			}catch(e){}
		";
	}elseif($tran_type == "TRANSFER_OUT" && $tran_is_sync){
		$script .= "
			try{
				opener.SettleReport.ReportListReload('TRANSFER_IN', 'IN');
			}catch(e){}
		";
	}

	put_msg_and_exec_script_and_close("저장 되었습니다.", $script);
}