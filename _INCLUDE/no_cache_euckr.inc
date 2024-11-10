<?php
// 페이지를 캐쉬에서 읽어드리지 않고 다운로드


// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

if (!defined('DY_SET_TIME_LIMIT')) define('DY_SET_TIME_LIMIT', 0);
@set_time_limit(DY_SET_TIME_LIMIT);


// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면
// 캐쉬의 내용을 가져옴. 완전한지는 검증되지 않음
if(isset($GL_JsonHeader) && $GL_JsonHeader){
	header('Content-Type: text/html; charset=euc-kr');
}else {
	header('Content-Type: text/html; charset=utf-8');
}
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
?>