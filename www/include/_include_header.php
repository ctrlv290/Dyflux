<?php
//$pageMenuIdx
$C_SiteMenu = new SiteMenu();

$PageTitle = "";
$PageNavAry = array();
$PageNavNoAry = array();
$MenuInfoAry = array();
$i = 0;

$menuLoopIdx = $pageMenuIdx;
while (true) {
	$i++;
	$tmp = "";
	$tmp = $C_SiteMenu->getMenuInfo($menuLoopIdx);
	array_unshift($MenuInfoAry, $tmp);
	$menuLoopIdx = $tmp["parent_idx"];
	if($tmp["parent_idx"] == 0 || $i == 5)
	{
		break;
	}
}

$PageNavIsFav = false;
$favCount = $C_SiteMenu->isFavMenu($GL_Member["member_idx"], $pageMenuIdx);
if($favCount > 0){
	$PageNavIsFav = true;
}
//print_r($MenuInfoAry);

foreach($MenuInfoAry as $MM)
{
	array_push($PageNavAry, $MM["name"]);
	array_push($PageNavNoAry, $MM["idx"]);
}

$charge_remain_amount = 0;
if($GL_Member["member_type"] == "VENDOR" && $GL_Member["vendor_use_charge"] == "Y"){

	$C_Vendor = new Vendor();
	$charge_remain_amount = $C_Vendor->getVendorRemainChargeAmount($GL_Member["member_idx"]);
}


