/*
 * 항목설정 관리 js
 */
var ColumnSetting = (function() {
	var root = this;

	var init = function() {
	};

	var ColumnSettingListInit = function(){
		$("#grid_list").jqGrid({
			data: grid_data,
			datatype: "local",
			postData:{
				param: $("#searchForm").serialize()
			},
			colModel: [
				{ label: '항목', name: 'default_name', index: 'default_name', width: 100, sortable: false, align: 'left'},
				{ label:'노출 이름', name: 'visible_name', index: 'visible_name', width: 150, align: 'left',formatter: function(cellvalue, options, rowobject){
						if(!rowobject.is_readonly) {
							return '' +
								'<input type="text" name="column_visible_name" id="column_' + rowobject.column_name + '" value="' + cellvalue + '" />';
						}else{
							return rowobject.default_name;
						}
					}, sortable: false
				},
				{ label: '정렬', name: 'sort', index: 'sort', width: 40, sortable: false,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<i class="fas fa-bars" style="cursor:pointer;"></i>';
					}, sortable: false
				},
				{ label: 'column_name', name: 'column_name', index: 'column_name', width: 0, hidden: true, sortable: false, align: 'left'},
			],
			rowNum:1000,
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'manage_group_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			multiselect: true,
			loadComplete: function(){
				$(".check_all").on("click", function(){
					if(!$(".check_all").is(":checked")){
						$(".check_all").prop("checked", true);
					}else{
						$(".check_all").prop("checked", false);
					}
					$(".is_use").prop("checked", $(this).is(":checked"));
				});

				$.each(grid_data, function(i, o){
					if(o.is_use) {
						$("#grid_list").setSelection((i + 1), o.is_use);
					}
				});
			}
		});

		$("#grid_list").sortableRows();
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(confirm('저장 하시겠습니까?')) {
				//$("form[name='dyForm']").submit();
				var rowData = $("#grid_list").getRowData();
				var saveData = [];

				$.each(rowData, function (i, o) {

					var tmp = {};
					tmp.sort = i + 1;
					tmp.column_name = o.column_name;
					tmp.visible_name = $("#column_" + o.column_name).val();
					tmp.is_use = $("#jqg_grid_list_" + (i + 1)).is(":checked");

					saveData.push(tmp);

				});

				var p_url = "column_setting_proc.php";
				var dataObj = new Object();
				dataObj.target = $("#column_setting_target").val();
				dataObj.mode = $("#column_setting_mode").val();
				dataObj.saveData = saveData;


				showLoader();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if (response.result) {
						alert("저장되었습니다.");
						try{
							opener.location.reload();
						}catch(e){

						}
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}

		});
	};

	var ColumnSettingXlsListInit = function(){
		$("#grid_list").jqGrid({
			data: grid_data,
			datatype: "local",
			postData:{
				param: $("#searchForm").serialize()
			},
			colModel: [
				{ label: '항목', name: 'default_name', index: 'default_name', width: 100, sortable: false, align: 'left'},
				{ label:'노출 이름', name: 'visible_name', index: 'visible_name', width: 150, align: 'left',formatter: function(cellvalue, options, rowobject){
						if(!rowobject.is_readonly) {
							return '' +
								'<input type="text" name="column_visible_name" id="column_' + rowobject.column_name + '" value="' + cellvalue + '" />';
						}else{
							return rowobject.default_name;
						}
					}, sortable: false
				},
				{ label: '정렬', name: 'sort', index: 'sort', width: 40, sortable: false,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<i class="fas fa-bars" style="cursor:pointer;"></i>';
					}, sortable: false
				},
				{ label: 'column_name', name: 'column_name', index: 'column_name', width: 0, hidden: true, sortable: false, align: 'left'},
			],
			rowNum:1000,
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'manage_group_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			multiselect: true,
			loadComplete: function(){
				$(".check_all").on("click", function(){
					if(!$(".check_all").is(":checked")){
						$(".check_all").prop("checked", true);
					}else{
						$(".check_all").prop("checked", false);
					}
					$(".is_use").prop("checked", $(this).is(":checked"));
				});

				$.each(grid_data, function(i, o){
					if(o.is_use) {
						$("#grid_list").setSelection((i + 1), o.is_use);
					}
				});
			}
		});

		$("#grid_list").sortableRows();
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(confirm('저장 하시겠습니까?')) {
				//$("form[name='dyForm']").submit();
				var rowData = $("#grid_list").getRowData();
				var saveData = [];

				$.each(rowData, function (i, o) {

					var tmp = {};
					tmp.sort = i + 1;
					tmp.column_name = o.column_name;
					tmp.visible_name = $("#column_" + o.column_name).val();
					tmp.is_use = $("#jqg_grid_list_" + (i + 1)).is(":checked");

					saveData.push(tmp);

				});

				var p_url = "column_setting_proc_xls.php";
				var dataObj = new Object();
				dataObj.target = $("#column_setting_target").val();
				dataObj.mode = $("#column_setting_mode").val();
				dataObj.saveData = saveData;


				showLoader();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if (response.result) {
						alert("저장되었습니다.");
						try{
							//opener.location.reload();
						}catch(e){

						}
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}

		});
	};

	return {
		ColumnSettingListInit : ColumnSettingListInit,
		ColumnSettingXlsListInit : ColumnSettingXlsListInit,
	}
})();