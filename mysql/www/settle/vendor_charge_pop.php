<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 충전금관리 - 충전금 등록 팝업 페이지
 */
//Page Info
$pageMenuIdx = 252;
//Init
include_once "../_init_.php";

$mode = "add";

$C_ManageGroup = new ManageGroup();
$_vendor_list = $C_ManageGroup->getManageGroupMemberList("VENDOR_CHARGE_GROUP", "");
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormChargePop" id="dyFormChargePop" method="post" class="<?=$mode?>" action="vendor_charge_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<div class="tb_wrap" style="overflow-x:visible;">
					<table>
						<colgroup>
							<col width="260">
							<col width="120">
							<col width="120">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>벤더사</th>
							<th>일자&nbsp
                                <a href="javascript:;" id="" class="allDate">
                                    <img src="../images/ico_calander.png" alt="" style="width:13%; height=13%;" />
                                </a>
                                <input type="hidden" id="dp" />
                                <div></div>
                            </th>
							<th>충전금</th>
							<th>비고</th>
						</tr>
						</thead>
						<tbody>
						<?php
						for($i=1;$i<11;$i++) {
							?>
							<tr>
								<td class="text_left">
									<select name="member_idx[]" class="seller_idx">
										<option value="">벤더사를 선택하세요.</option>
										<?php
										foreach($_vendor_list as $s){
											echo '<option value="'.$s["idx"].'">'.$s["name"].'</option>';
										}
										?>
									</select>
								</td>
								<td>
									<input type="text" name="charge_date[]" class="w100per jqDate" value="" readonly="readonly" />
								</td>
								<td>
									<input type="text" name="charge_amount[]" class="w100per money" value="" />
								</td>
								<td>
									<input type="text" name="charge_memo[]" class="w100per" value="" />
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-pop" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.charge.js?v=191219"></script>
<script>
	SettleCharge.VendorChargePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

