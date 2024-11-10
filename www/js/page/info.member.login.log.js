var MemberLoginLog = (function() {
	var root = this;

	var init = function() {
	};

	//변경이력 창 초기화
	var LoginLogListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		$("#grid_list").jqGrid({
			url: './member_login_log_grid.php',
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
				{ label: '접속일자', name: 'member_login_regdate', index: 'member_login_regdate', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){ return Common.toDateTimeOnlyDate(cellvalue); }
				},
				{ label: '접속시간', name: 'member_login_regdate', index: 'member_login_regdate', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){ return Common.toDateTimeOnlyTime(cellvalue); }
				},
				{ label: '접속자 ID', name: 'member_id', index: 'member_id', width: 150, sortable: false},
				{ label: '접속자 이름', name: 'member_name', index: 'member_name', width: 150, sortable: false},
				{ label: '접속자 IP', name: 'member_login_regip', index: 'member_login_regip', width: 150, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.member_login_idx',
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
	};

	//목록/검색
	var ChangeListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};


	return {
		LoginLogListInit : LoginLogListInit
	}
})();