<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 160;
//Init
include_once "../_init_.php";


$mode = "add";
$vendor_idx                 = $_GET["vendor_idx"];
$vendor_name                = "";
$manage_group_idx           = 0;
$vendor_grade               = "X";
$vendor_ceo_name            = "";
$vendor_license_number      = "";
$vendor_license_no1         = "";
$vendor_license_no2         = "";
$vendor_license_no3         = "";
$vendor_officer1_name       = "";
$vendor_officer1_tel        = "";
$vendor_officer1_mobile     = "";
$vendor_officer1_email      = "";
$vendor_officer2_name       = "";
$vendor_officer2_tel        = "";
$vendor_officer2_mobile     = "";
$vendor_officer2_email      = "";
$vendor_officer3_name       = "";
$vendor_officer3_tel        = "";
$vendor_officer3_mobile     = "";
$vendor_officer1_email      = "";
$vendor_officer4_name       = "";
$vendor_officer4_tel        = "";
$vendor_officer4_mobile     = "";
$vendor_officer4_email      = "";
$vendor_md                  = "";
$vendor_etc                 = "";
$is_use          = "Y";
$vendor_use_charge = "N";
$vendor_is_order_block = "N";


$C_Vendor = new Vendor();

if($vendor_idx)
{
	$_view = $C_Vendor->getVendorData($vendor_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);

		list($vendor_license_no1, $vendor_license_no2, $vendor_license_no3) = explode('-', $vendor_license_number);

		if($vendor_officer1_tel){
			list($vendor_officer1_tel1, $vendor_officer1_tel2, $vendor_officer1_tel3) = explode("-", $vendor_officer1_tel);
		}
		if($vendor_officer1_mobile){
			list($vendor_officer1_mobile1, $vendor_officer1_mobile2, $vendor_officer1_mobile3) = explode("-", $vendor_officer1_mobile);
		}
		if($vendor_officer1_email){
			list($vendor_officer1_email1, $vendor_officer1_email2) = explode("@", $vendor_officer1_email);
		}

		if($vendor_officer2_tel){
			list($vendor_officer2_tel1, $vendor_officer2_tel2, $vendor_officer2_tel3) = explode("-", $vendor_officer2_tel);
		}
		if($vendor_officer2_mobile){
			list($vendor_officer2_mobile1, $vendor_officer2mobile2, $vendor_officer2_mobile3) = explode("-", $vendor_officer2_mobile);
		}
		if($vendor_officer2_email){
			list($vendor_officer2_email1, $vendor_officer2_email2) = explode("@", $vendor_officer2_email);
		}

		if($vendor_officer3_tel){
			list($vendor_officer3_tel1, $vendor_officer3_tel2, $vendor_officer3_tel3) = explode("-", $vendor_officer3_tel);
		}
		if($vendor_officer3_mobile){
			list($vendor_officer3_mobile1, $vendor_officer3_mobile2, $vendor_officer3_mobile3) = explode("-", $vendor_officer3_mobile);
		}
		if($vendor_officer3_email){
			list($vendor_officer3_email1, $vendor_officer3_email2) = explode("@", $vendor_officer3_email);
		}

		if($vendor_officer4_tel){
			list($vendor_officer4_tel1, $vendor_officer4_tel2, $vendor_officer4_tel3) = explode("-", $vendor_officr4_tel);
		}
		if($vendor_officer4_mobile){
			list($vendor_officer4_mobile1, $vendor_officer4_mobile2, $vendor_officer4_mobile3) = explode("-", $vendor_officer4_mobile);
		}
		if($vendor_officer4_email){
			list($vendor_officer4_email1, $vendor_officer4_email2) = explode("@", $vendor_officer4_email);
		}
	}else{
		put_msg_and_back("존재하지 않는 벤더사입니다.");
	}
}

//벤더사 등급 가져오기
$C_VendorGrade = new VendorGrade();
$aryVendorGradeList = $C_VendorGrade->getVendorGradeList();
?>

