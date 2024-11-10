<?php

//Page Info
$pageMenuIdx = 314;

//Init
include_once "../_init_.php";
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
							<span class="text">요청일</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:" id="btn_searchBar" class="wide_btn btn_default">검색</a>
						</div>
					</div>
				</div>
			</div>
		</form>
		<div class="grid_btn_set_top">
			<a href="javascript:" class="btn" id="btn_request_sample">신규요청</a>
			<div class="right">
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
<script src="/js/String.js"></script>
<script src="/js/page/common.function.js"></script>

<script>
	window.name = 'stock_request_sample';

	function initPage() {
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		// 신규 샘플 요청 연결
		$('#btn_request_sample').on('click', function() {
			openRequestPop();
		});

		let grid = $("#grid_list");
		grid.jqGrid({
			url: './stock_request_sample_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel:  [
				{label: '요청번호', name: 'request_idx', index: 'request_idx', width: 0, sortable: false, hidden: true},
				{label: '상태', name: 'status', index: 'status', width: 60, sotrable: true},
				{label: '요청자', name: 'request_member_name', index: 'request_member_name', width: 80, sortable: true},
				{label: '요청일', name: 'request_date', index: 'request_date', width: 140, sortable: true},
				{label: '샘플리스트', name: 'request_products', index: 'request_products', width: 340, sortable: false, formatter: function(cv, opt, ro){
						return jsonToTable(cv);
					}},
				{label: '출고자', name: 'out_member_name', index: 'out_member_name', width: 80, sortable: true},
				{label: '출고일', name: 'out_date', index: 'out_date', width: 120, sortable: true},
				{label: '메모', name: 'request_memo', index: 'request_memo', width: 160, sortable: false},
				{label: '처리', name: 'process', width: 140, sortable: false, formatter: function(cv, opt, ro){
						let btnSet = '';
						if (ro.status === '요청') {
							btnSet += '<a href="javascript:" class="xsmall_btn btn_out_sample" data-request_idx="' + ro.request_idx + '">출고</a>&nbsp;';
							btnSet += '<a href="javascript:" class="xsmall_btn btn_cancel_sample_request red_btn" data-request_idx="' + ro.request_idx + '">취소</a>';
						} else if (ro.status === '출고') {
							btnSet += '<a href="javascript:" class="xsmall_btn btn_return_sample" data-request_idx="' + ro.request_idx + '">반납</a>';
						} else if (ro.status === '반납') {
							btnSet = '완료';
                        }
						return btnSet;
					}}
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'request_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//출고 처리
				$(".btn_out_sample").on("click", function(){
					openOutSamplePop($(this).data("request_idx"));
				});

				//반납 처리
				$(".btn_return_sample").on("click", function(){
					returnSample($(this).data("request_idx"));
				});

				//취소 처리
				$(".btn_cancel_sample_request").on("click", function(){
					cancelSampleRequest($(this).data("request_idx"));
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_due_list", grid);
			},
			resizeStop: function(){
				//컬럼 사이즈 저장
				let colModel = grid.jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(colModel, "stock_due_list");
			}
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop").on("keyup", function(event){
			let keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode === 13) {
				event.preventDefault();
				refreshGrid();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			refreshGrid();
		});
	}

	function openRequestPop(request_idx) {
		let url = '/stock/stock_request_sample_pop.php';
		if (request_idx !== undefined) {
			url += '?request_idx=' + request_idx;
		}
		Common.newWinPopup(url, 'stock_request_sample_pop', 700, 550, 'yes');
	}

	function openOutSamplePop(request_idx) {
        Common.newWinPopup('/stock/stock_out_sample_pop.php?request_idx=' + request_idx, 'stock_out_sample_pop', 800, 600, 'yes');
	}

	function returnSample(request_idx) {
        if (confirm("샘플을 반납하시겠습니까?")) {
			showLoader();

			$.ajax({
				type: "POST",
				url: "/stock/stock_request_sample_proc.php",
				dataType: "json",
				data: {
					mode: "return_sample",
					request_idx: request_idx
				},
			}).done(function(response) {
				hideLoader();

				if (response.result) {
					alert("반납 요청하였습니다.");
					refreshGrid();
				} else {
					alert(response.msg);
				}
			}).fail(ajaxFailWithHideLoader);
        }
	}

	function cancelSampleRequest(request_idx) {
		if (confirm("샘플 요청을 취소하시겠습니까?")) {
			showLoader();

			$.ajax({
				type: "POST",
				url: "/stock/stock_request_sample_proc.php",
				dataType: "json",
				data: {
					mode: "cancel_sample_request",
                    request_idx: request_idx
                },
			}).done(function(response) {
				hideLoader();

				if (response.result) {
					alert("취소되었습니다.");
					refreshGrid();
				} else {
					alert(response.msg);
				}
			}).fail(ajaxFailWithHideLoader);
        }
    }

	function jsonToTable(jsonObj) {
		let tableHtml = '<table class="sample_list_table no_border" id=""><colgroup><col width="*"><col width="60"></colgroup><tbody>';
		let parsedObj = JSON.parse(jsonObj);

		$.each(parsedObj, function(i, o) {
			tableHtml += '<tr><td>' + o.product_name + ' ' + o.product_option_name + '</td><td>' + o.request_amount + '개</td></tr>';
        });
		tableHtml += '</tbody></table>';
		return tableHtml;
	}

	function refreshGrid() {
		$("#grid_list").trigger("reloadGrid");
    }

	initPage();
</script>

<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>
