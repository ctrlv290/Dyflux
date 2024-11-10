<?php
	include_once "/include/_include_check_login.php";
	//include_once DY_INCLUDE_PATH . "/_include_check_permission.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title>dyflux</title>
	<link rel="stylesheet" href="/fontawesome-free-5.1.1-web/css/all.css">
	<link href="https://fonts.googleapis.com/css?family=Nanum+Gothic:400,700,800&amp;subset=korean" rel="stylesheet">
	<link rel="stylesheet" href="/css/font.css">
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/style.css">
	<link rel="stylesheet" href="/css/loading.css">
	<link rel="stylesheet" href="/css/jquery-ui.css">
	<link rel="stylesheet" href="/css/ui.jqgrid.css">
	<link rel="stylesheet" href="/css/multiple-emails.css">
	<link rel="stylesheet" href="/css/lightbox.css">
	<link rel="stylesheet" href="/css/selectize.default.css">
	<script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/moment.js"></script>
	<script type="text/javascript" src="/js/moment.locale.ko.js"></script>
	<script type="text/javascript" src="/js/grid.locale-kr.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.min.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.setColWidth.js"></script>
	<script type="text/javascript" src="/js/multiple-emails.js"></script>
	<script type="text/javascript" src="/js/multiple-emails.js"></script>
	<script type="text/javascript" src="/js/lightbox.min.js"></script>
	<script type="text/javascript" src="/js/selectize.min.js"></script>
	<script type="text/javascript" src="/js/jquery.inputmask.bundle.min.js"></script>
	<script type="text/javascript" src="/js/common.js?v=20191213"></script>
	<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
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
<div class="wrap popup">