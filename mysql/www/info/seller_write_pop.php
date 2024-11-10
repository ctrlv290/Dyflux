<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 158;
//Permission IDX
$pagePermissionIdx = 43;
//Init
include_once "../_init_.php";


$mode = "add";
$seller_idx             = $_GET["seller_idx"];
$market_type            = "MARKETLIST";
$market_code            = "";
$seller_name            = "";
$manage_group_idx       = 0;
$market_login_id        = "";
$market_login_pw        = "";
$market_auth_code       = "";
$market_auth_code2      = "";
$market_admin_url       = "";
$market_mall_url        = "";
$market_product_url     = "";
$seller_auto_order      = "";
$seller_invoice_product = "Y";
$seller_invoice_option  = "Y";
$seller_is_use          = "Y";


$C_Seller = new Seller();

$C_Code = new Code();
$market_name = "";

if($seller_idx)
{
	$_view = $C_Seller->getSellerData($seller_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);
		if($seller_type == "CUSTOM_SELLER") {
			$market_type = "MARKETDEFINE";  // 왜 $seller_type 이랑 $market_type 분리한지 모르겠음? - ssawoona
		}
		$_Code = $C_Code->getCodeDataByCodes($seller_type, $market_code);
		if($_Code)
		{
			$market_name = $_Code["code_name"];
		}

	}else{
		put_msg_and_back("존재하지 않는 판매처입니다.");
	}
}
//print_r2($_view);
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<script type='text/javascript'>
	$(document).ready(function () {
		$("#market_code").on("change", function () {
			var strJson =
				'{' +
				'   "11ST" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "API KEY \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"AUCTION" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "마스터 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "마스터 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true} ' +
				'   } ' +
				'   ,"GMARKET" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "마스터 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "마스터 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true} ' +
				'   } ' +
				'   ,"NAVER" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"COUPANG" : {' +
				'       "login_id" : {"text" : "업체코드 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "", "display" : false}, ' +
				'       "auth_code1" : {"text" : "Access Key \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "Secret Key \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true} ' +
				'   } ' +
				'   ,"WEMAKEPRICE" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"WEMAKEPRICE20" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"TICKETMONSTER" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"INTERPARK" : {' +
				'       "login_id" : {"text" : "제휴업체 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "", "display" : false}, ' +
				'       "auth_code1" : {"text" : "업체번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "공급계약일련번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true} ' +
				'   } ' +

				'   ,"SSGMALL" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "API KEY \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"LOTTECOM" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"AKMALL" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"ETBS" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "", "display" : false}, ' +
				'       "auth_code2" : {"text" : "", "display" : false} ' +
				'   } ' +
				'   ,"CAFE24" : {' +
				'       "login_id" : {"text" : "로그인 아이디 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, '+
				'       "login_pwd" : {"text" : "로그인 비밀번호 \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code1" : {"text" : "App key \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true}, ' +
				'       "auth_code2" : {"text" : "secret key \u003cspan class=lb_red\u003e필수\u003c/span\u003e", "display" : true} ' +
				'   } ' +


				'}' +
				'';
			var json = jQuery.parseJSON(strJson);
			try {
				$('#market_login_id_text').html(json[$(this).val()].login_id.text);
				$('#market_login_pw_text').html(json[$(this).val()].login_pwd.text);
				$('#market_auth_code_text').html(json[$(this).val()].auth_code1.text);
				$('#market_auth_code2_text').html(json[$(this).val()].auth_code2.text);

				if(json[$(this).val()].login_id.display)
					$('#market_login_id_display').show();
				else
					$('#market_login_id_display').hide();

				if(json[$(this).val()].login_pwd.display)
					$('#market_login_pw_display').show();
				else
					$('#market_login_pw_display').hide();

				if(json[$(this).val()].auth_code1.display)
					$('#market_auth_code_display').show();
				else
					$('#market_auth_code_display').hide();

				if(json[$(this).val()].auth_code2.display)
					$('#market_auth_code2_display').show();
				else
					$('#market_auth_code2_display').hide();

			} catch (e) {
				$('#market_login_id_text').html("로그인 아이디");
				$('#market_login_pw_text').html("로그인 비밀번호");
				$('#market_auth_code_text').html("보안코드");
				$('#market_auth_code2_text').html("보안코드2");
				$('#market_login_id_display').show();
				$('#market_login_pw_display').show();
				$('#market_auth_code_display').show();
				$('#market_auth_code2_display').show();
			}
		}).trigger("change");
	});
