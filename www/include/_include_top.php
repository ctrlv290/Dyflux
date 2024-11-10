<?php
	include_once DY_INCLUDE_PATH . "/_include_check_login.php";
	//include_once DY_INCLUDE_PATH . "/_include_check_permission.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title>DYFLUX</title>
	<meta property="og:title" content="DYFLUX"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?=DY_URL?>/"/>
	<meta property="og:image" content="<?=DY_URL?>/images/og_meta.png"/>
	<meta property="og:description" content="DYFLUX"/>
	<link rel="stylesheet" href="/fontawesome-free-5.1.1-web/css/all.css">
	<link href="https://fonts.googleapis.com/css?family=Nanum+Gothic:400,700,800&amp;subset=korean" rel="stylesheet">
	<link rel="stylesheet" href="/css/font.css">
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/style.css?v=<?=time()?>">
	<link rel="stylesheet" href="/css/loading.css?v=190430">
	<link rel="stylesheet" href="/css/jquery-ui.css">
	<link rel="stylesheet" href="/css/ui.jqgrid.css">
	<link rel="stylesheet" href="/css/multiple-emails.css">
	<link rel="stylesheet" href="/css/lightbox.css">
	<link rel="stylesheet" href="/css/selectize.default.css">
	<link rel="stylesheet" href="/css/jquery.scrollbar.css">
	<script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/moment.js"></script>
	<script type="text/javascript" src="/js/moment.locale.ko.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.min.js"></script>
	<script type="text/javascript" src="/js/grid.locale-kr.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.setColWidth.js"></script>
	<script type="text/javascript" src="/js/multiple-emails.js"></script>
	<script type="text/javascript" src="/js/lightbox.min.js"></script>
	<script type="text/javascript" src="/js/selectize.min.js"></script>
	<script type="text/javascript" src="/js/jquery.inputmask.bundle.min.js"></script>
	<script type="text/javascript" src="/js/jquery.scrollbar.min.js"></script>
	<script type="text/javascript" src="/js/common.js?v=<?=time()?>"></script>
	<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
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

	<script>
		//오늘 날짜
		var _gl_today_text = '<?=date('Y-m-d', time())?>';
		var _gl_today_arr = _gl_today_text.split('-');
		var _gl_today_obj = new Date(_gl_today_arr[0], _gl_today_arr[1], _gl_today_arr[2]);

		var isDYLogin = <?=(isDYLogin()) ? "true;".PHP_EOL : "false;".PHP_EOL;?>
		var gl_vendor_grade = "<?=(isDYLogin()) ? "": $GL_Member["vendor_grade"]?>";
		var _gl_url = '<?=DY_URL?>';

		var jqgridDefaultSetting = true;
	</script>
</head>
<body class="<?=(IS_DEV_SITE) ? "dev": "" ?>">
<div class="wrap index_page <?=($_COOKIE["gnb_menu_hide"] == "Y") ? "menu_hide" : "" ?>">