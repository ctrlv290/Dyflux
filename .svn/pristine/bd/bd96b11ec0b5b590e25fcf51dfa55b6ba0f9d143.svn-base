<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 49;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Supplier = new Supplier();
$C_Files = new Files();

$mode                             = $_POST["mode"];
$supplier_idx                     = $_POST["supplier_idx"];
$login_id                         = $_POST["login_id"];
$login_pw                         = $_POST["login_pw"];
$supplier_name                    = $_POST["supplier_name"];
$manage_group_idx                 = $_POST["manage_group_idx"];
$supplier_ceo_name                = $_POST["supplier_ceo_name"];
$supplier_license_no              = $_POST["supplier_license_no1"] . "-" . $_POST["supplier_license_no2"] . "-" . $_POST["supplier_license_no3"];
$supplier_zipcode                 = $_POST["supplier_zipcode"];
$supplier_addr1                   = $_POST["supplier_addr1"];
$supplier_addr2                   = $_POST["supplier_addr2"];
$supplier_fax                     = $_POST["supplier_fax"];
$supplier_startdate               = $_POST["supplier_startdate"];
$supplier_enddate                 = $_POST["supplier_enddate"];
$supplier_license_file            = $_POST["supplier_license_file"];
$supplier_bank_account_number     = $_POST["supplier_bank_account_number"];
$supplier_bank_name               = $_POST["supplier_bank_name"];
$supplier_bank_holder_name        = $_POST["supplier_bank_holder_name"];
$supplier_bank_book_copy_file     = $_POST["supplier_bank_book_copy_file"];
$supplier_email_default           = $_POST["supplier_email_default"];
$supplier_email_account           = $_POST["supplier_email_account"];
$supplier_email_order             = $_POST["supplier_email_order"];
$supplier_use_prepay              = $_POST["supplier_use_prepay"];
$supplier_payment_type            = $_POST["supplier_payment_type"];

$supplier_officer1_name          = $_POST["supplier_officer1_name"];
$supplier_officer1_tel1          = $_POST["supplier_officer1_tel1"];
$supplier_officer1_tel2          = $_POST["supplier_officer1_tel2"];
$supplier_officer1_tel3          = $_POST["supplier_officer1_tel3"];
$supplier_officer1_tel           = ($supplier_officer1_tel2 && $supplier_officer1_tel3) ? $supplier_officer1_tel1 . "-" . $supplier_officer1_tel2 . "-" . $supplier_officer1_tel3 : "";
$supplier_officer1_mobile1       = $_POST["supplier_officer1_mobile1"];
$supplier_officer1_mobile2       = $_POST["supplier_officer1_mobile2"];
$supplier_officer1_mobile3       = $_POST["supplier_officer1_mobile3"];
$supplier_officer1_mobile        = $supplier_officer1_mobile1 . "-" . $supplier_officer1_mobile2 . "-" . $supplier_officer1_mobile3;
$supplier_officer1_email1        = $_POST["supplier_officer1_email1"];
$supplier_officer1_email2        = $_POST["supplier_officer1_email2"];
$supplier_officer1_email         = ($supplier_officer1_email1 && $supplier_officer1_email2) ? $supplier_officer1_email1 . "@" . $supplier_officer1_email2 : "";

$supplier_officer2_name          = $_POST["supplier_officer2_name"];
$supplier_officer2_tel1          = $_POST["supplier_officer2_tel1"];
$supplier_officer2_tel2          = $_POST["supplier_officer2_tel2"];
$supplier_officer2_tel3          = $_POST["supplier_officer2_tel3"];
$supplier_officer2_tel           = ($supplier_officer2_tel2 && $supplier_officer2_tel3) ? $supplier_officer2_tel1 . "-" . $supplier_officer2_tel2 . "-" . $supplier_officer2_tel3 : "";
$supplier_officer2_mobile1       = $_POST["supplier_officer2_mobile1"];
$supplier_officer2_mobile2       = $_POST["supplier_officer2_mobile2"];
$supplier_officer2_mobile3       = $_POST["supplier_officer2_mobile3"];
$supplier_officer2_mobile        = ($supplier_officer2_mobile2 && $supplier_officer2_mobile3) ? $supplier_officer2_mobile1 . "-" . $supplier_officer2_mobile2 . "-" . $supplier_officer2_mobile3 : "";
$supplier_officer2_email1        = $_POST["supplier_officer2_email1"];
$supplier_officer2_email2        = $_POST["supplier_officer2_email2"];
$supplier_officer2_email         = ($supplier_officer2_email1 && $supplier_officer2_email2) ? $supplier_officer2_email1 . "@" . $supplier_officer2_email2 : "";

