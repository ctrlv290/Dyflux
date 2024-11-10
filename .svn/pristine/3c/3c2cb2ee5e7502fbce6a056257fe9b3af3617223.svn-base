<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 관련 Process
 */

//Page Info
$pageMenuIdx = 134;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Bank = new Bank();
$C_Loan = new Loan();
$C_Report = new Report();

if($mode == "get_bank_data") {

	$period    = $_POST["period"];
	$tran_date = $_POST["tran_date"];
	$bank_type = $_POST["bank_type"];

	if ($period == "month") {
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	$_list = $C_Bank->getTodayBankTransactionDetail($period, $tran_date, $bank_type);

	$response["result"] = true;
	$response["data"]   = $_list;
}elseif($mode == "get_loan_data") {

	$period    = $_POST["period"];
	$tran_date = $_POST["tran_date"];

	if($period == "month"){
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	$_list = $C_Loan->getTodayLoanTransactionDetail($period, $tran_date);

	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_report_data"){

	$period     = $_POST["period"];
	$tran_date  = $_POST["tran_date"];
	$tran_type  = $_POST["tran_type"];
	$tran_inout = $_POST["tran_inout"];

	if($period == "month"){
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	$_list = $C_Report->getReportDataByDate($tran_date, $tran_type, $tran_inout, $period);

	//카드사용내역일 경우 월, 년 합계 구하기
	if($tran_type == "CARD_OUT"){
		$_month_sum = $C_Report->getReportDataByMonth($tran_date, $tran_type, $tran_inout);
	}

	$response["result"] = true;
	$response["data"] = $_list;
	$response["date"] = $tran_date;

	if($_month_sum) {
		$response["expand_data"] = $_month_sum;
	}
}elseif($mode == "get_report_account_data"){

	$period     = $_POST["period"];
	$tran_date  = $_POST["tran_date"];
	$tran_inout = $_POST["tran_inout"];

	if($period == "month"){
		$tran_date = date('Y-m-t', strtotime($tran_date));
	}

	//$_list = $C_Report->getReportSumDataByAccount($tran_date, $tran_inout, $period);
	$rst = $C_Report->getReportSumDataByAccount($tran_date, $tran_inout, $period);
	$_list = $rst["list"];
	$_cash_sum = $rst["cash_sum"];

	$response["result"] = true;
	$response["data"] = $_list;
	$response["cash_sum"] = $_cash_sum;
}elseif($mode == "delete_report_data"){
	$tran_idx = $_POST["tran_idx"];

	$tmp = $C_Report->deleteReportData($tran_idx);

	$response["result"] = true;
}

echo json_encode($response);
?>