<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 홈배너 관리 리스트
 */

//Page Info
$pageMenuIdx = 281;
//Init
include_once "../_init_.php";

$C_Banner = new Banner();
$_list = $C_Banner->getBannerList("HOME");

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="btn_set">
			<a href="banner_home_write.php" class="btn btn-banner-write-pop">신규등록</a>
		</div>
		<div class="tb_wrap">
			<table class="max1200">
				<colgroup>
					<col width="100" />
					<col width="*" />
					<col width="200" />
					<col width="80" />
					<col width="120" />
					<col width="160" />
					<col width="150" />
				</colgroup>
				<thead>
				<tr>
					<th>순서</th>
					<th>배너이미지/클릭 URL</th>
					<th>기간</th>
					<th>사용여부</th>
					<th>작업자</th>
					<th>등록일</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$i = 1;
				foreach($_list as $row) {
					?>
					<tr>
						<td>
							<?=$row["banner_sort"]?>
							<a href="banner_home_proc.php?mode=move&dir=up&banner_idx=<?=$row["banner_idx"]?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
							<a href="banner_home_proc.php?mode=move&dir=dn&banner_idx=<?=$row["banner_idx"]?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
						</td>
						<td>
							<a href="<?=DY_BANNER_FILE_URL?>/<?=$row["banner_image"]?>" class="product_img_thumb product_img_link" data-lightbox="banner"><img src="<?=DY_BANNER_FILE_URL?>/<?=$row["banner_image"]?>" style="height: 100px;" /></a>
							<br>
							<a href="<?=$row["banner_click_url"]?>" target="<?=$row["banner_click_target"]?>"><?=$row["banner_click_url"]?></a>
						</td>
						<td>
							<?php
							if($row["banner_use_period"] == "N"){
								echo "기간 사용안함";
							}else{
								echo $row["banner_period_start"] . " ~ " . $row["banner_period_end"];
							}
							?>
						</td>
						<td><?=$row["banner_is_use"]?></td>
						<td><?=$row["member_id"]?></td>
						<td><?=date('Y-m-d H:i:s', strtotime($row["banner_regdate"]))?></td>
						<td>
							<a href="banner_home_write.php?banner_idx=<?=$row["banner_idx"]?>" class="btn btn-bank-modify">수정</a>
							<a href="javascript:;" class="btn red_btn btn-banner-delete" data-idx="<?=$row["banner_idx"]?>" data-type="home">삭제</a>
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
<script src="/js/main.js"></script>
<script src="/js/page/info.banner.js"></script>
<script>
	Banner.BannerListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

