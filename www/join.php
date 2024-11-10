<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 페이지
 */

//Init
include_once "./_init_.php";


$mode = "add";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>DYFLUX</title>
	<meta property="og:title" content="DYFLUX"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?=DY_URL?>/"/>
	<meta property="og:image" content="<?=DY_URL?>/images/og_meta.png"/>
	<meta property="og:description" content="DYFLUX"/>
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

	<link rel="stylesheet" href="/fontawesome-free-5.1.1-web/css/all.css">

	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/fonts.css"/>
	<link rel="stylesheet" type="text/css" href="css/slick.css"/>
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<link rel="stylesheet" type="text/css" href="css/loading.css"/>

	<script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/moment.js"></script>
	<script type="text/javascript" src="/js/moment.locale.ko.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.min.js"></script>
	<script type="text/javascript" src="/js/grid.locale-kr.js"></script>
	<script type="text/javascript" src="/js/jquery.jqGrid.setColWidth.js"></script>
	<script type="text/javascript" src="/js/multiple-emails.js"></script>
	<script type="text/javascript" src="/js/lightbox.min.js"></script>
	<script type="text/javascript" src="/js/selectize.min.js"></script>
	<script type="text/javascript" src="/js/jquery.inputmask.bundle.min.js"></script>
	<script type="text/javascript" src="/js/jquery.scrollbar.min.js"></script>
	<script type="text/javascript" src="/js/common.js"></script>

	<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>

</head>
<body>

