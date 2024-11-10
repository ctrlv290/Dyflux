<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 내정보 수정
 */
//Page Info
$pageMenuIdx = 55;
//Permission IDX
$permissionMenuIdx = 55;
//Init
include_once "../_init_.php";

$idx = $GL_Member["member_idx"];

$C_Users = new Users();
$member_type = $C_Users -> getUserType($idx);

$write_form_filename = "";
if($member_type == "USER" || $member_type == "ADMIN") {
	$write_form_filename = "myinfo_user.php";
}elseif($member_type == "VENDOR"){
	$write_form_filename = "myinfo_vendor.php";
}elseif($member_type == "SUPPLIER"){
	$write_form_filename = "myinfo_supplier.php";
}else{
	put_msg_and_back("존재하지 않는 사용자입니다.");
}

include_once $write_form_filename;
?>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>