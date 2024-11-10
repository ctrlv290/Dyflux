<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 택배사관리  페이지
 */
//Page Info
$pageMenuIdx = 54;
//Permission IDX
$pagePermissionIdx = 54;
//Init
include_once "../_init_.php";

$C_Delivery = new Delivery();

$_list = $C_Delivery->getDeliveryTrackingList($delivery_name);

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
							<input type="text" name="delivery_name" class="w200px enterDoSearch" placeholder="택배사 이름" value="<?=$delivery_name?>" />
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
		<div class="tb_wrap max1200">
			<div class="btn_set">
				<a href="javascript:;" class="btn btn-delivery-write-pop">신규등록</a>
				<div class="right">
					<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
				</div>
			</div>
			<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="150" />
						<col width="*" />
						<col width="150" />
						<col width="80" />
						<col width="80" />
					</colgroup>
					<thead>
					<tr>
						<th>택배사 이름</th>
						<th>배송추적 URL</th>
						<th>등록일</th>
						<th>사용여부</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($_list as $row) {
					?>
					<tr>
						<td><?=$row["delivery_name"]?></td>
						<td class="text_left"><?=$row["tracking_url"]?></td>
						<td><?=date('Y-m-d H:i:s', strtotime($row["delivery_regdate"]))?></td>
						<td><?=$row["delivery_is_use"]?></td>
						<td><a href="javascript:;" onclick="" class="btn btn-delivery-modify" data-idx="<?=$row["delivery_idx"]?>">수정</a></td>
					</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.delivery.js"></script>
<script>
	window.name = 'delivery_list';
	Delivery.DeliveryListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