<div class="wrap">
	<div class="wrap_header">
		<div class="wrap_in">
			<div class="in">
				<a href="javascript:;" class="logo"><img src="images/logo.png" alt="dy flux" /></a>
			</div>
		</div>
	</div>

	<div class="wrap_content">
		<div class="wrap_in">
			<div class="in">

				<div class="apply_set">
					<p class="title">업체 신청</p>

					<form name="dyForm" method="post" class="<?php echo $mode?> apply_form">
						<input type="hidden" name="mode" value="<?php echo $mode?>" />
						<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
						<input type="hidden" name="where" id="h_where" value="main_join" />
						<div class="title_set">
							<p class="form_title">업체 신청서 작성</p>
							<p class="form_s_title"><span>*</span>필수입력 사항 입니다.</p>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_name">
									<span class="common_title"><span class="star">*</span>업체명</span>
									<span class="input_line"><input type="text" id="vendor_name" name="vendor_name" /></span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="login_id">
									<span class="common_title"><span class="star">*</span>아이디</span>
									<span class="input_line"><input type="text" id="login_id" name="login_id" class="userID" maxlength="12" /></span>
									<span class="info_txt col_red insert login_id_check_txt"></span>
									<span class="info_txt insert">(4~12자리 숫자, 영문, -, _ 만 가능)</span>
								</label>
							</div>
							<div class="line_set">
								<label for="login_pw">
									<span class="common_title"><span class="star">*</span>비밀번호</span>
									<span class="input_line"><input type="password" id="login_pw" name="login_pw" maxlength="12" /></span>
									<span class="insert">4~12자리의 숫자와 문자의 조합</span>
								</label>
							</div>
							<div class="line_set">
								<label for="login_pw_re">
									<span class="common_title"><span class="star">*</span>비밀번호 확인</span>
									<span class="input_line"><input type="password" id="login_pw_re" name="login_pw_re" maxlength="12" /></span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_ceo_name">
									<span class="common_title"><span class="star">*</span>대표이사</span>
									<span class="input_line"><input type="text" id="vendor_ceo_name" name="vendor_ceo_name" maxlength="50" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_license_no1">
									<span class="common_title"><span class="star">*</span>사업자번호</span>
									<span class="input_line input_size">
											<input type="text" name="vendor_license_no1" id="vendor_license_no1" maxlength="3" class="input_size01" />
											<input type="text" name="vendor_license_no2" maxlength="2" class="input_size02" />
											<input type="text" name="vendor_license_no3" maxlength="5" class="input_size02" />
										</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_addr2">
									<span class="common_title"><span class="star">*</span>주소</span>
									<span class="input_line input_add">
											<span class="zipcode_line">
												<input type="text" id="vendor_zipcode" name="vendor_zipcode" readonly="readonly" />
												<a href="javascript:;" class="btn zipcode btn-address-zipcode" data-zipcode-id="vendor_zipcode" data-addr1-id="vendor_addr1" data-addr2-id="vendor_addr2">우편번호</a>
											</span>
											<input type="text" name="vendor_addr1" id="vendor_addr1" maxlength="200" />
											<input type="text" name="vendor_addr2" id="vendor_addr2" maxlength="200" />
										</span>
								</label>
							</div>

							<div class="line_set">
								<label for="vendor_fax">
									<span class="common_title">팩스번호</span>
									<span class="input_line"><input type="text" id="vendor_fax" name="vendor_fax" class="onlyNumberPhone" maxlength="20" /></span>
								</label>
							</div>

							<div class="line_set">
								<label for="">
									<span class="common_title"><span class="star">*</span>사업자등록증</span>
									<span class="input_line input_size w750">
										<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-license-file" id="btn-vendor-license-file">
											파일업로드
										</a>
										<span class="insert uploaded-file span_vendor_license_file"></span>
										<input type="hidden" name="vendor_license_file" id="vendor_license_file" value="<?=$vendor_license_file?>" />
									</span>
								</label>
							</div>

							<div class="line_set">
								<label for="vendor_bank_account_number">
									<span class="common_title"><span class="star">*</span>계좌번호</span>
									<span class="input_line input_size">
										<input type="text" name="vendor_bank_account_number" id="vendor_bank_account_number" maxlength="30" class="" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_bank_name">
									<span class="common_title"><span class="star">*</span>은행명</span>
									<span class="input_line input_size">
										<input type="text" name="vendor_bank_name" id="vendor_bank_name" maxlength="50" class="" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_bank_holder_name">
									<span class="common_title"><span class="star">*</span>예금주</span>
									<span class="input_line input_size">
										<input type="text" name="vendor_bank_holder_name" id="vendor_bank_holder_name" maxlength="50" class="" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="btn-vendor-bank-book-copy-file">
									<span class="common_title"><span class="star">*</span>통장사본</span>
									<span class="input_line input_size w750">
										<a href="javascript:;" class="btn green_btn btn_relative btn-vendor-bank-book-copy-file" id="btn-vendor-bank-book-copy-file">
											파일업로드
										</a>
										<span class="insert uploaded-file span_vendor_bank_book_copy_file"></span>
										<input type="hidden" name="vendor_bank_book_copy_file" id="vendor_bank_book_copy_file" value="<?=$vendor_bank_book_copy_file?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_email_default">
									<span class="common_title"><span class="star">*</span>이메일(대표)</span>
									<span class="input_line input_add">
										<input type="text" name="vendor_email_default" id="vendor_email_default" maxlength="50" class="" placeholder="대표 이메일" />
										<input type="text" name="vendor_email_account" id="vendor_email_account" maxlength="50" class="" placeholder="회계용 이메일" />
										<input type="text" name="vendor_email_order"   id="vendor_email_order" maxlength="50" class="" placeholder="발주용 이메일" />
									</span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_officer1_name">
									<span class="common_title"><span class="star">*</span>담당자</span>
									<span class="input_line"><input type="text" id="vendor_officer1_name" name="vendor_officer1_name" maxlength="30" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer1_tel1">
									<span class="common_title"><span class="star">*</span>연락처</span>
									<span class="input_line input_size">

									<select name="vendor_officer1_tel1" id="vendor_officer1_tel1" class="input_size01">
										<?php
										foreach($GL_telCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer1_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer1_tel2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer1_tel2?>" />
									-
									<input type="text" name="vendor_officer1_tel3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer1_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer1_mobile1">
									<span class="common_title"><span class="star">*</span>휴대폰번호</span>
									<span class="input_line input_size">

									<select name="vendor_officer1_mobile1" id="vendor_officer1_mobile1" class="input_size01">
										<?php
										foreach($GL_mobileCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer1_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer1_mobile2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer1_tel2?>" />
									-
									<input type="text" name="vendor_officer1_mobile3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer1_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer1_email1">
									<span class="common_title"><span class="star">*</span>이메일</span>
									<span class="input_line input_size w750">
									<input type="text" name="vendor_officer1_email1" class="vendor_officer1_email1 input_size02" maxlength="30" value="<?=$vendor_officer1_tel2?>" />
									<span class="txt">@</span>
									<input type="text" name="vendor_officer1_email2" id="vendor_officer1_email2 " class="input_size03 " value="<?=$vendor_officer1_email2?>" />
									<select name="vendor_officer1_email3" id="vendor_officer1_email3" class="email_domain_select input_size03">
										<?php
										foreach($GL_emailCollection as $item){
											echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
										}
										?>
									</select>
									</span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_officer2_name">
									<span class="common_title">담당자</span>
									<span class="input_line"><input type="text" id="vendor_officer2_name" name="vendor_officer2_name" maxlength="30" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer2_tel1">
									<span class="common_title">연락처</span>
									<span class="input_line input_size">

									<select name="vendor_officer2_tel1" id="vendor_officer2_tel1" class="input_size01">
										<?php
										foreach($GL_telCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer2_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer2_tel2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer2_tel2?>" />
									-
									<input type="text" name="vendor_officer2_tel3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer2_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer2_mobile1">
									<span class="common_title">휴대폰번호</span>
									<span class="input_line input_size">

									<select name="vendor_officer2_mobile1" id="vendor_officer2_mobile1" class="input_size01">
										<?php
										foreach($GL_mobileCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer2_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer2_mobile2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer2_tel2?>" />
									-
									<input type="text" name="vendor_officer2_mobile3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer2_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer2_email1">
									<span class="common_title">이메일</span>
									<span class="input_line input_size w750">
									<input type="text" name="vendor_officer2_email1" class="vendor_officer2_email1 input_size02" maxlength="30" value="<?=$vendor_officer2_tel2?>" />
									<span class="txt">@</span>
									<input type="text" name="vendor_officer2_email2" id="vendor_officer2_email2 " class="input_size03 " value="<?=$vendor_officer2_email2?>" />
									<select name="vendor_officer2_email3" id="vendor_officer2_email3" class="email_domain_select input_size03">
										<?php
										foreach($GL_emailCollection as $item){
											echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
										}
										?>
									</select>
									</span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_officer3_name">
									<span class="common_title">담당자</span>
									<span class="input_line"><input type="text" id="vendor_officer3_name" name="vendor_officer3_name" maxlength="30" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer3_tel1">
									<span class="common_title">연락처</span>
									<span class="input_line input_size">

									<select name="vendor_officer3_tel1" id="vendor_officer3_tel1" class="input_size01">
										<?php
										foreach($GL_telCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer3_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer3_tel2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer3_tel2?>" />
									-
									<input type="text" name="vendor_officer3_tel3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer3_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer3_mobile1">
									<span class="common_title">휴대폰번호</span>
									<span class="input_line input_size">

									<select name="vendor_officer3_mobile1" id="vendor_officer3_mobile1" class="input_size01">
										<?php
										foreach($GL_mobileCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer3_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer3_mobile2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer3_tel2?>" />
									-
									<input type="text" name="vendor_officer3_mobile3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer3_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer3_email1">
									<span class="common_title">이메일</span>
									<span class="input_line input_size w750">
									<input type="text" name="vendor_officer3_email1" class="vendor_officer3_email1 input_size02" maxlength="30" value="<?=$vendor_officer3_tel2?>" />
									<span class="txt">@</span>
									<input type="text" name="vendor_officer3_email2" id="vendor_officer3_email2 " class="input_size03 " value="<?=$vendor_officer3_email2?>" />
									<select name="vendor_officer3_email3" id="vendor_officer3_email3" class="email_domain_select input_size03">
										<?php
										foreach($GL_emailCollection as $item){
											echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
										}
										?>
									</select>
									</span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_officer4_name">
									<span class="common_title">담당자</span>
									<span class="input_line"><input type="text" id="vendor_officer4_name" name="vendor_officer4_name" maxlength="30" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer4_tel1">
									<span class="common_title">연락처</span>
									<span class="input_line input_size">

									<select name="vendor_officer4_tel1" id="vendor_officer4_tel1" class="input_size01">
										<?php
										foreach($GL_telCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer4_tel1 == $item) ? 'selected' : '').' >'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer4_tel2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer4_tel2?>" />
									-
									<input type="text" name="vendor_officer4_tel3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer4_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer4_mobile1">
									<span class="common_title">휴대폰번호</span>
									<span class="input_line input_size">

									<select name="vendor_officer4_mobile1" id="vendor_officer4_mobile1" class="input_size01">
										<?php
										foreach($GL_mobileCollection as $item){
											echo '<option value="'.$item.'" '.(($vendor_officer4_mobile1 == $item) ? 'selected' : '').'>'.$item.'</option>';
										}
										?>
									</select>
									-
									<input type="text" name="vendor_officer4_mobile2" class="input_size01 onlyNumber" maxlength="4" value="<?=$vendor_officer4_tel2?>" />
									-
									<input type="text" name="vendor_officer4_mobile3" class="input_size01 onlyNumber " maxlength="4" value="<?=$vendor_officer4_tel3?>" />
									</span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_officer4_email1">
									<span class="common_title">이메일</span>
									<span class="input_line input_size w750">
									<input type="text" name="vendor_officer4_email1" class="vendor_officer4_email1 input_size02" maxlength="30" value="<?=$vendor_officer4_tel2?>" />
									<span class="txt">@</span>
									<input type="text" name="vendor_officer4_email2" id="vendor_officer4_email2 " class="input_size03 " value="<?=$vendor_officer4_email2?>" />
									<select name="vendor_officer4_email3" id="vendor_officer4_email3" class="email_domain_select input_size03">
										<?php
										foreach($GL_emailCollection as $item){
											echo '<option value="'.$item["email_en"].'">'.$item["email_en"].'('.$item["email_ko"].')</option>';
										}
										?>
									</select>
									</span>
								</label>
							</div>
						</div>

						<div class="top_bd">
							<div class="line_set">
								<label for="vendor_md">
									<span class="common_title">담당MD</span>
									<span class="input_line"><input type="text" id="vendor_md" name="vendor_md" maxlength="30" /></span>
								</label>
							</div>
							<div class="line_set">
								<label for="vendor_etc">
									<span class="common_title">비고</span>
									<span class="input_line2">
										<textarea id="vendor_etc" name="vendor_etc" class="textarea"></textarea>
									</span>
								</label>
							</div>
						</div>

						<span class="butn_set">
							<a href="javascript:;" id="btn-save" class="send_btn">신청</a>
							<a href="javascript:history.back();" class="cancel_btn">취소</a>
						</span>
					</form>
				</div>

			</div><!-- in -->
		</div><!-- wrap_in -->
	</div><!-- wrap_content -->

	<div class="wrap_footer">
		<div>
			<p><strong>(주)덕윤</strong><span>경기도 고양시 일산동구 정발산로24 웨스턴타워 4차 416-417 호</span></p>
		</div>
		<div>
			<p><strong>대표</strong><span>곽동호</span></p>
			<p><strong>개인정보 책임자</strong><span>조상현(shcho@duckyun.com)</span></p>
		</div>
		<div>
			<p><strong>사업자등록번호</strong><span>128-87-12256</span></p>
			<p><strong>통신판매업신고</strong><span>2014-고양일산동-0770</span></p>
		</div>
	</div><!-- wrap_footer -->
</div>

<div class="loading_dimmer">
	<div class="lds-css ng-scope">
		<div style="width:100%;height:100%" class="lds-wedges">
			<div>
				<div>
					<div></div>
				</div>
				<div>
					<div></div>
				</div>
				<div>
					<div></div>
				</div>
				<div>
					<div></div>
				</div>
			</div>
		</div>
	</div>
</div>

<iframe src="about:blank" id="hidden_ifrm_common_filedownload" name="hidden_ifrm_common_filedownload" frameborder="0" style="width: 0;height: 0;display: none;"></iframe>

<script src="js/main.js"></script>
<script src="js/String.js"></script>
<script src="js/FormCheck.js"></script>
<script src="/js/fileupload.js"></script>
<script src="js/page/main.join.js"></script>
<script>
	window.name = "main_join";
	MainJoin.JoinInit();
</script>
</body>
</html>