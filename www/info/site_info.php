<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 내정보 수정
 */
//Page Info
$pageMenuIdx = 187;
//Permission IDX
$permissionMenuIdx = 187;
//Init
include_once "../_init_.php";

$idx = $GL_Member["member_idx"];

$C_Users = new Users();
$member_type = $C_Users -> getUserType($idx);

$mode            = "mod";
$ceo_name        = "";
$license_no      = "";
$license_no1     = "";
$license_no2     = "";
$license_no3     = "";
$zipcode         = "";
$addr1           = "";
$addr2           = "";
$fax             = "";
$email_default   = "";
$email_account   = "";
$email_order     = "";
$invoice_name    = "";
$invoice_tel     = "";
$invoice_addr    = "";
$officer1_name   = "";
$officer1_tel    = "";
$officer1_mobile = "";
$officer1_email  = "";
$officer2_name   = "";
$officer2_tel    = "";
$officer2_mobile = "";
$officer2_email  = "";
$officer3_name   = "";
$officer3_tel    = "";
$officer3_mobile = "";
$officer3_email  = "";
$officer4_name   = "";
$officer4_tel    = "";
$officer4_mobile = "";
$officer4_email  = "";
$officer5_name   = "";
$officer5_tel    = "";
$officer5_mobile = "";
$officer5_email  = "";


$C_SiteInfo = new SiteInfo();

$_view = $C_SiteInfo->getSiteInfo();

