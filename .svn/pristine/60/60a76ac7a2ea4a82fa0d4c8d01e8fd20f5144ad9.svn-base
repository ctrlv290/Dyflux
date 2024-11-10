<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 내정보 수정 - 공급처 Include (from myinfo.php)
 */

$mode = "add";
$supplier_idx                 = $idx;
$supplier_name                = "";
$manage_group_idx           = 0;
$supplier_ceo_name            = "";
$supplier_license_number      = "";
$supplier_license_no1         = "";
$supplier_license_no2         = "";
$supplier_license_no3         = "";
$supplier_officer1_name       = "";
$supplier_officer1_tel        = "";
$supplier_officer1_mobile     = "";
$supplier_officer1_email      = "";
$supplier_officer2_name       = "";
$supplier_officer2_tel        = "";
$supplier_officer2_mobile     = "";
$supplier_officer2_email      = "";
$supplier_officer3_name       = "";
$supplier_officer3_tel        = "";
$supplier_officer3_mobile     = "";
$supplier_officer1_email      = "";
$supplier_officer4_name       = "";
$supplier_officer4_tel        = "";
$supplier_officer4_mobile     = "";
$supplier_officer4_email      = "";
$supplier_md                  = "";
$supplier_etc                 = "";
$is_use          = "Y";


$C_Supplier = new Supplier();

