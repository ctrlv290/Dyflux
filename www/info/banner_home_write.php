<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 홈 배너 추가/수정
 */

//Page Info
$pageMenuIdx = 282;
//Init
include_once "../_init_.php";

$C_Banner = new Banner();

$mode                = "add";
$banner_click_url    = "";
$banner_click_target = "_self";
$banner_use_period   = "N";
$banner_period_start = "";
$banner_period_end   = "";
$banner_sort         = "";
$banner_is_use       = "Y";
$banner_type         = "HOME";

$max_sort = $C_Banner -> getBannerMaxSort($banner_type);
$banner_sort = $max_sort;

$banner_idx = $_GET["banner_idx"];
if($banner_idx){
	$_view = $C_Banner->getBannerDetail($banner_idx);
	if($_view){
		$mode = "mod";
		extract($_view);
	}else{
		put_msg_and_back("잘못된 접근입니다.");
	}
}

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" id="dyForm" method="post" class="<?=$mode?>" action="banner_home_proc.php" enctype="multipart/form-data">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="banner_idx" value="<?=$banner_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>이미지</th>
							<td class="text_left">
								<?php
								if($mode == "mod"){
									echo '<a href="'.DY_BANNER_FILE_URL.'/'.$banner_image.'" data-lightbox="banner"><img src="'.DY_BANNER_FILE_URL.'/'.$banner_image.'" style="height: 100px;" /></a><br><br>';
								}
								?>
								<input type="file" name="banner_image" />
								<br>
								<span class="info_txt col_red">배너 사이즈 : 500px x 500px</span><br>
								<span class="info_txt col_red">JPG, PNG, GIF 만 가능</span>
							</td>
						</tr>
						<tr>
							<th>클릭 URL</th>
							<td class="text_left">
								<input type="text" name="banner_click_url" class="w100per" value="<?=$banner_click_url?>" />
								<br>
								<span class="info_txt col_red">빈값일 경우 클릭이 없는 배너 (입력 시 외루 사이트 링크 일 경우 http:// 필수 입력)</span>
							</td>
						</tr>
						<tr>
							<th>클릭 타겟</th>
							<td class="text_left">
								<select name="banner_click_target">
									<option value="_self" <?=($banner_click_target == "_self" ? "selected" : "")?> >본창</option>
									<option value="_blank" <?=($banner_click_target == "_blank" ? "selected" : "")?> >새창</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>배너기간</th>
							<td class="text_left">
								<label><input type="radio" name="banner_use_period" value="N" <?=($banner_use_period == "N" ? "checked" : "")?> /> 사용안함 </label>
								<label><input type="radio" name="banner_use_period" value="Y" <?=($banner_use_period == "Y" ? "checked" : "")?> /> 사용함 </label>
							</td>
						</tr>
						<tr class="period_tr">
							<th>배너기간</th>
							<td class="text_left">
								시작일 : <input type="text" name="banner_period_start" class="w100px jqDate" value="<?=$banner_period_start?>" />
								~
								종료일 : <input type="text" name="banner_period_end" class="w100px jqDate" value="<?=$banner_period_end?>" />
							</td>
						</tr>

						<tr>
							<th>배너순서</th>
							<td class="text_left">
								<input type="text" name="banner_sort" class="w40px onlyNumber" value="<?=$banner_sort?>" <?=($mode == "mod") ? 'readonly="readonly"' : ''?> />
							</td>
						</tr>
						<tr>
							<th>사용여부</th>
							<td class="text_left">
								<label><input type="radio" name="banner_is_use" value="Y"  <?=($banner_is_use == "Y") ? "checked" : ""?>/> Y</label>
								<label><input type="radio" name="banner_is_use" value="N"  <?=($banner_is_use == "N") ? "checked" : ""?> /> N</label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:history.back();" class="large_btn red_btn btn-cancel">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/main.js"></script>
<script src="/js/page/info.banner.js"></script>
<script>
	Banner.BannerWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

