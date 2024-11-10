<?php

/**
 * 일괄 접수 처리 페이지 20190701 kyu
 * 발주 부서 요청으로 인한 선택 작업
 */

//Page Info
$pageMenuIdx = 290;

//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"] || 0;

include_once DY_INCLUDE_PATH."/_include_top.php";
include_once DY_INCLUDE_PATH."/_include_header.php";
?>

<div class="container">
    <?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <div class="content">
    	<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" name="include_sum" id="include_sum" value="" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_type">
								<option value="order_regdate">발주일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
                        <div class="finder_col">
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매타입</span>
							<select name="product_sale_type" class="product_sale_type">
								<option value="">전체</option>
								<option value="SELF" <?=($product_sale_type == "SELF") ? "selected" : ""?>>사입/자체</option>
								<option value="CONSIGNMENT" <?=($product_sale_type == "CONSIGNMENT") ? "selected" : ""?>>위탁</option>
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
        <div class="grid_btn_set_top">
            <a href="javascript:;" class="large_btn red_btn btn-accept-cheacked">&nbsp;&nbsp;&nbsp;적용&nbsp;&nbsp;&nbsp;</a>
			<div class="right">
                <p>선택한 주문들을 가접수에서 접수상태로 변경합니다.</p>
			</div>
		</div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager">
            </div>
        </div>
    </div>
</div>

<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/column_const.js"></script>
<script src="/js/page/order.order.js"></script>
<script>
    window.name = 'order_confirm';
	
	function orderAcceptCheckedList(checkedList) {
		showLoader();

		var p_url = "/order/order_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_accept_checked_confirm";
		dataObj.order_pack_idx_list = checkedList;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.data == 0) {
				alert("오류가 발생하여 처리하지 못했습니다. 다시 시도해주세요.")
			} else {
				alert("요청 주문 " + response.order_count + "건에 포함된 " + response.data + "건의 주문이 접수처리 되었습니다.");
				location.reload();
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert("오류가 발생했습니다. 잠시 후에 다시 시도해주세요.\nerror : " + textStatus);
			hideLoader();
		});
	}

	orderBatchConfirmInit();

	function orderBatchConfirmInit() {
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		orderBatchConfirmGridInit();
	};

	function orderBatchConfirmGridInit() {
		//가접수 상태 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_confirm_grid.php',
			mtype: "GET",
			postData:{
				param: $("#searchForm").serialize()
			},
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: [
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 40, sortable: true},
				{ label: '합포번호', name: 'order_pack_idx', index: 'order_pack_idx', width: 40, sortable: false, hidden: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 50, sortable: true},
				{ label: '상품 코드', name: 'market_product_no', index: 'market_product_no', width: 60, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 60, sortable: true},
				{ label: '판매 타입', name: 'code_name', index: 'code_name', width: 30, sortable: true},
				{ label: '상품명', name: 'market_product_name', index: 'market_product_name', width: 200, sortable: false, align: 'left'},
				{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 200, sortable: false, align: 'left'},
				{ label: '수량', name: 'order_cnt', index: 'order_cnt', width: 20, sortable: false},
			],
			rowNum: 10000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			multiselect: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(completed_data){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(event){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				Order.OrderSearchListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			Order.OrderSearchListSearch();
		});
	};

	$(".btn-accept-cheacked").on("click", function() {
		var selRowId = $("#grid_list").getGridParam('selarrrow');
		
		if(selRowId == null || selRowId.length == 0){
			alert('선택된 내용이 없습니다.');
			return;
		}

		var msg = selRowId.length + "건의 주문을 접수 처리하시겠습니까?";
		if(confirm(msg)) {
			var idx_list = new Array();

			$.each(selRowId, function(i, o){
				var rowData =$("#grid_list").getRowData(o);
				idx_list.push(rowData.order_pack_idx);
			});

			orderAcceptCheckedList(idx_list);
		}
	});

</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>