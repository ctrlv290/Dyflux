/*
 * 그룹관리 js
 */
var MemberGroup = (function() {
	var root = this;

	var init = function() {
	};

	//그룹 목록 초기화 jqGrid Loading
	var MemberGroupListInit = function(){

		$("#grid_list").jqGrid({
			url: './member_group_list_grid.php',
			mtype: "GET",
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
				{ label: '그룹 코드', name: 'member_group_idx', index: 'A.member_group_idx', width: 100},
				{ label: '그룹 이름', name: 'member_group_name', index: 'A.member_group_name', width: 100},
				{ label: '메모', name: 'member_group_etc', index: 'a.member_group_etc', width: 150, sortable: false,formatter: function(cellvalue, options, rowobject){
						return (cellvalue != null) ? cellvalue.replace(/\r\n/g, ' ') : '';
					}},
				{ label: '그룹 인원', name: 'member_group_user_count', index: 'member_group_user_count', width: 100},
				{ label: '등록일', name: 'member_group_regdate', index: 'A.member_group_regdate', width: 150, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '작업자', name: 'last_member_id', index: 'last_member_id', width: 150, sortable: false},
				{ label: '사용여부', name: 'member_group_is_use', index: 'member_group_is_use', width: 150, sortable: false},
				{ label:'수정', name: '수정', width: 150,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="member_group_write.php?member_group_idx='+rowobject.member_group_idx+'" class="xsmall_btn">수정</a>';
					}, sortable: false
				},
			],
			rowNum:1000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.member_group_idx',
			sortorder: "ASC",
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
				UseListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			UseListSearch();
		});
	};

	//그룹 목록/검색
	var UseListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//그룹 입력 폼 초기화
	var MemberGroupWriteInit = function(){
		bindWriteForm();

		$(".group_member_list").on("click", ".btn-group-member-user-remove", function(e){
			MemberGroupUserRemove($(this));
		});
	};

	//그룹 등록/수정 폼 초기화
	var bindWriteForm = function () {
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;
			try{
				if (!valForm.chkValue(objForm.member_group_name, "그룹명을 정확히 입력해주세요.", 2, 50, null)) return returnType;

				this.action = "member_group_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});

		//그룹멤버 추가 팝업
		$(".btn-member-group-user-add-pop").on("click", function(){
			MemberGroupUserAddPopup();
		});
	};

	//그룹멤버 추가 팝업
	var MemberGroupUserAddPopup = function(){
		Common.newWinPopup("member_group_user_add_pop.php", 'member_group_user_add_pop', 700, 720, 'yes');
	};

	var MemberGroupUserAddPopupInit = function(){
		$("#grid_list").jqGrid({
			url: './member_group_user_add_pop_grid.php',
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
				{ label: '', name: '', index: 'chkbox', width: 40, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;

						rst = '<input type="checkbox" name="member_idx" class="chk_member_idx" value="' + rowobject.idx + '" data-name="'+ rowobject.name + '" data-id="' + rowobject.member_id + '" />';

						return rst;
					}
				},
				{ label: '사용자 아이디', name: 'member_id', index: 'A.member_id', width: 80},
				{ label: '사용자 이름', name: 'name', index: 'U.name', width: 80},
				{ label: '그룹', name: 'member_group_text', index: 'member_group_text', width: 150, sortable: false},
			],
			rowNum:1000,
			pager: '#grid_pager',
			sortname: 'A.member_id',
			sortorder: "ASC",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

			},
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				UseListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			UseListSearch();
		});

		//선택된 사용자 추가 버튼 클릭 이벤트
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$(".chk_member_idx:checked").each(function(i){
				var member_idx = $(this).val();
				var member_id = $(this).data("id");
				var member_name = $(this).data("name");

				opener.MemberGroup.MemberGroupUserAdd(member_idx, member_id, member_name);
			});

			//self.close();
		});
	};

	var MemberGroupUserAdd = function(member_idx, member_id, member_name){

		var html = '<li>\n' +
			member_name + '(' + member_id + ')' + '\n' +
			'<a href="javascript:;" class="btn-group-member-user-remove" data-idx="' + member_idx + '"><i class="far fa-times-circle"></i></a>\n' +
			'<input type="hidden" name="member_idx[]" value="' + member_idx + '" />\n' +
			'</li>\n';

		var rst = MemberGroupUserIdxListFunc("add", member_idx);
		if(!rst)
		{
			$(".group_member_list").append(html);
		}
	};

	var MemberGroupUserRemove = function($obj){
		MemberGroupUserIdxListFunc("remove", $obj.data("idx"));
		$obj.parent().remove();
	};

	var MemberGroupUserIdxListFunc = function(act, idx){
		console.log(act, idx);
		var idx_list = $("#member_idx_list").val();
		var idx_array = idx_list.split(',');
		if(idx_list == "")
		{
			idx_array = new Array();
		}
		var is_chk = false;

		for(var i = 0;i < idx_array.length; i++)
		{
			if (idx_array[i] == idx) {
				is_chk = true;
			}
		}

		if(act == "add") {
			if (!is_chk){
				idx_array.push(idx);
			}
		}else if(act == "remove"){
			idx_array.arrayRemove(idx + '');
		}

		$("#member_idx_list").val(idx_array.join(','));

		return is_chk;
	};

	return {
		MemberGroupListInit : MemberGroupListInit,
		MemberGroupWriteInit : MemberGroupWriteInit,
		MemberGroupUserAddPopupInit: MemberGroupUserAddPopupInit,
		MemberGroupUserAdd: MemberGroupUserAdd,
	}
})();