<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 계좌관리 관련 Process
 */

//Page Info
$pageMenuIdx = 288;
//Init
include_once "../_init_.php";

$mode = $_POST["mode"];

$C_Loan = new Loan();

if($mode == "add") {

	$loan_name   = $_POST["loan_name"];
	$loan_amount = str_replace(",", "", $_POST["loan_amount"]);
	$loan_detail = $_POST["loan_detail"];
	$loan_sort   = $_POST["loan_sort"];
	$loan_is_use = $_POST["loan_is_use"];
    $loan_start_date = $_POST["loan_start_date"];
    $loan_use_n_date = $_POST["loan_use_n_date"];

	$idx = $C_Loan->insertLoanAccount($loan_name, $loan_amount, $loan_detail, $loan_sort, $loan_is_use, $loan_start_date, $loan_use_n_date);

	$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("등록되었습니다.", $script);

}elseif($mode == "update"){
	$loan_idx    = $_POST["loan_idx"];
	$loan_name   = $_POST["loan_name"];
	$loan_amount = str_replace(",", "", $_POST["loan_amount"]);
	$loan_detail = $_POST["loan_detail"];
	$loan_is_use = $_POST["loan_is_use"];
    $loan_use_n_date = $_POST["loan_use_n_date"];

	$idx = $C_Loan->updateLoanAccount($loan_idx, $loan_name, $loan_amount, $loan_detail, $loan_is_use, $loan_use_n_date);

	$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("수정되었습니다.", $script);
}