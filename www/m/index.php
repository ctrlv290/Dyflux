<?php
include_once "./_init_.php";
?>
<!DOCTYPE html>
<html lang="ko" class="main">
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

	<!-- Favicon -->
	<link rel="apple-touch-icon" sizes="57x57" href="/images/fav/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/images/fav/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/images/fav/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/images/fav/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/images/fav/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/images/fav/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/images/fav/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/images/fav/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/fav/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/images/fav/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/fav/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/images/fav/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/fav/favicon-16x16.png">
	<link rel="manifest" href="/images/fav/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/images/fav/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	<!-- /Favicon -->

	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/fonts.css"/>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
	<link rel="stylesheet" type="text/css" href="css/loader.css"/>

	<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</head>
<body>
<div class="wrap main">
	<div class="wrap_login">
		<a href="javascript:;" class="logo"><img src="images/logo.png" alt="dyflux" /></a>
		<form method="get" class="login_form">
			<div class="input_set">
				<label><input type="text" id="id" placeholder="아이디" title="아이디를 입력해주세요" /></label>
				<label><input type="password" id="password" placeholder="비밀번호" title="비밀번호를 입력해주세요" /></label>
			</div>
			<div class="login_chk_set">
				<label>
					<input type="checkbox" name="save-id" id="save-id" value="Y" class="chk_btn" />
					로그인 상태 유지
				</label>
			</div>
			<a href="javascript:;" id="btn-login" class="login_btn">관리자 로그인</a>
		</form>
	</div>
</div><!-- wrap -->
<!--dimmer-->
<div class="dimmer_set">
	<div class="lds-facebook"><div></div><div></div><div></div></div>
</div>
<!--/dimmer-->
<script src="js/index.js"></script>
</body>
</html>