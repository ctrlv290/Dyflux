<?php
	include_once DY_Mobile_INCLUDE_PATH . "/_include_check_m_login.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title>DYFLUX</title>
	<meta property="og:title" content="DYFLUX"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?=DY_URL?>/m/"/>
	<meta property="og:image" content="<?=DY_URL?>/images/og_meta.png"/>
	<meta property="og:description" content="DYFLUX"/>
	<meta name="format-detection" content="telephone=no" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

	<link rel="stylesheet" href="/fontawesome-free-5.1.1-web/css/all.css">
	<link rel="stylesheet" type="text/css" href="/m/css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="/m/css/fonts.css"/>
	<link rel="stylesheet" type="text/css" href="/css/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="/m/css/style.css?v=190426"/>
	<link rel="stylesheet" type="text/css" href="/m/css/loader.css"/>

	<script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/moment.js"></script>
	<script type="text/javascript" src="/js/moment.locale.ko.js"></script>
	<script type="text/javascript" src="/js/jquery.floatThead.min.js"></script>
	<script type="text/javascript" src="/js/common.js?v=190412"></script>
	<script type="text/javascript" src="/js/page/common.function.js"></script>
	<script type="text/javascript" src="/m/js/main.js"></script>

	<script>
	//오늘 날짜
	var _gl_today_text = '<?=date('Y-m-d', time())?>';
	var _gl_today_arr = _gl_today_text.split('-');
	var _gl_today_obj = new Date(_gl_today_arr[0], _gl_today_arr[1], _gl_today_arr[2]);
	</script>