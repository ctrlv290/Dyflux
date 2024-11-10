/*
 * 공통으로 사용되는 함수 js
 */
var CommonFunction = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 공급처/판매처/벤더사 검색 팝업에서 그룹 리스트 로딩 및 바인딩
	 * 그룹 셀렉트박스에 data-selected="" 값이 존재하여야 함
	 * @param group_type            : "그룹타입" 판매처, 벤더사, 공급처
	 * @param select_target         : "그룹 셀렉트박스 jquery selector string"
	 * @param child_select_target   : "그룹 멤버 셀렉트 박스 jquery selector string"
	 */
	var bindManageGroupList = function(group_type, select_target, child_select_target){
		$(select_target).on("change", function(){
			bindManageGroupMemberList(group_type, child_select_target, $(this).val());
		});

		var p_url = "/info/manage_group_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_manage_group_list";
		dataObj.manage_group_type = group_type;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				var manage_group_idx_selected = $(select_target).data("selected");
				//console.log(manage_group_idx_selected);
				//console.log(response.list);
				var $list = response.list;
				//$(select_target+" option").remove();
				//$(select_target).append('<option value="0">전체 그룹</option>');
				$.each($list, function(i, v){
					if(manage_group_idx_selected == v.manage_group_idx)
					{
						$(select_target).append('<option value="' + v.manage_group_idx + '">' + v.manage_group_name + '</option>');
					}else {
						$(select_target).append('<option value="' + v.manage_group_idx + '">' + v.manage_group_name + '</option>');
					}
				});

				if(typeof manage_group_idx_selected != "undefined") {
					if(manage_group_idx_selected !== "") {
						$(select_target).val(manage_group_idx_selected).trigger("change").data("selected", "");
					}
				}
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
	 * 공급처/판매처/벤더사 검색 팝업에서 그룹 선택 시 하위 공급처/판매처 로딩 및 바인딩
	 * @param group_type            : "그룹타입" 판매처, 벤더사, 공급처
	 * @param select_target         : "그룹 멤버 셀렉트박스 jquery selector string"
	 * @param manage_group_idx      : "그룹" IDX
	 */
	var bindManageGroupMemberList = function(group_type, select_target, manage_group_idx){
		var p_url = "/info/manage_group_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_manage_group_member_list";
		dataObj.manage_group_type = group_type;
		dataObj.manage_group_idx = manage_group_idx;
		var manage_group_name = "";
		if(group_type == "SELLER_GROUP" || group_type == "SELLER_ALL_GROUP"){
			manage_group_name = "판매처";
		}else if(group_type == "VENDOR_GROUP" || group_type == "VENDOR_ABLE_GROUP") {
			manage_group_name = "벤더사";
		}else if(group_type == "SUPPLIER_GROUP") {
			manage_group_name = "공급처";
		}
		if(manage_group_idx == "")
		{
			$(select_target + " option").remove();
			if($(select_target).data("default-text") != "" && typeof $(select_target).data("default-text") != 'undefined')
			{
				if(window.name != "product_list"
					&& window.name != "product_trash_list"
					&& window.name != "product_matching_list"
					&& window.name != "stock_order"
					&& window.name != "stock_order_log_file"
					&& window.name != "stock_order_log_email"
					&& window.name != "stock_order_log_down"
					&& window.name != "stock_due"
					&& window.name != "stock_confirm_list"
					&& window.name != "stock_delay_list"
					&& window.name != "stock_list"
					&& window.name != "stock_period_list"
					&& window.name != "stock_product_list"
					&& window.name != "stock_log_list"
					&& window.name != "order_search_list"
					&& window.name != "order_package_except"
					&& window.name != "cs_pop"
					&& window.name != "product_commission_list"
					&& window.name != "settle_product_sale"
					&& window.name != "settle_market_product"
					&& window.name != "cs_list"
					&& window.name != "cs_return"
					&& window.name != "seller_cancel_list"
					&& window.name != "sms_alimtalk"
					&& window.name != "product_matching_delete_list"
					&& window.name != "order_confirm"
					&& window.name != "assets_state"
				) {
					$(select_target).append('<option value="' + $(select_target).data("default-value") + '">' + $(select_target).data("default-text") + '</option>');
				}
			}else{
				$(select_target).append('<option value="0">'+manage_group_name+'를 선택하세요.</option>');
			}
		}else {
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					var manage_member_idx_selected = $(select_target).data("selected");
					var $list = response.list;
					$(select_target + " option").remove();
					if($(select_target).data("default-text") != "" && typeof $(select_target).data("default-text") != 'undefined')
					{
						//기본 값을 넣을지 말지
						if(window.name != "product_list"
							&& window.name != "product_trash_list"
							&& window.name != "product_matching_list"
							&& window.name != "stock_order"
							&& window.name != "stock_order_log_file"
							&& window.name != "stock_order_log_email"
							&& window.name != "stock_order_log_down"
							&& window.name != "stock_due"
							&& window.name != "stock_confirm_list"
							&& window.name != "stock_delay_list"
							&& window.name != "stock_list"
							&& window.name != "stock_period_list"
							&& window.name != "stock_product_list"
							&& window.name != "stock_log_list"
							&& window.name != "order_search_list"
							&& window.name != "order_package_except"
							&& window.name != "cs_pop"
							&& window.name != "product_commission_list"
							&& window.name != "settle_product_sale"
							&& window.name != "settle_market_product"
							&& window.name != "cs_list"
							&& window.name != "cs_return"
							&& window.name != "seller_cancel_list"
							&& window.name != "sms_alimtalk"
							&& window.name != "product_matching_delete_list"
							&& window.name != "order_confirm"
							&& window.name != "assets_state"
						) {
							$(select_target).append('<option value="' + $(select_target).data("default-value") + '">' + $(select_target).data("default-text") + '</option>');
						}
					}else{
						$(select_target).append('<option value="0">'+manage_group_name+'를 선택하세요.</option>');
					}
					$.each($list, function (i, v) {
						if (manage_member_idx_selected == v.idx) {
							$(select_target).append('<option value="' + v.idx + '" selected="selected">' + v.name + '</option>');
						} else {
							$(select_target).append('<option value="' + v.idx + '">' + v.name + '</option>');
						}
					});
					if(typeof manage_member_idx_selected == "string") {
						if(manage_member_idx_selected.indexOf(',') > -1)
						{
							var manage_member_idx_selected_ary = manage_member_idx_selected.split(',');

							if(manage_member_idx_selected_ary.length > 0)
							{
								$.each(manage_member_idx_selected_ary, function(i, o){
									//$(select_target).val(o).data("selected", "");
									$(select_target + " option[value='" + o + "']").prop("selected", true);
								});
								$(select_target).data("selected", "");
							}
						}else {
							$(select_target).val(manage_member_idx_selected).data("selected", "");
						}
					}

					//멤버 리스트 다중 선택 스크립트 Reload()
					//상품 페이지 일 경우 공급사 멤버 목록
					//발주관리 페이지 일 경우 곱급사 멤버 목록
					if(
						window.name == "product_list"
						|| window.name == "product_trash_list"
						|| window.name == "product_matching_list"
						|| window.name == "stock_order"
						|| window.name == "stock_order_log_file"
						|| window.name == "stock_order_log_email"
						|| window.name == "stock_order_log_down"
						|| window.name == "stock_due"
						|| window.name == "stock_confirm_list"
						|| window.name == "stock_delay_list"
						|| window.name == "stock_list"
						|| window.name == "stock_period_list"
						|| window.name == "stock_product_list"
						|| window.name == "stock_log_list"
						|| window.name == "order_search_list"
						|| window.name == "order_package_except"
						|| window.name == "cs_pop"
						|| window.name == "product_commission_list"
						|| window.name == "settle_product_sale"
						|| window.name == "settle_market_product"
						|| window.name == "cs_list"
						|| window.name == "cs_return"
						|| window.name == "seller_cancel_list"
						|| window.name == "settle_chart"
						|| window.name == "sms_alimtalk"
						|| window.name == "product_matching_delete_list"
						|| window.name == "order_confirm"
						|| window.name == "assets_state"
					) {
						try {
							$(select_target)[0].sumo.reload();
						}catch (e) {

						}
					}

					try {
						$(select_target)[0].sumo.reload();
					}catch (e) {

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
	};




	return {
		bindManageGroupList : bindManageGroupList,
		bindManageGroupMemberList: bindManageGroupMemberList,
	}
})();