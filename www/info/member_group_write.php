<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자관리 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 168;
//Permission IDX
$permissionMenuIdx = 0;
//Init
include_once "../_init_.php";

$mode = "add";
$member_group_idx = $_GET["member_group_idx"];

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
$is_use = "Y";

if($member_group_idx)
{
	$C_MemberGroup = new MemberGroup();

	$_view = $C_MemberGroup->getMemberGroupData($member_group_idx);
	$_member_list = $C_MemberGroup->getMemberGroupUserList($member_group_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);
	}else{
		put_msg_and_back("존재하지 않는 권한그룹입니다.");
	}
}

$C_SiteMenu = new SiteMenu();
$MenuPermission_List = $C_SiteMenu -> getPermissionMenu($member_group_idx);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
		<div class="content write_page">
			<div class="content_wrap">
				<form name="dyForm" method="post" class="<?php echo $mode?>">
					<input type="hidden" name="mode" value="<?php echo $mode?>" />
					<input type="hidden" name="member_group_idx" value="<?php echo $member_group_idx?>" />
					<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
					<input type="hidden" name="member_idx_list" id="member_idx_list" value="" />
					<div class="tb_wrap">
						<table>
							<colgroup>
								<col width="150">
								<col width="*">
							</colgroup>
							<tbody>
							<tr>
								<th>그룹명 <span class="lb_red">필수</span></th>
								<td class="text_left">
									<input type="text" name="member_group_name" id="member_group_name" class="w200px" maxlength="20" value="<?=$member_group_name?>" />
								</td>
							</tr>
							<tr>
								<th>그룹멤버</th>
								<td class="text_left">
									<a href="javascript:;" class="btn green_btn btn-member-group-user-add-pop">멤버추가</a>
									<ul class="group_member_list mt10"></ul>
								</td>
							</tr>
							<tr>
								<th>비고</th>
								<td class="text_left">
									<textarea name="member_group_etc" class="w400px"><?=$member_group_etc?></textarea>
								</td>
							</tr>
							<tr>
								<th>사용여부 <span class="lb_red">필수</span></th>
								<td class="text_left">
									<select name="member_group_is_use">
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
										<label><input type="checkbox" name="permission_idx[]" class="MenuL_<?=$PItem["idx_L"]?> MenuM_<?=$PItem["idx_M"]?>" data-depth="S" data-idx-l="<?=$PItem["idx_L"]?>" data-idx-m="<?=$PItem["idx_M"]?>" value="<?=$PItem["idx_S"]?>"  <?=($PItem["permission_idx_S"]) ? "checked" : "" ?>/><?=$PItem["name_S"]?></label>
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
	<script src="/js/page/info.member.group.js"></script>
	<script>
		$(function(){
			MemberGroup.MemberGroupWriteInit();
			<?php
			if($_member_list){
				foreach($_member_list as $member_one){
			?>
				MemberGroup.MemberGroupUserAdd('<?=$member_one["member_idx"]?>', '<?=$member_one["member_id"]?>', '<?=$member_one["name"]?>');
			<?php
				}
			}
			?>

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