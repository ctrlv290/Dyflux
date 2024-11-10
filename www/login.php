<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 페이지
 */
header('Location: /');
// OR: header('Location: http://www.yoursite.com/home-page.html', true, 302);
exit;

//Init
include_once "./_init_.php";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title>dyflux</title>
	<link rel="stylesheet" href="/fontawesome-free-5.1.1-web/css/all.css">
	<link rel="stylesheet" href="/css/font.css">
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/style.css">
	<link rel="stylesheet" href="/css/loading.css">
	<link rel="stylesheet" href="/css/jquery-ui.css">
	<link rel="stylesheet" href="/css/ui.jqgrid.css">
	<link rel="stylesheet" href="/css/multiple-emails.css">
	<script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/moment.js"></script>
	<script type="text/javascript" src="/js/moment.locale.ko.js"></script>
	<script type="text/javascript" src="/js/grid.locale-kr.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.min.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.setColWidth.js"></script>
	<script type="text/javascript" src="/js/multiple-emails.js"></script>
	<script type="text/javascript" src="/js/common.js"></script>
	<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
	<style>
		html, body {height: 100%;overflow: hidden;}
	</style>
	<script>
		$(function(){

			//
			$("input[name='member_id']").on("keyup", function(e){
				var keyCode = (event.keyCode ? event.keyCode : event.which);
				if (keyCode == 13) {
					$("input[name='member_pw']").focus();
				}
			});

			//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
			$("input.enterDoSearch").on("keyup", function(e){
				var keyCode = (event.keyCode ? event.keyCode : event.which);
				if (keyCode == 13) {
					event.preventDefault();
					loginCheck();
				}
			});

			$(".btn-login").on("click", function(){
				loginCheck();
			});
		});

		var loginCheck = function(){

			var member_id = $("input[name='member_id']").val();
			var member_pw = $("input[name='member_pw']").val();

			if(member_id == "" || member_pw == "")
			{
				alert("로그인ID 또는 비밀번호를 정확하게 입력해주세요.");
				return false;
			}else{
				var p_url = "/proc/_login_check_ajax.php";
				var returnUrl = "<?=$_GET["return_url"]?>";
				returnUrl = (returnUrl == "") ? "/index.php" : returnUrl;
				var dataObj = new Object();
				dataObj.member_id = member_id;
				dataObj.member_pw = member_pw;
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					console.log(response);

					if(response.result)
					{
						location.href = returnUrl;
					}else{
						alert(response.msg);
					}

				}).fail(function(jqXHR, textStatus){
					//console.log(jqXHR, textStatus);
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				});
			}
		};
	</script>
</head>
<body>
<div class="" style="width: 100%;height: 100%;">
	<table style="width: 100%; height: 100%;">
		<tr>
			<td style="background-color: #1c435f;color: #fff;">
				<form name="loginForm">
				<p>
					<input type="text" name="member_id" value="" placeholder="로그인 ID" />
				</p>
				<p>
					<input type="password" name="member_pw" class="enterDoSearch" value="" placeholder="로그인 Password" />
				</p>
				<br>
				<a href="javascript:;" class="large_btn btn-login">로그인</a>
				</form>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
