<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 확인 프로세스
 */

//Init
$GL_JsonHeader = true; //Json Header
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["data"] = "";

$member_id  = trim($_POST["member_id"]);
$member_pw  = trim($_POST["member_pw"]);
$user_agent = trim($_SERVER["HTTP_USER_AGENT"]);
$save_id    = trim($_POST["save_id"]);

if(!$member_id || !$member_pw)
{
	$response["msg"] = "아이디 또는 비밀번호를 입력해주세요.";
}else{
	$C_Login = new Login();
	$_memAry = $C_Login -> checkLoginID($member_id);

	if(!$_memAry)
	{
		$response["msg"] = "존재하지 않는 회원입니다.";
	}else{

		$_db_member_idx = $_memAry["idx"];
		$_db_member_type = $_memAry["member_type"];
		$_db_member_pw = $_memAry["member_pw"];
		$_db_lastlogin_date = $_memAry["lastlogin_date"];

		if($_db_member_pw != crypt($member_pw, $_db_member_pw)) {
			$response["msg"] = "비밀번호가 일치하지 않습니다.";
		}else{
			if($user_agent == "DY_AUTO" || $user_agent == "DY_INVOICE") {
				// CS 프로그램에서 로그인 했을 경우
				if ($_db_member_type == "ADMIN" || $_db_member_type == "USER") {
					//일반 사용자
					$cs_login_token = $C_Login -> getCSloginToken();
					$cs_login_ip = $_SERVER["REMOTE_ADDR"];
					$C_Login -> setLoginSession($_db_member_idx);
					$C_Login -> updateLastLoginInfo($_db_member_idx, $cs_login_token, $cs_login_ip);
					$response["msg"] = $cs_login_token;
					$response["result"] = true;
				} else {
					$response["msg"] = "권한이 없습니다.";
				}
			} elseif ($_db_member_type == "ADMIN" || $_db_member_type == "USER") {
				//일반 사용자
				$C_Login -> setLoginSession($_db_member_idx);
				$C_Login -> updateLastLoginInfo($_db_member_idx);
				$response["result"] = true;

			} elseif ($_db_member_type == "VENDOR") {
				//벤더사 로그인

				$_vendor_status = $C_Login -> checkVendorStatus($_db_member_idx);

				if($_vendor_status == "VENDOR_PENDDING"){
					$response["msg"] = "벤더사 승인 대기 중입니다.";
				}elseif($_vendor_status == "VENDOR_REJECT"){
					$response["msg"] = "벤더사 승인이 반려 처리 되었습니다.";
				}elseif($_vendor_status == "VENDOR_APPLY"){

					$C_Login -> setLoginSession($_db_member_idx);
					$C_Login -> updateLastLoginInfo($_db_member_idx);
					$response["result"] = true;
				}

			} elseif ($_db_member_type == "SUPPLIER") {
				//공급처 로그인
				$C_Login -> setLoginSession($_db_member_idx);
				$C_Login -> updateLastLoginInfo($_db_member_idx);
				$response["result"] = true;
			}

			if($response["result"]){
				if($save_id == "Y"){
					$token = generateToken(10);
					$C_Login -> updateToken($token, $_db_member_idx);
					set_cookie("DY_TOKEN", $token, 30 * 24 * 60 * 60);
				}
			}

		}
	}

}

echo json_encode($response, true);
?>