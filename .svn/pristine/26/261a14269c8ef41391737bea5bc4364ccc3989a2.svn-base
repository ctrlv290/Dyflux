<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통 파일 정보
 */
//Init
include_once "../_init_.php";

$C_Files = new Files();

$mode = $_POST["mode"];
$file_idx = $_POST["file_idx"];

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["fileInfo"] = array();


if($mode == "get_file_info")
{
	$rst = $C_Files -> getFileInfo($file_idx);
	if($rst)
	{
		$response["result"] = true;
		$response["fileInfo"] = array(
			"file_idx" => $rst["file_idx"],
			"userfilename" => $rst["user_filename"],
			"extension" => $rst["extension"],
			"new_file_name" => $rst["save_filename"],
			"path" => $rst["save_webpath"],
		);
	}else{
		$response["msg"] = "잘못된 접근입니다.";
	}
}

echo json_encode($response, true);
?>