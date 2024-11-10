<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 주문 상세 정보 HTML
 */
//Page Info
$pageMenuIdx = 205;
//Init
include_once "../_init_.php";

$C_CS = new CS();

$order_idx = $_POST["order_idx"];
$product_option_idx = $_POST["product_option_idx"];

$_view = $C_CS ->getOrderDetailView($order_idx, $product_option_idx);
$_view2 = $C_CS ->getOrderDetailView2($order_idx, $product_option_idx);

if($_view2){
	$_stock = $C_CS -> getStockCount($_view["product_option_idx"]);
}


//상품상세페이지 URL 존재 여부
$isHasProductUrl = false;
if($_view["market_product_url"]){
	$isHasProductUrl = true;
	$_view["market_product_url"] = str_replace("{{상품코드}}", $_view["market_product_no"], $_view["market_product_url"]);
}

//택배사 배송 추적 URL 존재여부
$isHasTrackingUrl = false;
if($_view["tracking_url"]){
	$isHasTrackingUrl = true;
	$_view["tracking_url"] = str_replace("{{송장번호}}", $_view["invoice_no"], $_view["tracking_url"]);
}


?>
<script>
	$(function(){
		$(".js_order_status").each(function(i, o){
			var txt = $(this).text();
			var rst = Common.convertOrderStatusTextToLabel(txt);
			$(this).html(rst);
		});
	});
</script>
<div class="tb_box order">
	<div class="tit">
		<div class="tit_tb">
			<div class="tit_tb_c">주문정보</div>
		</div>
	</div>
	<div class="con_tb">
		<table class="info_table font_small">
			<colgroup>
				<col width="120"/>
				<col width="22%"/>
				<col width="120"/>
				<col width="22%"/>
				<col width="120"/>
				<col width="*"/>
			</colgroup>
			<tbody>
			<tr>
				<th>관리번호</th>
				<td class="bold"><?=$_view["order_idx"]?></td>
				<th>상태 / 보류</th>
				<td class="js_order_status"><?=$_view["order_progress_step_han"]?></td>
				<th>판매처</th>
				<td class="bold">
					<?=$_view["seller_name"]?>
