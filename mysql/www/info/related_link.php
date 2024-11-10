<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 관리자 링크 모음 수정
 */
//Page Info
$pageMenuIdx = 283;
//Init
include_once "../_init_.php";

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="mod">
				<div class="tb_wrap">
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 기준정보관리</p>
					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
							<col style="width: 150px;">
							<col style="width: 100px;">
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>판매처 변경이력</th>
							<td><a href="javascript:;" class="btn btn-seller">바로가기</a></td>
							<th>벤더사 변경이력</th>
							<td><a href="javascript:;" class="btn btn-vendor">바로가기</a></td>
							<th>공급처 변경이력</th>
							<td><a href="javascript:;" class="btn btn-supplier">바로가기</a></td>
						</tr>
						</tbody>
					</table>

					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>사용자 변경이력</th>
							<td><a href="javascript:;" class="btn btn-user">바로가기</a></td>
							<th>로그인 이력</th>
							<td><a href="javascript:;" class="btn btn-login">바로가기</a></td>
						</tr>
						</tbody>
					</table>
					<br><br>
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 상품관리</p>
					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>상품목록 변경이력</th>
							<td><a href="javascript:;" class="btn btn-product">바로가기</a></td>
							<th>매칭정보 삭제로그</th>
							<td><a href="javascript:;" class="btn btn-matching">바로가기</a></td>
						</tr>
						</tbody>
					</table>
					<br><br>
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 주문배송관리</p>
					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>주문삭제이력</th>
							<td><a href="javascript:;" class="btn btn-order">바로가기</a></td>
						</tr>
						</tbody>
					</table>
					<br><br>
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 재고관리</p>
					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>재고로그 조회</th>
							<td><a href="javascript:;" class="btn btn-stock">바로가기</a></td>
							<th>입고지연이력</th>
							<td><a href="javascript:;" class="btn btn-delay">바로가기</a></td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/main.js"></script>
<script>
	$(function(){
		$(".btn-seller").on("click", function(){
			Common.changeLogViewerPopup("seller");
		});
		$(".btn-vendor").on("click", function(){
			Common.changeLogViewerPopup("vendor");
		});
		$(".btn-supplier").on("click", function(){
			Common.changeLogViewerPopup("supplier");
		});
		$(".btn-user").on("click", function(){
			Common.changeLogViewerPopup("user");
		});
		$(".btn-login").on("click", function(){
			window.open("/info/member_login_log.php");
		});
		$(".btn-product").on("click", function(){
			Common.changeLogViewerPopup("product");
		});
		$(".btn-matching").on("click", function(){
			window.open("/product/product_matching_delete_list.php");
		});
		$(".btn-order").on("click", function(){
			Common.newWinPopup("/order/order_batch_delete_log_pop.php", 'order_batch_delete_log_pop', 800, 720, 'no');
		});
		$(".btn-stock").on("click", function(){
			window.open("/stock/stock_log_list.php");
		});
		$(".btn-delay").on("click", function(){
			Common.newWinPopup("/stock/stock_due_delay_list_pop.php", 'stock_due_delay_list_pop', 1200, 750, 'yes');
		});
	});
</script>