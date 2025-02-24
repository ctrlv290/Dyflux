/*
 * 주문관리 js
 */
var Order = (function() {
	var root = this;

	var init = function() {
	};

	var __ORDER__order_date = "";

	/**
	 * 발주 페이지 초기화
	 */
	var OrderListInit = function(isOrderBlock){

		//발주일 설정
		__ORDER__order_date = $("input[name='order_date']").val();

		//Grid 초기화
		OrderListGridInit(isOrderBlock);

		//판매처 수동 발주 업로드 modal popup 초기화
		$( "#modal_order_write_xls_pop" ).dialog({
			width: 550,
			height: 200,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
		});

		//발주서 포맷 설정 modal popup 초기화
		$( "#modal_order_format_seller_pop" ).dialog({
			width: 550,
			height: 680,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
		});

		//새로고침 버튼 바인딩
		$(".btn-order-list-grid-reload").on("click", function(){
			OrderListSearch();
		});
		//전체보기 버튼 바인딩
		$(".btn-order-list-reload").on("click", function(){
			$("input[name='search_keyword']").val('');
			OrderListSearch();
		});
		//전체발주삭제 버튼 바인딩
		$(".btn-all-order-delete").on("click", function(){
			OrderDeleteAll();
		});

		//일괄접수처리 버튼 바인딩 190703 update kyu
		/*$(".btn-order-accept-all").on("click", function(){
			OrderAcceptWholeConfirm();
		});*/

		//발주서 양식 다운
		$(".btn-xls-down").on("click", function(){
			location.href="/_xls_sample/발주서양식.xls";
		});

		OrderListChartDraw();
	};

	/**
	 * 일별매출차트 매출 그래프 초기화
	 * @constructor
	 */
	var OrderListChartDraw = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		chart.data = chartData;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "name";
		categoryAxis.renderer.labels.template.rotation = -90;
		categoryAxis.renderer.labels.template.horizontalCenter = "right";
		categoryAxis.renderer.labels.template.verticalCenter = "center";
		categoryAxis.renderer.minGridDistance = 10;
		if(!isDYLogin) {
			categoryAxis.width = 100;
		}
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.dashLength = 5;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "name";
		series.dataFields.valueY = "val";
		series.name = "판매처";
		series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.hideOversized = false;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	/**
	 * 발주목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderListGridInit = function(isOrderBlock){

		$("#grid_list").jqGrid({
			url: './order_list_grid.php',
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
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 120, sortable: true},
				{ label: '판매처 코드', name: 'seller_idx', index: 'seller_idx', width: 100, sortable: true},
				{ label: '수동발주', name: 'btn_manual_order', index: 'btn_manual_order', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						if (isOrderBlock) {
							return '';
						} else {
							return ' <a href="javascript:;" class="xsmall_btn green_btn btn-seller-order-write" data-seller_idx="' + rowobject.seller_idx + '">업로드</a>';
						}

					}},
				{ label: '발주삭제', name: 'btn_manual_order', index: 'btn_manual_order', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return ' <a href="javascript:;" class="xsmall_btn red_btn btn-seller-order-delete" data-seller_idx="'+rowobject.seller_idx+'" data-seller_name="'+rowobject.seller_name+'">삭제</a>';

					}},
				{ label: '최근 발주일', name: 'last_order_datetime', index: 'last_order_datetime', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.toDateTime(cellvalue);
				}},
				{ label: '최근 발주수량', name: 'last_order_count', index: 'last_order_count', width: 80, sortable: false},
				{ label: '최근 신규 발주수량', name: 'last_new_order_count', index: 'last_new_order_count', width: 80, sortable: false},
				{ label: '발주수량', name: 'available_order_count', index: 'available_order_count', width: 80, sortable: false},
				{ label: '최근 발주 작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '발주서포맷', name: 'btn_manual_order', index: 'btn_manual_order', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return ' <a href="javascript:;" class="xsmall_btn blue_btn btn-order-format-seller" data-seller_idx="'+rowobject.seller_idx+'">설정</a>';

					}},
			],
			rowNum: 1000,
			pgbuttons: false,
			pgtext: false,
			pager: '#grid_pager',
			sortname: 'A.seller_name',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//업로드
				$(".btn-seller-order-write").on("click", function(){
					OrderWriteXlsPopOpen($(this));
				});
				//발주삭제
				$(".btn-seller-order-delete").on("click", function(){
					OrderDeleteOne($(this));
				});
				//발주서포맷 설정
				$(".btn-order-format-seller").on("click", function(){
					OrderFormatSellerPopOpen($(this));
				});

				Common.jqGridResizeToWindowHeightMinusMarginH("#grid_list", 250);

				//최근 및 현재 발주 합산 가져오기
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".txt_sum_last_order_count").text(userData.sum_last_order_count);
				$(".txt_sum_last_new_order_count").text(userData.sum_last_new_order_count);
				$(".txt_sum_available_order_count").text(userData.sum_available_order_count);
			}
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeToWindowHeightMinusMarginH("#grid_list", 250);
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
				OrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderListSearch();
		});

	};

	/**
	 * 발주 목록/검색
	 * @constructor
	 */
	var OrderListSearch = function(){
		__ORDER__order_date = $("input[name='order_date']").val();
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 일괄접수처리 실행!! 190703 update by kyu
	 * @constructor
	 */
	var OrderAcceptWholeConfirm = function(){
		
		/*if(!confirm('일괄접수처리를 실행하시겠습니까?')) {
			return;
		}
		var p_url = "/order/order_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_accept_whole_confirm";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.data+"건이 일괄접수처리 되었습니다.");
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});*/
	};

	/**
	 * 전체 발주 삭제
	 * @constructor
	 */
	var OrderDeleteAll = function(){

		if(!confirm('전체발주를 삭제하시겠습니까?')){
			return;
		}

		var p_url = "/order/order_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_delete_all";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert("전체 발주 내역을 삭제하였습니다.");
				OrderListSearch();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 판매처 발주 내역 삭제
	 * @param $obj : "삭제" 버튼 object
	 * @constructor
	 */
	var OrderDeleteOne = function($obj){

		if(!confirm('[' + $obj.data("seller_name") + '] 판매처의 발주 내역을 삭제하시겠습니까?')){
			return;
		}

		var p_url = "/order/order_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_delete_one";
		dataObj.seller_idx = $obj.data("seller_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert("발주 내역을 삭제하였습니다.");
				OrderListSearch();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 판매처 수동발주 업로드 modal popup 열기
	 * @param $obj
	 * @constructor
	 */
	var OrderWriteXlsPopOpen = function($obj){
		var p_url = "order_write_xls_pop.php";
		var dataObj = new Object();
		dataObj.seller_idx = $obj.data("seller_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_write_xls_pop").html(response);
				$("#modal_order_write_xls_pop").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 판매처 수동발주 업로드 modal popup 닫기
	 * @constructor
	 */
	var OrderWriteXlsPopClose = function(){
		$("#modal_order_write_xls_pop").html("");
		$("#modal_order_write_xls_pop").dialog( "close" );
	};

	/**
	 * 판매처 수동발주 페이지 초기화
	 * @constructor
	 */
	var OrderWriteXlsInit = function(){
		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm_xls").submit();
		});

		$(".btn-order-write-xls-pop-close").on("click", function(){
			OrderWriteXlsPopClose();
		});
	};

	/**
	 * 판매처 수동발주 업로드 처리
	 * @param xls_filename
	 * @constructor
	 */
	var OrderSellerXlsRead = function(xls_filename){
		//xls_hidden_frame.location.replace("/order/order_proc_xls.php?xls_filename="+xls_filename+'&seller_idx='+$("form[name='searchForm_xls'] input[name='seller_idx']").val());
		xls_hidden_frame.location.replace("about:_blank");
		//xlsUploadedFileName
		var p_url = "/order/order_proc_xls.php";
		var dataObj = new Object();
		dataObj.xls_filename = xls_filename;
		dataObj.seller_idx = $("form[name='searchForm_xls'] input[name='seller_idx']").val();
		dataObj.order_date = __ORDER__order_date;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				var try_count = Number(response.collect_count);
				var inserted_count = Number(response.collect_order_count);
				var dup_count = Number(response.dup_count);

				alert(inserted_count + "건의 주문을 발주하였습니다.\n중복 주문 : " + dup_count + "건");
				OrderWriteXlsPopClose();
				OrderListSearch();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 발주서 포맷 설정 팝업 열기
	 * @param $obj
	 * @constructor
	 */
	var OrderFormatSellerPopOpen = function($obj){
		var p_url = "order_format_write_pop.php";
		var dataObj = new Object();
		dataObj.seller_idx = $obj.data("seller_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_format_seller_pop").html(response);
				$("#modal_order_format_seller_pop").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 발주서 포맷 설정 팝업 닫기
	 * @constructor
	 */
	var OrderFormatSellerPopClose = function(){
		$("#modal_order_format_seller_pop").html("");
		$("#modal_order_format_seller_pop").dialog( "close" );
	};

	/**
	 * 발주서 포맷 설정 팝업 페이지 초기화
	 * @constructor
	 */
	var OrderFormatSellerPopInit = function(){
		$("#btn-save-format").on("click", function(){
			OrderFormatSellerSave();
		});

		$(".btn-order-format-write-pop-close").on("click", function(){
			OrderFormatSellerPopClose();
		});
	};

	/**
	 * 발주서 포맷 설정 팝업 : 저장
	 * @constructor
	 */
	var OrderFormatSellerSave = function(){
		if(confirm('저장 하시겠습니까?')) {
			showLoader();
			var p_url = "order_format_proc.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyForm2']").serialize()
			}).done(function (response) {
				if (response.result) {
					alert('저장되었습니다.');
					OrderFormatSellerPopClose();
					//Grid reLoad
					OrderListGridInit();

				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 매칭 페이지 초기화
	 */
	var OrderMatchingInit = function(){

		showLoader();

		//자동 매칭 실행
		OrderMatchingAuto(false);

		//다음 버튼 바인딩
		$(".btn-next-package").on("click", function(){
			// if($("#grid_list").getGridParam("records") == 0){
			// 	location.href="order_package.php";
			// }else{
			// 	alert('매칭 되지 않은 발주건이 남아 있습니다.');
			// 	return;
			// }
			location.href="order_package.php";
		});

		//Grid 초기화
		//자동 매칭 실행 후 실행
		//OrderMatchingGridInit();

		//전체보기 버튼 바인딩
		$(".btn-matching-list-reload").on("click", function(){
			$("input[name='search_keyword']").val('');
			OrderMatchingSearch();
		});

		//매칭 modal popup 초기화
		$( "#modal_order_matching_pop" ).dialog({
			width: 1024,
			height: 800,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "red-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
		});

		//매칭 modal popup 초기화
		$( "#modal_matching_list" ).dialog({
			width: 1024,
			height: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "red-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
		});

		//매칭내역확인 버튼 바인딩
		$(".btn-matching-confirm-pop").on("click", function(){
			OrderMatchingConfirmPopOpen($(this));
		});

		//매칭내역확인 버튼 바인딩
		$(".btn-matching-confirm-xls-down").on("click", function(){
			OrderMatchingConfirmListXlsDown();
		});

		showLoader();
	};

	/**
	 * 매칭 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderMatchingGridInit = function(){

		$("#grid_list").jqGrid({
			url: './order_matching_grid.php',
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
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 80, sortable: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: true},
				{ label: '상품 코드', name: 'market_product_no', index: 'market_product_no', width: 100, sortable: true},
				{ label: '상품명', name: 'market_product_name', index: 'market_product_name', width: 200, sortable: false, align: 'left'},
				{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 200, sortable: false, align: 'left'},
				{ label: '수량', name: 'order_cnt', index: 'order_cnt', width: 100, sortable: false},
				{ label: '구분', name: 'btn_type', index: 'btn_type', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return '';

					}},
				{ label: '매칭', name: 'btn_matching', index: 'btn_matching', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return ' <a href="javascript:;" class="xsmall_btn green_btn btn-order-product-matching" data-order_idx="'+rowobject.order_idx+'">설정</a>';

					}}
			],
			rowNum: 100,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//매칭 설정
				$(".btn-order-product-matching").on("click", function(){
					OrderMatchingPopOpen($(this));
				});

				if($("#grid_list").getGridParam("records") == 0){
					$(".ui-jqgrid-bdiv > div > div").append('<div class="grid_center_message">모든 발주가 매칭되었습니다.</div>');
				}

				hideLoader();
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
				OrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderMatchingSearch();
		});

	};

	var customButtonClicked = function(){

	};

	/**
	 * 매칭 페이지 자동 매칭 실행
	 * @constructor
	 */
	var OrderMatchingAuto = function(isReload){
		var p_url = "order_matching_proc.php";
		var dataObj = new Object();
		dataObj.mode = "exec_auto_matching";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}

			if(!isReload) {

				//판매처 그룹 및 판매처 선택창 초기화
				CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
				$(".seller_idx").SumoSelect({
					placeholder: '전체 판매처',
					captionFormat : '{0}개 선택됨',
					captionFormatAllSelected : '{0}개 모두 선택됨',
					search: true,
					searchText: '판매처 검색',
					noMatch : '검색결과가 없습니다.'
				});

				OrderMatchingGridInit();

			}else{
				OrderMatchingSearch();
			}

		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 매칭 목록/검색
	 * @constructor
	 */
	var OrderMatchingSearch = function(){
		$(".grid_center_message").remove();
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매칭 팝업 열기
	 * @param $obj : "설정" 버튼 object
	 * @constructor
	 */
	var OrderMatchingPopOpen = function($obj){
		var p_url = "order_matching_pop.php";
		var dataObj = new Object();
		dataObj.order_idx = $obj.data("order_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_matching_pop").html(response);
				$("#modal_order_matching_pop").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 매칭 팝업 닫기
	 * @constructor
	 */
	var OrderMatchingPopClose = function(){
		$("#modal_order_matching_pop").html("");
		$("#modal_order_matching_pop").dialog( "close" );
	};

	/**
	 * 매칭 팝업 페이지 초기화
	 */
	var OrderMatchingPopInit = function(){

		//닫기 버튼 바인딩
		$(".btn-order-matching-pop-close").on("click", function(){
			OrderMatchingPopClose();
		});

		//매칭 버튼 바인딩
		$(".btn-order-matching-save").on("click", function(){
			OrderMatchingSave();
		});


		$("#ord_order_cnt").on("keyup", function(){
			var o_cnt = $(this).val();

			$(".selected_matching_cnt").each(function(i, o){
				var totalCnt = 0;
				var m_cnt = $(this).val();
				totalCnt = Number(o_cnt) * Number(m_cnt);
				//console.log(o_cnt);
				//console.log(m_cnt);
				//console.log(totalCnt);
				$(this).parent().parent().find(".delivery_cnt").val(totalCnt);
			});
		});

		$("#searchFormPop input[name='product_name']").focus();

		//수량 자동 계산 이벤트 바인드
		$("#ord_cnt_auto_cal").on("click", function(){

			if(!$(this).is(":checked")){
				$("#ord_matching_save").prop("checked", false);
				$("#ord_matching_save").prop("disabled", true);
			}else{
				$("#ord_matching_save").prop("disabled", false);
			}

			$(".selected_matching_cnt").each(function(i, o){
				var totalCnt = 0;
				var o_cnt = $("#ord_order_cnt").val();
				var m_cnt = $(this).val();
				if($("#ord_cnt_auto_cal").is(":checked")) {
					totalCnt = Number(o_cnt) * Number(m_cnt);
				}else{
					totalCnt = Number(m_cnt);
				}

				$(this).parent().parent().find(".delivery_cnt").val(totalCnt);
			});

		});

		$("#ord_matching_save").on("click", function(e){

			if(!$("#ord_cnt_auto_cal").is(":checked")){
				e.preventDefault();
			}

		});

		//Grid 초기화
		OrderMatchingPopGridInit();
	};

	/**
	 * 매칭 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderMatchingPopGridInit = function(){
		var colModel;

		if(is_vendor_seller) {
			//벤더 판매처의 경우 본인등급의 판매가만 표시
		}else{
			//관리자는 모든 판매가 표시
			colModel =  [
				{
					label: '수정', name: '수정', width: 60, sortable: false, is_use : true, hidden: true, formatter: function (cellvalue, options, rowobject) {
						//console.log(rowobject);
						return '<a href="javascript:;" class="xsmall_btn btn-product-modify" data-idx="' + rowobject.product_idx + '">수정</a>';
					}
				},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 50, is_use : true},
				{ label: '상품타입', name: 'code_name', index: 'code_name', width: 40, is_use : true},
				{ label: '상품타입', name: 'product_sale_type', index: 'product_sale_type', width: 0, hidden: true},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: true},
				{ label: '판매가(A)', name: 'product_option_sale_price_A', index: 'product_option_sale_price_A', width: 80, sortable: false, hidden: true, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(B)', name: 'product_option_sale_price_B', index: 'product_option_sale_price_B', width: 80, sortable: false, hidden: true, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(C)', name: 'product_option_sale_price_C', index: 'product_option_sale_price_C', width: 80, sortable: false, hidden: true, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(D)', name: 'product_option_sale_price_D', index: 'product_option_sale_price_D', width: 80, sortable: false, hidden: true, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(E)', name: 'product_option_sale_price_E', index: 'product_option_sale_price_E', width: 80, sortable: false, hidden: true, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false, align: 'left', hidden: false},
				{ label: '매칭', name: 'btn_matching', index: 'btn_matching', width: 50, sortable: false, formatter: function(cellvalue, options, rowobject){
						return ' <a href="javascript:;" class="xsmall_btn green_btn btn-product-option-matching-select"' +
							' data-rowNum="'+options.rowId+ '"' +
							' data-product_name="'+rowobject.product_name+'"' +
							'">선택</a>';
					}}
			];
		}

		if(!isDYLogin){
			$.each(colModel, function(i, o){
				if(o.name == 'supplier_name' || o.name == 'code_name'){
					colModel[i].hidden = true;
				}
			});
		}

		$("#grid_list_pop").jqGrid({
			url: '/order/order_matching_pop_option_list_grid.php',
			mtype: "GET",
			datatype: "local",
			postData:{
				param: $("#searchFormPop").serialize()
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager_pop',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(complete_data){
				//수정
				$(".btn-product-option-matching-select").on("click", function(){
					OrderMatchingProductOptionSelect($(this));
				});

				if($("#grid_list_pop").getGridParam("records") == 0) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$(".matching_pop_grid_wrap .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$(".matching_pop_grid_wrap .no-data").remove();
				}

				// 발주 부서 요청 - 상품명이 같고 타입이 다르면 사입만 표시 TODO : 문제 발생 여지 있음 kyu 2019-07-23
				var is_vendor = complete_data["is_vendor"];
				if (is_vendor) {
					var hide_targets = new Array();

					var allRowId = $("#grid_list_pop").getDataIDs();
					$.each(allRowId, function(i, o){
						var row_data1 = $("#grid_list_pop").getRowData(o);
						if (row_data1.product_sale_type == "SELF") return true;

						$.each(allRowId, function(i2, o2){
							if (o == o2) return true;
							var row_data2 = $("#grid_list_pop").getRowData(o2);
							if (row_data2.product_sale_type == "SELF") {
								if (row_data1.product_name == row_data2.product_name) {
									if (hide_targets.indexOf(o) < 0) {
										hide_targets.push(o);
									}
								}
							}
						});
					});

					if (hide_targets.length) {
						hide_targets.forEach(function(e){
							$("#grid_list_pop #"+ e).hide();
						});
					}
				}
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_pop", $(".container.popup .tb_wrap"));
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchFormPop").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				OrderMatchingPopSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			OrderMatchingPopSearch();
		});

		var colModel = [
			{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
			{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, sortable: false},
			{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false},
			{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false},
			{ label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false, align: 'left'},
			{ label: '매칭수량', name: 'matching_cnt', index: 'matching_cnt', width: 60, sortable: false, hidden: false, align: 'left', formatter: function(cellvalue, options, rowobject){
					var cnt = 1;

					/**
					 * [주문수량 * 매칭수량 = 배송수량] 으로 사용하기때문에 매칭 수량은 무조건 1 (ssawoona 수정)
					 if($("#ord_cnt_auto_cal").is(":checked")){
							cnt = $("#ord_order_cnt").val();
						}
					 */
					return '<input type="text" class="w100per onlyNumberDynamic selected_matching_cnt" value="'+cnt+'" />';

				}},
			{ label: '배송수량', name: 'delivery_cnt', index: 'delivery_cnt', width: 60, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
					return '<input type="text" class="w100per onlyNumberDynamic delivery_cnt" value="" readonly="readonly" />';

				}},
			{ label: '삭제', name: 'btn_matching', index: 'btn_matching', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
					return ' <a href="javascript:;" class="xsmall_btn red_btn btn-delete-matching-selected" data-rowid="'+options.rowId+'">삭제</a>';
				}}
		];

		if(!isDYLogin){
			$.each(colModel, function(i, o){
				if(o.name == 'supplier_name'){
					colModel[i].hidden = true;
				}
			});
		}

		//매칭 선택 테이블
		$("#grid_list_pop_target").jqGrid({
			datatype: "local",
			postData:{
				param: $("#searchFormPop").serialize()
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			sortname: 'product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			idPrefix: 'selected_',
			height: 150,
			loadComplete: function(){

				$("body").on("keyup", ".selected_matching_cnt", function(){

					var totalCnt = 0;
					var o_cnt = $("#ord_order_cnt").val();
					var m_cnt = $(this).val();
					if($("#ord_cnt_auto_cal").is(":checked")) {
						totalCnt = Number(o_cnt) * Number(m_cnt);
					}else{
						totalCnt = Number(m_cnt);
					}

					$(this).parent().parent().find(".delivery_cnt").val(totalCnt);
				});
			},
			afterInsertRow : function(rowid){
				//console.log(rowid);
				$("#"+rowid + " .btn-delete-matching-selected").on("click", function(){
					OrderMatchingDeleteforSeledted($(this));
				});

				var totalCnt = 0;
				var o_cnt = $("#ord_order_cnt").val();
				var m_cnt = $("#"+rowid + " .selected_matching_cnt").val();

				if($("#ord_cnt_auto_cal").is(":checked")) {
					totalCnt = Number(o_cnt) * Number(m_cnt);
				}else{
					totalCnt = Number(m_cnt);
				}

				$("#"+rowid + " .delivery_cnt").val(totalCnt);
			}
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_pop_target", $(".container.popup .tb_wrap"));
		}).trigger("resize");
	};

	/**
	 * 매칭 목록/검색
	 * @constructor
	 */
	var OrderMatchingPopSearch = function(){

		var txt1 = $("form[name='searchFormPop'] input[name='product_name']").val();
		var txt2 = $("form[name='searchFormPop'] input[name='product_option_name']").val();

		if($.trim(txt1) == "" && $.trim(txt2) == ""){
			alert('검색어를 입력해주세요.');
			return;
		}

		$("#grid_list_pop").setGridParam({
			datatype: "json",
			url: '/order/order_matching_pop_option_list_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 검색된 상품 목록에서 "선택" 버튼 클릭 시
	 * 매칭 선택 함수
	 * @param $obj : "선택" 버튼 object
	 * @constructor
	 */
	var OrderMatchingProductOptionSelect = function($obj){
		var rowNum = $obj.data("rownum");
		var rowData = $("#grid_list_pop").getRowData(rowNum);
		var product_option_idx = rowData.product_option_idx;
		var targetData = $("#grid_list_pop_target").getRowData();
		var isExist = false;
		for(tRow in targetData){
			if(targetData[tRow].product_option_idx == product_option_idx) {
				isExist = true;
			}
		}
		if(isExist) {
			alert('이미 선택되어 있습니다.');
			return;
		}

		$("#grid_list_pop_target").jqGrid('addRowData', product_option_idx, rowData);
	};

	/**
	 * 상품 매칭 - 선택된 상품 삭제
	 * @param $obj : "삭제" 버튼 Object
 	 * @constructor
	 */
	var OrderMatchingDeleteforSeledted = function($obj){
		var rowId = $obj.data("rowid");
		$("#grid_list_pop_target").delRowData("selected_"+rowId);
	};

	/**
	 * 상품 매칭 실행!!
	 * @constructor
	 */
	var OrderMatchingSave = function(){
		var selected_data = $("#grid_list_pop_target").getRowData();
		//선택된 상품 확인
		if(selected_data.length == 0){
			alert('매칭할 상품을 선택해주세요.');
			return;
		}

		//선택된 상품 to Array
		var selData = [];
		$.each(selected_data, function(i, o){
			var row = new Object();
			row.product_idx = o.product_idx;
			row.product_option_idx = o.product_option_idx;
			row.product_option_cnt = $(".selected_matching_cnt").eq(i).val(); //매칭수량

			//배송수량 - 19.07.17
			//배송수량을 저장 하는 것으로 변경
			row.product_delivery_cnt = $(".delivery_cnt").eq(i).val();

			selData.push(row);
		});

		//console.log(selData);
		//상품목록  array to json
		var matching_product_list_json = JSON.stringify(selData);

		var p_url = "/order/order_matching_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_matching_save";
		dataObj.order_idx = $("#order_idx").val();
		dataObj.seller_idx = $("#seller_idx").val();
		dataObj.market_product_no = $("#ord_market_product_no").val();
		dataObj.market_product_name = $("#ord_market_product_name").val();
		dataObj.market_product_option = $("#ord_market_product_option").val();
		dataObj.order_cnt = $("#ord_order_cnt").val();
		dataObj.matching_save = ($("#ord_matching_save").is(":checked")) ? "Y" : "N";
		dataObj.product_list = matching_product_list_json;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert("매칭되었습니다.");

				//팝업닫기
				OrderMatchingPopClose();

				//매칭 저장 시 자동매칭 실행
				//매칭 미 저장 시 발주 미매칭 목록 reLoad
				if(dataObj.matching_save == "Y") {
					OrderMatchingAuto(true);
				}else{
					OrderMatchingSearch();
					hideLoader();
				}
			}else{
				alert(response.msg);
				hideLoader();
			}

		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 매칭 내역 확인 팝업 열기
	 * @param $obj : "설정" 버튼 object
	 * @constructor
	 */
	var OrderMatchingConfirmPopOpen = function($obj){
		var p_url = "order_matching_confirm_list_pop.php";
		var dataObj = new Object();
		dataObj.seller_idx = $(".seller_idx:eq(0) option:selected").val();
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_matching_list").html(response);
				$("#modal_matching_list").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 매칭 내역 확인 팝업 초기화
	 * @constructor
	 */
	var OrderMatchingConfirmPopInit = function(){

		//닫기 버튼 바인딩
		$(".btn-order-matching-pop-close").on("click", function(){
			$("#modal_matching_list").html("");
			$("#modal_matching_list").dialog( "close" );
		});

		var colModel;

		colModel =  [
			{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, hidden: true, align: 'left'},
			{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, hidden: false, align: 'left'},
			{ label: '판매처 상품코드', name: 'market_product_no', index: 'market_product_no', width: 100, hidden: false, align: 'left'},
			{ label: '판매처 옵션', name: 'market_product_option', index: 'market_product_option', width: 100, hidden: false, align: 'left'},
			{ label: '매칭정보', name: 'matching_info', index: 'matching_info', width: 200, hidden: false, align: 'left', formatter: function(cellvalue, options, rowobject){
					var rst = cellvalue;
					rst = rst.replace(/\[;;\]/g, '<br>');
					return rst;
				}},
			{ label: '취소', name: 'btn_action', index: 'btn_action', width: 60, hidden: false, align: 'center', formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="btn red_btn btn-matching-cancel" data-order_idx="'+rowobject.order_idx+'">매칭취소</a>';
				}},
		];

		$("#grid_list_confirm").jqGrid({
			url: '/order/order_matching_confirm_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				seller_idx : $("#pop_seller_idx").val()
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: colModel,
			rowNum: 10000,
			pgbuttons : false,
			pgtext: null,
			sortname: 'order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){
				$(".btn-matching-cancel").on("click", function(){

					OrderMatchingCancel($(this).data("order_idx"));

				});

			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeByTargetAndMinusMarginH("#grid_list_confirm", $("#modal_matching_list"), 100);
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchFormPop").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				OrderMatchingPopSearch();
			}
		});
	};

	/**
	 * 매칭 내역 확인 목록/검색
	 * @constructor
	 */
	var OrderMatchingConfirmSearch = function(){
		$("#grid_list_confirm").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				seller_idx : $("#pop_seller_idx").val()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매칭 내역 취소
	 * @param order_idx
	 * @constructor
	 */
	var OrderMatchingCancel = function(order_idx){
		var p_url = "/order/order_matching_proc.php";
		var dataObj = new Object();
		dataObj.mode = "cancel_matching";
		dataObj.order_idx = order_idx;

		//
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				OrderMatchingConfirmSearch();
				OrderMatchingSearch();
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 매칭 내역 엑셀 다운로드
	 * @constructor
	 */
	var OrderMatchingConfirmListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var dataObj = {
			seller_idx: $("#pop_seller_idx").val()
		};

		var url = "order_matching_confirm_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("MATCHING_CONFIRM_LIST", function(){
				Order.OrderMatchingConfirmListXlsDownComplete();
			});
		}, 500);
	};

	var OrderMatchingConfirmListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 합포 페이지 초기화
	 */
	var OrderPackageInit = function(){

		//다음 버튼 바인딩
		$(".btn-next-complete").on("click", function(){
			//alert('준비중');
			//합포 재 확인
			var callback = function(isConfirm) {
				if (isConfirm) {
					location.href="order_complete.php";
				}
			};

			recheckOrderPackageAble(callback);
		});

		//자동합포 안내 modal popup 초기화
		$( "#modal_auto_pack_pop" ).dialog({
			width: 550,
			autoOpen: false,
			modal: true,
			minHeight: 0,
			maxHeight: 800,
			classes: {
				"ui-dialog-titlebar": "red-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
			buttons: {
				"예": function() {
					AutoOrderPackageExec();

					$( this ).dialog( "close" );
				},
				"아니오": function() {
					//Grid 초기화
					OrderPackageGridInit();

					$( this ).dialog( "close" );
				}
			}
		});

		CheckOrderPackageAble();


		//합포 체크 후 하도록 변경
		//Grid 초기화
		//OrderPackageGridInit();
	};

	/**
	 * 합포 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderPackageGridInit = function(){

		$("#grid_list").jqGrid({
			url: './order_package_grid.php',
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
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 80, sortable: false},
				// { label: '합포번호', name: 'order_pack_idx', index: 'order_pack_idx', width: 80, sortable: false},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '상품명', name: 'market_product_name', index: 'market_product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 150, sortable: false, align: 'left'},
				{ label: '수량', name: 'order_cnt', index: 'order_cnt', width: 60, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: false},
				{ label: '주소', name: 'receive_addr1', index: 'receive_addr1', width: 100, sortable: false},
				{ label: '전화', name: 'receive_tp_num', index: 'receive_tp_num', width: 100, sortable: false},
				{ label: '핸드폰', name: 'receive_hp_num', index: 'receive_hp_num', width: 100, sortable: false},
				{ label: '합포', name: 'btn_package', index: 'btn_package', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						if(rowobject.inner_no > 1 && rowobject.order_idx == rowobject.order_pack_idx){
							return ' <a href="javascript:;" class="xsmall_btn green_btn btn-order-package" data-order_idx="'+rowobject.order_idx+'" data-min_order_idx="'+rowobject.min_order_idx+'">합포</a>';
						}else if(rowobject.inner_no > 1 && rowobject.order_idx != rowobject.order_pack_idx){
							return '<span class="lb_blue">합포</span>';
						}else{
							return '';
						}
					}}
			],
			rowNum: 10000,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			idPrefix: 'packageRow_',
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//합포 버튼 바인딩
				$(".btn-order-package").on("click", function(){
					OrderPackageJoin($(this));
				});
			},
			rowattr: function(rowData, currentObj, rowId){
				if(currentObj.inner_no > 1){
					return {'class' : 'light_gray'}
				}
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
				OrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderPackageSearch();
		});

	};

	/**
	 * 합포 목록/검색
	 * @constructor
	 */
	var OrderPackageSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var CheckOrderPackageAble = function(){
		var p_url = "/order/order_package_proc.php";
		var dataObj = new Object();
		dataObj.mode = "check_package_able";

		//
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			hideLoader();

			if (response.result) {
				if(response.data.length > 0){
					var sum = 0;
					$.each(response.data, function(i, o){
						sum += Number(o.cnt);
					});

					var html = "";
					html += '<p>합포가능한 주문이 ' + sum + '건 있습니다.</p>';
					$.each(response.data, function(i, o){
						html += '<p>['+o.order_idx_list+']</p>';
					});
					html += '<p>합포하시겠습니까?</p>';

					$("#modal_auto_pack_pop").html(html);
					$("#modal_auto_pack_pop").dialog( "open" );

					//
					// if(confirm('합포가능한 주문이 ' + sum + '건 있습니다.\n합포하시겠습니까?')){
					// 	AutoOrderPackageExec();
					// }else{
					// 	//Grid 초기화
					// 	OrderPackageGridInit();
					// }
				}else{
					//Grid 초기화
					OrderPackageGridInit();
				}
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var recheckOrderPackageAble = function(callback) {
		var p_url = "/order/order_package_proc.php";
		var dataObj = new Object();
		dataObj.mode = "check_package_able";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				var sum = 0;
				$.each(response.data, function(i, o){
					sum += Number(o.cnt);
				});

				var isConfirm = false;
				if (sum > 0) {
					isConfirm = confirm("합포 가능한 주문이 " + sum + "건 남아있습니다. 정말 진행할까요?\n\n주의 : 합포하지 않은 주문은 다시 자동 합포할 수 없습니다.");
				} else {
					isConfirm = confirm("발주를 완료하시겠습니까?");
				}

				if (callback) callback(isConfirm);
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var AutoOrderPackageExec = function(){
		var p_url = "/order/order_package_proc.php";
		var dataObj = new Object();
		dataObj.mode = "auto_package_exec";

		//
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				alert(response.data + "건의 주문이 합포되었습니다.\n다음 버튼을 눌러 진행해주세요.");
				//Grid 초기화
				OrderPackageGridInit();
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 합포 실행!
	 * @param $obj : "합포" 버튼 Object
	 * @constructor
	 */
	var OrderPackageJoin = function($obj){
		if(confirm('합포 하시겠습니까?')){
			var current_order_idx = $obj.data("order_idx");
			var parent_order_idx = $obj.data("min_order_idx");

			var p_url = "/order/order_package_proc.php";
			var dataObj = new Object();
			dataObj.mode = "sum_package";
			dataObj.current_order_idx = current_order_idx;
			dataObj.parent_order_idx = parent_order_idx;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//합포 목록 reLoad
					OrderCollectSearch();
				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 원본 다운로드 페이지 초기화
	 * @constructor
	 */
	var OrderCollectPageInit = function(){

		//판매처 선택창 초기화
		$(".seller_idx").SumoSelect({
			placeholder: '판매처 선택',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		//Grid 초기화
		OrderCollectGridInit();
	};

	/**
	 * 원본 다운로드 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderCollectGridInit = function(){
		$("#grid_list").jqGrid({
			url: './order_collect_grid.php',
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
				{ label: '판매처코드', name: 'seller_idx', index: 'seller_idx', width: 100, sortable: true},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: true},
				{ label: '발주시간', name: 'order_collect_regdate', index: 'order_collect_regdate', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '발주작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '발주 수량', name: 'collect_order_count', index: 'collect_order_count', width: 100, sortable: false},
				{ label: '총 주문 수량', name: 'collect_count', index: 'collect_count', width: 100, sortable: false},
				{ label: '파일명', name: 'collect_filename', index: 'collect_filename', width: 200, sortable: false, formatter: function(cellvalue, options, rowobject){
						return '<a href="/proc/_order_download.php?filename='+cellvalue+'">'+cellvalue+'</a>';
					}},
				{ label: '발주성공', name: 'collect_state', index: 'collect_state', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						if(cellvalue == 'S'){
							return '성공';
						}else if(cellvalue == 'F'){
							return '실패';
						}else{
							return '';
						}
					}
					, cellattr: function(rowid, val, rowObject, cm, rdata){
						if(rowObject.collect_state == "F"){
							return ' name="state_fail" ';
						}
					}
				},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'order_collect_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$("td[name='state_fail']").parent().addClass("bg_danger");
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
				OrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderCollectSearch();
		});
	};

	/**
	 * 원본 목록/검색
	 * @constructor
	 */
	var OrderCollectSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 자동수집조회 페이지 초기화
	 * @constructor
	 */
	var OrderCollectAutoPageInit = function(){

		//판매처 선택창 초기화
		$(".seller_idx").SumoSelect({
			placeholder: '판매처 선택',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		//Grid 초기화
		OrderCollectAutoGridInit();
	};

	/**
	 * 자동수집조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderCollectAutoGridInit = function(){
		$("#grid_list").jqGrid({
			url: './order_collect_auto_grid.php',
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
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '판매처코드', name: 'seller_idx', index: 'seller_idx', width: 100, sortable: false, hidden: true},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '수집시작', name: 'collect_sdate', index: 'collect_sdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '수집종료', name: 'collect_edate', index: 'collect_edate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '수집건수', name: 'collect_count', index: 'collect_count', width: 60, sortable: false},
				{ label: '발주건수', name: 'collect_order_count', index: 'collect_order_count', width: 60, sortable: false},
				{ label: '결과', name: 'collect_state', index: 'collect_state', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
					if(cellvalue == "S"){
						return '성공';
					} else {
						return '실패';
					}
				}, cellattr: function(rowid, val, rowObject, cm, rdata){
					if(rowObject.collect_state == "F"){
						return ' name="state_fail" ';
					}
				}},
				{ label: '비고', name: 'collect_message', index: 'collect_message', width: 150, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'order_collect_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$("td[name='state_fail']").parent().addClass("bg_danger");
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
				OrderCollectAutoSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderCollectAutoSearch();
		});
	};

	/**
	 * 자동수집조회 목록/검색
	 * @constructor
	 */
	var OrderCollectAutoSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 발주완료 페이지 초기화
	 * @constructor
	 */
	var OrderCompleteInit = function(){

		//일괄접수처리 버튼 바인딩 190703 update by kyu
		/*$(".btn-order-accept-all").on("click", function(){
			OrderAcceptWholeConfirm();
		});*/


		OrderCompleteGridInit();
	};

	/**
	 * 발주완료 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderCompleteGridInit = function(){
		$("#grid_list").jqGrid({
			url: './order_complete_grid.php',
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
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, align: 'left'},
				{ label: '상품주문수량', name: 'sum_product_option_cnt', index: 'sum_product_option_cnt', width: 150, sortable: false},
                { label: '정상재고수량', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 150, sortable: false},
				{ label: '재고부족수량', name: 'shortage_cnt', index: 'shortage_cnt', width: 150, sortable: false ,formatter: function(cellvalue, options, rowobject){
						return Math.abs(rowobject.sum_product_option_cnt - rowobject.stock_amount_NORMAL);
					}}
			],
			rowNum: 10000,
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'OPM.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				if(userData.shortage_product == 0){
                    $("#shortage_product").text("0");
                }else{
                    $("#shortage_product").text(userData.shortage_product);
                }
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
				OrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderPackageSearch();
		});
	};

	/**
	 * 확장주문검색 페이지 초기화
	 * @constructor
	 */
	var OrderSearchListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		//항목설정 팝업
		$(".btn-column-setting-pop").on("click", function(){
			Common.newWinPopup("/common/column_setting_pop.php?target=ORDER_SEARCH_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});

		//다운로드 버튼 바인딩
		$(".btn-order-search-xls-down").on("click", function(){
			OrderSearchListXlsDown();
		});

		OrderSearchListGridInit();
	};

	/**
	 * 확장주문검색 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderSearchListGridInit = function(){

		var grid_cookie_name = "stock_list";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_search_list_grid.php',
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
			colModel: _gridColModel,
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'A.order_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//재고조정
				$(".btn-stock-control").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					StockControlPopOpen(product_option_idx, 'NORMAL', '');
				});

				//각 재고 수량 클릭 시 재고조정
				$(".btn-stock-control-status").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_status = $(this).data("stock_status");
					StockControlPopOpen(product_option_idx, stock_status, '');
				});

				//상품 별 로그
				$(".btn-stock-product-log").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					StockLogViewerPopOpen(product_option_idx);
				});

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".summary_order_amt_sum").text(Common.addCommas(userData.order_amt_sum));
				$(".summary_order_calculation_amt_sum").text(Common.addCommas(userData.order_calculation_amt_sum));
				$(".summary_shipped_cnt").text(Common.addCommas(userData.sum_shipped));
				$(".summary_order_cnt").text(Common.addCommas(userData.cnt_order));
				$(".summary_product_cnt").text(Common.addCommas(userData.sum_product_option_cnt));

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("order_search_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "order_search_list");
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
				OrderSearchListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderSearchListSearch();
		});
	};

	/**
	 * 확장주문검색 목록/검색
	 * @constructor
	 */
	var OrderSearchListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};


	var xlsDownIng = false;
	var xlsDownInterval;

	/**
	 * 확장주문검색 목록 엑셀 다운로드
	 * @constructor
	 */
	var OrderSearchListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		if($(".chk-include-sum").eq(0).is(":checked")){
			$("#include_sum").val("Y");
		}else{
			$("#include_sum").val("N");
		}

		var param = $("#searchForm").serialize();

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "order_search_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("ORDER_SEARCH_LIST", function(){
				Order.OrderSearchListXlsDownComplete();
			});
		}, 500);
	};


	var OrderSearchListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 주문일괄삭제 페이지 초기화
	 * @constructor
	 */
	var OrderBatchDeleteInit = function(){
		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		//주문삭제 이력 버튼 바인딩
		$(".btn-batch-delete-log-pop").on("click", function(){
			Common.newWinPopup("/order/order_batch_delete_log_pop.php", 'order_batch_delete_log_pop', 800, 720, 'no');
		});

		OrderBatchDeleteGridInit();
	};

	var OrderBatchSearchParam = new Object();
	OrderBatchSearchParam.date = "";
	OrderBatchSearchParam.time_start = "";
	OrderBatchSearchParam.time_end = "";
	/**
	 * 주문일괄삭제 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderBatchDeleteGridInit = function(){
		//주문일괄삭제 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_batch_delete_grid.php',
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
				{ label: '발주일시', name: 'order_date', index: 'order_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '판매처코드', name: 'seller_idx', index: 'seller_idx', width: 100, sortable: false},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '주문건수', name: 'order_cnt', index: 'order_cnt', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '송장입력건수', name: 'invoice_cnt', index: 'invoice_cnt', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '배송처리건수', name: 'shipped_cnt', index: 'shipped_cnt', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '삭제', name: 'util_btn', index: 'util_btn', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return ' <a href="javascript:;" class="xsmall_btn blue_btn btn-order-format-seller" data-seller_idx="'+rowobject.seller_idx+'">주문삭제</a>';

					}},
			],
			rowNum: 10000,
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'S.seller_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//주문삭제
				$(".btn-order-format-seller").on("click", function(){
					var seller_idx = $(this).data("seller_idx");
					OrderBatchDeleteExecute(seller_idx);
				});

				OrderBatchSearchParam.date = $("#searchForm input[name='date']").val();
				OrderBatchSearchParam.time_start = $("#searchForm input[name='time_start']").val();
				OrderBatchSearchParam.time_end = $("#searchForm input[name='time_end']").val();
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
				OrderBatchDeleteSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderBatchDeleteSearch();
		});
	};

	/**
	 * 주문일괄삭제 목록/검색
	 * @constructor
	 */
	var OrderBatchDeleteSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 주문일괄삭제 - 주문삭제 실행!!
	 * @param seller_idx
	 * @constructor
	 */
	var OrderBatchDeleteExecute = function(seller_idx){

		if(!confirm('주문을 삭제하시겠습니까?')) {
			return;
		}
		var p_url = "/order/order_batch_delete_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_batch_delete_seller_idx";
		dataObj.seller_idx = seller_idx;
		dataObj.order_date = OrderBatchSearchParam.date;
		dataObj.order_time_start = OrderBatchSearchParam.time_start;
		dataObj.order_time_end = OrderBatchSearchParam.time_end;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.data+"건의 주문이 삭제되었습니다.");
				OrderBatchDeleteSearch();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 주문삭제 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var OrderBatchDeleteLogPopInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		$("#grid_list").jqGrid({
			url: './order_batch_delete_log_pop_grid.php',
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
				{ label: '삭제일시', name: 'order_delete_log_regdate', index: 'order_delete_log_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.toDateTime(cellvalue);
				}},
				{ label: '발주일', name: 'order_date', index: 'order_date', width: 80, sortable: false, hidden: true},
				{ label: '발주시간', name: 'order_time', index: 'A.stock_order_idx', width: 120, sortable: true},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: true},
				{ label: '삭제된 주문개수', name: 'order_delete_log_count', index: 'order_delete_log_count', width: 100, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.order_delete_log_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//브라우저 리사이즈 trigger
				$(window).trigger("resize");
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
				OrderBatchDeleteLogPopSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderBatchDeleteLogPopSearch();
		});
	};

	/**
	 * 주문삭제 이력 팝업 목록/검색
	 * @constructor
	 */
	var OrderBatchDeleteLogPopSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 일괄합포제외 페이지 초기화
	 * @constructor
	 */
	var OrderPackageExceptInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});


		//일괄합포제외 버튼 바인딩
		$(".btn-order-package-except-batch").on("click", function(){
			OrderPackageExceptBatch();
		});

		//엑셀다운로드 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			OrderPackageExceptXlsDown();
		});

		//Grid 바인딩
		OrderPackageExceptGridInit();
	};

	var OrderPackageExceptAry = new Object();
	/**
	 * 일괄합포제외 목록 Grid 초기화
	 * @constructor
	 */
	var OrderPackageExceptGridInit = function(){
		//일괄합포제외 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_package_except_grid.php',
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
				{label: '합포IDX', name: 'order_pack_idx', index: 'order_pack_idx', width: 100, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKey},
				{label: '수령자', name: 'receive_name', index: 'receive_name', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
				{label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, sortable: false, hidden: true},
				{label: '관리번호', name: 'order_idx2', index: 'order_idx2', width: 100, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : rowobject.order_idx;
					}},
				{label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 100, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : Common.toDateTimeOnlyDate(cellvalue);
					}},
				{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, sortable: false, hidden: true},
				{label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 60, sortable: false},
				{label: '합포제외', name: 'input_package_except', index: 'input_package_except', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<div class="div_except_set_'+rowobject.order_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'">' +
							'<input type="checkbox" class="chk_package_except chk_package_except_'+rowobject.order_idx+'" data-product_option_idx="'+rowobject.product_option_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-order_idx="'+rowobject.order_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'">' +
							' <input type="text" class="onlyNumberDynamic w30px dis_none input_package_except input_package_except_'+rowobject.order_matching_idx+'" data-product_option_idx="'+rowobject.product_option_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'" maxlength="3" value="'+rowobject.product_option_cnt+'" disabled="disabled"/>' +
							'<input type="hidden" name="product_option_cnt_remain" class="product_option_cnt_remain product_option_cnt_remain_'+rowobject.order_matching_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-product_option_cnt="'+rowobject.product_option_cnt+'"  data-order_matching_idx="'+rowobject.order_matching_idx+'" value="'+rowobject.product_option_cnt+'" />' +
							'</div>';
						return btnz;

					}},
				{label: '제외 No', name: 'preview_package_except', index: 'preview_package_except', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						var result = '';
						result = '<div class="package_except_list package_except_list_'+rowobject.order_matching_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'"></div>';
						return result;

					}},
				{label: '복수제외', name: 'btn_except_util', index: 'btn_except_util', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<div class="btn_multi_except_set_'+rowobject.order_pack_idx+'">' +
							'<a href="javascript:;" class="xsmall_btn red_btn btn-package-multi-except" data-except_no="0" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-product_option_cnt="'+rowobject.product_option_cnt+'">복수제외</a>' +
							'<br><a href="javascript:;" class="xsmall_btn btn-package-except-init" data-order_pack_idx="'+rowobject.order_pack_idx+'">초기화</a>' +
							'</div>';
						return btnz;

					}},
				{label: '제외실행', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn red_btn btn-package-except-exec" data-order_pack_idx="'+rowobject.order_pack_idx+'">제외실행</a>'
						return btnz;

					}
					, cellattr:jsFormatterCompareAndSetRowSpan
				},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: '',
			sortorder: "",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans).addClass("bg-force-white");
					}
				});

				$("td[data-is-key='1']").parent().find("td").addClass("bold_top_line");

				//삭제 버튼 바인딩
				$(".btn-product-matching-delete").on("click", function(){
					ProductMatchingDeleteOne($(this).data("matching_info_idx"));
				});

				$(".chk_package_except").on("click", function(){

					var chk = $(this).is(":checked");
					var idx = $(this).data("order_matching_idx");

					$(".input_package_except_"+idx).prop("disabled", !chk);//.val("1");
					if(chk){
						$(".input_package_except_"+idx).removeClass("dis_none");
					}else{
						$(".input_package_except_"+idx).addClass("dis_none");
					}
				});

				$(".btn-package-multi-except").on("click", function(){
					var except_no = parseInt($(this).data("except_no"));
					except_no++;
					var _order_pack_idx = $(this).data("order_pack_idx");
					var $chk_list = $(".chk_package_except[data-order_pack_idx='"+_order_pack_idx+"']:checked");
					var packObj = new Array();

					var isOk = true;

					if($chk_list.length == 0) return;

					$chk_list.each(function(i, o){

						var $_chk = $(this);
						var _order_matching_idx = $_chk.data("order_matching_idx");
						var _order_idx = $_chk.data("order_idx");
						var _product_option_idx = $_chk.data("product_option_idx");
						var $_input = $(".input_package_except_"+_order_matching_idx);
						var _num = $_input.val();
						var _remain = parseInt($(".div_except_set_"+_order_idx+" .product_option_cnt_remain_"+_order_matching_idx).val());
						if($_chk.is(":checked")){

							if(Math.floor(_num) != _num || !$.isNumeric(_num) || _num < 1){
								alert("숫자만 입력 가능합니다.");
								isOk = false;
								return false;
							}else{
								_num = parseInt(_num);
							}

							if(_remain < _num) {
								alert("제외 수량이 잘못되었습니다.");
								isOk = false;
								return false;
							}

							var tmpObj = new Object();
							tmpObj.except_no = except_no;
							tmpObj.order_matching_idx = _order_matching_idx;
							tmpObj.order_pack_idx = _order_pack_idx;
							tmpObj.order_idx = _order_idx;
							tmpObj.product_option_idx = _product_option_idx;
							tmpObj.product_option_cnt = _num;

							packObj.push(tmpObj);
						}
					});

					if(!isOk) return;

					//order_pack_idx 키 가 존재 하지 않을 경우 생성
					if(!OrderPackageExceptAry.hasOwnProperty(_order_pack_idx)){
						OrderPackageExceptAry[_order_pack_idx] = new Object();
					}

					//함께 일괄제외 할 대상 For~
					$.each(packObj, function(i, o){
						//console.log(i, o);
						if(!OrderPackageExceptAry[_order_pack_idx].hasOwnProperty(except_no)){
							OrderPackageExceptAry[_order_pack_idx][except_no] = [];
						}
						//일괄제외 배열에 추가
						OrderPackageExceptAry[_order_pack_idx][except_no].push(o);

						//남은 수량 계산
						var _calRemain = parseInt($(".div_except_set_"+o.order_idx+" .product_option_cnt_remain_"+o.order_matching_idx).val());
						_calRemain = _calRemain - o.product_option_cnt;
						//$(".product_option_cnt_remain_"+o.product_option_idx).val(_calRemain);
						$(".product_option_cnt_remain_"+o.order_matching_idx).val(_calRemain);

						$(".package_except_list_"+o.order_matching_idx).append('<span>'+except_no+'('+o.product_option_cnt+')</span><br>');

						if(_calRemain == 0){
							$(".div_except_set_"+o.order_idx).find('input[type="checkbox"]').prop("checked", false);
							$(".div_except_set_"+o.order_idx).addClass("dis_none");
						}
					});

					//제외 순번 Update
					$(this).data("except_no", except_no);

					//console.log("except_total", except_total);

				});

				//초기화 버튼 바인딩
				$(".btn-package-except-init").on("click", function(){

					var order_pack_idx = $(this).data("order_pack_idx");

					$obj = $("input[data-order_pack_idx='"+order_pack_idx+"'].product_option_cnt_remain");
					$obj.each(function(){
						$(this).val($(this).data("product_option_cnt"));
					});

					$obj.parent().find(".chk_package_except").prop("checked", false);
					$obj.parent().find(".input_package_except").addClass("dis_none");
					$obj.parent().removeClass("dis_none");

					$(".package_except_list[data-order_pack_idx='"+order_pack_idx+"']").empty();
					$(".btn-package-multi-except[data-order_pack_idx='"+order_pack_idx+"']").data("except_no", "0");

					if(OrderPackageExceptAry.hasOwnProperty(order_pack_idx)){
						OrderPackageExceptAry[order_pack_idx] = {};
						delete OrderPackageExceptAry[order_pack_idx];
					}
				});

				//제외실행 버튼 바인딩
				$(".btn-package-except-exec").on("click", function(){
					var order_pack_idx = $(this).data("order_pack_idx");
					OrderPackageExceptExecOne(order_pack_idx);
				});
			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
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
				OrderPackageExceptSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderPackageExceptSearch();
		});
	};

	/**
	 * jqGrid 셀 병합을 위한 임시 저장변수
	 * @type {{chkval: undefined, rowNo: number, cellId: undefined}}
	 */
	var chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크

	/**
	 * 매칭정보 목록 셀병합 함수 1
	 * jqGrid 셀 병합을 위한 함수 (Key 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterComparePrimaryKey = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(this.id);
		var cellId = this.id + '_row_'+rowObject.order_pack_idx+'-'+cm.name;
		if(chkcell.chkval != rowObject.order_pack_idx && rowid != chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan" data-is-key="1"';
			//alert(result);
			chkcell = {cellId:cellId, chkval:rowObject.order_pack_idx, rowNo: rowid};
		}else{
			result = 'style="display: none;"  rowspanid="'+cellId+'"'; //같을 경우 display none 처리
			//alert(result);
		}
		return result;
	};

	/**
	 * 매칭정보 목록 셀병합 함수 2
	 * jqGrid 셀병합을 위한 함수 (일반 Cell 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterCompareAndSetRowSpan = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(cm);

		var cellId = this.id + '_row_'+rowObject.order_pack_idx+'-'+cm.name;
		if(chkcell.chkval == rowObject.order_pack_idx && rowid == chkcell.rowNo){ //check 값이랑 비교값이 다른 경우

			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';

		}else{
			result = 'style="display: none;" rowspanid="'+cellId+'"'; //같을 경우 display none 처리

		}
		return result;
	};

	/**
	 * 일괄합포제외 목록/검색
	 * @constructor
	 */
	var OrderPackageExceptSearch = function(){
		OrderPackageExceptAry = new Object();
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var OrderPackageExceptExeVars = new Array();
	/**
	 * 일괄합포제외 단일 제외 실행!
	 * @param order_pack_idx
	 * @constructor
	 */
	var OrderPackageExceptExecOne = function(order_pack_idx){

		OrderPackageExceptExeVars = new Array();
		OrderPackageExceptExeVars.push(order_pack_idx);

		//제외 대상
		var _targetOrderExceptObj = new Object();

		//복수 제외 된 내역 담기
		// if (!OrderPackageExceptAry.hasOwnProperty(order_pack_idx)) {
		// 	alert("제외할 대상이 없습니다.");
		// 	return;
		// }

		if(Object.size(OrderPackageExceptAry) > 0) {
			//단순 'var 변수=변수' 할 경우 deep copy 가 되어버림
			//shallow copy 를 위해 jQuery 이용
			var _targetOrderExceptObj = $.extend({}, OrderPackageExceptAry[order_pack_idx]);
			//console.log("copy");
		}

		//제외 No 가져오기
		var except_no = parseInt($(".btn-package-multi-except[data-order_pack_idx='"+order_pack_idx+"']").data("except_no"));

		//if(except_no == 0){

			var last_except_no = Object.size(_targetOrderExceptObj) + 1;

			var $chk_list = $(".chk_package_except[data-order_pack_idx='"+order_pack_idx+"']:checked");

			var tmpOrderPackageExceptAry = new Object();
			var packObj = new Array();
			var isOk = true;
			$chk_list.each(function(i, o){
				$_chk = $(this);
				var _order_idx = $_chk.data("order_idx");
				var _order_matching_idx = $_chk.data("order_matching_idx");
				var _product_option_idx = $_chk.data("product_option_idx");
				var $_input = $(".input_package_except_"+_order_matching_idx);
				var _num = $_input.val();
				var _remain = parseInt($(".div_except_set_"+_order_idx+" .product_option_cnt_remain_"+_order_matching_idx).val());
				if($_chk.is(":checked")){

					if(Math.floor(_num) != _num || !$.isNumeric(_num) || _num < 1){
						alert("숫자만 입력 가능합니다.");
						isOk = false;
						return false;
					}else{
						_num = parseInt(_num);
					}

					if(_remain < _num) {
						alert("제외 수량이 잘못되었습니다.");
						isOk = false;
						return false;
					}

					var tmpObj = new Object();
					tmpObj.except_no = last_except_no;
					tmpObj.order_matching_idx = _order_matching_idx;
					tmpObj.order_pack_idx = order_pack_idx;
					tmpObj.order_idx = _order_idx;
					tmpObj.product_option_idx = _product_option_idx;
					tmpObj.product_option_cnt = _num;

					packObj.push(tmpObj);
				}
			});

			if(!isOk) return;

			//함께 일괄제외 할 대상 For~
		if(packObj.length > 0) {
			_targetOrderExceptObj[last_except_no] = new Array();
			$.each(packObj, function (i, o) {
				_targetOrderExceptObj[last_except_no].push(o);
			});
		}

		//}else {

		//}

		if(Object.size(_targetOrderExceptObj) == 0){
			alert('제외 할 대상이 없습니다');
			return;
		}

		if(!confirm('제외를 실행하시겠습니까?')) {
			return;
		}
		var p_url = "/order/order_package_proc.php";
		var dataObj = new Object();
		dataObj.mode = "package_except_exec_one";
		dataObj.except = _targetOrderExceptObj;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			// if(response.result)
			// {
			// 	alert(response.data+"건이 일괄접수처리 되었습니다.");
			// }else{
			// 	alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			// }

			//합포제외 TD 내용 Hide
			//제외 No TD 내용 Hide
			//복수제외 TD 내용 Hide
			//제외실행 TD 내용 Hide
			//console.log(OrderPackageExceptExeVars.order_pack_idx);

			$.each(OrderPackageExceptExeVars, function(i, o){
				$("*[data-order_pack_idx='"+o+"']").hide();
			});



			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 일괄합포제외 실행!!
	 * 체크된 주문에 대해서만 합포를 실행 한다
	 * 복수제외는 일괄합포제외 되지 않는다!!!!
	 * @constructor
	 */
	var OrderPackageExceptBatch = function(){

		var OrderPackageExceptBatchObject = new Object();

		OrderPackageExceptExeVars = new Array();

		$(".btn-package-except-exec").each(function(){

			//OrderPackageExceptExeVars.push(order_pack_idx);

			var order_pack_idx = $(this).data("order_pack_idx");

			//제외 대상
			var _targetOrderExceptObj = new Object();

			//복수 제외 된 내역 담기
			// if (!OrderPackageExceptAry.hasOwnProperty(order_pack_idx)) {
			// 	alert("제외할 대상이 없습니다.");
			// 	return;
			// }

			if(OrderPackageExceptAry.hasOwnProperty(order_pack_idx)) {
				//단순 'var 변수=변수' 할 경우 deep copy 가 되어버림
				//shallow copy 를 위해 jQuery 이용
				var _targetOrderExceptObj = $.extend({}, OrderPackageExceptAry[order_pack_idx]);
			}

			//제외 No 가져오기
			var except_no = parseInt($(".btn-package-multi-except[data-order_pack_idx='"+order_pack_idx+"']").data("except_no"));

			var last_except_no = Object.size(_targetOrderExceptObj) + 1;

			var $chk_list = $(".chk_package_except[data-order_pack_idx='"+order_pack_idx+"']:checked");

			var tmpOrderPackageExceptAry = new Object();
			var packObj = new Array();
			var isOk = true;
			$chk_list.each(function(i, o){
				$_chk = $(this);
				var _order_matching_idx = $_chk.data("order_matching_idx");
				var _order_idx = $_chk.data("order_idx");
				var _product_option_idx = $_chk.data("product_option_idx");
				var $_input = $(".input_package_except_"+_order_matching_idx);
				var _num = $_input.val();
				var _remain = parseInt($(".div_except_set_"+_order_idx+" .product_option_cnt_remain_"+_order_matching_idx).val());
				if($_chk.is(":checked")){

					if(Math.floor(_num) != _num || !$.isNumeric(_num) || _num < 1){
						alert("숫자만 입력 가능합니다.");
						isOk = false;
						return false;
					}else{
						_num = parseInt(_num);
					}

					if(_remain < _num) {
						alert("제외 수량이 잘못되었습니다.");
						isOk = false;
						return false;
					}

					var tmpObj = new Object();
					tmpObj.except_no = last_except_no;
					tmpObj.order_pack_idx = order_pack_idx;
					tmpObj.order_idx = _order_idx;
					tmpObj.product_option_idx = _product_option_idx;
					tmpObj.product_option_cnt = _num;

					packObj.push(tmpObj);
				}
			});

			if(!isOk) return;

			//함께 일괄제외 할 대상 For~
			if(packObj.length > 0) {
				_targetOrderExceptObj[last_except_no] = new Array();
				$.each(packObj, function (i, o) {
					_targetOrderExceptObj[last_except_no].push(o);
				});
			}

			if(Object.size(_targetOrderExceptObj) > 0){
				OrderPackageExceptExeVars.push(order_pack_idx);

				OrderPackageExceptBatchObject[order_pack_idx] = $.extend({}, _targetOrderExceptObj);
			}
		});

		//console.log(OrderPackageExceptBatchObject);

		//복수제외 및 체크박스가 없는 경우
		if(Object.size(OrderPackageExceptBatchObject) == 0)
		{
			alert("제외할 대상이 없습니다.");
			return;
		}else{

			if(!confirm('일괄합포제외를 실행하시겠습니까?')) return;

			var p_url = "/order/order_package_proc.php";
			var dataObj = new Object();
			dataObj.mode = "package_except_exec_batch";
			dataObj.except = OrderPackageExceptBatchObject;
			showLoader();

			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				// if(response.result)
				// {
				// 	alert(response.data+"건이 일괄접수처리 되었습니다.");
				// }else{
				// 	alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				// }

				//합포제외 TD 내용 Hide
				//제외 No TD 내용 Hide
				//복수제외 TD 내용 Hide
				//제외실행 TD 내용 Hide
				//console.log(OrderPackageExceptExeVars.order_pack_idx);

				$.each(OrderPackageExceptExeVars, function(i, o){
					$("*[data-order_pack_idx='"+o+"']").hide();
				});



				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	var OrderPackageExceptXlsDown = function(){
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "order_package_except_xls_down.php?"+$.param(dataObj);
		hidden_ifrm_common_filedownload.location.href=url;
	};

	/**
	 * 송장등록조회 페이지 초기화
	 * @constructor
	 */
	var InoviceRegListPageInit = function(){

		//판매처 선택창 초기화
		$(".seller_idx").SumoSelect({
			placeholder: '판매처 선택',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "9");

		//엑셀다운로드 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			InoviceRegListXlsDown();
		});

		//Grid 초기화
		InoviceRegListGridInit();
	};

	/**
	 * 송장등록조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var InoviceRegListGridInit = function(){
		$("#grid_list").jqGrid({
			url: './invoice_reg_list_grid.php',
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
				{ label: '판매처코드', name: 'seller_idx', index: 'seller_idx', width: 100, sortable: true, hidden: true,},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 100, sortable: false},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 100, sortable: false},
				{ label: '주문번호상세', name: 'market_order_subno', index: 'market_order_subno', width: 100, sortable: false},
				{ label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: false},
				{ label: '등록시간', name: 'market_invoice_regdate', index: 'market_invoice_regdate', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '등록결과', name: 'market_invoice_state', index: 'market_invoice_state', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
					var rst = "";
					if(cellvalue == "N"){
						rst = "대기";
					}else if(cellvalue == "S") {
						rst = "정상";
					}else if(cellvalue == "E") {
						rst = "오류";
					}else if(cellvalue == "U") {
						rst = "정상";
					}

					return rst;
				}},
				{ label: '등록결과메시지', name: 'market_invoice_msg', index: 'market_invoice_msg', width: 100, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'O.order_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$("td[name='state_fail']").parent().addClass("bg_danger");
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
				InoviceRegListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			InoviceRegListSearch();
		});
	};

	/**
	 * 송장등록조회 목록/검색
	 * @constructor
	 */
	var InoviceRegListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var InoviceRegListXlsDown = function(){
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "invoice_reg_xls_down.php?"+$.param(dataObj);
		hidden_ifrm_common_filedownload.location.href=url;
	};

	/**
	 * 판매처별송장등록 페이지 초기화
	 * @constructor
	 */
	var SellerInvoiceInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		SellerInvoiceGridInit();

		$(".btn-format-pop").on("click", function(){
			Common.newWinPopup2('/order/seller_invoice_pop.php', 'seller_invoice_pop', 600, 600, 0, 1);
		});

		$(".btn-seller-invoice-down").on("click", function(){
			$("#searchForm").attr("action", "seller_invoice_xls_down.php");
			$("#searchForm").attr("target", "xls_hidden_frame");
			$("#searchForm").submit();
		});
	};

	/**
	 * 판매처별송장등록 목록 바인딩 jqGrid
	 * @constructor
	 */
	var SellerInvoiceGridInit = function(){

		var grid_cookie_name = "stock_list";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './seller_invoice_grid.php',
			mtype: "GET",
			postData:{
				param: $("#searchForm").serialize()
			},
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
				{ label: '관리번호', name: 'order_idx', index: 'A.order_idx', width: 80, sortable: false, hidden: false},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 100, sortable: false, hidden: false},
				{ label: '주문번호상세', name: 'market_order_subno', index: 'market_order_subno', width: 100, sortable: false, hidden: false},
				{ label: '상품명', name: 'market_product_name', index: 'market_product_name', width: 100, sortable: false, hidden: false, align: 'left'},
				{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 100, sortable: false, hidden: false, align: 'left'},
				{ label: '개수', name: 'order_cnt', index: 'order_cnt', width: 60, sortable: false, hidden: false},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '택배사', name: 'delivery_name', index: 'delivery_name', width: 100, sortable: true},
				{ label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: true, hidden: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: true, hidden: false},
			],
			rowNum: 300,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

			},
			resizeStop: function(newwidth, index){
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		// $("#searchForm").on("submit", function(e){
		// 	e.preventDefault();
		// });

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				SellerInvoiceSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			SellerInvoiceSearch();
		});
	};

	/**
	 * 판매처별송장등록 목록/검색
	 * @constructor
	 */
	var SellerInvoiceSearch = function(){

		if($("select[name='seller_idx']").val() == "" || $("select[name='seller_idx']").val() ==  null){
			alert("판매처를 선택해주세요.");
			return;
		}

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var SellerInvoicePopInit = function(){

		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		$(".seller_idx").on("change", function(){

			location.href="seller_invoice_pop.php?seller_idx="+$(this).val();

		});


		$("#btn-save").on("click", function(){
			$("#dyFormFormat").submit();
		});

		$("#dyFormFormat").on("submit", function(){

			if($("select[name='seller_idx']").val() == "" || $("select[name='seller_idx']").val() ==  null){
				alert("판매처를 선택해주세요.");
				return false;
			}

			if(!confirm('저장하시겠습니까?')){
				return false;
			}

		});

	};

	return {
		OrderListInit : OrderListInit,
		OrderFormatSellerPopInit: OrderFormatSellerPopInit,
		OrderWriteXlsInit: OrderWriteXlsInit,
		OrderSellerXlsRead: OrderSellerXlsRead,
		OrderMatchingInit: OrderMatchingInit,
		OrderMatchingPopInit: OrderMatchingPopInit,
		OrderCollectPageInit: OrderCollectPageInit,
		OrderCollectAutoPageInit: OrderCollectAutoPageInit,
		OrderPackageInit: OrderPackageInit,
		OrderCompleteInit: OrderCompleteInit,
		OrderSearchListInit: OrderSearchListInit,
		OrderSearchListSearch: OrderSearchListSearch,
		OrderBatchDeleteInit : OrderBatchDeleteInit,
		OrderBatchDeleteLogPopInit: OrderBatchDeleteLogPopInit,
		OrderPackageExceptInit: OrderPackageExceptInit,
		OrderPackageExceptAry: OrderPackageExceptAry,
		InoviceRegListPageInit: InoviceRegListPageInit,
		SellerInvoiceInit: SellerInvoiceInit,
		SellerInvoicePopInit: SellerInvoicePopInit,
		OrderSearchListXlsDownComplete: OrderSearchListXlsDownComplete,
		OrderMatchingConfirmPopInit: OrderMatchingConfirmPopInit,
		OrderMatchingConfirmListXlsDownComplete: OrderMatchingConfirmListXlsDownComplete,
	}
})();