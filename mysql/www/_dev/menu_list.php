<?php
//Page Info
$pageMenuIdx = 152;
//Init
include_once "../_init_.php";

if($GL_Member["member_type"] != "ADMIN"){
	put_msg_and_back("관리자만 접근 가능합니다.");
	exit;
}

$C_Dbconn = new DBConn();
$C_Menu = new Menu();

$LMenus = $C_Menu->getMenuList(0);

function convertHiddenValue($val)
{
	$returnValue = ($val== "Y") ? '<span class="lb_red lb_large"><i class="far fa-times-circle"></i> 숨김</span>' : '<span class="lb_black lb_large"><i class="far fa-eye"></i> 보임</span>';
	return $returnValue;
}

function convertUseValue($val)
{
	$returnValue = ($val== "Y") ? '<span class="lb_black lb_large"><i class="far fa-check-circle"></i> 사용함</span>' : '<span class="lb_red lb_large"><i class="far fa-times-circle"></i> 사용안함</span>';
	return $returnValue;
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<style>
	table tr:hover {background-color: #fffede;}
</style>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<div class="content">
			<div class="tb_wrap">
				<table>
					<caption></caption>
					<colgroup>
						<col style="width:50px;" />
						<col style="width:50px;" />
						<col style="width:30px;" />
						<col style="width:30px;" />
						<col style="width:30px;" />
						<col style="width:auto;" />
						<col style="width:150px;" />
						<col style="width:100px;" />
						<col style="width:100px;" />
						<col style="width:100px;" />
						<col style="width:200px;" />
					</colgroup>
					<thead>
					<tr>
						<th></th>
						<th colspan="5">
							메뉴명(짧은 메뉴명) [URL]
							<a href="javascript:;" class="btn blue_btn btn-menu-add" data-idx="0" data-depth="0">메뉴추가</a>
						</th>
						<th>Target</th>
						<th>CSS</th>
						<th>메뉴숨김</th>
						<th>사용여부</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($LMenus as $LM) {
						$MMenus = $C_Menu->getMenuList($LM["idx"]);
					?>
						<tr style="background-color: #ebebeb">
							<td><?php echo $LM["idx"];?></td>
							<td>
								<a href="javascript:;" class="btn-menu-move" data-dir="up" data-idx="<?php echo $LM["idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
								<a href="javascript:;" class="btn-menu-move" data-dir="dn" data-idx="<?php echo $LM["idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
							</td>
							<td colspan="4" class="text_left" >
								<strong style="font-size: 16px;"><?php echo $LM["name"];?>(<?php echo $LM["name_short"];?>)</strong>
								<?php if($LM["url"]){?>
									[<a href="<?php echo $LM["url"];?>" target="_blank"><?php echo $LM["url"];?></a>]
								<?php }else{?>
									<!--[URL 없음]-->
								<?php } ?>
							</td>
							<td class="text_left">
								<?php
								if($LM["target"] == "popup"){
									if($LM["popup_size"] == "0|0"){
										echo $LM["target"] . "[전체화면]";
									}else{
										echo $LM["target"] . "[" . str_replace("|", ", ", $LM["popup_size"]) . "]";
									}
								}else{
									echo $LM["target"];
								}
								?>
							</td>
							<td class="text_left"><?php echo $LM["css_class"];?></td>
							<td class="text_center"><?=convertHiddenValue($LM["is_hidden"])?></td>
							<td class="text_center"><?=convertUseValue($LM["is_use"])?></td>
							<td class="text_left">
								<a href="javascript:;" class="xsmall_btn blue_btn btn-menu-add" data-idx="<?php echo $LM["idx"];?>" data-depth="1">추가</a>
								<a href="javascript:;" class="xsmall_btn green_btn btn-menu-modify" data-idx="<?php echo $LM["idx"];?>" data-depth="0">수정</a>
								<a href="javascript:;" class="xsmall_btn red_btn btn-menu-delete" data-idx="<?php echo $LM["idx"];?>">삭제</a>
							</td>
						</tr>
					<?php
						if($MMenus)
						{
							foreach ($MMenus as $MM){
								$SMenus = $C_Menu->getMenuList($MM["idx"]);
					?>
								<tr style="background-color: #f9f9f9;">
									<td><?php echo $MM["idx"];?></td>
									<td>
										<a href="javascript:;" class="btn-menu-move" data-dir="up" data-idx="<?php echo $MM["idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
										<a href="javascript:;" class="btn-menu-move" data-dir="dn" data-idx="<?php echo $MM["idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
									</td>
									<td class="blank_td">└</td>
									<td colspan="3" class="text_left">
										<strong style="font-size: 14px;"><?php echo $MM["name"];?>(<?php echo $MM["name_short"];?>)</strong>
										<?php if($MM["url"]){?>
											[<a href="<?php echo $MM["url"];?>" target="_blank"><?php echo $MM["url"];?></a>]
										<?php }else{?>
											<!--[URL 없음]-->
										<?php } ?>
									</td>
									<td class="text_left">
										<?php
										if($MM["target"] == "popup"){
											if($MM["popup_size"] == "0|0"){
												echo $MM["target"] . "[전체화면]";
											}else{
												echo $MM["target"] . "[" . str_replace("|", ", ", $MM["popup_size"]) . "]";
											}
										}else{
											echo $MM["target"];
										}
										?>
									</td>
									<td class="text_left"><?php echo $MM["css_class"];?></td>
									<td class="text_center"><?=convertHiddenValue($MM["is_hidden"] == "Y")?></td>
									<td class="text_center"><?=convertUseValue($MM["is_use"]);?></td>
									<td class="text_left">
										<a href="javascript:;" class="xsmall_btn blue_btn btn-menu-add" data-idx="<?php echo $MM["idx"];?>" data-depth="2">추가</a>
										<a href="javascript:;" class="xsmall_btn green_btn btn-menu-modify" data-idx="<?php echo $MM["idx"];?>" data-depth="1">수정</a>
										<a href="javascript:;" class="xsmall_btn red_btn btn-menu-delete" data-idx="<?php echo $MM["idx"];?>">삭제</a>
									</td>
								</tr>
					<?php
								if($SMenus) {
									foreach ($SMenus as $SM) {
										$XMenus = $C_Menu->getMenuList($SM["idx"]);
					?>
										<tr>
											<td><?php echo $SM["idx"];?></td>
											<td>
												<a href="javascript:;" class="btn-menu-move" data-dir="up" data-idx="<?php echo $SM["idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
												<a href="javascript:;" class="btn-menu-move" data-dir="dn" data-idx="<?php echo $SM["idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
											</td>
											<td class="blank_td"></td>
											<td class="blank_td">└</td>
											<td colspan="2" class="text_left">
												<?php echo $SM["name"];?>(<?php echo $SM["name_short"];?>)
												<?php if($SM["url"]){?>
													[<a href="<?php echo $SM["url"];?>" target="_blank"><?php echo $SM["url"];?></a>]
												<?php }else{?>
													[URL 없음]
												<?php } ?>
											</td>
											<td class="text_left">
												<?php
												if($SM["target"] == "popup"){
													if($SM["popup_size"] == "0|0"){
														echo $SM["target"] . "[전체화면]";
													}else{
														echo $SM["target"] . "[" . str_replace("|", ", ", $SM["popup_size"]) . "]";
													}
												}else{
													echo $SM["target"];
												}
												?>
											</td>
											<td class="text_left"><?php echo $SM["css_class"];?></td>
											<td class="text_center"><?=convertHiddenValue($SM["is_hidden"] == "Y")?></td>
											<td class="text_center"><?=convertUseValue($SM["is_use"]);?></td>
											<td class="text_left">
												<a href="javascript:;" class="xsmall_btn blue_btn btn-menu-add" data-idx="<?php echo $SM["idx"];?>" data-depth="3">추가</a>
												<a href="javascript:;" class="xsmall_btn green_btn btn-menu-modify" data-idx="<?php echo $SM["idx"];?>" data-depth="2">수정</a>
												<a href="javascript:;" class="xsmall_btn red_btn btn-menu-delete" data-idx="<?php echo $SM["idx"];?>">삭제</a>
											</td>
										</tr>
					<?php
										if($XMenus) {
											foreach($XMenus as $XM) {
					?>
												<tr>
													<td><?php echo $XM["idx"];?></td>
													<td>
														<a href="javascript:;" class="btn-menu-move" data-dir="up" data-idx="<?php echo $XM["idx"];?>"><i class="fas fa-arrow-alt-circle-up"></i></a>
														<a href="javascript:;" class="btn-menu-move" data-dir="dn" data-idx="<?php echo $XM["idx"];?>"><i class="fas fa-arrow-alt-circle-down"></i></a>
													</td>
													<td class="blank_td"></td>
													<td class="blank_td"></td>
													<td class="blank_td">└</td>
													<td colspan="1" class="text_left">
														<?php echo $XM["name"];?>(<?php echo $XM["name_short"];?>)
														<?php if($XM["url"]){?>
															[<a href="<?php echo $XM["url"];?>" target="_blank"><?php echo $XM["url"];?></a>]
														<?php }else{?>
															[URL 없음]
														<?php } ?>
													</td>
													<td class="text_left"><?php echo $XM["target"];?></td>
													<td class="text_left"><?php echo $XM["css_class"];?></td>
													<td class="text_center"><?=convertHiddenValue($XM["is_hidden"] == "Y")?></td>
													<td class="text_center"><?=convertUseValue($XM["is_use"]);?></td>
													<td class="text_left">
														<a href="javascript:;" class="xsmall_btn green_btn btn-menu-modify" data-idx="<?php echo $XM["idx"];?>" data-depth="3">수정</a>
														<a href="javascript:;" class="xsmall_btn red_btn btn-menu-delete" data-idx="<?php echo $XM["idx"];?>">삭제</a>
													</td>
												</tr>
					<?php
											}
										}
									}
								}
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
	<script src="../js/page/menu_dev.js"></script>
	<script>
		$(function(){
			menu_dev.MenuListInit();
		});
	</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>