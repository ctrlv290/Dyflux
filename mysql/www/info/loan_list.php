<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 계좌 관리 리스트
 */

//Page Info
$pageMenuIdx = 288;
//Init
include_once "../_init_.php";


$C_Loan = new Loan();
$_list = $C_Loan->getLoanList();
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="btn_set" style="max-width: 1450px;">
			<a href="javascript:;" class="btn btn-bank-write-pop">신규등록</a>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-bank-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap">
			<table style="max-width: 1450px;">
				<colgroup>
					<col width="50" />
					<col width="50" />
					<col width="*" />
					<col width="130" />
					<col width="130" />
					<col width="*" />
					<col width="80" />
                    <col width="100" />
                    <col width="100" />
					<col width="100" />
					<col width="170" />
					<col width="80" />
				</colgroup>
				<thead>
				<tr>
					<th></th>
					<th>순서</th>
					<th>계좌명</th>
					<th>대출액</th>
					<th>총상환액</th>
					<th>만기일/상환일정</th>
					<th>사용여부</th>
                    <th>사용시작일</th>
                    <th>사용중지일</th>
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
							<a href="javascript:;" class="btn-bank-move" data-dir="up" data-idx="<?php echo $row["loan_idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
							<a href="javascript:;" class="btn-bank-move" data-dir="dn" data-idx="<?php echo $row["loan_idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
						</td>
						<td>
							<?=$row["loan_sort"]?>
						</td>
						<td class="text_left"><?=$row["loan_name"]?></td>
						<td class="text_right"><?=number_format($row["loan_amount"])?></td>
						<td class="text_right"><?=number_format($row["loan_repayment"])?></td>
						<td class="text_left"><?=$row["loan_detail"]?></td>
						<td><?=$row["loan_is_use"]?></td>
                        <td><?=$row["loan_start_date"]?></td>
                        <?php if($row["loan_is_use"] == 'N'){ ?>
                            <td><?=$row["loan_use_n_date"]?></td>
                        <?php } else { ?>
                            <td></td>
                        <?php } ?>
						<td><?=$row["member_id"]?></td>
						<td><?=date('Y-m-d H:i:s', strtotime($row["loan_regdate"]))?></td>
						<td>
							<a href="javascript:;" class="btn btn-bank-modify" data-idx="<?=$row["loan_idx"]?>">수정</a>
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
<script src="/js/page/info.loan.js?v=200424"></script>
<script>
	$(function(){
		Loan.LoanListInit();
	});
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

