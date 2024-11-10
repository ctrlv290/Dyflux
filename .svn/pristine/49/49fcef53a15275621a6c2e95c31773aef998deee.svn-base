/*
 * 택배사관리 js
 */
var Delivery = (function() {
	var root = this;

	var init = function() {
	};

	var DeliveryListInit = function(){

		$(".btn-delivery-write-pop").on("click", function(){
			DeliveryWritePopOpen("");
		});

		$(".btn-delivery-modify").on("click", function(){
			DeliveryWritePopOpen($(this).data("idx"));
		});

		//검색 버튼
		$("#searchForm").on("submit", function(e){
			$("#searchForm").submit();
		});

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(e){
			var param = $("#searchForm").serialize();
			$("#hidden_ifrm_common_filedownload").attr("src", "delivery_list_xls_down.php?"+param);
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});
	};

	var DeliveryWritePopOpen = function(delivery_idx){
		var url = "delivery_write_pop.php";
		if(delivery_idx != ""){
			url += "?delivery_idx="+delivery_idx;
		}
		Common.newWinPopup(url, 'delivery_write_pop', 650, 280, 'yes');
	};

	var DeliveryWritePopInit = function(){
		$("#btn-save").on("click", function(){
			$("#dyFormDelivery").submit();
		});

		$("#dyFormDelivery").on("submit", function(){

			if($("#delivery_code").length > 0) {
				if ($("#delivery_code").val() == null || $("#delivery_code").val() == "") {
					alert("택배사를 선택해주세요.");
					return false;
				}
			}

			if($("#tracking_url").val() == null || $("#tracking_url").val() == ""){
				alert("배송추적 URL을 입력해주세요.");
				return false;
			}


		});

	};

	return {
		DeliveryListInit: DeliveryListInit,
		DeliveryWritePopInit: DeliveryWritePopInit,
	}
})();