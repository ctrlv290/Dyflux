<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: _include_top.php 와 _include_top_popup.php 에서 Include 되는 권한 체크 프로세스
 * 변경 - _init_.php 에서 로딩 하는 걸로 변경 -
 */

//CS 에서 넘어 올 경우 $_POST 를 체크하여 CS 로그인 실행
$cs_error_return = false;
if($_POST["cs_m_id"] && $_POST["cs_m_token"]){
	$cs_error_return = true;
	$C_Login = new Login();
	$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기
}

if (defined("_DYFLUX_") && isset($pagePermissionIdx) && $pagePermissionIdx > 0) {
	if(isHasLoginSession()) {
		$C_SiteMenu = new SiteMenu();

		if ($C_SiteMenu->checkPermission($pagePermissionIdx, $GL_Member["member_idx"])) {
			//echo "권한있음";
		} else {
			//echo "권한없음";
			if(!$cs_error_return) {
				//put_msg_and_top_back("권한이 없는 페이지입니다.");
				put_msg_top_go_replace("로그인이 필요한 페이지 입니다.", "/");
			}else{
				echo "Permission Error";
			}
		}
	}else{
		if(!$cs_error_return) {
			put_msg_top_go_replace("로그인이 필요한 페이지 입니다.", "/");
		}else{
			echo "Login Error";
		}
	}
}
?>