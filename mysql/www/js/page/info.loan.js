/*
 * 대출계좌관리 관련 js
 */
var Loan = (function() {
	var root = this;

	var init = function() {
	};

	var LoanListInit = function(){
		$(".btn-bank-write-pop").on("click", function(){
			LoanWritePopOpen("");
		});

		$(".btn-bank-modify").on("click", function(){
			LoanWritePopOpen($(this).data("idx"));
		});

		$(".btn-bank-xls-down").on("click", function(){
			hidden_ifrm_common_filedownload.location.href="loan_xls_down.php";
		});

		$(".btn-bank-move").on("click", function () {
			moveLoan($(this));
		});
	};

	//계좌 순서 이동
	var moveLoan = function($obj)
	{
		var idx = $obj.data("idx");
		var dir = $obj.data("dir");
		var data = new Object();
		data.mode = "move";
		data.idx = idx;
		data.dir = dir;

		showLoader();

		var p_url = "loan_proc_ajax.php";
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

	var LoanWritePopOpen = function(loan_idx){
		var url = "loan_write_pop.php";
		if(loan_idx != "")
		{
			url += "?loan_idx="+loan_idx;
		}
		Common.newWinPopup(url, 'loan_write_pop', 600, 400, 'yes');
	};

	var LoanWritePopInit = function(){
		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		$("#btn-save-pop").on("click", function(){
			$("#dyFormPop").submit();
		});
		$("#dyFormPop").on("submit", function(e){

			if($.trim($("input[name='loan_name']").val()) == ""){
				alert("계좌명을 입력해주세요.");
				return false;
			}
			if($("input[name='loan_sort']").length > 0) {
				if ($.trim($("input[name='loan_sort']").val()) == "") {
					alert("계좌의 순서를 입력해주세요.");
					return false;
				}
			}
			if($('input:radio[id=loan_is_use_n]').is(':checked')) {
				if($.trim($("input[name='loan_use_n_date']").val()) == ""){
					alert("사용중지일을 입력해주세요.");
					return false;
				}

				var data = new Object();
				data.mode = "valid_chk";
				data.loan_idx = $("input[name='loan_idx']").val();
				data.loan_use_n_date = $("input[name='loan_use_n_date']").val();
				data.today = $("input[name='today']").val();

				var valid_chk = true;
				var p_url = "loan_proc_ajax.php";
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
		LoanListInit: LoanListInit,
		LoanWritePopInit: LoanWritePopInit,
	}
})();