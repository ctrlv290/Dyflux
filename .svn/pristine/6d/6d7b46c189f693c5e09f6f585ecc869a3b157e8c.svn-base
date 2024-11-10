<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사관리 관련 Process
 */

//Page Info
$pageMenuIdx = 46;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Vendor = new Vendor();
$C_Files = new Files();
//print_r($_POST);

$mode                           = $_POST["mode"];
$vendor_idx                     = $_POST["vendor_idx"];
$login_id                       = $_POST["login_id"];
$login_pw                       = $_POST["login_pw"];
$vendor_name                    = $_POST["vendor_name"];
$manage_group_idx               = $_POST["manage_group_idx"];
$vendor_grade                   = $_POST["vendor_grade"];
$vendor_ceo_name                = $_POST["vendor_ceo_name"];
$vendor_license_no              = $_POST["vendor_license_no1"] . "-" . $_POST["vendor_license_no2"] . "-" . $_POST["vendor_license_no3"];
$vendor_zipcode                 = $_POST["vendor_zipcode"];
$vendor_addr1                   = $_POST["vendor_addr1"];
$vendor_addr2                   = $_POST["vendor_addr2"];
$vendor_fax                     = $_POST["vendor_fax"];
$vendor_startdate               = $_POST["vendor_startdate"];
$vendor_enddate                 = $_POST["vendor_enddate"];
$vendor_license_file            = $_POST["vendor_license_file"];
$vendor_bank_account_number     = $_POST["vendor_bank_account_number"];
$vendor_bank_name               = $_POST["vendor_bank_name"];
$vendor_bank_holder_name        = $_POST["vendor_bank_holder_name"];
$vendor_bank_book_copy_file     = $_POST["vendor_bank_book_copy_file"];
$vendor_email_default           = $_POST["vendor_email_default"];
$vendor_email_account           = $_POST["vendor_email_account"];
$vendor_email_order             = $_POST["vendor_email_order"];
$vendor_use_charge              = $_POST["vendor_use_charge"];
$vendor_is_order_block          = $_POST["vendor_is_order_block"];

$vendor_officer1_name          = $_POST["vendor_officer1_name"];
$vendor_officer1_tel1          = $_POST["vendor_officer1_tel1"];
$vendor_officer1_tel2          = $_POST["vendor_officer1_tel2"];
$vendor_officer1_tel3          = $_POST["vendor_officer1_tel3"];
$vendor_officer1_tel           = ($vendor_officer1_tel2 && $vendor_officer1_tel3) ? $vendor_officer1_tel1 . "-" . $vendor_officer1_tel2 . "-" . $vendor_officer1_tel3 : "";
$vendor_officer1_mobile1       = $_POST["vendor_officer1_mobile1"];
$vendor_officer1_mobile2       = $_POST["vendor_officer1_mobile2"];
$vendor_officer1_mobile3       = $_POST["vendor_officer1_mobile3"];
$vendor_officer1_mobile        = $vendor_officer1_mobile1 . "-" . $vendor_officer1_mobile2 . "-" . $vendor_officer1_mobile3;
$vendor_officer1_email1        = $_POST["vendor_officer1_email1"];
$vendor_officer1_email2        = $_POST["vendor_officer1_email2"];
$vendor_officer1_email         = ($vendor_officer1_email1 && $vendor_officer1_email2) ? $vendor_officer1_email1 . "@" . $vendor_officer1_email2 : "";

$vendor_officer2_name          = $_POST["vendor_officer2_name"];
$vendor_officer2_tel1          = $_POST["vendor_officer2_tel1"];
$vendor_officer2_tel2          = $_POST["vendor_officer2_tel2"];
$vendor_officer2_tel3          = $_POST["vendor_officer2_tel3"];
$vendor_officer2_tel           = ($vendor_officer2_tel2 && $vendor_officer2_tel3) ? $vendor_officer2_tel1 . "-" . $vendor_officer2_tel2 . "-" . $vendor_officer2_tel3 : "";
$vendor_officer2_mobile1       = $_POST["vendor_officer2_mobile1"];
$vendor_officer2_mobile2       = $_POST["vendor_officer2_mobile2"];
$vendor_officer2_mobile3       = $_POST["vendor_officer2_mobile3"];
$vendor_officer2_mobile        = ($vendor_officer2_mobile1 && $vendor_officer2_mobile3) ? $vendor_officer2_mobile1 . "-" . $vendor_officer2_mobile2 . "-" . $vendor_officer2_mobile3 : "";
$vendor_officer2_email1        = $_POST["vendor_officer2_email1"];
$vendor_officer2_email2        = $_POST["vendor_officer2_email2"];
$vendor_officer2_email         = ($vendor_officer2_email1 && $vendor_officer2_email2) ? $vendor_officer2_email1 . "@" . $vendor_officer2_email2 : "";

