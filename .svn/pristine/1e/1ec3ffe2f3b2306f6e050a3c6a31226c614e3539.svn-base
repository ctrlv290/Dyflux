/*
 * 기본정보 관련 그룹 관리 js
 */
var ManageGroup = (function() {
	var root = this;
	var _manage_group_type;

	var init = function() {
	};

	//그룹 리스트 Ajax To Select option
	var getManageGroupList = function(manage_group_type){
		if(manage_group_type != "") {
			_manage_group_type = manage_group_type;
		}
		var p_url = "/info/manage_group_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_manage_group_list";
		dataObj.manage_group_type = _manage_group_type;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				var manage_group_idx_selected = $("#manage_group_idx").data("selected");
				//console.log(response.list);
				var $list = response.list;
				$("#manage_group_idx option").remove();
				if(window.name == 'seller_list' || window.name == 'vendor_list' || window.name == 'supplier_list') {
					$("#manage_group_idx").append('<option value="">전체</option>');
				}else{
					$("#manage_group_idx").append('<option value="0">그룹을 선택해주세요.</option>');
				}
				$.each($list, function(i, v){
					if(manage_group_idx_selected == v.manage_group_idx)
					{
						$("#manage_group_idx").append('<option value="' + v.manage_group_idx + '" selected="selected">' + v.manage_group_name + '</option>');
					}else {
						$("#manage_group_idx").append('<option value="' + v.manage_group_idx + '">' + v.manage_group_name + '</option>');
					}
				});
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			//console.log(jqXHR, textStatus);
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		});
	};

	//그룹 팝업 페이지 초기화
	var ManageGroupInit = function(manage_group_type){

		//그룹 타입 선언
		_manage_group_type = manage_group_type;

		//그룹 추가 바인딩
		ManageGroupAddForm();

		//그리드 리스트 바인딩
		$("#grid_list").jqGrid({
			url: './manage_group_pop_grid.php',
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
			colModel: [

				{ label: '코드', name: 'manage_group_idx', index: 'manage_group_idx', width: 80},
				{ label: '그룹이름', name: 'manage_group_name', index: 'manage_group_name', width: 180, formatter: function(cellvalue, options, rowobject){
						return '<input type="text" name="manage_group_name" id="edit_manage_group_name_'+rowobject.manage_group_idx+'" class="input_manage_group_name" value="'+cellvalue+'" data-idx="" />';
					}
				},
				{ label: '등록일', name: 'manage_group_regdate', index: 'manage_group_regdate', width: 150,formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '판매처 수', name: 'group_count', index: 'manage_count', width: 100, sortable: false},
				{ label:'', name: '', width: 150,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '' +
							'<a href="javascript:;" class="xsmall_btn blue_btn btn-manage-group-save" data-idx="'+rowobject.manage_group_idx+'">수정</a>' +
							' <a href="javascript:;" class="xsmall_btn red_btn btn-manage-group-del" data-idx="'+rowobject.manage_group_idx+'">삭제</a>';
					}, sortable: false
				},
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
			loadComplete: function(){
				ManageGroupGridBtnBind();
			}
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
				ManageGroupSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ManageGroupSearch();
		});
	};

	//그룹 팝업 입력폼 초기화
	var ManageGroupAddForm = function(){
		//그룹 추가 버튼 Bind
		$(".btn-manage-group-add").on("click", function(){
			var txt = $("#input_manage_group_name").val().trim();
			if(txt.length < 2)
			{
				alert("그룹 이름을 입력해주세요. 그룹이름은 2~ 20자 이내로 가능합니다.");
				$("#input_manage_group_name").focus();
				return;
			}
			ManageGroupAdd(txt);
		});
	};

	//그룹 팝업 jqGrid 리스트 내 버튼 바이딩
	//jqGrid 로드 시 마다 실행
	var ManageGroupGridBtnBind = function(){
		$(".btn-manage-group-save").on("click", function(){
			var idx = $(this).data("idx");
			var txt = $("#edit_manage_group_name_"+idx).val().trim();
			if(txt.length < 2)
			{
				alert("그룹 이름을 입력해주세요. 그룹이름은 2~ 20자 이내로 가능합니다.");
				$("#edit_manage_group_name_"+idx).focus();
				return;
			}
			ManageGroupSave(idx, txt);
		});
		$(".btn-manage-group-del").on("click", function(){
			var idx = $(this).data("idx");
			if(confirm('정말 삭제하시겠습니까?'))
			{
				ManageGroupDelete(idx);
			}
		});
	};

	//그룹 추가 함수
	var ManageGroupAdd = function(txt){
		var data = new Object();
		data.mode = "add_manage_group";
		data.manage_group_type = _manage_group_type;
		data.manage_group_name = txt;
		ManageGroupProc(data);
	};

	//그룹 저장 함수
	var ManageGroupSave = function(manage_group_idx, txt){
		var data = new Object();
		data.mode = "mod_manage_group";
		data.manage_group_type = _manage_group_type;
		data.manage_group_idx = manage_group_idx;
		data.manage_group_name = txt;
		ManageGroupProc(data);
	};

	//그룹 삭제 함수
	var ManageGroupDelete = function(manage_group_idx){
		var data = new Object();
		data.mode = "del_manage_group";
		data.manage_group_type = _manage_group_type;
		data.manage_group_idx = manage_group_idx;
		ManageGroupProc(data);
	};

	//그룹 추가/저장/삭제 실행 함수
	var ManageGroupProc = function(data){
		var p_url = "/info/manage_group_proc.php";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				//추가/저장/삭제 액션 이 성공적일 경우
				$("#input_manage_group_name").val("");

				//jqGrid 리스트 reload
				ManageGroupSearch();

				//부모창이 목록페이지 일 경우 검색 박스의 그룹 SelectBox redraw
				//부모창이 등록/수정 팝업 일 경우 그룹 선택 SelectBox redraw
				if(opener.name == 'seller_list' || opener.name == 'seller_write_pop' || opener.name == 'vendor_list' || opener.name == 'vendor_write_pop' || opener.name == 'supplier_list' || opener.name == 'supplier_write_pop') {
					opener.ManageGroup.ManageGroupReload('');

					try{
						opener.opener.ManageGroup.ManageGroupReload('');
					}catch(e){

					}
				}


			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		});
	};

	//그룹 목록/검색
	var ManageGroupSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	return {
		ManageGroupInit : ManageGroupInit,
		getManageGroupList: getManageGroupList,
		ManageGroupReload : getManageGroupList,
	}
})();