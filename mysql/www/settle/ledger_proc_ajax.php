<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 관련 Process
 */

//Page Info
$pageMenuIdx = 134;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_SETTLE = new Settle();

if($mode == "ledger_memo") {

	$ledger_memo_idx = $_POST["ledger_memo_idx"];
	$target_idx      = $_POST["target_idx"];
	$ledger_type     = $_POST["ledger_type"];
	$ledger_date     = $_POST["ledger_date"];
	$ledger_memo     = $_POST["ledger_memo"];

	$rst = $C_SETTLE->updateLedgerMemo($ledger_memo_idx, $ledger_type, $target_idx, $ledger_date, $ledger_memo);

	if (!$ledger_memo_idx) {
		$ledger_memo_idx = $rst;
	}

	$response["result"] = true;
	$response["data"]   = $ledger_memo_idx;
	$response["date"]   = $ledger_date;
}elseif($mode == "ledger_memo2") {

	$ledger_idx   = $_POST["ledger_idx"];
	$ledger_memo  = $_POST["ledger_memo"];
	$ledger_title = $_POST["ledger_title"];
	$amount       = $_POST["amount"];
	$amount_name  = $_POST["amount_name"];

	$rst = $C_SETTLE->updateLedgerMemo2($ledger_idx, $ledger_memo, $ledger_title, $amount_name, $amount);


	$response["result"] = true;
	$response["data"] = "";
	$response["ledger_idx"] = $ledger_idx;
}elseif($mode == "get_ledger_memo"){
	$ledger_memo_idx = $_POST["ledger_memo_idx"];

	$ledger_memo = $C_SETTLE->getLedgerMemo($ledger_memo_idx);

	$response["result"] = true;
	$response["data"] = nl2br($ledger_memo);
}elseif($mode == "get_ledger_memo2"){
	$ledger_idx = $_POST["ledger_idx"];

	$ledger_memo = $C_SETTLE->getLedgerMemo2($ledger_idx);

	$response["result"] = true;
	$response["data"] = nl2br($ledger_memo);
}elseif($mode == "ledger_delete"){

	$ledger_idx = $_POST["ledger_idx"];

	$rst = $C_SETTLE->deleteLedgerDetail($ledger_idx);

	$response["result"] = true;

}elseif($mode == "ledger_send_email"){
	//이메일 보내기
	$file_idx      = $_POST["file_idx"];
	$target_idx    = $_POST["target_idx"];
	$email_title   = $_POST["email_title"];
	$email_content = $_POST["email_content"];
	$target_email  = $_POST["target_email"];

	$target_email = str_replace(",", ";", $target_email);

	$self_name = $GL_Member["member_name"];
	$self_email = $GL_Member["member_email"];

	//메일 발송 전 메일로그 먼저 생성
	$C_Settle = new Settle();
	$email_idx = $C_Settle -> insertLedgerEmailSendLog($file_idx, $target_idx, $target_email, $email_title, $email_content, $self_email);

	/**
	 * 단축 URL 생성
	 * /proc/_stock_order_xls_down.php?stock_order_idx=*&stock_order_email_idx=*
	 */
	$shorty = new Shorty();

	$shorty->set_chars(DY_SHORTY_CHARS);
	$shorty->set_salt(DY_SHORTY_SALT);
	$shorty->set_padding(DY_SHORTY_PADDING);

	$stock_order_document_short_url = $shorty->returnUrl('/proc/_ledger_download_xls.php?target_idx='.$target_idx.'&file_idx='.$file_idx.'&email_idx='.$email_idx);

	$email_content .= '
		
		<a href="'.$stock_order_document_short_url.'">원장 파일 다운받기</a>
		
	';

	//공급처 Mail Send!!
	$rst = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $target_email, $email_title, $email_content, 1, null, "", "");

	//정상 메일 발송 시 로그 Insert
	if($rst){

		//이메일 로그 Update :: is_del => N
		$C_Settle -> updateLedgerEmailSendLogIsDel($email_idx);

		//발송자도 같은 내용 받기
		$rst2 = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $self_email, "[거래처별원장 이메일 확인]".$email_title, $email_content, 2, null, "", "");


		$response["result"] = true;
		$response["msg"] = "발송되었습니다.";
	}else{
		$response["msg"] = "메일발송에 실패하였습니다.";
		$response["data"] = error_get_last();
		$response["target_email"] = $target_email;
		$response["email_title"] = $email_title;
		$response["email_content"] = $email_content;
	}
}elseif($mode == "ledger_send_email_multi"){
	//이메일 보내기
	$ledger_type   = $_POST["ledger_type"];
	$target_list   = $_POST["target_list"];
	$email_title   = $_POST["email_title"];
	$email_content = $_POST["email_content"];

	$send_count = 0;

	foreach($target_list as $key => $target_set) {

		$target_set = explode("|", $target_set);
		$target_idx = $target_set[0];
		$file_idx = $target_set[1];

		if($ledger_type == "LEDGER_PURCHASE"){
			$C_Supplier = new Supplier();
			$_view = $C_Supplier -> getSupplierData($target_idx);
			$target_email = $_view["supplier_email_order"];
		}else{
			$C_Vendor = new Vendor();
			$_view = $C_Vendor->getVendorData($target_idx);
			$target_email = $_view["vendor_email_order"];
		}

		$target_email = str_replace(",", ";", $target_email);

		$self_name  = $GL_Member["member_name"];
		$self_email = $GL_Member["member_email"];

		//메일 발송 전 메일로그 먼저 생성
		$C_Settle  = new Settle();
		$email_idx = $C_Settle->insertLedgerEmailSendLog($file_idx, $target_idx, $target_email, $email_title, $email_content, $self_email);

		/**
		 * 단축 URL 생성
		 * /proc/_stock_order_xls_down.php?stock_order_idx=*&stock_order_email_idx=*
		 */
		$shorty = new Shorty();

		$shorty->set_chars(DY_SHORTY_CHARS);
		$shorty->set_salt(DY_SHORTY_SALT);
		$shorty->set_padding(DY_SHORTY_PADDING);

		$stock_order_document_short_url = $shorty->returnUrl('/proc/_ledger_download_xls.php?target_idx=' . $target_idx . '&file_idx=' . $file_idx . '&email_idx=' . $email_idx);

		$email_content_new = $email_content . '
			
			<a href="' . $stock_order_document_short_url . '">원장 파일 다운받기</a>
			
		';

		//공급처 Mail Send!!
		$rst = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $target_email, $email_title, $email_content_new, 1, null, "", "");

		//정상 메일 발송 시 로그 Insert
		if ($rst) {

			//이메일 로그 Update :: is_del => N
			$C_Settle->updateLedgerEmailSendLogIsDel($email_idx);

			//발송자도 같은 내용 받기
			$rst2 = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $self_email, "[거래처별원장 이메일 확인]" . $email_title, $email_content_new, 2, null, "", "");


			$send_count++;
		} else {

		}
	}

	$response["result"] = true;
	$response["msg"]    = $send_count."건의 메일이 발송되었습니다.";
	$response["data"]   = error_get_last();
}

echo json_encode($response);
?>