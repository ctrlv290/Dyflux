<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: FAQ 보기 페이지
 */
//Page Info
$pageMenuIdx = 150;
//Init
include_once "../_init_.php";

$C_BBS = new BBS();

$mode    = "add";
$bbs_id  = $_GET["bbs_id"];
$bbs_idx = $_GET["bbs_idx"];

if(!$bbs_idx) {
	put_msg_and_back("잘못된 접근입니다.");
}
$_view = $C_BBS->getBBSView($bbs_id, $bbs_idx);
if(!$_view){
	put_msg_and_back("잘못된 접근입니다.");
}

$canModify = false;
if($GL_Member["member_idx"] == $_view["member_idx"]){
	$canModify = true;
}

//조회수 Update
if($_COOKIE["dy_bbs_v_".$bbs_idx] != "Y"){
	$tmp = $C_BBS->updateRead($bbs_idx);
	set_cookie("dy_bbs_v_".$bbs_idx, "Y", 86400*365);
}

//벤더사 등급명, 할인율 가져오기
$C_VendorGrade = new VendorGrade();
$_vendor_grade_list = $C_VendorGrade->getVendorGradeList();

//페이지 파라미터
$page_param_column_ary = array("bbs_id", "page", "date_start", "date_end", "search_column", "search_keyword");
$page_param_ary = array();
foreach($page_param_column_ary as $col) $page_param_ary[] = $col . "=" . $_GET[$col];
$page_parameters = implode("&", $page_param_ary);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap w800px">
			<form name="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="bbs_id" value="<?=$bbs_id?>" />
				<input type="hidden" name="bbs_idx" value="<?php echo $bbs_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>카테고리</th>
							<td class="text_left">
								<?=$_view["category_name"]?>
							</td>
						</tr>
						<tr>
							<th>제목</th>
							<td class="text_left">
								<?=$_view["bbs_title"]?>
							</td>
						</tr>
						<tr>
							<th>내용</th>
							<td class="text_left">
								<?=nl2br($_view["bbs_contents"])?>
							</td>
						</tr>
						<?php if($_view["bbs_file_idx_1"]){?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn-download" data-file_idx="<?=$_view["bbs_file_idx_1"]?>" data-file_name="<?=$_view["save_filename_1"]?>"><?=$_view["user_filename_1"]?></a>
								</td>
							</tr>
						<?php }?>
						<?php if($_view["bbs_file_idx_2"]){?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn-download" data-file_idx="<?=$_view["bbs_file_idx_2"]?>" data-file_name="<?=$_view["save_filename_2"]?>"><?=$_view["user_filename_2"]?></a>
								</td>
							</tr>
						<?php }?>
						<?php if($_view["bbs_file_idx_3"]){?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn-download" data-file_idx="<?=$_view["bbs_file_idx_3"]?>" data-file_name="<?=$_view["save_filename_3"]?>"><?=$_view["user_filename_3"]?></a>
								</td>
							</tr>
						<?php }?>
						<?php if($_view["bbs_file_idx_4"]){?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn-download" data-file_idx="<?=$_view["bbs_file_idx_4"]?>" data-file_name="<?=$_view["save_filename_4"]?>"><?=$_view["user_filename_4"]?></a>
								</td>
							</tr>
						<?php }?>
						<?php if($_view["bbs_file_idx_5"]){?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn-download" data-file_idx="<?=$_view["bbs_file_idx_5"]?>" data-file_name="<?=$_view["save_filename_5"]?>"><?=$_view["user_filename_5"]?></a>
								</td>
							</tr>
						<?php }?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<?php if($canModify){?>
							<a href="faq_write.php?bbs_id=<?=$bbs_id?>&bbs_idx=<?=$bbs_idx?>&<?=$page_parameters?>" id="btn-save" class="large_btn blue_btn ">수정</a>
						<?php }?>
						<a href="faq_list.php?<?=$page_parameters?>" class="large_btn">목록</a>
						<?php if($_view["member_idx"] == $GL_Member["member_idx"]){?>
							<a href="javascript:;" id="btn-delete" class="large_btn red_btn">삭제</a>
						<?php }?>
					</div>
				</div>
				<br><br><br><br><br>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/fileupload.js"></script>
<script src="/js/page/help.js?v=<?=time()?>"></script>
<script>
	Help.FAQViewInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

