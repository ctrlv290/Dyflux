<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 기본정보 그룹 관리 관련 Process
 */
//Init
$GL_JsonHeader = true;
include "../_init_.php";

$C_ManageGroup = new ManageGroup();

$mode                   = $_POST["mode"];
$manage_group_type      = $_POST["manage_group_type"];
$manage_group_idx       = $_POST["manage_group_idx"];
$manage_group_name      = $_POST["manage_group_name"];

$response = array();
$response["result"] = false;
$response["list"] = array();

if($mode == "get_manage_group_list"){

	$rst = $C_ManageGroup->getManageGroupList($manage_group_type);
	$response["result"] = true;
	$response["list"] = $rst;
	echo json_encode($response);
	exit;
}elseif($mode == "add_manage_group"){

	$rst = $C_ManageGroup->insertManageGroup($manage_group_type, $manage_group_name);

	$response["result"] = true;
	echo json_encode($response);
	exit;

}elseif($mode == "mod_manage_group"){

	$args = array();
	$args["manage_group_idx"] = $manage_group_idx;
	$args["manage_group_name"] = $manage_group_name;

	$rst = $C_ManageGroup->updateManageGroup($args);

	$response["result"] = true;
	echo json_encode($response);
	exit;
}elseif($mode == "del_manage_group"){

	$manage_group_idx = $_POST["manage_group_idx"];

	$rst = $C_ManageGroup->deleteManageGroup($manage_group_idx);

	$response["result"] = true;
	echo json_encode($response);
	exit;
}elseif($mode == "get_manage_group_member_list") {

	$rst = $C_ManageGroup->getManageGroupMemberList($manage_group_type, $manage_group_idx);
	$response["result"] = true;
	$response["list"] = $rst;
	echo json_encode($response);
	exit;
}

?>