<!--					&nbsp;[<a href="">주문상세</a>]-->
				</td>
			</tr>
			<tr>
				<th>발주일</th>
				<td class="bold"><?=$_view["order_progress_step_accept_date"]?></td>
				<th>주문번호</th>
				<td class="bold"><?=$_view["market_order_no"]?></td>
				<th>판매처 상품코드</th>
				<td class="bold">
					<?php if($isHasProductUrl){?>
						<a href="<?=$_view["market_product_url"]?>" class="link" target="_blank"><?=$_view["market_product_no"]?></a>
					<?php } else { ?>
					<?=$_view["market_product_no"]?>
					<?php }?>
				</td>
			</tr>
			<tr>
				<th>판매자<br>상품명+옵션</th>
				<td colspan="3" class="bold">
					상품명 : <?=$_view["market_product_name"]?>
					<br>
					옵션 : <?=$_view["market_product_option"]?>
				</td>
				<th>주문상세번호</th>
				<td class="bold"><?=$_view["market_order_subno"]?></td>
			</tr>
			<tr>
				<th>판매금액</th>
				<td class="bold"><?=number_format($_view["order_amt"])?></td>
				<th>정산예정금액</th>
				<td class="bold"><?=number_format($_view["order_calculation_amt"])?></td>
				<th>결제금액</th>
				<td class="bold"><?=number_format($_view["order_pay_amt"])?></td>
			</tr>
			<tr>
				<th>구매자ID</th>
				<td class="bold"></td>
				<th>주문수량</th>
				<td class="bold"><?=number_format($_view["order_cnt"])?></td>
				<th></th>
				<td></td>
			</tr>
			<tr>
				<th>구매자</th>
				<td class="bold"><?=$_view["order_name"]?></td>
				<th>구매자 연락처</th>
				<td class="bold"><?=$_view["order_tp_num"]?></td>
				<td class="bold"><?=$_view["order_hp_num"]?></td>
				<td>[<a href="javascript:;" class="btn-send-sms" data-tel="<?=$_view["order_hp_num"]?>">문자보내기</a>]</td>
			</tr>
			<tr>
				<th>수령자</th>
				<td class="bold"><?=$_view["receive_name"]?></td>
				<th>수령자 연락처</th>
				<td class="bold"><?=$_view["receive_tp_num"]?></td>
				<td class="bold"><?=$_view["receive_hp_num"]?></td>
				<td>[<a href="javascript:;" class="btn-send-sms" data-tel="<?=$_view["receive_hp_num"]?>">문자보내기</a>]</td>
			</tr>
			<tr>
				<th>배송지 주소</th>
				<td colspan="3"><?=$_view["receive_addr1"]?> <?=$_view["receive_addr2"]?></td>
				<th>우편번호</th>
				<td><?=$_view["receive_zipcode"]?></td>
			</tr>
			<tr>
				<th>메모</th>
				<td><div class="ellipsis" title="<?=htmlentities_utf8($_view["receive_memo"])?>"><?=htmlentities_utf8($_view["receive_memo"])?></div></td>
				<th>주문일</th>
				<td><?= ($_view["order_pay_date"] == "1900-01-01 00:00:00.000") ? "" : $_view["order_pay_date"]?></td>
				<th>배송예정일</th>
				<td></td>
			</tr>
			<tr>
				<th>택배사</th>
				<td class="order_content_delivery_code" data-value="<?=$_view["delivery_code"]?>"><?=$_view["delivery_name"]?></td>
				<th>송장번호</th>
				<td class="order_content_invoice_no" data-value="<?=$_view["invoice_no"]?>">
					<?php if($_view["tracking_url"]){?>
					<a href="<?=$_view["tracking_url"]?>" class="link btn_invoice_no_tracking" target="_blank"><?=$_view["invoice_no"]?></a>
					<?php }else{ ?>
					<?=$_view["invoice_no"]?>
					<?php } ?>
				</td>
				<th>선착불</th>
				<td><?= ($_view["delivery_is_free"] == "Y") ? "선불" : "착불"?></td>
			</tr>
			<tr>
				<th>송장 입력일</th>
				<td><?=$_view["invoice_date"]?></td>
				<th>배송 POS 일</th>
				<td></td>
				<th>결제수단</th>
				<td></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="tb_box product">
	<div class="tit">
		<div class="tit_tb">
			<div class="tit_tb_c">상품정보</div>
		</div>
	</div>
	<div class="con_tb">
		<table class="info_table font_small">
			<colgroup>
				<col width="120"/>
				<col width="22%"/>
				<col width="120"/>
				<col width="22%"/>
				<col width="120"/>
				<col width="*"/>
			</colgroup>
			<tbody>
			<tr>
				<th>CS상태</th>
				<td>
					<?php
					if($_view2["order_cs_status_han"] == "정상"){
						echo '<span class="lb_small lb_sky">정상</span>';
					}elseif($_view2["order_cs_status_han"] == "취소"){
						echo '<span class="lb_small lb_red2">취소</span>';
					}elseif($_view2["order_cs_status_han"] == "교환"){
						echo '<span class="lb_small lb_blue2">교환</span>';
					}
					?>

					<?php
					if($_view2["product_change_shipped"] == "Y"){
						echo " - 배송 후 주문 교환";
					}elseif($_view2["product_change_shipped"] == "N"){
						echo " - 배송 전 주문 교환";
					}
					?>
				</td>
				<th>상품코드(옵션)</th>
				<td>
					<?=$_view2["product_idx"]?>
					(<?=$_view2["product_option_idx"]?>)
<!--					&nbsp;[<a href="">상품정보</a>]-->
				</td>
				<th>매칭수량</th>
				<td><?=$_view2["product_option_cnt"]?></td>
			</tr>
			<tr>
				<th>분리된 옵션</th>
				<td></td>
				<th>재고</th>
				<td><?=number_format($_stock["stock_amount_NORMAL"])?></td>
				<th>반품정보</th>
				<td></td>
			</tr>
			<tr>
				<th>매칭 상품명</th>
				<td><?=$_view2["product_name"]?></td>
				<th>카테고리</th>
				<td>
					<?=$_view2["category_l_name"]?>
					<?=($_view2["category_m_name"]) ? "> " . $_view2["category_m_name"] : ""?>
				</td>
				<th>매칭타입</th>
				<td>
					<?= ($_view2["order_matching_is_auto"] == "Y") ? "자동" : "수동"?>
<!--					[<a href="">이전 매칭정보</a>]-->
				</td>
			</tr>
			<tr>
				<th>매칭 옵션</th>
				<td><?=$_view2["product_option_name"]?></td>
				<th>상품메모</th>
				<td colspan="3"><div class="ellipsis"><?= $_view2["product_desc"] ?></div></td>
			</tr>
			<tr>
				<th>공급처</th>
				<td colspan="3"><?=$_view2["supplier_name"]?></td>
				<th></th>
				<td></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
