<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 추가/수정 페이지
 */
//Page Info
$pageMenuIdx = 1203;
//Init
include_once "../_init_.php";

$C_SmsMecro = new SmsMecro();

$member_idx = $_SESSION['dy_member']['member_idx'];

$rp_code = $_POST['rp_code'];
$rs_codes = $_POST['rs_codes'];

$rps_arr = explode(",", $rp_code);

$rs_code_han_ary = array(
	"seller_name" => "판매처",
	"order_idx" => "관리번호",
	"market_order_no" => "주문번호",
	"order_name" => "주문자",
	"receive_name" => "수령자",
	"receive_hp_num" => "수령자연락처",
	"product_name" => "상품명",
	"product_option_name" => "옵션",
	"invoice_no" => "송장번호",
	"market_product_name" => "판매처 상품명",
	"market_product_option" => "판매처 옵션",

);

?>
<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
	        <p class="sub_tit2">변수값 매칭 없이 메세지창에서 수기로 수정해도 무방합니다.</p>
            <form name="searchForm" id="searchForm" method="get">
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="100">
                            <col width="150">
                            <col width="100">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <?php
                        $r=0;
                        foreach($rps_arr as $k => $v) {
                        ?>
                        <tr>
                            <th>템플릿 변수</th>
                            <td class="text_left"><?=$v?></td>
                            <th>매칭</th>
                            <td class="text_left">
                                <select name="match_key_<?=$r?>" id="match_key_<?=$r?>" class="match_key_sel">
                                    <option value="">선택</option>
                                    <?php
                                    foreach($rs_codes as $k2 => $v2) {

                                    	if(array_key_exists($k2, $rs_code_han_ary)) {

		                                    if ($k2 != '삭제') {
			                                    ?>
			                                    <option value="<?= $k2 ?>"><?= $rs_code_han_ary[$k2] ?></option>
			                                    <?php
		                                    }
	                                    }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                            $r++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="btn_mecro_close ">
                    <div class="center">
                        <a href="javascript:;" class="large_btn blue_btn btn-match-save">선택</a>
                        <a href="javascript:;" class="large_btn red_btn btn-match-close">닫기</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<script>

	$(function(){

		var selected_values = $('#tp_replace_ex_code').val();

		if(selected_values != "") {
			var selected_ary = selected_values.split(',');
		}

		$.each(selected_ary, function(i, o){

			//console.log(i, o);
			$("#match_key_"+i).val(o);
		});

	});

    $(".btn-match-close").on("click", function(){
        $("#modal_sms_template_match").dialog( "close" );
    });

    $(".btn-match-save").on("click", function() {
        var m_key = "";
        var chk = 0;
        $(".match_key_sel").each(function(){
            var id = $(this).attr('id');
            var val = $('#' + id + " option:selected").val();
            if(val != '') {
                chk = 1;
            }
            m_key += val + ",";
        });

        if(chk == 0) {
            //alert('키를 매칭시키셔야 합니다');
            //return false;
	        if(!confirm('템플릿 변수가 매칭되지 않았습니다. 계속 진행 하시겠습니까?')){
	        	return false;
	        }
        }

        m_key = m_key.slice(0,-1);
        $('#tp_replace_ex_code').val(m_key);
        //console.log(m_key);
        $("#modal_sms_template_match").dialog( "close" );
    });

</script>