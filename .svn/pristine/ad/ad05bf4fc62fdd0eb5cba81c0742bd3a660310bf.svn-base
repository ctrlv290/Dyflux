<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사이트 정보관리 관련 Process
 */

//Page Info
$pageMenuIdx = 187;
//Init
include "../_init_.php";
//print_r($_POST);

$C_SiteInfo = new SiteInfo();

$mode                    = $_POST["mode"];

if($mode == "mod") {

	$site_name        = $_POST["site_name"];
	$ceo_name         = $_POST["ceo_name"];
	$license_no       = $_POST["license_no1"] . "-" . $_POST["license_no2"] . "-" . $_POST["license_no3"];
	$zipcode          = $_POST["zipcode"];
	$addr1            = $_POST["addr1"];
	$addr2            = $_POST["addr2"];
	$fax              = $_POST["fax"];

	$email_default    = $_POST["email_default"];
	$email_account    = $_POST["email_account"];
	$email_order      = $_POST["email_order"];

	$invoice_name     = $_POST["invoice_name"];
	$invoice_addr     = $_POST["invoice_addr"];
	$invoice_tel1     = $_POST["invoice_tel1"];
	$invoice_tel2     = $_POST["invoice_tel2"];
	$invoice_tel3     = $_POST["invoice_tel3"];
	$invoice_tel      = ($invoice_tel2 && $invoice_tel3) ? $invoice_tel1 . "-" . $invoice_tel2 . "-" . $invoice_tel3 : "";

	$officer1_name    = $_POST["officer1_name"];
	$officer1_tel1    = $_POST["officer1_tel1"];
	$officer1_tel2    = $_POST["officer1_tel2"];
	$officer1_tel3    = $_POST["officer1_tel3"];
	$officer1_tel     = ($officer1_tel2 && $officer1_tel3) ? $officer1_tel1 . "-" . $officer1_tel2 . "-" . $officer1_tel3 : "";
	$officer1_mobile1 = $_POST["officer1_mobile1"];
	$officer1_mobile2 = $_POST["officer1_mobile2"];
	$officer1_mobile3 = $_POST["officer1_mobile3"];
	$officer1_mobile  = $officer1_mobile1 . "-" . $officer1_mobile2 . "-" . $officer1_mobile3;
	$officer1_email1  = $_POST["officer1_email1"];
	$officer1_email2  = $_POST["officer1_email2"];
	$officer1_email   = ($officer1_email1 && $officer1_email2) ? $officer1_email1 . "@" . $officer1_email2 : "";

	$officer2_name    = $_POST["officer2_name"];
	$officer2_tel1    = $_POST["officer2_tel1"];
	$officer2_tel2    = $_POST["officer2_tel2"];
	$officer2_tel3    = $_POST["officer2_tel3"];
	$officer2_tel     = ($officer2_tel2 && $officer2_tel3) ? $officer2_tel1 . "-" . $officer2_tel2 . "-" . $officer2_tel3 : "";
	$officer2_mobile1 = $_POST["officer2_mobile1"];
	$officer2_mobile2 = $_POST["officer2_mobile2"];
	$officer2_mobile3 = $_POST["officer2_mobile3"];
	$officer2_mobile  = ($officer2_mobile2 && $officer2_mobile3) ? $officer2_mobile1 . "-" . $officer2_mobile2 . "-" . $officer2_mobile3 : "";
	$officer2_email1  = $_POST["officer2_email1"];
	$officer2_email2  = $_POST["officer2_email2"];
	$officer2_email   = ($officer2_email1 && $officer2_email2) ? $officer2_email1 . "@" . $officer2_email2 : "";

	$officer3_name    = $_POST["officer3_name"];
	$officer3_tel1    = $_POST["officer3_tel1"];
	$officer3_tel2    = $_POST["officer3_tel2"];
	$officer3_tel3    = $_POST["officer3_tel3"];
	$officer3_tel     = ($officer3_tel2 && $officer3_tel3) ? $officer3_tel1 . "-" . $officer3_tel2 . "-" . $officer3_tel3 : "";
	$officer3_mobile1 = $_POST["officer3_mobile1"];
	$officer3_mobile2 = $_POST["officer3_mobile2"];
	$officer3_mobile3 = $_POST["officer3_mobile3"];
	$officer3_mobile  = ($officer3_mobile2 && $officer3_mobile3) ? $officer3_mobile1 . "-" . $officer3_mobile2 . "-" . $officer3_mobile3 : "";
	$officer3_email1  = $_POST["officer3_email1"];
	$officer3_email2  = $_POST["officer3_email2"];
	$officer3_email   = ($officer3_email1 && $officer3_email2) ? $officer3_email1 . "@" . $officer3_email2 : "";

	$officer4_name    = $_POST["officer4_name"];
	$officer4_tel1    = $_POST["officer4_tel1"];
	$officer4_tel2    = $_POST["officer4_tel2"];
	$officer4_tel3    = $_POST["officer4_tel3"];
	$officer4_tel     = ($officer4_tel2 && $officer4_tel3) ? $officer4_tel1 . "-" . $officer4_tel2 . "-" . $officer4_tel3 : "";
	$officer4_mobile1 = $_POST["officer4_mobile1"];
	$officer4_mobile2 = $_POST["officer4_mobile2"];
	$officer4_mobile3 = $_POST["officer4_mobile3"];
	$officer4_mobile  = ($officer4_mobile2 && $officer4_mobile3) ? $officer4_mobile1 . "-" . $officer4_mobile2 . "-" . $officer4_mobile3 : "";
	$officer4_email1  = $_POST["officer4_email1"];
	$officer4_email2  = $_POST["officer4_email2"];
	$officer4_email   = ($officer4_email1 && $officer4_email2) ? $officer4_email1 . "@" . $officer4_email2 : "";

	$md               = $_POST["md"];
	$etc              = $_POST["etc"];
	$is_use           = $_POST["is_use"];

	$args                    = array();
	$args["site_name"]       = $site_name;
	$args["ceo_name"]        = $ceo_name;
	$args["license_no"]      = $license_no;
	$args["zipcode"]         = $zipcode;
	$args["addr1"]           = $addr1;
	$args["addr2"]           = $addr2;
	$args["fax"]             = $fax;
	$args["email_default"]   = $email_default;
	$args["email_account"]   = $email_account;
	$args["email_order"]     = $email_order;
	$args["invoice_name"]    = $invoice_name;
	$args["invoice_tel"]     = $invoice_tel;
	$args["invoice_addr"]    = $invoice_addr;
	$args["officer1_name"]   = $officer1_name;
	$args["officer1_tel"]    = $officer1_tel;
	$args["officer1_mobile"] = $officer1_mobile;
	$args["officer1_email"]  = $officer1_email;
	$args["officer2_name"]   = $officer2_name;
	$args["officer2_tel"]    = $officer2_tel;
	$args["officer2_mobile"] = $officer2_mobile;
	$args["officer2_email"]  = $officer2_email;
	$args["officer3_name"]   = $officer3_name;
	$args["officer3_tel"]    = $officer3_tel;
	$args["officer3_mobile"] = $officer3_mobile;
	$args["officer3_email"]  = $officer3_email;
	$args["officer4_name"]   = $officer4_name;
	$args["officer4_tel"]    = $officer4_tel;
	$args["officer4_mobile"] = $officer4_mobile;
	$args["officer4_email"]  = $officer4_email;
	$args["officer5_name"]   = $officer5_name;
	$args["officer5_tel"]    = $officer5_tel;
	$args["officer5_mobile"] = $officer5_mobile;
	$args["officer5_email"]  = $officer5_email;
	$args["md"]              = $md;
	$args["etc"]             = $etc;

	$rst = $C_SiteInfo->updateSiteInfo($args);

	put_msg_and_go("저장되었습니다.", "site_info.php");
}

?>