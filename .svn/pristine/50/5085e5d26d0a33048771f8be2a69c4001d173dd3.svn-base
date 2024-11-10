<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 관련 Process
 */

//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

if(!isLogin()) exit;

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "add_fav") {

	$C_SiteMenu = new SiteMenu();

	$menu_idx = $_POST["menu_idx"];

	$rst = $C_SiteMenu->saveFav($GL_Member["member_idx"], $menu_idx);

	if($rst){
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = "5개까지만 등록 가능합니다.";
	}
}elseif($mode == "remove_fav"){
	$C_SiteMenu = new SiteMenu();

	$menu_idx = $_POST["menu_idx"];

	$rst = $C_SiteMenu->removeFav($GL_Member["member_idx"], $menu_idx);

	if($rst){
		$response["result"] = true;
	}else{
		$response["result"] = false;
	}
}

echo json_encode($response);
?>