$supplier_officer3_name          = $_POST["supplier_officer3_name"];
$supplier_officer3_tel1          = $_POST["supplier_officer3_tel1"];
$supplier_officer3_tel2          = $_POST["supplier_officer3_tel2"];
$supplier_officer3_tel3          = $_POST["supplier_officer3_tel3"];
$supplier_officer3_tel           = ($supplier_officer3_tel2 && $supplier_officer3_tel3) ? $supplier_officer3_tel1 . "-" . $supplier_officer3_tel2 . "-" . $supplier_officer3_tel3 : "";
$supplier_officer3_mobile1       = $_POST["supplier_officer3_mobile1"];
$supplier_officer3_mobile2       = $_POST["supplier_officer3_mobile2"];
$supplier_officer3_mobile3       = $_POST["supplier_officer3_mobile3"];
$supplier_officer3_mobile        = ($supplier_officer3_mobile2 && $supplier_officer3_mobile3) ? $supplier_officer3_mobile1 . "-" . $supplier_officer3_mobile2 . "-" . $supplier_officer3_mobile3 : "";
$supplier_officer3_email1        = $_POST["supplier_officer3_email1"];
$supplier_officer3_email2        = $_POST["supplier_officer3_email2"];
$supplier_officer3_email         = ($supplier_officer3_email1 && $supplier_officer3_email2) ? $supplier_officer3_email1 . "@" . $supplier_officer3_email2 : "";

$supplier_officer4_name          = $_POST["supplier_officer4_name"];
$supplier_officer4_tel1          = $_POST["supplier_officer4_tel1"];
$supplier_officer4_tel2          = $_POST["supplier_officer4_tel2"];
$supplier_officer4_tel3          = $_POST["supplier_officer4_tel3"];
$supplier_officer4_tel           = ($supplier_officer4_tel2 && $supplier_officer4_tel3) ? $supplier_officer4_tel1 . "-" . $supplier_officer4_tel2 . "-" . $supplier_officer4_tel3 : "";
$supplier_officer4_mobile1       = $_POST["supplier_officer4_mobile1"];
$supplier_officer4_mobile2       = $_POST["supplier_officer4_mobile2"];
$supplier_officer4_mobile3       = $_POST["supplier_officer4_mobile3"];
$supplier_officer4_mobile        = ($supplier_officer4_mobile2 && $supplier_officer4_mobile3) ? $supplier_officer4_mobile1 . "-" . $supplier_officer4_mobile2 . "-" . $supplier_officer4_mobile3 : "";
$supplier_officer4_email1        = $_POST["supplier_officer4_email1"];
$supplier_officer4_email2        = $_POST["supplier_officer4_email2"];
$supplier_officer4_email         = ($supplier_officer4_email1 && $supplier_officer4_email2) ? $supplier_officer4_email1 . "@" . $supplier_officer4_email2 : "";

