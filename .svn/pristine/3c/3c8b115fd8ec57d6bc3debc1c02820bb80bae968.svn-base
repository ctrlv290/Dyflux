<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주서 포맷 설정 팝업 창
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";


$mode                   = "save";
$seller_idx             = $_POST["seller_idx"];
if($seller_idx == "") {
	// 강제 편집을 위해 - ssawoona
	$g_seller_idx             = $_GET["seller_idx"];
}
$C_Seller = new Seller();

if($g_seller_idx == "") {
	$_seller_info = $C_Seller->getUseSellerAllData($seller_idx);

	if (!$_seller_info) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: text/html; charset=UTF-8');
		die("Error");
	}
} else {
	$seller_idx = $g_seller_idx;
}
$C_Order = new Order();

//기본 발주서 포맷 불러오기
$_list = $C_Order -> getOrderFormatDefaultWithSeller($seller_idx);

if($g_seller_idx != "") {
	include_once DY_INCLUDE_PATH."/_include_top_popup.php";
	echo "<script src='/js/main.js'></script>";
	echo "<script src='/js/amcharts/core.js'></script>";
	echo "<script src='/js/amcharts/charts.js'></script>";
	echo "<script src='/js/amcharts/lang/ko_KR.js'></script>";
	echo "<script src='/js/amcharts/themes/animated.js'></script>";
	echo "<script src='/js/page/order.order.js'></script>";
}
?>

<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="seller_idx" value="<?php echo $seller_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>판매처코드</th>
							<td class="text_left"><?=$seller_idx?></td>
						</tr>
						<tr>
							<th>판매처명</th>
							<td class="text_left"><?=$_seller_info["seller_name"]?></td>
						</tr>
						</tbody>
					</table>
					<br>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>기준헤더</th>
							<th>발주서 헤더</th>
						</tr>
						<?php
						foreach($_list as $row) {
							?>
							<tr>
								<th><?= $row["order_format_default_header_name"] ?></th>
								<td class="text_left">
									<input type="hidden" name="order_format_default_idx[]" class="w100per" value="<?=$row["order_format_default_idx"]?>" />
									<input type="hidden" name="order_format_seller_idx[]" class="w100per" value="<?=$row["order_format_seller_idx"]?>" />
									<input type="text" name="order_format_seller_header_name[]" class="w100per" value="<?=$row["order_format_seller_header_name"]?>" />

									<?php if($row["order_format_default_header_name"] == "주문번호"){?>
										<br><span class="info_txt col_red">'auto_order_no' 입력 시 자동 주문번호 발생</span>
									<?php }elseif($row["order_format_default_header_name"] == "상품코드"){?>
										<br><span class="info_txt col_red">'auto_product_code' 입력 시 자동 상품코드 발생</span>
									<?php }elseif($row["order_format_default_header_name"] == "정산금액(부가세미포함)"){?>
                                        <br><span class="info_txt col_red">'정산금액 우선 적용</span>
									<?php } ?>
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
						<a href="javascript:;" id="btn-save-format" class="large_btn blue_btn ">저장</a>
						<a href="javascript:;" class="large_btn red_btn btn-order-format-write-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	Order.OrderFormatSellerPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

