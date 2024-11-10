<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME
 */
//Page Info
$pageMenuIdx = 0;
//Permission Info
$permissionMenuIdx = 0;
//Init
include_once "./_init_.php";

$_is_DYLogin = isDYLogin();

$is_calendar = true;
$is_today    = true;
$is_lastest  = true;
$is_delivery = true;
$is_process  = true;
$is_stock    = true;
$is_return   = true;
$is_product  = true;
$is_vendor   = true;

$C_MainControl = new MainControl();

$_my_main = $C_MainControl->getMyMainControl($GL_Member["member_idx"]);
foreach($_my_main as $row)
{
	$col = "is_".$row["my_main_type"];
	$val = $row["my_main_is_use"];

	$$col = ($val == "Y") ? true : false;
}

//벤더사 로그인일 경우
if(!$_is_DYLogin) {
	if ($GL_Member["member_type"] == "VENDOR") {
		$is_vendor   = false;
		$is_return   = false;
	}elseif ($GL_Member["member_type"] == "SUPPLIER") {
		$is_calendar = false;
		$is_today    = false;
		$is_lastest  = false;
		$is_delivery = false;
		$is_process  = false;
		$is_stock    = false;
		$is_return   = false;
		$is_product  = false;
		$is_vendor   = false;
	}
}


$C_Home = new Home();
if($is_product) {
	$_product_list = $C_Home->getLastProduct();
}
$_notice_list = $C_Home->getHomeNotice();
$_design_list = $C_Home->getHomeDesign();
$_biz_list = $C_Home->getHomeBiz();
$_fav_list = $C_Home->getFavList();

$clsProduct = new Product();
$_productSoldOutList = $clsProduct->getSoldOutList();
$_productSoldOutTodayCnt = 0;
foreach ($_productSoldOutList as $sd){
    $_productSoldOutToday = date('Y-m-d', strtotime($sd["product_option_soldout_date"]));
    if ($_productSoldOutToday == date('Y-m-d')) {
        $_productSoldOutTodayCnt ++;
    }
}


if($is_calendar) {
	//매출캘린더
	$date       = date('Y-m-d');
	$date_year  = date('Y');
	$date_month = date('m');

	$C_Settle = new Settle();

	$_list    = $C_Settle->getThisMonthsSettleData("settle_sale_supply", $date, 0, 0);
	$_listAry = array();

	foreach ($_list as $row) {
		$_listAry[$row["dt"]] = array("amount" => $row["sum_settle_sale_supply"], "count" => $row["sum_product_option_cnt"]);
	}

	//---- 기준날짜
	$thisyear  = date('Y', strtotime($date)); // 4자리 연도
	$thismonth = date('n', strtotime($date)); // 0을 포함하지 않는 월
	$today     = date('j', strtotime($date)); // 0을 포함하지 않는 일

	//------ $year, $month 값이 없으면 현재 날짜
	$year  = isset($_GET['year']) ? $_GET['year'] : $thisyear;
	$month = isset($_GET['month']) ? $_GET['month'] : $thismonth;
	$day   = isset($_GET['day']) ? $_GET['day'] : $today;

	$prev_month = $month - 1;
	$next_month = $month + 1;
	$prev_year  = $next_year = $year;
	if ($month == 1) {
		$prev_month = 12;
		$prev_year  = $year - 1;
	} else if ($month == 12) {
		$next_month = 1;
		$next_year  = $year + 1;
	}
	$preyear  = $year - 1;
	$nextyear = $year + 1;

	$predate  = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
	$nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

	// 1. 총일수 구하기
	$max_day = date('t', mktime(0, 0, 0, $month, 1, $year)); // 해당월의 마지막 날짜
	//echo '총요일수'.$max_day.'<br />';

	// 2. 시작요일 구하기
	$start_week = date("w", mktime(0, 0, 0, $month, 1, $year)); // 일요일 0, 토요일 6

	// 3. 총 몇 주인지 구하기
	$total_week = ceil(($max_day + $start_week) / 7);

	// 4. 마지막 요일 구하기
	$last_week = date('w', mktime(0, 0, 0, $month, $max_day, $year));
}


//홈 배너
$C_Banner = new Banner();
$home_banner_list = $C_Banner -> getUseBanner("HOME");

//이번달 첫째날
$date = strtotime(date("Y-m-01"));

//3개월전 첫째날
$date_3month_ago =date("Y-m-d", strtotime("-3 month", $date));
$date_3month_ago_md =date("m/d", strtotime("-3 month", $date));


