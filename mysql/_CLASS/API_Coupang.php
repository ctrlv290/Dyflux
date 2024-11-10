<?php
/**
 * Class API_Coupang
 * User: ssawoona
 * Date 2019
 */
class API_Coupang extends DBConn
{
	private $tmp_xls_header = "묶음배송번호|주문번호|택배사|운송장번호|분리배송 Y/N|주문시 출고예정일|주문일|등록상품명|등록옵션명|노출상품명(옵션명)|노출상품ID|옵션ID|최초등록옵션명|업체상품코드|결제액|배송비구분|배송비|도서산간 추가배송비|구매수(수량)|옵션판매가(판매단가)|구매자|구매자이메일|구매자전화번호|수취인이름|수취인전화번호|우편번호|수취인 주소|배송메세지|결제위치|취소여부|취소수량|분리배송가능여부|배송완료일|출고일|주문자 연락처|구매확정일자|할인 가격|상품별 개별 입력 항목|업체상품옵션 추가 정보|환불대기수량|운송장번호 업로드 일시|실제 출고예정일|쿠런티|업체상품 아이디|중고 상품 여부|결제일시|수취인 연락처|도서산간여부|발주서상태";
	private $tmp_api_header = "shipmentBoxId|orderId|deliveryCompanyName|invoiceNumber|splitShipping|orderItems_estimatedShippingDate|orderedAt|orderItems_sellerProductName|orderItems_sellerProductItemName|orderItems_vendorItemName|orderItems_productId|orderItems_vendorItemId|orderItems_firstSellerProductItemName|orderItems_externalVendorSkuCode|orderItems_orderPrice|orderItems_deliveryChargeTypeName|shippingPrice|remotePrice|orderItems_shippingCount|orderItems_salesPrice|orderer_name|orderer_email|orderer_safeNumber|receiver_name|receiver_safeNumber|receiver_postCode|receiver_addr1|parcelPrintMessage|refer|orderItems_canceled|orderItems_cancelCount|ableSplitShipping|deliveredDate|inTrasitDateTime|orderer_ordererNumber|orderItems_confirmDate|orderItems_discountPrice|orderItems_etcInfoHeader|orderItems_extraProperties|orderItems_holdCountForCancel|orderItems_invoiceNumberUploadDate|orderItems_plannedShippingDate|orderItems_pricingBadge|orderItems_sellerProductId|orderItems_usedProduct|paidAt|receiver_receiverNumber|remoteArea|status";
	public $XLS_HEADERS;
	public $API_HEADERS;
	function __construct()
	{
		DBConn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	public function repCode2String($key, $data)
	{
		$ret = "";
		//echo $key, $data;
		$arr_ableSplitShipping = array(
			'0' => 'N',
			'1' => 'Y',
			'' => '분리배송불가',
		);
		if ($key == "ableSplitShipping") {
			$ret = $arr_ableSplitShipping[$data];
		}
		//echo $ret;
		return $ret;
	}

	public function getCurl($args)
	{
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$path = "";
		$query = "";
		$strJson = "";
		$method = "";
		extract($args);

		date_default_timezone_set("GMT+0");
		$datetime = date("ymd").'T'.date("His").'Z';
		if($method == "GET") {
			$message = $datetime . $method . $path . $query;
			$url = 'https://api-gateway.coupang.com'.$path.'?'.$query;
		} else {
			$message = $datetime . $method . $path;
			$url = 'https://api-gateway.coupang.com'.$path;
		}
		$signature = hash_hmac('sha256', $message, $SECRET_KEY);
		$authorization  = "CEA algorithm=HmacSHA256, access-key=".$ACCESS_KEY.", signed-date=".$datetime.", signature=".$signature;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($curl, CURLOPT_POST, false);
		if($method != "GET") {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $strJson);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$arrRet['result_text'] = curl_exec($curl);
		$arrRet['status_code'] = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arrRet;
	}

	/**
	 * nextToken 을 사용한 페이징이 있을 경우 재기 호출을 사용해 다 모아라
	 * @param $args
	 * @return array
	 */
	public function getOrderList_API($args)
	{
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$result_data = array();
		$VENDOR_ID = "";
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$s_date = "";
		$e_date = "";
		$order_status = "";
		$nextToken = "";
		extract($args);

		// 주문 확인(상품준비중) 리스트 조회
		$path = "/v2/providers/openapi/apis/api/v4/vendors/".$VENDOR_ID."/ordersheets";
		$query = "createdAtFrom=".$s_date."&createdAtTo=".$e_date."&maxPerPage=50&status=".$order_status."&nextToken=".$nextToken;
		$ret_curl    = $this->getCurl(array(
				'method' => "GET",
				'path' => $path,
				'query' => $query,
				'ACCESS_KEY' => $ACCESS_KEY,
				'SECRET_KEY' => $SECRET_KEY,
			)
		);

		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		$arrRet["status_code"] = $status_code;

		if ($status_code == 200) {
			$data_array = json_decode($response, true);
			foreach ($data_array["data"] as $r_key => $r_val) {
				$result_data[count($result_data)] = $r_val;
			}
			$arrRet["result_text"] = $result_data;
			if($data_array["nextToken"] != "") {
				$args["result_data"] = $result_data;
				$args["nextToken"] = $data_array["nextToken"];
				$arrRet = $this->getOrderList_API($args);
			}
		}
		return $arrRet;
	}


	/**
	 * 옵션 별로 Row 만들어서 엑셀처럼 $tmp_api_header 에 맞는 Array 재 배열
	 * @param $args
	 * @param string $is_canceled
	 * @return array
	 */
	public function repOrderListArray($data_array, $is_canceled)
	{
		//$arrRet = array();
		$API_header = $this->API_HEADERS;
		// 주문 확인 리스트 Array 파싱
		$arrData     = array();
		$tmp_array   = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num = 0;
		foreach ($data_array as $o_key => $order) {
			$arr_row = $tmp_array;
			//echo $o_key."->".$order."<br />";
			for ($i = 0; $i < count($API_header); $i++) {
				$tmp = "";
				//echo "<br />";
				foreach ($order as $i_key => $item) {
					//echo $i_key . "->" . $item . "<br />";
					if ($i_key == $API_header[$i]) {
						$tmp = (string)$item;
						if($API_header[$i] == "ableSplitShipping") {
							$tmp = $this->repCode2String($API_header[$i], $tmp);
						}
						if($API_header[$i] == "orderedAt") {
							$tmp = str_replace("T", " ", $tmp);
						}

					}
					if(strpos($API_header[$i], "orderer_") !== false) {
						if ($i_key == "orderer") {
							/*foreach ($item as $s_key => $s_item) {
								echo "orderer_".$s_key . "->" . $s_item . "<br />";
							}*/
							$tmp = $item[str_replace("orderer_", "", $API_header[$i])];
						}
					}
					if(strpos($API_header[$i], "receiver_") !== false) {
						if ($i_key == "receiver") {
							/*foreach ($item as $s_key => $s_item) {
								echo "receiver_".$s_key . "->" . $s_item . "<br />";
							}*/
							$tmp = $item[str_replace("receiver_", "", $API_header[$i])];
						}
					}
				}
				$arr_row[$API_header[$i]] = (string)$tmp. "";
			}
			$arr_row["receiver_addr1"] = $arr_row["receiver_addr1"]." ".$order["receiver"]["addr2"];

			foreach ($order["orderItems"] as $i_key => $item) {
				$arr_item_row = $arr_row;
				$canceled = "true";
				foreach ($item as $oi_key => $order_item) {
					//echo "orderItems_".$oi_key . "->" . $order_item . "<br />";
					for ($i = 0; $i < count($API_header); $i++) {
						if ($oi_key == str_replace("orderItems_", "", $API_header[$i])) {
							//echo $API_header[$i]." : ".$oi_key . "->" . $order_item . "<br />";
							$arr_item_row[$API_header[$i]] = $order_item."";
							if($API_header[$i] == "orderItems_canceled") {
								//echo $API_header[$i]." : ".$oi_key . "->" . $order_item . "<br />";
								$arr_item_row[$API_header[$i]] = ($order_item) ? "true" : "false";
							}
						}
					}
				}
				//print_r2($item);
				if ($arr_row["orderId"] != "") {
					if($arr_item_row["orderItems_canceled"] == $is_canceled) {
						$arrData[$row_num] = $arr_item_row;
					}
					$row_num++;
				}
			}

		}


		return $arrData;

	}

	
	/**
	 * 주문 리스트 
	 * @param $args
	 * @param string $is_canceled : "false"=발송대기 , "true"=취소요청
	 * @return array
	 */
	public function getOrderList($args, $is_canceled = "false")
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$VENDOR_ID = "";
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->API_HEADERS;
		extract($args);

		$new_order_cnt = 0;
		$array_shipmentBoxIds = array();
		$args["order_status"] = "ACCEPT";     // 신규주문 상품
		//$args["order_status"] = "INSTRUCT";     // 주문확인 상품
		$retData = $this->getOrderList_API($args);
		$status_code = $retData['status_code'];
		$data_array  = $retData['result_text'];
		// 주문 확인 리스트 Array 파싱
		if ($status_code == 200) {
			$arrRet["status_code"]  = 0;
			foreach ($data_array as $o_key => $order) {
				//echo $o_key."->".$order["shipmentBoxId"]."<br />";
				$array_shipmentBoxIds[$new_order_cnt] = $order["shipmentBoxId"];
				$new_order_cnt ++;
			}
		}
		// 신규 주문에 대한 주문 확인 (취소 주문 조회시 주문확인 실행 X)
		if($new_order_cnt > 0 && $is_canceled == "false") {
			$array_OrderConfirm                   = array();
			$array_OrderConfirm["vendorId"]       = $VENDOR_ID;
			$array_OrderConfirm["shipmentBoxIds"] = $array_shipmentBoxIds;
			$strJson                              = json_encode($array_OrderConfirm, true);
			$path        = "/v2/providers/openapi/apis/api/v4/vendors/" . $VENDOR_ID . "/ordersheets/acknowledgement";
			$ret_curl    = $this->getCurl(array(
					'method' => "PATCH",
					'path' => $path,
					'strJson' => $strJson,
					'ACCESS_KEY' => $ACCESS_KEY,
					'SECRET_KEY' => $SECRET_KEY,
				)
			);
		}


		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error (신규주문)";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}


		$args["order_status"] = "INSTRUCT";     // 상품 준비중
		//$args["order_status"] = "DELIVERING";     // 테스트
		$retData = $this->getOrderList_API($args);
		$status_code = $retData['status_code'];
		$data_array  = $retData['result_text'];
		//echo "status_code->".$status_code;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$arrRet["result_text"] = "";
			$arrRet["result_data"] = $this->repOrderListArray($data_array, $is_canceled);
			//print_r2($arrRet["result_data"]);
		} else {
			$arrRet["result_text"] = "API Error (발송대기)";
			return $arrRet;
		}
		//$arrRet["result_data"] = $arrData;
		return $arrRet;

	}

	/**
	 * 주문 취소 요청 리스트
	 * @param $args
	 * @return array
	 */
	public function execCancelProc($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 취소 요청 수
			'confirm_cnt' => 0,    // 취소 숭인 수
			'reject_cnt' => 0,     // 취소 거부 수 (이미 송장 이상으로)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);
		$VENDOR_ID = "";
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);

		$C_CS = new CS();
		$C_Order = new Order();
		// 취소 주문 조회
		$ret_cancel = $this->getOrderList($args, "true");
		$status_code = $ret_cancel['status_code'];
		if ($status_code == 0) {
			$arrRet["status_code"] = 0;
			foreach ($ret_cancel['result_data'] as $val) {
				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $val["orderId"], $val["orderItems_vendorItemId"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
						$arrRet["reject_cnt"]++;
					} else {
						$arrRet["result_data"][] = array(
							'market_order_no' => $val["orderId"],
							'market_order_subno' => $val["orderItems_vendorItemId"],
							'ordPrdCnSeq' => $val["orderItems_cancelCount"],
						);
						$cancel_ret = $C_CS->updateOrderCancelOneByOrderIdx($_row["order_idx"], $cs_reason_code1, $cs_reason_code2, $cs_msg); // DY 에 취소상태 변경
						if($cancel_ret)  {
							$arrRet['confirm_cnt']++;
						} else {
							$arrRet["reject_cnt"]++;
						}
					}
				} else {
					$arrRet['notorder_cnt']++;
				}

			}
		}
		return $arrRet;
	}


	/**
	 * 송장 입력 처리
	 * @param $args
	 * @return array
	 */
	public function execDeliveryProc($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 발송 대기 수
			'confirm_cnt' => 0,    // 송장 입력 수
			'reject_cnt' => 0,     // 송장 거부 수 (에러 및 DY 배송상태 아님 등)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);
		$VENDOR_ID = "";
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);

		$_delivery = array();

		$C_Order = new Order();
		// 주문 조회
		$ret_cancel = $this->getOrderList($args);
		$status_code = $ret_cancel['status_code'];
		//print_r2($ret_cancel['result_data']);
		if ($status_code == 0) {
			$arrRet["status_code"] = 0;
			foreach ($ret_cancel['result_data'] as $val) {
				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $val["orderId"], $val["orderItems_vendorItemId"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_SHIPPED") {
						$arrRet["confirm_cnt"]++;

						$invoiceNo = $_row["invoice_no"];
						if ($_row["market_delivery_code"] == "CJGLS") {
							$invoiceNo = str_replace("-", "", $invoiceNo);
						}

						$_delivery[] = array(
							'shipmentBoxId' => $val["shipmentBoxId"],
							'orderId' => $val["orderId"],
							'deliveryCompanyCode' => $_row["market_delivery_code"],
							'invoiceNumber' => $invoiceNo,
							'vendorItemId' => $val["orderItems_vendorItemId"],
							'splitShipping' => false,
							'preSplitShipped' => false,
							'estimatedShippingDate' => "",
						);

						$C_Order->updateMarketInvoiceState($seller_idx, $val["orderId"], $val["orderItems_vendorItemId"]
							, "U"
							, "미처리");

					} else {
						$arrRet["reject_cnt"]++;
					}
				} else {
					$arrRet['notorder_cnt']++;
				}

			}
			if(count($_delivery) > 0) {
				$array_DelieryConfirm                               = array();
				$array_DelieryConfirm["vendorId"]                   = $VENDOR_ID;
				$array_DelieryConfirm["orderSheetInvoiceApplyDtos"] = $_delivery;

				$strJson     = json_encode($array_DelieryConfirm, true);
				$path        = "/v2/providers/openapi/apis/api/v4/vendors/" . $VENDOR_ID . "/orders/invoices";

				//region *** 마켓 실제 송장입력 처리 ***
				// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
				if(DY_MARKET_IS_LIVE) {
					$ret_curl    = $this->getCurl(array(
							'method' => "POST",
							'path' => $path,
							'strJson' => $strJson,
							'ACCESS_KEY' => $ACCESS_KEY,
							'SECRET_KEY' => $SECRET_KEY,
						)
					);
					$_d_status_code = $ret_curl['status_code'];
					$_d_response    = $ret_curl['result_text'];
					if ($_d_status_code == 200) {
						$data_array = json_decode($_d_response, true);
						foreach ($data_array["data"] as $d_key => $d_val) {
							//$result_data[count($result_data)] = $r_val;
							foreach ($d_val["responseList"] as $r_key => $r_val) {
								foreach ($ret_cancel['result_data'] as $val) {
									if($val["shipmentBoxId"] == $r_val["shipmentBoxId"]) {
										$C_Order->updateMarketInvoiceState($seller_idx, $val["orderId"], $val["orderItems_vendorItemId"]
											, ($r_val["succeed"] ? "S" : "F")
											, ($r_val["succeed"] ? "" : $r_val["resultCode"].":".$r_val["resultMessage"] ));
									}
								}
							}
						}
					} else {
						$arrRet["result_text"] = "네트워크 요청이 실패했습니다.\n상태 코드: " . $_d_status_code . "\n실패 사유: " . $_d_response;
					}


				}
				//endregion
			}
		}
		return $arrRet;
	}

}
?>