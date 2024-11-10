<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 메인화면관리 관련 Process
 */
//Page Info
$pageMenuIdx = 62;
//Init
include "../_init_.php";
//print_r($_POST);

$C_MainControl = new MainControl();

$mode                    = $_POST["mode"];

if($mode == "save") {

	$main_type_list = array(
		"calendar",
		"today",
		"lastest",
		"delivery",
		"process",
		"stock",
		"return",
		"product",
		"vendor",
	);

	$list = array();
	foreach ($main_type_list as $t){
		$list[$t] = (isset($_POST[$t]) && ($_POST[$t] == "Y" || $_POST[$t] = "N")) ? $_POST[$t] : "N";
	}

	$C_MainControl->saveMainControl($GL_Member["member_idx"], $list);

	put_msg_and_go("저장되었습니다.", "main_control.php");
}elseif($mode == "fav_delete"){

	$fav_idx = $_POST["idx"];

	$C_SiteMenu = new SiteMenu();

	$rst = $C_SiteMenu->removeFavByFavIdx($fav_idx);

	put_msg_and_go("삭제되었습니다.", "main_control.php");

}

?>