<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사 상태 변경 팝업
 */
//Page Info
$pageMenuIdx = 162;
//Init
include_once "../_init_.php";

$C_Vendor = new Vendor();

$vendor_idx = $_GET["idx"];

$vendorInfo = $C_Vendor->getVendorData($vendor_idx);

if(!$vendorInfo)
{
	put_msg_and_close("잘못된 접근입니다.");
	exit;
}else{
	extract($vendorInfo);
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="vendor_status_change" />
				<input type="hidden" name="vendor_idx" value="<?php echo $vendor_idx?>" />
				<input type="hidden" name="vendor_status" value="" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>메모</th>
							<td class="text_left">
								<?php
								//벤더사 상태가 승인 또는 반려 일 경우 메모 표시
								if($vendor_status == "VENDOR_APPLY" || $vendor_status == "VENDOR_REJECT") {
									echo nl2br($vendor_status_msg, false);
								?>
								<?php
								}else {
								?>
								<textarea name="vendor_status_msg" id="vendor_status_msg" class="w400px"></textarea>
								<?php
								}
								?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<?php
				//벤더사 상태가 승인 또는 반려 일 변경 불가
				if($vendor_status == "VENDOR_PENDDING") {
				?>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" data-status="VENDOR_APPLY" class="btn-save big_btn blue_btn ">승인</a>
						<a href="javascript:self.close();" class="big_btn gray_btn">취소</a>
						<a href="javascript:;" data-status="VENDOR_REJECT" class="btn-save big_btn red_btn ">반려</a>
					</div>
				</div>
				<?php
				}else {
				?>
					<div class="btn_set">
						<div class="center">
							<a href="javascript:self.close();" class="big_btn gray_btn">닫기</a>
						</div>
					</div>
				<?php
				}
				?>
			</form>
		</div>
	</div>
</div>


<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.vendor.js"></script>
<script>
	Vendor.VendorStatusPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
