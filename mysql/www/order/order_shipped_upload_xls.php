<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 배송일괄처리(파일) 페이지 (엑셀 업로드)
 */

//Page Info
$pageMenuIdx = 86;
//Init
include_once "../_init_.php";

$mode = "update";
$_sample_filename = "배송_일괄_처리.xlsx";
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
			<input type="hidden" name="mode" id="xlswrite_mode" value="<?=$mode?>" />
			<input type="hidden" name="act" id="xlswrite_act" value="grid" />
			<input type="hidden" name="xls_type" value="order_shipped_upload" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<input type="file" name="xls_file" />
						</div>
						<div class="finder_col">
							<a href="javascript:;" class="btn green_btn btn-upload">업로드</a>
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

		<p class="sub_desc">
			샘플 파일 다운로드 <a href="/_xls_sample/<?=$_sample_filename?>" class="btn blue_btn">다운로드</a>
			* 샘플파일을 다운로드하여 포맷을 확인하시고 등록해 주세요. 양식에 맞지 않으면 정상적으로 등록되지 않습니다.
		</p>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="large_btn red_btn btn-xls-insert">&nbsp;&nbsp;&nbsp;배송처리&nbsp;&nbsp;&nbsp;</a>
			<div class="right">
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/page/order.shipped.js"></script>
<script>
	OrderShipped.OrderShippedUploadInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

