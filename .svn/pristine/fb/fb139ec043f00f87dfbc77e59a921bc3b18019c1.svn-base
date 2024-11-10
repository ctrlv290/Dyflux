<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 페이지
 */

//Init
include_once "./_init_.php";

$C_BBS = new BBS();

$mode    = "add";
$bbs_id  = "notice";
$bbs_idx = $_GET["bbs_idx"];

if(!$bbs_idx) {
	put_msg_and_back("잘못된 접근입니다.");
}
$_view = $C_BBS->getBBSView($bbs_id, $bbs_idx);
if(!$_view){
	put_msg_and_back("잘못된 접근입니다.");
}

//조회수 Update
if($_COOKIE["dy_bbs_v_".$bbs_idx] != "Y"){
	$tmp = $C_BBS->updateRead($bbs_idx);
	set_cookie("dy_bbs_v_".$bbs_idx, "Y", 86400*365);
}

//이전-다음
$prev_next_Ary = $C_BBS->getMainNoticeViewPrevNext($bbs_idx);

//페이지 파라미터
$page_param_column_ary = array("page");
$page_param_ary = array();
foreach($page_param_column_ary as $col) $page_param_ary[] = $col . "=" . $_GET[$col];
$page_parameters = implode("&", $page_param_ary);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>dyflux</title>
	<meta property="og:title" content=""/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:description" content=""/>
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

	<link rel="stylesheet" type="text/css" href="css/main_reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main_fonts.css"/>
	<link rel="stylesheet" type="text/css" href="/css/slick.css"/>
	<link rel="stylesheet" type="text/css" href="/css/main.css"/>

	<script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="/js/slick.min.js"></script>
	<script>
		$(function(){
			//첨부파일 다운로드
			$(".btn-download").on("click", function() {
				var file_idx = $(this).data("file_idx");
				var file_name = $(this).data("file_name");
				var url = "/proc/_filedownload.php?idx=" + file_idx + "&filename=" + file_name;
				$("#hidden_ifrm_common_filedownload").attr("src", url);
			});
		});
	</script>
</head>
<body>

<div class="notice_view_set">
	<div class="inner_set">
		<table class="view_table">
			<tbody>
			<tr>
				<th>카테고리</th>
				<td colspan="3"><?=$_view["category_name"]?></td>
			</tr>
			<tr>
				<th>제목</th>
				<td colspan="3"><?=$_view["bbs_title"]?></td>
			</tr>
			<tr>
				<th>등록일</th>
				<td class="thin"><?=date('Y-m-d', strtotime($_view["bbs_regdate"]))?></td>
				<th class="lookup">조회수</th>
				<td class="lookup thin"><?=number_format($_view["bbs_read"])?></td>
			</tr>
			<tr class="view_inner">
				<td colspan="4">
					<?=nl2br($_view["bbs_contents"]);?>
				</td>
			</tr>
			<?php
			if(!empty($_view["bbs_file_idx_1"])) {
				?>
				<tr>
					<th>첨부파일</th>
					<td colspan="3">
						<a href="javascript:;" class="btn-download thin file_btn" data-file_idx="<?=$_view["bbs_file_idx_1"]?>" data-file_name="<?=$_view["save_filename_1"]?>"><?=$_view["user_filename_1"]?></a>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<div class="btn_set">
			<?php
			if($prev_next_Ary["prev"]) {
				?>
				<a href="notice_view.php?bbs_idx=<?=$prev_next_Ary["prev"]["bbs_idx"]?>&page=<?=$page?>" class="prev">이전</a>
				<?php
			}
			?>
			<?php
			if($prev_next_Ary["next"]) {
				?>
				<a href="notice_view.php?bbs_idx=<?=$prev_next_Ary["next"]["bbs_idx"]?>&page=<?=$page?>" class="next">다음</a>
				<?php
			}
			?>
			<a href="notice_list.php?<?=$page_parameters?>" class="list">목록</a>
		</div>
	</div>
</div>
<iframe src="about:blank" id="hidden_ifrm_common_filedownload" name="hidden_ifrm_common_filedownload" frameborder="0" style="width: 0;height: 0;display: none;"></iframe>
</body>
</html>