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

echo json_encode($response);
?>