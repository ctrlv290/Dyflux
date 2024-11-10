<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 신상품등록 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 209;
//Permission IDX
$permissionMenuIdx = 205;
//Init
include_once "../_init_.php";

$C_CS = new CS();
$order_idx = $_GET["order_idx"];
$_order         = $C_CS -> getOrderDetail2($order_idx);

$order_progress_step = $_order["order_progress_step_han"];
$order_cs_status = $_order["order_cs_status_han"];

switch ($order_progress_step) {
	case "접수" :
		$order_progress_step = '<span class="lbl lb_green">접수</span>';
		break;
	case "정상" :
		$order_progress_step = '<span class="lbl lb_blue">정상</span>';
		break;
	case "송장" :
		$order_progress_step = '<span class="lbl lb_violet2">송장</span>';
		break;
	case "배송" :
		$order_progress_step = '<span class="lbl lb_violet">배송</span>';
		break;
	case "보류" :
		$order_progress_step = '<span class="lbl lb_red">보류</span>';
		break;
}

switch ($order_cs_status) {
	case "정상" :
		$order_cs_status = '<span class="lbl lb_sky">정상</span>';
		break;
	case "취소" :
		$order_cs_status = '<span class="lbl lb_blue2">교환</span>';
		break;
	case "교환" :
		$order_cs_status = '<span class="lbl lb_red2">취소</span>';
		break;
}

//CS 작업 목록 가져오기
$C_Code = new Code();
$_cs_task_List = $C_Code -> getSubCodeList("CS_TASK");

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup cs_history_popup">
<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
<div class="content write_page">
	<div class="content_wrap">
		<div class="tb_wrap">
			<table>
				<colgroup>
					<col width="110" />
					<col width="*" />
					<col width="100" />
					<col width="*" />
					<col width="100" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<th>관리번호</th>
					<td><?=$_order["order_idx"]?></td>
					<th>주문번호</th>
					<td><?=$_order["market_order_no"]?></td>
					<th>C/S 상태</th>
					<td><?=$order_progress_step?> <?=$order_cs_status?></td>
				</tr>
				<tr>
					<th>발주일</th>
					<td><?=mssqlDateTimeStringConvert($_order["order_progress_step_accept_date"], 1)?></td>
					<th>주문일</th>
					<td><?=($_order["order_pay_date"] == "1900-01-01 00:00:00.000") ? "" : mssqlDateTimeStringConvert($_order["order_pay_date"], 1)?></td>
					<th>판매처</th>
					<td><?=$_order["seller_name"]?></td>
				</tr>
				<tr>
					<th>구매자/수령자</th>
					<td><?=$_order["order_name"]?>/<?=$_order["receive_name"]?></td>
					<th>휴대전화</th>
					<td><?=$_order["receive_hp_num"]?></td>
					<th>전화</th>
					<td><?=$_order["receive_tp_num"]?></td>
				</tr>
				<tr>
					<th>주소</th>
					<td colspan="5" class="text_left"><?=$_order["receive_addr1"]?> <?=$_order["receive_addr2"]?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<form name="searchFormPop" id="searchFormPop" method="get">
			<input type="hidden" name="order_pack_idx" value="<?=$order_idx?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">

								<select name="cs_task">
									<option value="">전체</option>
									<?php
									foreach($_cs_task_List as $task){
										echo '<option value="'.$task["code"].'">'.$task["code_name"].'</option>';
									}
									?>
								</select>
							</div>
							<div class="finder_col">
								<select name="search_column">
									<option value="member_id">작업자</option>
									<option value="cs_comment">내용</option>
								</select>
								<input type="text" name="search_keyword" class="w150px enterDoSearchPop" placeholder="검색어" />
								<a href="javascript:;" id="btn_searchBar_pop" class="btn blue_btn btn_default">검색</a>
							</div>
						</div>
					</div>
				</div>
				<div class="find_btn empty">
					<div class="table">
						<div class="table_cell">
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<div class="tb_wrap">
			<table class="cs_list">
				<colgroup>
					<col width="150" />
					<col width="60" />
					<col width="*" />
					<col width="60" />
					<col width="*" />
				</colgroup>
				<thead>
					<th colspan="5">C/S 이력</th>
				</thead>
				<tbody>
				<tr>
					<td rowspan="2"></td>
					<th>작업</th>
					<td class="text_left"></td>
					<th>처리</th>
					<td class="text_left"></td>
				</tr>
				<tr>
					<td colspan="4" class="text_left"></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/cs.cs.js"></script>
<script>
	CSPopup.CSHistoryInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>