$vendor_officer3_name          = $_POST["vendor_officer3_name"];
$vendor_officer3_tel1          = $_POST["vendor_officer3_tel1"];
$vendor_officer3_tel2          = $_POST["vendor_officer3_tel2"];
$vendor_officer3_tel3          = $_POST["vendor_officer3_tel3"];
$vendor_officer3_tel           = ($vendor_officer3_tel2 && $vendor_officer3_tel3) ? $vendor_officer3_tel1 . "-" . $vendor_officer3_tel2 . "-" . $vendor_officer3_tel3 : "";
$vendor_officer3_mobile1       = $_POST["vendor_officer3_mobile1"];
$vendor_officer3_mobile2       = $_POST["vendor_officer3_mobile2"];
$vendor_officer3_mobile3       = $_POST["vendor_officer3_mobile3"];
$vendor_officer3_mobile        = ($vendor_officer3_mobile2 && $vendor_officer3_mobile3) ? $vendor_officer3_mobile1 . "-" . $vendor_officer3_mobile2 . "-" . $vendor_officer3_mobile3 : "";
$vendor_officer3_email1        = $_POST["vendor_officer3_email1"];
$vendor_officer3_email2        = $_POST["vendor_officer3_email2"];
$vendor_officer3_email         = ($vendor_officer3_email1 && $vendor_officer3_email2) ? $vendor_officer3_email1 . "@" . $vendor_officer3_email2 : "";

$vendor_officer4_name          = $_POST["vendor_officer4_name"];
$vendor_officer4_tel1          = $_POST["vendor_officer4_tel1"];
$vendor_officer4_tel2          = $_POST["vendor_officer4_tel2"];
$vendor_officer4_tel3          = $_POST["vendor_officer4_tel3"];
$vendor_officer4_tel           = ($vendor_officer4_tel2 && $vendor_officer4_tel3) ? $vendor_officer4_tel1 . "-" . $vendor_officer4_tel2 . "-" . $vendor_officer4_tel3 : "";
$vendor_officer4_mobile1       = $_POST["vendor_officer4_mobile1"];
$vendor_officer4_mobile2       = $_POST["vendor_officer4_mobile2"];
$vendor_officer4_mobile3       = $_POST["vendor_officer4_mobile3"];
$vendor_officer4_mobile        = ($vendor_officer4_mobile2 && $vendor_officer4_mobile3) ? $vendor_officer4_mobile1 . "-" . $vendor_officer4_mobile2 . "-" . $vendor_officer4_mobile3 : "";
$vendor_officer4_email1        = $_POST["vendor_officer4_email1"];
$vendor_officer4_email2        = $_POST["vendor_officer4_email2"];
$vendor_officer4_email         = ($vendor_officer4_email1 && $vendor_officer4_email2) ? $vendor_officer4_email1 . "@" . $vendor_officer4_email2 : "";

$vendor_md                     = $_POST["vendor_md"];
$vendor_etc                    = $_POST["vendor_etc"];
$is_use                        = $_POST["is_use"];