if($supplier_idx)
{
	$_view = $C_Supplier->getSupplierData($supplier_idx);
	if($_view)
	{
		$mode = "mod_self";
		extract($_view);

		list($supplier_license_no1, $supplier_license_no2, $supplier_license_no3) = explode('-', $supplier_license_number);

		if($supplier_officer1_tel){
			list($supplier_officer1_tel1, $supplier_officer1_tel2, $supplier_officer1_tel3) = explode("-", $supplier_officer1_tel);
		}
		if($supplier_officer1_mobile){
			list($supplier_officer1_mobile1, $supplier_officer1_mobile2, $supplier_officer1_mobile3) = explode("-", $supplier_officer1_mobile);
		}
		if($supplier_officer1_email){
			list($supplier_officer1_email1, $supplier_officer1_email2) = explode("@", $supplier_officer1_email);
		}

		if($supplier_officer2_tel){
			list($supplier_officer2_tel1, $supplier_officer2_tel2, $supplier_officer2_tel3) = explode("-", $supplier_officer2_tel);
		}
		if($supplier_officer2_mobile){
			list($supplier_officer2_mobile1, $supplier_officer2_mobile2, $supplier_officer2_mobile3) = explode("-", $supplier_officer2_mobile);
		}
		if($supplier_officer2_email){
			list($supplier_officer2_email1, $supplier_officer2_email2) = explode("@", $supplier_officer2_email);
		}

		if($supplier_officer3_tel){
			list($supplier_officer3_tel1, $supplier_officer3_tel2, $supplier_officer3_tel3) = explode("-", $supplier_officer3_tel);
		}
		if($supplier_officer3_mobile){
			list($supplier_officer3_mobile1, $supplier_officer3_mobile2, $supplier_officer3_mobile3) = explode("-", $supplier_officer3_mobile);
		}
		if($supplier_officer3_email){
			list($supplier_officer3_email1, $supplier_officer3_email2) = explode("@", $supplier_officer3_email);
		}
		if($supplier_officer4_tel){
			list($supplier_officer4_tel1, $supplier_officer4_tel2, $supplier_officer4_tel3) = explode("-", $supplier_officer4_tel);
		}
		if($supplier_officer4_mobile){
			list($supplier_officer4_mobile1, $supplier_officer4_mobile2, $supplier_officer4_mobile3) = explode("-", $supplier_officer4_mobile);
		}
		if($supplier_officer4_email){
			list($supplier_officer4_email1, $supplier_officer4_email2) = explode("@", $supplier_officer4_email);
		}
	}else{
		put_msg_and_back("존재하지 않는 공급처입니다.");
	}
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
				<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
				<input type="hidden" name="supplier_idx" value="<?php echo $supplier_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>로그인 아이디 <?php if($mode == "add"){?><span class="lb_red">필수</span><?php }?></th>
							<td class="text_left">
								<?php if($mode == "add"){?>
									<input type="text" name="login_id" id="login_id" class="w200px userID" maxlength="12" value="" />
									<span class="info_txt col_red login_id_check_txt"></span>
									<span class="info_txt col_red">(4~12자리 숫자, 영문, -, _ 만 가능)</span>
								<?php }else{ ?>
									<?php echo $member_id;?>
								<?php } ?>
							</td>
						</tr>
						<tr class="mod_pass_btn dis_none">
							<th>로그인 비밀번호</th>
							<td class="text_left">
								<a href="javascript:;" class="btn red_btn btn-password-change">비밀번호 변경</a>
							</td>
						</tr>
						<tr class="mod_pass">
							<th>로그인 비밀번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="password" name="login_pw" id="login_pw" class="w200px" maxlength="12" value="" />
								<span class="info_txt col_red">(4~12자리의 숫자와 문자의 조합)</span>
							</td>
						</tr>
						<tr class="mod_pass">
							<th>비밀번호 확인 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="password" name="login_pw_re" id="login_pw_re" class="w200px" maxlength="12" value="" />
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
							<th>대표이사 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_ceo_name" class="w400px" maxlength="50" value="<?=$supplier_ceo_name?>" />
							</td>
						</tr>
						<tr>
							<th>사업자등록번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_license_no1" class="w30px onlyNumber" maxlength="3" value="<?=$supplier_license_no1?>" />
								-
								<input type="text" name="supplier_license_no2" class="w30px onlyNumber" maxlength="2" value="<?=$supplier_license_no2?>" />
								-
								<input type="text" name="supplier_license_no3" class="w40px onlyNumber" maxlength="5" value="<?=$supplier_license_no3?>" />
							</td>
						</tr>
						<tr>
							<th>주소 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_zipcode" id="supplier_zipcode" class="w60px" readonly="readonly" value="<?=$supplier_zipcode?>" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="supplier_zipcode" data-addr1-id="supplier_addr1" data-addr2-id="supplier_addr2">우편번호</a>
								<div class="row">
									<input type="text" name="supplier_addr1" id="supplier_addr1" class="w400px" readonly="readonly" value="<?=$supplier_addr1?>" />
								</div>
								<div class="row">
									<input type="text" name="supplier_addr2" id="supplier_addr2" class="w400px" value="<?=$supplier_addr2?>" />
								</div>
							</td>
						</tr>
						<tr>
							<th>팩스번호</th>
							<td class="text_left">
								<input type="text" name="supplier_fax" class="w400px onlyNumberPhone" maxlength="20" value="<?=$supplier_fax?>" />
							</td>
						</tr>
						<tr>
							<th>거래일</th>
							<td class="text_left">
								거래시작일 : <?=$supplier_startdate?>
								<input type="hidden" name="supplier_startdate" value="<?=$supplier_startdate?>" />
								&nbsp;&nbsp;
								거래종료일 : <?=$supplier_startdate?>
								<input type="hidden" name="supplier_enddate" value="<?=$supplier_startdate?>" />
							</td>
						</tr>
						<tr>
							<th>사업자등록증 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-license-file" id="btn-vendor-license-file">
									파일업로드
								</a>
								<span class="uploaded-file span_supplier_license_file"></span>
								<input type="hidden" name="supplier_license_file" id="supplier_license_file" value="<?=$supplier_license_file?>" />
							</td>
						</tr>
						<tr>
							<th>계좌번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_bank_account_number" class="w400px" maxlength="30" value="<?=$supplier_bank_account_number?>" />
							</td>
						</tr>
						<tr>
							<th>은행명 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_bank_name" class="w400px" maxlength="50" value="<?=$supplier_bank_name?>" />
							</td>
						</tr>
						<tr>
							<th>예금주 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_bank_holder_name" class="w400px" maxlength="50" value="<?=$supplier_bank_holder_name?>" />
							</td>
						</tr>
						<tr>
							<th>통장사본 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-bank-book-copy-file" id="btn-vendor-bank-book-copy-file">
									파일업로드
								</a>
								<span class="uploaded-file span_supplier_bank_book_copy_file"></span>
								<input type="hidden" name="supplier_bank_book_copy_file" id="supplier_bank_book_copy_file" value="<?=$supplier_bank_book_copy_file?>" />

							</td>
						</tr>
						<tr>
							<th rowspan="3">이메일</th>
							<td class="text_left">
								대표 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="supplier_email_default" id="supplier_email_default" class="w300px" maxlength="300" value="<?=$supplier_email_default?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								회계용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="supplier_email_account" id="supplier_email_account" class="w300px" maxlength="300" value="<?=$supplier_email_account?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								발주용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="supplier_email_order" id="supplier_email_order" class="w300px" maxlength="300" value="<?=$supplier_email_order?>" />
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
							<th>담당자 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_officer1_name" class="w400px" maxlength="30" value="<?=$supplier_officer1_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="supplier_officer1_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer1_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer1_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer1_tel2?>" />
								-
								<input type="text" name="supplier_officer1_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$supplier_officer1_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="supplier_officer1_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer1_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer1_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer1_mobile2?>" />
								-
								<input type="text" name="supplier_officer1_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer1_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="supplier_officer1_email1" class="w100px" value="<?=$supplier_officer1_email1?>" />
								@
								<input type="text" name="supplier_officer1_email2" id="supplier_officer1_email2" class="w100px" value="<?=$supplier_officer1_email2?>" />
								<select name="supplier_officer1_email3" id="supplier_officer1_email3" class="email_domain_select">
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
								<input type="text" name="supplier_officer2_name" class="w400px" maxlength="30" value="<?=$supplier_officer2_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="supplier_officer2_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer2_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer2_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer2_tel2?>" />
								-
								<input type="text" name="supplier_officer2_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$supplier_officer2_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="supplier_officer2_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer2_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer2_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer2_mobile2?>" />
								-
								<input type="text" name="supplier_officer2_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer2_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="supplier_officer2_email1" class="w100px" value="<?=$supplier_officer2_email1?>" />
								@
								<input type="text" name="supplier_officer2_email2" id="supplier_officer2_email2" class="w100px" value="<?=$supplier_officer2_email2?>" />
								<select name="supplier_officer2_email3" id="supplier_officer2_email3" class="email_domain_select">
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
								<input type="text" name="supplier_officer3_name" class="w400px" maxlength="30" value="<?=$supplier_officer3_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="supplier_officer3_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer3_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer3_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer3_tel2?>" />
								-
								<input type="text" name="supplier_officer3_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$supplier_officer3_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="supplier_officer3_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer3_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer3_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer3_mobile2?>" />
								-
								<input type="text" name="supplier_officer3_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer3_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="supplier_officer3_email1" class="w100px" value="<?=$supplier_officer3_email1?>" />
								@
								<input type="text" name="supplier_officer3_email2" id="supplier_officer3_email2" class="w100px" value="<?=$supplier_officer3_email2?>" />
								<select name="supplier_officer3_email3" id="supplier_officer3_email3" class="email_domain_select">
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
								<input type="text" name="supplier_officer4_name" class="w400px" maxlength="30" value="<?=$supplier_officer4_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="supplier_officer4_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer4_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer4_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer4_tel2?>" />
								-
								<input type="text" name="supplier_officer4_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$supplier_officer4_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="supplier_officer4_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($supplier_officer4_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="supplier_officer4_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer4_mobile2?>" />
								-
								<input type="text" name="supplier_officer4_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$supplier_officer4_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="supplier_officer4_email1" class="w100px" value="<?=$supplier_officer4_email1?>" />
								@
								<input type="text" name="supplier_officer4_email2" id="supplier_officer4_email2" class="w100px" value="<?=$supplier_officer4_email2?>" />
								<select name="supplier_officer4_email3" id="supplier_officer4_email3" class="email_domain_select">
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
								<input type="text" name="supplier_md" class="w400px" maxlength="50" value="<?=$supplier_md?>" />
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<textarea name="supplier_etc" class="w400px"><?=$supplier_etc?></textarea>
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
<script src="/js/page/info.supplier.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	Supplier.SupplierWriteInit();
	ManageGroup.getManageGroupList('SUPPLIER_GROUP');
	//FileUpload.initUploadHTML();

</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
