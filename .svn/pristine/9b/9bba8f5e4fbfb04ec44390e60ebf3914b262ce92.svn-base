<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자관리 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 155;
//Permission IDX
$permissionMenuIdx = 54;
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
$is_use = "N";

if($idx)
{
	$C_Users = new Users();

	$_view = $C_Users->getUserData($idx);
	if($_view)
	{
		$mode = "mod";
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

$C_SiteMenu = new SiteMenu();
$MenuPermission_List = $C_SiteMenu -> getPermissionMenu($idx);
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
					<table class="permission_table">
						<colgroup>
							<col width="150">
							<col width="200">
							<col width="200">
							<col width="*">
						</colgroup>
						<tbody>
						<?php
						$i = 0;
						foreach($MenuPermission_List as $PItem)
						{
							//$MenuM_List = $C_SiteMenu -> getMenuListForPermission(0, 0);
						?>
						<tr>
							<th >권한 <span class="lb_red">필수</span></th>
							<td class="text_left td_L " data-idx="<?=$PItem["idx_L"]?>">
								<label><input type="checkbox" name="permission_idx[]" class="MenuL_<?=$PItem["idx_L"]?>" data-depth="L" value="<?=$PItem["idx_L"]?>" <?=($PItem["permission_idx_L"]) ? "checked" : "" ?>/><?=$PItem["name_L"]?></label>
							</td>
							<td class="text_left td_M " data-idx="<?=$PItem["idx_M"]?>">
								<label><input type="checkbox" name="permission_idx[]" class="MenuL_<?=$PItem["idx_L"]?> MenuM_<?=$PItem["idx_M"]?>" data-depth="M" data-idx-l="<?=$PItem["idx_L"]?>" value="<?=$PItem["idx_M"]?>"  <?=($PItem["permission_idx_M"]) ? "checked" : "" ?>/><?=$PItem["name_M"]?></label>
							</td>
							<td class="text_left td_S" data-idx="<?=$PItem["idx_S"]?>">
								<?php
								if($PItem["idx_S"] != null) {
									?>
									<label><input type="checkbox" name="permission_idx[]"
									              class="MenuL_<?= $PItem["idx_L"] ?> MenuM_<?= $PItem["idx_M"] ?>"
									              data-depth="S" data-idx-l="<?= $PItem["idx_L"] ?>"
									              data-idx-m="<?= $PItem["idx_M"] ?>"
									              value="<?= $PItem["idx_S"] ?>" <?= ($PItem["permission_idx_S"]) ? "checked" : "" ?>/><?= $PItem["name_S"] ?>
									</label>
									<?php
								}?>
							</td>
						</tr>
						<?php
							$i++;
						}
						?>
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

			$(".permission_table th").eq(0).attr("rowspan", $(".permission_table th").length);
			$(".permission_table th").not(":eq(0)").remove();

			var idx_prev;
			$(".permission_table td.td_L").each(function(i, o){
				var idx = $(this).data("idx");
				var $obj = $(".permission_table td.td_L[data-idx='" + idx + "']");
				var cnt = $obj.length;
				//console.log($(this).data("idx"));
				//console.log(cnt);

				if(idx_prev != idx) {
					$obj.eq(0).attr("rowspan", cnt);
					$obj.not(":eq(0)").remove();
					idx_prev = idx;
				}
			});

			var idx_prev_M;
			$(".permission_table td.td_M").each(function(i, o){
				var idx = $(this).data("idx");
				var $obj = $(".permission_table td.td_M[data-idx='" + idx + "']");
				var cnt = $obj.length;
				//console.log($(this).data("idx"));
				//console.log(cnt);

				if(idx_prev_M != idx) {
					$obj.eq(0).attr("rowspan", cnt);
					$obj.not(":eq(0)").remove();
					idx_prev_M = idx;
				}
			});

			$(".permission_table input[type='checkbox']").on("change", function(){

				var idx = $(this).val();
				var depth = $(this).data("depth");
				//console.log(idx, depth);
				if(depth == "L")
				{
					$(".MenuL_"+idx).prop("checked", $(this).is(":checked"));
				}else if(depth == "M"){
					$(".MenuM_"+idx).prop("checked", $(this).is(":checked"));

					var idx_l = $(this).data("idx-l");

					if($(this).is(":checked")) {
						$(".permission_table input[type='checkbox'][value='" + idx_l + "']").prop("checked", $(this).is(":checked"));
					}else{
						var m_siblings_count = $(".permission_table input[type='checkbox'][data-idx-l='"+idx_l+"']:checked").length;
						if(m_siblings_count == 0) {
							$(".permission_table input[type='checkbox'][value='" + idx_l + "']").prop("checked", $(this).is(":checked"));
						}
					}
				}else if(depth == "S"){

					var idx_m = $(this).data("idx-m");
					if($(this).is(":checked")) {
						$(".permission_table input[type='checkbox'][value='" + idx_m + "']").prop("checked", $(this).is(":checked"));
					}else{
						var s_siblings_count = $(".permission_table input[type='checkbox'][data-idx-m='"+idx_m+"']:checked").length;
						console.log(s_siblings_count);
						if(s_siblings_count == 0) {
							console.log("꺼라");
							$(".permission_table input[type='checkbox'][value='" + idx_m + "']").prop("checked", $(this).is(":checked"));
						}
					}

					var idx_l = $(this).data("idx-l");
					if($(this).is(":checked")) {
						$(".permission_table input[type='checkbox'][value='" + idx_l + "']").prop("checked", $(this).is(":checked"));
					}else{
						var m_siblings_count = $(".permission_table input[type='checkbox'][data-idx-l='"+idx_l+"']:checked").length;
						if(m_siblings_count == 0) {
							$(".permission_table input[type='checkbox'][value='" + idx_l + "']").prop("checked", $(this).is(":checked"));
						}
					}
				}

				checkedCheck();
			});

			var checkedCheck = function(){
				$(".permission_table input[type='checkbox']").each(function(i, o){
					if($(this).is(":checked"))
					{
						$(this).parent().parent().addClass("permission_checked");
					}else{
						$(this).parent().parent().removeClass("permission_checked");
					}
				});
			}

			checkedCheck();

		});
	</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>