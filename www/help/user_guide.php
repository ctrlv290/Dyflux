<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자가이드
 */
//Page Info
$pageMenuIdx = 151;
//Init
include_once "../_init_.php";

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="mod">
				<div class="tb_wrap">
					<p class="sub_tit2"><i class="fas fa-caret-right"></i> 메뉴얼 다운로드</p>
					<table class="no-width">
						<colgroup>
							<col style="width: 150px;">
							<col style="width: 100px;">
						</colgroup>
						<tbody>
						<tr>
							<th>기준정보관리</th>
							<td><a href="/_manual/dyflux-매뉴얼_01_기준정보관리_20190527_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>상품관리</th>
							<td><a href="/_manual/dyflux-매뉴얼_02_상품관리_20190527_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>주문배송관리</th>
							<td><a href="/_manual/dyflux-매뉴얼_03_주문배송관리_20190527_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>CS관리</th>
							<td><a href="/_manual/dyflux-매뉴얼_04_CS_20190530_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>재고관리</th>
							<td><a href="/_manual/dyflux-매뉴얼_05_재고관리_20190527_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>정산통계</th>
							<td><a href="/_manual/dyflux-매뉴얼_06_정산통계_20190603_00.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>고객센터</th>
							<td><a href="/_manual/dyflux-매뉴얼_07_고객센터_20190428_00.pdf"  target="_blank"class="btn btn-seller">다운로드</a></td>
						</tr>
						<tr>
							<th>응용프로그램</th>
							<td><a href="/_manual/dyflux-매뉴얼_99_응용프로그램_20190506.pdf" target="_blank" class="btn btn-seller">다운로드</a></td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/main.js"></script>