<?php
//Page Info
$pageMenuIdx = 291;
//Init
include_once "../_init_.php";

$classProduct = new Product();
$soldOutList = $classProduct->getSoldOutList();
$shortageList = $classProduct->getSoldOutList();
$warningList = $classProduct->getSoldOutList();
?>

<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>

<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<p class="sub_tit2 mt20">품절 제품</p>
			<div class="tb_wrap grid_tb">
				<table id="grid_list_sold_out" style="width: 100%;">
				</table>
			</div>
			<!--<p class="sub_tit2 mt20">재고 부족 제품</p>
			<div class="tb_wrap grid_tb">
				<table id="grid_list_stock_amt_shortage" style="width: 100%;">
				</table>
			</div>
			<p class="sub_tit2 mt20">재고 위험 제품</p>
			<div class="tb_wrap grid_tb">
				<table id="grid_list_stock_amt_warning" style="width: 100%;">
				</table>
			</div>-->
		</div>
	</div>
	<div class="btn_set">
		<div class="center">
			<a href="javascript:self.close();" class="large_btn red_btn">확인</a>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script>
	function gridListInit(id, colModel, jsonList) {
		$("#"+id).jqGrid({
			url: './product_deficiency_list_grid.php?mode=sold_out',
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: colModel,
			rowNum: 1000,
			sortname: 'product_option_soldout_date',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 300,
			loadComplete: function(res){
				console.log(res);
			}
		});
	}

	function init() {
		var colModel = [
			{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
			{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 0, hidden: true},
			{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false},
			{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false},
			{ label: '품절일', name: 'product_option_soldout_date', index: 'product_option_soldout_date', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
			        var date = new Date()
                    var today = date.getFullYear() +'-'+ ("0"+(date.getMonth()+1)).slice(-2) + '-' + ("0"+date.getDate()).slice(-2);
			        if(Common.toDateTimeOnlyDate(rowobject.product_option_soldout_date) == today) {
                        return "<strong>" + Common.toDateTimeOnlyDate(cellvalue) + "</strong>&nbsp;<img src='../images/ico_new.png' alt=''/>"
                    }else {
                        return Common.toDateTimeOnlyDate(cellvalue)
                    }
                }},
			{ label: '비고', name: 'product_option_soldout_memo', index: 'product_option_soldout_memo', width: 100, sortable: false},
		];

		gridListInit("grid_list_sold_out", colModel, <?= json_encode($soldOutList); ?>);
		gridListInit("grid_list_stock_amt_shortage", colModel, <?= json_encode($shortageList); ?>);
		gridListInit("grid_list_stock_amt_warning", colModel, <?= json_encode($warningList); ?>);
	}

	init();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
