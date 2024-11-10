<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 반품통계 페이지
 */
//Page Info
$pageMenuIdx = 218;
//Init
include_once "../_init_.php";

$C_CS = new CS();

$_list = $C_CS->getCSReturnStatistics();
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup cs_order_return_popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<div class="tb_wrap">
			<table>
				<thead>
				<tr>
					<th rowspan="2">날짜</th>
					<th colspan="6">취소</th>
					<th colspan="5">교환</th>
				</tr>
				<tr>
					<th>반품-취소(환불)</th>
					<th>반품-불량</th>
					<th>반품-오배송</th>
					<th>취소-분실</th>
					<th>취소-상품품절</th>
					<th>취소-배송지연</th>
					<th>단순교환</th>
					<th>불량교환</th>
					<th>오배송교환</th>
					<th>품절교환</th>
					<th>상품교환</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach($_list as $row) {
				?>
				<tr>
					<td><?=$row["date"]?></td>
					<td><?=$row["RETURN_REFUND"]?></td>
					<td><?=$row["RETURN_POOR"]?></td>
					<td><?=$row["RETURN_DELIVERY_ERR"]?></td>
					<td><?=$row["CANCEL_LOSS"]?></td>
					<td><?=$row["CANCEL_SOLDOUT"]?></td>
					<td><?=$row["CANCEL_DELIVERY_DELAY"]?></td>
					<td><?=$row["EXCHANGE_NORMAL"]?></td>
					<td><?=$row["EXCHANGE_POOR"]?></td>
					<td><?=$row["EXCHANGE_DELIVERY_ERR"]?></td>
					<td><?=$row["EXCHANGE_SOLDOUT"]?></td>
					<td><?=$row["EXCHANGE_PRODUCT_CHANGE"]?></td>
				</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

