<?php
//Page Info
$pageMenuIdx = 313;

include_once "../_init_.php";

$date = date('Y-m-d');
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap" style="margin-bottom: 10px;">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">해당일</span>
                            <label for="period_preset_input">
                                <input type="text" name="target_date" id="period_preset_input" class="w80px jqDate " value="<?=$date;?>" readonly="readonly" />
                            </label>
						</div>
						<div class="finder_col">
							<span class="text">입/출 구분</span>
                            <label>
                                <select name="stock_inout_type" class="stock_inout_type" data-selected="">
                                    <option value="all">전체</option>
                                    <option value="in">입고</option>
                                    <option value="out">출고</option>
                                </select>
                            </label>
                        </div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
                            <label>
                                <select name="search_column">
                                    <option value="all">전체</option>
                                    <option value="product_name">상품명</option>
                                    <option value="product_option_name">상품옵션명</option>
                                </select>
                            </label>
                            <label>
                                <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어"/>
                            </label>
                        </div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:" id="btn_searchBar" class="big_btn btn_default">검색</a>
						</div>
					</div>
				</div>
			</div>
		</form>
        <p class="sub_desc">
            현재 자산 합계 : <span class="strong" id="current_stock_value_total"></span>원 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            당일 자산 합계 : <span class="strong" id="until_today_stock_value_total"></span>원
        </p>
        <div class="grid_btn_set_top">
            <span></span>
            <div class="right">
                <a href="javascript:" class="btn green_btn" id="download_to_excel">다운로드</a>
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
	let xlsDownIng = false;
	let xlsDownInterval;

	function initializeGird() {
		let grid = $("#grid_list");
		let searchForm = $("#searchForm");

		grid.jqGrid({
			url: './stock_daily_summary_grid.php',
			mtype: "GET",
			datatype: "json",
			postData: {
				param: searchForm.serialize()
			},
			jsonReader: {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: [
				{label: '상품번호', name: 'product_idx', index: 'product_idx', width: 80, hidden: false,},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 280, sortable: true,},
				{
					label: '옵션번호',
					name: 'product_option_idx',
					index: 'product_option_idx',
					width: 80,
					hidden: false,
				},
				{
					label: '옵션명',
					name: 'product_option_name',
					index: 'product_option_name',
					width: 140,
					sortable: true,
				},
				{
					label: '단가',
					name: 'stock_unit_price',
					index: 'stock_unit_price',
					width: 100,
					sortable: true,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
				{
					label: '당일 정상',
					name: 'until_today_status_normal_total',
					index: 'until_today_status_normal_total',
					width: 100,
					sortable: true,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
				{
					label: '당일 불량',
					name: 'until_today_status_bad_total',
					index: 'until_today_status_bad_total',
					width: 100,
					sortable: true,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
				{
					label: '당일 입고',
					name: 'daily_in_total',
					index: 'daily_in_total',
					width: 100,
					sortable: true,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
				{
					label: '당일 출고',
					name: 'daily_out_total',
					index: 'daily_out_total',
					width: 100,
					sortable: true,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
				{
					label: '당일 반품',
					name: 'daily_return_total',
					index: 'daily_return_total',
					width: 100,
					sortable: false,
					formatter(cv) {
						return Common.addCommas(cv);
					}
				},
                {
                	label: '당일 조정',
                    name: 'daily_adjust_total',
                    index: 'daily_adjust_total',
                    width: 100,
                    sortable: false,
					formatter(cv) {
						return Common.addCommas(cv);
					}
                },
				{
					label: '당일 자산 합계',
					name: 'until_today_stock_value_total',
					index: 'until_today_stock_value_total',
					width: 140,
					sortable: true,
					formatter: function (cv) {
						return Common.addCommas(cv);
					}
				},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'product_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
            loadComplete: function(data) {
				$("#current_stock_value_total").text(Common.addCommas(data.current_stock_value_total));
				$("#until_today_stock_value_total").text(Common.addCommas(data.until_today_stock_value_total));

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_daily_summary", grid);
            },
			resizeStop: function () {
				//컬럼 사이즈 저장
				let col_ary = grid.jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_daily_summary");
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		searchForm.on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			let keyCode = (e.keyCode ? e.keyCode : e.which);
			if (keyCode === 13) {
				e.preventDefault();
				research();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			research();
		});

		//다운로드 버튼
		$("#download_to_excel").on("click", function(){
			excelDown();
		});
	}

	function research(){
		$("#grid_list").jqGrid("setGridParam", {
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	}

	function excelDown() {
		if(xlsDownIng) return;

		xlsDownIng = true;

		let dataObj = {
			param: $("#searchForm").serialize()
		};

		let url = "stock_daily_summary_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_STOCK_DAILY_SUMMARY", function(){
				excelDownComplete();
			});
		}, 500);
	}

	function excelDownComplete(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	}

	initializeGird();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>