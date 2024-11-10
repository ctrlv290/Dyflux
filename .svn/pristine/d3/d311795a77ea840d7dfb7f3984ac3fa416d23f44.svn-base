<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 계좌관리 관련 Process
 */

//Page Info
$pageMenuIdx = 240;
//Init
include_once "../_init_.php";

$mode = $_POST["mode"];

$C_Bank = new Bank();

if($mode == "add") {

	$bank_type = $_POST["bank_type"];
	$bank_name = $_POST["bank_name"];
	$bank_sort = $_POST["bank_sort"];
	$bank_is_use = $_POST["bank_is_use"];
    $bank_start_date = $_POST["bank_start_date"];
    $bank_use_n_date = $_POST["bank_use_n_date"];

	$idx = $C_Bank->insertBankAccount($bank_type, $bank_name, $bank_sort, $bank_is_use, $bank_start_date, $bank_use_n_date);

	$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("등록되었습니다.", $script);

}elseif($mode == "update"){
	$bank_idx = $_POST["bank_idx"];
	$bank_type = $_POST["bank_type"];
	$bank_name = $_POST["bank_name"];
	$bank_is_use = $_POST["bank_is_use"];
    $bank_use_n_date = $_POST["bank_use_n_date"];

	$idx = $C_Bank->updateBankAccount($bank_idx, $bank_type, $bank_name, $bank_is_use, $bank_use_n_date);

	$script = "
			try{
				opener.location.reload();
			}catch(e){}
		";
	put_msg_and_exec_script_and_close("수정되었습니다.", $script);
}