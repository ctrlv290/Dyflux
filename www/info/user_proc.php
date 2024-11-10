<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자관리 관련 Process
 */
//Page Info
$pageMenuIdx = 56;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_SiteMenu = new SiteMenu();
$C_Permission = new Permission();
//print_r($_POST);

$mode       = $_POST["mode"];
$idx        = $_POST["idx"];
$login_id   = $_POST["login_id"];
$login_pw   = $_POST["login_pw"];
$name       = $_POST["name"];
$tel1       = $_POST["tel1"];
$tel2       = $_POST["tel2"];
$tel3       = $_POST["tel3"];
$tel        = ($tel2 && $tel3) ? $tel1 . "-" . $tel2 . "-" . $tel3 : "";
$mobile1    = $_POST["mobile1"];
$mobile2    = $_POST["mobile2"];
$mobile3    = $_POST["mobile3"];
$mobile     = $mobile1 . "-" . $mobile2 . "-" . $mobile3;
$email1     = $_POST["email1"];
$email2     = $_POST["email2"];
$email      = $email1 . "@" . $email2;
$etc        = $_POST["etc"];
$is_use     = $_POST["is_use"];


if($mode == "add")
{
	//Check Dup
	if(!$C_Users->checkDupID($login_id))
	{
		put_msg_and_back("이미 사용중인 아이디입니다.");
		exit;
	}else{
		$args = array();
		$args["login_id"]       = $login_id;
		$args["login_pw"]       = crypt($login_pw, DY_PASSWORD_SALT);
		$args["name"]           = $name;
		$args["tel"]            = $tel;
		$args["mobile"]         = $mobile;
		$args["email"]          = $email;
		$args["etc"]            = $etc;
		$args["is_use"]         = $is_use;
		$inserted_member_idx = $C_Users->insertUser($args);



		//권한 Update 시작

		//메뉴 리스트 가져오기 (모든 메뉴, 권한 포함)
		//신규 회원이라 권한은 모두 없음
		$prevMenuPermission = $C_SiteMenu -> getAllMenuListWithPermission($inserted_member_idx);

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
		$tmp = $C_Permission -> insertPermission($inserted_member_idx, $nextMenuPermission, implode(",", $permissionMenuIdxAryAdd), implode(",", $permissionMenuIdxAryDelete), "I");

		//권한 Update 끝



		go_replace("user_list.php");
	}
}elseif($mode == "mod" || $mode == "mod_self"){

	//내정보 수정일 경우 로그인된 IDX 와 비교
	if($mode == "mod_self")
	{
		if($idx != $GL_Member["member_idx"])
		{
			put_msg_and_back("잘못된 접근입니다.");
		}
	}

	$args = array();
	$args["idx"]            = $idx;
	if($login_pw) {
		$args["login_pw"] = crypt($login_pw, DY_PASSWORD_SALT);
	}
	$args["name"]           = $name;
	$args["tel"]            = $tel;
	$args["mobile"]         = $mobile;
	$args["email"]          = $email;
	$args["etc"]            = $etc;
	$args["is_use"]         = $is_use;
	$C_Users->updateUser($args);


	if($mode == "mod") {
		//권한 Update 시작

		//메뉴 리스트 가져오기 (모든 메뉴, 권한 포함)
		$prevMenuPermission = $C_SiteMenu->getAllMenuListWithPermission($idx);

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
		$tmp = $C_Permission->insertPermission($idx, $nextMenuPermission, implode(",", $permissionMenuIdxAryAdd), implode(",", $permissionMenuIdxAryDelete), "U");

		//권한 Update 끝

		//내정보 수정일 경우
		if($mode == "mod_self") {
			//로그인 세션 Update
			$C_Login = new Login();
			$C_Login->setLoginSession($idx);
		}
	}

	if($mode == "mod") {
		go_replace("user_list.php");
	}elseif($mode == "mod_self") {
		go_replace("myinfo.php");
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