<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일괄합포제외 페이지
 */
//Page Info
$pageMenuIdx = 204;
//Init
include_once "../_init_.php";

$period_search_type         = $_GET["period_search_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$seller_idx                 = $_GET["seller_idx"] || 0;
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];
$supplier_idx               = implode(",", $_GET["supplier_idx"]);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">발주일</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<select name="hour_start">
								<?php
								for($i=0;$i<24;$i++){
									echo '<option value="'.make2digit($i).'">'.make2digit($i).'</option>';
								}
								?>
							</select>
							<span class="text">:00:00</span>
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select name="hour_end">
								<?php
								for($i=0;$i<24;$i++){
									$selected = ($i == 23) ? 'selected="selected"' : '';
									echo '<option value="'.make2digit($i).'" '.$selected.'>'.make2digit($i).'</option>';
								}
								?>
							</select>
							<span class="text">:59:59</span>
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">주문수량</span>
							<input type="text" name="order_cnt_start" class="onlyNumber w40px" maxlength="4" />
							~
							<input type="text" name="order_cnt_end" class="onlyNumber w40px" maxlength="4" />
						</div>
						<div class="finder_col">
							<span class="text">상품수량</span>
							<input type="text" name="product_option_cnt_start" class="onlyNumber w40px" maxlength="4" />
							~
							<input type="text" name="product_option_cnt_end" class="onlyNumber w40px" maxlength="4" />
						</div>
						<div class="finder_col">
							<label>
								<input type="checkbox" name="include_single" value="Y" />단품주문포함
							</label>
						</div>
						<div class="finder_col">
							<label>
								<input type="checkbox" name="include_soldout" value="Y" />품절
							</label>
						</div>
						<div class="finder_col">
							<label>
								<input type="checkbox" name="include_soldout_temp" value="Y" />일시품절
							</label>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<select name="search_column">
								<option value="product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>옵션명</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="big_btn btn_default">검색</a>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<!--
		<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
		<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
		-->
		<div class="grid_btn_set_top">
			<span><a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a></span>
			<div class="right">
<!--				<label><input type="checkbox" name="" value=""/> <strong>제외된 주문 부분배송 설정</strong></label>-->
<!--				&nbsp;-->
<!--				<label><input type="checkbox" name="" value=""/> <strong>원주문 부분배송 설정</strong></label>-->
<!--				&nbsp;-->
				<label><input type="checkbox" name="" value=""/> <strong>자동합포 금지</strong></label>
				&nbsp;
				<a href="javascript:;" class="btn red_btn btn-order-package-except-batch">일괄합포제외</a>

			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/order.order.js"></script>
<script>
	window.name = 'order_package_except';
	Order.OrderPackageExceptInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

