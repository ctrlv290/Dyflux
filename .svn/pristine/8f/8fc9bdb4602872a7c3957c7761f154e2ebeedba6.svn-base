<?php
/**
 * User: ssawoona
 * Date: 2018
 * Desc: API 사용가능 한 몰들에 대한 주문 송장 처리
 * Request : SellerID, StartDate, EndDate
 * Session : 로그인상태
 */

//Init
include_once "../_init_.php";
set_time_limit(600);

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$ret_json = array(
	'result' => false,
	'status_code' => 0,
	'result_text' => '',
	'request_cnt' => 0,    // 전체 취소 요청 수
	'confirm_cnt' => 0,    // 취소 숭인 수
	'reject_cnt' => 0,     // 취소 거부 수 (이미 송장 이상으로)
	'notorder_cnt' => 0,   // DY 에 주문 내역 없음
);

$seller_idx = $_GET["seller_idx"];
$mk_code    = $_GET["market_code"];
$s_date     = $_GET["s_date"];
$e_date     = $_GET["e_date"];
$cancel_orders     = $_GET["cc_orders"];

if (!$s_date || !$e_date || !$mk_code) {
	$ret_json['status_code'] = -9999;
	$ret_json['result_text'] = "잘못된 접근 입니다.";
	echo json_encode($ret_json, true);
	return;
}
$C_Seller = new Seller();
if ($seller_idx) {
	$_view = $C_Seller->getSellerData($seller_idx);
	if ($_view) {
		extract($_view);
		if ($market_code != $mk_code) {
			$ret_json['status_code'] = -9997;
			$ret_json['result_text'] = "존재하지 않은 판매처 입니다.";
		}
	} else {
		$ret_json['status_code'] = -9998;
		$ret_json['result_text'] = "존재하지 않은 판매처 입니다.";
	}
} else {
	$ret_json['status_code'] = -9999;
	$ret_json['result_text'] = "잘못된 접근 입니다.";
}
if ($ret_json['status_code'] < 0) {
	echo json_encode($ret_json, true);
	return;
}

