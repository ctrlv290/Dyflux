var ChangeLogViewer = (function() {
	var root = this;

	var init = function() {
	};

	//변경이력 창 초기화
	var ChangeListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		$("#grid_list").jqGrid({
			url: './change_log_viewer_grid.php',
			mtype: "GET",
			datatype: "local",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: [
				{ label: '항목', name: 'memo', index: 'memo', width: 100, sortable: false},
				{ label: '기존값', name: 'before_data', index: 'before_data', width: 100, sortable: false},
				{ label: '변경값', name: 'after_data', index: 'after_data', width: 150, sortable: false},
				{ label: '변경일', name: 'regdate', index: 'regdate', width: 150, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }, sortable: false},
				{ label: '대상(코드)', name: 'table_idx1', index: 'table_idx1', width: 150, sortable: false, formatter: function(cellValue, options, rowobject) {
						if(rowobject.table_nm == 'DY_PRODUCT_OPTION')
						{
							return rowobject.target_name + '[' + cellValue + ']' + '[' + rowobject.table_idx2 + ']';
						}else {
							return rowobject.target_name + '[' + cellValue + ']';
						}
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 150, sortable: false},
				{ label: '비고', name: 'action_type', index: 'action_type', width: 150, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				ChangeListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ChangeListSearch();
		});

		//다운로드 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			ChangeListXlsDown();
		});
	};

	//이력 목록/검색
	var ChangeListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var xlsDownIng;
	var xlsDownInterval;
	var ChangeListXlsDown = function(){

		if(xlsDownIng) return;

		xlsDownIng = true;


		//var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "change_log_viewer_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("ChangeLogViewerXls", function(){
				ChangeLogViewer.ChangeListXlsDownComplete();
			});
		}, 500);
	};

	var ChangeListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		ChangeListInit : ChangeListInit,
		ChangeListXlsDownComplete: ChangeListXlsDownComplete
	}
})();