if($_view)
{
	$mode = "mod";
	extract($_view);

	if($license_no){
		list($license_no1, $license_no2, $license_no3) = explode("-", $license_no);
	}
	if($officer1_tel){
		list($officer1_tel1, $officer1_tel2, $officer1_tel3) = explode("-", $officer1_tel);
	}
	if($invoice_tel){
		list($invoice_tel1, $invoice_tel2, $invoice_tel3) = explode("-", $invoice_tel);
	}
	if($officer1_mobile){
		list($officer1_mobile1, $officer1_mobile2, $officer1_mobile3) = explode("-", $officer1_mobile);
	}
	if($officer1_email){
		list($officer1_email1, $officer1_email2) = explode("@", $officer1_email);
	}

	if($officer2_tel){
		list($officer2_tel1, $officer2_tel2, $officer2_tel3) = explode("-", $officer2_tel);
	}
	if($officer2_mobile){
		list($officer2_mobile1, $officer2_mobile2, $officer2_mobile3) = explode("-", $officer2_mobile);
	}
	if($officer2_email){
		list($officer2_email1, $officer2_email2) = explode("@", $officer2_email);
	}

	if($officer3_tel){
		list($officer3_tel1, $officer3_tel2, $officer3_tel3) = explode("-", $officer3_tel);
	}
	if($officer3_mobile){
		list($officer3_mobile1, $officer3_mobile2, $officer3_mobile3) = explode("-", $officer3_mobile);
	}
	if($officer3_email){
		list($officer3_email1, $officer3_email2) = explode("@", $officer3_email);
	}
	if($officer4_tel){
		list($officer4_tel1, $officer4_tel2, $officer4_tel3) = explode("-", $officer4_tel);
	}
	if($officer4_mobile){
		list($officer4_mobile1, $officer4_mobile2, $officer4_mobile3) = explode("-", $officer4_mobile);
	}
	if($officer4_email){
		list($officer4_email1, $officer4_email2) = explode("@", $officer4_email);
	}
	
}else{
	put_msg_and_back("기본 사이트 정보가 존재하지 않습니다.");
}
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="mod">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<div class="tb_wrap">
					<a href="javascript:;" class="large_btn btn btn-change-log-viewer-pop">변경이력</a>
				</div>
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>상호명 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="site_name" class="w400px" maxlength="50" value="<?=$site_name?>" />
							</td>
						</tr>
						<tr>
							<th>대표이사 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="ceo_name" class="w400px" maxlength="50" value="<?=$ceo_name?>" />
							</td>
						</tr>
						<tr>
							<th>사업자등록번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="license_no1" class="w30px onlyNumber" maxlength="3" value="<?=$license_no1?>" />
								-
								<input type="text" name="license_no2" class="w30px onlyNumber" maxlength="2" value="<?=$license_no2?>" />
								-
								<input type="text" name="license_no3" class="w40px onlyNumber" maxlength="5" value="<?=$license_no3?>" />
							</td>
						</tr>
						<tr>
							<th>주소 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="zipcode" id="zipcode" class="w60px" readonly="readonly" value="<?=$zipcode?>" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="zipcode" data-addr1-id="addr1" data-addr2-id="addr2">우편번호</a>
								<div class="row">
									<input type="text" name="addr1" id="addr1" class="w400px" readonly="readonly" value="<?=$addr1?>" />
								</div>
								<div class="row">
									<input type="text" name="addr2" id="addr2" class="w400px" value="<?=$addr2?>" />
								</div>
							</td>
						</tr>
						<tr>
							<th>팩스번호</th>
							<td class="text_left">
								<input type="text" name="fax" class="w400px onlyNumberPhone" maxlength="20" value="<?=$fax?>" />
							</td>
						</tr>
						<tr>
							<th rowspan="3">이메일</th>
							<td class="text_left">
								대표 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row w400px">
									<input type="text" name="email_default" id="email_default" class="w100px" maxlength="300" value="<?=$email_default?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								회계용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row w400px">
									<input type="text" name="email_account" id="email_account" class="w100px" maxlength="300" value="<?=$email_account?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								발주용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row w400px">
									<input type="text" name="email_order" id="email_order" class="w100px" maxlength="300" value="<?=$email_order?>" />
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
							<th>송장용 상호명<span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="invoice_name" class="w400px" maxlength="30" value="<?=$invoice_name?>" />
							</td>
						</tr>
						<tr>
							<th>송장용 연락처 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="invoice_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($invoice_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="invoice_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$invoice_tel2?>" />
								-
								<input type="text" name="invoice_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$invoice_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>송장용 주소<span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="invoice_addr" class="w400px" maxlength="100" value="<?=$invoice_addr?>" />
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
							<th>담당자 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="officer1_name" class="w400px" maxlength="30" value="<?=$officer1_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="officer1_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($officer1_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer1_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$officer1_tel2?>" />
								-
								<input type="text" name="officer1_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$officer1_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="officer1_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($officer1_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer1_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$officer1_mobile2?>" />
								-
								<input type="text" name="officer1_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$officer1_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="officer1_email1" class="w100px" value="<?=$officer1_email1?>" />
								@
								<input type="text" name="officer1_email2" id="officer1_email2" class="w100px" value="<?=$officer1_email2?>" />
								<select name="officer1_email3" id="officer1_email3" class="email_domain_select">
									<?php
									foreach($GL_emailCollection as $item){
										echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
									}
									?>
								</select>
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
							<th>담당자</th>
							<td class="text_left">
								<input type="text" name="officer2_name" class="w400px" maxlength="30" value="<?=$officer2_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="officer2_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($officer2_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer2_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$officer2_tel2?>" />
								-
								<input type="text" name="officer2_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$officer2_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="officer2_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($officer2_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer2_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$officer2_mobile2?>" />
								-
								<input type="text" name="officer2_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$officer2_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="officer2_email1" class="w100px" value="<?=$officer2_email1?>" />
								@
								<input type="text" name="officer2_email2" id="officer2_email2" class="w100px" value="<?=$officer2_email2?>" />
								<select name="officer2_email3" id="officer2_email3" class="email_domain_select">
									<?php
									foreach($GL_emailCollection as $item){
										echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
									}
									?>
								</select>
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
							<th>담당자</th>
							<td class="text_left">
								<input type="text" name="officer3_name" class="w400px" maxlength="30" value="<?=$officer3_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="officer3_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($officer3_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer3_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$officer3_tel2?>" />
								-
								<input type="text" name="officer3_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$officer3_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="officer3_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($officer3_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer3_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$officer3_mobile2?>" />
								-
								<input type="text" name="officer3_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$officer3_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="officer3_email1" class="w100px" value="<?=$officer3_email1?>" />
								@
								<input type="text" name="officer3_email2" id="officer3_email2" class="w100px" value="<?=$officer3_email2?>" />
								<select name="officer3_email3" id="officer3_email3" class="email_domain_select">
									<?php
									foreach($GL_emailCollection as $item){
										echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
									}
									?>
								</select>
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
							<th>담당자</th>
							<td class="text_left">
								<input type="text" name="officer4_name" class="w400px" maxlength="30" value="<?=$officer4_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="officer4_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($officer4_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer4_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$officer4_tel2?>" />
								-
								<input type="text" name="officer4_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$officer4_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="officer4_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($officer4_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="officer4_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$officer4_mobile2?>" />
								-
								<input type="text" name="officer4_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$officer4_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="officer4_email1" class="w100px" value="<?=$officer4_email1?>" />
								@
								<input type="text" name="officer4_email2" id="officer4_email2" class="w100px" value="<?=$officer4_email2?>" />
								<select name="officer4_email3" id="officer4_email3" class="email_domain_select">
									<?php
									foreach($GL_emailCollection as $item){
										echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
									}
									?>
								</select>
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
							<th>담당MD</th>
							<td class="text_left">
								<input type="text" name="md" class="w400px" maxlength="50" value="<?=$md?>" />
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<textarea name="etc" class="w400px"><?=$etc?></textarea>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:history.back();" class="large_btn red_btn">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/main.js"></script>
<script src="/js/page/info.site.js"></script>
<script>
	$(function(){
		SiteInfo.SiteInfoInit();
	});
</script>