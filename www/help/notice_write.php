<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 시스템공지사항 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 145;
//Init
include_once "../_init_.php";

//등록 가능 여부
if(!$GL_Member["member_type"] == "USER"){
	put_msg_and_back("잘못된 접근입니다.");
}


$C_BBS = new BBS();

$mode    = "add";
$bbs_id  = $_GET["bbs_id"];
$bbs_idx = $_GET["bbs_idx"];

if(!empty($bbs_idx)) {
	$_view = $C_BBS->getBBSView($bbs_id, $bbs_idx);
	if (!$_view || $_view["bbs_id"] != "notice") {
		put_msg_and_back("잘못된 접근입니다.");
	} else {
		$mode = "update";
	}
}

$bbs_target = "";
$bbs_category = "";
$bbs_is_main = "";
$bbs_title = "";
$bbs_contents = "";
$bbs_file_idx_1 = "";

extract($_view);

//페이지 파라미터
$page_param_column_ary = array("bbs_id", "page", "category", "date_start", "date_end", "search_column", "search_keyword");
$page_param_ary = array();
foreach($page_param_column_ary as $col) $page_param_ary[] = $col . "=" . $_GET[$col];
$page_parameters = implode("&", $page_param_ary);

//벤더사 등급명, 할인율 가져오기
$C_VendorGrade = new VendorGrade();
$_vendor_grade_list = $C_VendorGrade->getVendorGradeList();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap w800px">
			<form name="dyForm" method="post" class="<?php echo $mode?>" action="bbs_proc.php?<?=$page_parameters?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="bbs_id" value="notice" />
				<input type="hidden" name="bbs_idx" value="<?php echo $bbs_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>대상 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="bbs_target">
									<option value="ALL" <?=($bbs_target == "ALL") ? "selected" : ""?>>전체</option>
									<option value="SELLER" <?=($bbs_target == "SELLER") ? "selected" : ""?>>판매처</option>
									<option value="SUPPLIER" <?=($bbs_target == "SUPPLIER") ? "selected" : ""?>>공급처</option>
								</select>
							</td>
						</tr>
						<tr class="dis_none tr_target_seller">
							<th>대상 판매처 등급<span class="lb_red">필수</span></th>
							<td class="text_left">
								<?php
								foreach($_vendor_grade_list as $vg){
									$checked = "";
									$col = "bbs_target_vendor_".$vg["vendor_grade"];
									if($$col == "Y"){
										$checked = 'checked="checked"';
									}
									echo '<label><input type="checkbox" class="bbs_target_vendor" name="bbs_target_vendor_'.strtoupper($vg["vendor_grade"]).'" value="Y" '.$checked.'> '.$vg["vendor_grade_name"].'</label>';
								}
								?>
							</td>
						</tr>
						<tr>
							<th>카테고리  <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="bbs_category">
									<option value="NORMAL" <?=($bbs_category == "NORMAL") ? "selected" : ""?>>일반</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>공개범위</th>
							<td class="text_left">
								<label>
									<input type="checkbox" name="bbs_is_main" value="Y" <?=($bbs_is_main == "Y") ? "checked": ""?>> 홈페이지 공개
								</label>
							</td>
						</tr>
						<tr>
							<th>제목  <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="bbs_title" class="w100per" value="<?=$bbs_title?>" />
							</td>
						</tr>
						<tr>
							<th>내용  <span class="lb_red">필수</span></th>
							<td class="text_left">
								<textarea name="bbs_contents" class="w100per"><?=$bbs_contents?></textarea>
							</td>
						</tr>
						<tr>
							<th>첨부파일</th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn-bbs_file_idx_1" id="btn-bbs_file_idx_1">
									파일업로드
								</a>
								<span class="uploaded-file span_bbs_file_idx_1"></span>
								<input type="hidden" name="bbs_file_idx_1" id="bbs_file_idx_1" value="<?=$bbs_file_idx_1?>" />
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:history.back(-1);" class="large_btn red_btn">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/fileupload.js"></script>
<script src="/js/page/help.js"></script>
<script>
	Help.NoticeWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

