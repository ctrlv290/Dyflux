<?php

include_once "../_init_.php";

$sellerMng = new Seller();
$sellerList = $sellerMng->getSellerList();

?>

<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="form_add_ad_charge" method="post" class="">
                <input type="hidden" name="mode" value="add_ad_charge">
				<div class="tb_wrap">
					<table id="table_ad_charge">
						<colgroup>
							<col width="30">
							<col width="200">
							<col width="100">
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th></th>
							<th>광고업체</th>
							<th>일자</th>
							<th>충전금</th>
							<th>비고</th>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
        <div class="btn_set">
            <div class="center">
                <a href="javascript:;" id="btn_append_ad_charge_data_row" class="large_btn green_btn ">내역 추가</a>
                <a href="javascript:;" id="btn_save_ad_charge" class="large_btn blue_btn ">저장</a>
                <a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
            </div>
        </div>
	</div>
</div>
<script>
    function initAdChargePop() {
		$(".btn_close_pop").on("click", function() {
			$("#modal_charge").dialog("close");
			$("#modal_charge").html("");
		});

    	$("#btn_append_ad_charge_data_row").on("click", function(){
            appendChargeDataRow();
        });

    	appendChargeDataRow();

		$("#btn_save_ad_charge").on("click", function(){
			showLoader();

			$.ajax({
				type: 'POST',
				url: '/ad/ad_proc.php',
				dataType: "json",
				data: $("form[name='form_add_ad_charge']").serialize()
			}).done(function (response) {
				if(response.rst) {
					alert('저장되었습니다.');
					closeModalPop("#modal_charge");
					Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
				}else{
					alert(response.msg);
				}
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}).always(function(){
				hideLoader();
			});
		});
    }

    function appendChargeDataRow() {
		let trHtml = '<tr>';
		trHtml += '<td style="padding: 5px;"><a href="javascript:;" id="btn_del_ad_charge_data_row"><i class="far fa-times-circle fa-1x"></i></a></td>';
		trHtml += '<td><select name="seller_idx[]" class="w100per">';
        <?php foreach($sellerList as $s) { ?>
		trHtml += '<option value="<?=$s["seller_idx"]?>"><?=$s["seller_name"]?></option>';
		<?php } ?>
		trHtml += "</select></td>"
		trHtml += '<td><input type="text" name="charge_date[]" class="w100per jqDateDynamic" readonly="readonly"></td>';
		trHtml += '<td><input type="text" name="charge_cost[]" class="w100per money"></td>';
		trHtml += '<td><input type="text" name="charge_memo[]" class="w100per"></td></tr>';

		$('table[id="table_ad_charge"] > tbody:last').append(trHtml);

		$('table[id="table_ad_charge"] > tbody:last > tr:last a').on("click", function(){
			removeChargeDataRow($(this).parent().parent().index());
		});

		$("#modal_charge").dialog({
			position: {my:"center", at:"center", of: window }
		});

		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		Common.setDatePickerForDynamicElement($('table[id="table_ad_charge"] > tbody:last > tr:last .jqDateDynamic'));
    }

    function removeChargeDataRow(trIdx) {
		$('table[id="table_ad_charge"] > tbody:last > tr:last a').off();
		$('table[id="table_ad_charge"] > tbody:last > tr').eq(trIdx).remove();

		$("#modal_charge").dialog({
			position: {my:"center", at:"center", of: window }
		});
    }

    initAdChargePop();
</script>