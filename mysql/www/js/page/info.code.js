/*
 * 공통코드관리 js
 */
var Code = (function() {
	var root = this;

	var init = function() {
	};

	var CodeListInit = function(){
		$("#grid_list").jqGrid({
			url: './code_list_grid.php',
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
				{ label: '코드이름', name: 'code_name', index: 'A.code_name', width: 150, sortable: false},
				{ label: '코드값', name: 'code', index: 'A.code', width: 150, sortable: false},
				{ label: '상위 코드명', name: 'parent_code_name', index: 'parent_code_name', width: 150, sortable: false},
				{ label: '상위 코드값', name: 'parent_code', index: 'parent_code', width: 150, sortable: false},
				{ label: '등록일', name: 'regdate', index: 'A.regdate', width: 150, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }, sortable: false},
				{ label: '사용여부', name: 'is_use', index: 'A.is_use', width: 100, sortable: false},
				{ label:'수정', name: '수정', width: 100,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="code_write.php?idx='+rowobject.idx+'" type="xsmall_btn">수정</a>';
					}, sortable: false
				},
			],
			rowNum:1000,
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
				CodeListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CodeListSearch();
		});
	};

	var CodeListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var CodeWriteInit = function(){
		bindWriteForm();
	};

	var bindWriteForm = function () {
		$("#code_value").on("keyup", function(){
			if($(this).val().trim() != "") {
				var code_idx = $("#code_idx").val();
				var p_url = "/info/code_proc.php";
				var dataObj = new Object();
				dataObj.mode = "code_check";
				dataObj.code_idx = code_idx;
				dataObj.code = $(this).val();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if(response.result)
					{
						$(".code_value_check_txt").removeClass("col_red").addClass("col_blue").html("사용가능한 코드값입니다.").show();
						$("#dupcheck").val("Y");
					}else{
						$(".code_value_check_txt").removeClass("col_blue").addClass("col_red").html("사용이 불가능한 코드값입니다.").show();
						$("#dupcheck").val("N");
					}
				});
			}else{
				$(".code_value_check_txt").removeClass("col_red col_blue").html("").hide();
				$("#dupcheck").val("N");
			}
		});

		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			if($(this).hasClass("add")) {
				if ($("#dupcheck").val() != "Y") {
					alert('사용가능한 코드값이 아닙니다.');
					return false;
				}
			}

			try{
				if (!valForm.chkValue(objForm.code_name, "이름을 정확히 입력해주세요.", 2, 40, null)) return returnType;
				if($(this).hasClass("add"))
				{
					if (!valForm.chkValue(objForm.code, "코드값을 정확히 입력해주세요.", 1, 40, null)) return returnType;

				}
				this.action = "code_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	return {
		CodeListInit : CodeListInit,
		CodeWriteInit : CodeWriteInit,
	}
})();