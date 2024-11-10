<?php

//Page Info
$pageMenuIdx = 316;     //신규 발주

include_once "../_init_.php";

$mode = $_POST["mode"];

$response = [];
$response["result"] = false;
$response["msg"] = "";

if ($mode == "get_current_stock_amount") {
	$products = $_POST["products"];

	$product_option_idx_list = [];

	foreach ($products as $product_data) {
		$product_option_idx_list[] = $product_data["product_option_idx"];
	}

	$product_option_idx_list_str = join(", ", $product_option_idx_list);

	$conn = new DBConn();
	$conn->db_connect();

	$qry = "
		SELECT
			S.product_idx, S.product_option_idx
			, IFNULL(Sum(Case When stock_status = 'NORMAL' OR stock_status = 'ABNORMAL' OR stock_status = 'BAD' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NAB
			, P.product_name
			, PO.product_option_name
		FROM DY_STOCK S
			JOIN DY_PRODUCT P ON S.product_idx = P.product_idx
			JOIN DY_PRODUCT_OPTION PO ON S.product_option_idx = PO.product_option_idx
		WHERE
			S.stock_is_del = N'N'
			And S.product_option_idx IN ($product_option_idx_list_str)
			And stock_status IN ('NORMAL', 'ABNORMAL', 'BAD')
			And stock_is_confirm = N'Y'
		Group by S.product_idx, S.product_option_idx, stock_unit_price
	";

	$select_result = $conn->execSqlList($qry);

	$conn->db_close();

	$response["list"] = $select_result;
	$response["result"] = true;
} elseif($mode == "new_sample_request") {
	$request_list = $_POST["list"];

//	foreach ($request_list as $request_data) {
//
//	}

	$request_data = [];
	$request_data["request_date"] = "NOW()";
	$request_data["request_member"] = $_SESSION["dy_member"]["member_idx"];
	$request_data["request_products"] = json_encode($request_list, JSON_UNESCAPED_UNICODE);
	$request_data["request_memo"] = $_POST["request_memo"];
	$request_data["return_due_date"] = $_POST["return_due_date"];

	$conn = new DBConn();
	$inserted_idx = $conn->insertFromArray($request_data, "DY_STOCK_SAMPLE_REQUEST");

	if($inserted_idx != 0) $response["result"] = true;
	else {
		$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
	}
} elseif($mode == "out_sample") {
	$stock_manager = new Stock();

	$request_idx = $_POST["request_idx"];
	$request_list = $_POST["list"];

	$response = [];
	$response["result"] = false;
	$response["msg"] = "";

	$stock_manager->db_connect();
	$stock_manager->sqlTransactionBegin();

	foreach ($request_list as $sample_data) {
		$out_status = $sample_data["out_status"];
		$out_amount = $sample_data["out_amount"];

		$available_data = $stock_manager->calculateAvailableStock($sample_data["product_option_idx"], $sample_data["stock_unit_price"], [$out_status]);

		if ($available_data == null) {
			$response["msg"] = $sample_data["product_name"] . " " . $sample_data["product_option_name"]
				. " 재고가 존재하지 않습니다. 출고할 수 없습니다.";
			break;
		}

		if (!($available_data["stock_amount_" . $out_status]) || $available_data["stock_amount_" . $out_status] < $out_amount) {
			$response["msg"] = $out_status . " 재고가 존재하지 않거나 "
				. $out_status . " 재고가 출고 수량보다 많습니다.";
			break;
		}

		$stock_data = [];
		$stock_data["product_idx"] = $sample_data["product_idx"];
		$stock_data["product_option_idx"] = $sample_data["product_option_idx"];
		$stock_data["stock_kind"] = "SAMPLE";
		$stock_data["stock_type"] = -1;
		$stock_data["stock_status"] = $out_status;
		$stock_data["stock_unit_price"] = $sample_data["stock_unit_price"];
		$stock_data["stock_amount"] = $out_amount;
		$stock_data["stock_request_date"] = "NOW()";
		$stock_data["stock_order_msg"] = "request_idx=" . $request_idx;
		$stock_data["stock_msg"] = "샘플 출고";
		$stock_data["stock_is_proc"] = "Y";
		$stock_data["stock_is_confirm"] = "Y";
		$stock_data["stock_is_confirm_date"] = "NOW()";
		$stock_data["stock_is_confirm_member_idx"] = $_SESSION["dy_member"]["member_idx"];
		$stock_data["stock_regdate"] = "NOW()";
		$stock_data["stock_regip"] = $_SERVER["REMOTE_ADDR"];
		$stock_data["last_member_idx"] = $_SESSION["dy_member"]["member_idx"];

		$inserted_idx = $stock_manager->insertFromArray($stock_data, "DY_STOCK");

		if ($inserted_idx == 0) {
			$response["result"] = false;
			$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
			break;
		} else {
			$response["result"] = true;
		}
	}

	if ($response["result"]) {
		$request_data = [];
		$request_data["request_idx"] = $request_idx;
		$request_data["out_date"] = "NOW()";
		$request_data["out_member"] = $_SESSION["dy_member"]["member_idx"];
		$request_data["status"] = "출고";

		$update_result = $stock_manager->insertFromArray($request_data, "DY_STOCK_SAMPLE_REQUEST", "request_idx");

		if (!$update_result) {
			$response["result"] = false;
			$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
		}
	}

	if ($response["result"]) {
		$stock_manager->sqlTransactionCommit();
	} else {
		$stock_manager->sqlTransactionRollback();
	}

	$stock_manager->db_close();
} elseif ($mode == "cancel_sample_request") {
	$request_idx = $_POST["request_idx"];

	$stock_manager = new Stock();
	$request_data = $stock_manager->getSampleRequestData($request_idx);

	if ($request_data) {
		if ($request_data["request_member"] != $_SESSION["dy_member"]["member_idx"]) {
			$response["msg"] = "샘플 요청자만 취소할 수 있습니다.";
		} else {
			$request_cancel_data = [];
			$request_cancel_data["request_idx"] = $request_data["request_idx"];
			$request_cancel_data["is_del"] = "Y";

			$cancel_result = $stock_manager->insertFromArray($request_cancel_data, "DY_STOCK_SAMPLE_REQUEST", "request_idx");

			if ($cancel_result) {
				$response["result"] = true;
			} else {
				$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
			}
		}
	} else {
		$response["msg"] = "샘플 요청 정보가 존재하지 않습니다.";
	}

} elseif ($mode == "return_sample") {
	$request_idx = $_POST["request_idx"];

	$stock_manager = new Stock();
	$request_data = $stock_manager->getSampleRequestData($request_idx);

	if ($request_data) {
		if ($request_data["status"] != "출고") {
			$response["msg"] = "출고 상태가 아니라 반납할 수 없습니다.";
		} else {
			if ($request_data["request_member"] != $_SESSION["dy_member"]["member_idx"]) {
				$response["msg"] = "샘플 요청자만 반납할 수 있습니다.";
			} else {
				$stock_manager->db_connect();
				$stock_manager->sqlTransactionBegin();

				$out_sample_list = $stock_manager->execSqlList("
					SELECT * FROM DY_STOCK 
					WHERE stock_kind = 'SAMPLE' AND stock_order_msg = 'request_idx=".$request_idx."'
				");

				foreach ($out_sample_list as $out_sample_data) {
					unset($out_sample_data["stock_idx"]);
					unset($out_sample_data["stock_is_confirm_date"]);
					$out_sample_data["stock_type"] = 0;
					$out_sample_data["stock_due_amount"] = $out_sample_data["stock_amount"];
					$out_sample_data["stock_amount"] = 0;
					$out_sample_data["stock_msg"] = "샘플 반납";
					$out_sample_data["stock_request_date"] = "NOW()";
					$out_sample_data["stock_request_member_idx"] = $_SESSION["dy_member"]["member_idx"];
					$out_sample_data["stock_regip"] = $_SERVER["REMOTE_ADDR"];
					$out_sample_data["stock_regdate"] = "NOW()";
					$out_sample_data["stock_is_proc"] = "N";
					$out_sample_data["stock_is_confirm"] = "N";
					$out_sample_data["last_member_idx"] = $_SESSION["dy_member"]["member_idx"];

					$inserted_idx = $stock_manager->insertFromArray($out_sample_data, "DY_STOCK");

					if ($inserted_idx == 0) {
						$response["result"] = false;
						$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
						break;
					} else {
						$response["result"] = true;
					}
				}

				if ($response["result"]) {
					$return_sample_data = [];
					$return_sample_data["request_idx"] = $request_data["request_idx"];
					$return_sample_data["status"] = "반납";

					$return_result = $stock_manager->insertFromArray($return_sample_data, "DY_STOCK_SAMPLE_REQUEST", "request_idx");

					if (!$return_result) {
						$response["result"] = false;
						$response["msg"] = "데이터 베이스 입력에 문제가 발생했습니다.\n잠시 후, 시도해주세요.";
					}
				}

				if ($response["result"]) {
					$stock_manager->sqlTransactionCommit();
				} else {
					$stock_manager->sqlTransactionRollback();
				}

				$stock_manager->db_close();
			}
		}
	} else {
		$response["msg"] = "샘플 요청 정보가 존재하지 않습니다.";
	}
}

echo json_encode($response);