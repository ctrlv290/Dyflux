<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 택배사 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 225;
//Permission IDX
$pagePermissionIdx = 54;
//Init
include_once "../_init_.php";


$mode = "add";
$delivery_idx             = $_GET["delivery_idx"];
$delivery_code       = "";
$tracking_url        = "";
$delivery_is_use        = "Y";


$C_Delivery = new Delivery();

$_delivery_list = $C_Delivery->getDeliveryCodeListExceptReg();

if($delivery_idx)
{
	$_view = $C_Delivery->getDeliveryData($delivery_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);

	}else{
		put_msg_and_back("존재하지 않는 택배사입니다.");
	}
}
//print_r2($_view);
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormDelivery" id="dyFormDelivery" method="post" class="<?php echo $mode?>" action="delivery_proc.php">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="delivery_idx" value="<?php echo $delivery_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<?php if($mode == "add") { ?>
						<tr>
							<th>택배사 이름 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="delivery_code" id="delivery_code">
									<option value="">택배사를 선택해주세요.</option>
									<?php
									foreach($_delivery_list as $row){
										echo '<option value="'.$row["delivery_code"].'">'.$row["delivery_name"].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<?php }else{ ?>
							<tr>
								<th>택배사 이름</th>
								<td class="text_left">
									<?=$delivery_name?>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<th>배송추적 URL <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="tracking_url" id="tracking_url" class="w400px" maxlength="800" value="<?=$tracking_url?>" />
								<div>
									<span class="info_txt col_red">{{송장번호}} 입력 시 송장번호로 자동 치환</span>
								</div>
								<div>
									<span class="info_txt col_red">예) https://www.doortodoor.co.kr/parcel/doortodoor.do?invc_no={{송장번호}}</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>사용여부 </th>
							<td class="text_left">
								<label><input type="radio" id="delivery_is_use_y" name="delivery_is_use" value="Y" <?=($delivery_is_use == "Y") ? "checked" : ""?> /> Y</label>
								<label><input type="radio" id="delivery_is_use_n" name="delivery_is_use" value="N" <?=($delivery_is_use == "N") ? "checked" : ""?>/> N</label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.delivery.js"></script>
<script>
	Delivery.DeliveryWritePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