$C_CS  = new CS();
$C_Order = new Order();
switch ($market_code) {
	case "11ST" :
		$C_API_11st = new API_11st();
		$ret        = $C_API_11st->execDeliveryProc(array(
			's_date' => str_replace("-", "", $s_date) . "0000",
			'e_date' => str_replace("-", "", $e_date) . "2359",
			'api_key' => $market_auth_code,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' => $cs_msg,
		));
		break;
	case "COUPANG" :
		$C_API_Coupang = new API_Coupang();
		$ret           = $C_API_Coupang->execDeliveryProc(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'VENDOR_ID' => $market_login_id,
			'ACCESS_KEY' => $market_auth_code,
			'SECRET_KEY' => $market_auth_code2,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' => $cs_msg,
		));
		break;
	case "INTERPARK" :
		$C_API_Interpark = new API_Interpark();
		$ret           = $C_API_Interpark->execDeliveryProc(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'sc_entrId' => $market_login_id,
			'sc_supplyEntrNo' => $market_auth_code,
			'sc_supplyCtrtSeq' => $market_auth_code2,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' => $cs_msg,
		));
		break;
	case "SSGMALL" :
		$C_API_SSGmall = new API_SSGmall();
		$ret           = $C_API_SSGmall->execDeliveryProc(array(
			's_date' => str_replace("-", "", $s_date),
			'e_date' => str_replace("-", "", $e_date),
			'api_key' => $market_auth_code,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' => $cs_msg,
		));
		break;
	case "LOTTECOM" :
		$C_API_LotteCom = new API_LotteCom();
		$ret           = $C_API_LotteCom->execDeliveryProc(array(
			's_date' => str_replace("-", "", $s_date),
			'e_date' => str_replace("-", "", $e_date),
			'UserId' => $market_login_id,
			'PassWd' => $market_login_pw,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' => $cs_msg,
		));
		break;
	case "CAFE24" :
		$C_API_Cafe24 = new API_Cafe24();
		$ret           = $C_API_Cafe24->execDeliveryProc(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'seller_idx' => $seller_idx,
			'cs_reason_code1' => $cs_reason_code1,
			'cs_reason_code2' => $cs_reason_code2,
			'cs_msg' =>$cs_msg,
		));
		break;
	default:
		// 기타 쇼핑몰이면 송장번호 출력
		if($cancel_orders != "") {
			$ret['status_code']  = 0;
			$ret['result_text']  = "";
			$ret['request_cnt']  = 0;    // 조회 요청 수
			$ret['confirm_cnt']  = 0;    // 배송 상태 수
			$ret['reject_cnt']   = 0;    // 배송 상태 아닌 수
			$ret['notorder_cnt'] = 0;    // DY 에 주문 내역 없음
			$ret['deliveryInfo'] = array();

			$row_num = 0;
			$cancel_orders_list = explode("|", $cancel_orders);
			//2019012528548691|2019012750979051
			foreach ($cancel_orders_list as $_orders) {
				$ret['deliveryInfo'][$row_num] = array('market_delivery_code' => '', 'delivery_name' => '', 'invoice_no' => '');
				$_orders_info = explode("^", $_orders);
				if(count($_orders_info) == 2) {
					$ret['request_cnt']++;
//					print_r2( $_orders_info);
//					echo $seller_idx;
					if($market_code == "TICKETMONSTER" || $market_code == "WEMAKEPRICE20") {
						// 주문상세 번호가 없어서 옵션명으로 만든 아이들은 주문번호로 한번에 주문 조회 해야함
						$_row = $C_Order->getOrderByMarketOrderNo_autoSubNo($seller_idx, $_orders_info[0]);
					} else {
						$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $_orders_info[0], $_orders_info[1]);
					}
					if (count($_row) > 0) {
						//echo $_row["order_idx"];
						if($_row["order_progress_step"] == "ORDER_SHIPPED") {
							$ret['deliveryInfo'][$row_num]["market_delivery_code"] = $_row["market_delivery_code"];
							$ret['deliveryInfo'][$row_num]["delivery_name"] = $_row["delivery_name"];
							$ret['deliveryInfo'][$row_num]["invoice_no"] = $_row["invoice_no"];

							if($market_code == "TICKETMONSTER" || $market_code == "WEMAKEPRICE20") {
								$C_Order->updateMarketInvoiceState_autoSubNo($seller_idx, $_orders_info[0]
									, "U", "");
							} else {
								$C_Order->updateMarketInvoiceState($seller_idx, $_orders_info[0], $_orders_info[1]
									, "U", "");
							}

							//market_delivery_code, DC.delivery_name
							$ret["confirm_cnt"]++;
						} else {
							$ret["reject_cnt"]++;
						}

					} else {
						$ret['notorder_cnt']++;
					}
				} else {
					$ret['notorder_cnt']++;
				}
				$row_num++;
			}

		}
		else {
			$ret_json['status_code'] = -9996;
			$ret_json['result_text'] = "잘못된 접근 입니다.";
			break;
		}
}

$ret_json['result']       = ($ret['status_code'] == "0" ? true : false);
$ret_json['status_code']  = $ret['status_code'];
$ret_json['result_text']  = $ret['result_text'];
$ret_json['request_cnt']  = $ret['request_cnt'];     // 전체 요청 수
$ret_json['confirm_cnt']  = $ret['confirm_cnt'];     // 배송 상태 수
$ret_json['reject_cnt']   = $ret['reject_cnt'];      // 배송 상태 아닌 수
$ret_json['notorder_cnt'] = $ret['notorder_cnt'];   // DY 에 주문 내역 없음
$ret_json['deliveryInfo'] = $ret['deliveryInfo'];   // 주문정보



echo json_encode($ret_json, true);
?>

