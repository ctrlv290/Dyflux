/*
 * 벤더사 선택 팝업창 js
 */
var ProductVendorSelect = (function() {
	var root = this;

	var init = function() {
	};

	var vendorNameListAry = [];
	var vendorIdxListAry = [];

	//벤더사 선택 팝업창 초기화
	var ProductVendorSelectInit = function(){

		//기본 선택 바인딩
		var idx_list = $(".product_vendor_show_list", window.opener.document).val();
		var idx_ary = [];
		if(idx_list != "")
		{

			idx_ary = idx_list.split(",");

			$.each(idx_ary, function(i, o){
				$('.vendor_select[value="'+o+'"]').prop("checked", true);
			});
		}

		//전체선택 클릭 시
		$(".vendor_select_all").on("change", function(){
			$('.vendor_select').prop("checked", $(this).is(":checked"));
		});

		//저장 버튼 클릭 시
		$("#btn-save").on("click", function(){
			$('.vendor_select:checked').each(function(i, o){
				//console.log(i, o);

				vendorNameListAry.push($(this).data("vendor-name"));
				vendorIdxListAry.push($(this).val());
			});

			//console.log(vendorNameListAry);
			//console.log(vendorIdxListAry);

			$(".product_vendor_show_list", window.opener.document).val(vendorIdxListAry.join(","));
			$(".div_product_vendor_show_list", window.opener.document).html(vendorNameListAry.join(","));
			self.close();
		});
	};

	return {
		ProductVendorSelectInit : ProductVendorSelectInit
	}
})();