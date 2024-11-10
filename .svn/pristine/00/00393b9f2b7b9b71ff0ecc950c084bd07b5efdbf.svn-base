<?php

//Page Info
$pageMenuIdx = 316;     //샘플 출고

include_once "../_init_.php";

$request_idx = $_GET["request_idx"];
$stock_manager = new Stock();
$request_data = $stock_manager->getSampleRequestData($request_idx);
$products = json_decode($request_data["request_products"], true);

foreach ($products as &$product) {
	$available_stocks = $stock_manager->calculateAvailableStock($product["product_option_idx"]);

	$product["available_stock_total"] = 0;
	$product["available_stock_NORMAL"] = 0;
	$product["available_stock_ABNORMAL"] = 0;
	$product["available_stock_BAD"] = 0;

	if ($available_stocks["stock_amount_NORMAL"] && $available_stocks["stock_amount_NORMAL"] > 0) {
		$product["available_stock_NORMAL"] = $available_stocks["stock_amount_NORMAL"];
		$product["available_stock_total"] += $product["available_stock_NORMAL"];
	}

	if ($available_stocks["stock_amount_ABNORMAL"] && $available_stocks["stock_amount_ABNORMAL"] > 0) {
		$product["available_stock_ABNORMAL"] = $available_stocks["stock_amount_ABNORMAL"];
		$product["available_stock_total"] += $product["available_stock_ABNORMAL"];
	}

	if ($available_stocks["stock_amount_BAD"] && $available_stocks["stock_amount_BAD"] > 0) {
		$product["available_stock_BAD"] = $available_stocks["stock_amount_BAD"];
		$product["available_stock_total"] += $product["available_stock_BAD"];
	}

	if ($available_stocks["stock_unit_price"]) {
		$product["stock_unit_price"] = $available_stocks["stock_unit_price"];
	}
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" id="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col style="width: 150px;">
							<col style="width: *;">
						</colgroup>
						<tbody>
						<tr>
							<th>출고자</th>
							<td class="text_left">
								<label>
									<?=$_SESSION["dy_member"]["member_name"]?>
								</label>
							</td>
						</tr>
						<tr>
							<th>요청자</th>
							<td class="text_left">
								<label>
									<?=$request_data["request_member_name"]?>
								</label>
							</td>
						</tr>
						<tr>
							<th>반납예정일</th>
							<td class="text_left">
								<label>
									<?=$request_data["return_due_date"]?>
								</label>
							</td>
						</tr>
						<tr>
							<th>메모</th>
							<td class="text_left w100per">
								<textarea id="request_memo" class="w100per" rows="5" style="height: 100px; " readonly><?=$request_data["request_memo"]?></textarea>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<p class="sub_tit2 mt20">
					샘플 요청 리스트
				</p>
				<div class="tb_wrap grid_tb">
					<table id="table_product_list">
					</table>
					<div id="grid_pager"></div>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:" id="btn_save" class="large_btn blue_btn ">출고</a>
						<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script>
	window.name = 'stock_out_sample_pop';

	function initPop() {
		$("#table_product_list").jqGrid({
			datatype: "local",
			colModel: [
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false},
				{label: '상품 idx', name: 'product_idx', index: 'product_idx', width: 150, sortable: false, hidden: true, key: true},
				{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false},
				{label: '옵션 idx', name: 'product_option_idx', index: 'product_option_idx', width: 150, sortable: false, hidden: true},
				{label: 'stock_unit_price', name: 'stock_unit_price', index: 'product_option_idx', width: 150, sortable: false, hidden: true},
				{label: '정상 재고', name: 'available_stock_NORMAL', index: 'available_stock_NORMAL', width: 60, sortable: false},
				{label: '양품 재고', name: 'available_stock_ABNORMAL', index: 'available_stock_ABNORMAL', width: 60, sortable: false},
				{label: '불량 재고', name: 'available_stock_BAD', index: 'available_stock_BAD', width: 60, sortable: false},
				{label: '요청 수량', name: 'request_amount', index: 'request_amount', width: 60, sortable: false},
				{label: '출고 수량', name: 'out_amount', index: 'out_amount', width: 200, sortable: false, formatter(cv, opt, ro) {
					return '<select id="out_status_' + ro.product_option_idx + '" name="out_status" style="height: 26px;">' +
                            '<option value="NORMAL">정상</option><option value="ABNORMAL">양품</option><option value="BAD">불량</option>' +
                        '</select>' +
                        '<input type="text" class="w100px" id="out_amount_' + ro.product_option_idx + '" name="out_amount[]" value="' + ro.request_amount + '" style="height: 22px;"/>';
				}}
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			viewrecords: true,
			autowidth: true,
			shrinkToFit: true,
			height: 150
		});

		//init grid
        let grid_data = [];
        <?php foreach ($products as &$product) { ?>
        grid_data.push(JSON.parse('<?=json_encode($product, JSON_UNESCAPED_UNICODE)?>'));
        <?php } ?>

        if (grid_data.length > 0) {
        	$.each(grid_data, function(i, o) {
				$("#table_product_list").jqGrid("addRowData", o.product_option_idx, o);
            });
        }

		// 출고 버튼 연결
		$("#btn_save").on("click", function(e) {
			outSample();
		});
	}

	function outSample() {
		let grid_data = $("#table_product_list").jqGrid().getRowData();

		let param = {};
		param.list = [];
		param.mode = "out_sample";
		param.request_idx = <?=$request_idx?>;

		$.each(grid_data, function(i, o) {
			let product_data = {};
			product_data.product_idx = o.product_idx;
			product_data.product_name = o.product_name;
			product_data.product_option_idx = o.product_option_idx;
			product_data.product_option_name = o.product_option_name;
			product_data.request_amount = o.request_amount;
			product_data.out_amount = $("#out_amount_" + o.product_option_idx).val();
			product_data.out_status = $("#out_status_" + o.product_option_idx + " option:selected").val();
			product_data.stock_unit_price = o.stock_unit_price;

			if (Number(product_data.request_amount) !== Number(product_data.out_amount)) {
			    alert(o.product_name + " " + o.product_option_name + "에\n요청한 수량과 출고 수량이 다릅니다.");
			    return;
            }

			if (Number(product_data.out_amount) > Number(o["available_stock_" + product_data.out_status])) {
				alert("출고 수량이 " + $("#out_status_" + o.product_option_idx + " option:selected").text() + " 재고 수량보다 많습니다.");
				return;
            }

			param.list.push(product_data);
		});

		showLoader();

		$.ajax({
			type: "POST",
			url: "/stock/stock_request_sample_proc.php",
			dataType: "json",
			data: param,
		}).done(function(response) {
			hideLoader();

			if (response.result) {
				alert("샘플을 출고하였습니다.");
				window.opener.refreshGrid();
				self.close();
			} else {
				alert(response.msg);
			}
		}).fail(ajaxFailWithHideLoader);
    }

    initPop();
</script>
