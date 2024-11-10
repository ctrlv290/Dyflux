<?php
/**
 * User: ssawoona
 * Date: 2019-01
 */
include "../_init_.php";

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$C_Order = new Order();
$C_API_CJ_Invoice = new API_CJ_Invoice();

$JOB_MOD              = $_GET["job_mod"];
if($JOB_MOD == "") {
	$JOB_MOD = "INST_PRINT_LOG";
}
$print_type           = $_POST["print_type"];
if($print_type == "") {
	$print_type = "F";
}
$print_date           = $_POST["print_date"];
$print_date_count     = $_POST["print_date_count"];
$order_pack_idx       = $_POST["order_pack_idx"];
$invoice_no           = $_POST["invoice_no"];
$product_option_names = $_POST["product_option_names"];
$receive_name         = $_POST["receive_name"];
$receive_zipcode      = $_POST["receive_zipcode"];
$receive_addr         = $_POST["receive_addr"];
$receive_memo         = $_POST["receive_memo"];
$receive_hp_num       = $_POST["receive_hp_num"];
$receive_tp_num       = $_POST["receive_tp_num"];
$delivery_code        = $_POST["delivery_code"];
$delivery_name        = $_POST["delivery_name"];
$p_clsfcd             = $_POST["p_clsfcd"];
$p_subclsfcd          = $_POST["p_subclsfcd"];
$p_clsfaddr           = $_POST["p_clsfaddr"];
$p_clldlcbranshortnm  = $_POST["p_clldlcbranshortnm"];
$p_clldlvempnm        = $_POST["p_clldlvempnm"];
$p_clldlvempnicknm    = $_POST["p_clldlvempnicknm"];
$p_prngdivcd          = $_POST["p_prngdivcd"];
$p_farediv            = $_POST["p_farediv"];
$p_boxtyp             = $_POST["p_boxtyp"];
$delivery_fee         = $_POST["delivery_fee"];
$send_name            = $_POST["send_name"];
$send_phone1          = $_POST["send_phone1"];
$send_phone2          = $_POST["send_phone2"];
$send_add             = $_POST["send_add"];
if($p_boxtyp== "") {
	$p_boxtyp = "01";   // 박스타입코드 : 01: 극소,  02: 소,  03: 중,  04: 대,  05: 특대 (ex : 02)
}
if($p_farediv== "") {
	$p_farediv = "03";   // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
}
$shipped_invoiceNos   = $_POST["shipped_invoiceNos"];

$args = array();

$args["print_date"]           = $print_date;
$args["print_date_count"]     = $print_date_count;
$args["order_pack_idx"]       = $order_pack_idx;
$args["invoice_no"]           = $invoice_no;
$args["product_option_names"] = $product_option_names;
$args["receive_name"]         = $receive_name;
$args["receive_zipcode"]      = $receive_zipcode;
$args["receive_addr"]         = $receive_addr;
$args["receive_memo"]         = $receive_memo;
$args["receive_hp_num"]       = $receive_hp_num;
$args["receive_tp_num"]       = $receive_tp_num;
$args["delivery_code"]        = $delivery_code;
$args["delivery_name"]        = $delivery_name;
$args["p_clsfcd"]             = $p_clsfcd;
$args["p_subclsfcd"]          = $p_subclsfcd;
$args["p_clsfaddr"]           = $p_clsfaddr;
$args["p_clldlcbranshortnm"]  = $p_clldlcbranshortnm;
$args["p_clldlvempnm"]        = $p_clldlvempnm;
$args["p_clldlvempnicknm"]    = $p_clldlvempnicknm;
$args["p_prngdivcd"]          = $p_prngdivcd;
$args["p_farediv"]            = $p_farediv;
$args["p_boxtyp"]             = $p_boxtyp;
$args["delivery_fee"]         = $delivery_fee;
$args["send_name"]            = $send_name;
$args["send_phone1"]          = $send_phone1;
$args["send_phone2"]          = $send_phone2;
$args["send_add"]             = $send_add;
$args["print_type"]             = $print_type;

$response = array(
	'result' => false,
	'msg' => '',
	'print_count' => -1,
	'print_date_count' => 1,
	'list' => array (),
);
switch ($JOB_MOD) {
	case "INST_PRINT_LOG" :
		if ($invoice_no != "") {
			$print_count             = $C_API_CJ_Invoice->insertInvoicePrintLog($args);
			if($print_count ==0) {
				if($print_type == "A") {
					//추가송장
					$response["result"]      = true;
					$response["print_count"] = $print_count;
				} else {
					// 새로운송장입력
					$rst = $C_Order->updateOrderStepToInvoice($order_pack_idx, $invoice_no, "CJGLS", false, "송장출력", "AUTO");
					if ($rst["result"]) {
						$response["result"]      = true;
						$response["print_count"] = $print_count;
					}
				}
			} else if($print_count > 0) {
				// 재출력
				$response["result"]      = true;
				$response["print_count"] = $print_count;
			}

		}
		break;

	case "GET_PRINT_DATE_CNT" :
		$print_count                  = $C_API_CJ_Invoice->getPrintDateCount();
		$response["result"]           = true;
		$response["print_date_count"] = $print_count;
		break;

	case "GET_PRINT_DATE_CNT_LIST" :
		if ($print_date != "") {
			$list                         = $C_API_CJ_Invoice->getPrintDateCountList($print_date);
			$response["result"]           = true;
			$response["list"] = $list;
		}
		break;

	case "SET_ORDER_STEP_SHIPPED" :
		// 배송상태로 처리
		if ($shipped_invoiceNos != "") {
			$list = array();
			$shipped_invoiceNos_list = explode(",", $shipped_invoiceNos);
			foreach ($shipped_invoiceNos_list as $val) {
				$rst = $C_Order->getOrderListByInvoiceNo($val);
				if (!$rst) {
					$rowValid = false;
					$msg = "송장번호가 올바르지 않습니다.";
				} else {
					$ret = $C_Order->updateOrderStepToShippedByInvoiceNo($val);
					$rowValid = true;

					$msg = "";
				}
				$list[] = array('invoice_no' => $val, 'ret' => $rowValid, 'msg' => $msg);
			}
			$response["result"]           = true;
			$response["list"] = $list;
		}
		break;
}

echo json_encode($response, true);

?>