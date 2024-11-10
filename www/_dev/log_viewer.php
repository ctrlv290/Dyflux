<?php
//Page Info
$pageMenuIdx = 287;
//Init
include_once "../_init_.php";

if($GL_Member["member_type"] != "ADMIN"){
	put_msg_and_back("관리자만 접근 가능합니다.");
	exit;
}


$mode = $_GET["mode"];
if($mode != "PHP" && $mode != "DB" && $mode != "DEBUG"){
	$mode = "PHP";
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
	<style>
		/*table tr:hover {background-color: #fffede;}*/

		.line_box {border: 1px solid #c0c0c0;padding: 10px;}

		.log_detail .plus {display: inline;}
		.log_detail .minus {display: none;}
		.log_detail.show .plus {display: none;}
		.log_detail.show .minus {display: inline;}
	</style>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<div class="content">
			<div class="wrap_tab_menu">
				<ul class="tab_menu">
					<li>
						<a href="log_viewer.php?mode=PHP" class="<?=($mode == "PHP") ? "on" : "" ?>">PHP 로그</a>
					</li>
					<li>
						<a href="log_viewer.php?mode=DB" class="<?=($mode == "DB") ? "on" : "" ?>">DB 로그</a>
					</li>
					<li>
						<a href="log_viewer.php?mode=DEBUG" class="<?=($mode == "DEBUG") ? "on" : "" ?>">DEBUG 로그</a>
					</li>
				</ul>
			</div>
			<div class="tb_wrap">
				<form id="searchForm" name="searchForm">
					<input type="hidden" name="mode" id="mode" value="<?=$mode?>" />
				</form>
				<table class="no_border">
					<colgroup>
						<col width="160" />
						<col width="20" />
						<col width="*" />
					</colgroup>
					<tbody>
					<tr>
						<td class="text_left vtop">
							<div class="tb_wrap">
								<p class="sub_tit2">로그 날짜</p>
								<div class="date_wrap scrollbar-macosx">
									<ul class="line_box">
	<!--									<li><a href="javascript:;" class="link">2019-05-01</a></li>-->
									</ul>
								</div>
							</div>
						</td>
						<td></td>
						<td class="text_left vtop">
							<div class="tb_wrap wrap_log">
								<p class="sub_tit2">로그 내역</p>
								<table class="log_list">
									<colgroup>
										<col width="180" />
										<col width="100" />
										<col width="*" />
									</colgroup>
									<thead>
										<th>일시</th>
										<th>타입</th>
										<th>에러내용</th>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="../js/main.js"></script>
	<link rel=stylesheet href=https://cdn.jsdelivr.net/npm/pretty-print-json@0.1/dist/pretty-print-json.css>
	<script src=https://cdn.jsdelivr.net/npm/pretty-print-json@0.1/dist/pretty-print-json.min.js></script>
	<script src="../js/page/dev.logviewer.js"></script>
	<script>
		$(function(){
			LogViewer.Init();
		});
	</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>