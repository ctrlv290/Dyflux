<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 내정보 수정 - 사용자 Include (from myinfo.php)
 */

$mode = "mod_self";
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
$is_use = "N";

if($idx)
{
	$C_Users = new Users();

	$_view = $C_Users->getUserData($idx);
	if($_view)
	{
		$mode = "mod_self";
		extract($_view);

		if($tel){
			list($tel1, $tel2, $tel3) = explode("-", $tel);
		}
		if($mobile){
			list($mobile1, $mobile2, $mobile3) = explode("-", $mobile);
		}
		if($email){
			list($email1, $email2) = explode("@", $email);
		}
	}else{
		put_msg_and_back("존재하지 않는 사용자입니다.");
	}
}
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="mod">
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
						<tr>
							<th>로그인 아이디 <?php if($mode == "add"){?><span class="lb_red">필수</span><?php }?></th>
							<td class="text_left">
								<?php if($mode == "add"){?>
									<input type="text" name="login_id" id="login_id" class="w200px userID" maxlength="12" value="" />
									<span class="info_txt col_red login_id_check_txt"></span>
									<span class="info_txt col_red">(4~12자리 숫자, 영문, -, _ 만 가능)</span>
								<?php }else{ ?>
									<?php echo $member_id;?>
								<?php } ?>
							</td>
						</tr>
						<tr class="mod_pass_btn dis_none">
							<th>로그인 비밀번호</th>
							<td class="text_left">
								<a href="javascript:;" class="btn red_btn btn-password-change">비밀번호 변경</a>
							</td>
						</tr>
						<tr class="mod_pass">
							<th>로그인 비밀번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="password" name="login_pw" id="login_pw" class="w200px" maxlength="12" value="" />
								<span class="info_txt col_red">(4~12자리의 숫자와 문자의 조합)</span>
							</td>
						</tr>
						<tr class="mod_pass">
							<th>비밀번호 확인 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="password" name="login_pw_re" id="login_pw_re" class="w200px" maxlength="12" value="" />
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>이름 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="name" class="w200px" maxlength="20" value="<?=$name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처</th>
							<td class="text_left">
								<select name="tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="tel2" class="w60px onlyNumber" maxlength="4" value="<?=$tel2?>" />
								-
								<input type="text" name="tel3" class="w60px onlyNumber " maxlength="4" value="<?=$tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$mobile2?>" />
								-
								<input type="text" name="mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="email1" class="w150px" value="<?=$email1?>" />
								@
								<input type="text" name="email2" id="email2" class="w150px" value="<?=$email2?>" />
								<select name="email3" id="email3">
									<?php
									foreach($GL_emailCollection as $item){
										echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<textarea name="etc" class="w400px"><?=$etc?></textarea>
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
<script src="/js/page/info.user.js"></script>
<script>
	$(function(){
		User.UserWriteInit();
	});
</script>