<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 광고비관리
 */

//Page Index Setting
$pageMenuNo_L = 7;
$pageMenuNo_M = 0;

//Init
include_once "../../_init_.php";

$today = date('Y-m-d');
$seller_idx = 0;

$C_Settle = new Settle();
?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_header.php"; ?>
	<div class="wrap_main">
		<div class="wrap_page bd_non">
			<div class="wrap_page_in">
				<form name="dyForm" id="dyForm">
					<div class="form_sale_set">
						<div class="page_line">
							<span class="title">날짜</span>
							<span class="select_set">
								<input type="text" name="date_start" id="date_start" class="jqDate w90px" value="" readonly="readonly" />
								~
								<input type="text" name="date_end" id="date_end" class="jqDate w90px" value="" readonly="readonly" />
<!--								<select class="sel_period_preset" id="period_preset_select"></select>-->
							</span>
						</div>
						<div class="page_line">
							<span class="title"></span>
							<div class="btn_set" id="date_select_btn_set">
							</div>
						</div>
						<div class="page_line sellers">
							<span class="title">판매처</span>
							<span class="select_set">
								<select name="product_seller_group_idx" class="product_seller_group_idx w100px" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" id="seller_idx" class="seller_idx w100px" data-selected="0" data-default-value="" data-default-text="전체 판매처">
								</select>
							</span>
						</div>
					</div>
					<a href="javascript:;" id="btn-search" class="search_btn">검색</a>
				</form>
			</div>
		</div>
		<div class="wrap_inner">

		</div>
	</div>
	<script src="../js/page/ad.cost.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>