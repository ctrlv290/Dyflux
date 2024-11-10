<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 페이지
 */
//Page Info
$pageMenuIdx = 205;
//Permission IDX
$pagePermissionIdx = 205;
//Init
include_once "../_init_.php";

//오늘
$now_date = date('Y-m-d');

$date_start          = $_GET["date_start"];
$date_end            = $_GET["date_end"];
$order_progress_step = $_GET["order_progress_step"];
$order_cs_status     = $_GET["order_cs_status"];

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

$order_idx = (isset($_GET["order_idx"]) && $_GET["order_idx"] != "") ? $_GET["order_idx"] : "";
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<style>
	html, body{width: 100%; height: 100%;overflow: hidden;}
	html.cs_popup {width: 100%;height: 100%;overflow: hidden;}
	html.cs_popup body {width: 100%;height: 100%;}
	html.cs_popup .wrap {width: 100%; height: 100%;}
</style>
<script>
	$(function(){

		$(window).on("resize", function(){
			//setAutoHeight();
			$("#list_top").setGridWidth($(".list_wrap_top").eq(0).width() * 1);
			$("#list_bottom").setGridWidth($(".list_wrap_bottom").eq(0).width() * 1);
		}).trigger("resize");


	});

</script>

	<div class="container popup cs_page_con">
		<div class="find_wrap font_small">
			<form name="searchForm" id="searchForm" method="get">
			<div class="finder">
				<div class="finder_set">
					<div class="finder_col">
						<select name="period_type">
							<option value="order_regdate">발주일</option>
							<option value="order_accept_regdate">접수일</option>
							<option value="order_invoice_date">송장입력일</option>
							<option value="order_shipping_date">배송일</option>
						</select>
						<input type="text" name="date_start" id="period_preset_start_input" class="text w70px jqDate " value="<?=$date_start?>" readonly="readonly" />
						~
						<input type="text" name="date_end" id="period_preset_end_input" class="text w70px jqDate " value="<?=$date_end?>" readonly="readonly" />
						<select class="sel_period_preset" id="period_preset_select"></select>
					</div>
					<div class="finder_col">
						<select name="search_column">
                            <option value="name_all">수령자+구매자</option>
							<option value="receive_name">수령자</option>
							<option value="receive_tp_num">수령자 전화</option>
							<option value="receive_hp_num">수령자 핸드폰</option>
							<option value="receive_addr1">주소</option>
							<option value="order_name">구매자</option>
							<option value="order_tp_num">구매자 전화</option>
							<option value="order_hp_num">구매자 핸드폰</option>
							<option value="A.order_idx">관리번호</option>
							<option value="A.invoice_no">송장번호</option>
							<option value="A.market_order_no">주문번호(마켓)</option>
						</select>
					</div>
					<div class="finder_col">
						<span class="text">검색어</span>
						<span><input type="text" name="search_keyword" placeholder="검색어 입력" class="text enterDoSearch" /></span>
						<a href="javascript:;" id="btn_searchBar" class="xsmall_btn">검색</a>
						<a href="javascript:;" class="xsmall_btn btn_form_reset">초기화</a>
					</div>
					<div class="finder_col">
						<span><input type="text" name="market_order_no" placeholder="주문번호" class="text enterDoSearch" value="<?=$order_idx?>" /></span>
					</div>
					<div class="finder_col">
						<a href="javascript:;" class="xsmall_btn blue_btn btn-order-write">주문생성<i class="far fa-file-alt"></i></a>
					</div>
				</div>
				<div class="finder_set">
					<div class="finder_col">
						<span class="text">판매처</span>
						<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
							<option value="0">전체그룹</option>
						</select>
						<select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
						</select>

					</div>
					<div class="finder_col">
						<span class="text">상태</span>
						<select name="order_progress_step">
							<option value="">전체</option>
							<option value="ACCEPT_TEMP_BEFORE" <?=($order_progress_step == "ACCEPT_TEMP_BEFORE") ? "selected" : ""?>>발주</option>
							<option value="ACCEPT_TEMP" <?=($order_progress_step == "ACCEPT_TEMP") ? "selected" : ""?>>가접수</option>
							<option value="ACCEPT" <?=($order_progress_step == "ACCEPT") ? "selected" : ""?>>접수</option>
							<option value="INVOICE" <?=($order_progress_step == "INVOICE") ? "selected" : ""?>>송장</option>
							<option value="ACCEPT_INVOICE" <?=($order_progress_step == "ACCEPT_INVOICE") ? "selected" : ""?>>접수+송장</option>
							<option value="SHIPPED" <?=($order_progress_step == "SHIPPED") ? "selected" : ""?>>배송</option>
							<option value="ACCEPT_SHIPPED" <?=($order_progress_step == "ACCEPT_SHIPPED") ? "selected" : ""?>>접수+배송</option>
							<option value="INVOICE_SHIPPED" <?=($order_progress_step == "INVOICE_SHIPPED") ? "selected" : ""?>>송장+배송</option>
						</select>
					</div>
					<div class="finder_col">
						<span class="text">C/S</span>
						<select name="order_cs_status">
							<option value="">전체</option>
							<option value="NORMAL" <?=($order_cs_status == "NORMAL") ? "selected" : ""?>>정상</option>
							<option value="NORMAL_CHANGE" <?=($order_cs_status == "NORMAL_CHANGE") ? "selected" : ""?>>정상+교환</option>
							<option value="CANCEL" <?=($order_cs_status == "CANCEL") ? "selected" : ""?>>취소</option>
							<option value="CHANGE" <?=($order_cs_status == "CHANGE") ? "selected" : ""?>>교환</option>
							<option value="CHANGE_SHIPPED" <?=($order_cs_status == "CHANGE_SHIPPED") ? "selected" : ""?>>배송후교환C</option>
							<option value="CHANGE_SHIPPED_NORMAL" <?=($order_cs_status == "CHANGE_SHIPPED_NORMAL") ? "selected" : ""?>>배송후교환C + 정상</option>
						</select>
					</div>
					<div class="finder_col">
						<span class="text">배송비</span>
						<select name="delivery_is_free">
							<option value="">전체</option>
							<option value="Y">선불</option>
							<option value="N">착불</option>
						</select>
					</div>
					<div class="finder_col">
						<span class="text">작업</span>
						<select name="cs_task">
							<option value="">전체</option>
							<option value="HOLD_ON">보류설정</option>
							<option value="HOLD_OFF">보류해제</option>
							<option value="ADDRESS_CHANGE">배송정보변경</option>
							<option value="INVOICE_INSERT">송장입력</option>
							<option value="INVOICE_DELETE">송장삭제</option>
							<option value="SHIPPED_CONFIRM">배송확인</option>
							<option value="SHIPPED_CANCEL">배송취소</option>
							<option value="PACKAGE_ADD">합포추가</option>
							<option value="PACKAGE_EXCEPT">합포제외</option>
							<option value="ORDER_CANCEL_ALL">취소처리</option>
							<option value="ORDER_RESTORE_ALL">정상복귀</option>
							<option value="ORDER_COPY_ONE">주문복사</option>
							<option value="ORDER_WRITE">주문생성</option>
							<option value="PRODUCT_CHANGE">상품교환</option>
							<option value="PRODUCT_ADD">상품추가</option>
							<option value="MATCHING_DELETE">매칭삭제</option>
							<option value="PRIORITY_ON">우선순위설정</option>
							<option value="PRIORITY_OFF">우선순위해제</option>
							<option value="SMS_SEND">문자발송</option>
						</select>
					</div>
				</div>
				<div class="finder_set">
					<div class="finder_col check">
						<label><input type="checkbox" name="chk_hold" value="Y"/>보류</label>
						<label><input type="checkbox" name="chk_soldout" value="Y"/>품절</label>
						<label><input type="checkbox" name="chk_souldout_temp" value="Y"/>일시품절</label>
						<label><input type="checkbox" name="chk_cs_not_confirm" value="Y"/>미처리 CS</label>
						<label><input type="checkbox" name="chk_pack" value="Y"/>합포</label>
						<label><input type="checkbox" name="chk_gift" value="Y"/>사은품</label>
						<label class="color"><input type="checkbox" name="chk_except" value="Y"/>제외</label>
					</div>
				</div>
			</div>
			</form>
		</div>
		<div class="wrap_splitter" id="wrap_splitter">
		<div class="list_wrap tb_wrap font_small list_wrap_top min_grid_tb " id="wrap_splitter_top" style="position: relative;">
			<table id="list_top" class="font_small color_table"></table>
			<div id="pager_top"></div>
		</div>
		<div class="info_wrap_01 list_wrap_bottom min_grid_tb" id="wrap_splitter_bottom">

			<div id="order_detail_tabs">
				<ul>
				</ul>
			</div>

		</div>
		</div>
		<div class="info_wrap_02">
			<div class="left" id="CSDetailView">
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
								<td></td>
								<th>상태 / 보류</th>
								<td></td>
								<th>판매처</th>
								<td></td>
							</tr>
							<tr>
								<th>발주일</th>
								<td></td>
								<th>주문번호</th>
								<td></td>
								<th>판매처 상품코드</th>
								<td></td>
							</tr>
							<tr>
								<th>판매자<br>상품명+옵션</th>
								<td colspan="3"></td>
								<th>주문상세번호</th>
								<td></td>
							</tr>
							<tr>
								<th>판매금액</th>
								<td></td>
								<th>정산금액</th>
								<td></td>
								<th>결제금액</th>
								<td></td>
							</tr>
							<tr>
								<th>구매자ID</th>
								<td></td>
								<th>주문수량</th>
								<td></td>
								<th></th>
								<td></td>
							</tr>
							<tr>
								<th>구매자</th>
								<td></td>
								<th>구매자 연락처</th>
								<td></td>
								<td></td>
								<td>[<a href="javascript:;">문자보내기</a>]</td>
							</tr>
							<tr>
								<th>수령자</th>
								<td></td>
								<th>수령자 연락처</th>
								<td></td>
								<td></td>
								<td>[<a href="javascript:;">문자보내기</a>]</td>
							</tr>
							<tr>
								<th>배송지 주소</th>
								<td colspan="3"></td>
								<th>우편번호</th>
								<td></td>
							</tr>
							<tr>
								<th>메모</th>
								<td></td>
								<th>주문일</th>
								<td></td>
								<th>배송예정일</th>
								<td></td>
							</tr>
							<tr>
								<th>택배사</th>
								<td></td>
								<th>송장번호</th>
								<td></td>
								<th>선착불</th>
								<td></td>
							</tr>
							<tr>
								<th>송장 입력일</th>
								<td></td>
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
								<td></td>
								<th>매칭코드</th>
								<td></td>
								<th>매칭수량</th>
								<td></td>
							</tr>
							<tr>
								<th>분리된 옵션</th>
								<td></td>
								<th>입고예정일</th>
								<td></td>
								<th>반품정보</th>
								<td></td>
							</tr>
							<tr>
								<th>매칭 상품명</th>
								<td></td>
								<th>카테고리</th>
								<td></td>
								<th>매칭타입</th>
								<td><a href="">이전 매칭정보</a></td>
							</tr>
							<tr>
								<th>매칭 옵션</th>
								<td></td>
								<th>상품메모</th>
								<td colspan="3"></td>
							</tr>
							<tr>
								<th>공급처</th>
								<td colspan="3"></td>
								<th></th>
								<td></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="right">
				<div class="right_box">
					<div class="top">
						<p>C/S이력</p>
						<a href="javascript:;" class="btn btn-cs-history-pop">팝업열기</a>
						<div class="r_set">
							<label class="label_pointer"><input type="checkbox" class="cs_show_only_title" />제목만보기</label>
							<label class="label_pointer"><input type="checkbox" class="cs_show_all" />전체보기</label>
							<a href="javascript:;" class="btn btn-cs-write">C/S남기기</a>
							<a href="javascript:;" class="btn btn-cs-confirm-batch">완료처리</a>
						</div>
					</div>
					<div class="bot">
						<div class="inner cs_list" id="cs_list">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modal_order_write" title="주문 생성" class="red_theme" style="display: none;"></div>
	<div id="modal_order_hold" title="보류 설정" class="red_theme" style="display: none;"></div>
	<div id="modal_common" title="" class="red_theme" style="display: none;"></div>
	<div id="modal_order_cs_write" title="C/S 남기기" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<link rel="stylesheet" href="/css/jquery.splitter.css">
<link rel="stylesheet" href="/css/jquery.toast.min.css">
<script>
	jqgridDefaultSetting = false;
</script>
<script src="/js/jquery.toast.min.js"></script>
<script src="/js/ion.sound.min.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/main.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.splitter.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/fileupload.js"></script>
<script src="/js/page/cs.cs.js?v=200710"></script>
<script>
	window.name = "cs_pop";
	CSPopup.CSPopupInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

