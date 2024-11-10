<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 권한그룹관리 관련 Process
 */
//Page Info
$pageMenuIdx = 168;
//Init
include "../_init_.php";

$C_MemberGroup = new MemberGroup();
$C_SiteMenu = new SiteMenu();
$C_Permission = new Permission();

$mode                   = $_POST["mode"];
$member_group_idx       = $_POST["member_group_idx"];
$member_idx_list        = $_POST["member_idx_list"];
$member_group_name      = $_POST["member_group_name"];
$member_idx             = $_POST["member_idx"];
$member_group_etc       = $_POST["member_group_etc"];
$member_group_is_use    = $_POST["member_group_is_use"];


if($mode == "add")
{
	//Check Dup
	$args = array();
	$args["member_group_name"]       = $member_group_name;
	$args["member_group_etc"]        = $member_group_etc;
	$args["member_group_is_use"]     = $member_group_is_use;
	$args["member_idx_list"]         = $member_idx;
	$inserted_member_group_idx = $C_MemberGroup->insertMemberGroup($args);

	echo $inserted_member_group_idx;

	//권한 Update 시작

	//메뉴 리스트 가져오기 (모든 메뉴, 권한 포함)
	//신규 회원이라 권한은 모두 없음
	$prevMenuPermission = $C_SiteMenu -> getAllMenuListWithPermission($inserted_member_group_idx);

	//추가 권한 (권한이 있는 메뉴들의 IDX)
	$permission_idx = $_POST["permission_idx"];

	$nextMenuPermission = array();
	$permissionMenuIdxAryAdd = array();
	$permissionMenuIdxAryDelete = array();
	if($permission_idx)
	{
		//넘겨 받은 수정 권한 Array 화
		$_postPermissionList = array_map("trim", $permission_idx);

		//넘겨 받은 수정 권한 중
		//기존에 있던 권한 인지 체크 하여
		//추가/유지/삭제 로그 만들기
		foreach($prevMenuPermission as $prevP)
		{
			$_oneP = array();
			$_oneP["idx"] = $prevP["idx"];
			$_oneP["permission"] = 0;
			if(in_array($prevP["idx"], $_postPermissionList))
			{
				if($prevP["is_permission"] == 0) {
					//기존에도 없던 권한 이면 추가
					$_oneP["permission"] = 1;
					$permissionMenuIdxAryAdd[] = $prevP["idx"];
				}
				$nextMenuPermission[] = $_oneP;
			}
		}
	}

	//기존 권한 삭제 및 수정 권한 Insert
	$tmp = $C_Permission -> insertPermission($inserted_member_group_idx, $nextMenuPermission, implode(",", $permissionMenuIdxAryAdd), implode(",", $permissionMenuIdxAryDelete), "I");

	//권한 Update 끝

	go_replace("member_group_list.php");

}elseif($mode == "mod"){

	//내정보 수정일 경우 로그인된 IDX 와 비교
	if($mode == "mod_self")
	{
		if($idx != $GL_Member["member_idx"])
		{
			put_msg_and_back("잘못된 접근입니다.");
		}
	}

	$args = array();
	$args["member_group_idx"]        = $member_group_idx;
	$args["member_group_name"]       = $member_group_name;
	$args["member_group_etc"]        = $member_group_etc;
	$args["member_group_is_use"]     = $member_group_is_use;
	$args["member_idx_list"]         = $member_idx;
	$C_MemberGroup->updateMemberGroup($args);


	if($mode == "mod") {
		//권한 Update 시작

		//메뉴 리스트 가져오기 (모든 메뉴, 권한 포함)
		$prevMenuPermission = $C_SiteMenu->getAllMenuListWithPermission($member_group_idx);

		//수정 권한 (권한이 있는 메뉴들의 IDX)
		$permission_idx = $_POST["permission_idx"];

		$nextMenuPermission = array();
		$permissionMenuIdxAryAdd = array();
		$permissionMenuIdxAryDelete = array();
		if ($permission_idx) {
			//넘겨 받은 수정 권한 Array 화
			$_postPermissionList = array_map("trim", $permission_idx);

			//넘겨 받은 수정 권한 중
			//기존에 있던 권한 인지 체크 하여
			//추가/유지/삭제 로그 만들기
			foreach ($prevMenuPermission as $prevP) {
				$_oneP = array();
				$_oneP["idx"] = $prevP["idx"];
				$_oneP["permission"] = 0;
				if (in_array($prevP["idx"], $_postPermissionList)) {
					if ($prevP["is_permission"] == 0) {
						//기존에도 없던 권한 이면 추가
						$_oneP["permission"] = 1;
						$permissionMenuIdxAryAdd[] = $prevP["idx"];
					} else {
						//기존에 있던 권한 이면 유지
						$_oneP["permission"] = 0;
					}

					$nextMenuPermission[] = $_oneP;
				} else {
					if ($prevP["is_permission"] == 0) {
						//기존에도 없던 권한 이면 유지
						$_oneP["permission"] = 0;
					} else {
						//기존에도 있던 권한 이면 삭제
						$_oneP["permission"] = -1;
						$permissionMenuIdxAryDelete[] = $prevP["idx"];
					}
				}
			}
		}

		//기존 권한 삭제 및 수정 권한 Insert
		$tmp = $C_Permission->insertPermission($member_group_idx, $nextMenuPermission, implode(",", $permissionMenuIdxAryAdd), implode(",", $permissionMenuIdxAryDelete), "U");

		//권한 Update 끝
	}

	if($mode == "mod") {
		go_replace("member_group_list.php");
	}else{
		go_replace("/");
	}


}elseif($mode == "id_check"){
	$rst = $C_Users->checkDupID($login_id);
	$response = array("result" => $rst);
	echo json_encode($response);
	exit;
}elseif($mode == "lst"){

}

?>