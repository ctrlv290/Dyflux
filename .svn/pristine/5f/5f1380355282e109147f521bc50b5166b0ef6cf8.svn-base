<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 목록 페이지
 */
//Page Info
$pageMenuIdx = 312;
//Init
include_once "../_init_.php";

$period_search_type         = $_GET["period_search_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$seller_idx                 = $_GET["seller_idx"];
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

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
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
                            <select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
                        <div class="finder_col">
                            <select name="search_column">
                                <option value="product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
                                <option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>옵션명</option>
                                <option value="product_option_idx" <?=($search_column == "product_option_idx") ? "selected" : ""?>>옵션코드</option>
                            </select>
                            <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
                        </div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
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
			<a href="javascript:;" class="btn" id="btn_new">신규등록</a>
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
<div id="modal_write" title="판매처별 관리" class="red_theme" style="display: none;"></div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	window.name = 'product_manage_by_seller';

	let isWriting = false;

	function listSearch() {
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
    }

    function openWritePopup(idx) {
		let p_url = "/product/popup_write_manage_by_seller.php";
		let dataObj = {};
		if (idx) dataObj.idx = idx;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response) {
				$("#modal_write").html(response);
				$("#modal_write").dialog( "open" );

				initWritePopup();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function(jqXHR, textStatus){
			alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
		}).always(function(){
			hideLoader();
		});
    }

	function closeModalPop(modalSelector) {
		$(modalSelector).dialog("close");
		$(modalSelector).html("");
	}

	function initWritePopup() {
		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".popup .seller_group_idx", ".popup .seller_idx");
		$(".popup .seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//상품 옵션 팝업
		$("#btn_add_product_option").on("click", function(){
			Common.newWinPopup("/common/product_search_pop.php?mode=product_option&type=single&callback=addProductOption", 'product_search_pop', 800, 700, 'yes');
		});

		//저장 버튼
		$("#btn_write").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if (!isWriting)
				$("form[name='form_write_pdt_mng_by_seller']").submit();
		});

		$(".btn_close_pop").on("click", function(){
			closeModalPop("#modal_write");
		});

		//폼 Submit 이벤트
		$("form[name='form_write_pdt_mng_by_seller']").submit(function(e){
			e.preventDefault();
			let returnType = false;        // "" or false;
			let valForm = new FormValidation();
			let objForm = this;

			try{
				if (!valForm.chkValue(objForm.seller_idx, "판매처를 선택해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_idx, "상품을 선택해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_idx, "상품을 선택해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.sale_unit_price, "판매 단가를 정확히 입력해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.sale_delivery_fee, "판매 배송비를 정확히 입력해주세요.", 1, 20, null)) return returnType;

				isWriting = true;

				showLoader();

				$.ajax({
					type: 'POST',
					url: '/product/product_option_proc.php',
					dataType: "json",
					data: $("form[name='form_write_pdt_mng_by_seller']").serialize()
				}).done(function (response) {
					alert(response.msg);

					if(response.result) {
						closeModalPop("#modal_write");
						listSearch();
					}

				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}).always(function(){
					hideLoader();
					isWriting = false;
				});

				return false;

			}catch(e){
				alert(e);
				isWriting = false;
				return false;
			}
		});
    }

	function addProductOption(args) {
		args.list.forEach(function(val){
			$("#product_idx").val(val.product_idx);
			$("#lb_product_name").text(val.product_name);
			$("#product_option_idx").val(val.product_option_idx);
			$("#lb_product_option_name").text(val.product_option_name);
		});
	}

	function delete_pmbs(idx) {
		if(!confirm("정말 삭제하시겠습니까?")) {
			return;
		}

		showLoader();

		let dataObj = {};
		dataObj.idx = idx;
		dataObj.mode = "delete_manage_by_seller";

		$.ajax({
			type: 'POST',
			url: '/product/product_option_proc.php',
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			hideLoader();
			alert(response.msg);

			if(response.result) {
				listSearch();
			}

		}).fail(ajaxFailWithHideLoader);
    }

	function special_manage_init() {
		$("#btn_new").on("click", function(){
            openWritePopup(null);
        });

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			selectAll:true,
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//selectAll 박스 위치 조정
		$('.seller_idx').on('sumo:opening', function () {
			$('.select-all').css('height', '35px');
			$('.select-all').children('label').text('전체 선택');
		});

		//수동 등록 모달팝업 세팅
		$("#modal_write").dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_manage_by_seller_grid.php',
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
				{ label: '관리', name: 'btn_action', index: 'btn_action', width: 100, sortable: false, formatter: function(cv, options, ro){
					    let btn_group = '<a href="javascript:;" class="xsmall_btn green_btn btn_pmbs_update" data-idx="'+ro.idx+'">수정</a>&nbsp';
						btn_group += '<a href="javascript:;" class="xsmall_btn red_btn btn_pmbs_delete" data-idx="'+ro.idx+'">삭제</a>';
						return btn_group;
					}
				},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: true },
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: true },
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 200, sortable: true },

				{ label: '판매단가', name: 'sale_unit_price', index: 'sale_unit_price', width: 100, sortable: false, formatter: 'integer' },
				{ label: '판매 배송비', name: 'sale_delivery_fee', index: 'sale_delivery_fee', width: 100, sortable: false, formatter: 'integer' },

				{label: '등록일', name: 'reg_date', index: 'reg_date', width: 150, formatter: function (cv) {
						return Common.toDateTime(cv);
					}
				},
				{label: '등록자', name: 'reg_member_name', index: 'reg_member_name', width: 80, sortable: false },

				{label: '변경일', name: 'mod_date', index: 'mod_date', width: 150, formatter: function (cv) {
						return Common.toDateTime(cv);
					}
				},
				{label: '변경자', name: 'mod_member_name', index: 'mod_member_name', width: 80, sortable: false }
			],
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'A.seller_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function() {
				//수정 팝업
				$(".btn_pmbs_update").on("click", function(){
					openWritePopup($(this).data("idx"));
				});

				//삭제 버튼
				$(".btn_pmbs_delete").on("click", function() {
					delete_pmbs($(this).data("idx"));
				});
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
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				listSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			listSearch();
		});
    }

	special_manage_init();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

