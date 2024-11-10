<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통코드관리 관련 Process
 */
//Page Info
$pageMenuIdx = 53;
include "../_init_.php";

$C_Code = new Code();
//print_r($_POST);

$mode       = $_POST["mode"];
$idx        = $_POST["idx"];
$code_idx   = $_POST["code_idx"];
$code       = $_POST["code"];
$code_name  = $_POST["code_name"];
$is_use     = $_POST["is_use"];


if($mode == "add")
{
	//Check Dup
	if(!$C_Code->checkDupCode($code, $code_idx))
	{
		put_msg_and_back("이미 사용중인 코드 값입니다.");
		exit;
	}else{
		$args = array();
		$args["code_idx"]       = $code_idx;
		$args["code"]           = strtoupper($code);
		$args["code_name"]      = $code_name;
		$args["is_use"]         = $is_use;
		$C_Code->insertCode($args);

		go_replace("code_list.php");
	}
}elseif($mode == "mod"){

	$args = array();
	$args["idx"]            = $idx;
	$args["code_idx"]       = $code_idx;
	$args["code_name"]      = $code_name;
	$args["is_use"]         = $is_use;
	$C_Code->updateCode($args);

	go_replace("code_list.php");
}elseif($mode == "code_check"){
	$rst = $C_Code->checkDupCode($code, $code_idx);
	$response = array("result" => $rst);
	echo json_encode($response);
	exit;
}elseif($mode == "lst"){

}

?>