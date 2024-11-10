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

$C_SETTLE = new Settle();

if($mode == "get_sale_credit") {

	//매출현황(외상매출금)

	$period_type = $_POST["period_type"];
	$date        = $_POST["date"];

	if($period_type == "month"){
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
	}

	$_list              = $C_SETTLE->getTransactionStateSaleCredit($period_type, $date, "N");
	$response["result"] = true;
	$response["data"]   = $_list;
}elseif($mode == "get_sale_prepay") {

	//매출현황(선입금)
	$period_type = $_POST["period_type"];
	$date        = $_POST["date"];

	if($period_type == "month"){
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
	}

	$_list              = $C_SETTLE->getTransactionStateSaleCredit($period_type, $date, "Y",true);
	$response["result"] = true;
	$response["data"]   = $_list;
}elseif($mode == "get_sale_prepay_n") {

    //매출현황(일반거래처)
    $period_type = $_POST["period_type"];
    $date        = $_POST["date"];

    if($period_type == "month"){
        $date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
        $date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
    }

    $_list              = $C_SETTLE->getTransactionStateSaleCredit($period_type, $date, "N", true);
    $response["result"] = true;
    $response["data"]   = $_list;

}elseif($mode == "get_purchase_credit_type_m") {

	//매입현황(외상매입금)

	$period_type = $_POST["period_type"];
    $supplier_payment_type = "MONTH";
	$date        = $_POST["date"];

	if($period_type == "month"){
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
	}

	$_list              = $C_SETTLE->getTransactionStatePurchaseCredit($period_type, $date, "N",$supplier_payment_type);
	$response["result"] = true;
	$response["data"]   = $_list;
}elseif($mode == "get_purchase_credit_type_d") {

    //매입현황(외상매입금)

    $period_type = $_POST["period_type"];
    $supplier_payment_type = "DAY";
    $date        = $_POST["date"];

    if($period_type == "month"){
        $date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
        $date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
    }

    $_list              = $C_SETTLE->getTransactionStatePurchaseCredit($period_type, $date, "N", $supplier_payment_type);
    $response["result"] = true;
    $response["data"]   = $_list;
}elseif($mode == "get_purchase_prepay") {

	//매입현황(선입금)

	$period_type = $_POST["period_type"];
	$date        = $_POST["date"];
    $supplier_payment_type = "";

	if($period_type == "month"){
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-01";
		$date = $_POST["date_year"] . "-" . $_POST["date_month"] . "-" . date('t', strtotime($date));
	}

	$_list              = $C_SETTLE->getTransactionStatePurchaseCredit($period_type, $date, "Y", $supplier_payment_type);
	$response["result"] = true;
	$response["data"]   = $_list;
}elseif($mode == "save_transaction") {

	$tran_type   = $_POST["tran_type"];
	$tran_idx    = $_POST["tran_idx"];
	$date        = $_POST["date"];
	$target_idx  = $_POST["target_idx"];
	$tran_amount = $_POST["tran_amount"];
	$tran_memo   = $_POST["tran_memo"];

	//$rst = $C_SETTLE->saveTransactionModify($tran_type, $date, $target_idx, $tran_amount, $tran_memo);
	$rst = $C_SETTLE->saveTransactionMemoModify($tran_type, $date, $target_idx, $tran_memo);

	if($rst){
		$response["result"] = true;
	}
}elseif($mode == "save_transaction_table") {


	$tran_type = $_POST["tran_type"];
	$date      = $_POST["date"];
	$tran_list = $_POST["tran_list"];


	foreach ($tran_list as $tran) {
		$target_idx  = $tran["target_idx"];
		$tran_amount = $tran["tran_amount"];
		$rst         = $C_SETTLE->saveTransactionModify($tran_type, $date, $target_idx, $tran_amount);
	}

	if ($rst) {
		$response["result"] = true;
	}
}elseif($mode == "get_sale_etc" || $mode == "get_purchase_etc"){
	$tran_type = $_POST["tran_type"];
	$date      = $_POST["date"];

	$rst = $C_SETTLE->getTransactionEtc($tran_type, $date);

	$response["result"] = true;
	$response["data"] = $rst;
}elseif($mode == "save_etc_transaction") {

	$tran_type   = $_POST["tran_type"];
	$tran_idx    = $_POST["tran_idx"];
	$date        = $_POST["date"];
	$target_idx  = $_POST["target_idx"];
	$prev_amount = $_POST["prev_amount"];
	$today_amount = $_POST["today_amount"];
	$tran_amount = $_POST["tran_amount"];
	$remain_amount = $_POST["remain_amount"];
	$tran_memo   = $_POST["tran_memo"];

	$rst = $C_SETTLE->saveTransactionEtcModify($tran_idx, $prev_amount, $today_amount, $tran_amount, $remain_amount, $tran_memo);

	if($rst){
		$response["result"] = true;
	}
}

echo json_encode($response);
?>