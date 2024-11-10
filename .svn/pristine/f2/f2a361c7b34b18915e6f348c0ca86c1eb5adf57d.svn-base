<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 페이지 -주문 생성 관련 Process
 */

//Page Info
$pageMenuIdx = 206;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode            = $_POST["mode"];
$address_idx     = $_POST["address_idx"];
$address_name    = $_POST["address_name"];
$address_tel_num = $_POST["address_tel_num"];
$address_hp_num  = $_POST["address_hp_num"];
$address_zipcode = $_POST["address_zipcode"];
$address_address = $_POST["address_address"];

$C_CS = new CS();

if($mode == "addressbook_list"){
	$data = $C_CS -> getAddressBookList();
	$response["result"] = true;

	if($data) {
		$response["data"] = $data;
	}else{
		$response["data"] = array();
	}
}elseif($mode == "addressbook_add"){

	$_address_idx = $C_CS -> addAddressBook($address_name, $address_tel_num, $address_hp_num, $address_zipcode, $address_address);

	if($_address_idx){
		$response["result"] = true;
	}

}elseif($mode == "addressbook_update"){

	$_address_idx = $C_CS -> updateAddressBook($address_idx, $address_name, $address_tel_num, $address_hp_num, $address_zipcode, $address_address);

	if($_address_idx){
		$response["result"] = true;
	}

}elseif($mode == "addressbook_delete"){
	$_address_idx = $C_CS -> deleteAddressBook($address_idx);

	$response["result"] = true;
}

echo json_encode($response);
?>