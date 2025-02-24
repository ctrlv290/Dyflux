/*
 * 계좌관리 관련 js
 */
var Bank = (function() {
	var root = this;

	var init = function() {
	};

	var BankListInit = function(){
		$(".btn-bank-write-pop").on("click", function(){
			BankWritePopOpen("");
		});

		$(".btn-bank-modify").on("click", function(){
			BankWritePopOpen($(this).data("idx"));
		});

		$(".btn-bank-xls-down").on("click", function(){
			hidden_ifrm_common_filedownload.location.href="bank_xls_down.php";
		});

		$(".btn-bank-move").on("click", function () {
			moveBank($(this));
		});
	};

	//계좌 순서 이동
	var moveBank = function($obj)
	{
		var idx = $obj.data("idx");
		var dir = $obj.data("dir");
		var data = new Object();
		data.mode = "move";
		data.idx = idx;
		data.dir = dir;

		showLoader();

		var p_url = "bank_proc_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				location.reload();
			}else{
				alert(response.msg);
			}
			hideLoader();
		});
	};

	var BankWritePopOpen = function(bank_idx){
		var url = "bank_write_pop.php";
		if(bank_idx != "")
		{
			url += "?bank_idx="+bank_idx;
		}
		Common.newWinPopup(url, 'bank_write_pop', 600, 350, 'yes');
	};

	var BankWritePopInit = function(){
		$("#btn-save-pop").on("click", function(){
			$("#dyFormPop").submit();
		});
		$("#dyFormPop").on("submit", function(e){

			if($.trim($("input[name='bank_name']").val()) == ""){
				alert("계좌명을 입력해주세요.");
				return false;
			}
			if($.trim($("input[name='bank_sort']").val()) == ""){
				alert("계좌의 순서를 입력해주세요.");
				return false;
			}
			showLoader();
		});
	};

	return {
		BankListInit: BankListInit,
		BankWritePopInit: BankWritePopInit,
	}
})();