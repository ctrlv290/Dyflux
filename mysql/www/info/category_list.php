<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 카테고리관리 - 목록
 */

//Page Info
$pageMenuIdx = 52;
//Init
include_once "../_init_.php";
$C_Dbconn = new DBConn();
$C_Category = new Category();

$LCategorys = $C_Category->getCategoryList(0);

$mode = $_GET["mode"];

if($mode == "pop"){
    include_once DY_INCLUDE_PATH . "/_include_top_popup.php";
} else {
    include_once DY_INCLUDE_PATH . "/_include_top.php";
}
    include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
	<style>
		table tr:hover {background-color: #fffede;}
	</style>
<?php
    if($mode == "pop"){ ?>
	<div class="container popup">
        <?php } else { ?>
        <div class="container">
        <?php }
        include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<div class="content">
			<div class="tb_wrap">
				<table>
					<caption></caption>
					<colgroup>
						<col style="width:50px;" />
						<col style="width:50px;" />
						<col style="width:auto;" />
						<col style="width:200px;" />
						<col style="width:80px;" />
						<col style="width:200px;" />
					</colgroup>
					<thead>
					<tr>
						<th></th>
						<th colspan="2">
							카테고리명
							<a href="javascript:;" class="btn blue_btn btn-category-add" data-idx="0" data-depth="0">상위 카테고리 추가</a>
						</th>
						<th>등록일</th>
						<th>사용여부</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($LCategorys as $LM) {
						$MCategorys = $C_Category->getCategoryList($LM["category_idx"]);
						?>
						<tr style="background-color: #ebebeb">
							<td><?php echo $LM["category_idx"];?></td>
							<td colspan="2" class="text_left">
								<a href="javascript:;" class="btn-category-move" data-dir="up" data-idx="<?php echo $LM["category_idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
								<a href="javascript:;" class="btn-category-move" data-dir="dn" data-idx="<?php echo $LM["category_idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
								<?php echo $LM["name"];?>
							</td>
							<td class="text_center"><?php echo mssqlDateTimeStringConvert($LM["regdate"]);?></td>
							<td class="text_center"><?php echo $LM["is_use"];?></td>
							<td class="text_left">
								<a href="javascript:;" class="xsmall_btn blue_btn btn-category-add" data-idx="<?php echo $LM["category_idx"];?>" data-depth="1">추가</a>
								<a href="javascript:;" class="xsmall_btn green_btn btn-category-modify" data-idx="<?php echo $LM["category_idx"];?>" data-depth="0">수정</a>
								<a href="javascript:;" class="xsmall_btn red_btn btn-category-delete" data-idx="<?php echo $LM["category_idx"];?>">삭제</a>
							</td>
						</tr>
						<?php
						if($MCategorys)
						{
							foreach ($MCategorys as $MM){
						?>
								<tr>
									<td><?php echo $MM["category_idx"];?></td>
									<td class="blank_td">└</td>
									<td colspan="1" class="text_left">
										<a href="javascript:;" class="btn-category-move" data-dir="up" data-idx="<?php echo $MM["category_idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
										<a href="javascript:;" class="btn-category-move" data-dir="dn" data-idx="<?php echo $MM["category_idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
										<?php echo $MM["name"];?>
									</td>
									<td class="text_center"><?php echo mssqlDateTimeStringConvert($MM["regdate"]);?></td>
									<td class="text_center"><?php echo $MM["is_use"];?></td>
									<td class="text_left">
										<a href="javascript:;" class="xsmall_btn green_btn btn-category-modify" data-idx="<?php echo $MM["category_idx"];?>" data-depth="1">수정</a>
										<a href="javascript:;" class="xsmall_btn red_btn btn-category-delete" data-idx="<?php echo $MM["category_idx"];?>">삭제</a>
									</td>
								</tr>
						<?php
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="../js/main.js"></script>
	<script src="../js/page/info.category.js?v=191210"></script>
	<script>
		$(function(){
			Category.CategoryListInit();
		});
	</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>