<?php

//Page Info
$pageMenuIdx = 315;     //신규 샘플 요청

include_once "../_init_.php";

$mode = "new_sample_request";
$today = date("Y-m-d");
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
                            <th>요청자</th>
                            <td class="text_left">
                                <label>
									<?=$_SESSION["dy_member"]["member_name"]?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>반납예정일</th>
                            <td class="text_left">
                                <label>
                                    <input type="text" id="return_due_date" name="return_due_date" class="jqDateDynamic w80px" readonly="readonly" value="<?=$today?>">
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>메모</th>
                            <td class="text_left w100per">
                                <textarea id="request_memo" class="w100per" rows="5" style="height: 100px;"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
				<p class="sub_tit2 mt20">
					샘플 상품 리스트 &nbsp&nbsp&nbsp<a href="javascript:" class="btn" id="btn_add_option">상품추가</a>
				</p>
                <div class="tb_wrap grid_tb">
                    <table id="table_product_list">
                    </table>
                    <div id="grid_pager"></div>
                </div>
				<div class="btn_set">
					<div class="center">
                        <a href="javascript:" id="btn_save" class="large_btn blue_btn ">저장</a>
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
	window.name = 'stock_request_sample_pop';

	function initPopup() {
		$("#table_product_list").jqGrid({
			datatype: "local",
            colModel: [
				{ label: '삭제', name: 'btn_set', index: 'btn_set', width: 60, sortable: false, formatter: function(cv, opt){
					return ' <a href="javascript:" class="xsmall_btn red_btn btn-delete-add-selected" data-rowid="'+opt.rowId+'">삭제</a>';
				}},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false},
				{label: '상품 idx', name: 'product_idx', index: 'product_idx', width: 150, sortable: false, hidden: true, key: true},
				{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false},
				{label: '옵션 idx', name: 'product_option_idx', index: 'product_option_idx', width: 150, sortable: false, hidden: true},
				//{label: '현 재고 수량', name: 'current_amount', index: 'current_amount', width: 100, sortable: false},
				{label: '요청 수량', name: 'request_amount', index: 'request_amount', width: 100, sortable: false, formatter(cv, opt, ro) {
                    return '<input type="text" class="w100per" id="request_amount_' + ro.product_option_idx + '" name="request_amount[]" value="1" />';
				}},
            ],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			sortname: 'product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){
			},
			afterInsertRow : function(rowid){
				$("#"+rowid + " .btn-delete-add-selected").on("click", function() {
					$("#table_product_list").jqGrid("delRowData", rowid);
				});
			}
        });

		//상품 추가 버튼 바인딩
		$("#btn_add_option").on("click", function() {
			Common.newWinPopup("/common/product_search_pop.php?callback=onAddedProductOption&auto_close=false&product_sale_type=SELF", 'product_search_pop', 800, 500, 'yes');
		});

		//
        $("#btn_save").on("click", function(e) {
			requestSample();
        });

		Common.setDatePickerForDynamicElement($('.jqDateDynamic'));
    }

    initPopup();

	function onAddedProductOption(products_data) {
		let ids = $("#table_product_list").jqGrid().getDataIDs();
		$.each(products_data.list, function(i, o) {
			if ($.inArray(o.product_option_idx, ids) === -1)
			    $("#table_product_list").jqGrid('addRowData', o.product_option_idx, o);
        });
	}

	function requestSample() {
        let grid_data = $("#table_product_list").jqGrid().getRowData();

        if (grid_data.length === 0) {
        	alert("선택된 상품이 없습니다. 상품을 선택해주세요.");
        	return;
        }

        let param = {};
        param.list = [];
        param.mode = "new_sample_request";
		param.request_memo = $("#request_memo").val();
        param.return_due_date = $("#return_due_date").val();

        $.each(grid_data, function(i, o) {
            let product_data = {};
			product_data.product_idx = o.product_idx;
			product_data.product_name = o.product_name;
            product_data.product_option_idx = o.product_option_idx;
			product_data.product_option_name = o.product_option_name;
            product_data.request_amount = $("#request_amount_" + o.product_option_idx).val();

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
				alert("신규 샘플 출고를 요청했습니다.");
				window.opener.refreshGrid();
				self.close();
            } else {
				alert(response.msg);
			}
        }).fail(ajaxFailWithHideLoader);
	}
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>