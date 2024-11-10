/*
 * 사이트 정보 관리 js
 */
var MainControl = (function() {
	var root = this;

	var init = function() {
	};

	var MainControlInit = function(){

		$("#btn-save").on("click", function(){

			if(!confirm('저장하시겠습니까?')) return;

			$("#mainForm").submit();

		});

		$("#btn-fav-delete").on("click", function(){

			if(!confirm('삭제하시겠습니까?')) return;

			$("#favForm input[name='idx']").val($(this).data("idx"));

			$("#favForm").submit();

		});
	};


	return {
		MainControlInit : MainControlInit
	}
})();