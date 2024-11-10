<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통코드관리 등록 페이지
 */
//Page Info
$pageMenuIdx = 156;
//Init
include_once "../_init_.php";

$mode = "add";
$idx = $_GET["idx"];

$member_id = "";
$name = "";
$tel = "";
$tel1 = "";
$tel2 = "";
$tel3 = "";
$mobile = "";
$mobile1 = "";
$mobile2 = "";
$mobile3 = "";
$email = "";
$email1 = "";
$email2 = "";
$etc = "";


$C_Code = new Code();

if($idx)
{
	$C_Code = new Code();

	$_view = $C_Code->getCodeData($idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);
	}else{
		put_msg_and_back("존재하지 않는 사용자입니다.");
	}
}


//상위 코드 리스트
$upperCodeList = $C_Code->getParentCode();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
		<div class="content write_page">
			<div class="content_wrap">
				<form name="dyForm" method="post" class="<?php echo $mode?>">
					<input type="hidden" name="mode" value="<?php echo $mode?>" />
					<input type="hidden" name="idx" value="<?php echo $idx?>" />
					<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
					<div class="tb_wrap">
						<table>
							<colgroup>
								<col width="150">
								<col width="*">
							</colgroup>
							<tbody>
							<tr class="">
								<th>상위코드</th>
								<td class="text_left">
									<select name="code_idx" id="code_idx">
										<option value="0">상위코드 없음</option>
										<?php
										foreach($upperCodeList as $cd)
										{
											echo '<option value="'.$cd["idx"].'" '.(($code_idx == $cd["idx"]) ? "selected" : "").'>'.$cd["code_name"].'('.$cd["code"].')'.'</option>';
										}
										?>
									</select>
								</td>
							</tr>
							<tr class="">
								<th>코드 이름 <span class="lb_red">필수</span></th>
								<td class="text_left">
									<input type="text" name="code_name" class="w200px" maxlength="50" value="<?=$code_name?>" />
								</td>
							</tr>
							<tr class="">
								<th>코드 값 <span class="lb_red">필수</span></th>
								<td class="text_left">
									<?php if($mode == "add"){?>
									<input type="text" name="code" id="code_value" class="w200px onlyNumberAlphabet2" maxlength="40" value="<?=$code?>" style="text-transform: uppercase;" />
									<span class="info_txt col_red code_value_check_txt dis_none"></span>
									<?php }else{?>
									<strong><?=$code?></strong>
									<?php }?>
								</td>
							</tr>
							<tr>
								<th>사용여부 <span class="lb_red">필수</span></th>
								<td class="text_left">
									<select name="is_use">
										<option value="Y" <?=($is_use == "Y") ? "selected" : "" ?>>Y</option>
										<option value="N" <?=($is_use == "N") ? "selected" : "" ?>>N</option>
									</select>
								</td>
							</tr>
							</tbody>
						</table>
					</div>
					<div class="btn_set">
						<div class="center">
							<a href="javascript:;" id="btn-save" class="large_btn blue_btn">저장</a>
							<a href="javascript:history.back();" class="large_btn red_btn">취소</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="/js/String.js"></script>
	<script src="/js/FormCheck.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/page/info.code.js"></script>
	<script>
		$(function(){
			Code.CodeWriteInit();
		});
	</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>