<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
				<input type="hidden" name="vendor_idx" value="<?php echo $vendor_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>벤더사 명 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_name" class="w400px" maxlength="20" value="<?=$vendor_name?>" />
								<span class="info_txt col_red">(한글 20자 이내)</span>
							</td>
						</tr>
						<tr>
							<th>벤더사 그룹</th>
							<td class="text_left">
								<select name="manage_group_idx" id="manage_group_idx" data-selected="<?=$manage_group_idx?>">
								</select>
								<a href="javascript:;" class="btn orange_btn btn-vendor_group_pop">벤더사 그룹 신규 등록</a>
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
							<th>등급 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<?php
								foreach($aryVendorGradeList as $vG){
									echo '<label><input type="radio" id="vendor_grade_'.$vG["vendor_grade"].'" name="vendor_grade" value="'.$vG["vendor_grade"].'" ' . (($vendor_grade == $vG["vendor_grade"]) ? "checked" : "") . ' /> '.$vG["vendor_grade_name"].'</label>';
								}
								?>
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
								<input type="text" name="vendor_ceo_name" class="w400px" maxlength="50" value="<?=$vendor_ceo_name?>" />
							</td>
						</tr>
						<tr>
							<th>사업자등록번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_license_no1" class="w30px onlyNumber" maxlength="3" value="<?=$vendor_license_no1?>" />
								-
								<input type="text" name="vendor_license_no2" class="w30px onlyNumber" maxlength="2" value="<?=$vendor_license_no2?>" />
								-
								<input type="text" name="vendor_license_no3" class="w40px onlyNumber" maxlength="5" value="<?=$vendor_license_no3?>" />
							</td>
						</tr>
						<tr>
							<th>주소 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_zipcode" id="vendor_zipcode" class="w60px" readonly="readonly" value="<?=$vendor_zipcode?>" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="vendor_zipcode" data-addr1-id="vendor_addr1" data-addr2-id="vendor_addr2">우편번호</a>
								<div class="row">
									<input type="text" name="vendor_addr1" id="vendor_addr1" class="w400px" readonly="readonly" value="<?=$vendor_addr1?>" />
								</div>
								<div class="row">
									<input type="text" name="vendor_addr2" id="vendor_addr2" class="w400px" value="<?=$vendor_addr2?>" />
								</div>
							</td>
						</tr>
						<tr>
							<th>팩스번호</th>
							<td class="text_left">
								<input type="text" name="vendor_fax" class="w400px onlyNumberPhone" maxlength="20" value="<?=$vendor_fax?>" />
							</td>
						</tr>
						<tr>
							<th>거래일</th>
							<td class="text_left">
								거래시작일 : <input type="text" name="vendor_startdate" class="w100px jqDate" readonly="readonly" maxlength="10" value="<?=$vendor_startdate?>" />
								&nbsp;&nbsp;
								거래종료일 : <input type="text" name="vendor_enddate" class="w100px jqDate" readonly="readonly" maxlength="10" value="<?=$vendor_startdate?>" />
							</td>
						</tr>
						<tr>
							<th>사업자등록증 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-license-file" id="btn-vendor-license-file">
									파일업로드
								</a>
								<span class="uploaded-file span_vendor_license_file"></span>
								<input type="hidden" name="vendor_license_file" id="vendor_license_file" value="<?=$vendor_license_file?>" />
							</td>
						</tr>
						<tr>
							<th>계좌번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_bank_account_number" class="w400px" maxlength="30" value="<?=$vendor_bank_account_number?>" />
							</td>
						</tr>
						<tr>
							<th>은행명 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_bank_name" class="w400px" maxlength="50" value="<?=$vendor_bank_name?>" />
							</td>
						</tr>
						<tr>
							<th>예금주 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_bank_holder_name" class="w400px" maxlength="50" value="<?=$vendor_bank_holder_name?>" />
							</td>
						</tr>
						<tr>
							<th>통장사본 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-bank-book-copy-file" id="btn-vendor-bank-book-copy-file">
									파일업로드
								</a>
								<span class="uploaded-file span_vendor_bank_book_copy_file"></span>
								<input type="hidden" name="vendor_bank_book_copy_file" id="vendor_bank_book_copy_file" value="<?=$vendor_bank_book_copy_file?>" />

							</td>
						</tr>
						<tr>
							<th rowspan="3">이메일</th>
							<td class="text_left">
								대표 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="vendor_email_default" id="vendor_email_default" class="w300px" maxlength="300" value="<?=$vendor_email_default?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								회계용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="vendor_email_account" id="vendor_email_account" class="w300px" maxlength="300" value="<?=$vendor_email_account?>" />
								</div>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								발주용 이메일 <span class="lb_red">필수</span>
								<span class="info_txt col_red">다중입력 가능 (엔터)</span>
								<div class="row">
									<input type="text" name="vendor_email_order" id="vendor_email_order" class="w300px" maxlength="300" value="<?=$vendor_email_order?>" />
								</div>
							</td>
						</tr>
						<tr>
							<th>충전금 사용여부  <span class="lb_red">필수</span></th>
							<td class="text_left">
								<label><input type="radio" id="vendor_use_charge_y" name="vendor_use_charge" value="Y" <?=($vendor_use_charge == "Y") ? "checked" : ""?> /> Y</label>
								<label><input type="radio" id="vendor_use_charge_n" name="vendor_use_charge" value="N" <?=($vendor_use_charge == "N") ? "checked" : ""?>/> N</label>
							</td>
						</tr>
                        <tr>
                            <th>발주 차단<span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <label><input type="radio" id="vendor_is_order_block_y" name="vendor_is_order_block" value="Y" <?=($vendor_is_order_block == "Y") ? "checked" : ""?> /> Y</label>
                                <label><input type="radio" id="vendor_is_order_block_n" name="vendor_is_order_block" value="N" <?=($vendor_is_order_block == "N") ? "checked" : ""?>/> N</label>
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
								<input type="text" name="vendor_officer1_name" class="w400px" maxlength="30" value="<?=$vendor_officer1_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="vendor_officer1_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer1_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer1_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer1_tel2?>" />
								-
								<input type="text" name="vendor_officer1_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$vendor_officer1_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<select name="vendor_officer1_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer1_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer1_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer1_mobile2?>" />
								-
								<input type="text" name="vendor_officer1_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer1_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="vendor_officer1_email1" class="w100px" value="<?=$vendor_officer1_email1?>" />
								@
								<input type="text" name="vendor_officer1_email2" id="vendor_officer1_email2" class="w100px" value="<?=$vendor_officer1_email2?>" />
								<select name="vendor_officer1_email3" id="vendor_officer1_email3" class="email_domain_select">
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
								<input type="text" name="vendor_officer2_name" class="w400px" maxlength="30" value="<?=$vendor_officer2_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="vendor_officer2_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer2_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer2_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer2_tel2?>" />
								-
								<input type="text" name="vendor_officer2_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$vendor_officer2_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="vendor_officer2_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer2_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer2_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer2_mobile2?>" />
								-
								<input type="text" name="vendor_officer2_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer2_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="vendor_officer2_email1" class="w100px" value="<?=$vendor_officer2_email1?>" />
								@
								<input type="text" name="vendor_officer2_email2" id="vendor_officer2_email2" class="w100px" value="<?=$vendor_officer2_email2?>" />
								<select name="vendor_officer2_email3" id="vendor_officer2_email3"  class="email_domain_select">
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
								<input type="text" name="vendor_officer3_name" class="w400px" maxlength="30" value="<?=$vendor_officer3_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="vendor_officer3_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer3_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer3_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer3_tel2?>" />
								-
								<input type="text" name="vendor_officer3_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$vendor_officer3_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="vendor_officer3_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer3_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer3_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer3_mobile2?>" />
								-
								<input type="text" name="vendor_officer3_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer3_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="vendor_officer3_email1" class="w100px" value="<?=$vendor_officer3_email1?>" />
								@
								<input type="text" name="vendor_officer3_email2" id="vendor_officer3_email2" class="w100px" value="<?=$vendor_officer3_email2?>" />
								<select name="vendor_officer3_email3" id="vendor_officer3_email3" class="email_domain_select">
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
								<input type="text" name="vendor_officer4_name" class="w400px" maxlength="30" value="<?=$vendor_officer4_name?>" />
							</td>
						</tr>
						<tr>
							<th>연락처 </th>
							<td class="text_left">
								<select name="vendor_officer4_tel1" class="w60px">
									<?php
									foreach($GL_telCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer4_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer4_tel2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer4_tel2?>" />
								-
								<input type="text" name="vendor_officer4_tel3" class="w60px onlyNumber " maxlength="4" value="<?=$vendor_officer4_tel3?>" />
							</td>
						</tr>
						<tr>
							<th>휴대폰번호</th>
							<td class="text_left">
								<select name="vendor_officer4_mobile1" class="w60px">
									<?php
									foreach($GL_mobileCollection as $item){
										echo '<option value="'.$item.'" '.(($vendor_officer4_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
									}
									?>
								</select>
								-
								<input type="text" name="vendor_officer4_mobile2" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer4_mobile2?>" />
								-
								<input type="text" name="vendor_officer4_mobile3" class="w60px onlyNumber" maxlength="4" value="<?=$vendor_officer4_mobile3?>" />
							</td>
						</tr>
						<tr>
							<th>이메일</th>
							<td class="text_left">
								<input type="text" name="vendor_officer4_email1" class="w100px" value="<?=$vendor_officer4_email1?>" />
								@
								<input type="text" name="vendor_officer4_email2" id="vendor_officer4_email2" class="w100px" value="<?=$vendor_officer4_email2?>" />
								<select name="vendor_officer4_email3" id="vendor_officer4_email3" class="email_domain_select">
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
								<input type="text" name="vendor_md" class="w400px" maxlength="50" value="<?=$vendor_md?>" />
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<textarea name="vendor_etc" class="w400px"><?=$vendor_etc?></textarea>
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
							<th>사용여부  <span class="lb_red">필수</span></th>
							<td class="text_left">
								<label><input type="radio" id="is_use_y" name="is_use" value="Y" <?=($is_use == "Y") ? "checked" : ""?> /> Y</label>
								<label><input type="radio" id="is_use_n" name="is_use" value="N" <?=($is_use == "N") ? "checked" : ""?>/> N</label>
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
<script src="/js/page/info.vendor.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	Vendor.VendorWriteInit();
	ManageGroup.getManageGroupList('VENDOR_GROUP');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
