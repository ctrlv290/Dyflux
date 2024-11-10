<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 발주 관련 Process
 */

//Page Info
$pageMenuIdx = 184;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "get_supplier_info"){
	//공급처 정보 반환

	$supplier_idx = $_POST["supplier_idx"];

	$C_Supplier = new Supplier();

	$rst = $C_Supplier->getUseSupplierData($supplier_idx);
	if($rst){
		$response["result"] = true;
		$response["data"] = $rst;
	}else{
		$response["msg"] = "사용할 수 없는 공급처 입니다.";
	}
}elseif($mode == "get_stock_order_officer_info"){
	//사이트 담당자 정보 반환

	$officer_no = $_POST["officer_no"];

	$C_SiteInfo = new SiteInfo();

	//사이트 담당자 리스트 얻기
	$_list = $C_SiteInfo->getOfficerList();

	$key = array_search($officer_no, array_column($_list, 'no'));

	if($key !== false){
		$response["result"] = true;
		$response["data"] = $_list[$key];
	}else{
		$response["msg"] = "없는 담당자입니다.";
	}

}elseif($mode == "create_stock_order_document"){
	//발주서 파일 생성

	$stock_order_idx = $_POST["stock_order_idx"];

}elseif($mode == "get_supplier_officer_email"){

	$supplier_idx = $_POST["supplier_idx"];

	$C_Supplier = new Supplier();

	$_view = $C_Supplier -> getSupplierData($supplier_idx);

	$_list = array();
	if($_view){
		/*
		if($_view["supplier_officer1_email"]){
			$_list[] = $_view["supplier_officer1_email"];
		}
		if($_view["supplier_officer2_email"]){
			$_list[] = $_view["supplier_officer2_email"];
		}
		if($_view["supplier_officer3_email"]){
			$_list[] = $_view["supplier_officer3_email"];
		}
		if($_view["supplier_officer4_email"]){
			$_list[] = $_view["supplier_officer4_email"];
		}
		*/

		$email_order = $_view["supplier_email_order"];
		$_list = explode(",", $email_order);


		$response["result"] = true;
		$response["data"] = $_list;

	}else{
		$response["msg"] = "공급처 정보가 없습니다.";
	}

}elseif($mode == "send_stock_order_email"){
	//이메일 보내기
	$stock_order_idx                = $_POST["stock_order_idx"];
	$stock_order_file_idx           = $_POST["stock_order_file_idx"];
	$supplier_idx                   = $_POST["supplier_idx"];
	//$stock_order_document_short_url = $_POST["stock_order_document_short_url"];
	$email_title                    = $_POST["email_title"];
	$email_content                  = $_POST["email_content"];
	$supplier_email                 = $_POST["supplier_email"];

	$self_name = $GL_Member["member_name"];
	$self_email = $GL_Member["member_email"];

	//메일 발송 전 메일로그 먼저 생성
	$C_Stock = new Stock();
	$stock_order_email_idx = $C_Stock -> insertStockOrderEmailSendLog($stock_order_idx, $stock_order_file_idx, $supplier_idx, $supplier_email, $email_title, $email_content, $self_email);

	/**
	 * 단축 URL 생성
	 * /proc/_stock_order_xls_down.php?stock_order_idx=*&stock_order_email_idx=*
	 */
	$shorty = new Shorty();

	$shorty->set_chars(DY_SHORTY_CHARS);
	$shorty->set_salt(DY_SHORTY_SALT);
	$shorty->set_padding(DY_SHORTY_PADDING);

	$stock_order_document_short_url = $shorty->returnUrl('/proc/_stock_order_xls_down.php?stock_order_idx='.$stock_order_idx.'&stock_order_file_idx='.$stock_order_file_idx.'&stock_order_email_idx='.$stock_order_email_idx);

	$email_content .= '
		
		<a href="'.$stock_order_document_short_url.'">발주서 파일 다운받기</a>
		
	';

	//공급처 Mail Send!!
	$rst = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $supplier_email, $email_title, $email_content, 2, null, "", "");

	//정상 메일 발송 시 로그 Insert
	if($rst){

		//이메일 로그 Update :: is_del => N
		$C_Stock -> updateStockOrderEmailSendLogIsDel($stock_order_email_idx);

		//발송자도 같은 내용 받기
		$rst2 = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $self_email, "[발주 이메일 확인]".$email_title, $email_content, 2, null, "", "");


		$response["result"] = true;
	}else{
		$response["msg"] = "메일발송에 실패하였습니다.";
	}

}elseif($mode == "stock_order_place_order"){
	//발주하기
	$stock_order_idx                = $_POST["stock_order_idx"];

	$C_Stock = new Stock();

	//발주상태 확인
	$_view = $C_Stock -> getStockOrderData($stock_order_idx);

	if($_view) {
		//미발주 상태일 경우에만 발주 가능
		if($_view["stock_order_is_order"] == "N") {
			$rst = $C_Stock->placeStockOrder($stock_order_idx);
			$response["result"] = true;
		}elseif($_view["stock_order_is_order"] == "Y") {
			$response["msg"] = "이미 발주된 발주입니다.";
		}elseif($_view["stock_order_is_order"] == "C") {
			$response["msg"] = "이미 발주취소된 발주입니다.";
		}elseif($_view["stock_order_is_order"] == "T") {
			$response["msg"] = "이미 발주된 발주입니다.";
		}
	}else{
		$response["msg"] = "발주서 정보 확인에 실패하였습니다.";
	}

}elseif($mode == "stock_order_cancel_order"){
	//발주취소
	$stock_order_idx                = $_POST["stock_order_idx"];

	$C_Stock = new Stock();

	//발주상태 확인
	$_view = $C_Stock -> getStockOrderData($stock_order_idx);

	if($_view) {
		//발주 상태일 경우에만 발주 가능
		if($_view["stock_order_is_order"] == "Y") {
			$rst = $C_Stock->cancelStockOrder($stock_order_idx);
			$response["result"] = true;
		}elseif($_view["stock_order_is_order"] == "T") {
			$response["msg"] = "전체입고 또는 부분입고 된 발주입니다.\n취소가 불가능합니다.";
		}elseif($_view["stock_order_is_order"] == "N") {
			$response["msg"] = "아직 발주되지 않은 발주입니다.";
		}elseif($_view["stock_order_is_order"] == "C") {
			$response["msg"] = "이미 발주취소된 발주입니다.";
		}
	}else{
		$response["msg"] = "발주서 정보 확인에 실패하였습니다.";
	}
}

echo json_encode($response);
?>