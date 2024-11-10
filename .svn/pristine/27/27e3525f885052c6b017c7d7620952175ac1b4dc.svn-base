<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 모바일 _include_top.php Include 되는 로그인 체크 프로세스
 */

include_once "../_init_.php";

$C_Login = new Login();
/**
 * 자동 로그인 쿠키가 있을 경우 자동 로그인 실행
 */
$_tmp_dy_token = get_cookie("DY_TOKEN_Mobile");
if($_tmp_dy_token) {
	$C_Login->checkAutoMLogin($_tmp_dy_token);
}
unset($_tmp_dy_token);

MLoginCheck($GL_page_url);

$C_Login = null;
?>