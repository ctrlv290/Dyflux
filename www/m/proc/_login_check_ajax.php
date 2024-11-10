<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 확인 프로세스
 */

//Init
$GL_JsonHeader = true; //Json Header
include_once "../../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["data"] = "";

$id         = trim($_POST["id"]);
$pw         = trim($_POST["pw"]);
$save_id    = trim($_POST["save_id"]);
$user_agent = trim($_SERVER["HTTP_USER_AGENT"]);



$C_Login = new Login();

if($id && $pw) {
	$_memAry = $C_Login -> checkLoginID($id);
	if($_memAry){

		$member_idx = (int) $_memAry["idx"];
		$can_mobile_login = $_memAry["can_mobile_login"];   //모바일 로그인 가능 여부

		if($can_mobile_login == "Y"){
			$_db_member_idx = $_memAry["idx"];
			$_db_member_type = $_memAry["member_type"];
			$_db_member_pw = $_memAry["member_pw"];
			$_db_lastlogin_date = $_memAry["lastlogin_date"];

			if($_db_member_pw != crypt($pw, $_db_member_pw)) {
				$response["msg"] = "비밀번호가 일치하지 않습니다.";
			}else{
				//일반 사용자
				$cs_login_ip = $_SERVER["REMOTE_ADDR"];
				$C_Login -> setMobileLoginSession($_db_member_idx);
				$C_Login -> updateLastLoginInfo($_db_member_idx, "", "");
				$response["msg"] = $cs_login_token;
				$response["result"] = true;
			}

			if($response["result"]){
				if($save_id == "Y"){
					$token = generateToken(10);
					$C_Login -> updateMToken($token, $_db_member_idx);
					set_cookie("DY_TOKEN_Mobile", $token, 30 * 24 * 60 * 60);
				}
			}

		}else{
			$response["msg"] = "접근 할 수 없는 아이디 입니다.";
		}

	}else{
		$response["msg"] = "아이디 또는 비밀번호를 정확히 입력해주세요.";
	}
}else{
	$response["msg"] = "아이디와 비밀번호를 정확히 입력해주세요.";
}

echo json_encode($response);

?>