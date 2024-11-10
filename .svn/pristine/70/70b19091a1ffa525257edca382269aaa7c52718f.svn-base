<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 리스트 테이블 항목 설정 창
 */

//Page Info
$pageMenuIdx = 278;
//Init
include_once "../_init_.php";

$lastest_page_idx_list = $_COOKIE["last_page_menu_idx"];

$_list = explode("|", $lastest_page_idx_list);

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post">
				<input type="hidden" name="target" id="column_setting_target" value="<?=$_GET["target"]?>" />
				<input type="hidden" name="mode" id="column_setting_mode" value="<?=$_GET["mode"]?>" />
			</form>
			<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="*" />
						<col width="180" />
					</colgroup>
					<thead>
					<tr>
						<th>메뉴명</th>
						<th>시간</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($_list as $row) {

						$rowAry = explode("^", $row);

						$MenuInfoAry = array();
						$menuLoopIdx = $rowAry[0];
						while (true) {
							$i++;
							$tmp = "";
							$tmp = $C_SiteMenu->getMenuInfo($menuLoopIdx);
							array_unshift($MenuInfoAry, $tmp);
							$menuLoopIdx = $tmp["parent_idx"];
							if ($tmp["parent_idx"] == 0 || $i == 5) {
								break;
							}
						}

						$m_text = "";
						$m_url = "";
						foreach ($MenuInfoAry as $m){
							if($m_text) $m_text .= " > ";

							$m_text .= $m["name"];
							$m_url = $m["url"];

						}

					?>
					<tr>
						<td class="text_left"><a href="<?=$m_url?>" class="link" target="_blank"><?php echo $m_text?></a></td>
						<td><?=date('Y-m-d H:i:s', $rowAry[1]);?></td>
					</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/column_const.js"></script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

