<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 완료 페이지
 * TODO : 판매처 접속 시 매칭 화면에서 벤더사 노출 상품만 검색 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 183;
//Init
include_once "../_init_.php";

//오늘
$now_date = date('Y-m-d');


$C_Order = new Order();
$C_Stock = new Stock();

$_order_list = $C_Order -> getOrderReadyToComplete();

//print_r2($_order_list);
list($usec, $sec) = explode(" ",microtime());
$tmp_randno = (round(((float)$usec + (float)$sec))).rand(1,10000);
$rst = $C_Order ->updateOrderToAcceptTemp($tmp_randno);

//print_r2($rst);
$order_count    = number_format($rst["order_cnt"]);
$accept_count   = number_format($rst["accept_cnt"]);
$shortage_count = number_format($rst["shortage_cnt"]);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="step_wrap">
			<?php if(isDYLogin()) {?>
				<div class="sp_btn_wrap"><a href="order_confirm.php" class="large_btn red_btn btn-order-accept-all">일괄접수처리</a></div>
			<?php } ?>
			<a href="order_list.php" class="large_btn">이전 <i class="fas fa-caret-left"></i></a>
<!--			<a href="javascript:;" class="large_btn btn-next-package">다음 <i class="fas fa-caret-right"></i></a>-->
			<div class="arrow-steps clearfix">
				<div class="step"><span>발주</span></div>
				<div class="step"><span>매칭</span></div>
				<div class="step"><span>합포</span></div>
				<div class="step current"><span>발주완료</span></div>
			</div>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" name="tmp_randno" value="<?=$tmp_randno?>" />
		</form>

		<div class="div_center">
			<p class="sub_tit3">
				발주가 완료되었습니다. (가접수)
			</p>
		</div>
		<div class="tb_wrap ">

			<table class="free_width">
				<colgroup>
					<col width="200" />
					<col width="200" />
					<col width="200" />
				</colgroup>
				<thead>
				<tr>
					<th>주문건</th>
					<th>발주 수량</th>
					<th>재고부족상품</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?=$order_count?></td>
					<td><?=$accept_count?></td>
					<td id="shortage_product"></td>
				</tr>
				</tbody>
			</table>
		</div>
		<p class="sub_desc">
			※ 재고부족 수량은 현재고 기준으로 주문량과 매칭한 것입니다.<br>
			배송은 접수순서에 기준으로 하기 때문에 해당 페이지의 내용과 다를 수 있습니다.
		</p>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_order_matching_pop" title="상품매칭" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/page/order.order.js?v=200511"></script>
<script>
	window.name = 'order_complete';
	Order.OrderCompleteInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

