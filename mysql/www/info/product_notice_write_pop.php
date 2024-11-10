<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 170;
//Permission IDX
$pagePermissionIdx = 60;
//Init
include_once "../_init_.php";


$mode = "add";
$product_notice_idx = $_GET["product_notice_idx"];
$product_notice_title = "";
$product_notice_1_use = "";
$product_notice_1_title = "";
$product_notice_2_use = "";
$product_notice_2_title = "";
$product_notice_3_use = "";
$product_notice_3_title = "";
$product_notice_4_use = "";
$product_notice_4_title = "";
$product_notice_5_use = "";
$product_notice_5_title = "";
$product_notice_6_use = "";
$product_notice_6_title = "";
$product_notice_7_use = "";
$product_notice_7_title = "";
$product_notice_8_use = "";
$product_notice_8_title = "";
$product_notice_9_use = "";
$product_notice_9_title = "";
$product_notice_10_use = "";
$product_notice_10_title = "";
$product_notice_11_use = "";
$product_notice_11_title = "";
$product_notice_12_use = "";
$product_notice_12_title = "";
$product_notice_13_use = "";
$product_notice_13_title = "";
$product_notice_14_use = "";
$product_notice_14_title = "";
$product_notice_15_use = "";
$product_notice_15_title = "";
$product_notice_16_use = "";
$product_notice_16_title = "";
$product_notice_17_use = "";
$product_notice_17_title = "";
$product_notice_18_use = "";
$product_notice_18_title = "";
$product_notice_19_use = "";
$product_notice_19_title = "";
$product_notice_20_use = "";
$product_notice_20_title = "";


$C_ProductNotice = new ProductNotice();

if($product_notice_idx)
{
	$_view = $C_ProductNotice->getProductNoticeData($product_notice_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);
	}else{
		put_msg_and_back("존재하지 않는 상품정보제공고시 입니다.");
	}
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="product_notice_idx" value="<?php echo $product_notice_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>제목</th>
							<td class="text_left">
								<input type="text" name="product_notice_title" class="w400px" maxlength="100" value="<?=$product_notice_title?>" />
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
						<?php
						for($i=1;$i<21;$i++){
							//${"product_notice_" . $i . "_use"};
							//${"product_notice_" . $i . "_title"};
						?>
						<tr>
							<th>항목명 <?=$i?></th>
							<td class="text_left">
								<label><input type="checkbox" name="product_notice_<?=$i?>_use" value="Y" <?=(${"product_notice_" . $i . "_use"} == "Y") ? "checked" : ""?> />사용</label>
								<input type="text" name="product_notice_<?=$i?>_title" class="w400px" value="<?=${"product_notice_" . $i . "_title"}?>" />
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
<script src="/js/page/info.product.notice.js"></script>
<script>
	ProductNotice.ProductNoticeWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
