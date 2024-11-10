<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 내역 조회 페이지
 */
//Page Info
$pageMenuIdx = 96;
//Init
include_once "../_init_.php";

//상품 수정에서 이전 페이지로 넘어 왔을 경우 파라미터 세팅
$period_search_type         = $_GET["period_search_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$stock_status               = $_GET["stock_status"];
$sale_status                = $_GET["sale_status"];
$product_category_l_idx     = $_GET["product_category_l_idx"];
$product_category_m_idx     = $_GET["product_category_m_idx"];
$product_sale_type          = $_GET["product_sale_type"];
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

$supplier_idx               = implode(",", $_GET["supplier_idx"]);

//사용자 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumn("CS_LIST", $GL_Member["member_idx"]);

//작업자 리스트 가져오기
$C_Users = new Users();
$_user_list = $C_Users->getUserList();

//CS Type 가져오기
$C_Code = new Code();
$_cs_type_list = $C_Code -> getSubCodeList("CS_TYPE");

//CS 작업 가져오기
$C_Code = new Code();
$_cs_task_list = $C_Code -> getSubCodeList("CS_TASK");

//취소타입 가져오기  - CS 사유 (취소타입)
$C_Code = new Code();
$_cs_cancel_list = $C_Code -> getSubCodeList("CS_REASON_CANCEL");

//교환타입 가져오기  - CS 사유 (교환타입)
$C_Code = new Code();
$_cs_change_list = $C_Code -> getSubCodeList("CS_REASON_CHANGE");

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_type">
								<option value="cs_regdate">CS등록일</option>
								<option value="order_accept">발주일</option>
								<option value="order_shipped">배송일</option>
							</select>

							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>

						<div class="finder_col">
							<span class="text">작업자</span>
							<select name="member_idx">
								<option value="">전체</option>
								<?php
								foreach($_user_list as $user){
									echo '<option value="'.$user["idx"].'">'.$user["member_id"].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx[]" class="seller_idx" data-selected="" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">주문상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_ACCEPT">접수</option>
								<option value="ORDER_INVOICE">송장</option>
								<option value="ORDER_SHIPPED">배송</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">C/S 상태</span>
							<select name="order_cs_status">
								<option value="">전체</option>
								<option value="NORMAL">정상</option>
								<option value="NORMAL_PRODUCT_CHANGE">정상+교환</option>
								<option value="ORDER_CANCEL">취소(배송전+배송후)</option>
								<option value="PRODUCT_CHANGE">교환(배송전+배송후)</option>
								<option value="ORDER_CANCEL_N">배송전 취소</option>
								<option value="ORDER_CANCEL_Y">배송후 취소</option>
								<option value="PRODUCT_CHANGE_N">배송전 교환</option>
								<option value="PRODUCT_CHANGE_Y">배송후 교환</option>
								<option value="PAUSE">보류</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">처리여부</span>
							<select name="cs_confirm">
								<option value="">전체</option>
								<option value="N">미처리</option>
								<option value="Y">처리완료</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">작업</span>
							<select name="cs_task">
								<option value="">전체</option>
								<?php
								foreach($_cs_task_list as $task){
									echo '<option value="'.$task["code"].'">'.$task["code_name"].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">취소타입</span>
							<select name="cs_cancel_type">
								<option value="">전체</option>
								<?php
								foreach($_cs_cancel_list as $task){
									echo '<option value="'.$task["code"].'">'.$task["code_name"].'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">교환타입</span>
							<select name="cs_change_type">
								<option value="">전체</option>
								<?php
								foreach($_cs_change_list as $task){
									echo '<option value="'.$task["code"].'">'.$task["code_name"].'</option>';
								}
								?>
							</select>
						</div>
<!--						<div class="finder_col">-->
<!--							<span class="text">일반 CS 타입</span>-->
<!--							<select name="cs_type">-->
<!--								<option value="">전체</option>-->
<!--								--><?php
//								foreach($_cs_type_list as $task){
//									echo '<option value="'.$task["code"].'">'.$task["code_name"].'</option>';
//								}
//								?>
<!--							</select>-->
<!--						</div>-->
						<div class="finder_col">
							<span class="text">알림설정</span>
							<select name="cs_alarm">
								<option value="">전체</option>
								<option value="M">설정(내것만)</option>
								<option value="Y">설정(모두)</option>
								<option value="N">미설정</option>

							</select>
						</div>
<!--						<div class="finder_col">-->
<!--							<span class="text">메세지여부</span>-->
<!--							<select name="cs_sms">-->
<!--								<option value="">전체</option>-->
<!--								<option value="Y">메세지만</option>-->
<!--								<option value="N">메세지제외</option>-->
<!--							</select>-->
<!--						</div>-->
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처 상품명</span>
							<input type="text" name="market_product_name" class="w200px enterDoSearch" placeholder="" value="<?=$market_product_name?>" />
						</div>
						<div class="finder_col">
							<span class="text">판매처 옵션명</span>
							<input type="text" name="market_product_option" class="w200px enterDoSearch" placeholder="" value="<?=$market_product_option?>" />
						</div>

						<div class="finder_col">
							<select name="search_column">
								<option value="receive_name">수령자</option>
								<option value="receive_tp_num">수령자 전화</option>
								<option value="receive_hp_num">수령자 핸드폰</option>
								<option value="receive_addr1">주소</option>
								<option value="name_all">수령자+구매자</option>
								<option value="order_name">구매자</option>
								<option value="order_tp_num">구매자 전화</option>
								<option value="order_hp_num">구매자 핸드폰</option>
								<option value="CS.order_idx">관리번호</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="big_btn btn_default">검색</a>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<!--
		<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
		<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
		-->
		<p class="sub_desc sum_total">
			발주: 0건, 접수: 0건, 송장: 0건, 배송: 0건
		</p>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-cs-confirm">CS선택완료</a>
			<a href="javascript:;" class="btn btn-cs-confirm-batch">CS일괄완료</a>
			<a href="javascript:;" class="btn btn-cs-write">CS선택등록</a>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>

		<div id="modal_order_cs_write" title="C/S 남기기" class="red_theme" style="display: none;"></div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<script src="/js/page/cs.list.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	window.name = 'cs_list';
	CSList.CSListInit();
	//ManageGroup.getManageGroupList('SUPPIER_GROUP');
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

