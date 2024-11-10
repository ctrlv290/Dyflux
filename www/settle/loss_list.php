<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산예정금 관리 페이지
 */
//Page Info
$pageMenuIdx = 268;
//Init
include_once "../_init_.php";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

$date_start = date('Y-m-d');
$date_end  = date('Y-m-d');

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
							<span class="text">정산일</span>
							<input type="text" name="date_start" id="date_start" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="date_end" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
						</div>
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" id="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
							</select>
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<div class="btn_set">
			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>
			<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-go-stats">통계보기</a>
			</div>
		</div>
		<div class="tb_wrap">
			<table class="no_border">
				<colgroup>
					<col width="*" />
					<col width="30" />
					<col width="400" />
				</colgroup>
				<tbody>
				<tr>
					<td class="text_left vtop">
						<p class="sub_tit2">입금예정금액</p>
						<div class="tb_wrap grid_tb">
							<table id="grid_list_loss">
							</table>
							<div id="grid_pager"></div>
						</div>
					</td>
					<td></td>
					<td class="text_left vtop">
						<p class="sub_tit2">실입금액</p>
						<table class="bank_customer_in">
							<thead>
							<tr>
								<th>입금일</th>
								<th>구분</th>
								<th>금액</th>
								<th>메모</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</tbody>
						</table>
						<br>
						<p class="sub_tit2">공제/환급액</p>
						<table class="refund">
							<thead>
							<tr>
								<th>일자</th>
								<th>내용</th>
								<th>금액</th>
								<th>메모</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.loss.js"></script>
<script>
	//사용자 항목설정을 불러오기전에 초기화
	var _gridColModel = [];
	var user_column_list = [];
</script>
<script src="/js/column_const.js"></script>
<script src="/common/column_load_js.php?target=LOSS_LIST"></script>
<script>
	console.log(_gridColModel);
	window.name = 'settle_loss';
	SettleLoss.LossListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