if($mode == "add")
{
	//Check Dup
	if(!$C_Users->checkDupID($login_id))
	{
		put_msg_and_back("이미 사용중인 아이디입니다.");
		exit;
	}else{
		$args = array();

		$args["login_id"]       = $login_id;
		$args["login_pw"]       = crypt($login_pw, DY_PASSWORD_SALT);

		$args["vendor_name"]                 = $vendor_name;
		$args["manage_group_idx"]            = $manage_group_idx;
		$args["vendor_grade"]                = ($vendor_grade) ? $vendor_grade : "E";
		$args["vendor_ceo_name"]             = $vendor_ceo_name;
		$args["vendor_license_number"]       = $vendor_license_no;
		$args["vendor_zipcode"]              = $vendor_zipcode;
		$args["vendor_addr1"]                = $vendor_addr1;
		$args["vendor_addr2"]                = $vendor_addr2;
		$args["vendor_fax"]                  = $vendor_fax;
		$args["vendor_startdate"]            = $vendor_startdate;
		$args["vendor_enddate"]              = $vendor_enddate;
		$args["vendor_license_file"]         = $vendor_license_file;
		$args["vendor_bank_account_number"]  = $vendor_bank_account_number;
		$args["vendor_bank_name"]            = $vendor_bank_name;
		$args["vendor_bank_holder_name"]     = $vendor_bank_holder_name;
		$args["vendor_bank_book_copy_file"]  = $vendor_bank_book_copy_file;
		$args["vendor_email_default"]        = $vendor_email_default;
		$args["vendor_email_account"]        = $vendor_email_account;
		$args["vendor_email_order"]          = $vendor_email_order;
		$args["vendor_use_charge"]           = ($vendor_use_charge) ? $vendor_use_charge : "N";
		$args["vendor_is_order_block"]       = ($vendor_is_order_block) ? $vendor_is_order_block : "N";

		$args["vendor_officer1_name"]        = $vendor_officer1_name;
		$args["vendor_officer1_tel"]         = $vendor_officer1_tel;
		$args["vendor_officer1_mobile"]      = $vendor_officer1_mobile;
		$args["vendor_officer1_email"]       = $vendor_officer1_email;

		$args["vendor_officer2_name"]        = $vendor_officer2_name;
		$args["vendor_officer2_tel"]         = $vendor_officer2_tel;
		$args["vendor_officer2_mobile"]      = $vendor_officer2_mobile;
		$args["vendor_officer2_email"]       = $vendor_officer2_email;

		$args["vendor_officer3_name"]        = $vendor_officer3_name;
		$args["vendor_officer3_tel"]         = $vendor_officer3_tel;
		$args["vendor_officer3_mobile"]      = $vendor_officer3_mobile;
		$args["vendor_officer3_email"]       = $vendor_officer3_email;

		$args["vendor_officer4_name"]        = $vendor_officer4_name;
		$args["vendor_officer4_tel"]         = $vendor_officer4_tel;
		$args["vendor_officer4_mobile"]      = $vendor_officer4_mobile;
		$args["vendor_officer4_email"]       = $vendor_officer4_email;

		$args["vendor_md"]                   = $vendor_md;
		$args["vendor_etc"]                  = $vendor_etc;
		$args["is_use"]                      = ($is_use) ? $is_use : "Y";
		$vendor_idx = $C_Vendor->insertVendor($args);

		//업로드 파일 Update
		if($vendor_license_file) {
			$argsFile = array();
			$argsFile["file_idx"] = $vendor_license_file;
			$argsFile["ref_table_idx"] = $vendor_idx;
			$tmp = $C_Files -> updateFileActive($argsFile);
		}
		if($vendor_bank_book_copy_file) {
			$argsFile = array();
			$argsFile["file_idx"] = $vendor_bank_book_copy_file;
			$argsFile["ref_table_idx"] = $vendor_idx;
			$tmp = $C_Files -> updateFileActive($argsFile);
		}

		$exec_script = "
			try{
				opener.Vendor.VendorListReload();
			}catch(e){
			}
			if('".$_POST["where"]."' == 'main_join'){
				alert('신청이 접수 되었습니다.');
				location.href='/';
			}
		";

		exec_script_and_close($exec_script);

		//go_replace("user_list.php");
	}
}elseif($mode == "mod" || $mode == "mod_self"){

	//내정보 수정일 경우 로그인된 IDX 와 비교
	if($mode == "mod_self")
	{
		if($vendor_idx != $GL_Member["member_idx"])
		{
			put_msg_and_back("잘못된 접근입니다.");
		}
	}

	$args = array();

	$args["idx"]            = $vendor_idx;
	if($login_pw) {
		$args["login_pw"] = crypt($login_pw, DY_PASSWORD_SALT);
	}

	$args["vendor_name"]                 = $vendor_name;
	$args["manage_group_idx"]            = $manage_group_idx;
	$args["vendor_grade"]                = $vendor_grade;
	$args["vendor_ceo_name"]             = $vendor_ceo_name;
	$args["vendor_license_number"]       = $vendor_license_no;
	$args["vendor_zipcode"]              = $vendor_zipcode;
	$args["vendor_addr1"]                = $vendor_addr1;
	$args["vendor_addr2"]                = $vendor_addr2;
	$args["vendor_fax"]                  = $vendor_fax;
	$args["vendor_startdate"]            = $vendor_startdate;
	$args["vendor_enddate"]              = $vendor_enddate;
	$args["vendor_license_file"]         = $vendor_license_file;
	$args["vendor_bank_account_number"]  = $vendor_bank_account_number;
	$args["vendor_bank_name"]            = $vendor_bank_name;
	$args["vendor_bank_holder_name"]     = $vendor_bank_holder_name;
	$args["vendor_bank_book_copy_file"]  = $vendor_bank_book_copy_file;
	$args["vendor_email_default"]        = $vendor_email_default;
	$args["vendor_email_account"]        = $vendor_email_account;
	$args["vendor_email_order"]          = $vendor_email_order;
	$args["vendor_use_charge"]           = $vendor_use_charge;
	$args["vendor_is_order_block"]       = $vendor_is_order_block;

	$args["vendor_officer1_name"]        = $vendor_officer1_name;
	$args["vendor_officer1_tel"]         = $vendor_officer1_tel;
	$args["vendor_officer1_mobile"]      = $vendor_officer1_mobile;
	$args["vendor_officer1_email"]       = $vendor_officer1_email;

	$args["vendor_officer2_name"]        = $vendor_officer2_name;
	$args["vendor_officer2_tel"]         = $vendor_officer2_tel;
	$args["vendor_officer2_mobile"]      = $vendor_officer2_mobile;
	$args["vendor_officer2_email"]       = $vendor_officer2_email;

	$args["vendor_officer3_name"]        = $vendor_officer3_name;
	$args["vendor_officer3_tel"]         = $vendor_officer3_tel;
	$args["vendor_officer3_mobile"]      = $vendor_officer3_mobile;
	$args["vendor_officer3_email"]       = $vendor_officer3_email;

	$args["vendor_officer4_name"]        = $vendor_officer4_name;
	$args["vendor_officer4_tel"]         = $vendor_officer4_tel;
	$args["vendor_officer4_mobile"]      = $vendor_officer4_mobile;
	$args["vendor_officer4_email"]       = $vendor_officer4_email;

	$args["vendor_md"]                   = $vendor_md;
	$args["vendor_etc"]                  = $vendor_etc;
	$args["is_use"]                      = $is_use;

	$C_Vendor->updateVendor($args);

	//내정보 수정일 경우
	if($mode == "mod_self") {
		//로그인 세션 Update
		$C_Login = new Login();
		$C_Login->setLoginSession($vendor_idx);
	}

	if($mode == "mod") {
		$exec_script = "
			opener.Vendor.VendorListReload();
		";
		exec_script_and_close($exec_script);
	}elseif($mode == "mod_self") {
		go_replace("myinfo.php");
	}else{
		go_replace("/");
	}

}elseif($mode == "id_check"){
	$rst = $C_Users->checkDupID($login_id);
	$response = array("result" => $rst);
	echo json_encode($response);
	exit;
}elseif($mode == "vendor_status_change"){

	$vendor_status = $_POST["vendor_status"];
	$vendor_status_msg = $_POST["vendor_status_msg"];

	$vendorInfo = $C_Vendor->getVendorData($vendor_idx);
	if($vendorInfo["vendor_status"] == "VENDOR_APPLY" || $vendorInfo["vendor_status"] == "VENDOR_REJECT")
	{
		put_msg_and_back("이미 승인 또는 반려된 상태입니다.");
	}else {

		$args = array();
		$args["vendor_idx"] = $vendor_idx;
		$args["vendor_status"] = $vendor_status;
		$args["vendor_status_msg"] = $vendor_status_msg;
		$rst = $C_Vendor->changeVendorStatus($args);

		$exec_script = "
			opener.Vendor.VendorListReload();
		";

		exec_script_and_close($exec_script);
	}
}

?>