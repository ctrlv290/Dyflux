<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 메인화면관리  페이지
 */
//Page Info
$pageMenuIdx = 62;
//Permission IDX
$pagePermissionIdx = 62;
//Init
include_once "../_init_.php";

$calendar = "Y";
$today = "Y";
$lastest = "Y";
$delivery = "Y";
$process = "Y";
$stock = "Y";
$return = "Y";
$product = "Y";
$vendor = "Y";

$C_MainControl = new MainControl();

$_list = $C_MainControl->getMyMainControl($GL_Member["member_idx"]);

foreach($_list as $row)
{
	$col = $row["my_main_type"];
	$val = $row["my_main_is_use"];

	$$col = $val;
}

$C_Home = new Home();
$_fav_list = $C_Home->getFavList();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="tb_wrap" style="max-width: 600px;">
			<form name="mainForm" id="mainForm" method="post" action="main_control_proc.php">
				<input type="hidden" name="mode" value="save" />
				<p class="sub_tit2">섹션관리</p>
				<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="150" />
						<col width="*" />
						<col width="150" />
						<col width="*" />
					</colgroup>
					<thead>
					<tr>
						<th>항목</th>
						<th>사용여부</th>
						<th>항목</th>
						<th>사용여부</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>매출캘린더</td>
						<td>
							<label><input type="radio" name="calendar" value="Y" <?=($calendar == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="calendar" value="N" <?=($calendar == "N") ? "checked" : ""?> />N</label>
						</td>
						<td>당일매출현황</td>
						<td>
							<label><input type="radio" name="today" value="Y" <?=($today == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="today" value="N" <?=($today == "N") ? "checked" : ""?> />N</label>
						</td>
					</tr>
					<tr>
						<td>최근현황</td>
						<td>
							<label><input type="radio" name="lastest" value="Y" <?=($lastest == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="lastest" value="N" <?=($lastest == "N") ? "checked" : ""?> />N</label>
						</td>
						<td>배송지연현황</td>
						<td>
							<label><input type="radio" name="delivery" value="Y" <?=($delivery == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="delivery" value="N" <?=($delivery == "N") ? "checked" : ""?> />N</label>
						</td>
					</tr>
					<tr>
						<td>미처리현황</td>
						<td>
							<label><input type="radio" name="process" value="Y" <?=($process == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="process" value="N" <?=($process == "N") ? "checked" : ""?> />N</label>
						</td>
						<td>재고현황</td>
						<td>
							<label><input type="radio" name="stock" value="Y" <?=($stock == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="stock" value="N" <?=($stock == "N") ? "checked" : ""?> />N</label>
						</td>
					</tr>
					<tr>
						<td>반품현황</td>
						<td>
							<label><input type="radio" name="return" value="Y" <?=($return == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="return" value="N" <?=($return == "N") ? "checked" : ""?> />N</label>
						</td>
						<td>신규 제품목록</td>
						<td>
							<label><input type="radio" name="product" value="Y" <?=($product == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="product" value="N" <?=($product == "N") ? "checked" : ""?> />N</label>
						</td>
					</tr>
					<tr>
						<td>충전금 부족업체</td>
						<td>
							<label><input type="radio" name="vendor" value="Y" <?=($vendor == "Y") ? "checked" : ""?> />Y</label>
							<label><input type="radio" name="vendor" value="N" <?=($vendor == "N") ? "checked" : ""?> />N</label>
						</td>
						<td></td>
						<td>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			</form>
			<div class="btn_set">
				<span><br></span>
				<div class="right">
					<a href="javascript:;" id="btn-save" class="btn green_btn btn-save">저장</a>
				</div>
			</div>
			<p class="sub_tit2">바로가기 관리</p>
			<div class="tb_wrap">
				<form name="favForm" id="favForm" method="post" action="main_control_proc.php">
					<input type="hidden" name="mode" value="fav_delete" />
					<input type="hidden" name="idx" value="" />
				</form>
				<table>
					<colgroup>
						<col width="*" />
						<col width="100" />
					</colgroup>
					<thead>
					<tr>
						<th>메뉴명</th>
						<th>삭제</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($_fav_list as $key => $fav) {
						$fav_idx = $key;
						$menu_ary = array();

						foreach($fav as $f) $menu_ary[] = $f["name"];

						$menu_fullname = implode(" > ", $menu_ary);

						$url = end($fav)["url"];
					?>
					<tr>
						<td class="text_left"><?=$menu_fullname?></td>
						<td><a href="javascript:;" id="btn-fav-delete" class="btn red_btn btn-fav-delete" data-idx="<?=$fav_idx?>">삭제</a></td>
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
<script src="/js/page/info.main.js"></script>
<script>
	MainControl.MainControlInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