$supplier_md                     = $_POST["supplier_md"];
$supplier_etc                    = $_POST["supplier_etc"];
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

		$args["supplier_name"]                 = $supplier_name;
		$args["manage_group_idx"]              = $manage_group_idx;
		$args["supplier_ceo_name"]             = $supplier_ceo_name;
		$args["supplier_license_number"]       = $supplier_license_no;
		$args["supplier_zipcode"]              = $supplier_zipcode;
		$args["supplier_addr1"]                = $supplier_addr1;
		$args["supplier_addr2"]                = $supplier_addr2;
		$args["supplier_fax"]                  = $supplier_fax;
		$args["supplier_startdate"]            = $supplier_startdate;
		$args["supplier_enddate"]              = $supplier_enddate;
		$args["supplier_license_file"]         = $supplier_license_file;
		$args["supplier_bank_account_number"]  = $supplier_bank_account_number;
		$args["supplier_bank_name"]            = $supplier_bank_name;
		$args["supplier_bank_holder_name"]     = $supplier_bank_holder_name;
		$args["supplier_bank_book_copy_file"]  = $supplier_bank_book_copy_file;
		$args["supplier_email_default"]        = $supplier_email_default;
		$args["supplier_email_account"]        = $supplier_email_account;
		$args["supplier_email_order"]          = $supplier_email_order;
		$args["supplier_use_prepay"]           = $supplier_use_prepay;
        $args["supplier_payment_type"]         = $supplier_payment_type;

		$args["supplier_officer1_name"]        = $supplier_officer1_name;
		$args["supplier_officer1_tel"]         = $supplier_officer1_tel;
		$args["supplier_officer1_mobile"]      = $supplier_officer1_mobile;
		$args["supplier_officer1_email"]       = $supplier_officer1_email;

		$args["supplier_officer2_name"]        = $supplier_officer2_name;
		$args["supplier_officer2_tel"]         = $supplier_officer2_tel;
		$args["supplier_officer2_mobile"]      = $supplier_officer2_mobile;
		$args["supplier_officer2_email"]       = $supplier_officer2_email;

		$args["supplier_officer3_name"]        = $supplier_officer3_name;
		$args["supplier_officer3_tel"]         = $supplier_officer3_tel;
		$args["supplier_officer3_mobile"]      = $supplier_officer3_mobile;
		$args["supplier_officer3_email"]       = $supplier_officer3_email;

		$args["supplier_officer4_name"]        = $supplier_officer4_name;
		$args["supplier_officer4_tel"]         = $supplier_officer4_tel;
		$args["supplier_officer4_mobile"]      = $supplier_officer4_mobile;
		$args["supplier_officer4_email"]       = $supplier_officer4_email;

		$args["supplier_md"]                   = $supplier_md;
		$args["supplier_etc"]                  = $supplier_etc;
		$args["is_use"]                        = $is_use;

		$supplier_idx = $C_Supplier->insertSupplier($args);

		//업로드 파일 Update
		if($supplier_license_file) {
			$argsFile = array();
			$argsFile["file_idx"] = $supplier_license_file;
			$argsFile["ref_table_idx"] = $supplier_idx;
			$tmp = $C_Files -> updateFileActive($argsFile);
		}
		if($supplier_bank_book_copy_file) {
			$argsFile = array();
			$argsFile["file_idx"] = $supplier_bank_book_copy_file;
			$argsFile["ref_table_idx"] = $supplier_idx;
			$tmp = $C_Files -> updateFileActive($argsFile);
		}

		$exec_script = "
			try{
				opener.Supplier.SupplierListReload();
			}catch(e){
			}
			if(window.name == 'main_join'){
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
		if($supplier_idx != $GL_Member["member_idx"])
		{
			put_msg_and_back("잘못된 접근입니다.");
		}
	}


	$args = array();

	$args["idx"]            = $supplier_idx;
	if($login_pw) {
		$args["login_pw"] = crypt($login_pw, DY_PASSWORD_SALT);
	}

	$args["supplier_name"]                 = $supplier_name;
	$args["manage_group_idx"]              = $manage_group_idx;
	$args["supplier_ceo_name"]             = $supplier_ceo_name;
	$args["supplier_license_number"]       = $supplier_license_no;
	$args["supplier_zipcode"]              = $supplier_zipcode;
	$args["supplier_addr1"]                = $supplier_addr1;
	$args["supplier_addr2"]                = $supplier_addr2;
	$args["supplier_fax"]                  = $supplier_fax;
	$args["supplier_startdate"]            = $supplier_startdate;
	$args["supplier_enddate"]              = $supplier_enddate;
	$args["supplier_license_file"]         = $supplier_license_file;
	$args["supplier_bank_account_number"]  = $supplier_bank_account_number;
	$args["supplier_bank_name"]            = $supplier_bank_name;
	$args["supplier_bank_holder_name"]     = $supplier_bank_holder_name;
	$args["supplier_bank_book_copy_file"]  = $supplier_bank_book_copy_file;
	$args["supplier_email_default"]        = $supplier_email_default;
	$args["supplier_email_account"]        = $supplier_email_account;
	$args["supplier_email_order"]          = $supplier_email_order;
	$args["supplier_use_prepay"]           = $supplier_use_prepay;
    $args["supplier_payment_type"]         = $supplier_payment_type;

	$args["supplier_officer1_name"]        = $supplier_officer1_name;
	$args["supplier_officer1_tel"]         = $supplier_officer1_tel;
	$args["supplier_officer1_mobile"]      = $supplier_officer1_mobile;
	$args["supplier_officer1_email"]       = $supplier_officer1_email;

	$args["supplier_officer2_name"]        = $supplier_officer2_name;
	$args["supplier_officer2_tel"]         = $supplier_officer2_tel;
	$args["supplier_officer2_mobile"]      = $supplier_officer2_mobile;
	$args["supplier_officer2_email"]       = $supplier_officer2_email;

	$args["supplier_officer3_name"]        = $supplier_officer3_name;
	$args["supplier_officer3_tel"]         = $supplier_officer3_tel;
	$args["supplier_officer3_mobile"]      = $supplier_officer3_mobile;
	$args["supplier_officer3_email"]       = $supplier_officer3_email;

	$args["supplier_officer4_name"]        = $supplier_officer4_name;
	$args["supplier_officer4_tel"]         = $supplier_officer4_tel;
	$args["supplier_officer4_mobile"]      = $supplier_officer4_mobile;
	$args["supplier_officer4_email"]       = $supplier_officer4_email;

	$args["supplier_md"]                   = $supplier_md;
	$args["supplier_etc"]                  = $supplier_etc;
	$args["is_use"]                      = $is_use;

	$C_Supplier->updateSupplier($args);

	//내정보 수정일 경우
	if($mode == "mod_self") {
		//로그인 세션 Update
		$C_Login = new Login();
		$C_Login->setLoginSession($supplier_idx);
	}

	if($mode == "mod") {
		$exec_script = "
			opener.Supplier.SupplierListReload();
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
}

?>