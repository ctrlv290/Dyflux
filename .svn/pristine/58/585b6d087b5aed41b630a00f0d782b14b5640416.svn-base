/*
 * 상품정보제공고시 관리 js
 */
var ProductNotice = (function() {
	var root = this;

	var init = function() {
	};

	//상품정보제공고시 목록 초기화 jqGrid Loading
	var ProductNoticeListInit = function(){

		//신규등록 팝업
		$(".btn-product-notice-write-pop").on("click", function(){
			ProductNoticeWritePopup();
		});

		$("#grid_list").jqGrid({
			url: './product_notice_list_grid.php',
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
				{ label: '상품정보제공고시 명', name: 'product_notice_title', index: 'A.product_notice_title', width: 150},
				{ label: '등록일', name: 'product_notice_regdate', index: 'A.product_notice_regdate', width: 150, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '작업자', name: 'last_member_id', index: 'last_member_id', width: 150, sortable: false},
				{ label:'수정', name: '수정', width: 150,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="javascript:;" class="xsmall_btn btn-product-notice-modify-pop" data-idx="'+rowobject.product_notice_idx+'">수정</a>';
					}, sortable: false
				},
			],
			rowNum:1000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.product_notice_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//수정 팝업
				$(".btn-product-notice-modify-pop").on("click", function(){
					ProductNoticeWritePopup($(this).data("idx"));
				});
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
				ProductNoticeListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			//ProductNoticeListSearch();
			$("#grid_list").setGridParam({
				search: true,
				datatype: "json",
				postData:{
					param: $("#searchForm").serialize()
				}
			}).trigger("reloadGrid");
		});
	};

	//상품정보제공고시 목록/검색
	var ProductNoticeListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//상품정보제공고시 입력 폼 초기화
	var ProductNoticeWriteInit = function(){
		bindWriteForm();
	};

	//상품정보제공고시 등록/수정 폼 초기화
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
				if (!valForm.chkValue(objForm.product_notice_title, "상품정보제공고시 명을 정확히 입력해주세요.", 2, 50, null)) return returnType;

				this.action = "product_notice_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	//상품정보제공고시 추가 팝업
	var ProductNoticeWritePopup = function(idx){
		var qs = (idx != "" && typeof idx !== 'undefined') ? "?product_notice_idx=" + idx : "";
		Common.newWinPopup("/info/product_notice_write_pop.php"+qs, 'product_notice_write_pop', 850, 720, 'yes');
	};

	//입력/수정 이후 본창 리스트를 reload 할때 실행되는 함수
	//window.name 이 'product_notice_list' 일 때 만 실행
	//다른 페이지에서 입력/수정 팝업을 띄워 입력/수정 된 경우 실행하지 않도록 설정
	var ProductNoticeListReload = function(){
		if(window.name == 'product_notice_list'){
			ProductNoticeListSearch();
		}else if(window.name == 'product_write'){
			//상품 등록/수정 페이지에서 상품정보고시 셀렉트 박스 Reload
			bindProductNoticeList(".product_notice_idx");
		}
	};

	//상품정보제공고시 리스트를 셀렉트박스 바인딩
	let bindProductNoticeList = function(target_class_name){
		let p_url = "/info/product_notice_proc.php";
		let dataObj = {};
		dataObj.mode = "get_product_notice_list_for_selectbox";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				let product_notice_idx = $(target_class_name).data("selected");
				let $list = response.list;
				$(target_class_name + " option").remove();
				$(target_class_name).append('<option value="0">상품정보고시를 선택해주세요.</option>');
				$.each($list, function(i, v){
					if(product_notice_idx === v.product_notice_idx)
					{
						$(target_class_name).append('<option value="' + v.product_notice_idx + '" selected="selected">' + v.product_notice_title + '</option>');
					}else {
						$(target_class_name).append('<option value="' + v.product_notice_idx + '">' + v.product_notice_title + '</option>');
					}
				});
				$(target_class_name).trigger("change");
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	return {
		ProductNoticeListInit : ProductNoticeListInit,
		ProductNoticeWriteInit : ProductNoticeWriteInit,
		ProductNoticeListReload : ProductNoticeListReload,
		bindProductNoticeList: bindProductNoticeList,
		ProductNoticeWritePopup: ProductNoticeWritePopup,
	}
})();