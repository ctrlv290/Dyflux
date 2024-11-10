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
if($mode == "send_order_download_email"){
	//이메일 보내기
	$order_download_file_idx        = $_POST["order_download_file_idx"];
	$supplier_idx                   = $_POST["supplier_idx"];
	//$stock_order_document_short_url = $_POST["stock_order_document_short_url"];
	$email_title                    = $_POST["email_title"];
	$email_content                  = $_POST["email_content"];
	$supplier_email                 = $_POST["supplier_email"];

	$supplier_email = str_replace(",", ";", $supplier_email);

	$self_name = $GL_Member["member_name"];
	$self_email = $GL_Member["member_email"];

	//메일 발송 전 메일로그 먼저 생성
	$C_Order = new Order();
	$order_download_email_idx = $C_Order -> insertOrderDownloadEmailSendLog($order_download_file_idx, $supplier_idx, $supplier_email, $email_title, $email_content, $self_email);

	/**
	 * 단축 URL 생성
	 * /proc/_stock_order_xls_down.php?stock_order_idx=*&stock_order_email_idx=*
	 */
	$shorty = new Shorty();

	$shorty->set_chars(DY_SHORTY_CHARS);
	$shorty->set_salt(DY_SHORTY_SALT);
	$shorty->set_padding(DY_SHORTY_PADDING);

	$stock_order_document_short_url = $shorty->returnUrl('/proc/_order_download_xls_down.php?supplier_idx='.$supplier_idx.'&order_download_file_idx='.$order_download_file_idx.'&order_download_email_idx='.$order_download_email_idx);

	$email_content .= '
		
		<a href="'.$stock_order_document_short_url.'">주문서 파일 다운받기</a>
		
	';

	//공급처 Mail Send!!
	$rst = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $supplier_email, $email_title, $email_content, 2, null, "", "");

	//정상 메일 발송 시 로그 Insert
	if($rst){

		//이메일 로그 Update :: is_del => N
		$C_Order -> updateOrderDownloadEmailSendLogIsDel($order_download_email_idx);

		//발송자도 같은 내용 받기
		$rst2 = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $self_email, "[주문다운로드 이메일 확인]".$email_title, $email_content, 2, null, "", "");


		$response["result"] = true;
	}else{
		$response["msg"] = "메일발송에 실패하였습니다.";
		$response["data"] = error_get_last();
		$response["supplier_email"] = $supplier_email;
		$response["email_title"] = $email_title;
		$response["email_content"] = $email_content;
	}

}elseif($mode == "send_email_log_selected"){

	$idx_list = $_POST["idx_list"];

	$self_name = $GL_Member["member_name"];
	$self_email = $GL_Member["member_email"];

	/**
	 * 단축 URL 생성
	 * /proc/_stock_order_xls_down.php?stock_order_idx=*&stock_order_email_idx=*
	 */
	$shorty = new Shorty();

	$shorty->set_chars(DY_SHORTY_CHARS);
	$shorty->set_salt(DY_SHORTY_SALT);
	$shorty->set_padding(DY_SHORTY_PADDING);

	$C_Order = new Order();

	$total_cnt = 0;
	$send_cnt = 0;
	//$idx_ary = array();
	foreach($idx_list as $idx)
	{
		$total_cnt++;
		//$idx_ary[] = $idx;

		$_view = $C_Order -> getOrderDownloadFileLogDetail($idx);

		$supplier_idx  = $_view["supplier_idx"];
		$supplier_email = $_view["supplier_email_order"];

		if($_view && $supplier_email){

			$email_title = "거래명세서 (주)덕윤";
			$email_content = "거래명세서";

			//메일 발송 전 메일로그 먼저 생성
			$order_download_email_idx = $C_Order -> insertOrderDownloadEmailSendLog($idx, $supplier_idx, $supplier_email, $email_title, $email_content, $self_email);

			//단축 URL 생성
			$stock_order_document_short_url = $shorty->returnUrl('/proc/_order_download_xls_down.php?supplier_idx='.$supplier_idx.'&order_download_file_idx='.$idx.'&order_download_email_idx='.$order_download_email_idx);


			$email_content .= '
			
				<a href="'.$stock_order_document_short_url.'">주문서 파일 다운받기</a>
				
			';

			//공급처 Mail Send!!
			$rst = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $supplier_email, $email_title, $email_content, 2, null, "", "");

			//정상 메일 발송 시 로그 Insert
			if($rst){

				//이메일 로그 Update :: is_del => N
				$C_Order -> updateOrderDownloadEmailSendLogIsDel($order_download_email_idx);

				//발송자도 같은 내용 받기
				$rst2 = mailer(DY_ADMIN_MAIL_SENDER_NAME, DY_ADMIN_MAIL_SENDER_EMAIL, $self_email, "[주문다운로드 이메일 확인]".$email_title, $email_content, 2, null, "", "");

				$send_cnt++;
			}else{

			}

		}
	}

	$result_ary = array();
	$result_ary["total"] = $total_cnt;
	$result_ary["send"] = $send_cnt;

	$response["result"] = true;
	$response["data"] = $result_ary;

}
echo json_encode($response);
?>