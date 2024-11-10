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

			if($("input[name='bank_sort']").length > 0) {
				if($.trim($("input[name='bank_sort']").val()) == ""){
					alert("계좌의 순서를 입력해주세요.");
					return false;
				}
			}

			if($('input:radio[id=bank_is_use_n]').is(':checked')) {
				if($.trim($("input[name='bank_use_n_date']").val()) == ""){
					alert("사용중지일을 입력해주세요.");
					return false;
				}

				var data = new Object();
				data.mode = "valid_chk";
				data.bank_idx = $("input[name='bank_idx']").val();
				data.bank_use_n_date = $("input[name='bank_use_n_date']").val();
				data.today = $("input[name='today']").val();

				var valid_chk = true;
				var p_url = "bank_proc_ajax.php";
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType : "json",
					async: false,
					data: data
				}).done(function (response) {
					if(!response.result) {
						if(!confirm("사용중지일 이후 거래내역이 있거나 잔액이 0이 아닙니다.\n계속 진행하시겠습니까?")){
							hideLoader();
							valid_chk = false;
							console.log(valid_chk);
						}else{
							hideLoader();
						}
					}
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					valid_chk = false;
					hideLoader();
				});
				console.log(valid_chk)
				if(!valid_chk){
					return false;
				}

			}


			showLoader();
		});
	};

	return {
		BankListInit: BankListInit,
		BankWritePopInit: BankWritePopInit,
	}
})();