<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: _include_top.php 와 _include_top_popup.php 에서 Include 되는 로그인 체크 프로세스
 */

include_once "../_init_.php";

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

/**
 * 자동 로그인 쿠키가 있을 경우 자동 로그인 실행
 */
$_tmp_dy_token = get_cookie("DY_TOKEN");
if($_tmp_dy_token) {
	$C_Login->checkAutoLogin($_tmp_dy_token);
}
unset($_tmp_dy_token);


LoginCheck($GL_page_url);
?>