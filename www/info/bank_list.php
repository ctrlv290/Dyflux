<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 계좌 관리 리스트
 */

//Page Info
$pageMenuIdx = 240;
//Init
include_once "../_init_.php";


$C_Bank = new Bank();
$_list = $C_Bank->getBankList();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="btn_set max1200">
			<a href="javascript:;" class="btn btn-bank-write-pop">신규등록</a>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-bank-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap">
			<table class="max1200">
				<colgroup>
					<col width="50" />
					<col width="60" />
					<col width="150" />
					<col width="*" />
					<col width="80" />
					<col width="150" />
					<col width="200" />
					<col width="100" />
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>순서</th>
					<th>구분</th>
					<th>계좌명</th>
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
							<a href="javascript:;" class="btn-bank-move" data-dir="up" data-idx="<?php echo $row["bank_idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
							<a href="javascript:;" class="btn-bank-move" data-dir="dn" data-idx="<?php echo $row["bank_idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
						</td>
						<td>
							<?=$row["bank_sort"]?>
						</td>
						<td><?=$row["bank_type_han"]?></td>
						<td class="text_left"><?=$row["bank_name"]?></td>
						<td><?=$row["bank_is_use"]?></td>
						<td><?=$row["member_id"]?></td>
						<td><?=date('Y-m-d H:i:s', strtotime($row["bank_regdate"]))?></td>
						<td>
							<a href="javascript:;" class="btn btn-bank-modify" data-idx="<?=$row["bank_idx"]?>">수정</a>
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
<script src="/js/page/info.bank.js"></script>
<script>
	$(function(){
		Bank.BankListInit();
	});
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

