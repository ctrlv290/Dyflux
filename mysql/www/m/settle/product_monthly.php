<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 매출관리
 */

//Page Index Setting
$pageMenuNo_L = 3;
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
							<span class="title">발주일</span>
							<span class="select_set">
								<select name="date_start_year" id="period_start_year_input">
									<?php
									for($i = 2018;$i<=date('Y');$i++){
										$selected = ($i == date('Y')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<select name="date_start_month" id="period_start_month_input">
									<?php
									for($i = 1;$i<=12;$i++){
										$selected = ($i == date('m')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<span class="wave_ico">~</span>
								<select name="date_end_year" id="period_end_year_input">
									<?php
									for($i = 2018;$i<=date('Y');$i++){
										$selected = ($i == date('Y')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<select name="date_end_month" id="period_end_month_input">
									<?php
									for($i = 1;$i<=12;$i++){
										$selected = ($i == date('m')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
							</span>
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
						<div class="page_line sellers">
							<span class="title">공급처</span>
							<span class="select_set">
								<select name="product_supplier_group_idx" class="product_supplier_group_idx w100px" data-selected="0">
								<option value="0">전체그룹</option>
								</select>
								<select name="supplier_idx" id="supplier_idx" class="supplier_idx w100px" data-selected="0" data-default-value="" data-default-text="전체 공급처">
							</select>
							</span>
						</div>
						<div class="page_line sellers">
							<span class="title">조회</span>
							<span class="select_set">
								<select name="value_view">
									<option value="c" <?=($value_view == "c") ? "selected" : ""?>>수량조회</option>
									<option value="p" <?=($value_view == "p") ? "selected" : ""?>>원가조회</option>
									<option value="s" <?=($value_view == "s") ? "selected" : ""?>>판매가조회</option>
								</select>
							</span>
						</div>
						<div class="page_line names">
							<select id="" name="search_column" title="" class="">
								<option value="product_name">상품명</option>
								<option value="product_option_name">옵션</option>
								<option value="product_name_option_name">상품명+옵션</option>
								<option value="P.product_idx">상품코드</option>
								<option value="PO.product_option_idx">상품옵션코드</option>
								<option value="product_supplier_name">공급처 상품명</option>
								<option value="product_supplier_option">공급처 옵션</option>
							</select>
							<input type="text" name="search_keyword" maxlength="50" class="" />
						</div>
					</div>
					<a href="javascript:;" id="btn-search" class="search_btn">검색</a>
				</form>
			</div>
		</div>
		<div class="wrap_inner">
			<dl>
				<dd class="sum_total">판매 총액 :</dd>
			</dl>
			<div class="wrap_scroll mt20 table_result">

			</div>
		</div>
	</div>
	<script src="../js/page/product.monthly.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>