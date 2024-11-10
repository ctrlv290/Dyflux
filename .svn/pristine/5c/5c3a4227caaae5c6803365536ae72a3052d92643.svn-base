<?php
/**
 * User: ssawoona
 * Date: 2019
 */
include "../_init_.php";
set_time_limit(300);

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기


$C_Order = new Order();
$C_API_CJ_Invoice = new API_CJ_Invoice();

$print_type          = $_POST["print_type"];    // F:일반출력, A:추가출력
$order_pack_idx_list = $_POST["pidx_list"];
if($order_pack_idx_list){
	$order_pack_idx_list = explode(",", $order_pack_idx_list);
}
//print_r2($order_pack_idx_list);
if($print_type == "") {
	$print_type = "F";
}

$response = array();
$row_data = array();
$inserted_count = 0;
foreach ($order_pack_idx_list as $val) {
	$row_data = array(
		'result' => false,
		'msg' => '',
		'order_pack_idx' => $val,
		'new_invoice_no' => '',
		'cj_addinfo' => array(),
	);
	//echo $val."<br />";
	$rowValid = true;
	if ($val == "") {
		$rowValid = false;
		$row_data["msg"] = "관리번호가 입력되지 않았습니다.";
		//$row[$c_str] = "<strong>관리번호가 입력되지 않았습니다.</strong>";
	}else{
		$rst = $C_Order->getOrderDataForInvoiceUpload($val);
		if (!$rst) {
			$rowValid = false;
			$row_data["msg"] = "관리번호가 정확하지 않습니다.";
			//$row[$c_str] = "<strong>관리번호가 정확하지 않습니다.</strong>";
		}else{
			$order_idx                              = $val;
			$receive_addr                           = $rst["receive_addr1"];
			$row["order_progress_step_accept_date"] = $rst["order_progress_step_accept_date"];
			$row["market_order_no"]                 = $rst["market_order_no"];
			$row["invoice_no"]                      = $rst["invoice_no"];
			$row["order_progress_step_han"]         = $rst["order_progress_step_han"];
			$row["invoice_date"]                    = $rst["invoice_date"];

			//print_r2($row);
			if($print_type == "E") {
				/// **** 에러가 나서 롤백 될경우 상태를 [송장 -> 접수] 로 변경 ***
				//송장삭제
				$invoice_no     = $rst["invoice_no"];
				$cs_msg         = "송장 출력 오류로 상태 변경 [송장 -> 접수]";
				$rowValid        = false;
				//송장번호로 송장 삭제
				$c_rst = $C_Order -> deleteOrderInvoiceByInvoiceNo($invoice_no, false, $cs_msg);

			} else if($print_type == "A") {
				/// **** 추가 출력일 경우 ****
				if ($rst["order_progress_step"] != "ORDER_INVOICE" && $rst["order_progress_step"] != "ORDER_SHIPPED") {
					$rowValid        = false;
					$row_data["msg"] = "추가 송장 출력은 송장 또는 배송 상태의 주문건만 가능합니다.";
				}
			}
			else {
				/// **** 일반출력일 경우 ****
				//이미 배송 완료 인지 체크
				if ($rst["order_progress_step"] == "ORDER_SHIPPED") {
					$rowValid        = false;
					$row_data["msg"] = "이미 배송된 주문건입니다.";
					//$row["err_msg"] = "<strong>이미 배송된 주문건입니다.</strong>";
				} elseif ($rst["order_progress_step"] != "ORDER_ACCEPT" && $rst["order_progress_step"] != "ORDER_INVOICE") {
					$rowValid        = false;
					$row_data["msg"] = "접수 또는 송장 상태의 주문건만 송장입력이 가능합니다.";
					//$row["err_msg"] = "<strong>접수 또는 송장 상태의 주문건만 송장입력이 가능합니다.</strong>";
				}
			}
		}
	}

	if($rowValid) {
		$ret_cj_info = $C_API_CJ_Invoice->getNewCJInvoiceNum(array(
			'order_pack_idx' => $order_idx,
			'receive_addr' => $receive_addr,
			));
		if($print_type == "A") {
			/// **** 추가 출력일 경우 ****
			if ($ret_cj_info["result"]) {
				
				$row_data           = $ret_cj_info;
				$row_data["result"] = true;
				$row_data["msg"]    = "";
				$inserted_count++;

				$C_CS = new CS();
				$cs_task = "INVOICE_ADD_INSERT";    //추가송장출력

				$qry = "SELECT delivery_name FROM DY_DELIVERY_CODE WHERE market_code = N'DY' AND delivery_code = N'CJGLS'";
				$C_Dbconn = new Dbconn();
				$C_Dbconn->db_connect();
				$delivery_name = $C_Dbconn->execSqlOneCol($qry);
				$C_Dbconn->db_close();

				/// 추가 출력일 경우 CS 내역 남김
				$cs_msg = "[택배사 : ".$delivery_name.", 송장번호 : ".$ret_cj_info["new_invoice_no"]."]";
				$cs_idx = $C_CS -> insertCS($order_idx, $order_idx, 0, 0, 0, "Y", ""
					, $cs_task, $cs_msg, "", "", null, true);


			} else {
				$row_data["msg"]    = $ret_cj_info["msg"];
			}
		} else {
			/// **** 일반출력일 경우 ****
			if ($ret_cj_info["result"]) {
				//print_r2($ret_cj_info);
				//echo $invoice_no = $C_API_CJ_Invoice->getNewCJInvoiceNum(array('order_pack_idx' => $order_idx));				
//				$rst = $C_Order->updateOrderStepToInvoice($order_idx, $ret_cj_info["new_invoice_no"], "CJGLS", false, "송장출력", "AUTO");
//				if ($rst["result"]) {
//					$row_data           = $ret_cj_info;
//					$row_data["result"] = true;
//					$row_data["msg"]    = "";
//					$inserted_count++;
//				}

				$row_data           = $ret_cj_info;
				$row_data["result"] = true;
				$row_data["msg"]    = "";
				$inserted_count++;
			} else {
				$row_data["msg"]    = $ret_cj_info["msg"];
			}
		}
	}

	$response["rows"][] = $row_data;


}

//print_r2($listData);
echo json_encode($response, true);
//print_r2($C_API_CJ_Invoice->repCJAddress($order_pack_idx_list));





?>