</script>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="seller_idx" value="<?php echo $seller_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>판매처 타입</th>
							<td class="text_left">
								<label <?=($mode == 'mod') ? 'style="display:none;"' : ''?> ><input type="radio" name="market_type" class="market_type" value="MARKET_SELLER" <?=($market_type == 'MARKETLIST') ? 'checked="checked"' : ''?>
										<?=($mode == 'mod') ? 'onclick="return false;"' : ''?> /> 기본판매처</label>
								<label <?=($mode == 'mod') ? 'style="display:none;"' : ''?> ><input type="radio" name="market_type" class="market_type" value="CUSTOM_SELLER" <?=($market_type == 'MARKETDEFINE') ? 'checked="checked"' : ''?>
										<?=($mode == 'mod') ? 'onclick="return false;"' : ''?> /> 사용자정의판매처</label>
								<?=($mode == 'mod') ? ($market_type == 'MARKETLIST') ? '기본판매처' : '사용자정의판매처' : ''?>
							</td>
						</tr>
						<tr>
							<th>판매처</th>
							<td class="text_left">
								<?php if($mode == 'add') {?>
									<select name="market_code" id="market_code" data-selected="<?=$market_code?>" <?=($mode == 'mod') ? 'style="display:none;"' : ''?>   ></select>
								<?php }elseif($mode == "mod") { ?>
									<?=($mode == 'mod') ? $market_name : ''?>
									<input type="hidden" name="market_code" value="<?=$market_code?>" />
								<?php } ?>

							</td>
						</tr>
						<tr>
							<th>판매처명 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="seller_name" class="w400px" maxlength="20" value="<?=$seller_name?>" />
								<span class="info_txt col_red">(한글 20자 이내)</span>
							</td>
						</tr>
						<tr>
							<th>판매처 그룹</th>
							<td class="text_left">
								<select name="manage_group_idx" id="manage_group_idx" data-selected="<?=$manage_group_idx?>">
								</select>
								<a href="javascript:;" class="btn orange_btn btn-seller_group_pop">판매처 그룹 신규 등록</a>
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr id="market_login_id_display">
							<th id="market_login_id_text">로그인 아이디<!-- <span class="lb_red">필수</span>--></th>
							<td class="text_left">
								<input type="text" name="market_login_id" class="w400px" value="<?=$market_login_id?>" />
							</td>
						</tr>
						<tr id="market_login_pw_display">
							<th id="market_login_pw_text">로그인 비밀번호<!-- <span class="lb_red">필수</span>--></th>
							<td class="text_left">
								<input type="text" name="market_login_pw" class="w400px not-kor" value="<?=$market_login_pw?>" />
							</td>
						</tr>
						<tr id="market_auth_code_display">
							<th id="market_auth_code_text">보안코드</th>
							<td class="text_left">
								<input type="text" name="market_auth_code" class="w400px" value="<?=$market_auth_code?>" />
							</td>
						</tr>
						<tr id="market_auth_code2_display">
							<th id="market_auth_code2_text">보안코드2</th>
							<td class="text_left">
								<input type="text" name="market_auth_code2" class="w400px" value="<?=$market_auth_code2?>" />
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>관리자 URL</th>
							<td class="text_left">
								<input type="text" name="market_admin_url" class="w400px" value="<?=$market_admin_url?>" />
								<div>
									<span class="info_txt col_red">http:// 또는 https:// 를 포함하여 입력</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>쇼핑몰 URL</th>
							<td class="text_left">
								<input type="text" name="market_mall_url" class="w400px" value="<?=$market_mall_url?>" />
								<div>
									<span class="info_txt col_red">http:// 또는 https:// 를 포함하여 입력</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>상품페이지 URL</th>
							<td class="text_left">
								<input type="text" name="market_product_url" class="w400px" value="<?=$market_product_url?>" />
								<div>
									<span class="info_txt col_red">http:// 또는 https:// 를 포함하여 입력</span>
								</div>
								<div>
									<span class="info_txt col_red">{{상품코드}} 입력 시 상품코드로 자동 치환</span>
								</div>
								<div>
									<span class="info_txt col_red">예) http://www.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo={{상품코드}}</span>
								</div>
							</td>
						</tr>
						</tbody>
					</table>

					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>자동발주 사용</th>
							<td class="text_left">
								<label><input type="checkbox" name="seller_auto_order" value="N"  <?=($seller_auto_order == "N") ? "checked" : ""?>/>사용안함</label>
							</td>
						</tr>
						<tr>
							<th>API 사용여부 </th>
							<td class="text_left">
								<label><input type="radio" id="seller_use_api" name="seller_use_api" value="Y" <?=($seller_use_api == "Y") ? "checked" : ""?> /> Y</label>
								<label><input type="radio" id="seller_use_api" name="seller_use_api" value="N" <?=($seller_use_api == "N") ? "checked" : ""?>/> N</label>
							</td>
						</tr>
						<tr>
							<th>송장출력</th>
							<td class="text_left">
								<label><input type="checkbox" name="seller_invoice_product" value="Y"  <?=($seller_invoice_product == "Y") ? "checked" : ""?>/>상품명</label>
								<label><input type="checkbox" name="seller_invoice_option" value="Y"  <?=($seller_invoice_option == "Y") ? "checked" : ""?>/>옵션</label>
								<span class="info_txt col_red">V(체크) 를 하지 않으면 해당 정보는 출력되지 않습니다.</span>
							</td>
						</tr>
						</tbody>
					</table>

					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>사용여부 </th>
							<td class="text_left">
								<label><input type="radio" id="is_use_y" name="seller_is_use" value="Y" <?=($seller_is_use == "Y") ? "checked" : ""?> /> Y</label>
								<label><input type="radio" id="is_use_n" name="seller_is_use" value="N" <?=($seller_is_use == "N") ? "checked" : ""?>/> N</label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.seller.js"></script>
<script src="/js/page/info.group.js"></script>
<script>
	Seller.SellerWriteInit();
	ManageGroup.getManageGroupList('SELLER_GROUP');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
