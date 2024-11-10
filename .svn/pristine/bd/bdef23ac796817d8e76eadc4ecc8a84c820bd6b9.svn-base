<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 계좌관리 관련 Process
 */

//Page Info
$pageMenuIdx = 240;

$GL_JsonHeader = true; //Json Header
include_once "../_init_.php";

$C_Bank = new Bank();

$response = array(
	"result" => false,
	"msg" => "",
);

$mode       = $_POST["mode"];
$idx        = $_POST["idx"];

if($mode == "move"){

	$dir = $_POST["dir"];

	$args = array();
	$args["dir"] = $dir;
	$args["idx"] = $idx;
	$chk = $C_Bank->checkCanSortChange($args);

	if($chk["result"]) {
		$C_Bank->moveBankSort($args);
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = $chk["msg"];
	}
}

if($mode == "valid_chk"){

    $bank_idx    = $_POST["bank_idx"];
    $bank_use_n_date = $_POST["bank_use_n_date"];
    $today = $_POST["today"];
    $chk = $C_Bank->getBankTransactionCheck($bank_use_n_date, $today, $bank_idx);

    if($chk) {
        if ($chk["tran_in"] != 0 || $chk["tran_out"] != 0 || $chk["tran_sum"] != 0) {
            $response["result"] = false;
        }else{
            $response["result"] = true;
        }
    }else{
        $response["result"] = true;
    }
}

echo json_encode($response);
?>