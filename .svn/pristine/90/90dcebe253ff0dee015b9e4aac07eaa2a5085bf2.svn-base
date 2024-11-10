<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 카테고리관리 - 목록
 */

//Page Info
$pageMenuIdx = 61;
//Init
include_once "../_init_.php";
$C_VendorGrade = new VendorGrade();

$_list = $C_VendorGrade->getVendorGradeList();
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<style>
			table.tbl600 {width: 650px !important;}
		</style>
		<div class="content">
			<div class="tb_wrap">
				<table class="tbl600">
					<caption></caption>
					<colgroup>
						<col style="width:50px;" />
						<col style="width:80px;" />
						<col style="width:150px;" />
						<col style="width:150px;" />
						<col style="width:200px;" />
						<col style="width:100px;" />
					</colgroup>
					<thead>
					<tr>
						<th></th>
						<th>등급코드</th>
						<th>등급명</th>
						<th>등급요율 할인율(%)</th>
						<th>등급기준</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$i = 1;
					foreach($_list as $vG) {
					?>
						<tr>
							<td><?=$i?></td>
							<td class="text_center">
								<?=$vG["vendor_grade"]?>
							</td>
							<td class="text_center">
								<input type="text" name="vendor_grade_name" class="w100per" maxlength="20" value="<?=$vG["vendor_grade_name"]?>" />
							</td>
							<td class="text_center">
								<input type="text" name="vendor_grade_discount" class="w50px onlyNumber" maxlength="3" value="<?=$vG["vendor_grade_discount"]?>" />%
							</td>
							<td class="text_center">
								<input type="text" name="vendor_grade_etc" class="w100per" maxlength="100" value="<?=$vG["vendor_grade_etc"]?>" />
							</td>
							<td class="text_center">
								<a href="javascript:;" class="xsmall_btn green_btn btn-vendor-grade-save" data-idx="<?php echo $vG["vendor_grade_idx"];?>" data-depth="0">저장</a>
							</td>
						</tr>
					<?php
						$i++;
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="../js/main.js"></script>
	<script src="../js/page/info.vendor.grade.js"></script>
	<script>
		$(function(){
			VendorGrade.VendorGradeListInit();
		});
	</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>