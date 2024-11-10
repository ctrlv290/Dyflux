<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 개인정보파기  페이지
 */
//Page Info
$pageMenuIdx = 59;
//Permission IDX
$pagePermissionIdx = 59;
//Init
include_once "../_init_.php";

$C_SiteInfo = new SiteInfo();

$_info = $C_SiteInfo -> getPersonalDataDestroySetting();

$_list = $C_SiteInfo->getPersonalDataDestroyLog();

$C_Seller = new Seller();

$_seller_list = $C_Seller->getSellerListDetail();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="tb_wrap" style="max-width: 800px;">
				<img src="../images/161011_personal_page.jpg" />
				<div class="tb_wrap">
					<form name="mainForm" id="mainForm" method="post" action="personal_destroy_proc.php">
						<input type="hidden" name="mode" value="save" />
					<table>
						<colgroup>
							<col width="150" />
							<col width="*" />
							<col width="100" />
						</colgroup>
						<thead>
						<tr>
							<th>항목</th>
							<th>설정기준</th>
							<th>저장</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								개인정보 마스킹
							</td>
							<td>
								<label><input type="checkbox" name="accept" value="Y" <?=($_info["accept"] == "Y") ? "checked" : ""?> /> 접수</label>
								<label><input type="checkbox" name="invoice" value="Y" <?=($_info["invoice"] == "Y") ? "checked" : ""?> /> 송장</label>
								<label><input type="checkbox" name="shipped" value="Y" <?=($_info["shipped"] == "Y") ? "checked" : ""?> /> 배송</label>
							</td>
							<td>
								<a href="javascript:;" id="btn-save" class="btn btn-save">저장</a>
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					<br>
					<span class="info_txt col_red">기준을 선택하지 않을 경우 기본 선택값이 적용됩니다.</span>
					<br>
					<span class="info_txt col_red">마스킹 작업은 매일 새벽4시에 진행됩니다.</span>
					<br>
					<span class="info_txt col_red">3개월 = 90일</span>
					<br><br>
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 최근실행결과</p>
					<table>
						<colgroup>
							<col width="300" />
							<col width="*" />
						</colgroup>
						<thead>
						<tr>
							<th>실행일자</th>
							<th>파기 건수</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($_list as $row){?>
						<tr>
							<td>
								<?=date('Y-m-d H:i:s', strtotime($row["regdate"]))?>
							</td>
							<td class="text_right">
								<?=number_format($row["sum_cnt"])?>
							</td>
						</tr>
						<?php } ?>
						</tbody>
					</table>

					<br><br>
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 개인정보파기 대상 설정</p>
					<p class="sub_tit2" style="text-align: right;"><a href="javascript:;" id="btn-sell-save" class="btn green_btn ">저장</a></p>
					<form name="sellForm" id="sellForm" method="post" action="personal_destroy_proc.php">
						<input type="hidden" name="mode" value="sell_save" />
					<table>
						<colgroup>
							<col width="20%" />
							<col width="60%" />
							<col width="20%" />
						</colgroup>
						<thead>
						<tr>
							<th>판매처코드</th>
							<th>판매처명</th>
							<th>사용여부</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($_seller_list as $row){?>
							<tr>
								<td>
									<?=$row["seller_idx"]?>
								</td>
								<td>
									<?=$row["seller_name"]?>
								</td>
								<td>
									<label><input type="radio" name="sell_<?=$row["seller_idx"]?>" <?=($row["seller_use_personal_destroy"] == "Y") ? "checked" : ""?>  value="Y" />Y </label>
									<label><input type="radio" name="sell_<?=$row["seller_idx"]?>" <?=($row["seller_use_personal_destroy"] == "N") ? "checked" : ""?> value="N" />N </label>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					</form>
				</div>

		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.site.js"></script>
<script>
	SiteInfo.PersonalDataInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

