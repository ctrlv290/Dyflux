<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처 일괄등록 페이지
 */

//Page Info
$pageMenuIdx = 50;
//Init
include_once "../_init_.php";

$mode = "add";
$_sample_filename = "공급처_일괄_등록_엑셀.xlsx";
//일괄수정
if($_GET["mode"] == "mod")
{
	//Page Info
	$pageMenuIdx = 51;

	$mode = "mod";
	$_sample_filename = "공급처_일괄_수정_엑셀.xlsx";
}

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
			<input type="hidden" name="mode" id="xlswrite_mode" value="<?=$mode?>" />
			<input type="hidden" name="act" id="xlswrite_act" value="grid" />
			<input type="hidden" name="xls_type" value="supplier_regist" />
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
		<?php if($mode == "mod"){?>
			<p class="info_txt col_red">수정하려는 공급처코드가 맞지 않는 경우 수정되지 않습니다.</p>
		<?php }?>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="large_btn red_btn btn-xls-insert">&nbsp;&nbsp;&nbsp;적용&nbsp;&nbsp;&nbsp;</a>
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
<script src="/js/page/info.supplier.js?v=200401"></script>
<script>
	Supplier.SupplierXlsWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