//$AllMenuList = $C_SiteMenu->getAllMenuList();
//권한 기반 메뉴 불러오기
$AllMenuList = $C_SiteMenu->getAllMenuListByPermission();
//print_r($LMenuList);
?>
<div class="header <?=(IS_DEV_SITE) ? "dev": "" ?>">
	<a href="javascript:;" class="hide_btn">
		<span class="i_po out"><i class="fas fa-indent fa-lg"></i></span>
		<span class="i_po in"><i class="fas fa-outdent fa-lg"></i></span>
	</a>
	<div class="logo">
		<a href="/main.php"><img src="/images/img_logo.png" alt=""/></a>
	</div>
	<div class="right_menu">
		<?php if(isDYLogin()){?>
		<div style="display: inline-block;margin-right: 100px;">
			<a href="javascript:Common.newWinPopup2('/cs/cs.php', 'menu_205', 0, 0, 0, 1);" class="btn large_btn">C/S</a>
			<a href="javascript:Common.newWinPopup('/sms/sms_personal_send.php', 'send_personal_send', 1100, 750, 'yes');" class="btn large_btn">문자메세지</a>
			<a href="javascript:Common.newWinPopup('/common/lastest_page.php', 'lastest_page', 600, 450, 'yes');" class="btn large_btn">최근본메뉴</a>
		</div>
		<?php } else { //대표님 요청 하드코딩 190807
			$now_date_t = date_create(date("Y-m-d"));
			$tar_date_t = date_create("2020-05-31");
			$diff_date_t = date_diff($now_date_t, $tar_date_t);
		?>
		<span>2차 버전 오픈 예정일 D- <?= $diff_date_t->days; ?></span>
		<?php } ?>

		<?php if($GL_Member["member_type"] == "VENDOR" && $GL_Member["vendor_use_charge"] == "Y"){ ?>
		<span>충전금 현황 : <?=number_format($charge_remain_amount)?>원</span>
		<?php } ?>
		<span>마지막 로그인 : <?=mssqlDateTimeStringConvert($_SESSION["dy_member"]["lastlogin_date"], 1)?></span>
		<span><img src="/images/ico_user.png" /> <?=$_SESSION["dy_member"]["member_id"]?>(<?=$_SESSION["dy_member"]["member_name"]?>)</span>
		<a href="/info/myinfo.php" class="btn large_btn">정보수정</a>
		<a href="/logout.php" class="btn large_btn">로그아웃</a>
	</div>
	<nav class="nav">
		<ul class="gnb">
			<?php
			foreach ($AllMenuList as $MenuL) {
					if ($MenuL["depth"] != 1) continue;

					$L_No = $MenuL["idx"];
					$L_Title = $MenuL["name"];
					$L_TitleShort = $MenuL["name_short"];
					$L_Icon = $MenuL["css_class"];
					$L_Url = ($MenuL["url"]) ? $MenuL["url"] : "javascript:;";
					$L_Target = $MenuL["target"];
					if ($L_Target == "popup") {
						list($L_popupX, $L_popupY) = explode("|", $MenuL["popup_size"]);
						$L_Target = "_self";
						$L_Url = "javascript:newWinPopup('" . $L_Url . "', 'menu_" . $L_No . "', '" . $L_popupX . "', '" . $L_popupY . "');";
					}

					if ($MenuInfoAry[0]["idx"] == $L_No) {
						$L_MenuOn = true;
					} else {
						$L_MenuOn = false;
					}

					$L_Submenu = array_search($L_No, array_column($AllMenuList, 'parent_idx'));

					?>
					<li>
						<a href="<?php echo $L_Url; ?>" class="<?php echo ($L_MenuOn) ? "on" : ""; ?>"><i
									class="dy_ico <?php echo $L_Icon; ?>"></i><span
									class="mn_txt"><?php echo $L_TitleShort; ?></span></a>
						<div class="sub_gnb_wrap">
							<div class="s_gnb ">
								<div class="scrollbar-macosx">
								<div class="tit"><p><?php echo $L_Title; ?></p></div>
								<?php
								if ($L_Submenu) {
									?>
									<ul class="sub_gnb">
										<?php
										foreach ($AllMenuList as $MenuM) {

											if ($MenuM["parent_idx"] != $L_No) continue;

											$M_No = $MenuM["idx"];
											$M_Title = $MenuM["name"];
											$M_Url = ($MenuM["url"]) ? $MenuM["url"] : "javascript:;";
											$M_Target = $MenuM["target"];
											if ($M_Target == "popup") {
												list($M_popupX, $M_popupY) = explode("|", $MenuM["popup_size"]);
												$M_Target = "_self";
												$M_Url = "javascript:Common.newWinPopup2('" . $M_Url . "', 'menu_" . $M_No . "', " . $M_popupX . ", " . $M_popupY . ", 0, 1);";
											}
											$M_Class = $MenuM["css_class"];

											$M_Submenu = array_search($M_No, array_column($AllMenuList, 'parent_idx'));

											if ($MenuInfoAry[0]["idx"] == $L_No && $MenuInfoAry[1]["idx"] == $M_No) {
												$M_MenuOn = true;
											} else {
												$M_MenuOn = false;
											}

											//hidden
											$M_Hidden = $MenuM["hidden"];
											if ($M_Hidden === true) {
												continue;
											}
											?>
											<li>
												<a href="<?php echo $M_Url; ?>"
												   class="sub_gnb_tit <?php echo $M_Class; ?> <?=(!$M_Submenu && $M_MenuOn) ? "on" : "" ?>"><?php echo $M_Title; ?></a>
												<?php
												if ($M_Submenu) {
													?>
													<ul class="ulwrap">
														<?php
														foreach ($AllMenuList as $MenuS) {

															if ($MenuS["parent_idx"] != $M_No) continue;

															$S_No = $MenuS["idx"];
															$S_Title = $MenuS["name"];
															$S_Url = ($MenuS["url"]) ? $MenuS["url"] : "javascript:;";
															$S_Target = $MenuS["target"];
															if ($S_Target == "popup") {
																list($S_popupX, $S_popupY) = explode("|", $MenuS["popup_size"]);

																$S_Target = "_self";
																$S_Url = "javascript:Common.newWinPopup2('" . $S_Url . "', 'menu_" . $S_No . "', " . $S_popupX . ", " . $S_popupY . ", 0, 1);";
															}
															$S_Class = $MenuS["css_class"];

															if ($MenuInfoAry[0]["idx"] == $L_No && $MenuInfoAry[1]["idx"] == $M_No && $MenuInfoAry[2]["idx"] == $S_No) {
																$S_MenuOn = true;
															} else {
																$S_MenuOn = false;
															}

															//hidden
															$S_Hidden = $MenuS["hidden"];
															if ($S_Hidden === true) {
																continue;
															}
															?>
															<li><a href="<?php echo $S_Url; ?>"
															       class="<?php echo $S_Class; ?> <?=($S_MenuOn) ? "on" : "" ?>"><?php echo $S_Title; ?></a>
															</li>
															<?php
														}
														?>
													</ul>
													<?php
												}
												?>
											</li>
											<?php
										}
										?>
									</ul>
									<?php
								}
								?>
								</div>
							</div>
						</div>
					</li>
					<?php
				}
			?>
		</ul>
	</nav>
</div>