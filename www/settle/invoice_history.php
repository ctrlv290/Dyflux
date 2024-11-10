<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 미출고요약표 리스트 JSON
 */
//Page Info
$pageMenuIdx = 256;
//Init
include_once "../_init_.php";

$C_Delivery = new Delivery();
$_delivery_list = $C_Delivery->getDeliveryCodeList();

$period_type                = $_GET["period_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_seller_group_idx   = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx                 = $_GET["seller_idx"];
$product_supplier_group_idx = (isset($_GET["product_supplier_group_idx"])) ? $_GET["product_supplier_group_idx"] : "0";
$supplier_idx               = $_GET["supplier_idx"];
$delivery_code              = (isset($_GET["product_supplier_group_idx"])) ? $_GET["delivery_code"] : "CJGLS";

$C_Settle = new Settle();

if($date_start && $date_end){

	$_list = $C_Settle->getInvoiceHistory($period_type, $date_start, $date_end, $delivery_code, $seller_idx, $supplier_idx);

}


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
							<select name="period_type">
								<option value="invoice_date" <?=($period_type == "invoice_date") ? "selected" : ""?>>송장일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">택배사</span>
							<select name="delivery_code">
								<?php
								foreach ($_delivery_list as $d){
									$selected = ($delivery_code == $d["delivery_code"]) ? "selected" : "";
									echo '<option value="'.$d["delivery_code"].'" '.$selected.'>'.$d["delivery_name"].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
							</select>
						</div>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
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
		<div class="btn_set">
			<p class="sub_tit2">&nbsp;</p>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap">
			<?php
			?>
			<table>
				<thead>
				<tr>
					<th>관리번호</th>
					<th>입력일</th>
					<th>송장번호</th>
					<th>택배사</th>
					<th>작업자</th>
					<th>등록타입</th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach ($_list as $row) {
					$order_idx = $row["order_idx"];
					$cs_regdate = $row["cs_regdate"];
					$invoice_no = $row["invoice_no"];
					$delivery_name = $row["delivery_name"];
					$member_id = $row["member_id"];
					$invoice_reg_type = $row["invoice_reg_type"];

					$invoice_reg_type_han = "";
					switch ($invoice_reg_type){
						case "AUTO" :
							$invoice_reg_type_han = "자동등록";
							break;
						case "CS" :
							$invoice_reg_type_han = "CS등록";
							break;
						case "XLS" :
							$invoice_reg_type_han = "엑셀업로드";
							break;
					}
					?>
					<tr>
						<td><?=$order_idx?></td>
						<td><?=date('Y-m-d H:i:s', strtotime($cs_regdate))?></td>
						<td><?=$invoice_no?></td>
						<td><?=$delivery_name?></td>
						<td><?=$member_id?></td>
						<td><?=$invoice_reg_type_han?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.delivery.js"></script>
<script>
	window.name = 'invoice_history';
	SettleDelivery.InvoiceHistoryInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