?>
<?php include_once DY_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
	<script>
		//$('.wrap').toggleClass('hide');
		$('.wrap').addClass('menu_hide');
	</script>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<div class="content">
			<div class="tb_wrap main_wrap">
				<div class="main_set">
					<div class="main_left">
						<?php if($is_calendar) { ?>
						<div class="calendar_main_wrap">
							<div class="main_box_header">
								<p class="sub_tit">매출캘린더</p>
								<span style="margin-left:30px;">합계 금액 : <span class="monthly_total_sales">0</span>원</span>
								<span style="margin-left:30px;">합계 수량 : <span class="monthly_total_count">0</span>건</span>
								<div class="right">
									<a href="/settle/chart_calendar.php" class="btn btn-delay">더보기</a>
								</div>
							</div>
							<table class="calendar">
							<thead>
							<tr>
								<th>일</th>
								<th>월</th>
								<th>화</th>
								<th>수</th>
								<th>목</th>
								<th>금</th>
								<th>토</th>
							</tr>
							</thead>
							<tbody>
							<?php
							// 5. 화면에 표시할 화면의 초기값을 1로 설정
							$day=1;

							// 6. 총 주 수에 맞춰서 세로줄 만들기
							for($i=1; $i <= $total_week; $i++) {
								?>
								<tr>
									<?php
									// 7. 총 가로칸 만들기
									for ($j = 0; $j < 7; $j++) {
										// 8. 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않음
										
										if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))) {

											echo '<td style="cursor: pointer;" onclick="location.href=\'/settle/transaction_list.php?date_start=' . $year . '-' . make2digit($month) . '-' . make2digit($day) . '&date_end=' . $year . '-' . make2digit($month) . '-' . make2digit($day) . '\'">';

											if ($j == 0) {
												// 9. $j가 0이면 일요일이므로 빨간색
												$style = "holy";
											} else if ($j == 6) {
												// 10. $j가 0이면 토요일이므로 파란색
												$style = "blue";
											} else {
												// 11. 그외는 평일이므로 검정색
												$style = "black";
											}

											// 12. 오늘 날짜면 굵은 글씨
											if ($year == $thisyear && $month == $thismonth && $day == date("j")) {
												// 13. 날짜 출력
												echo '<div class="day ' . $style . '">';
												echo $day;
												echo '</div>';
											} else {
												echo '<div class="day ' . $style . '">';
												echo $day;
												echo '</div>';
											}

											$l_date = $year . "-" . make2digit($month) . "-" . make2digit($day);
											//매출 출력

											$amount = $_listAry[$l_date]["amount"];
											$count = $_listAry[$l_date]["count"];

											if($amount != 0 && $count != 0) {
												echo '<div class="amount">' . number_format($amount) . '<span class="cnt">(' . number_format($count) . ')</span></div>';
											}
											// 14. 날짜 증가
											$day++;

											echo '</td>';
										}else{
											echo '<td>';
											echo '</td>';
										}
									
									}
									?>
								</tr>
								<?php
							}
							?>
							</tbody>
						</table>
						</div>
						<?php } ?>

						<ul class="main_box">
							<?php if($is_today) {?>
							<li class="today">
								<div class="main_box_header">
									<p class="sub_tit">당일매출현황</p>
									<div class="right"><a href="javascript:;" class="btn btn-today">조회</a></div>
								</div>
								<table class="grid">
									<thead>
									<tr>
										<th rowspan="2"></th>
										<th colspan="2">매출</th>
										<th colspan="2">취소</th>
										<th>송장</th>
										<th>배송</th>
									</tr>
									<tr>
										<th>건수</th>
										<th>금액</th>
										<th>건수</th>
										<th>금액</th>
										<th>건수</th>
										<th>건수</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>금일</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									<tr>
										<td>전일</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
							</li>
							<?php }?>
							<?php if($is_lastest) {?>
							<li class="lastest">
								<div class="main_box_header">
									<p class="sub_tit">최근현황</p>
									<ul class="tab_menu">
										<li><a href="javascript:;" data-type="SalesAmount" class="on">판매금액(만원)</a></li>
										<li><a href="javascript:;" data-type="SalesCount" class="">판매수량</a></li>
										<li><a href="javascript:;" data-type="CancelCount" class="">취소수량</a></li>
									</ul>
									<div class="right"><a href="javascript:;" class="btn btn-lastest">조회</a></div>
								</div>
								<style>
									#chartdiv {width: 100%; height: 150px;overflow: hidden;}
								</style>
								<div id="chartdiv">

								</div>
							</li>
							<?php }?>
							<?php if($is_delivery) {?>
							<li class="delay">
								<div class="main_box_header">
									<p class="sub_tit">배송지연현황</p>
									<div class="right"><a href="javascript:;" class="btn btn-delay">조회</a></div>
								</div>
								<table class="grid">
									<colgroup>
										<col width="90" />
									</colgroup>
									<thead>
									<tr>
										<th>장기지연<br><?=$date_3month_ago_md?><br>이전/이후</th>
										<th>5일차</th>
										<th>4일차</th>
										<th>3일차</th>
										<th>2일차</th>
										<th>1일차</th>
										<th>금일</th>
										<th>합계</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>-/-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
							</li>
							<?php }?>
							<?php if($is_process) {?>
							<li class="yet">
								<div class="main_box_header">
									<p class="sub_tit">미처리현황</p>
									<div class="right"><a href="javascript:;" class="btn btn-yet">조회</a></div>
								</div>
								<table class="grid">
									<colgroup>
										<col width="50%" />
										<col width="50%" />
									</colgroup>
									<thead>
									<tr>
										<th>송장출력예정</th>
										<th>송장전송대기</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
							</li>
							<?php }?>
							<?php if($is_stock) {?>
							<li class="stock">
								<div class="main_box_header">
									<p class="sub_tit">재고현황</p>
									<div class="right"><a href="javascript:;" class="btn btn-stock">조회</a></div>
								</div>
								<?php if(isDYLogin()){?>
								<table class="grid">
									<colgroup>
										<col width="17%" />
										<col width="17%" />
										<col width="17%" />
										<col width="17%" />
										<col width="17%" />
										<col width="16%" />
									</colgroup>
									<thead>
									<tr>
										<th>현재고</th>
										<th>금일입고</th>
										<th>금일출고</th>
										<th>금일배송</th>
										<th>금일불량</th>
										<th>재고경고</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
								<?php } else { ?>
									<table class="grid">
										<colgroup>
											<col width="100%" />
										</colgroup>
										<thead>
										<tr>
											<th>현재고</th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td>-</td>
										</tr>
										</tbody>
									</table>
								<?php } ?>
							</li>
							<?php }?>
							<?php if($is_return) {?>
							<li class="return">
								<div class="main_box_header">
									<p class="sub_tit">반품현황</p>
									<div class="right"><a href="javascript:;" class="btn btn-return">조회</a></div>
								</div>
								<table class="grid">
									<colgroup>
										<col width="50%" />
										<col width="50%" />
									</colgroup>
									<thead>
									<tr>
										<th>배송후취소</th>
										<th>반품장기지연</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
							</li>
							<?php }?>
							<?php if($is_product) {?>
							<li class="product">
								<div class="main_box_header">
									<p class="sub_tit">신규 제품목록</p>
									<div class="right"><a href="/product/product_list.php" class="btn">더보기</a></div>
								</div>
								<ul class="line_list">
									<?php
									foreach($_product_list as $prod) {
										?>
										<li><?=$prod["product_name"]?></li>
										<?php
									}
									?>
								</ul>
							</li>
							<?php }?>
							<?php if($is_vendor) {?>
							<li class="vendor">
								<div class="main_box_header">
									<p class="sub_tit">충전금 부족업체</p>
									<div class="right"><a href="javascript:;" class="btn btn-vendor">조회</a></div>
								</div>
								<table class="grid">
									<colgroup>
										<col width="50%" />
										<col width="50%" />
									</colgroup>
									<thead>
									<tr>
										<th>벤더사명</th>
										<th>충전금 잔액</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>-</td>
										<td>-</td>
									</tr>
									</tbody>
								</table>
							</li>
							<?php }?>

						</ul>
					</div>
					<div class="main_right">
						<div class="right_box">
							<div class="main_box_header">
								<p class="sub_tit">품절 목록 (총 <?= count($_productSoldOutList); ?>건) / 당일 품절 (총 <?= $_productSoldOutTodayCnt; ?>건)</p>
								<div class="right"><a href="javascript:;" class="btn btn_sold_out_list">더보기</a></div>
							</div>
							<ul class="line_list">
								<?php
								$soldOutTotal = count($_productSoldOutList);
								$soldOutDisplayCnt = 4;

								foreach($_productSoldOutList as $pd){
									if ($soldOutDisplayCnt == 0) break;
                                    if (date('Y-m-d', strtotime($pd["product_option_soldout_date"])) == date('Y-m-d')){
                                ?>
                                <li><?= $pd["product_name"]; ?> > <?= $pd["product_option_name"]; ?>&nbsp;<img src="images/ico_new.png" alt=""/></li>
                                    <?php
                                    }else{
                                        ?><li><?= $pd["product_name"]; ?> > <?= $pd["product_option_name"]; ?></li>
                                            <?php
                                        }
									$soldOutTotal--;
									$soldOutDisplayCnt--;
								}
								?>
								<li>외 <?= $soldOutTotal; ?>건</li>
							</ul>
						</div>
						<div class="right_box">
							<div class="main_box_header">
								<p class="sub_tit">바로가기</p>
							</div>
							<ul class="line_list">
								<?php
								foreach($_fav_list as $fav){

									$menu_ary = array();

									foreach($fav as $f) $menu_ary[] = $f["name"];

									$menu_fullname = implode(" > ", $menu_ary);

									$url = end($fav)["url"];

								?>
									<li><a href="<?=$url?>"><?=$menu_fullname?></a></li>
									<?php
								}
								?>
							</ul>
						</div>
						<div class="right_box">
							<div class="main_box_header">
								<p class="sub_tit">공지사항</p>
								<div class="right"><a href="/help/notice_list.php" class="btn">더보기</a></div>
							</div>
							<ul class="line_list">
								<?php
								foreach($_notice_list as $notice){
									?>
									<li><a href="/help/notice_view.php?bbs_id=notice&bbs_idx=<?=$notice["bbs_idx"]?>"><?=$notice["bbs_title"]?></a></li>
									<?php
								}
								?>
							</ul>
						</div>
						<div class="right_box">
							<div class="main_box_header">
								<p class="sub_tit">디자인게시판</p>
								<div class="right"><a href="/help/design_list.php" class="btn">더보기</a></div>
							</div>
							<ul class="line_list">
								<?php
								foreach($_design_list as $notice){
									?>
									<li><a href="/help/design_view.php?bbs_id=design&bbs_idx=<?=$notice["bbs_idx"]?>"><?=$notice["bbs_title"]?></a></li>
									<?php
								}
								?>
							</ul>
						</div>
						<div class="right_box">
							<div class="main_box_header">
								<p class="sub_tit">업체게시판</p>
								<div class="right"><a href="/help/biz_list.php" class="btn">더보기</a></div>
							</div>
							<ul class="line_list">
								<?php
								foreach($_biz_list as $notice){
									?>
									<li><a href="/help/biz_view.php?bbs_id=biz&bbs_idx=<?=$notice["bbs_idx"]?>"><?=$notice["bbs_title"]?></a></li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<link rel="stylesheet" type="text/css" href="css/slick.css"/>
	<link rel="stylesheet" type="text/css" href="css/slick-theme.css"/>
	<script type="text/javascript" src="/js/slick.min.js"></script>
	<div class="home_banner">
		<div class="home_slider_wrap">
			<div class="btn_close"><a href="javascript:;" class="btn-home-banner-close"><i class="fas fa-times"></i></a></div>
			<div class="home_slider slick-slider">
				<?php
				foreach($home_banner_list as $bn) {
					$img = '<img src="'.DY_BANNER_FILE_URL . "/" . $bn["banner_image"].'" />';
					if($bn["banner_click_url"]){
						$img = '<a href="'.$bn["banner_click_url"].'" target="'.$bn["banner_click_target"].'">'.$img.'</a>';
					}
				?>
				<div class="slide"><?=$img?></div>
				<?php } ?>
			</div>
			<div class="home_slider_bottom">
				<label><input type="checkbox" class="btn-home-banner-hide-today" name="today_hide">오늘 하루 닫기</label>
			</div>
		</div>
	</div>

	<script src="../js/main.js"></script>

	<script src="/js/amcharts/core.js"></script>
	<script src="/js/amcharts/charts.js"></script>
	<script src="/js/amcharts/lang/ko_KR.js"></script>
	<script src="/js/amcharts/themes/animated.js"></script>
	<script src="../js/page/home.js?v=<?=time()?>"></script>
	<script>
		var todayExpires = new Date("<?=date("Y-m-d", strtotime("+1 day"))?>"+"");
		var banner_cnt = <?=count($home_banner_list)?>;

		$(function(){
			Home.HomeInit();
		});

		function calcMonthlyTotal() {
			var totalSales = 0;
			var totalCount = 0;

			$(".amount").each(function(){
				var text = $(this).text();
				text = text.replace(")", "");
				text = text.replace(/,/gi, "");
				var totals = text.split("(");

				totalSales += Number(totals[0]);
				totalCount += Number(totals[1]);
			});

			$(".monthly_total_sales").text(Common.addCommas(totalSales));
			$(".monthly_total_count").text(Common.addCommas(totalCount));
		}

		calcMonthlyTotal();

		function mainPopupOpen() {
			var url = '/product/product_deficiency_list_pop.php';
			Common.newWinPopup(url, 'product_deficiency_list_pop', 800, 500, 'yes');
		}

		$(".btn_sold_out_list").on("click", function(){
			mainPopupOpen();
		});
	</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>