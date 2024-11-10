/*
 * 모바일 - 일별캘린더 관련 js
 */
var ChartCalendar = (function() {
	var root = this;

	var init = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");

		//폼 전송 방지
		$("#dyForm").on("submit", function(e){

		});

		$("#btn-search").on("click", function(){
			$("#dyForm").submit();
		});

	};

	return {
		init: init,
	}
})();

$(function(){
	ChartCalendar.init();
});
