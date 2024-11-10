/*
 * 모바일 - 매출관리 관련 js
 */
var SellerSale = (function() {
	var root = this;

	var init = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectBtnMobile("date_select_btn_set", 'date_start', 'date_end', '', '', "1");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");

		//폼 전송 방지
		$("#dyForm").on("submit", function(e){
			e.preventDefault();
		});

		$("#btn-search").on("click", function(){

			if($("#seller_idx").val() == ""){
				alert("판매처를 선택해주세요.");
				return;
			}

			var p_url = "seller_sale_list.php";
			var dataObj = new Object();
			dataObj.seller_idx = $("#seller_idx").val();
			dataObj.date_start = $("#date_start").val();
			dataObj.date_end = $("#date_end").val();
			showLoaderM();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					$(".wrap_inner").html(response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoaderM();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoaderM();
			});

		});

	};

	return {
		init: init,
	}
})();

$(function(){
	SellerSale.init();
});
